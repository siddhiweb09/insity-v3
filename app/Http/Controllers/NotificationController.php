<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    /**
     * Generate an OAuth2 access token for FCM HTTP v1 using a Google service account JSON.
     *
     * @param  string  $serviceAccountKeyPath  Absolute path to the service-account JSON file
     * @return string  Access token
     *
     * @throws \RuntimeException if the file/key is invalid or the token exchange fails
     */
    public function generateAccessToken(string $serviceAccountKeyPath): string
    {
        if (!is_readable($serviceAccountKeyPath)) {
            throw new \RuntimeException("Service account file not readable: {$serviceAccountKeyPath}");
        }

        $serviceAccountKey = json_decode(file_get_contents($serviceAccountKeyPath), true);
        if (!is_array($serviceAccountKey)) {
            throw new \RuntimeException('Invalid service account JSON.');
        }

        $clientEmail = $serviceAccountKey['client_email'] ?? null;
        $privateKey  = $serviceAccountKey['private_key']  ?? null;

        if (!$clientEmail || !$privateKey) {
            throw new \RuntimeException('Missing client_email or private_key in service account JSON.');
        }

        $now = time();

        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $jwtPayload = [
            'iss'   => $clientEmail,
            'sub'   => $clientEmail,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600, // 1 hour
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $headerEncoded  = $this->base64UrlEncode(json_encode($jwtHeader,  JSON_UNESCAPED_SLASHES));
        $payloadEncoded = $this->base64UrlEncode(json_encode($jwtPayload, JSON_UNESCAPED_SLASHES));
        $unsignedJwt    = "{$headerEncoded}.{$payloadEncoded}";

        // Use an OpenSSL key resource for signing
        $keyResource = openssl_pkey_get_private($privateKey);
        if (!$keyResource) {
            throw new \RuntimeException('Unable to parse private_key. Check formatting and line breaks.');
        }

        $signature = '';
        if (!openssl_sign($unsignedJwt, $signature, $keyResource, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Failed to sign JWT (openssl_sign).');
        }
        openssl_pkey_free($keyResource);

        $assertion = $unsignedJwt . '.' . $this->base64UrlEncode($signature);

        // Exchange JWT for access token using Laravel's HTTP client
        $resp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $assertion,
        ]);

        if ($resp->failed()) {
            throw new \RuntimeException('Token exchange failed: ' . $resp->body());
        }

        $accessToken = $resp->json('access_token');
        if (!$accessToken) {
            throw new \RuntimeException('No access_token in response: ' . $resp->body());
        }

        return $accessToken;
    }

    /**
     * Base64 URL-safe encoder (RFC 7515).
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send a data notification to a single device using FCM HTTP v1.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $token            FCM device token
     * @param  string|int  $lastInsertId Arbitrary payload value
     * @param  string  $targetActivity   Your app’s activity identifier
     * @param  string|null $serviceJson  Absolute path to service-account JSON (defaults to storage/app/firebase.json)
     * @param  string|null $projectId    GCP Project ID (defaults to env FIREBASE_PROJECT_ID)
     * @return array{ok:bool, response:mixed|null, error:string|null}
     */
    public function sendNotificationV1(
        string $title,
        string $message,
        string $token,
        $lastInsertId,
        string $targetActivity,
        ?string $serviceJson = null,
        ?string $projectId   = null
    ): array {
        try {
            $serviceJson = $serviceJson ?: storage_path('app/firebase.json');
            $projectId   = $projectId   ?: env('FIREBASE_PROJECT_ID', 'insity-dee8c');

            // 1) Get access token
            $accessToken = $this->generateAccessToken($serviceJson);

            // 2) FCM v1 endpoint
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // 3) Build message payload
            // You can also include a 'notification' block if you want system UI notifications.
            $payload = [
                'message' => [
                    'token' => $token,
                    // 'notification' => ['title' => $title, 'body' => $message], // optional
                    'data' => [
                        'title'           => $title,
                        'body'            => $message,
                        'click_action'    => 'com.isbm.insity.NOTIFICATION_CLICK',
                        'extra_payload'   => (string) $lastInsertId,
                        'target_activity' => $targetActivity,
                    ],
                ],
            ];

            // 4) Call FCM
            $resp = Http::withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload);

            if ($resp->failed()) {
                return [
                    'ok'       => false,
                    'response' => $resp->json(),
                    'error'    => 'FCM error: ' . $resp->body(),
                ];
            }

            return [
                'ok'       => true,
                'response' => $resp->json(),
                'error'    => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'       => false,
                'response' => null,
                'error'    => $e->getMessage(),
            ];
        }
    }
}

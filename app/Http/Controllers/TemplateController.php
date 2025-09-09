<?php

namespace App\Http\Controllers;

use App\Models\CreativeTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TemplateController extends Controller
{

    public function CreativeTemplateList()
    {
        return view('templates.list_creativeTemplate');
    }

    public function loadCreativeTemplates(Request $request)
    {
        // Delete action
        if ($request->isMethod('post') && $request->input('action') === 'delete') {
            $id = intval($request->input('id') ?? 0);
            if ($id <= 0)
                return response()->json(['success' => false, 'error' => 'Invalid ID']);

            $deleted = DB::table('creatives_templates')->where('id', $id)->delete();
            return response()->json(['success' => (bool) $deleted]);
        }

        // Fetch single template
        $id = intval($request->input('id') ?? 0);
        if ($id > 0) {
            $template = DB::table('creatives_templates')->where('id', $id)->first();
            if (!$template)
                return response()->json(['success' => false, 'error' => 'Template not found']);

            if (!empty($template->bg_image) && !preg_match('#^https?://#', $template->bg_image)) {
                $template->bg_image = asset('uploads/' . $template->bg_image);
            }

            return response()->json(['success' => true, 'template' => $template]);
        }

        // Return all templates
        $templates = DB::table('creatives_templates')
            ->select('id', 'title', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'templates' => $templates]);
    }

    public function buildCreativeTemplate()
    {
        return view('templates.build_creativeTemplate');
    }

    public function storeCreativeTemplate(Request $request)
    {
        $authUser = Auth::user()->employee_code . '*' . Auth::user()->name;
        $data = $request->all();

        if (!$data) {
            return response()->json(['success' => false, 'error' => 'No data']);
        }

        $title = substr(trim($data['title'] ?? 'Untitled'), 0, 255);
        $json = $data['json'] ?? '{}';

        // dd($data['exportedImage']);
        $imageDataURL = base64_decode($data['exportedImage']) ?? null;

        $imageFilename = null;

        if (!empty($data['exportedImage'])) {
            // if it's JSON, decode it
            $exported = is_array($data['exportedImage'])
                ? $data['exportedImage']
                : json_decode($data['exportedImage'], true);

            if (isset($exported['src']) && preg_match('/^data:image\/(\w+);base64,/', $exported['src'], $matches)) {
                $ext = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                $base64Data = substr($exported['src'], strpos($exported['src'], ',') + 1);
                $decoded = base64_decode($base64Data);

                // Ensure directory exists
                $dir = public_path('assets/images/creative_images/');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Generate filename
                $safeTitle = Str::slug($title);
                $imageFilename = $safeTitle . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

                file_put_contents($dir . $imageFilename, $decoded);
            }
        }

        $created_by = $authUser;
        $created_at = Carbon::now();

        try {
            $id = DB::table('creatives_templates')->insertGetId([
                'title' => $title,
                'bg_image' => $imageFilename,   // store filename in DB
                'image_json' => $json,
                'created_by' => $created_by,
                'created_at' => $created_at,
            ]);

            return response()->json(['success' => true, 'id' => $id, 'image' => $imageFilename]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function createCreativeImage($id)
    {
        $templateId = intval($id);
        $authUser = Auth::user();


        return view('templates.create_creativeImage', compact('templateId', 'authUser'));
    }

    public function deleteCreativeTemplate($id)
    {
        try {
            $template = CreativeTemplates::find($id);

            if (!$template) {
                return response()->json(['success' => false, 'message' => 'Template not found.'], 404);
            }

            // Delete background image if exists
            $bgImagePath = public_path(ltrim($template->bg_image, '/'));
            if ($template->bg_image && file_exists($bgImagePath)) {
                unlink($bgImagePath);
            }

            // Delete template from DB
            $template->delete();

            return response()->json(['success' => true, 'message' => 'Template deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCreativeBackground(Request $request)
    {
        $templateId = $request->input('template_id');
        $template = DB::table('creatives_templates')->where('id', $templateId)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found']);
        }

        if (!$request->hasFile('bg_image')) {
            return response()->json(['success' => false, 'message' => 'No image uploaded']);
        }

        $file = $request->file('bg_image');

        // ✅ Original background size from DB
        $oldImagePath = public_path('assets/images/creative_images/' . $template->bg_image);
        [$oldWidth, $oldHeight] = getimagesize($oldImagePath);

        // ✅ Load new image
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));

        // ✅ Create new image with original size
        $resizedImage = imagecreatetruecolor($oldWidth, $oldHeight);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        // ✅ Resize keeping aspect ratio (fill to fit)
        imagecopyresampled(
            $resizedImage,
            $image,
            0,
            0,
            0,
            0,
            $oldWidth,
            $oldHeight,
            imagesx($image),
            imagesy($image)
        );

        // ✅ Save with SAME NAME as old image (overwrite)
        $filePath = public_path('assets/images/creative_images/' . $template->bg_image);
        imagepng($resizedImage, $filePath);

        imagedestroy($image);
        imagedestroy($resizedImage);

        return response()->json([
            'success' => true,
            'image_url' => asset('assets/images/creative_images/' . $template->bg_image)
        ]);
    }
}

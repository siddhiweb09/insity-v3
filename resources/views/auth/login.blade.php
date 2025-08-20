<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Insity</title>

    <!-- endinject -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/select2/select2.min.css" />
    <link rel="stylesheet" href="assets/select2-bootstrap-theme/select2-bootstrap.min.css" />

    <!-- summernote -->
    <link rel="stylesheet" href="assets/summernote/summernote-bs4.min.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/vertical-layout-light/style.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/mediaCss.css" />
    <style>
        #pdf-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
            overflow: auto;
            background-color: #f9f9f9;
        }

        #controls {
            margin-bottom: 10px;
        }

        #canvas-container {
            display: flex;
            justify-content: center;
        }

        canvas {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper p-0">
            <div class="content-wrapper d-flex align-items-center auth p-0">
                <div class="row w-100 h-100 mx-0">
                    <div class="col-lg-7 mx-auto p-0">
                        <img src="assets/images/login.jpg" class="login-banner">
                    </div>
                    <div class="col-lg-5 col-md-12 auth-form-light mx-auto p-0">
                        <div class="auth-form text-left py-5 px-sm-5">
                            <div class="brand-logo">
                                <img src="assets/images/logo.png" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <form class="pt-3" id="loginForm" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="username" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Password">
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>
                            </form>
                        </div>
                        <div class="row justify-content-center px-4">
                            <a href="privacy-policy" class="px-2 text-secondary text-small w-auto">Privacy Policy</a>
                            <a href="terms-and-conditions" class="border-top-0 border border-bottom-0 px-2 text-secondary text-small w-auto">Terms and Conditions</a>
                            <a href="about-us" class="px-2 text-secondary text-small w-auto">About Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...');

            // Create FormData
            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('login.submit') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                        });
                        setTimeout(() => {
                            window.location.href = response.redirect || "{{ route('dashboard') }}";
                        }, 1000);
                    } else {
                        // Show error message
                        submitBtn.prop('disabled', false).text(originalText);
                        Swal.fire({
                            title: "Sorry!",
                            text: response.message,
                            icon: "error",
                            showCancelButton: true,
                            confirmButtonColor: "#4b49ac",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Lets Try Again!",
                        }).then((result) => {
                            window.location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).text(originalText);

                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        Swal.fire({
                            title: 'Error!',
                            text: errors || 'An error occurred during login.',
                            icon: 'error',
                            confirmButtonText: 'Cool'
                        })
                    }
                }
            });
        });
    </script>
</body>

</html>
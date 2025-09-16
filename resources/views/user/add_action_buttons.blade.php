@extends('frames.frame')

@section('content')
    <style>
        .create-user-card {
            border: 3px solid var(--super-light-primary) !important;
        }

        .form-label {
            color: #495057;
        }
    </style>
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Add Action Buttons</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="create-user-card card bg-white rounded-3 shadow overflow-hidden">
                        <!-- Form Header -->
                        <form id="addActionButtons" method="post">
                            <!-- Basic Information Section -->
                            <div class="form-section p-3">
                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="buttonTitleName" class="form-label fw-medium mb-2">Button
                                            Title/Name:</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-typography fs-6"></i></span>
                                            <input type="text" class="form-control transition" id="buttonTitleName"
                                                name="buttonTitleName" placeholder="Enter Button Title/Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="buttonIcon" class="form-label fw-medium mb-2">Button
                                            Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-icons fs-6"></i></span>
                                            <input type="text" class="form-control" id="buttonIcon" name="buttonIcon"
                                                placeholder="Enter Button Icon Class" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="buttonClass" class="form-label fw-medium mb-2">Button Class</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-code fs-6"></i></span>
                                            <input type="text" class="form-control transition" id="buttonClass"
                                                name="buttonClass" placeholder="Enter Class Name to perform action script"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="buttonCategory" class="form-label fw-medium mb-2">Button
                                            Category</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-category fs-6"></i></span>
                                            <input type="text" class="form-control" id="buttonCategory"
                                                name="buttonCategory" placeholder="Enter Button Category" required>
                                        </div>
                                    </div>
                                    <div class="mb-2 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-medium mb-2" for="buttonpurpose">Button Purpose:
                                            </label>
                                            <textarea class="form-control" name="buttonpurpose" id="buttonpurpose"
                                                placeholder="Enter purpose of this button" style="height: 100px"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-section p-3 border-none">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-send-2 me-2"></i>Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            $('#addActionButtons').on('submit', function (e) {
                e.preventDefault();

                let formData = {
                    buttonTitleName: $('#buttonTitleName').val(),
                    buttonIcon: $('#buttonIcon').val(),
                    buttonClass: $('#buttonClass').val(),
                    buttonCategory: $('#buttonCategory').val(),
                    buttonpurpose: $('#buttonpurpose').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('storeActionButton') }}",
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#addActionButtons')[0].reset();
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Something went wrong!');
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Error while saving action button.');
                    }
                });
            });

        });
    </script>
@endsection
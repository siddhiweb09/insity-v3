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
        <div class="card p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Add Sidebar Menus</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="create-user-card card bg-white rounded-3 shadow">
                        <!-- Form Header -->
                        <form id="addSidebarMenus" method="post">
                            <!-- Basic Information Section -->
                            <div class="form-section p-3">
                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium mb-2 required-field">Sidebar
                                            Category</label>
                                        <div class="existed-sidebar-category input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-category fs-6"></i></span>
                                            <select class="form-select transition" id="existedSidebarCategory"
                                                name="existedSidebarCategory" required>
                                                <option value="" selected disabled>Select Category</option>
                                                @foreach($sidebarMenus as $menu)
                                                    <option value="{{ $menu->categories }}" data-icon="{{ $menu->icons }}">
                                                        {{ $menu->categories }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="new-sidebar-category input-group d-none">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-category-plus fs-6"></i></span>
                                            <input type="text" class="form-control transition" id="newSidebarCategory"
                                                name="newSidebarCategory" placeholder="Enter New Sidebar Category">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="SidebarIcon" class="form-label fw-medium mb-2 required-field">Sidebar
                                            Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-photo-scan fs-6"></i></span>
                                            <input type="text" class="form-control" id="sidebarIcon" name="sidebarIcon"
                                                placeholder="Enter Icon Code here" required>
                                        </div>
                                    </div>
                                    <div class="mb-2 col-12">
                                        <div class="form-group">
                                            <label class="mb-2" for="add_new_category">
                                                <input type="checkbox" id="add_new_category" name="add_new_category"
                                                    class="mx-2 mt-2"> Add
                                                New sidebar Category
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-medium mb-2 required-field">Sidebar Menu
                                            name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-layout-sidebar fs-6"></i></span>
                                            <input type="text" class="form-control transition" id="sidebarName"
                                                name="sidebarName" placeholder="Enter Sidebar Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mobile" class="form-label fw-medium mb-2 required-field">Sidebar Menu
                                            URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-link fs-6"></i></span>
                                            <input type="text" class="form-control" id="sidebarUrl" name="sidebarUrl"
                                                placeholder="Enter Sidebar Menu URL" required>
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

            $('#add_new_category').change(function () {
                if ($(this).is(':checked')) {
                    // Show the input for new category & Hide the select for existing category
                    $('.new-sidebar-category').removeClass('d-none');
                    $('.existed-sidebar-category').addClass('d-none');
                } else {
                    // Show the select for existing category & Hide the input for new category
                    $('.existed-sidebar-category').removeClass('d-none');
                    $('.new-sidebar-category').addClass('d-none');
                }
            });

            $('#existedSidebarCategory').on('change', function () {
                var selectedIcon = $(this).find(':selected').data('icon');
                $('#sidebarIcon').val(selectedIcon); // fill input with icon code
            });

            $('#addSidebarMenus').on('submit', function (e) {
                e.preventDefault();

                let formData = {
                    existedSidebarCategory: $('#existedSidebarCategory').val(),
                    newSidebarCategory: $('#newSidebarCategory').val(),
                    sidebarIcon: $('#sidebarIcon').val(),
                    sidebarName: $('#sidebarName').val(),
                    sidebarUrl: $('#sidebarUrl').val(),
                    add_new_category: $('#add_new_category').is(':checked') ? 1 : 0
                };
                console.log(formData);

                $.ajax({
                    url: "{{ route('storeSidebarMenus') }}",
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#addSidebarMenus')[0].reset();
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Something went wrong!');
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Error while saving sidebar menu.');
                    }
                });
            });
        });
    </script>
@endsection
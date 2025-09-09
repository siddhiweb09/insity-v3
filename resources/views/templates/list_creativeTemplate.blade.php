@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Creative Templates</h3>
                <a href="{{ route("templates.build_creativeTemplate") }}" class="btn btn-primary" tabindex="0"
                    id="goToBuilder">
                    <i class="mdi mdi-folder-plus text-warning me-1"></i> New Template
                </a>
            </div>


            <div class="card shadow-none">
                <div class="card-body" id="listCreativeTemplate">
                    <h4 class="mb-4">Your Creatives</h4>
                    <div class="row" id="templateList">Loading templates...</div>
                </div>
            </div>


        </div>
    </div>
@endsection

@section('customJs')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {

            var scrollbar1 = document.getElementById("listCreativeTemplate");
            if (scrollbar1) {
                new PerfectScrollbar(scrollbar1, {
                    wheelPropagation: false
                });
            }

            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            loadTemplates();

            function loadTemplates() {
                $.ajax({
                    url: '{{ route("loadCreativeTemplates") }}',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function () {
                        $('#templateList').html('<p>Loading templates...</p>');
                    },
                    success: function (data) {
                        if (!data.templates || data.templates.length === 0) {
                            $('#templateList').html('<p>No templates found.</p>');
                            return;
                        }

                        let html = '';
                        $.each(data.templates, function (index, t) {
                            html += `
                                            <div class="col-md-4 col-6 mb-3">
                                                <div class="template-card card border-0 shadow h-100 p-3">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h5 class="card-title text-truncate mb-0 fw-bold">${t.title}</h5>
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-clock me-1"></i>${t.created_at}
                                                            </small>
                                                        </div>
                                                        <div class="d-flex mt-auto pt-3">
                                                                <a href="/create-creative-image/${t.id}" class="btn btn-primary rounded-3 me-3" style="padding-top:10px !important; padding-bottom:10px !important;">
                                                                    <i class="mdi mdi-brush me-1"></i> Use Template
                                                                </a>
                                                                <button class="btn btn-danger d-flex justify-content-center align-items-center rounded-circle delete-template" data-id="${t.id}" style="height:40px !important; width:40px !important">
                                                                    <i class="mdi mdi-trash-can"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`;
                        });

                        $('#templateList').html(html);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading templates:', error);
                        $('#templateList').html('<p class="text-danger">Failed to load templates. Please try again.</p>');
                    }
                });
            }

            $(document).on('click', '.delete-template', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this action!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4B49AC',
                    cancelButtonColor: '#FF2121',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-creative-template/' + id,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    loadTemplates(); // Refresh template list
                                } else {
                                    Swal.fire('Error!', res.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
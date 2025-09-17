@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Groups</h3>
                <button class="btn btn-primary mdi mdi-plus" data-bs-toggle="offcanvas" data-bs-target="#addGroup"
                    title="Add Group"></button>
            </div>

            <!-- Groups Content -->
            <div class="card shadow-none">
                <div class="card-body">
                    <div class="row groupsData">
                        @if(isset($groups) && count($groups) > 0)
                            @foreach($groups as $group)
                                <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                                    <div
                                        class="card primary-gt p-3 text-center position-relative d-flex flex-column h-100 w-100 mx-auto">
                                        @if(!empty($group->group_avatar))
                                            <img src="{{ asset('assets/images/group-avatars/' . $group->group_avatar) }}"
                                                class="card-img-top mt-3" alt="{{ $group->group_name }}">
                                        @else
                                            <img src="{{ asset('assets/images/profile_picture/profile.png') }}"
                                                class="card-img-top mt-3" alt="Default Avatar">
                                        @endif
                                        <div class="card-body">
                                            <p class="card-text">{{ $group->group_leader }}</p>
                                            <h5 class="card-title">{{ $group->group_name }}</h5>
                                            <small>{{ $group->group_zone }}</small>
                                            <div class="mt-2">
                                                <a href="{{ route('user.view_teams', ['encoded' => base64_encode($group->id . '*' . $group->group_name)]) }}"
                                                    class="text-decoration-none text-dark">
                                                    View Teams
                                                </a> |
                                                <a href="{{ route('user.team_mapping', ['encoded' => base64_encode($group->id . '*' . $group->group_name . '*' . $group->group_zone . '*' . $group->group_leader)]) }}"
                                                    class="text-decoration-none text-dark addTeamsBtn">
                                                    Add Teams
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        class="btn counsellor-rank primary-bg editGroups d-flex justify-content-center align-items-center"
                                        data-id="{{ $group->id }}">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center">
                                <p>No records found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('offcanvas.offcanvas_add_groups')
    @include('offcanvas.offcanvas_add_teams')
    @include('offcanvas.offcanvas_edit_groups')
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            const offcanvasElement = document.getElementById("addGroup");

            if (offcanvasElement) {
                offcanvasElement.addEventListener("shown.bs.offcanvas", function () {
                    $(".js-example-basic-single").select2({
                        dropdownParent: $("#addGroup .offcanvas-body") // Ensure this matches your actual DOM structure
                    });
                });
            }

            var scrollbar1 = document.getElementById("Groups_table");
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

            // Initial load: populate zones with no preselection
            fetch_zones("ALL");
            fetch_leaders("ALL");

            $('#storeGroups').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                let formData = new FormData(this); // Collect form data
                $.ajax({
                    url: "{{ route('storeGroups') }}", // Laravel route
                    method: "POST", // HTTP method
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#storeGroups')[0].reset(); // Reset the form
                            $('#addGroup').offcanvas('hide'); // Close offcanvas
                            setTimeout(function () {
                                location.reload();
                            }, 300);
                        } else {
                            alert('Something went wrong!');
                        }
                    },
                    error: function (xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                alert(value[0]);
                            });
                        } else {
                            alert('Server Error');
                        }
                    }
                });
            });

            $(document).on('click', '.editGroups', function () {
                let offcanvasElement = document.getElementById("editOffcanvas");
                let bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
                let dataId = $(this).data('id');

                $.ajax({
                    url: "{{ route('fetchGroupData') }}", // Laravel route
                    type: "POST",
                    data: {
                        id: dataId,
                        _token: "{{ csrf_token() }}" // Add CSRF token for Laravel
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            let group = response.group;

                            $('#editId').val(group.id);
                            $('#editName').val(group.group_name);
                            $('#editLeader').val(group.group_leader);

                            fetch_zones(group.group_zone);
                            fetch_leaders(group.group_leader);

                            bsOffcanvas.show();
                        } else {
                            alert("Group data not found");
                        }
                    },
                    error: function (xhr) {
                        console.error("AJAX Error: ", xhr);
                        alert("Error fetching Group data");
                    }
                });
            });

            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();
                console.log(formData);
                $.ajax({
                    url: "/update-group",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        console.log(response);

                        if (response.success === true) {
                            alert("Group updated successfully");
                            const offcanvas = $('#editOffcanvas');

                            offcanvas.offcanvas('hide');

                            offcanvas.on('hidden.bs.offcanvas', function () {
                                location.reload();
                            });
                        } else {
                            alert("Error updating Group: " + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert("Error updating Group");
                        console.error("AJAX Error: ", xhr);
                    }
                });
            });

        });

        // ✅ Only keep zone population (no counselors anymore)
        function fetch_zones(groupZone) {
            $.ajax({
                type: "POST",
                url: "/fetch-zones",
                dataType: "json",
                success: function (response) {
                    var names = response.zones;
                    var zone = $(".zone");
                    var editZone = $(".editZone");

                    zone.empty().append('<option value="">Select Zone</option>');
                    editZone.empty();

                    names.forEach(function (name) {
                        zone.append($("<option>", {
                            value: name,
                            text: name
                        }));

                        editZone.append($("<option>", {
                            value: name,
                            text: name,
                            selected: groupZone &&
                                name.trim().toLowerCase() === groupZone.trim().toLowerCase()
                        }));
                    });
                },
                error: function (error) {
                    console.error("Error fetching Zones:", error);
                }
            });
        }

        function fetch_leaders(selectedLeader = null) {
            $.ajax({
                type: "POST",
                url: "/fetch-users",
                dataType: "json",
                success: function (response) {
                    console.log("Leaders Response:", response);
                    var names = response.counselors || [];

                    var counselor = $(".counselor");      // Add form dropdown
                    var editCounselor = $(".editLeader"); // Edit form dropdown

                    counselor.empty().append('<option value="">Select Leader</option>');
                    editCounselor.empty();

                    names.forEach(function (item) {
                        // Use full string for both value and text
                        counselor.append($("<option>", {
                            value: item,
                            text: item
                        }));

                        editCounselor.append($("<option>", {
                            value: item,
                            text: item,
                            selected: selectedLeader &&
                                item.toLowerCase() === selectedLeader.toLowerCase()
                        }));
                    });
                },
                error: function (error) {
                    console.error("Error fetching leaders:", error);
                }
            });
        }


        $('#editOffcanvas').on('hidden.bs.offcanvas', function () {
            $('.offcanvas-backdrop').remove(); // just in case
        });


    </script>
@endsection
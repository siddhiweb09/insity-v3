@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="card overflow-hidden p-4 shadow-none">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-500 text-primary">Teams</h3>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-primary mdi mdi-plus " tabindex="0" id=""
                    aria-controls="releaseNotesOffcanvasEnd" type="button" data-bs-toggle="offcanvas"
                    data-bs-placement="top" title="" data-bs-original-title="Add Notes"
                    data-bs-target="#addTeam"></button>
            </div>
        </div>

        <div class="card shadow-none">
            <div class="card-body">
                <div class="row teamsData">
                    @if(isset($teams) && count($teams) > 0)
                    @foreach($teams as $team)
                    <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                        <div
                            class="card primary-gt p-3 text-center position-relative d-flex flex-column h-100 w-100 mx-auto">
                            <img src="{{ asset('assets/images/profile_picture/profile.png') }}"
                                class="card-img-top mt-3" alt="Counselor Image">
                            <div class="card-body">
                                <p class="card-text">{{ $team->team_leader }}</p>
                                <h5 class="card-title">{{ $team->team_name }}</h5>
                                <div class="mt-2">
                                    <a href="{{ route('user.view_members', ['encoded' => base64_encode($team->id . '*' . $team->team_name)]) }}"
                                        class="text-decoration-none text-dark">
                                        Manage Users
                                    </a> |
                                    <a href="{{ route('user.user_mapping', ['encoded' => base64_encode($team->id . '*' . $team->team_name . '*' . $team->team_leader . '*' . $team->group_name)]) }}" class="text-decoration-none text-dark addTeamsBtn">
                                        Add Users
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button
                            class="btn counsellor-rank primary-bg editTeams d-flex justify-content-center align-items-center"
                            data-id="{{ $team->id }}">
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

@include('offcanvas.offcanvas_add_teams')
@include('offcanvas.offcanvas_edit_teams')

@endsection

@section('customJs')
<script>
    $(document).ready(function() {

        const offcanvasElement = document.getElementById("addTeam");

        if (offcanvasElement) {
            offcanvasElement.addEventListener("shown.bs.offcanvas", function() {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#addTeam .offcanvas-body") // Ensure this matches your actual DOM structure
                });
            });
        }

        var scrollbar1 = document.getElementById("Teams_table");
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

        $('#storeTeams').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this); // Collect form data
            $.ajax({
                url: "{{ route('storeTeams') }}", // Laravel route
                method: "POST", // HTTP method
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        alert(response.message);
                        $('#storeTeams')[0].reset(); // Reset the form
                        $('#addTeam').offcanvas('hide'); // Close offcanvas
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    } else {
                        alert('Something went wrong!');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            alert(value[0]);
                        });
                    } else {
                        alert('Server Error');
                    }
                }
            });
        });

        fetch_leaders("ALL");

        // Handle edit button click
        $(document).on('click', '.editTeams', function() {
            let offcanvasElement = document.getElementById("editOffcanvas");
            let bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
            // bsOffcanvas.show();
            var dataId = $(this).data('id');
            console.log(dataId);

            // Fetch team data by ID
            $.ajax({
                url: "{{ route('fetchTeamData') }}",
                type: "POST",
                data: {
                    id: dataId,
                    _token: "{{ csrf_token() }}" // ✅ always send CSRF token in Laravel
                },
                dataType: "json",
                success: function(response) {
                    console.log("Server Response:", response);

                    if (response.status === "success") {
                        let team = response.team; // ✅ directly use group

                        if (team) {
                            $('#editId').val(team.id);
                            $('#editName').val(team.group_name);
                            fetch_leaders(team.group_leader);
                            bsOffcanvas.show();
                        } else {
                            alert("Team not found.");
                        }
                    } else if (response.status === "error") {
                        alert(response.message || "Something went wrong.");
                    } else {
                        alert("Unexpected response format.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error, xhr.responseText);
                    alert("An error occurred while fetching team data. Please try again.");
                }
            });
        });

        // Handle form submission
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            console.log(formData);

            $.ajax({
                url: "{{ route('updateTeam') }}",
                type: "POST",
                data: formData,
                dataType: "json", // ✅ add this
                success: function(response) {
                    console.log(response);

                    if (response.success === true) {
                        alert("Team updated successfully");
                        const offcanvas = $('#editOffcanvas');

                        offcanvas.offcanvas('hide');

                        offcanvas.on('hidden.bs.offcanvas', function() {
                            location.reload();
                        });
                    } else {
                        alert("Error updating team: " + response.message);
                    }
                },
                error: function(xhr) {
                    alert("Error updating team");
                    console.error("AJAX Error: ", xhr);
                }
            });
        });

    });

    function fetch_leaders(leader) {
        $.ajax({
            type: "POST",
            url: "{{ route('fetchAllCounselors') }}",
            dataType: "json",
            success: function(response) {
                var names = response.counselors;
                var counselor = $(".leader");
                var editCounselor = $(".editLeader");

                counselor.empty().append('<option value="">Select Leader Name</option>');
                editCounselor.empty();

                // Populate Add dropdown always
                for (var i = 0; i < names.length; i++) {
                    var name = names[i];
                    counselor.append(
                        $("<option>", {
                            value: name,
                            text: name,
                        })
                    );
                }

                // If leader is passed (edit mode), fill and select in edit dropdown
                if (leader && typeof leader === "string" && leader.trim().toLowerCase() !== "all") {
                    for (var i = 0; i < names.length; i++) {
                        var name = names[i];
                        editCounselor.append(
                            $("<option>", {
                                value: name,
                                text: name,
                                selected: (name.trim().toLowerCase() === leader.trim().toLowerCase()),
                            })
                        );
                    }
                }

                console.log("fetch_leaders() called with:", leader);
            },
            error: function(error) {
                console.error("Error fetching Leader Names:", error);
            },
        });
    }


    $('#editOffcanvas').on('hidden.bs.offcanvas', function() {
        $('.offcanvas-backdrop').remove(); // just in case
    });
</script>
@endsection
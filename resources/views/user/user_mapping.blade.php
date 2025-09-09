@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card p-4 shadow-none">
            <div class="row justify-content-between mb-4">
                <h3 class="text-primary">Manage Team Members</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <p class="fw-bold">Team Name:</p>
                    <h5>{{ $name }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold">Team Leader:</p>
                    <h5>{{ $leaderCode }}*{{ $leaderName }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold">Team Group:</p>
                    <h5>{{ $group ?? 'NA' }}</h5>
                </div>
            </div>

            <div class="row mt-5">
                <!-- Available users -->
                <div class="col-md-6">
                    <h5>Available Users</h5>
                    <select id="userSearch" class="form-control" style="width:100%" multiple></select>
                    <div class="d-flex justify-content-end mt-5">
                        <button id="addUsersBtn" class="btn btn-primary rounded-3" style="padding-top: 15px !important; padding-bottom: 15px !important;"><i class="mdi mdi-plus me-2"></i>Add Selected
                            Users</button>
                    </div>
                </div>

                <!-- Assigned users -->
                <div class="col-md-6">
                    <h5>Assigned Users to <span class="fw-semibold">{{ $name }}</span></h5>
                    <ul class="list-group" id="assignedUsersList">
                        @foreach($users as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                data-id="{{ $user->id }}">
                                {{ $user->employee_code }}*{{ $user->employee_name }}
                                <!-- <button class="btn btn-sm btn-danger removeUser">Remove</button> -->
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {
            // Enable Select2 with AJAX
            $('#userSearch').select2({
                placeholder: "Search for users...",
                ajax: {
                    url: "{{ route('users.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: data.map(u => ({ id: u.id, text: u.employee_code + ' * ' + u.employee_name }))
                    }),
                    cache: true
                }
            });

            // On Add Users button click
            $('#addUsersBtn').on('click', function () {
                let selectedUsers = $('#userSearch').select2('data');
                if (selectedUsers.length === 0) {
                    alert("Please select at least one user.");
                    return;
                }

                let userIds = selectedUsers.map(u => u.id);
                let userTexts = selectedUsers.map(u => u.text);

                $.post("{{ route('users.addToTeam') }}", {
                    ids: userIds,
                    team_name: "{{ $name }}",
                    _token: "{{ csrf_token() }}"
                }, function (res) {
                    if (res.status === "success") {
                        userTexts.forEach((text, index) => {
                            $("#assignedUsersList").append(
                                `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${userIds[index]}">
                                ${text}
                            </li>`
                            );
                        });
                        $('#userSearch').val(null).trigger('change'); // Clear selection
                    }
                });
            });
        });
    </script>
@endsection
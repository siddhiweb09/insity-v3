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
                <select id="userSearch" class="form-control" style="width:100%"></select>
            </div>

            <!-- Assigned users -->
            <div class="col-md-6">
                <h5>Assigned to Team</h5>
                <ul class="list-group" id="assignedUsersList">
                    @foreach($users as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $user->id }}">
                            {{ $user->employee_code }}*{{ $user->employee_name }}
                            <button class="btn btn-sm btn-danger removeUser">Remove</button>
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
$(document).ready(function() {
    // Enable Select2 search with AJAX
    $('#userSearch').select2({
        placeholder: "Search for users...",
        ajax: {
            url: "{{ route('users.search') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(u => ({ id: u.id, text: u.employee_code+' * '+u.employee_name }))
            }),
            cache: true
        }
    });

    // Add user to team
    $('#userSearch').on('select2:select', function(e) {
        let userId = e.params.data.id;
        let userText = e.params.data.text;

        $.post("{{ route('users.addToTeam') }}", {
            id: userId,
            team_name: "{{ $name }}",
            _token: "{{ csrf_token() }}"
        }, function(res) {
            if (res.status === "success") {
                $("#assignedUsersList").append(
                    `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${userId}">
                        ${userText}
                        <button class="btn btn-sm btn-danger removeUser">Remove</button>
                    </li>`
                );
                $('#userSearch').val(null).trigger('change');
            }
        });
    });

    // Remove user from team
    $(document).on("click", ".removeUser", function() {
        let li = $(this).closest("li");
        let userId = li.data("id");

        $.post("{{ route('users.removeFromTeam') }}", {
            id: userId,
            _token: "{{ csrf_token() }}"
        }, function(res) {
            if (res.status === "success") {
                li.remove();
            }
        });
    });
});
</script>
@endsection

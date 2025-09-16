@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="font-weight-500 text-primary">
                    "{{ $users->count() ? $users->first()->team_name : 'Selected' }}" Connected Users
                </h3>
                @php
                    $encoded = base64_encode($team->id . '*' . $team->team_name);
                @endphp
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a class="btn btn-primary" href="{{ route('user.manage_team_members', ['encoded' => $encodedValue]) }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Manage Team Members"><i
                            class="ti ti-users-group fs-6 me-2"></i>Manage Team Members
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
                    <div class="card shadow-none">
                        <div class="card-body p-0" id="ViewTeams_table">
                            <div class="row m-0 viewTeamsData mt-3">
                                @if($users->count() > 0)
                                    @foreach($users as $user)
                                        <div class="col-lg-3 col-md-6 col-12 mb-3 d-flex">
                                            <div
                                                class="card primary-gt p-3 text-center shadow-sm position-relative h-100 w-100 mx-auto">
                                                <img src="{{ asset('assets/images/profile_picture/profile.png') }}"
                                                    class="card-img-top mt-3" alt="Team Image">
                                                <div class="card-body">
                                                    <p class="text-primary fw-bold mb-1">
                                                        {{ $user->employee_code }}*{{ $user->employee_name }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button
                                                class="btn counsellor-rank removeUser primary-bg d-flex justify-content-center align-items-center">
                                                <i class="mdi mdi-trash-can text-white fs-6"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center mt-4">
                                        <p style="color: #888;">No records found for the selected team.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {
            var scrollbar1 = document.getElementById("ViewTeams_table");
            if (scrollbar1) {
                new PerfectScrollbar(scrollbar1, {
                    wheelPropagation: false
                });
            }
        });

        $(document).ready(function () {
            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            // Remove user from team
            $(document).on("click", ".removeUser", function () {
                let li = $(this).closest("li");
                let userId = li.data("id");

                $.post("{{ route('users.removeFromTeam') }}", {
                    id: userId,
                    _token: "{{ csrf_token() }}"
                }, function (res) {
                    if (res.status === "success") {
                        li.remove();
                    }
                });
            });

        });
    </script>
@endsection
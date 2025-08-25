@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <h3 class="font-weight-500 mb-xl-4 text-primary">
                "{{ $users->count() ? $users->first()->team_name : 'Selected' }}" Connected Teams
            </h3>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a class="btn btn-primary mdi mdi-arrow-left fs-5" href="{{ Route('user.teams') }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Back">
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
                                        <div class="col-lg-4 col-md-6 col-12 mb-3">
                                            <div class="card primary-gt p-3 text-center shadow-sm">
                                                <img src="{{ asset('assets/images/profile_picture/profile.png') }}"
                                                    class="card-img-top mt-3" alt="Team Image">
                                                <div class="card-body">
                                                    <p class="text-primary fw-bold mb-1">Member Name : {{ $user->employee_code }}*{{ $user->employee_name }}</p>
                                                </div>
                                            </div>
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

        });
    </script>
@endsection
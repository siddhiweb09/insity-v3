@extends('frames.frame')

@section('content')
    <style>
        .tab-nav .nav-link {
            color: #495057;
        }

        .tab-nav .nav-link.active {
            background-color: #4b49ac;
            color: #fff;
        }

        .user-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            background-color: #fff;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .calling-enabled {
            color: #28a745;
            margin-left: 8px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #495057;
            font-size: 1.2rem;
        }

        .table-view tbody tr:hover {
            background-color: #f1f3f5;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        #listViewTable thead {
            position: sticky;
            top: 0;
            z-index: 20;
            color: #fff;
        }

        .table tbody td,
        .table thead th {
            text-align: start !important;
        }

        .modal-content {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(120deg, var(--primary), #6c63ff);
        }

        .btn-close.opacity-75:hover {
            opacity: 1 !important;
        }

        .modal-user-avatar {
            width: 100px;
            height: 100px;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 2.5rem;
        }

        .user-status {
            bottom: 5px;
            right: 5px;
            width: 22px;
            height: 22px;
            border: 3px solid white;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin: 1.5rem 0;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            border-color: transparent;
        }

        .detail-card:hover {
            transform: translateY(-3px);
        }

        .calling-badge {
            background: #28A74526;
        }

        .last-login-badge {
            background-color: #cceeff;
        }

        .info-highlight {
            border-left: 4px solid var(--primary);
            border-right: 4px solid var(--primary);
        }
    </style>

    <div class="content-wrapper">

        <!-- Tabs -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="font-weight-500 text-primary">Manage Users</h3>
            <ul class="nav nav-tabs tab-nav mb-2" id="userTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold rounded-3 transition me-1 d-flex justify-content-center align-items-center"
                        style="padding:15px !important;" id="grid-tab" data-bs-toggle="tab" href="#gridView" role="tab"><i
                            class="ti ti-layout-grid fs-5 fw-medium"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold rounded-3 transition me-1 d-flex justify-content-center align-items-center"
                        style="padding:15px !important;" id="list-tab" data-bs-toggle="tab" href="#listView" role="tab"><i
                            class="ti ti-list-details fs-5 fw-medium"></i></a>
                </li>
            </ul>
        </div>

        <div class="tab-content">

            <!-- Grid View -->
            <div class="tab-pane fade show active" id="gridView" role="tabpanel">
                <div class="row g-3 align-items-stretch" id="gridViewTab">
                    <p>Loading Users Cards...</p>
                </div>
            </div>

            <!-- List View -->
            <div class="tab-pane fade" id="listView" role="tabpanel">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-hover align-middle mb-0" id="listViewTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Official Mobile</th>
                                <th>Runo Mobile</th>
                                <th>Designation</th>
                                <th>Branch</th>
                                <th>Zone</th>
                                <th>Last Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center">Loading Users...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
@endsection

@include('modal.modal_user_details')

@section('customJs')
    <script>
        $(document).ready(function () {

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

            function loadUsers() {
                $.ajax({
                    url: "{{ route('fetchCounselors') }}",
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        console.log("Fetch URL:", "{{ route('fetchCounselors') }}");
                        // --------- Grid View ---------
                        if (!response.users || response.count === 0) {
                            $('#gridViewTab').html('<p>No Users Found</p>');
                            $('#listViewTable tbody').html('<tr><td colspan="10">No Users Found</td></tr>');
                            return;
                        }

                        let gridHtml = '';
                        let listHtml = '';

                        $.each(response.users, function (index, user) {
                            // Calculate status and icons
                            let status = user.status ?? 'Inactive';
                            let loginDate = user.login_date ?? '-';
                            let rowClass = user.working_status == 0 ? 'card-light-danger' : '';
                            let callingIcon = user.enable_calling == 1 ? `<i class='ti ti-phone-call calling-enabled'></i>` : '';
                            let statusIndicator = status === 'Active'
                                ? '<span class="status-dot" style="background-color: green; margin-left: 5px;"></span>'
                                : '';
                            let avatarHtml = '';

                            if (user.profile_picture) {
                                // If profile picture exists
                                avatarHtml = `
                                    <img src="/assets/images/profile_picture/${user.profile_picture}"
                                        alt="${user.employee_name}" 
                                        class="user-avatar me-3 rounded-circle" 
                                        style="height:50px !important; width:50px !important; object-fit:cover;">
                                `;
                            } else {
                                // If no profile picture, show initial
                                avatarHtml = `
                                    <div class="user-avatar me-3 d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" 
                                        style="height:50px !important; width:50px !important; font-size: 1.5rem !important;">
                                        ${user.employee_name.charAt(0)}
                                    </div>
                                `;
                            }
                            // ---------- Grid Card HTML ----------
                            gridHtml += `
                                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                                        <div class="card user-card h-100">
                                            <div class="card-header ${rowClass}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        ${avatarHtml}
                                                        <div>
                                                            <h5 class="mb-1">${user.employee_name}</h5>
                                                            <small class="text-dark">${user.employee_code}</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        ${statusIndicator}${callingIcon}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="mb-3">
                                                    <p class="mb-1"><strong>Email:</strong> ${user.email_id_official}</p>
                                                    <p class="mb-0"><strong>Official Mobile:</strong> ${user.mobile_no_official}</p>
                                                </div>
                                                <button class="btn btn-outline-primary w-100 rounded-3 view-details-btn mt-auto" 
                                                    data-user='${JSON.stringify(user)}'>
                                                    <i class="ti ti-eye me-1"></i> View Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            listHtml += `
                                    <tr class="${rowClass}">
                                        <td>${user.employee_code}</td>
                                        <td>${user.employee_name}${statusIndicator}${callingIcon}</td>
                                        <td>${user.email_id_official}</td>
                                        <td>${user.mobile_no_official}</td>
                                        <td>${user.mobile_no_runo}</td>
                                        <td>${user.job_title_designation}</td>
                                        <td>${user.branch}</td>
                                        <td>${user.zone}</td>
                                        <td>${loginDate}</td>
                                    </tr>
                                `;
                        });

                        $('#gridViewTab').html(gridHtml);
                        $('#listViewTable tbody').html(listHtml);
                    },
                    error: function (err) {
                        $('#gridViewTab').html('<p>Error loading users.</p>');
                        $('#listViewTable tbody').html('<tr><td colspan="10">Error loading users.</td></tr>');
                    }
                });
            }

            $(document).on('click', '.view-details-btn', function () {
                let user = $(this).data('user');

                $('#modalUserAvatar').text(user.employee_name.charAt(0));
                $('#modalProfilePicture').text(user.profile_picture ?? null);
                // $('#modalUserAvatar').text(user.profile_picture);
                $('#modalUserName').text(user.employee_name);
                $('.modalEmployeeCode').text(user.employee_code);
                $('#modalLastLogin').text(user.login_date ?? '-');
                $('#modalStatus').text(user.status);
                $('#modalEnableCalling').text(user.enable_calling === 1 ? 'Enabled' : 'Disabled');
                $('#modalUserCategory').text(user.user_category)
                $('#modalUserGroupName').text(user.group_name)
                $('#modalUserTeamName').text(user.team_name);
                $('#modalUserStatus').text(user.status_activity);

                $('#modalPersonalEmail').text(user.email_id_personal);
                $('#modalPersonalMobile').text(user.mobile_no_personal);
                $('#modalGender').text(user.gender);
                $('#modalDOB').text(user.dob);
                $('#modalPanCard').text(user.pan_card_no_encoded); // show encoded initially
                $('#modalPanCard').data('encoded', user.pan_card_no_encoded); // store encoded
                $('#togglePanCard').text('Show')

                $('#modalOfficialMobile').text(user.mobile_no_official);
                $('#modalofficialEmail').text(user.email_id_official);
                $('#modalDepartment').text(user.department);
                $('#modalDesignation').text(user.job_title_designation);
                $('#modalBranch').text(user.branch_1);
                $('#modalZone').text(user.zone);
                $('#modalDOJ').text(user.doj);
                $('#modalUserGroupLeader').text(user.group_leader)
                $('#modalUserTeamLeader').text(user.team_leader)

                $('#modalWorkingStatus').text(user.working_status == 1 ? 'Enabled' : 'Disabled'); // Enable for Leads
                $('#modalIntFlag').text(user.int_flag == 1 ? 'Enabled' : 'Disabled');
                $('#modalInactiveStartDate').text(user.inactive_start_date);
                $('#modalInactiveEndDate').text(user.inactive_end_date);
                $('#modalEnableCallingOverlay').text(user.enable_calling_overlay == 1 ? 'Enabled' : 'Disabled'); // App Access for calling overlay
                $('#modalAppAccess').text(user.app_access == 1 ? 'Enabled' : 'Disabled'); // App Access for user
                $('#modalFirebaseToken').text(user.firebase_token); // notification Token
                $('#modalScript').text(user.script);

                $('#modalLeadSources').text(user.lead_sources);

                $('#modalTelegramToken').text(user.telegram_token);
                $('#modalTelegramChatId').text(user.telegram_chat_id);
                $('#modalTelegramChannel').text(user.telegram_channel_name);
                $('#modalTelegramUserName').text(user.telegram_user_name);

                // Parent columns
                const groupCol = $('#modalUserGroupLeader').closest('.group-leader-col');
                const teamCol = $('#modalUserTeamLeader').closest('.team-leader-col');
                const infoHighlight = $('.info-highlight');

                // Check condition: if either is empty, hide both
                if (!user.group_leader && !user.team_leader) {
                    groupCol.hide();
                    teamCol.hide();
                } else {
                    groupCol.show();
                    teamCol.show();
                }

                if (!user.group_name && !user.team_name) {
                    infoHighlight.removeClass('d-flex').addClass('d-none');
                } else {
                    infoHighlight.removeClass('d-none').addClass('d-flex');
                }
                $('#modalUserGroupName').text(user.group_name || '');
                $('#modalUserTeamName').text(user.team_name || '');

                let workingStatusIcon = $('#modalWorkingStatus').closest('.detail-card').find('.icon-container i');
                if (user.working_status == 1) {
                    // Enabled
                    workingStatusIcon.removeClass('ti-toggle-left').addClass('ti-toggle-right');
                    $('#modalWorkingStatus').text('Enabled');
                } else {
                    // Disabled
                    workingStatusIcon.removeClass('ti-toggle-right').addClass('ti-toggle-left');
                    $('#modalWorkingStatus').text('Disabled');
                }

                // Enabled For International Leads
                let intFlagIcon = $('#modalIntFlag').closest('.detail-card').find('.icon-container i');
                if (user.int_flag == 1) {
                    intFlagIcon.removeClass('ti-world-off').addClass('ti-world');
                    $('#modalIntFlag').text('Enabled');
                } else {
                    intFlagIcon.removeClass('ti-world').addClass('ti-world-off');
                    $('#modalIntFlag').text('Disabled');
                }

                if (user.profile_picture) {
                    // Show profile picture
                    $('#modalProfilePicture')
                        .attr('src', '/assets/images/profile_picture/' + user.profile_picture)
                        .removeClass('d-none');
                    $('#modalUserAvatar').removeClass('d-flex');
                    $('#modalUserAvatar').addClass('d-none');
                } else {
                    // Show avatar with initial
                    $('#modalUserAvatar')
                        .text(user.employee_name.charAt(0).toUpperCase())
                        .removeClass('d-none');
                    $('#modalProfilePicture').addClass('d-none');
                }

                if (user.enable_calling === 1) {
                    $('#callingBadge').removeClass('d-none').addClass('d-flex');
                } else {
                    $('#callingBadge').removeClass('d-flex').addClass('d-none');
                }

                if (user.status_activity === 'Active') {
                    $('.user-status').removeClass('d-none').addClass('d-flex');
                } else {
                    $('.user-status').removeClass('d-flex').addClass('d-none');
                }
                let modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
                modal.show();
            });

            // Load users on page ready
            loadUsers();

            $(document).on('click', '#togglePanCard', function () {
                const panSpan = $('#modalPanCard');

                if ($(this).text() === 'Show') {
                    // Decode Base64 and show actual PAN
                    let encoded = panSpan.data('encoded'); // stored in data attribute
                    let decoded = atob(encoded);
                    panSpan.text(decoded);
                    $(this).text('Hide');
                } else {
                    // Show encoded PAN
                    let encoded = panSpan.data('encoded');
                    panSpan.text(encoded);
                    $(this).text('Show');
                }
            });

        });
    </script>
@endsection
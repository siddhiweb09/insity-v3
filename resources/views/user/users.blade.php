@extends('frames.frame')

@section('content')
    <style>
        .tab-nav .nav-link {
            color: #495057;
        }

        .transition {
            transition: all 0.3s;
        }

        .tab-nav .nav-link.active {
            background-color: #4b49ac;
            color: #fff;
        }

        /* -------------------- Grid Cards -------------------- */
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

        :root {
            --primary-color: #4b49ac;
            --secondary-color: #f8f9fa;
            --accent-color: #f5f7ff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --success-color: #28a745;
            --warning-color: #ffc107;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(120deg, var(--primary-color), #6c63ff);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 2.5rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .user-status {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 3px solid white;
        }

        .status-active {
            background-color: var(--success-color);
        }

        .status-inactive {
            background-color: #dc3545;
        }

        .user-name {
            font-weight: 700;
            font-size: 1.6rem;
            margin-top: 1rem;
            color: var(--text-primary);
        }

        .user-code {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin: 1.5rem 0;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.8rem 1.2rem;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-color: transparent;
        }

        .detail-card {
            background: var(--secondary-color);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s;
        }

        .detail-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .icon-container {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.2rem 1.5rem;
        }

        .btn-modal {
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .tab-content {
            padding: 0 0.5rem;
        }

        .calling-badge {
            background: rgba(40, 167, 69, 0.15);
            color: var(--success-color);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin-left: 0.8rem;
        }

        .last-login-badge {
            background: rgba(255, 193, 7, 0.15);
            color: #e0a800;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
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
                <div class="row" id="gridViewTab">
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
                    url: "{{ route('fetchAllUsers') }}",
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        console.log("Fetch URL:", "{{ route('fetchAllUsers') }}");
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
                            // ---------- Grid Card HTML ----------
                            gridHtml += `
                                        <div class="col-md-6 col-12 mb-4">
                                            <div class="card user-card">
                                                <div class="card-header ${rowClass}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar me-3">${user.employee_name.charAt(0)}</div>
                                                            <div>
                                                                <h5 class="mb-0">${user.employee_name}</h5>
                                                                <small class="text-muted">${user.employee_code}</small>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            ${statusIndicator}${callingIcon}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <p class="mb-1"><strong>Email:</strong> ${user.email_id_official}</p>
                                                        <p class="mb-0"><strong>Official Mobile:</strong> ${user.mobile_no_official}</p>
                                                    </div>
                                                    <button class="btn btn-outline-primary w-100 rounded-3 view-details-btn" 
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
                $('#modalUserName').text(user.employee_name);
                $('#modalEmployeeCode').text(user.employee_code);
                $('#modalEmail').text(user.email_id_official);
                $('#modalOfficialMobile').text(user.mobile_no_official);
                $('#modalRunoMobile').text(user.mobile_no_runo);
                $('#modalDesignation').text(user.job_title_designation);
                $('#modalBranch').text(user.branch);
                $('#modalZone').text(user.zone);
                $('#modalEmailPassword').text(user.email_id_password);
                $('#modalScript').text(user.script);
                $('#modalLastLogin').text(user.login_date ?? '-');
                $('#modalTelegramToken').text(user.telegram_token);
                $('#modalTelegramChatId').text(user.telegram_chat_id);
                $('#modalTelegramChannel').text(user.telegram_channel_name);

                let modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
                modal.show();
            });

            // Load users on page ready
            loadUsers();

        });
    </script>
@endsection
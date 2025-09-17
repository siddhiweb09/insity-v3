@extends('frames.frame')

@section('content')
    <style>
        .accordion-button::after {

            content: unset;
        }

        .table-responsive .table tbody tr:last-child td {
            border-bottom: none;
        }

        .accordion-item:last-child {
            border-bottom: none;
        }

        .table {
            table-layout: fixed;
            /* forces equal distribution */
            width: 100%;
            /* full width of container */
        }

        .table th,
        .table td {
            text-align: center;
            /* optional: center align content */
            overflow: hidden;
            /* hide overflow */
            text-overflow: ellipsis;
            /* add "..." if text too long */
            white-space: nowrap;
            /* keep text in one line */
        }
    </style>
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <form id="manage-team-members" method="POST">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-500 text-primary mb-0">Manage Team Members</h3>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="submit" id="submitForm" class="btn btn-sm px-4 btn-primary">
                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                        </button>
                        <button type="button" id="cancel" class="btn btn-sm px-4 btn-danger">
                            <i class="ti ti-x me-2"></i>Cancel
                        </button>
                    </div>
                </div>
                <div class="row m-0">
                    <div class="col-12 col-md-6 col-lg-6">
                        <p class="card-text fw-bold">Team Name:</p>
                        <h4 class="card-title" id="teamName">{{ $teamName }}</h4>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <p class="card-text fw-bold">Team leader:</p>
                        <h4 class="card-title" id="teamId">{{ $leader ?? 'N/A' }}</h4>
                    </div>
                </div>
                <nav>
                    <div class="nav nav-tabs manage-user-navtabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-users-tab" data-bs-toggle="tab" data-bs-id="Active Users"
                            data-bs-target="#nav-users" type="button" role="tab" aria-controls="nav-users"
                            aria-selected="true">Active Users</button>
                        <button class="nav-link" id="nav-sources-tab" data-bs-toggle="tab" data-bs-id="Lead Sources"
                            data-bs-target="#nav-sources" type="button" role="tab" aria-controls="nav-sources"
                            aria-selected="false">Lead Sources</button>
                        <button class="nav-link" id="nav-access-tab" data-bs-toggle="tab" data-bs-id="Access Level"
                            data-bs-target="#nav-access" type="button" role="tab" aria-controls="nav-access"
                            aria-selected="false">Access Level</button>
                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                            data-bs-id="Email & Phone Configuration" data-bs-target="#nav-contact" type="button" role="tab"
                            aria-controls="nav-contact" aria-selected="false">Email & Phone
                            Configuration</button>
                    </div>
                </nav>
                <div class="tab-content p-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-users" role="tabpanel" aria-labelledby="nav-users-tab">
                        <div class="card overflow-hidden pt-3" style="height: 60vh">
                            <div class="card-body p-0" id="activeUsersView">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="coustom-thead bg-white" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th>Members</th>
                                                <th>
                                                    <div class="custom-control custom-switch">
                                                        <label class="custom-control-label pt-1"
                                                            for="masterActiveFlagSwitch">General Lead
                                                            Allocation</label>
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="masterActiveFlagSwitch" data-user-id="a">
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="custom-control custom-switch">
                                                        <label class="custom-control-label pt-1"
                                                            for="masterIntFlagSwitch">International
                                                            Lead
                                                            Allocation</label>
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="masterIntFlagSwitch">
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="custom-control custom-switch">
                                                        <label class="custom-control-label pt-1"
                                                            for="masterCallFlagSwitch">Enabled Calling
                                                        </label>
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="masterCallFlagSwitch">
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="activeUsersTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-sources" role="tabpanel" aria-labelledby="nav-sources-tab">
                        <div class="card overflow-hidden pt-3" style="height: 60vh">
                            <div class="card-body p-0" id="leadSourcesView">
                                <div class="leadSourcesBody"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-access" role="tabpanel" aria-labelledby="nav-access-tab">
                        <div class="card overflow-hidden pt-3" style="height: 60vh">
                            <div class="card-body p-0" id="accessLevelView">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="coustom-thead bg-white" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th>Members</th>
                                                <th>Want to Change to</th>
                                                <th>Current Access Level</th>
                                            </tr>
                                        </thead>
                                        <tbody class="accessLevelBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="card overflow-hidden pt-3" style="height: 60vh">
                            <div class="card-body p-0" id="configurationView">
                                <div class="table-responsive">
                                    <table class="table table-equal-cells">
                                        <thead class="coustom-thead bg-white" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th>Members</th>
                                                <th>Want to Change to</th>
                                                <th>Current Configuration</th>
                                            </tr>
                                        </thead>
                                        <tbody class="configurationBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-primary mt-3 p-2" role="alert">
                        <strong>Note:</strong> If you need to update the team name, please contact the web team.
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
        });

        $(document).ready(function () {

            let teamName = $('#teamName').text();
            let teamId = $('#teamId').text();
            // Initialize PerfectScrollbar for each view
            const scrollbar1 = new PerfectScrollbar("#activeUsersView", {
                wheelPropagation: false
            });
            const scrollbar2 = new PerfectScrollbar("#leadSourcesView", {
                wheelPropagation: false
            });
            const scrollbar3 = new PerfectScrollbar("#accessLevelView", {
                wheelPropagation: false
            });
            const scrollbar4 = new PerfectScrollbar("#configurationView", {
                wheelPropagation: false
            });

            // Data storage object
            const formDataCache = {
                activeUsers: [],
                leadSources: {},
                accessLevel: [],
                configuration: [],
                allLeadSources: []
            };

            // Initialize all data
            function initializeAllData() {
                const tabs = ['Active Users', 'Lead Sources', 'Access Level', 'Email & Phone Configuration'];

                // First load lead sources (needed for the Lead Sources tab)
                fetchLeadSources().then(() => {
                    // Then load team members for each tab
                    tabs.forEach(tab => {
                        fetchBranchMembers(tab, true);
                    });
                });
            }

            // Fetch all lead sources
            function fetchLeadSources() {
                return $.ajax({
                    type: "POST",
                    url: "{{ route('fetchActiveLeadSources') }}",
                    dataType: "json",
                    success: function (response) {
                        if (response.sources && response.sources.length > 0) {
                            formDataCache.allLeadSources = response.sources;
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching lead sources:", error);
                    }
                });
            }

            // Fetch team members for a specific tab
            function fetchBranchMembers(tabValue, initialLoad = false) {
                $.ajax({
                    url: "{{ route('fetchteamInfo') }}",
                    type: "POST",
                    data: {
                        team_name: teamName,
                        tabValue: tabValue
                    },
                    dataType: "json",
                    beforeSend: function () {
                        if (!initialLoad) {
                            const bodyClass = getTabBodyClass(tabValue);
                            $(`.${bodyClass}`).html(`<tr><td colspan="3" class="text-center">Loading...</td></tr>`);
                        }
                    },
                    success: function (response) {
                        // Store the data in cache
                        const cacheKey = getCacheKey(tabValue);
                        formDataCache[cacheKey] = response.data;

                        // For Lead Sources tab, store the sources by employee code
                        if (tabValue === "Lead Sources") {
                            formDataCache.leadSources = response.data; // always array
                        }

                        // If this is the active tab or initial load, render it
                        if ($(`#nav-${getTabId(tabValue)}-tab`).hasClass('active') || initialLoad) {
                            renderTabData(tabValue, response.data);
                        }
                    },
                    error: function () {
                        alert("Error fetching team members");
                    }
                });
            }

            // Helper functions
            function getTabBodyClass(tabValue) {
                const map = {
                    'Active Users': 'activeUsersTableBody',
                    'Lead Sources': 'leadSourcesBody',
                    'Access Level': 'accessLevelBody',
                    'Email & Phone Configuration': 'configurationBody'
                };
                return map[tabValue] || '';
            }

            function getCacheKey(tabValue) {
                const map = {
                    'Active Users': 'activeUsers',
                    'Lead Sources': 'leadSources',
                    'Access Level': 'accessLevel',
                    'Email & Phone Configuration': 'configuration'
                };
                return map[tabValue] || '';
            }

            function getTabId(tabValue) {
                const map = {
                    'Active Users': 'users',
                    'Lead Sources': 'sources',
                    'Access Level': 'access',
                    'Email & Phone Configuration': 'contact'
                };
                return map[tabValue] || '';
            }

            // Render tab data
            function renderTabData(tabValue, data) {
                const bodyClass = getTabBodyClass(tabValue);
                $(`.${bodyClass}`).empty();

                if (tabValue === "Active Users") {
                    let allGeneralChecked = true;
                    let allIntChecked = true;
                    let allCallChecked = true;
                    data.forEach((item, index) => {
                        const isGeneralChecked = item.working_status === 1;
                        const isIntChecked = item.int_flag === 1;
                        const isCallChecked = item.enable_calling === 1;

                        if (!isGeneralChecked) allGeneralChecked = false;
                        if (!isIntChecked) allIntChecked = false;
                        if (!isCallChecked) allCallChecked = false;

                        const generalSwitchId = `activeFlagSwitch_${index}`;
                        const intSwitchId = `intFlagSwitch_${index}`;
                        const callSwitchId = `callFlagSwitch_${index}`;

                        $('.activeUsersTableBody').append(`
                                                                                                                                <tr>
                                                                                                                                    <td>${item.employee_code}*${item.employee_name}</td>
                                                                                                                                    <td>
                                                                                                                                        <div class="custom-control custom-switch">
                                                                                                                                            <input type="checkbox" class="custom-control-input member-general-switch" 
                                                                                                                                                id="${generalSwitchId}" ${isGeneralChecked ? 'checked' : ''}
                                                                                                                                                data-user-id="${item.employee_code}">
                                                                                                                                            <label class="custom-control-label" for="${generalSwitchId}"></label>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <div class="custom-control custom-switch">
                                                                                                                                            <input type="checkbox" class="custom-control-input member-int-switch" 
                                                                                                                                                id="${intSwitchId}" ${isIntChecked ? 'checked' : ''}
                                                                                                                                                data-user-id="${item.employee_code}">
                                                                                                                                            <label class="custom-control-label" for="${intSwitchId}"></label>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <div class="custom-control custom-switch">
                                                                                                                                            <input type="checkbox" class="custom-control-input member-call-switch" 
                                                                                                                                                id="${callSwitchId}" ${isCallChecked ? 'checked' : ''}
                                                                                                                                                data-user-id="${item.employee_code}">
                                                                                                                                            <label class="custom-control-label" for="${callSwitchId}"></label>
                                                                                                                                        </div>
                                                                                                                                    </td>                         
                                                                                                                                </tr>
                                                                                                                            `);
                    });

                    // Set master switches
                    $('#masterActiveFlagSwitch').prop('checked', allGeneralChecked);
                    $('#masterIntFlagSwitch').prop('checked', allIntChecked);
                    $('#masterCallFlagSwitch').prop('checked', allCallChecked);

                    // After rendering rows in Active Users tab
                    $('.member-general-switch').on('change', function () {
                        const allChecked = $('.member-general-switch').length === $('.member-general-switch:checked').length;
                        $('#masterActiveFlagSwitch').prop('checked', allChecked);
                    });

                    $('.member-int-switch').on('change', function () {
                        const allChecked = $('.member-int-switch').length === $('.member-int-switch:checked').length;
                        $('#masterIntFlagSwitch').prop('checked', allChecked);
                    });

                    $('.member-call-switch').on('change', function () {
                        const allChecked = $('.member-call-switch').length === $('.member-call-switch:checked').length;
                        $('#masterCallFlagSwitch').prop('checked', allChecked);
                    });


                } else if (tabValue === "Access Level") {
                    data.forEach((item, index) => {
                        $('.accessLevelBody').append(`
                                                                                                                                <tr>
                                                                                                                                    <td>${item.employee_code}*${item.employee_name}</td>
                                                                                                                                    <td>
                                                                                                                                        <select class="form-control form-control-sm access-level-select" 
                                                                                                                                            data-user-id="${item.employee_code}">
                                                                                                                                            <option value="">Select New Access Level</option>
                                                                                                                                            <option value="Team Leader" ${item.user_category === 'Team Leader' ? 'selected' : ''}>Team Leader</option>
                                                                                                                                            <option value="Counsellor" ${item.user_category === 'Counsellor' ? 'selected' : ''}>Counsellor</option>
                                                                                                                                        </select>
                                                                                                                                    </td>
                                                                                                                                    <td><p>${item.user_category}</p></td>                       
                                                                                                                                </tr>
                                                                                                                            `);
                    });

                } else if (tabValue === "Email & Phone Configuration") {
                    data.forEach((item, index) => {
                        $('.configurationBody').append(`
                                                                                                                                <tr>
                                                                                                                                    <td>${item.employee_code}*${item.employee_name}</td>
                                                                                                                                    <td>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-12 mb-3">
                                                                                                                                                <input type="text" class="form-control form-control-sm config-field" 
                                                                                                                                                    data-field="telegram_token" data-user-id="${item.employee_code}"
                                                                                                                                                    value="${item.telegram_token || ''}"
                                                                                                                                                    placeholder="Telegram Bot Token">
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12 mb-3">
                                                                                                                                                <input type="text" class="form-control form-control-sm config-field"
                                                                                                                                                    data-field="telegram_chat_id" data-user-id="${item.employee_code}"
                                                                                                                                                    value="${item.telegram_chat_id || ''}"
                                                                                                                                                    placeholder="Telegram Chat ID">
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12 mb-3">
                                                                                                                                                <input type="text" class="form-control form-control-sm config-field"
                                                                                                                                                    data-field="telegram_channel_name" data-user-id="${item.employee_code}"
                                                                                                                                                    value="${item.telegram_channel_name || ''}"
                                                                                                                                                    placeholder="Telegram Channel Name">
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12 mb-3">
                                                                                                                                                <input type="text" class="form-control form-control-sm config-field"
                                                                                                                                                    data-field="script" data-user-id="${item.employee_code}"
                                                                                                                                                    value="${item.script || ''}"
                                                                                                                                                    placeholder="Script name">
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12 mb-3">
                                                                                                                                                <input type="text" class="form-control form-control-sm config-field"
                                                                                                                                                    data-field="mobile_no_official" data-user-id="${item.employee_code}"
                                                                                                                                                    value="${item.mobile_no_personal || ''}"
                                                                                                                                                    placeholder="Official Mobile Number">
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <div class="row justify-content-between h-100">
                                                                                                                                            <div class="col-12">
                                                                                                                                                <label class="form-label py-0">Telegram Bot Token</label>
                                                                                                                                                <p>${item.telegram_token || 'Not set'}</p>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12">
                                                                                                                                                <label class="form-label py-0">Telegram Chat Id</label>
                                                                                                                                                <p>${item.telegram_chat_id || 'Not set'}</p>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12">
                                                                                                                                                <label class="form-label py-0">Telegram Channel Name</label>
                                                                                                                                                <p>${item.telegram_channel_name || 'Not set'}</p>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12">
                                                                                                                                                <label class="form-label py-0">Script</label>
                                                                                                                                                <p>${item.script || 'Not set'}</p>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-12">
                                                                                                                                                <label class="form-label py-0">Official Mobile No</label>
                                                                                                                                                <p>${item.mobile_no_personal || 'Not set'}</p>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </td>                       
                                                                                                                                </tr>
                                                                                                                            `);
                    });

                } else if (tabValue === "Lead Sources") {
                    data.forEach((item, index) => {
                        const employeeId = item.employee_code;

                        $('.leadSourcesBody').append(`
                                                                                                        <div class="accordion mb-3" id="accordion_${employeeId}">
                                                                                                            <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="heading_${employeeId}">
                                                                                                                <button class="accordion-button collapsed d-flex justify-content-between align-items-center" 
                                                                                                                    type="button" data-bs-toggle="collapse" 
                                                                                                                    data-bs-target="#collapse_${employeeId}" aria-expanded="false" 
                                                                                                                    aria-controls="collapse_${employeeId}">

                                                                                                                    <!-- Left side: Employee name -->
                                                                                                                    <span class="fw-semibold">${item.employee_code} * ${item.employee_name}</span>

                                                                                                                    <!-- Right side: Select All checkbox -->
                                                                                                                    <div class="form-group mb-0">
                                                                                                                        <input type="checkbox" class="form-check-input selectAllSources" 
                                                                                                                            data-employee-id="${employeeId}" id="selectAll_${employeeId}" />
                                                                                                                        <label class="mb-0 form-check-label mt-1" for="selectAll_${employeeId}">Select All</label>
                                                                                                                    </div>
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="collapse_${employeeId}" class="accordion-collapse collapse" 
                                                                                                                aria-labelledby="heading_${employeeId}" data-bs-parent="#accordion_${employeeId}">
                                                                                                                <div class="accordion-body">
                                                                                                                    <div id="lead_sources_container_${employeeId}" class="row m-0 p-2"></div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        </div>
                                                                                                    `);

                        // Render lead sources for this employee
                        renderEmployeeLeadSources(employeeId, item.lead_sources || '');
                    });
                }
            }

            // Render lead sources for a specific employee
            function renderEmployeeLeadSources(employeeId, currentSources = '') {
                const container = $(`#lead_sources_container_${employeeId}`);
                container.empty();

                if (!formDataCache.allLeadSources || formDataCache.allLeadSources.length === 0) {
                    container.html('<div class="col-12 text-center py-3">No lead sources available</div>');
                    return;
                }

                // Get the current sources for this employee
                const currentSourcesStr = formDataCache.leadSources[employeeId] || currentSources || '';
                const currentSourcesArray = currentSourcesStr ? currentSourcesStr.split(',') : [];

                let html = '<div class="col-12">';
                html += '<div class="ms-md-3 border row p-3 mb-3 rounded bg-light">';

                // Check if any sources should be selected by default
                let allChecked = true;
                const checkboxes = [];

                formDataCache.allLeadSources.forEach((source, index) => {
                    const isChecked = currentSourcesArray.includes(source.trim());
                    if (!isChecked) allChecked = false;

                    checkboxes.push(`
                                                                                                                    <div class="form-check mb-1 col-md-3 col-sm-12" style="text-wrap: auto;">
                                                                                                                        <input class="form-check-input source-item-checkbox"
                                                                                                                            type="checkbox" data-employee-id="${employeeId}" value="${source}"
                                                                                                                            id="source_${employeeId}_${index}" ${isChecked ? 'checked' : ''}>
                                                                                                                        <label class="form-check-label" for="source_${employeeId}_${index}">
                                                                                                                            ${source}
                                                                                                                        </label>
                                                                                                                    </div>
                                                                                                                    `);
                });

                // Set the "Select All" checkbox state
                $(`input.selectAllSources[data-employee-id="${employeeId}"]`).prop('checked', allChecked);

                html += checkboxes.join('');
                html += '</div></div>';
                container.append(html);
            }

            // Event handlers
            $(document).on("change", ".selectAllSources", function () {
                const isChecked = $(this).prop("checked");
                const employeeId = $(this).data("employee-id");

                $(`#lead_sources_container_${employeeId} .source-item-checkbox`)
                    .prop("checked", isChecked);
            });

            // Handle "Check All" for International Leads
            $('#masterIntFlagSwitch').change(function () {
                $('.member-int-switch').prop('checked', $(this).prop('checked'));
            });

            $('#masterCallFlagSwitch').change(function () {
                $('.member-call-switch').prop('checked', $(this).prop('checked'));
            });

            // Handle "Check All" for General Leads
            $('#masterActiveFlagSwitch').change(function () {
                $('.member-general-switch').prop('checked', $(this).prop('checked'));
            });

            // Tab change handler - render from cache
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const tabValue = e.target.getAttribute('data-bs-id');
            })

            // Form submission - collect data from all tabs
            $(document).on('click', '#submitForm', function () {
                const formData = new FormData();

                // Add team identification data
                formData.append('team_id', teamId);
                formData.append('team_name', teamName);

                // Collect data from all tabs
                collectActiveUsersData(formData);
                collectLeadSourcesData(formData);
                collectAccessLevelData(formData);
                collectConfigurationData(formData);

                // Submit all data
                $.ajax({
                    url: '{{ route('updateTeamInfo') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json', // <- tell jQuery to expect JSON
                    beforeSend: function () {
                        $('#submitForm').prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
                    },
                    success: function (result) {
                        if (result.success) {
                            showToast('success', result.message || 'Changes saved successfully');
                            initializeAllData();
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast('danger', result.message || 'Error saving changes'); // Bootstrap uses 'bg-danger' not 'bg-error'
                        }
                    },
                    error: function (xhr, status, error) {
                        let msg = 'Error: ' + error;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast('danger', msg);
                        console.error('AJAX error:', error, xhr.responseText);
                    },
                    complete: function () {
                        $('#submitForm').prop('disabled', false).html('Save Changes');
                    }
                });

            });

            // Data collection functions
            function collectActiveUsersData(formData) {
                formDataCache.activeUsers.forEach((user, index) => {
                    const employeeCode = user.employee_code;
                    const generalChecked = $(`.member-general-switch[data-user-id="${employeeCode}"]`).is(':checked') ? '1' : '0';
                    const intChecked = $(`#intFlagSwitch_${index}`).is(':checked') ? '1' : '0';
                    const callChecked = $(`#callFlagSwitch_${index}`).is(':checked') ? '1' : '0';

                    formData.append(`active_users[${employeeCode}][general]`, generalChecked);
                    formData.append(`active_users[${employeeCode}][international]`, intChecked);
                    formData.append(`active_users[${employeeCode}][calling]`, callChecked);
                });
            }

            function collectLeadSourcesData(formData) {
                formDataCache.leadSources.forEach(user => {
                    const employeeCode = user.employee_code;
                    const selectedSources = [];

                    $(`#lead_sources_container_${employeeCode} .source-item-checkbox:checked`).each(function () {
                        selectedSources.push($(this).val());
                    });

                    formData.append(`lead_sources[${employeeCode}]`, selectedSources.join(','));
                });
            }

            function collectAccessLevelData(formData) {
                $('.access-level-select').each(function () {
                    const employeeCode = $(this).data('user-id');
                    const newLevel = $(this).val();

                    if (newLevel) {
                        formData.append(`access_level[${employeeCode}]`, newLevel);
                    }
                });
            }

            function collectConfigurationData(formData) {
                $('.config-field').each(function () {
                    const employeeCode = $(this).data('user-id');
                    const fieldName = $(this).data('field');
                    const value = $(this).val();

                    if (value) {
                        formData.append(`configuration[${employeeCode}][${fieldName}]`, value);
                    }
                });
            }

            // Initialize all data on page load
            initializeAllData();
        });

        function showToast(type, message) {
            const toast = $(`
                    <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `);

            $('#toastContainer').append(toast);
            const bsToast = new bootstrap.Toast(toast[0], { delay: 5000 });
            bsToast.show();

            toast.on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }
    </script>
@endsection
{{-- resources/views/leads/index.blade.php --}}
@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="card overflow-hidden p-4" style="height: 80vh">
        <div class="row justify-content-between mb-4 mx-0">
            <h4 class="card-title">{{ $stageName }}</h4>
            <div class="col-lg-8 col-md-8 p-0">
                <div class="row">
                    <div class="col-12 col-md-6 col-xl-6">
                        <input type="text" id="date-range"
                            class="btn btn-light bg-white dropdown-toggle text-right ml-auto d-flex" />
                    </div>
                    <div class="col-12 col-md-6 col-xl-6">
                        <select class="form-control date_source js-example-basic-single w-100" name="date_source" required>
                            <option value="lead_assignment_date" selected>Lead Assignment Date</option>
                            <option value="last_lead_activity_date">Last Lead Activity Date</option>
                            <option value="last_enquirer_activity_date">Last Enquirer Activity Date</option>
                            <option value="recording_date">Call Recording Date</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-between mb-4 mx-0">
            <div class="btn-group" role="group" aria-label="Basic example">
                <!-- @dump(session('action_buttons')) -->
                @php
                use Illuminate\Support\Str;

                $actions = session('action_buttons', []);
                $category = 'Lead Manager'; // <- change this if you want another category
                    $cfg=$actions[$category] ?? null;

                    // e.g. "leadManagerTable" for aria-controls
                    $tableId=lcfirst(Str::studly($category)) . 'Table' ;
                    @endphp

                    @if($cfg && !empty($cfg['items']))
                    @foreach($cfg['items'] as $item)
                    @php
                    $label=$item['name'] ?? 'Action' ;
                    $css=$item['class'] ?? Str::slug($label, '-' );
                    // use item icon if present; else fall back to category icon; else default
                    $icon=$item['icon'] ?? ($cfg['icon'] ?? 'mdi-filter' );
                    @endphp

                    <button
                    type="button"
                    id="{{ $css }}Btn"
                    class="btn btn-primary mdi {{ $icon }} action-btn {{ $css }}"
                    tabindex="0"
                    aria-controls="{{ $tableId }}"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="{{ $label }}"
                    aria-label="{{ $label }}"
                    data-action="{{ $css }}"
                    data-category="{{ $category }}">
                    </button>
                    @endforeach
                    @else
                    {{-- Optional: nothing to render --}}
                    @endif

                    <button class="btn btn-primary mdi mdi-filter filters" tabindex="0"
                        aria-controls="leadManagerTable" type="button" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="" data-bs-original-title="Filters"
                        aria-label="Filters"><span></span></button>
                    <button class="btn btn-primary mdi mdi-download download-csv" tabindex="0"
                        aria-controls="leadManagerTable" type="button" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="" data-bs-original-title="Download"
                        aria-label="Download"><span></span></button>
                    <!-- <button class="btn btn-primary mdi mdi-view-carousel columns-visibility" tabindex="0"
                    aria-controls="leadManagerTable" type="button" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="" data-bs-original-title="Show Columns"
                    aria-label="Show Columns"><span></span></button> -->
                    <button class="btn btn-primary mdi mdi-reload clear-filter" tabindex="0"
                        aria-controls="leadManagerTable" type="button" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="" data-bs-original-title="Clear Filter"
                        aria-label="Clear Filter"><span></span></button>
            </div>
            <div class="col-lg-4 col-md-4 p-0">
                <input type="text" id="searchInput" class="form-control ml-auto "
                    placeholder="Search...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Lead Owner</th>
                        <th>Lead Source</th>
                        <th>Lead Stage</th>
                        <th>Assigned Date</th>
                        <th>Branch</th>
                        <th>Zone</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->lead_owner }}</td>
                        <td>{{ $lead->lead_source }}</td>
                        <td>{{ $lead->lead_stage }}</td>
                        <td>{{ \Carbon\Carbon::parse($lead->lead_assignment_date)->format('Y-m-d') }}</td>
                        <td>{{ $lead->branch }}</td>
                        <td>{{ $lead->zone }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">No leads found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('offcanvas.offcanvas_recommendation_leads')
@include('offcanvas.offcanvas_add_application_id')
@include('offcanvas.offcanvas_reassign_leads')
@include('offcanvas.offcanvas_filters')
@include('offcanvas.offcanvas_leads_table')
@include('offcanvas.offcanvas_leads_upload')
@include('offcanvas.offcanvas_assign_lead')
@endsection

@section('customJs')
<script>
    var dateRange = "";
    var currentdateRange = "";
    var active_user = "<?php echo session('employee_code') ?>";
    var page = window.location.href;
    var pageSegments = page.split('/');
    // var startDate = moment().startOf('week');
    // var endDate = moment().endOf('week');
    var startDate = moment().subtract(7, 'days').startOf('day');
    var endDate = moment().endOf('day');
    var date_source = $(".date_source").val();

    $('#date-range').daterangepicker({
        opens: 'left',
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: startDate,
        endDate: endDate
    }, function(start, end) {
        dateRange = start.format('YYYY-MM-DD') + '*' + end.format('YYYY-MM-DD');
        fetch_data_tLeads(dateRange, date_source);
        updateURLWithDateRange(dateRange); // Update the URL with selected date range
    });

    $(".date_source").on("change", function() {
        var date_source = $(this).val();
        console.log("date_source inner:" + date_source);
        fetch_data_tLeads(currentdateRange, date_source);
    });

    var daterangeSegments = pageSegments[3].split('?');
    if (!daterangeSegments[1] || daterangeSegments[1].length === 0) {
        page = pageSegments[3];
        currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
    } else {
        page = daterangeSegments[0];
        currentdateRange = daterangeSegments[1];
    }

    function updateURLWithDateRange(dateRange) {
        var currentURL = window.location.href.split('?')[0]; // Clean URL without query params
        var newURL = currentURL + '?' + encodeURIComponent(dateRange);
        history.pushState(null, "", newURL); // Update the URL without reloading the page
    }
    var scrollbar1 = document.getElementById("leadManager");

    if (scrollbar1) {
        new PerfectScrollbar(scrollbar1, {
            wheelPropagation: false
        });
    }

    // });
    var today = new Date();
    var formatDate = function(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        return year + '-' + month + '-' + day;
    };

    fetch_data_tLeads(currentdateRange, date_source);

    function fetch_data_tLeads(dateRange, date_source) {
        var tableBody = $("#leadManagerBody");
        $('#entries').empty();
        $('#entries').text(0);
        tableBody.empty();
        $.ajax({
            url: "lmScript/fetch_leads.php",
            type: "GET",
            data: {
                dateRange: dateRange,
                page: page,
                date_source: date_source
            },
            dataType: "json",
            success: function(response) {
                if (response && response.result && Array.isArray(response.result)) {
                    var count = response.result.length;
                    $('#entries').text(count);
                    response.result.forEach(item => {
                        var accessRow = ``;
                        if (page === "unallocated-leads") {
                            accessRow = `<button class="btn text-decoration-underline assginLead" type="button"
                                        data-bs-toggle="offcanvas" data-bs-target="#assginLeadsOffcanvas"
                                        aria-controls="assginLeadsOffcanvas"
                                        data-id="${btoa(item.log_id || '')}">
                                        ${item.registered_name || ''}
                                <span class="badge badge-primary ml-2">
                                    ${item.registration_attempts || ''}
                                </span>
                                    </button>`;
                        } else {
                            accessRow = `<a href="lead-details?info=${btoa(item.id || '')}">
                                ${item.registered_name || ''}
                                <span class="badge badge-primary ml-2">
                                    ${item.registration_attempts || ''}
                                </span>
                            </a>`;
                        }
                        var tableRow = `<tr>

                                <td id="col-td-1">

                                    <input type="checkbox" 

                                value="${(item.id || '')}*${(item.registered_name || '')}" name="check" id="check">

                                </td>

                                <td id="col-td-2">${accessRow}</td>

                                <td id="col-td-3" style="display:none" >${item.registered_name || ''}</td>

                                <td id="col-td-3">${item.registered_email || ''}</td>

                                <td id="col-td-4">${item.registered_mobile || ''}</td>

                                <td id="col-td-5">${item.state || ''}</td>

                                <td id="col-td-6">${item.city || ''}</td>

                                <td id="col-td-44">${item.branch || ''}</td>

                                <td id="col-td-45">${item.zone || ''}</td>

                                <td id="col-td-7">${item.user_registration_date || ''}</td>

                                <td id="col-td-18">${item.lead_owner || ''}</td>

                                <td id="col-td-19">${item.lead_origin || ''}</td>

                                <td id="col-td-20">${item.lead_source || ''}</td>

                                <td id="col-td-23">${item.lead_stage || ''}</td>

                                <td id="col-td-24">${item.lead_sub_stage || ''}</td>

                                <td id="col-td-26">${item.lead_remark || ''}</td>

                                <td id="col-td-8">${item.stage_change_count || ''}</td>

                                <td id="col-td-9">${item.registration_attempts || ''}</td>

                                <td id="col-td-10">${item.notes_count || ''}</td>

                                <td id="col-td-11">${item.followup_count || ''}</td>

                                <td id="col-td-12">${item.registered_country || ''}</td>

                                <td id="col-td-13">${item.utm_source || ''}</td>

                                <td id="col-td-14">${item.utm_medium || ''}</td>

                                <td id="col-td-15">${item.utm_campaign || ''}</td>

                                <td id="col-td-44">${item.utm_adgroup || ''}</td>

                                <td id="col-td-45">${item.utm_term || ''}</td>

                                <td id="col-td-16">${item.level_applying_for || ''}</td>

                                <td id="col-td-17">${item.course || ''}</td>

                                <td id="col-td-21">${item.alternate_mobile || ''}</td>

                                <td id="col-td-22">${item.current_url || ''}</td>

                                <td id="col-td-25">${item.lead_followup_date || ''}</td>

                                <td id="col-td-27">${item.reassigned || ''}</td>

                                <td id="col-td-28">${item.assign_reassigned_by || ''}</td>

                                <td id="col-td-29">${item.reassigned_on || ''}</td>

                                <td id="col-td-30">${item.lead_score || ''}</td>

                                <td id="col-td-31">${item.mobile_verification_status || ''}</td>

                                <td id="col-td-32">${item.email_verification_status || ''}</td>

                                <td id="col-td-33">${item.lead_verification_date || ''}</td>

                                <td id="col-td-34">${item.email_sent_count || ''}</td>

                                <td id="col-td-35">${item.whatsapp_message_count || ''}</td>

                                <td id="col-td-36">${item.sms_sent_count || ''}</td>

                                <td id="col-td-37">${item.widget_name || ''}</td>

                                <td id="col-td-38">${item.application_submitted || ''}</td>

                                <td id="col-td-39">${item.last_lead_activity_date || ''}</td>

                                <td id="col-td-44">${item.last_enquirer_activity_date || ''}</td>

                                <td id="col-td-45">${item.enquirer_activity_source || ''}</td>

                                <td id="col-td-46">${item.is_rec_available || ''}</td>

                                <td id="col-td-47">${item.recording_date || ''}</td>

                                <td id="col-td-40">${item.lead_status || ''}</td>

                                <td id="col-td-41">${item.lead_assignment_date || ''}</td>

                                <td id="col-td-42">${item.lead_id || ''}</td>

                                <td id="col-td-43">${item.raw_data_id || ''}</td>

                            </tr>`;
                        tableBody.append(tableRow);
                    });
                    // visibility();
                    $("#leadManager tbody").on("click", "tr", function() {
                        $("#leadManager tbody tr").removeClass("selected-row");
                        $(this).addClass("selected-row");
                    });
                } else {
                    console.error('Unexpected response format:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.error("Response:", xhr.responseText);
            }
        });
    }

    function visibility() {
        var columnsToHide = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45
        ];
        columnsToHide.each(function(index) {
            $("#col-td-" + index).hide();
            $("#col-th-" + index).hide();
        });

    }
    $(".reassign").on("click", function() {
        var offcanvasElement = document.getElementById(
            "reassignleadsOffcanvasEnd"
        );
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
        offcanvasElement.addEventListener(
            "shown.bs.offcanvas",
            function() {
                $(".js-example-basic-single").select2({
                    dropdownParent: $(
                        "#reassignleadsOffcanvasEnd .offcanvas-body"
                    ), // Ensure the dropdown is appended to the correct off-canvas element

                });
            }, {
                once: true

            }
        );
        getCheckedValues();
        $.ajax({
            type: "POST",
            url: "dbFiles/fetch_emp_branchwise.php",
            dataType: "json",
            data: {
                active_user: active_user

            },
            success: function(response) {
                var lead_id = $("#lead_id");
                lead_id.val(checkedValues);
                var employees = response.emp;
                var employee_code = $("#employee_code");
                employee_code.empty();
                for (var i = 0; i < employees.length; i++) {
                    var emp = employees[i];
                    employee_code.append(
                        $("<option>", {
                            value: emp,
                            text: emp,
                        })
                    );
                }
                console.log(emp);
            },
            error: function(error) {
                console.error("Error fetching Employee Name:", error);
            },
        });

    });

    $(".recommendation").on("click", function() {
        var offcanvasElement2 = document.getElementById(
            "recommendationOffcanvasEnd"
        );
        var bsOffcanvas2 = new bootstrap.Offcanvas(offcanvasElement2);
        bsOffcanvas2.show();
        offcanvasElement2.addEventListener(
            "shown.bs.offcanvas",
            function() {
                $(".js-example-basic-single").select2({
                    dropdownParent: $(
                        "#recommendationOffcanvasEnd .offcanvas-body"
                    ), // Ensure the dropdown is appended to the correct off-canvas element

                });
            }, {
                once: true

            }
        );
        getCheckedValues();
        $("#leadId").val(checkedValues);
    });

    $(".add-app-id").on("click", function() {
        getCheckedValues();
        if (checkedValues.length !== 1) {
            alert("Please select exactly one item to proceed.");
            return;
        } else {
            var offcanvasElement = document.getElementById(
                "addAppIdOffcanvasEnd"
            );
            var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
            bsOffcanvas.show();
            offcanvasElement.addEventListener(
                "shown.bs.offcanvas",
                function() {
                    $(".js-example-basic-single").select2({
                        dropdownParent: $(
                            "#addAppIdOffcanvasEnd .offcanvas-body"
                        ), // Ensure the dropdown is appended to the correct off-canvas element

                    });
                }, {
                    once: true
                }
            );
            $("#regLeadId").val(checkedValues);
        }
    });

    $(".filters").on("click", function() {
        var offcanvasElement = document.getElementById(
            "filtersOffcanvasEnd"
        );
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
        offcanvasElement.addEventListener(
            "shown.bs.offcanvas",
            function() {
                $(".js-example-basic-single").select2({
                    dropdownParent: $(
                        "#filtersOffcanvasEnd .offcanvas-body"
                    ),
                });
            }, {
                once: true

            }
        );
        $('#url').val(window.location.href);

    });

    $(".upload-lead").on("click", function() {
        var offcanvasElement = document.getElementById(
            "uploadLeadOffcanvasEnd"
        );
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
        offcanvasElement.addEventListener(
            "shown.bs.offcanvas",
            function() {
                $(".js-example-basic-single").select2({
                    dropdownParent: $(
                        "#uploadLeadOffcanvasEnd .offcanvas-body"
                    ),
                });
            }, {
                once: true

            }
        );
        $('#url').val(window.location.href);

    });

    $(".download-csv").on("click", function() {
        downloadCSV2("#leadManagerBody", "Leads.csv");
        alert("Your CSV has downloaded.");
        var numberOfRecords = $("#leadManagerBody tr").length;
        $.ajax({
            type: "POST",
            url: "dbFiles/csv_controller.php",
            dataType: "json",
            data: {
                active_user: active_user,
                number_of_records: numberOfRecords

            },
            success: function(response) {
                console.log(response);
            },
            error: function(error) {
                console.error("Error fetching CSV download:", error);
            },
        });

    });

    $(".clear-filter").on("click", function() {
        clear_filter();

    });

    $(document).ready(function() {
        // Initialize search filter
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#leadManagerBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

    });
</script>
@endsection
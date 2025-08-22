@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="card overflow-hidden p-4" style="height: 80vh">
        <div class="row justify-content-between mb-4 mx-0">
            <h4 class="card-title">{{ $stageName }} Leads</h4>
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
            <div class="btn-group p-0" role="group" aria-label="Basic example">
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
                    <button
                        id="columnsVisibilityBtn"
                        class="btn btn-primary mdi mdi-view-carousel"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#columnsOffcanvas"
                        aria-controls="columnsOffcanvas"
                        title="Show/Hide Columns"
                        aria-label="Show/Hide Columns">
                    </button>

                    <!-- <button class="btn btn-primary mdi mdi-view-carousel columns-visibility" tabindex="0" id="columnsVisibilityBtn"
                        aria-controls="leadManagerTable" type="button" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="" data-bs-original-title="Show Columns"
                        aria-label="Show Columns"><span></span></button> -->
            </div>
            <div class="col-lg-4 col-md-4 p-0">
                <input type="text" id="searchInput" class="form-control m-0 h-100"
                    placeholder="Search...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0" id="leadsTable">
                <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                    <th id="col-th-1"><input type="checkbox" name="checkAll" id="checkAll"> </th>
                    <th id="col-th-2">Registered Name</th>
                    <th style="display:none" id="col-th-2">Registered Name</th>
                    <th id="col-th-3">Registered Email</th>
                    <th id="col-th-4">Registered Mobile</th>
                    <th id="col-th-5">State</th>
                    <th id="col-th-6">City</th>
                    <th id="col-th-44">Branch</th>
                    <th id="col-th-45">Zone</th>
                    <th id="col-th-7">User Registration Date</th>
                    <th id="col-th-18">Lead Owner</th>
                    <th id="col-th-19">Lead Origin</th>
                    <th id="col-th-20">Lead Source</th>
                    <th id="col-th-23">Lead Stage</th>
                    <th id="col-th-24">Lead Sub Stage</th>
                    <th id="col-th-26">Lead Remark</th>
                    <th id="col-th-8">Stage Change Count</th>
                    <th id="col-th-9">Registration Attempts</th>
                    <th id="col-th-10">Notes Count</th>
                    <th id="col-th-11">Followup Count</th>
                    <th id="col-th-12">Registered Country</th>
                    <th id="col-th-13">Utm Source</th>
                    <th id="col-th-14">Utm Medium</th>
                    <th id="col-th-15">Utm Campaign</th>
                    <th id="col-th-14">Utm Ad Group</th>
                    <th id="col-th-15">Utm Term</th>
                    <th id="col-th-16">Level Applying for</th>
                    <th id="col-th-17">Course</th>
                    <th id="col-th-21">Alternate Mobile</th>
                    <th id="col-th-22">Current Url</th>
                    <th id="col-th-25">Lead Follow-Up Date</th>
                    <th id="col-th-27">Re-assigned</th>
                    <th id="col-th-28">Re-assigned By</th>
                    <th id="col-th-29">Re-assigned On</th>
                    <th id="col-th-30">Lead Score</th>
                    <th id="col-th-31">Mobile Verification Status</th>
                    <th id="col-th-32">Email Verification Status</th>
                    <th id="col-th-33">Lead Verification Date</th>
                    <th id="col-th-34">Email Sent Count</th>
                    <th id="col-th-35">Whatsapp Message Status</th>
                    <th id="col-th-36">SMS Sent Count</th>
                    <th id="col-th-37">Widget Name</th>
                    <th id="col-th-38">Application Submitted</th>
                    <th id="col-th-39">Last Modified Date</th>
                    <th id="col-th-44">Last Enquirer Modified Date</th>
                    <th id="col-th-45">Last Enquirer Modified Source</th>
                    <th id="col-th-46">Is Recording Available</th>
                    <th id="col-th-47">Recording Date</th>
                    <th id="col-th-40">Lead Status</th>
                    <th id="col-th-41">Registration Attempt Date</th>
                    <th id="col-th-42">Lead Id</th>
                    <th id="col-th-43">Raw Data Id</th>
                </thead>
                <tbody>
                    @php
                    // Derive the current page slug from URL if you need the unallocated-leads condition
                    $page = request()->segment(2) ?? 'all';
                    $leadsCount = $leads->count();
                    @endphp

                    @foreach ($leads as $lead)
                    <tr>
                        <td id="col-td-1">
                            <input
                                type="checkbox"
                                name="check"
                                id="check-{{ $lead->id }}"
                                value="{{ ($lead->id ?? '') . '*' . ($lead->registered_name ?? '') }}">
                        </td>

                        <td id="col-td-2">
                            @if ($page === 'unallocated-leads')
                            <button
                                class="btn text-decoration-underline assginLead"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#assginLeadsOffcanvas"
                                aria-controls="assginLeadsOffcanvas"
                                data-id="{{ base64_encode($lead->log_id ?? '') }}">
                                {{ $lead->registered_name ?? '' }}
                                <span class="badge badge-primary ml-2">{{ $lead->registration_attempts ?? '' }}</span>
                            </button>
                            @else
                            <a href="{{ url('lead-details') }}?info={{ base64_encode($lead->id ?? '') }}">
                                {{ $lead->registered_name ?? '' }}
                                <span class="badge badge-primary ml-2">{{ $lead->registration_attempts ?? '' }}</span>
                            </a>
                            @endif
                        </td>

                        <td id="col-td-3" style="display:none">{{ $lead->registered_name ?? '' }}</td>
                        <td id="col-td-3">{{ $lead->registered_email ?? '' }}</td>
                        <td id="col-td-4">{{ $lead->registered_mobile ?? '' }}</td>
                        <td id="col-td-5">{{ $lead->state ?? '' }}</td>
                        <td id="col-td-6">{{ $lead->city ?? '' }}</td>

                        <td id="col-td-44">{{ $lead->branch ?? '' }}</td>
                        <td id="col-td-45">{{ $lead->zone ?? '' }}</td>

                        <td id="col-td-7">{{ $lead->user_registration_date ?? '' }}</td>
                        <td id="col-td-18">{{ $lead->lead_owner ?? '' }}</td>
                        <td id="col-td-19">{{ $lead->lead_origin ?? '' }}</td>
                        <td id="col-td-20">{{ $lead->lead_source ?? '' }}</td>
                        <td id="col-td-23">{{ $lead->lead_stage ?? '' }}</td>
                        <td id="col-td-24">{{ $lead->lead_sub_stage ?? '' }}</td>
                        <td id="col-td-26">{{ $lead->lead_remark ?? '' }}</td>

                        <td id="col-td-8">{{ $lead->stage_change_count ?? '' }}</td>
                        <td id="col-td-9">{{ $lead->registration_attempts ?? '' }}</td>
                        <td id="col-td-10">{{ $lead->notes_count ?? '' }}</td>
                        <td id="col-td-11">{{ $lead->followup_count ?? '' }}</td>

                        <td id="col-td-12">{{ $lead->registered_country ?? '' }}</td>
                        <td id="col-td-13">{{ $lead->utm_source ?? '' }}</td>
                        <td id="col-td-14">{{ $lead->utm_medium ?? '' }}</td>
                        <td id="col-td-15">{{ $lead->utm_campaign ?? '' }}</td>

                        <td id="col-td-44">{{ $lead->utm_adgroup ?? '' }}</td>
                        <td id="col-td-45">{{ $lead->utm_term ?? '' }}</td>

                        <td id="col-td-16">{{ $lead->level_applying_for ?? '' }}</td>
                        <td id="col-td-17">{{ $lead->course ?? '' }}</td>
                        <td id="col-td-21">{{ $lead->alternate_mobile ?? '' }}</td>
                        <td id="col-td-22">{{ $lead->current_url ?? '' }}</td>
                        <td id="col-td-25">{{ $lead->lead_followup_date ?? '' }}</td>

                        <td id="col-td-27">{{ $lead->reassigned ?? '' }}</td>
                        <td id="col-td-28">{{ $lead->assign_reassigned_by ?? '' }}</td>
                        <td id="col-td-29">{{ $lead->reassigned_on ?? '' }}</td>
                        <td id="col-td-30">{{ $lead->lead_score ?? '' }}</td>
                        <td id="col-td-31">{{ $lead->mobile_verification_status ?? '' }}</td>
                        <td id="col-td-32">{{ $lead->email_verification_status ?? '' }}</td>
                        <td id="col-td-33">{{ $lead->lead_verification_date ?? '' }}</td>
                        <td id="col-td-34">{{ $lead->email_sent_count ?? '' }}</td>
                        <td id="col-td-35">{{ $lead->whatsapp_message_count ?? '' }}</td>
                        <td id="col-td-36">{{ $lead->sms_sent_count ?? '' }}</td>
                        <td id="col-td-37">{{ $lead->widget_name ?? '' }}</td>
                        <td id="col-td-38">{{ $lead->application_submitted ?? '' }}</td>
                        <td id="col-td-39">{{ $lead->last_lead_activity_date ?? '' }}</td>

                        <td id="col-td-44">{{ $lead->last_enquirer_activity_date ?? '' }}</td>
                        <td id="col-td-45">{{ $lead->enquirer_activity_source ?? '' }}</td>

                        <td id="col-td-46">{{ $lead->is_rec_available ?? '' }}</td>
                        <td id="col-td-47">{{ $lead->recording_date ?? '' }}</td>
                        <td id="col-td-40">{{ $lead->lead_status ?? '' }}</td>
                        <td id="col-td-41">{{ $lead->lead_assignment_date ?? '' }}</td>
                        <td id="col-td-42">{{ $lead->lead_id ?? '' }}</td>
                        <td id="col-td-43">{{ $lead->raw_data_id ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-3 mx-0">
            <p>Showing <span id="entries">{{$leadsCount}}</span> Entries</p>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="columnsOffcanvas" aria-labelledby="columnsOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="columnsOffcanvasLabel">Show / Hide Columns</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" id="columnsSelectAll">Select All</button>
                <button class="btn btn-sm btn-outline-secondary" id="columnsClearAll">Clear All</button>
            </div>
            <small class="text-muted">Saved automatically</small>
        </div>

        <div id="columnsList" class="list-group list-group-flush">
            {{-- JS will inject one switch per column --}}
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
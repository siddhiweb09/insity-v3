<div class="offcanvas offcanvas-end" tabindex="-1" id="filtersOffcanvasEnd" aria-labelledby="filtersOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="filtersOffcanvasEndLabel" class="offcanvas-title">Filter Leads</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <form id="action-filters" method="post" data-action="{{ route('filteredValues') }}">
            @csrf
            <div class="d-flex flex-wrap justify-content-end mb-4">
                <button id="addFilter" type="button" class="btn btn-sm btn-primary mdi mdi-plus-circle"></button>
                <input type="text" id="date-range-filter"
                    class="btn btn-light bg-white dropdown-toggle text-right ml-auto d-flex" />
                <div class="col-12 col-md-4 col-xl-4 mb-4 mb-xl-0">
                    <select class="form-control date_source js-example-basic-single w-100" name="date_source" required>
                        <option value="lead_assignment_date" selected>Lead Assignment Date</option>
                        <option value="last_lead_activity_date">Last Lead Activity Date</option>
                        <option value="last_enquirer_activity_date">Last Enquirer Activity Date</option>
                        <option value="recording_date">Call Recording Date</option>
                    </select>
                </div>
            </div>
            <div id="filterBox">
                <div class="row filterSet">
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                        <select name="filterTitle0" class="form-control filterTitle js-example-basic-single w-100">
                            <option value="">Select Title</option>
                            <!-- <option value="registered_name">Registered Name</option>
                            <option value="registered_email">Registered Email</option>
                            <option value="registered_mobile">Registered Mobile</option>
                            <option value="stage_change_count">Stage Change Count</option>
                            <option value="registration_attempts">Registration Attempts</option>
                            <option value="notes_count">Notes Count</option>
                            <option value="followup_count">Followup Count</option>
                            <option value="registered_country">Registered Country</option>
                            <option value="state">State</option>
                            <option value="city">City</option>
                            <option value="branch">Branch</option>
                            <option value="zone">Zone</option>
                            <option value="utm_source">Utm Source</option>
                            <option value="utm_medium">Utm Medium</option>
                            <option value="utm_campaign">Utm Campaign</option>
                            <option value="level_applying_for">Level Applying for</option>
                            <option value="course">Course</option>
                            <option value="lead_owner">Lead Owner</option>
                            <option value="lead_origin">Lead Origin</option>
                            <option value="lead_source">Lead Source</option>
                            <option value="user_registration_date">User Registration Date</option>
                            <option value="alternate_mobile">Alternate Mobile</option>
                            <option value="last_lead_activity_date">Last Modified Date</option>
                            <option value="current_url">Current Url</option>
                            <option value="lead_stage">Lead Stage</option>
                            <option value="lead_sub_stage">Lead Sub Stage</option>
                            <option value="lead_followup_date">Lead Follow-Up Date</option>
                            <option value="lead_remark">Lead Remark</option>
                            <option value="outbound_success">Outbound Success</option>
                            <option value="outbound_missed">Outbound Missed</option>
                            <option value="outbound_total">Outbound Total</option>
                            <option value="inbound_success">Inbound Success</option>
                            <option value="inbound_missed">Inbound Missed</option>
                            <option value="inbound_total">Inbound Total</option>
                            <option value="reassigned">Re-assigned</option>
                            <option value="assign_reassigned_by">Re-assigned By</option>
                            <option value="reassigned_on">Re-assigned On</option>
                            <option value="lead_score">Lead Score</option>
                            <option value="mobile_verification_status">Mobile Verification Status</option>
                            <option value="email_verification_status">Email Verification Status</option>
                            <option value="email_sent_count">Email Sent Count</option>
                            <option value="whatsapp_message_count">Whatsapp Message Status</option>
                            <option value="sms_sent_count">SMS Sent Count</option>
                            <option value="lead_verification_date">Lead Verification Date</option>
                            <option value="widget_name">Widget Name</option>
                            <option value="application_submitted">Application Submitted</option>
                            <option value="lead_status">Lead Status</option>
                            <option value="lead_assignment_date">Registration Attempt Date</option>
                            <option value="lead_id">Lead Id</option>
                            <option value="log_id">Log Id</option>
                            <option value="raw_data_id">Raw Data Id</option>
                            <option value="additional_remark">Additional Remark</option>
                            <option value="runo_allocation">Runo Allocation</option>
                            <option value="is_rec_available">Recording Available</option>
                            <option value="aadhar_flag">Aadhar Verified</option>
                            <option value="eligibility_check_submitted">Eligibility Check</option> -->
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-6 col-sm-12 filterSearchDiv">
                        <select name="filterSearch0" class="form-control filterSearch js-example-basic-single w-100">
                            <option value="">Search</option>
                            <option value="=">EQUAL</option>
                            <option value="!=">NOT EQUAL</option>
                            <option value="LIKE">LIKE</option>
                            <option value="NOT LIKE">NOT LIKE</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-6 col-sm-12 filterValueDiv">
                        <input name="filterValue0[]" type="text" class="form-control" placeholder="Enter Search Value">
                    </div>
                    <input type="hidden" name="tableName" id="tableName" class="form-control">
                    <input type="hidden" name="date_range" id="dateRange" class="form-control">
                    <input type="hidden" name="category" id="category" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                data-bs-dismiss="offcanvas">Close</button>
        </form>
    </div>
</div>
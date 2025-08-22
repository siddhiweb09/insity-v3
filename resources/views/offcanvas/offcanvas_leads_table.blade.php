<div class="offcanvas offcanvas-end" tabindex="-1" id="leadsTableOffcanvasEnd" aria-labelledby="leadsTableOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="leadsTableOffcanvasEndLabel" class="offcanvas-title">Create New Lead</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <form class="forms-sample row p-4" action="dbFiles/check_registered_lead.php" method="post">
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Name</label>
                <div class="col-sm-9">
                    <input type="text" name="name" class="form-control">
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Mobile Number</label>
                <div class="col-sm-9">
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Email</label>
                <div class="col-sm-9">
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Entity</label>
                <div class="col-sm-9">
                    <select name="widget_name" id="widget_name" class="form-control js-example-basic-single w-100">
                        <option value="">Select Entity</option>
                        <option value="ISBM">ISBM</option>
                        <option value="ISBMU">ISBM University</option>
                        <option value="ISTM">ISTM</option>
                    </select>
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">State</label>
                <div class="col-sm-9">
                    <select name="state" class="form-control js-example-basic-single w-100 state" id="state"> </select>
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">City</label>
                <div class="col-sm-9">
                    <select name="city" class="form-control js-example-basic-single w-100 city" id="city"> </select>
                </div>
            </div>

            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Level For Applying</label>
                <div class="col-sm-9">
                    <select name="level" class="form-control js-example-basic-single w-100 level" id="level"> </select>
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Course</label>
                <div class="col-sm-9">
                    <select name="course" class="form-control js-example-basic-single w-100 course" id="course">
                    </select>
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Lead Source</label>
                <div class="col-sm-9">
                    <input type="text" name="Reference_display" value="Reference" class="form-control" readonly>
                    <input type="hidden" name="lead_source" value="Reference">
                </div>
            </div>
            <div class="form-group row col-12">
                <label class="col-sm-2 col-form-label py-0">Verification Status</label>
                <div class="col-sm-9">
                    <select name="lead_status" id="lead_status" class="form-control js-example-basic-single w-100">
                        <option value="VERIFIED">VERIFIED</option>
                        <option value="UNVERIFIED">UNVERIFIED</option>
                    </select>
                </div>
            </div>
            <div class="form-group row col-lg-12">
                <label class="col-sm-12 col-form-label py-0">Additional Remark</label>
                <div class="col-sm-12">
                    <textarea name="additional_remark" rows="7" class="form-control" id="additional_remark"> </textarea>
                </div>
            </div>
            <input type="text" name="current_url" value="https://insityapp.com/lead-manager" class="form-control" hidden>

            <div class="form-group row col-lg-12">
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <button type="button" class="btn btn-sm btn-inverse-danger btn-fw" data-bs-dismiss="offcanvas">Close</button>
            </div>
        </form>
    </div>
</div>
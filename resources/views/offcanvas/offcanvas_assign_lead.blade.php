<div class="offcanvas offcanvas-end" tabindex="-1" id="assginLeadsOffcanvas"
    aria-labelledby="assginLeadsOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 id="assginLeadsOffcanvasLabel" class="offcanvas-title">Assign Lead</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div id="assginLeadsOffcanvasContainer" class="offcanvas-body m-0 flex-grow-0">
        <form class="assign-lead-forms row">
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Name</label>
                <input type="text" id="registered_name" name="registered_name" class="form-control">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Email Address</label>
                <input type="email" id="registered_email" name="registered_email" class="form-control">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Mobile Number</label>
                <input type="text" id="registered_mobile" name="registered_mobile" class="form-control" disabled>
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Alternate Mobile Number</label>
                <input type="text" id="alternate_mobile" name="alternate_mobile" class="form-control">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Registered State</label>
                <div class="badge badge-success" id="registered_state"></div>
                <div class="error" id="stateerror" style="display:none"></div>
                <div id="stateDiv"></div>
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Registered City</label>
                <div class="badge badge-success" id="registered_city"></div>
                <div class="error" id="cityerror" style="display:none"></div>
                <div id="cityDiv"></div>
            </div>

            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Level For Applying</label>
                <div class="badge badge-success" id="registered_level"></div>
                <div class="error" id="levelerror" style="display:none"></div>
                <div id="levelDiv"></div>
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label py-0">Course</label>
                <div class="badge badge-success" id="registered_course"></div>
                <div class="error" id="courseerror" style="display:none"></div>
                <div id="courseDiv"></div>
            </div>

            <div class="form-group m-0 row">
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                    data-bs-dismiss="offcanvas">Close</button>
            </div>
        </form>
    </div>
</div>
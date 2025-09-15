<div class="offcanvas offcanvas-end" tabindex="-1" id="addAppIdOffcanvasEnd" aria-labelledby="addAppIdOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="addAppIdOffcanvasEndLabel" class="offcanvas-title">Add Application Id</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <form id="add-application-id" method="post" data-action="{{ route('leads.addApplicationId') }}">
            @csrf
            <div class="form-group">
                <label>Lead ID's</label>
                <input name="lead_id" type="text" class="form-control" placeholder="Lead ID's" id="regLeadId" readonly>
            </div>
            <div class="form-group ">
                <label>Application ID</label>
                <input name="application_id" type="text" class="form-control" placeholder="Application ID's">
            </div>

            <button type="submit" name="submit" class="btn btn-sm btn-primary">Submit</button>
            <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                data-bs-dismiss="offcanvas">Close</button>
        </form>
    </div>
</div>
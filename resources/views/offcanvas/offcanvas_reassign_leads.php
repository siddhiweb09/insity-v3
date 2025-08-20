<div class="offcanvas offcanvas-end" tabindex="-1" id="reassignleadsOffcanvasEnd" aria-labelledby="reassignleadsOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="reassignleadsOffcanvasEndLabel" class="offcanvas-title">Re-assign Leads</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <form action="reassign" method="post">
            <div class="form-group">
                <label>Lead ID's</label>
                <input name="lead_id" type="text" class="form-control" placeholder="Lead ID's" id="lead_id">
            </div>
            <div class="form-group">
                <label>Employee Codes</label>
                <select name="employee_code" class="form-control employee_code js-example-basic-single w-100" id="employee_code"> </select>
            </div>

            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                data-bs-dismiss="offcanvas">Close</button>
        </form>
    </div>
</div>
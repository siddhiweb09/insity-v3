<div class="offcanvas offcanvas-end" tabindex="-1" id="uploadLeadOffcanvasEnd"
    aria-labelledby="uploadLeadOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="uploadLeadOffcanvasEndLabel" class="offcanvas-title">Filter Leads</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>

    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <div class="col-12">
            <a class="text-primary" style="text-decoration: underline;" href="lead_uploader.csv" download=""><b>Download
                    Demo
                    File</b></a>
        </div>
        <form class="forms-sample row" action="assign-uploaded-lead" method="post" enctype="multipart/form-data">
            <div class="form-group col-12">
                <input type="file" id="csvFile" name="csvFile" class="form-control">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                data-bs-dismiss="offcanvas">Close</button>
        </form>
    </div>
</div>
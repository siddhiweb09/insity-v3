<div class="offcanvas offcanvas-end" tabindex="-1" id="recommendationOffcanvasEnd" aria-labelledby="recommendationOffcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="recommendationOffcanvasEndLabel" class="offcanvas-title">Recommendation</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
        <form action="recommendation" method="post">
            <div class="form-group">
                <label>Lead ID's</label>
                <input name="lead_id" type="text" class="form-control" placeholder="Lead ID's" id="leadId" readonly>
            </div>
            <div class="form-group ">
                <label>Recommendation</label>
                <textarea id="recommendation" class="form-control" name="recommendation" rows="10"></textarea>
            </div>
            <!-- <div class="form-group">
                <label>Recommendation</label>
                <input type="text" name="recommendation" id="recommendation"  class="form-control">
            </div> -->
            <input type="text" name="url" id="url" class="form-control" hidden>

            <button type="submit" name="submit" class="btn btn-sm btn-primary">Submit</button>
            <button type="button" class="btn btn-sm btn-inverse-danger btn-fw"
                data-bs-dismiss="offcanvas">Close</button>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#url').val(window.location.href);
    });
</script>
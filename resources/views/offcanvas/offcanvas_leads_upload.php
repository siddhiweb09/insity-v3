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
<script>
    $(document).ready(function () {
        $('#page').val(window.location.href);
    });

    var dateRange = "";

    var active_user = "<?php echo session('employee_code') ?>";

    var page = window.location.href;

    var pageSegments = page.split('/');

    page = pageSegments[3];

    // $(document).ready(function () {

    // var startDate = moment().startOf('week');

    // var endDate = moment().endOf('week');
    var startDate = moment().subtract(7, 'days').startOf('day');
    var endDate = moment().endOf('day');
    $('#date-range-filter').daterangepicker({

        opens: 'left',

        locale: {

            format: 'YYYY-MM-DD'

        },

        startDate: startDate,

        endDate: endDate

    }, function (start, end) {

        dateRange = start.format('YYYY-MM-DD') + '*' + end.format('YYYY-MM-DD');
        $('#dateRange').val(dateRange);
    });

    var today = new Date();
    var formatDate = function (date) {

        var year = date.getFullYear();

        var month = ('0' + (date.getMonth() + 1)).slice(-2);

        var day = ('0' + date.getDate()).slice(-2);

        return year + '-' + month + '-' + day;

    };
    // var currentdateRange = formatDate(today) + "*" + formatDate(today);

    var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
    $('#dateRange').val(currentdateRange);
</script>
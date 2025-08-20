@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-8 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold" id="generatePdfBtn">Marketing Dashboard</h3>
                </div>
                <div class="col-4 col-xl-4 mb-4 mb-xl-0 dashboardFilter">
                    <input type="text" id="date-filter"
                        class="btn btn-sm btn-light bg-white dropdown-toggle text-right ml-auto d-flex w-100" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row justify-content-between mx-0 mb-xl-3">
                    <h3 class="font-weight-500 text-primary m-0">Lead Status Report</h3>
                    <div class="row m-0">
                        <div class="col-auto p-0 mr-3">
                            <input type="text" id="searchInput" class="form-control m-0"
                                placeholder="Search...">
                        </div>
                        <button id="downloadCsvBtn3" type="button"
                            class="btn btn-inverse-primary btn-icon" data-toggle="tooltip"
                            title="Download Report">
                            <i class="mdi mdi-download"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0" id="scrollbarTable1">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white"
                                style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Branch</th>
                                    <th>Counsellor Name</th>
                                    <th>Total Leads Assigned </th>
                                    <th>Leads from Shine</th>
                                    <th>Leads from Naukri</th>
                                    <th>Leads Interested</th>
                                    <th>Leads Not Interested</th>
                                    <th>Pending to Work</th>
                                    <th>Worked</th>
                                    <th>Enrollments</th>
                                    <th>Conversion Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody id="leadActivityMarketingData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 grid-margin transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row justify-content-between mb-4 mx-0">
                    <h3 class="font-weight-500 text-primary">Lead Status Group Report</h3>
                    <div class="col-lg-4 col-md-4 p-0">
                        <input type="text" id="searchInput2" class="form-control ml-auto "
                            placeholder="Search...">
                    </div>
                </div>
                <div class="card-body p-0" id="scrollbarTable2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white"
                                style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Branch</th>
                                    <th>Counsellor Name</th>
                                    <th>MQL</th>
                                    <th>NQL</th>
                                    <th>SQL</th>
                                    <th>Untouched</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statusGroupMarketingData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    function generateDocPDF() {
        const {
            jsPDF
        } = window.jspdf;

        html2canvas(document.getElementById('leadActivityMarketingData')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF();
            const imgWidth = 190;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);

            // Convert PDF to Blob
            const pdfOutput = pdf.output('blob');
            sendToTelegram(pdfOutput);
        });
    }

    function sendToTelegram(pdfBlob) {
        const message = "Marketing Data Report";

        const formData = new FormData();
        formData.append('chat_id', '-1002216604081');
        formData.append('text', message);
        formData.append('document', pdfBlob, 'Marketing Data Report.pdf');

        const telegramUrl = "https://api.telegram.org/bot7045943726:AAHnqVXz3h-_QwnIrMzKVZX_VhM5uI2r7o4/sendDocument"; // Use sendDocument to send files

        fetch(telegramUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Success:", data);
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }

    $('#downloadCsvBtn3').on('click', function() {
        downloadCSV("#leadActivityMarketingData", "Marketing Data");
    });

    $(document).ready(function() {
        // setInterval(generateDocPDF, 5000);

        var scrollbar1 = document.getElementById("scrollbarTable1");
        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false
            });
        }

        var scrollbar2 = document.getElementById("scrollbarTable2");
        if (scrollbar2) {
            new PerfectScrollbar(scrollbar2, {
                wheelPropagation: false
            });
        }

        let columnToFreeze = null;

        $(".table thead").on("click", "th", function() {
            const table = $(this).closest("table");
            const columnIndex = $(this).index();

            columnToFreeze = columnIndex;

            rearrangeAndFreezeColumn(table, columnIndex);
        });

        function rearrangeAndFreezeColumn(table, columnIndex) {
            const thead = $(table).find("thead");
            const tbody = $(table).find("tbody");
            moveColumn(thead, columnIndex);
            moveColumn(tbody, columnIndex);
            freezeColumn(thead, 0);
            freezeColumn(tbody, 0);
        }

        function moveColumn(section, columnIndex) {
            section.find("tr").each(function() {
                const cell = $(this).children().eq(columnIndex);
                $(this).prepend(cell);
            });
        }

        function freezeColumn(section, columnIndex) {
            section.find('th, td').removeClass('sticky sticky-cell');
            section.find(`tr`).each(function() {
                $(this).children().eq(columnIndex).addClass('sticky sticky-cell');
            });
        }


        var startDate = moment().startOf('day');
        var endDate = moment().startOf('day');

        $('#date-filter').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: startDate,
            endDate: endDate
        }, function(start, end) {
            dateRange = start.format('YYYY-MM-DD') + '*' + end.format('YYYY-MM-DD');
            fetch_marketing_data_stat(dateRange)
        });

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
        fetch_marketing_data_stat(currentdateRange)

        var options = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Lead Origin Distribution",
                },
            },
        };

        function fetch_marketing_data_stat(dateRange) {
            $.ajax({
                url: "{{ route('marketingStats') }}", // This should point to your PHP file
                type: "GET",
                data: {
                    dateRange: dateRange
                },
                dataType: "json",
                success: function(response) {

                    var tableBody1 = $("#leadActivityMarketingData");
                    tableBody1.empty(); // Clear existing data

                    $.each(response, function(index, row) {
                        var conversion_rate1 = row.total_leads > 0 ?
                            ((row.leads_enrollments / row.total_leads) * 100).toFixed(2) :
                            0;
                        var tableRow1 = `<tr>
                                <td>${row.branch}</td>
                                <td>${row.lead_owner}</td>
                                <td>${row.total_leads}</td>
                                <td>${row.shine_leads_count}</td>
                                <td>${row.naukri_leads_count}</td>                            
                                <td>${row.leads_interested}</td>
                                <td>${row.leads_not_interested}</td>
                                 <td>${row.pending_to_work}</td>
                                <td>${row.worked}</td>
                                <td>${row.leads_enrollments}</td>
                                <td>${conversion_rate1}</td>
                            </tr>`;
                        tableBody1.append(tableRow1);
                    });
                    $('#leadActivityMarketingData').on('click', 'tr', function() {
                        $('#leadActivityMarketingData tr').removeClass(
                            'selected-row'); // Remove the class from all rows
                        $(this).addClass(
                            'selected-row'); // Add the class to the clicked row
                    });
                    // console.log(response);
                    var tableBody2 = $("#statusGroupMarketingData");
                    tableBody2.empty(); // Clear existing data

                    $.each(response, function(index, row) {
                        var tableRow2 = `<tr>
                                <td>${row.branch}</td>
                                <td>${row.lead_owner}</td>
                                <td>${row.mql_count}</td>
                                <td>${row.nql_count}</td>
                                <td>${row.sql_count}</td>
                                <td>${row.untouched_count}</td>
                                <td>${row.total_leads}</td>
                            </tr>`;
                        tableBody2.append(tableRow2);
                    });
                    $('#statusGroupMarketingData').on('click', 'tr', function() {
                        $('#statusGroupMarketingData tr').removeClass(
                            'selected-row'); // Remove the class from all rows
                        $(this).addClass(
                            'selected-row'); // Add the class to the clicked row
                    });
                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                },
            });
        }
    });


    $(document).ready(function() {
        // Initialize search filter
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#leadActivityMarketingData tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        $('#searchInput2').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#statusGroupMarketingData tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
@endsection
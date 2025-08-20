@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-8 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold" id="generatePdfBtn">Counsellor Dashboard</h3>
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
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-500 text-primary">Counsellor Log Activity</h3>
                    <button id="downloadCsvBtn3" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white"
                                style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Employee</th>
                                    <th>Log Activity Count</th>
                                </tr>
                            </thead>
                            <tbody id="logActivityCount">
                                <!-- Data will be inserted here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 grid-margin transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-500 text-primary">Counsellor Log Details</h3>
                    <button id="downloadCsvBtn3" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable3">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white"
                                style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Employee</th>
                                    <th>Log Id</th>
                                    <th>Lead Stage</th>
                                    <th>Lead Sub Stage</th>
                                    <th>Task</th>
                                    <th>Followup Date</th>
                                    <th>Remark</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="logActivityCounsellor">
                                <!-- Data will be inserted here via JavaScript -->
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
    function sendToTelegram(pdfBlob) {
        const message = "Counsellor Activity Report";

        const formData = new FormData();
        formData.append('chat_id', '-1002216604081');
        formData.append('text', message);
        formData.append('document', pdfBlob, 'Counsellor Activity Report.pdf');

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

    $(document).ready(function() {
        // setInterval(generateDocPDF, 5000);

        var scrollbar1 = document.getElementById("scrollbarTable1");
        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false
            });
        }

        // Initialize Perfect Scrollbar for the second table
        var scrollbar2 = document.getElementById("scrollbarTable2");
        if (scrollbar2) {
            new PerfectScrollbar(scrollbar2, {
                wheelPropagation: false
            });
        }

        // Initialize Perfect Scrollbar for the second table
        var scrollbar3 = document.getElementById("scrollbarTable3");
        if (scrollbar3) {
            new PerfectScrollbar(scrollbar3, {
                wheelPropagation: false
            });
        }

        $('#downloadCsvBtn2').on('click', function() {
            downloadCSV("#statTableBody2", "Lead Source Wise Performance Report.csv");
        });

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

            // Move the selected column to the first position
            moveColumn(thead, columnIndex);
            moveColumn(tbody, columnIndex);

            // Apply sticky class to the new first column
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
            fetch_counsellor_stat(dateRange);
        });

        var scrollbar1 = document.getElementById("leadManager");
        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false
            });
        }

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
        fetch_counsellor_stat(currentdateRange);

        // Configuration options
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

        function fetch_counsellor_stat(dateRange) {
            $.ajax({
                url: "{{ route('counsellorStats') }}", // This should point to your PHP file
                type: "GET",
                data: {
                    dateRange: dateRange
                },
                dataType: "json",
                success: function(response) {
                    if (response.employee_log_count && Object.keys(response.employee_log_count).length > 0) {
                        var tableBody = $("#logActivityCount");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.employee_log_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var tableRow = `<tr>
                                <td>${row[0]}</td>
                                <td>${row[1]}</td>
                            </tr>`;
                            tableBody.append(tableRow);
                        });

                        $('#logActivityCount').on('click', 'tr', function() {
                            $('#logActivityCount tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }
                    if (response.data && Object.keys(response.data).length > 0) {
                        var tableBody = $("#logActivityCounsellor");
                        tableBody.empty(); // Clear existing data

                        $.each(response.data, function(index, row) {
                            var tableRow = `<tr>
                                <td>${row['employee_id']+ "*"+row['updated_by'] || '-'}</td>
                                <td>${row['log_id'] || '-'}</td>
                                <td>${row['lead_stage'] || '-'}</td>
                                <td>${row['lead_sub_stage'] || '-'}</td>
                                <td>${row['task'] || '-'}</td>
                                <td>${row['followup_date'] || '-'}</td>
                                <td>${row['remark_by_caller'] || '-'}</td>
                                <td>${row['created_at'] || '-'}</td>
                            </tr>`;
                            tableBody.append(tableRow);
                        });

                        $('#logActivityCounsellor').on('click', 'tr', function() {
                            $('#logActivityCounsellor tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }
                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                },
            });
        }

        function fetch_log_counsellor_wise(dateRange) {
            $.ajax({
                url: 'dashScripts/fetch_log_counsellor_wise.php',
                type: 'GET',
                data: {
                    dateRange: dateRange
                },
                success: function(response) {
                    var tableBody = $('#logActivityCounsellor');
                    tableBody.empty(); // Clear existing data

                    $.each(response, function(index, row) {
                        var tableRow = `<tr>
                                <td>${row.employee_id}*${row.updated_by}</td> 
                                <td>${row.log_id}</td>
                                <td>${row.lead_stage}</td>
                                <td>${row.lead_sub_stage}</td>
                                <td>${row.task}</td>
                                <td>${row.followup_date}</td>
                                <td>${row.remark_by_caller}</td>
                                <td>${row.created_at}</td>
                            </tr>`;
                        tableBody.append(tableRow);
                    });
                    $('#logActivityCounsellor').on('click', 'tr', function() {
                        $('#logActivityCounsellor tr').removeClass(
                            'selected-row'); // Remove the class from all rows
                        $(this).addClass(
                            'selected-row'); // Add the class to the clicked row
                    });
                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        function fetch_log_activity_count_counsellor_wise(dateRange) {
            $.ajax({
                url: 'dashScripts/fetch_log_activity_count_counsellor_wise.php',
                type: 'GET',
                data: {
                    dateRange: dateRange
                },
                success: function(response) {
                    var tableBody = $('#logActivityCount');
                    tableBody.empty(); // Clear existing data

                    $.each(response, function(index, row) {
                        var tableRow = `<tr>
                                <td>${row.employee_id}*${row.updated_by}</td> 
                                <td>${row.log_count}</td>
                            </tr>`;
                        tableBody.append(tableRow);
                    });
                    $('#logActivityCounsellor').on('click', 'tr', function() {
                        $('#logActivityCount tr').removeClass(
                            'selected-row'); // Remove the class from all rows
                        $(this).addClass(
                            'selected-row'); // Add the class to the clicked row
                    });
                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
        }
    });
</script>
@endsection
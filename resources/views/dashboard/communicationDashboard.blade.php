@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-8 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Communication Dashboard</h3>
                    <!-- <h6 class="font-weight-normal mb-0 col-12">All systems are running smoothly!</h6> -->
                </div>
                <div class="col-4 col-xl-4 mb-4 mb-xl-0 dashboardFilter">
                    <input type="text" id="date-filter"
                        class="btn btn-sm btn-light bg-white dropdown-toggle text-right ml-auto d-flex w-100" />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-500 text-primary">Calling Performance</h3>
                    <canvas id="lead_total" width="299" height="200"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-500 text-primary">Counsellor Wise Calling Performance
                    </h3>
                    <button id="downloadCsvBtn1" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable1">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white"
                                style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Counsellor</th>
                                    <th>Inbound Success</th>
                                    <th>Inbound Missed</th>
                                    <th>Inbound Total</th>
                                    <th>Outbound Success</th>
                                    <th>Outbound Missed</th>
                                    <th>Outbound Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody1">
                                <!-- Data will be inserted here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-500 text-primary">Calling Performance
                    </h3>
                    <button id="downloadCsvBtn2" type="button" class="btn btn-inverse-primary btn-icon"
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
                                    <th>Counsellor</th>
                                    <th>Total Calls</th>
                                    <th>Total Call Duration</th>
                                    <th>Registered Lead Calls</th>
                                    <th>Marketing Qualified Lead Calls</th>
                                    <th>Marketing Non Qualified Lead Calls</th>
                                    <th>Personal Calls</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody2">
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
    $(document).ready(function() { // Initialize Perfect Scrollbar for the first table
        var scrollbar1 = document.getElementById("scrollbarTable1");
        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false
            });
        }

        $('#downloadCsvBtn1').on('click', function() {
            downloadCSV("#statTableBody1", "Counsellor vs Lead.csv");
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

        var startDate = moment().subtract(7, 'days').startOf('day');
        var endDate = moment().endOf('day');

        $('#date-filter').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: startDate,
            endDate: endDate
        }, function(start, end) {
            dateRange = start.format('YYYY-MM-DD') + '*' + end.format('YYYY-MM-DD');

            fetch_calls_stat(dateRange);
        });

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());

        fetch_calls_stat(currentdateRange);

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

        //   lead_total_chart_data
        var ctxTotal = document.getElementById("lead_total").getContext("2d");
        var myPieChartTotal;

        function fetch_calls_stat(dateRange) {
            $.ajax({
                url: "{{ route('communicationStats') }}",
                type: "GET",
                data: {
                    dateRange: dateRange
                },
                dataType: "json",
                success: function(response) {
                    // Lead Performance Date Wise Bar Chart
                    if (response.call_date_count && Object.keys(response.call_date_count).length > 0) {
                        if (window.myPieChartTotal) {
                            window.myPieChartTotal.destroy();
                        }

                        window.myPieChartTotal = new Chart(ctxTotal, {
                            type: "bar",
                            data: {
                                labels: Object.keys(response.call_date_count),
                                datasets: [{
                                    data: Object.values(response.call_date_count),
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255,99,132,1)',
                                    borderWidth: 1,
                                    fill: false
                                }],
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                },
                                legend: {
                                    display: false
                                },
                                elements: {
                                    point: {
                                        radius: 0
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        formatter: function(value, context) {
                                            return value;
                                        },
                                        color: 'black' // Color of the labels
                                    }
                                }
                            },
                            plugins: [ChartDataLabels] // Register the plugin
                        });
                    }

                    // Counsellor Vs Branch Performance
                    if (response.call_type_count_by_employee && Object.keys(response.call_type_count_by_employee).length > 0) {
                        var tableBody = $("#statTableBody1");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.call_type_count_by_employee)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var counsellor = row[0];
                            var count = row[1];
                            console.log(count);
                            var tableRow = `<tr>
                                                    <td>${counsellor}</td>
                                                    <td>${count['success_incoming']}</td>
                                                    <td>${count['missed_incoming']}</td>
                                                    <td>${count['total_incoming']}</td>
                                                    <td>${count['success_outgoing']}</td>
                                                    <td>${count['missed_outgoing']}</td>
                                                    <td>${count['total_outgoing']}</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });
                        $('#statTableBody1').on('click', 'tr', function() {
                            $('#statTableBody1 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Calling Performance
                    if (response.call_details_by_employee && Object.keys(response.call_details_by_employee).length > 0) {
                        var tableBody = $("#statTableBody2");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.call_details_by_employee)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var counsellor = row[0];
                            var count = row[1];
                            console.log(count);
                            var tableRow = `<tr>
                                                    <td>${counsellor}</td>
                                                    <td>${count['total_calls']}</td>
                                                    <td>${count['total_duration']} Mins</td>
                                                    <td>${count['registered_lead_calls']}/${count['registered_lead_calls_percentage']}%</td>
                                                    <td>${count['mql_calls']}/${count['mql_calls_percentage']}%</td>
                                                    <td>${count['mnql_calls']}/${count['mnql_calls_percentage']}%</td>
                                                    <td>${count['personal_calls']}/${count['personal_calls_percentage']}%</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });
                        $('#statTableBody2').on('click', 'tr', function() {
                            $('#statTableBody2 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }
                },
                error: function(error) {
                    console.error("Error fetching chart data:", error);
                },
            });
        }
    });
</script>
@endsection
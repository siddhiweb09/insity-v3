@extends('frames.frame')

@section('content')
@php
use Carbon\Carbon;
date_default_timezone_set('Asia/Kolkata');

$currentMonth = Carbon::now('Asia/Kolkata');
$currentMonthFormatted = $currentMonth->format('Y-m');
$currentMonthName = $currentMonth->format('F');

$previousMonth = $currentMonth->copy()->subMonth();
$previousMonthFormatted = $previousMonth->format('Y-m');
$previousMonthName = $previousMonth->format('F');
@endphp

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-8 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">
                        Report for: {{ $previousMonthName }} - {{ $currentMonthName }}
                    </h3>
                </div>
                <div class="col-4 col-xl-4 mb-4 mb-xl-0 dashboardFilter">
                    <input type="text" id="date-filter"
                        class="btn btn-sm btn-light bg-white dropdown-toggle text-right ml-auto d-flex w-100" />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Lead Performance (Bar) --}}
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Performance</h3>
                    <canvas id="lead_total" width="299" height="200" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

        {{-- Lead Source Wise Performance (pivot by month) --}}
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Source Wise Performance</h3>
                    <button id="downloadCsvBtn1" type="button" class="btn btn-inverse-primary btn-icon" data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable1">
                    <div class="table-responsive">
                        <table class="table myTable">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                                <tr></tr>
                            </thead>
                            <tbody id="statTableBody1" class="myTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Performers (per month) --}}
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Top Performers</h3>
                    <button id="downloadCsvBtn2" type="button" class="btn btn-inverse-primary btn-icon" data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Channels</th>
                                    <th>Verified Leads</th>
                                    <th>Total Leads</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody2">
                                {{-- Filled by JS --}}
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
    $(document).ready(function() {
        // -------- Perfect Scrollbar ----------
        const ps1 = document.getElementById("scrollbarTable1");
        if (ps1) new PerfectScrollbar(ps1, {
            wheelPropagation: false
        });

        const ps2 = document.getElementById("scrollbarTable2");
        if (ps2) new PerfectScrollbar(ps2, {
            wheelPropagation: false
        });

        // -------- CSV Downloads ----------
        $('#downloadCsvBtn1').on('click', function() {
            downloadCSV("#statTableBody1", "Lead Source Wise Performance.csv");
        });
        $('#downloadCsvBtn2').on('click', function() {
            downloadCSV("#statTableBody2", "Top Performers.csv");
        });

        // -------- Freeze/Move Column on TH click ----------
        $(".table thead").on("click", "th", function() {
            const table = $(this).closest("table");
            const columnIndex = $(this).index();
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

        // -------- Date Range Picker ----------
        var startDate = moment().startOf('year');
        var endDate = moment().endOf('month');

        $('#date-filter').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM'
            },
            startDate: startDate,
            endDate: endDate
        }, function(start, end) {
            const dateRange = start.format('YYYY-MM') + '*' + end.format('YYYY-MM');
            fetch_admin_stat(dateRange);
        });

        function formatMonth(date) {
            var y = date.getFullYear();
            var m = ('0' + (date.getMonth() + 1)).slice(-2);
            return y + '-' + m;
        }

        var currentdateRange = formatMonth(startDate.toDate()) + "*" + formatMonth(endDate.toDate());
        fetch_admin_stat(currentdateRange);

        // -------- Chart: Lead Performance --------
        var ctxTotal = document.getElementById("lead_total").getContext("2d");
        window.myPieChartTotal = null;

        function fetch_admin_stat(dateRange) {
            $.ajax({
                url: "{{ route('adminStats') }}",
                type: "GET",
                data: {
                    dateRange
                },
                dataType: "json",
                success: function(response) {
                    if (!response || response.length === 0) {
                        console.error("No data received");
                        return;
                    }

                    // ---- Lead Performance (Bar) ----
                    if (window.myPieChartTotal) window.myPieChartTotal.destroy();
                    window.myPieChartTotal = new Chart(ctxTotal, {
                        type: "bar",
                        data: {
                            labels: response.map(item => item.month_name),
                            datasets: [{
                                data: response.map(item => item.total_leads),
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255,99,132,1)',
                                borderWidth: 1
                            }],
                        },
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            }
                        }
                    });

                    // ---- Lead Source Wise (Pivot Table) ----
                    const tableBody1 = $("#statTableBody1");
                    const tableHead1 = $("#scrollbarTable1 thead tr");
                    tableBody1.empty();
                    tableHead1.empty();

                    const months = response.map(item => item.month_name);
                    const headRow = `<th>Lead Source</th>` + months.map(m => `<th>${m}</th>`).join('');
                    tableHead1.append(headRow);

                    // Build dictionary: { source: { month: count } }
                    const sourceData = {};
                    response.forEach(item => {
                        (item.source_leads || []).forEach(src => {
                            if (!sourceData[src.lead_source]) sourceData[src.lead_source] = {};
                            sourceData[src.lead_source][item.month_name] = src.total_leads;
                        });
                    });

                    Object.entries(sourceData).forEach(([leadSource, monthMap]) => {
                        const row = `<tr><td>${leadSource}</td>` +
                            months.map(m => `<td>${monthMap[m] || 0}</td>`).join('') +
                            `</tr>`;
                        tableBody1.append(row);
                    });

                    // ---- Top Performers (Monthly) ----
                    const tableBody2 = $("#statTableBody2");
                    const tableHead2 = $("#scrollbarTable2 thead tr");
                    tableBody2.empty();
                    tableHead2.empty();

                    const categories = ['top_source', 'top_branch', 'top_zone', 'top_state', 'top_admission_source'];
                    const headerRow = `<th>Top Performers</th>` + months.map(m => `<th colspan="2">${m}</th>`).join('');
                    tableHead2.append(headerRow);

                    const rows = {
                        top_source: '<tr><td><b>Top Source</b></td>',
                        top_branch: '<tr><td><b>Top Branch</b></td>',
                        top_zone: '<tr><td><b>Top Zone</b></td>',
                        top_state: '<tr><td><b>Top State</b></td>',
                        top_admission_source: '<tr><td><b>Top Admission Source</b></td>',
                    };

                    months.forEach(m => {
                        const mdata = (response.find(it => it.month_name === m) || {}).top_performers || {};
                        categories.forEach(cat => {
                            const label = mdata[cat] ?? 'N/A';
                            const cnt = mdata[cat + '_lead_count'] ?? 0;
                            rows[cat] += `<td colspan="2">${label} (${cnt} Leads)</td>`;
                        });
                    });

                    categories.forEach(cat => {
                        rows[cat] += '</tr>';
                        tableBody2.append(rows[cat]);
                    });

                    // Row highlight behavior
                    $('#statTableBody1, #statTableBody2').on('click', 'tr', function() {
                        $(this).closest('tbody').find('tr').removeClass('selected-row');
                        $(this).addClass('selected-row');
                    });
                },
                error: function(err) {
                    console.error("Error fetching data:", err);
                }
            });
        }
    });
</script>
@endsection
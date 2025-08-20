@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 col-md-12 col-xl-12">
            <h3 class="font-weight-bold">Welcome <?php echo session('employee_name') ?></h3>
            <h6 class="font-weight-normal mb-0 col-12">All systems are running smoothly!</h6>
        </div>
    </div>
    <div class="row justify-content-end mb-4">
        <div class="col-6 col-md-3 col-xl-2 mb-4 mb-xl-0">
            <select class="form-control requested_for js-example-basic-single w-100" required>
                <option value="All" selected>All</option>
                @php
                $user_id = Auth::user()->employee_code . "*" . Auth::user()->employee_name;
                $leadSourceQuery = DB::table('user_lead_soureces')
                ->where('employee', $user_id)
                ->first();

                $leadSources = $leadSourceQuery ? explode(', ', $leadSourceQuery->lead_sources) : [];
                @endphp

                @foreach($leadSources as $leadSource)
                <option value="{{ $leadSource }}">{{ $leadSource }}</option>
                @endforeach

            </select>
        </div>
        <div class="col-6 col-md-3 col-xl-2 mb-4 mb-xl-0">
            <select class="form-control date_source js-example-basic-single w-100" required>
                <option value="lead_assignment_date" selected>Lead Assignment Date</option>
                <option value="last_lead_activity_date">Last Lead Activity Date</option>
                <option value="last_enquirer_activity_date">Last Enquirer Activity Date</option>
                <option value="recording_date">Call Recording Date</option>
                <option value="lead_followup_date">Lead Followup Date</option>
            </select>
        </div>
        <div class="col-12 col-md-3 col-xl-2 mb-4 mb-xl-0 dashboardFilter">
            <input type="text" id="date-filter"
                class="btn btn-sm btn-light bg-white dropdown-toggle text-right ml-auto d-flex w-100" />
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card tale-bg position-relative">
                <?php
                $qrData = "https://insityapp.com/AppAPI/insity.apk";
                $encodedQrData = urlencode($qrData);
                $qrCodeUrl = 'http://api.qrserver.com/v1/create-qr-code/?data=' . $encodedQrData . '&size=200x200';
                ?>
                <div class="app-apk p-3 row">
                    <h3 class="col-xl-8 col-lg-7 col-md-6">Scan to Download App</h3>
                    <img class="col-xl-4 col-lg-5 col-md-6" src="<?= $qrCodeUrl ?>" alt="QR Code" />
                </div>
                <div class="card-people mt-auto">
                    <img src="assets/images/dashboard/people.svg" alt="people">
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin transparent">
            <?php
            $cards = [
                [
                    'id' => 'hot',
                    'icon' => 'mdi-fire',
                    'title' => 'Hot Leads',
                    'colorClass' => 'card-light-danger'
                ],
                [
                    'id' => 'warm',
                    'icon' => 'mdi-weather-sunset',
                    'title' => 'Warm Leads',
                    'colorClass' => 'card-warning'
                ],
                [
                    'id' => 'cold',
                    'icon' => 'mdi-oil-temperature',
                    'title' => 'Cold Leads',
                    'colorClass' => 'card-tale'
                ],
                [
                    'id' => 'inquiry',
                    'icon' => 'mdi-phone-incoming',
                    'title' => 'Inquiry Leads',
                    'colorClass' => 'card-dark-blue'
                ],
                [
                    'id' => 'adm_process',
                    'icon' => 'mdi-school',
                    'title' => 'Admission in Process',
                    'colorClass' => 'card-light-blue'
                ],
                [
                    'id' => 'scrap',
                    'icon' => 'mdi-delete-variant',
                    'title' => 'Scrap Leads',
                    'colorClass' => 'card-secondary'
                ],
                [
                    'id' => 'non_qualified',
                    'icon' => 'mdi-repeat-off',
                    'title' => 'Non Qualified Leads',
                    'colorClass' => 'card-info'
                ],
                [
                    'id' => 'non_contactable',
                    'icon' => 'mdi-close-circle',
                    'title' => 'Non Contactable Leads',
                    'colorClass' => 'card-maroon',
                ],
                [
                    'id' => 'followup',
                    'icon' => 'mdi-check-circle',
                    'title' => 'Follow Up count Leads',
                    'colorClass' => 'card-info-light',
                ],
                [
                    'id' => 'amd_done',
                    'icon' => 'mdi-certificate',
                    'title' => 'Admission Done Leads',
                    'colorClass' => 'card-success'
                ],
                [
                    'id' => 'untouched',
                    'icon' => 'mdi-account-search',
                    'title' => 'Untouched Leads',
                    'colorClass' => 'card-danger'
                ],
                [
                    'id' => 'assigned',
                    'icon' => 'mdi-basket-fill',
                    'title' => 'Total Assigned Leads',
                    'colorClass' => 'card-dribbble'
                ]
            ];
            ?>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 g-4 mt-0">
                <?php foreach ($cards as $card): ?>
                    <div class="col pb-3 m-0">
                        <div class="card h-100 stat-card <?= $card['colorClass'] ?>" id="<?= $card['id'] ?>-card">
                            <a class="text-decoration-none text-white text-center m-auto" id="<?= $card['id'] ?>-link">
                                <div class="card-body justify-content-center row mt-1 mx-0">
                                    <p class="fs-30 mdi <?= $card['icon'] ?> mr-2 w-auto p-0"></p>
                                    <p class="fs-30 w-auto p-0" id="<?= $card['id'] ?>">0</p>
                                    <p class="mb-0 col-12 p-0"><?= $card['title'] ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Origin Wise Performance</h3>
                    <canvas id="lead_origin" width="299" height="200" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Status Performance</h3>
                    <canvas id="lead_status_canvas" width="299" height="200" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Performance</h3>
                    <canvas id="lead_total" width="299" height="200" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Source Wise Performance</h3>
                    <canvas id="lead_source" width="299" height="200" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">

                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Top Performing Channels</h3>
                    <button id="downloadCsvBtn1" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable1">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0;">
                                <tr>
                                    <th>Channels</th>
                                    <th>Total Leads</th>
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
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">State-Wise Performance</h3>
                    <button id="downloadCsvBtn2" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; ">
                                <tr>
                                    <th>State</th>
                                    <th>Total Leads</th>
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
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Zone-Wise Performance </h3>
                    <button id="downloadCsvBtn3" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable3">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; ">
                                <tr>
                                    <th>Zone</th>
                                    <th>Total Leads</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody3">
                                <!-- Data will be inserted here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Branch-Wise Performance </h3>
                    <button id="downloadCsvBtn4" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable4">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Branch</th>
                                    <th>Total Leads</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody4">
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
                    <h3 class="font-weight-bold mb-xl-4 text-primary">
                        Lead Source vs Lead Stage
                    </h3>
                    <button id="downloadCsvBtn5" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable5">
                    <div class="table-responsive">
                        <table class="table myTable">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; ">
                                <tr>
                                    <th>Source</th>
                                    <th>Untouched</th>
                                    <th>Hot</th>
                                    <th>Warm</th>
                                    <th>Cold</th>
                                    <th>Inquiry</th>
                                    <th>Admission In Process</th>
                                    <th>Admission Done</th>
                                    <th>Scrap</th>
                                    <th>Non Qualified</th>
                                    <th>Non Contactable</th>
                                    <th>Follow Up count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody5" class="myTbody">
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
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Lead Source Vs Branch Performance
                    </h3>
                    <button id="downloadCsvBtn6" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Branch</th>
                                    <th>Lead Source</th>
                                    <th>Untouched</th>
                                    <th>Hot</th>
                                    <th>Warm</th>
                                    <th>Cold</th>
                                    <th>Inquiry</th>
                                    <th>Admission In Process</th>
                                    <th>Admission Done</th>
                                    <th>Scrap</th>
                                    <th>Non Qualified</th>
                                    <th>Non Contactable</th>
                                    <th>Follow Up count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody6">
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
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Counsellor Vs Branch Performance
                    </h3>
                    <button id="downloadCsvBtn7" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable7">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; ">
                                <tr>
                                    <th>Branch</th>
                                    <th>Counsellor</th>
                                    <th>Untouched</th>
                                    <th>Hot</th>
                                    <th>Warm</th>
                                    <th>Cold</th>
                                    <th>Inquiry</th>
                                    <th>Admission In Process</th>
                                    <th>Admission Done</th>
                                    <th>Scrap</th>
                                    <th>Non Qualified</th>
                                    <th>Non Contactable</th>
                                    <th>Follow Up count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody7">
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
                    <h3 class="font-weight-bold mb-xl-4 text-primary">Branch Vs Lead Source Vs Counsellor
                        Performance
                    </h3>
                    <button id="downloadCsvBtn8" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable8">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Branch</th>
                                    <th>Lead Source</th>
                                    <th>Counsellor</th>
                                    <th>Untouched</th>
                                    <th>Hot</th>
                                    <th>Warm</th>
                                    <th>Cold</th>
                                    <th>Inquiry</th>
                                    <th>Admission In Process</th>
                                    <th>Admission Done</th>
                                    <th>Scrap</th>
                                    <th>Non Qualified</th>
                                    <th>Non Contactable</th>
                                    <th>Follow Up count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody8">
                                <!-- Data will be inserted here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (session("user_category") === "Admin") { ?>
        <div class="chat" id="chatBox" style="display: none;">
            <div class="chat-header">

                <button type="button" class="close" onclick="hideChatBox()">&times;</button>
            </div>
            <div class="chat-history">
                <ul id="chatLog" class="m-b-0">
                    <!-- Dynamic messages will be loaded here -->
                </ul>
            </div>
            <div class="chat-message clearfix">
                <!-- Reply Box -->
                <div id="replyBox" class="mb-2" style="display: none;">
                    <div class="alert alert-secondary">
                        Replying to: <span id="replyText"></span>
                        <button type="button" class="close" onclick="cancelReply()">&times;</button>
                    </div>
                </div>
                <!-- Chat Input and Send Button -->
                <div class="input-group mb-0">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-paper-plane"></i></span>
                    </div>
                    <input type="text" id="chatInput" class="form-control" placeholder="Enter text here...">
                    <input type="hidden" id="replyToMessageId">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="handleSendMessage()">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
@endsection

@section('customJs')
<script>
    $(document).ready(function() {
        // Initialize Perfect Scrollbar for tables
        const scrollbarTables = [
            "scrollbarTable1", "scrollbarTable2", "scrollbarTable3", "scrollbarTable4",
            "scrollbarTable5", "scrollbarTable6", "scrollbarTable7", "scrollbarTable8", "scrollbarTable10"
        ];

        scrollbarTables.forEach(tableId => {
            const scrollbar = document.getElementById(tableId);
            if (scrollbar) {
                new PerfectScrollbar(scrollbar, {
                    wheelPropagation: false
                });
            }
        });

        // CSV download handlers
        const downloadHandlers = [{
                btn: '#downloadCsvBtn1',
                table: '#statTableBody1',
                filename: 'Top Performing Channels.csv'
            },
            {
                btn: '#downloadCsvBtn2',
                table: '#statTableBody2',
                filename: 'State-Wise Performance.csv'
            },
            {
                btn: '#downloadCsvBtn3',
                table: '#statTableBody3',
                filename: 'Zone-Wise Performance.csv'
            },
            {
                btn: '#downloadCsvBtn4',
                table: '#statTableBody4',
                filename: 'Branch-Wise Performance.csv'
            },
            {
                btn: '#downloadCsvBtn5',
                table: '#statTableBody5',
                filename: 'Lead Source Wise Performance Report.csv'
            },
            {
                btn: '#downloadCsvBtn6',
                table: '#statTableBody6',
                filename: 'Lead Source Vs Branch Performance.csv'
            },
            {
                btn: '#downloadCsvBtn7',
                table: '#statTableBody7',
                filename: 'Counsellor Vs Branch Performance.csv'
            },
            {
                btn: '#downloadCsvBtn8',
                table: '#statTableBody8',
                filename: 'Lead Source Vs Counsellor Vs Branch Performance.csv'
            },
            {
                btn: '#downloadCsvBtn10',
                table: '#statTableBody10',
                filename: 'Lead Source Wise Performance Report.csv'
            }
        ];

        downloadHandlers.forEach(handler => {
            $(handler.btn).on('click', function() {
                downloadCSV(handler.table, handler.filename);
            });
        });

        // Table column freezing functionality
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

        // Date range picker initialization
        var startDate = moment().subtract(7, 'days').startOf('day');
        var endDate = moment().endOf('day');
        var requested_for = $(".requested_for").val();
        var date_source = $(".date_source").val();

        $('#date-filter').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: startDate,
            endDate: endDate
        }, function(start, end) {
            dateRange = start.format('YYYY-MM-DD') + '*' + end.format('YYYY-MM-DD');
            requested_for = $(".requested_for").val();
            date_source = $(".date_source").val();
            currentdateRange = dateRange;
            fetch_lead_stat(dateRange, requested_for, date_source);
        });

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
        fetch_lead_stat(currentdateRange, requested_for, date_source);

        $(".requested_for, .date_source").on("change", function() {
            requested_for = $(".requested_for").val();
            date_source = $(".date_source").val();
            fetch_lead_stat(currentdateRange, requested_for, date_source);
        });

        // Chart configuration
        var options = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: "top"
                },
                title: {
                    display: true,
                    text: "Lead Origin Distribution"
                }
            }
        };

        // Chart contexts
        var ctxOrigin = document.getElementById("lead_origin")?.getContext("2d");
        var ctxStatus = document.getElementById("lead_status_canvas")?.getContext("2d");
        var ctxTotal = document.getElementById("lead_total")?.getContext("2d");
        var ctxSource = document.getElementById("lead_source")?.getContext("2d");

        var myPieChartOrigin, myPieChartStatus, myPieChartTotal, myPieChartSource;

        // Fetch lead statistics
        function fetch_lead_stat(dateRange, requested_for, date_source) {
            $.ajax({
                url: "{{ route('leadStats') }}",
                type: "GET",
                data: {
                    requested_for,
                    date_source,
                    dateRange
                },
                dataType: "json",
                success: function(response) {
                    // Lead Source vs Lead Stage
                    if (response.source_ls_count && Object.keys(response.source_ls_count)
                        .length > 0) {
                        var tableBody = $("#statTableBody5");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.source_ls_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var lead_source = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${lead_source}</td>
                                                    <td>${count['Untouched']}</td>
                                                    <td>${count['Hot']}</td>
                                                    <td>${count['Warm']}</td>
                                                    <td>${count['Cold']}</td>
                                                    <td>${count['Inquiry']}</td>
                                                    <td>${count['Admission In Process']}</td>
                                                    <td>${count['Admission Done']}</td>
                                                    <td>${count['Scrap']}</td>
                                                    <td>${count['Non Qualified']}</td>
                                                    <td>${count['Non-Contactable']}</td>
                                                    <td>${count['Follow-Up']}</td>
                                                    <td>${count['Total']}</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });
                        $('#statTableBody5').on('click', 'tr', function() {
                            $('#statTableBody5 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    if (response.source_ls_count && Object.keys(response.source_ls_count)
                        .length > 0) {
                        var tableBody = $("#statTableBody10");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.source_ls_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var lead_source = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${lead_source}</td>
                                                    <td>${count['Total']}</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });
                        $('#statTableBody10').on('click', 'tr', function() {
                            $('#statTableBody10 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Lead Source Vs Branch Performance
                    if (response.source_branch_count && Object.keys(response
                            .source_branch_count).length > 0) {
                        var tableBody = $("#statTableBody6");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.source_branch_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var branch = row[0]; // "BANGALORE"
                            var leadSources = row[
                                1]; // Object containing different lead sources

                            // Loop through lead sources and extract values
                            Object.entries(leadSources).forEach(([lead_source,
                                count
                            ]) => {
                                var tableRow = `<tr>
                                                        <td>${branch}</td>
                                                        <td>${lead_source}</td>
                                                        <td>${count['Untouched']}</td>
                                                        <td>${count['Hot']}</td>
                                                        <td>${count['Warm']}</td>
                                                        <td>${count['Cold']}</td>
                                                        <td>${count['Inquiry']}</td>
                                                        <td>${count['Admission In Process']}</td>
                                                        <td>${count['Admission Done']}</td>
                                                        <td>${count['Scrap']}</td>
                                                        <td>${count['Non Qualified']}</td>
                                                        <td>${count['Non-Contactable']}</td>
                                                    <td>${count['Follow-Up']}</td>
                                                        <td>${count['Total']}</td>
                                                    </tr>`;
                                tableBody.append(tableRow);

                            });

                        });
                        $('#statTableBody6').on('click', 'tr', function() {
                            $('#statTableBody6 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Counsellor Vs Branch Performance
                    if (response.lead_owner_branch_count && Object.keys(response
                            .lead_owner_branch_count).length > 0) {
                        var tableBody = $("#statTableBody7");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.lead_owner_branch_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var branch = row[0]; // "BANGALORE"
                            var leadOwners = row[
                                1]; // Object containing different lead sources

                            // Loop through lead sources and extract values
                            Object.entries(leadOwners).forEach(([lead_owner,
                                count
                            ]) => {
                                var tableRow = `<tr>
                                                        <td>${branch}</td>
                                                        <td>${lead_owner}</td>
                                                        <td>${count['Untouched']}</td>
                                                        <td>${count['Hot']}</td>
                                                        <td>${count['Warm']}</td>
                                                        <td>${count['Cold']}</td>
                                                        <td>${count['Inquiry']}</td>
                                                        <td>${count['Admission In Process']}</td>
                                                        <td>${count['Admission Done']}</td>
                                                        <td>${count['Scrap']}</td>
                                                        <td>${count['Non Qualified']}</td>
                                                        <td>${count['Non-Contactable']}</td>
                                                    <td>${count['Follow-Up']}</td>
                                                        <td>${count['Total']}</td>
                                                    </tr>`;
                                tableBody.append(tableRow);

                            });

                        });
                        $('#statTableBody7').on('click', 'tr', function() {
                            $('#statTableBody7 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Branch Vs Lead Source Vs Counsellor Performance
                    if (response.source_lead_owner_branch_count && Object.keys(response
                            .source_lead_owner_branch_count).length > 0) {
                        var tableBody = $("#statTableBody8");
                        tableBody.empty(); // Clear existing data

                        var sortedData = Object.entries(response.source_lead_owner_branch_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var branch = row[0]; // "BANGALORE"
                            var lead_owners = row[
                                1]; // Object containing different lead sources

                            // Loop through lead sources
                            Object.entries(lead_owners).forEach(([lead_source,
                                lead_owner_data
                            ]) => {
                                // Loop through lead owners within each lead source
                                Object.entries(lead_owner_data).forEach(([
                                    lead_owner, count
                                ]) => {
                                    var tableRow = `<tr>
                                    <td>${branch}</td>
                                    <td>${lead_source}</td>
                                    <td>${lead_owner}</td>
                                    <td>${count['Untouched'] || 0}</td>
                                    <td>${count['Hot'] || 0}</td>
                                    <td>${count['Warm'] || 0}</td>
                                    <td>${count['Cold'] || 0}</td>
                                    <td>${count['Inquiry'] || 0}</td>
                                    <td>${count['Admission In Process'] || 0}</td>
                                    <td>${count['Admission Done'] || 0}</td>
                                    <td>${count['Scrap'] || 0}</td>
                                    <td>${count['Non Qualified'] || 0}</td>
                                    <td>${count['Non-Contactable'] || 0}</td>
                                    <td>${count['Follow-Up'] || 0}</td>
                                    <td>${count['Total'] || 0}</td>
                                </tr>`;
                                    tableBody.append(tableRow);
                                });
                            });
                        });

                        $('#statTableBody8').on('click', 'tr', function() {
                            $('#statTableBody8 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Top Lead Stages Cards
                    if (response.lead_stage_count && Object.keys(response.lead_stage_count)
                        .length > 0) {
                        $("#hot").text(response.lead_stage_count["Hot"]);
                        $("#warm").text(response.lead_stage_count["Warm"]);
                        $("#cold").text(response.lead_stage_count["Cold"]);
                        $("#inquiry").text(response.lead_stage_count["Inquiry"]);
                        $("#adm_process").text(response.lead_stage_count["Admission In Process"]);
                        $("#scrap").text(response.lead_stage_count["Scrap"]);
                        $("#non_qualified").text(response.lead_stage_count["Non Qualified"]);
                        $("#amd_done").text(response.lead_stage_count["Admission Done"]);
                        $("#assigned").text(response.lead_stage_count["Total"]);
                        $("#untouched").text(response.lead_stage_count["Untouched"]);
                        $("#non_contactable").text(response.lead_stage_count["Non-Contactable"]);
                        $("#followup").text(response.lead_stage_count["Follow-Up"]);

                        // Construct  href
                        $("#hot-lead-link").attr("href", "hot-leads?" + encodeURIComponent(
                            dateRange));
                        $("#warm-lead-link").attr("href", "warm-leads?" + encodeURIComponent(
                            dateRange));
                        $("#cold-lead-link").attr("href", "cold-leads?" + encodeURIComponent(
                            dateRange));
                        $("#inquiry-lead-link").attr("href", "inquiry-leads?" +
                            encodeURIComponent(dateRange));
                        $("#adm-process-link").attr("href", "admission-in-process?" +
                            encodeURIComponent(
                                dateRange));
                        $("#scrap-leads-link").attr("href", "scrap-leads?" + encodeURIComponent(
                            dateRange));
                        $("#non-qualified-link").attr("href", "non-qualified-leads?" +
                            encodeURIComponent(dateRange));
                        $("#adm-done-link").attr("href", "admission-done?" + encodeURIComponent(
                            dateRange));
                        $("#assigned-link").attr("href", "lead-manager?" + encodeURIComponent(
                            dateRange));
                        $("#untouched-link").attr("href", "untouched-leads?" +
                            encodeURIComponent(dateRange));
                        $("#non-contactable-link").attr("href", "non-contactable-leads?" +
                            encodeURIComponent(
                                dateRange));
                        $("#followup-link").attr("href", "followup-leads?" +
                            encodeURIComponent(dateRange));
                    }

                    // Lead Origin Wise Performance Pie Chart
                    if (response.lead_origin_count && Object.keys(response.lead_origin_count)
                        .length > 0) {
                        // Lead Origin Wise Performance
                        if (window.myPieChartOrigin) {
                            window.myPieChartOrigin.destroy();
                        }
                        var labels = [];
                        Object.keys(response.lead_origin_count).forEach(function(key) {
                            labels.push(key + " - " + response.lead_origin_count[key]);
                        });
                        window.myPieChartOrigin = new Chart(ctxOrigin, {
                            type: "pie",
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: Object.values(response
                                        .lead_origin_count),
                                    backgroundColor: [
                                        "#4B49AC",
                                        "#FFC100",
                                        "#248AFD",
                                        "#7DA0FA",
                                    ],
                                    borderColor: [
                                        "#4B49AC",
                                        "#FFC100",
                                        "#248AFD",
                                        "#7DA0FA",
                                    ],
                                    borderWidth: 1,
                                }],
                            },
                            options: options,
                        });
                    }

                    // Lead Status Performance Pie Chart
                    if (response.lead_status_count && Object.keys(response.lead_status_count)
                        .length > 0) {
                        // Lead Status Performance
                        if (window.myPieChartStatus) {
                            window.myPieChartStatus.destroy();
                        }
                        var labels = [];
                        Object.keys(response.lead_status_count).forEach(function(key) {
                            labels.push(key + " - " + response.lead_status_count[key]);
                        });
                        window.myPieChartStatus = new Chart(ctxStatus, {
                            type: "pie",
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: Object.values(response
                                        .lead_status_count),
                                    backgroundColor: [
                                        "#4B49AC",
                                        "#FFC100",
                                        "#248AFD",
                                        "#7DA0FA",
                                    ],
                                    borderColor: [
                                        "#4B49AC",
                                        "#FFC100",
                                        "#248AFD",
                                        "#7DA0FA",
                                    ],
                                    borderWidth: 1,
                                }],
                            },
                            options: options,
                        });
                    }

                    // Lead Performance Date Wise Bar Chart
                    if (response.lead_date_count && Object.keys(response.lead_date_count)
                        .length > 0) {
                        // Lead Performance
                        if (window.myPieChartTotal) {
                            window.myPieChartTotal.destroy();
                        }

                        window.myPieChartTotal = new Chart(ctxTotal, {
                            type: "bar",
                            data: {
                                labels: Object.keys(response.lead_date_count),
                                datasets: [{
                                    data: Object.values(response
                                        .lead_date_count),
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

                    // Lead Source Wise Performance Line Chart & Top Performing Channels
                    if (response.lead_source_count && Object.keys(response.lead_source_count)
                        .length > 0) {
                        if (myPieChartSource) {
                            myPieChartSource.destroy();
                        }

                        myPieChartSource = new Chart(ctxSource, {
                            type: "line",
                            data: {
                                labels: Object.keys(response.lead_source_count),
                                datasets: [{
                                    data: Object.values(response
                                        .lead_source_count),
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255,99,132,1)',
                                    borderWidth: 1,
                                    fill: true, // 3: no fill
                                }, ],
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

                        // Table Top Performing Channels
                        var tableBody = $("#statTableBody1");
                        tableBody.empty();

                        var sortedData = Object.entries(response.lead_source_count)
                            .sort((a, b) => b[1] - a[1]);
                        $.each(sortedData, function(index, row) {
                            var source = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${source}</td>      
                                                    <td>${count}</td>
                                                </tr>`;
                            tableBody.append(
                                tableRow); // Make sure #tableBody is correctly selected
                        });

                        $('#statTableBody1').on('click', 'tr', function() {
                            $('#statTableBody1 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // State-Wise Performance
                    if (response.lead_state_count && Object.keys(response.lead_state_count)
                        .length > 0) {
                        var tableBody = $("#statTableBody2");
                        tableBody.empty();

                        var sortedData = Object.entries(response.lead_state_count)
                            .sort((a, b) => b[1] - a[1]);
                        $.each(sortedData, function(index, row) {
                            var state = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${state}</td>      
                                                    <td>${count}</td>
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

                    // Zone-Wise Performance
                    if (response.lead_zone_count && Object.keys(response.lead_zone_count)
                        .length > 0) {
                        var tableBody = $("#statTableBody3");
                        tableBody.empty();

                        var sortedData = Object.entries(response.lead_zone_count)
                            .sort((a, b) => b[1] - a[1]);
                        $.each(sortedData, function(index, row) {
                            var zone = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${zone}</td>      
                                                    <td>${count}</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });

                        $('#statTableBody3').on('click', 'tr', function() {
                            $('#statTableBody3 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }

                    // Branch-Wise Performance
                    if (response.lead_branch_count && Object.keys(response.lead_branch_count)
                        .length > 0) {
                        var tableBody = $("#statTableBody4");
                        tableBody.empty();

                        var sortedData = Object.entries(response.lead_branch_count)
                            .sort((a, b) => b[1] - a[1]);
                        $.each(sortedData, function(index, row) {
                            var branch = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${branch}</td>      
                                                    <td>${count}</td>
                                                </tr>`;
                            tableBody.append(tableRow);
                        });

                        $('#statTableBody4').on('click', 'tr', function() {
                            $('#statTableBody4 tr').removeClass(
                                'selected-row'); // Remove the class from all rows
                            $(this).addClass(
                                'selected-row'); // Add the class to the clicked row
                        });
                    }
                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Initialize Bootstrap collapse
        $('[data-toggle="collapse"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).collapse('toggle');
            $(this).closest('.nav-item').toggleClass('active');
        });

        // Logout button functionality
        $(".logout-button").on("click", function() {
            sessionStorage.removeItem("selectedDateRange");
        });
    });
</script>
@endsection
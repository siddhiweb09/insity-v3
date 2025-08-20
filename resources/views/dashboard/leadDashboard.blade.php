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
                    <h3 class="font-weight-bold mb-xl-4 text-primary">
                        Lead Source Wise Count
                    </h3>
                    <button id="downloadCsvBtn10" type="button" class="btn btn-inverse-primary btn-icon"
                        data-toggle="tooltip" title="Download Report">
                        <i class="mdi mdi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="scrollbarTable10">
                    <div class="table-responsive">
                        <table class="table myTable">
                            <thead class="bg-primary text-white" style="position: sticky; top: 0; ">
                                <tr>
                                    <th>Source</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="statTableBody10" class="myTbody">
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
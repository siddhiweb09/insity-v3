<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Insity - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="{{ asset('assets/select2/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap-theme/select2-bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/summernote/summernote-bs4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />


    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Then Bootstrap JS bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/mediaCss.css') }}" />

    <style>
        #pdf-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
            overflow: auto;
            background-color: #f9f9f9;
        }

        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }

        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }

        .autocomplete-active {
            background-color: DodgerBlue !important;
            color: #ffffff;
        }

        .sticky-cell {
            position: sticky;
            left: 0;
            background: white;
            z-index: 10;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .selected-row {
            background-color: #f8f9fa !important;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        @if(Auth::user()->user_category !== "Chat Support")
        @include('offcanvas.offcanvas_new_lead')
        @endif

        <!-- Navbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/images/logo.png') }}" class="mr-2" alt="logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" />
                </a>
            </div>

            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>

                <ul class="navbar-nav mr-lg-2">
                    @if(Auth::user()->user_category !== "Chat Support")
                    <li class="nav-item nav-search d-none d-lg-block">
                        <button type="button"
                            class="btn btn-inverse-primary btn-rounded btn-icon newlead d-flex align-items-center justify-content-center"
                            data-bs-original-title="Add Lead" aria-label="Add Lead">
                            <i class="mdi mdi-plus m-auto"></i>
                        </button>
                    </li>
                    @endif

                    <li class="nav-item nav-search d-none ml-1 d-lg-block">
                        <div class="input-group">
                            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                                <span class="input-group-text" id="search">
                                    <i class="icon-search"></i>
                                </span>
                            </div>
                            <div class="autocomplete" style="width:300px;">
                                <input id="myInput" class="border-0 pl-3 pt-2" type="text" name="myCountry"
                                    placeholder="Search Now">
                            </div>
                        </div>
                    </li>
                </ul>

                <ul class="navbar-nav navbar-nav-right">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        @php
                        $user_id = Auth::user()->employee_code . "*" . Auth::user()->employee_name;
                        $recommendationCount = DB::table('recommendations')
                        ->where('lead_owner', $user_id)
                        ->where('seen', 0)
                        ->count();
                        @endphp

                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-bell-outline" style="font-size: 1.60rem;color: #4b49ac;"></i>
                            @if($recommendationCount > 0)
                            <span class="count">{{ $recommendationCount }}</span>
                            @endif
                        </a>

                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="notificationDropdown">
                            <h6 class="dropdown-header">Recommendations</h6>
                            <div class="dropdown-divider"></div>

                            @if($recommendationCount > 0)
                            @foreach(DB::table('recommendations')->where('lead_owner', $user_id)->where('seen', 0)->get() as $recommendation)
                            <a class="dropdown-item"
                                href="{{ url('lead-details?param=' . base64_encode($recommendation->log_id) . '&status=' . base64_encode($recommendation->id)) }}">
                                <i class="mdi mdi-email-outline text-primary"></i>
                                {{ $recommendation->recommendation }} added by {{ $recommendation->added_by }}
                            </a>
                            @endforeach
                            @else
                            <a class="dropdown-item">
                                <i class="mdi mdi-email-outline text-primary"></i>
                                No new recommendations.
                            </a>
                            @endif
                        </div>
                    </li>

                    <!-- User Profile -->
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if(empty(Auth::user()->profile_picture))
                            <img src="{{ asset('assets/images/face28.jpg') }}" alt="profile" />
                            @else
                            <img src="{{ asset('dbFiles/profile_picture/' . Auth::user()->profile_picture) }}"
                                alt="Profile Picture">
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <i class="mdi mdi-account-circle text-primary"></i>
                                Profile
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item logout-button"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-logout text-primary"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>

                    <li class="nav-item nav-settings d-none d-lg-flex">
                        {{ Auth::user()->employee_name }}
                    </li>
                </ul>

                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    @foreach(session('sidebar_menu') as $category => $data)
                    @php
                    $category_id = strtolower(str_replace(' ', '-', $category));
                    $is_category_active = false;

                    // Check if any item in this category is active
                    foreach ($data['items'] as $item) {
                    if (
                    request()->is(trim($item['url'], '/')) ||
                    (trim($item['url'], '/') !== '' && strpos(request()->path(), trim($item['url'], '/')) === 0)
                    ) {
                    $is_category_active = true;
                    break;
                    }
                    }
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#{{ $category_id }}-menu"
                            aria-expanded="{{ $is_category_active ? 'true' : 'false' }}"
                            aria-controls="{{ $category_id }}-menu">
                            {!! $data['icon'] !!}
                            <span class="menu-title">{{ $category }}</span>
                            <i class="collapse-arrow mdi mdi-arrow-down-drop-circle"></i>
                        </a>
                        <div class="collapse {{ $is_category_active ? 'show' : '' }}" id="{{ $category_id }}-menu">
                            <ul class="nav flex-column sub-menu">
                                @foreach($data['items'] as $item)
                                @php
                                $is_active = request()->is(trim($item['url'], '/')) ||
                                (trim($item['url'], '/') !== '' &&
                                strpos(request()->path(), trim($item['url'], '/')) === 0);
                                @endphp

                                <li class="nav-item">
                                    <a class="nav-link {{ $is_active ? 'active' : '' }}" href="{{ url($item['url']) }}">
                                        {{ $item['name'] }}
                                        @if($is_active)
                                        <span class="sr-only">(current)</span>
                                        @endif
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Main Panel -->
            <div class="main-panel">
                @yield('content')

                <div class="followupDiv d-none"></div>

                <!-- Footer -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <div>
                            <a href="{{ url('privacy-policy') }}" class="px-2 text-secondary text-small">Privacy
                                Policy</a>
                            <a href="{{ url('terms-and-conditions') }}"
                                class="border-top-0 border border-bottom-0 px-2 text-secondary text-small">Terms and
                                Conditions</a>
                            <a href="{{ url('about-us') }}" class="px-2 text-secondary text-small">About Us</a>
                        </div>
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.
                            <a href="https://insityapp.com" target="_blank">ISBM Group</a>. All rights reserved.</span>
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">v.1.2024</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script>
        window.__TEAM_MEMBERS__ = @json(session('team_members', []));
    </script>

    <!-- Moment.js (required for daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Then your other scripts -->
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('assets/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/js/filter.js') }}"></script>
    <script src="{{ asset('assets/js/leads.js') }}"></script>
    <script src="{{ asset('assets/js/new-lead.js') }}"></script>

    <!-- Date Range Picker (after moment.js) -->

    <!-- Modal check_followup -->
    <div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followupModalLabel">Followup Details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Dynamic content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-inverse-danger btn-fw" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal check_activity -->
    <div class="modal fade" id="userActivity" tabindex="-1" role="dialog" aria-labelledby="userActivityLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userActivityLabel">User Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content will be updated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var scrollbar1 = document.getElementById("scrollableBody");

        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false,
            });
        }
        $(".preloader").fadeOut("slow");

        function inactivityTime() {
            let timer;
            let isActive = true;

            function resetTimer() {
                clearTimeout(timer);
                if (!isActive) {
                    isActive = true;
                    sendStatusToServer("Active");
                }
                timer = setTimeout(goInactive, 60000);
            }

            function goInactive() {
                isActive = false;
                sendStatusToServer("Inactive");
            }

            function sendStatusToServer(status) {
                $.ajax({
                    type: "POST",
                    url: "dbFiles/statusElement.php",
                    dataType: "json",
                    data: {
                        statusElement: status
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        console.error("Error fetching statusElement:", error);
                    },
                });
            }

            function setupEvents() {
                window.addEventListener("mousemove", resetTimer);
                window.addEventListener("keydown", resetTimer);
                window.addEventListener("scroll", resetTimer);
                window.addEventListener("click", resetTimer);
                window.addEventListener("touchstart", resetTimer);
            }

            setupEvents();
            resetTimer(); // Initialize timer
        }

        document.addEventListener("DOMContentLoaded", function() {
            inactivityTime();
        });

        $(function() {
            $("#summernote").summernote({
                tabsize: 2,
                height: 300,
            });
        });

        // CSV Download
        function downloadCSV(tableSelector, fileName) {
            var table = document.querySelector(tableSelector).closest("table");
            var rows = table.querySelectorAll("thead, tbody tr"); // Include both header and body rows

            var csvContent = "";

            rows.forEach((row) => {
                const cells = row.querySelectorAll("td, th");
                const rowData = Array.from(cells)
                    .map((cell) => `"${cell.textContent.replace(/"/g, '""')}"`)
                    .join(",");
                csvContent += rowData + "\n";
            });

            const blob = new Blob([csvContent], {
                type: "text/csv;charset=utf-8;"
            });
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", fileName);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        function downloadCSV2(tableSelector, fileName) {
            var table = document.querySelector(tableSelector).closest("table");
            var rows = table.querySelectorAll("thead, tbody tr"); // Include both header and body rows

            var csvContent = "";

            rows.forEach((row) => {
                const cells = row.querySelectorAll("td, th");

                // Convert the cells to an array and slice it to skip the first two columns
                const rowData = Array.from(cells)
                    .slice(2) // Skip the first two columns
                    .map((cell) => `"${cell.textContent.replace(/"/g, '""')}"`)
                    .join(",");

                csvContent += rowData + "\n";
            });

            const blob = new Blob([csvContent], {
                type: "text/csv;charset=utf-8;"
            });
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", fileName);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        //Check followup
        function base64_encode(str) {
            return btoa(unescape(encodeURIComponent(str)));
        }

        setInterval(check_followup, 600000);

        function check_followup() {
            $.ajax({
                url: "dbFiles/check_followup.php",
                method: "GET",
                success: function(response) {
                    try {
                        if (typeof response === "string") {
                            response = JSON.parse(response);
                        }
                        if (response.active_followup === "TRUE") {
                            let followupDetails = "";
                            $(".followupDiv").empty();
                            if (response.followups && response.followups.length > 0) {
                                $(".followupDiv").removeClass("d-none");
                                response.followups.forEach(function(followup) {
                                    var param = base64_encode(followup.id.toString());
                                    followupDetails += `<a href="lead-details?param=${param}"><div role="alert" class="fade d-flex align-items-center alert alert-info show">
            <p class="mb-0">${followup.task}</p></div></a>`;
                                    // send_followup(followup.task, followup.id);
                                });
                            }
                            $(".followupDiv").append(followupDetails);

                            // $("#followupModal .modal-body").html(followupDetails);
                            // $("#followupModal").modal("toggle");
                        } else {
                            // console.log("No active followup found.");
                        }
                    } catch (error) {
                        console.error("Error processing response:", error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                },
            });
        }

        //get Checked Values
        var checkedValues = [];

        function getCheckedValues() {
            checkedValues = [];
            $("input[name='check']").prop("checked", $(this).prop("checked"));

            $("input[name='check']:checked").each(function() {
                checkedValues.push($(this).val());
            });
            // console.log("checkedValues:" + checkedValues);
        }

        $("#checkAll").click(function() {
            $("input[name='check']").prop("checked", $(this).prop("checked"));
        });

        // Check/uncheck the "checkAll" checkbox based on the state of individual checkboxes
        $("input[name='check']").click(function() {
            if (
                $("input[name='check']:checked").length ===
                $("input[name='check']").length
            ) {
                $("#checkAll").prop("checked", true);
            } else {
                $("#checkAll").prop("checked", false);
            }
        });

        // Attach onchange event handler to the #state select element
        $("#state").on("change", function() {
            var state = $(this).val();
            fetch_cities(state);
        });

        function fetch_cities(state) {
            $.ajax({
                type: "POST",
                url: "fetchAPI/fetch_cities.php",
                dataType: "json",
                data: {
                    state: state
                },
                success: function(response) {
                    var cities = response.cities;
                    var citiesSelect = $("#city");
                    citiesSelect.empty();

                    $.each(cities, function(index, city) {
                        citiesSelect.append(
                            $("<option>", {
                                value: city,
                                text: city,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching cities:", error);
                },
            });
        }

        // Fetch levels
        function fetch_levels(entity) {
            $.ajax({
                type: "POST",
                url: "fetchAPI/fetch_levels.php",
                dataType: "json",
                data: {
                    entity: entity
                },
                success: function(response) {
                    var levels = response.levels;
                    var levelsSelect = $("#level");
                    levelsSelect.empty();

                    $.each(levels, function(index, level) {
                        levelsSelect.append(
                            $("<option>", {
                                value: level,
                                text: level,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching levels:", error);
                },
            });
        }
        // fetch_levels();

        $("#widget_name").on("change", function() {
            var entity = $(this).val();
            fetch_levels(entity);
        });

        // Attach onchange event handler to the #level select element
        $("#level").on("change", function() {
            var level = $(this).val();
            var entity = $("#widget_name").val();
            fetch_courses(level, entity);
        });

        function fetch_courses(level, entity) {
            $.ajax({
                type: "POST",
                url: "fetchAPI/fetch_courses.php",
                dataType: "json",
                data: {
                    level: level,
                    entity: entity
                },
                success: function(response) {
                    var courses = response.courses;
                    var coursesSelect = $("#course");
                    coursesSelect.empty();

                    $.each(courses, function(index, course) {
                        coursesSelect.append(
                            $("<option>", {
                                value: course,
                                text: course,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching courses:", error);
                },
            });
        }

        // Fetch lead_source
        function fetch_lead_source() {
            $.ajax({
                type: "POST",
                url: "fetchAPI/fetch_lead_sources.php",
                dataType: "json",
                data: "",
                success: function(response) {
                    var leadsources = response.leadsources;
                    var leadsourcesSelect = $("#lead_source");
                    leadsourcesSelect.empty();

                    $.each(leadsources, function(index, leadsource) {
                        leadsourcesSelect.append(
                            $("<option>", {
                                value: leadsource,
                                text: leadsource,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching lead_source:", error);
                },
            });
        }

        // Fetch states
        function fetch_states() {
            $.ajax({
                type: "POST",
                url: "fetchAPI/fetch_states.php",
                dataType: "json",
                data: "",
                success: function(response) {
                    var states = response.states;
                    var statesSelect = $("#state");
                    statesSelect.empty();

                    $.each(states, function(index, state) {
                        statesSelect.append(
                            $("<option>", {
                                value: state,
                                text: state,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching states:", error);
                },
            });
        }

        $(document).ready(function() {
            var active_user = "<?php session('employee_code') ?>";

            function setWhatsappContent(response) {
                $("#template_id").val(response.template_id);
                $("#entity").val(response.entity);
                $("#button_payload").val(response.button_payload);
                $("#header_url").val(response.header_url);
                $("#header_type").val(response.header_type);
                $("#template_for").val(response.template_for);
                $("#whatsapp_template").summernote("code", response.templates);
                $(".edit-whatsapp-forms").attr(
                    "action",
                    "edit-whatsapp-template?id=" + response.id
                );
            }

            $(".editWhatsapp").on("click", function() {
                var dataId = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_whatsapp_templates.php",
                    dataType: "json",
                    data: {
                        id: dataId
                    },
                    success: function(response) {
                        setWhatsappContent(response);
                    },
                    error: function(error) {
                        console.error("Error fetching whatsapp templates:", error);
                    },
                });
            });

            $(".viewWhatsapp").on("click", function() {
                var dataId = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_whatsapp_templates.php",
                    dataType: "json",
                    data: {
                        id: dataId
                    },
                    success: function(response) {
                        $("#viewWhatsappTemplateModalLabel").text(response.template_id);
                        $("#whatsappTemplateView").html(response.templates);
                    },
                    error: function(error) {
                        console.error("Error fetching whatsapp templates:", error);
                    },
                });
            });

            $(".edit-whatsapp-forms").on("submit", function(event) {
                event.preventDefault();
                var formData = $(this).serializeArray();
                var actionUrl = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: formData,
                    success: function(response) {
                        console.log("Whatsapp template updated successfully:", response);
                        $("#offcanvasEnd").offcanvas("hide");
                        window.location.href = "whatsapp-templates";
                    },
                    error: function(error) {
                        console.error("Error updating Whatsapp template:", error);
                    },
                });
            });

            $("#emailBody").summernote({
                tabsize: 2,
                height: 600,
            });

            $("#sms").summernote({
                tabsize: 2,
                height: 600,
            });

            $("#whatsapp_template").summernote({
                tabsize: 2,
                height: 400,
            });

            function setEmailContent(response) {
                $("#emailSubject").val(response.subject);
                $("#emailBody").summernote("code", response.body);
                $(".edit-email-forms").attr(
                    "action",
                    "dbFiles/edit_email_template.php?id=" + response.id
                );
            }

            $(".editEmail").on("click", function() {
                var dataId = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_email_templates.php",
                    dataType: "json",
                    data: {
                        id: dataId
                    },
                    success: function(response) {
                        setEmailContent(response);
                    },
                    error: function(error) {
                        console.error("Error fetching email templates:", error);
                    },
                });
            });

            $(".edit-email-forms").on("submit", function(event) {
                event.preventDefault();
                var formData = $(this).serializeArray();
                formData.push({
                    name: "template",
                    value: $("#emailBody").summernote("code"),
                });
                var actionUrl = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: formData,
                    success: function(response) {
                        // console.log("Email template updated successfully:", response);
                        $("#offcanvasEnd").offcanvas("hide");
                        window.location.href = "email-templates";
                    },
                    error: function(error) {
                        console.error("Error updating email template:", error);
                    },
                });
            });

            function setSMSContent(response) {
                $("#template_id").val(response.template_id);
                $("#sender_id").val(response.sender_id);
                $("#sms").summernote("code", response.sms);
                $(".edit-sms-forms").attr(
                    "action",
                    "dbFiles/edit_sms_template.php?id=" + response.id
                );
            }

            $(".editSMS").on("click", function() {
                var dataId = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_sms_templates.php",
                    dataType: "json",
                    data: {
                        id: dataId
                    },
                    success: function(response) {
                        setSMSContent(response);
                    },
                    error: function(error) {
                        console.error("Error fetching sms templates:", error);
                    },
                });
            });

            $(".edit-sms-forms").on("submit", function(event) {
                event.preventDefault();
                var formData = $(this).serializeArray();
                formData.push({
                    name: "sms",
                    value: $("#sms").summernote("code")
                });
                var actionUrl = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: formData,
                    success: function(response) {
                        // console.log("SMS template updated successfully:", response);
                        $("#offcanvasEnd").offcanvas("hide");
                        window.location.href = "sms-templates";
                    },
                    error: function(error) {
                        console.error("Error updating SMS template:", error);
                    },
                });
            });

            // edit Lead Details
            $(".editLead").on("click", function() {
                var dataId = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_lead_details.php",
                    dataType: "json",
                    data: {
                        log_id: dataId
                    },
                    success: function(response) {
                        setLeadContent(response);
                    },
                    error: function(error) {
                        console.error("Error fetching Lead Details:", error);
                    },
                });
            });

            function setLeadContent(response) {
                $("#registered_name").val(response.registered_name);
                $("#registered_email").val(response.registered_email);
                $("#registered_mobile").val(response.registered_mobile);
                $("#alternate_mobile").val(response.alternate_mobile);
                $("#registered_state").text(response.state);
                $("#registered_city").text(response.city);
                $("#registered_level").text(response.level_applying_for);
                $("#registered_course").text(response.course);

                var registered_state = response.state;
                var registered_city = response.city;
                var registered_level = response.level_applying_for;
                var registered_course = response.course;
                var entity = response.widget_name;

                // $("#level").on("change", function () {
                //   var level = $(this).val();
                //   // var entity = $("#widget_name").val();
                //   fetch_courses(level, entity);
                // });

                $.ajax({
                    type: "POST",
                    url: "dbFiles/check_data_existance.php",
                    dataType: "json",
                    data: {
                        state: response.state,
                        city: response.city,
                        level: response.level_applying_for,
                        course: response.course,
                        entity: response.entity,
                    },
                    success: function(response) {
                        $("#stateDiv").empty();
                        $("#cityDiv").empty();
                        $("#levelDiv").empty();
                        $("#courseDiv").empty();

                        if (response.stateStatus === "notmatched") {
                            $("#stateerror")
                                .text("Kindly choose correct state as per our system")
                                .show();
                            var selectElement = $("<select>")
                                .attr("id", "state")
                                .addClass("form-control state");

                            selectElement.appendTo("#stateDiv");

                            selectElement.on("change", function() {
                                var state = $(this).val();
                                fetch_cities(state);
                            });
                        } else {
                            $("<input>")
                                .addClass("form-control")
                                .val(registered_state)
                                .attr("id", "state")
                                .prop("disabled", true)
                                .appendTo("#stateDiv");
                        }

                        if (
                            response.cityStatus === "notmatched" &&
                            response.stateStatus === "notmatched"
                        ) {
                            $("#cityerror")
                                .text("Kindly choose correct state as per our system")
                                .show();

                            var selectElement = $("<select>")
                                .attr("id", "city")
                                .addClass("form-control city");
                            selectElement.appendTo("#cityDiv");
                        } else if (
                            response.cityStatus === "notmatched" &&
                            response.stateStatus === "matched"
                        ) {
                            $("#cityerror")
                                .text("Kindly choose correct city as per our system")
                                .show();

                            var selectElement = $("<select>")
                                .attr("id", "city")
                                .addClass("form-control city");
                            selectElement.appendTo("#cityDiv");
                            fetch_cities(registered_state);
                        } else {
                            $("<input>")
                                .addClass("form-control")
                                .val(registered_city)
                                .attr("id", "city")
                                .prop("disabled", true)
                                .appendTo("#cityDiv");
                        }
                        // console.log(response.levelStatus);

                        if (response.levelStatus === "notmatched") {
                            $("#levelerror")
                                .text("Kindly choose correct level as per our system")
                                .show();
                            var selectElement = $("<select>")
                                .attr("id", "level")
                                .addClass("form-control level");

                            console.log(entity);
                            fetch_levels(entity);

                            selectElement.appendTo("#levelDiv");

                            selectElement.on("change", function() {
                                var level = $(this).val();
                                fetch_courses(level, entity);
                            });
                        } else {
                            $("<input>")
                                .addClass("form-control")
                                .val(registered_level)
                                .attr("id", "level")
                                .prop("disabled", true)
                                .appendTo("#levelDiv");
                        }
                        if (
                            response.courseStatus === "notmatched" &&
                            response.levelStatus === "notmatched"
                        ) {
                            $("#courseerror")
                                .text("Kindly choose correct level as per our system")
                                .show();

                            var selectElement = $("<select>")
                                .attr("id", "course")
                                .addClass("form-control course");
                            selectElement.remove();
                            selectElement.appendTo("#courseDiv");
                        } else if (
                            response.courseStatus === "notmatched" &&
                            response.levelStatus === "matched"
                        ) {
                            $("#courseerror")
                                .text("Kindly choose correct course as per our system")
                                .show();

                            var selectElement = $("<select>")
                                .attr("id", "course")
                                .addClass("form-control course");
                            selectElement.remove();
                            selectElement.appendTo("#courseDiv");
                            fetch_courses(registered_level, entity);
                        } else {
                            $("<input>")
                                .addClass("form-control")
                                .val(registered_course)
                                .attr("id", "course")
                                .prop("disabled", true)
                                .appendTo("#courseDiv");
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching Lead Details:", error);
                    },
                });
                $(".edit-lead-forms").attr(
                    "action",
                    "dbFiles/edit_lead.php?log_id=" + response.log_id
                );
                $(".assign-lead-forms").attr(
                    "action",
                    "assign-lead?id=" + response.log_id
                );
            }

            $(".edit-lead-forms").on("submit", function(event) {
                event.preventDefault();

                var formData = {
                    registered_name: $("#registered_name").val(),
                    registered_email: $("#registered_email").val(),
                    registered_mobile: $("#registered_mobile").val(),
                    alternate_mobile: $("#alternate_mobile").val(),
                    registered_state: $("#state").val(),
                    registered_city: $("#city").val(),
                    level_applying_for: $("#level").val(),
                    registered_course: $("#course").val(),
                };

                var actionUrl = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: formData,
                    success: function(response) {
                        $("#editLead").offcanvas("hide");
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error("Error updating Lead details:", error);
                    },
                });
            });

            // Assign Lead
            $(".assginLead").on("click", function() {
                var offcanvasElement = document.getElementById("assginLeadsOffcanvas");
                var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
                bsOffcanvas.show();
                var dataId = $(this).attr("data-id");

                $.ajax({
                    type: "POST",
                    url: "fetchFiles/fetch_lead_details.php",
                    dataType: "json",
                    data: {
                        log_id: dataId
                    },
                    success: function(response) {
                        setLeadContent(response);
                    },
                    error: function(error) {
                        console.error("Error fetching Lead Details:", error);
                    },
                });
            });

            $(".assign-lead-forms").on("submit", function(event) {
                event.preventDefault();

                var formData = {
                    registered_name: $("#registered_name").val(),
                    registered_email: $("#registered_email").val(),
                    registered_mobile: $("#registered_mobile").val(),
                    alternate_mobile: $("#alternate_mobile").val(),
                    registered_state: $("#state").val(),
                    registered_city: $("#city").val(),
                    level_applying_for: $("#level").val(),
                    registered_course: $("#course").val(),
                };

                var actionUrl = $(this).attr("action");

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: formData,
                    success: function(response) {
                        console.log("Lead details updated successfully:", response);
                        $("#assginLeadsOffcanvas").offcanvas("hide");
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error("Error updating Lead details:", error);
                    },
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Bootstrap collapse functionality
            $('[data-toggle="collapse"]').on('click', function(e) {
                // Prevent default if needed (e.g., for anchor tags)
                e.preventDefault();

                // Get the target collapse element
                var target = $(this).attr('href');
                $(target).collapse('toggle');

                // Find the nav-item parent and toggle active class
                var navItem = $(this).closest('.nav-item');
                navItem.toggleClass('active');

                // For debugging
                console.log('Toggled:', target, 'Parent item:', navItem);
            });

            // Logout button functionality
            $(".logout-button").on("click", function() {
                sessionStorage.removeItem("selectedDateRange");
            });

            // Set active menu items
            function setActiveMenu() {
                var currentPath = window.location.pathname.replace(/\/+$/, '');

                $('.nav-link').each(function() {
                    var $link = $(this);
                    var linkPath = $link.attr('href');

                    if (linkPath && linkPath !== '#') {
                        linkPath = linkPath.split('?')[0].split('#')[0].replace(/\/+$/, '');

                        if (currentPath === linkPath ||
                            (linkPath !== '/' && currentPath.indexOf(linkPath) === 0)) {

                            // If this is a submenu item, expand its parent
                            if ($link.closest('.sub-menu').length) {
                                var $parentCollapse = $link.closest('.collapse');
                                $parentCollapse.addClass('show');
                            }
                        }
                    }
                });
            }

            // Run on page load
            setActiveMenu();

            // Run again if URL changes (for SPA-like behavior)
            $(window).on('popstate', setActiveMenu);
        });
    </script>

    <script>
        // Autocomplete function
        function autocomplete(inp, arr) {
            var currentFocus;
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                closeAllLists();
                if (!val) return false;
                currentFocus = -1;
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < arr.length; i++) {
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        b.addEventListener("click", function(e) {
                            var selectedValue = this.getElementsByTagName("input")[0].value;
                            inp.value = selectedValue;

                            $.ajax({
                                url: '{{ url("fetchFiles/fetch_serach_lead_id") }}',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    selectedValue: selectedValue
                                },
                                success: function(data) {
                                    window.location.href = `https://insityapp.com/lead-details?param=${btoa(data || '')}`;
                                },
                                error: function(error) {
                                    console.error("Error fetching session data:", error);
                                }
                            });
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });

            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = x.length - 1;
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }

            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        // Initialize autocomplete with lead data
        var leadData = @json(session('leadData', []));

        autocomplete(document.getElementById("myInput"), leadData);
    </script>

    @yield('customJs')
</body>

</html>
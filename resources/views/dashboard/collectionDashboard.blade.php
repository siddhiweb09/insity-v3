@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-8 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold" id="generatePdfBtn">Collection Dashboard</h3>
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
                    <h3 class="font-weight-500 text-primary">Top 10 Counsellor's</h3>
                    <div class="row m-0 counsellor-collection"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-500 text-primary">Entity-wise Financial Summary Report</h3>
                    <canvas id="lead_total" width="299" height="200"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-10 col-xl-10 col-md-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-500 text-primary">Transaction and Entity-wise Financial Summary Report</h3>
                        </div>
                        <div class="col-2 col-xl-2 col-md-4 mb-4 mb-xl-0">
                            <select name="widget_name" class="form-control border-1 js-example-basic-single w-100 widget_name" style="border: 1px solid #000 !important">
                                <option value="ISBM" selected>ISBM</option>
                                <option value="ISBMU">ISBM University</option>
                                <option value="ISTM">ISTM</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="lead_source" width="299" height="200"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4 stretch-card transparent">
            <div class="card overflow-hidden p-4" style="height: 500px">
                <div class="row mx-0 mb-3 justify-content-between">
                    <h3 class="font-weight-500 text-primary">Counsellor Wise COllection Report
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
                                    <th> Counselor </th>
                                    <th> Branch </th>
                                    <th> Zone </th>
                                    <th> Record Date </th>
                                    <th> Entity </th>
                                    <th> Isbmfc001 Receipt </th>
                                    <th> Isbmfc001 Settled </th>
                                    <th> Isbmfc001 Pending </th>
                                    <th> Isbmpdc002 Receipt </th>
                                    <th> Isbmpdc002 Settled </th>
                                    <th> Isbmpdc002 Pending </th>
                                    <th> Isbmpdcfc003 Receipt </th>
                                    <th> Isbmpdcfc003 Settled </th>
                                    <th> Isbmpdcfc003 Pending </th>
                                    <th> Isbmoc004 Receipt </th>
                                    <th> Isbmoc004 Settled </th>
                                    <th> Isbmoc004 Pending </th>
                                    <th> Isbmrc005 Receipt </th>
                                    <th> Isbmrc005 Settled </th>
                                    <th> Isbmrc005 Pending </th>
                                    <th> Isbmpc006 Receipt </th>
                                    <th> Isbmpc006 Settled </th>
                                    <th> Isbmpc006 Pending </th>
                                    <th> Total Receipt</th>
                                    <th> Total Settled </th>
                                    <th> Total Pending </th>
                                    <th> Admission Count </th>
                                    <th> Pr Sales Amount </th>
                                    <th> Sr Sales Amount </th>
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
    </div>
</div>
@endsection

@section('customJs')
<script>
    $(document).ready(function() {
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

            fetch_lead_stat(dateRange, "ISBM");
        });

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        var currentdateRange = formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());

        fetch_lead_stat(currentdateRange, "ISBM");

        //  chart_data
        var ctxTotal = document.getElementById("lead_total").getContext("2d");
        var myPieChartTotal;
        var ctxSource = document.getElementById("lead_source").getContext("2d");
        var myPieChartSource;

        $(".widget_name").on("change", function() {
            var widgetNameValue = $(this).val();
            fetch_lead_stat(currentdateRange, widgetNameValue);
        });

        function fetch_lead_stat(dateRange, widgetNameValue) {

            $.ajax({
                url: "{{ route('collectionStats') }}", // This should point to your PHP file
                type: "GET",
                data: {
                    dateRange: dateRange,
                    widgetNameValue: widgetNameValue
                },
                dataType: "json",
                success: function(response) {

                    // entity-wise financial summary report Bar Chart
                    if (response.transactions_count && Object.keys(response.transactions_count).length > 0) {
                        if (window.myPieChartTotal) {
                            window.myPieChartTotal.destroy();
                        }

                        const dates = Object.keys(response.transactions_count);
                        const entityTypes = ["ISBMU", "ISBM", "ISTM"];

                        // Extract data for each entity
                        const datasets = entityTypes.map(entity => ({
                            label: entity,
                            data: dates.map(date => response.transactions_count[date][entity] || 0),
                            backgroundColor: entity === "ISBMU" ? 'rgba(255, 99, 132, 0.5)' : entity === "ISBM" ? 'rgba(54, 162, 235, 0.5)' : 'rgba(255, 206, 86, 0.5)',
                            borderColor: entity === "ISBMU" ? 'rgba(255, 99, 132, 1)' : entity === "ISBM" ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        }));

                        window.myPieChartTotal = new Chart(ctxTotal, {
                            type: "bar",
                            data: {
                                labels: dates, // Dates as X-axis labels
                                datasets: datasets // Multiple datasets for 3 entities
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true
                                    }, // Stack bars together
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        formatter: (value) => value,
                                        color: 'black'
                                    }
                                }
                            },
                            plugins: [ChartDataLabels] // Register the plugin
                        });
                    }

                    // Transaction and Entity-wise Financial Summary Report
                    if (response.entity_wise_count && Object.keys(response.entity_wise_count).length > 0) {
                        if (window.myPieChartSource) {
                            window.myPieChartSource.destroy();
                        }

                        const dates = Object.keys(response.entity_wise_count);
                        const entityTypes = ["Receipt", "Settled", "Pending"];

                        // Extract data for each entity
                        const datasets = entityTypes.map(entity => ({
                            label: entity.toUpperCase(),
                            data: dates.map(date => response.entity_wise_count[date][entity] || 0),
                            backgroundColor: entity === "Receipt" ? 'rgba(255, 99, 132, 0.5)' : entity === "Settled" ? 'rgba(54, 162, 235, 0.5)' : 'rgba(255, 206, 86, 0.5)',
                            borderColor: entity === "Receipt" ? 'rgba(255, 99, 132, 1)' : entity === "Settled" ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        }));

                        window.myPieChartSource = new Chart(ctxSource, {
                            type: "bar",
                            data: {
                                labels: dates, // Dates as X-axis labels
                                datasets: datasets // Multiple datasets for 3 entities
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true,
                                        ticks: {
                                            callback: function(value, index, values) {
                                                return this.getLabelForValue(value).toUpperCase(); // Convert X-axis labels to uppercase
                                            }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        formatter: (value) => value,
                                        color: 'black'
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });
                    }


                    $('.counsellor-collection').empty();
                    // entity-wise financial summary report Bar Chart
                    if (response.counselor_wise_count && response.counselor_wise_count.length > 0) {
                        response.counselor_wise_count.forEach(({
                            counselor,
                            gender,
                            profile_picture,
                            branch,
                            zone,
                            total_receipt
                        }, index) => {
                            var counselorName = counselor.split("*");

                            // Check if profile_picture is empty or null
                            let profilePicture = profile_picture && profile_picture.trim() !== "" ? profile_picture :
                                (gender === "Female" ? 'user-2.jpg' : '7309691.jpg');

                            // Assign class based on index (looping every 5)
                            let classList = ["error-gt", "success-gt", "warning-gt", "secondary-gt", "primary-gt"];
                            let assignedClass = classList[index % 5]; // Loops through classes every 5 items

                            let rankclassList = ["error-bg", "success-bg", "warning-bg", "secondary-bg", "primary-bg"];
                            let assignedrankClass = rankclassList[index % 5]; // Loops through classes every 5 items

                            var dnoneClass = "";
                            if (index !== 0) {
                                dnoneClass = "d-none";
                            }
                            $('.counsellor-collection').append(`
                                        <div class="col">
                                            <div class="card h-100 ${assignedClass} p-3 text-center position-relative">
                                                <img src="dbFiles/profile_picture/${profilePicture}" class="card-img-top mt-3" alt="Counselor Image">
                                                <div class="card-body">
                                                    <p class="card-text">${total_receipt} INR</p>
                                                    <h5 class="card-title">${counselorName[1]}</h5>
                                                    <p><small>${branch}</small> | <small>${zone}</small></p>
                                                    <button type="button" class="btn btn-sm bg-white btn-fw counselorInfo" data-id="${counselor}#${dateRange}"> View Details </button>
                                                </div>
                                            </div>
                                            <div class="counsellor-rank ${assignedrankClass}"><p>${index+1}</p></div>
                                            <div class="crown-img ${dnoneClass}"><img src="assets/images/crown.png"/></div>
                                        </div>
                                    `);
                        });
                    }

                    // entity-wise financial summary report Bar Chart
                    if (response.counselor_entity_wise_count) {
                        var tableBody = $("#statTableBody1");

                        tableBody.empty(); // Clear existing data
                        var sortedData = Object.entries(response.counselor_entity_wise_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            var counsellor = row[0];
                            var count = row[1];
                            var tableRow = `<tr>
                                                    <td>${counsellor}</td>
                                                    <td>${count['branch']}</td>
                                                    <td>${count['zone']}</td>
                                                    <td>${count['record_date']}</td>
                                                    <td>${count['entity']}</td>
                                                    <td>${count['Isbmfc001 Pending']}</td>
                                                    <td>${count['Isbmfc001 Receipt']}</td>
                                                    <td>${count['Isbmfc001 Settled']}</td>
                                                    <td>${count['Isbmoc004 Pending']}</td>
                                                    <td>${count['Isbmoc004 Receipt']}</td>
                                                    <td>${count['Isbmoc004 Settled']}</td>
                                                    <td>${count['Isbmpc006 Pending']}</td>
                                                    <td>${count['Isbmpc006 Receipt']}</td>
                                                    <td>${count['Isbmpc006 Settled']}</td>
                                                    <td>${count['Isbmpdc002 Pending']}</td>
                                                    <td>${count['Isbmpdc002 Receipt']}</td>
                                                    <td>${count['Isbmpdc002 Settled']}</td>
                                                    <td>${count['Isbmpdcfc003 Pending']}</td>
                                                    <td>${count['Isbmpdcfc003 Receipt']}</td>
                                                    <td>${count['Isbmpdcfc003 Settled']}</td>
                                                    <td>${count['Isbmrc005 Pending']}</td>
                                                    <td>${count['Isbmrc005 Receipt']}</td>
                                                    <td>${count['Isbmrc005 Settled']}</td>
                                                    <td>${count['Total Pending']}</td>
                                                    <td>${count['Total Receipt']}</td>
                                                    <td>${count['Total Settled']}</td>
                                                    <td>${count['Admissions']}</td>
                                                    <td>${count['PR Sales']}</td>
                                                    <td>${count['SR Sales']}</td>
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

                    if ($('.counsellor-collection').hasClass('slick-initialized')) {
                        $('.counsellor-collection').slick('unslick'); // Destroy existing Slick instance
                    }
                    $('.counsellor-collection').slick({
                        dots: false,
                        infinite: false,
                        arrows: false,
                        speed: 300,
                        autoplay: true,
                        slidesToShow: 5,
                        slidesToScroll: 2,
                        responsive: [{
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3,
                                    infinite: true,
                                    dots: true
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                },
            });
        }

        $(document).on("click", ".counselorInfo", function() {
            $("#counselorInfo").modal("toggle");
            var dataId = $(this).attr("data-id");
            var data = dataId.split("#");

            $('.counselorName').text(data[0]);
            $.ajax({
                url: "{{ route('collectionStats') }}", // This should point to your PHP file
                type: "GET",
                data: {
                    dateRange: data[1],
                    counselor: data[0]
                },
                dataType: "json",
                success: function(response) {
                    $('.counselorInfoDiv').empty();

                    if (response.entity_wise_count && Object.keys(response.entity_wise_count).length > 0) {
                        var sortedData = Object.entries(response.entity_wise_count)
                            .sort((a, b) => a[0].localeCompare(b[0]));

                        $.each(sortedData, function(index, row) {
                            // Append the entity container and store reference to it
                            var entityContainer = $(`
                                    <div class="entity-section">
                                        <h6 class="entityName"><b>Entity Name: ${row[0]}</b></h6>
                                        <div class="row m-0 collection-info"></div>
                                    </div>
                                `);

                            // Append it first to avoid duplication issues
                            $('.counselorInfoDiv').append(entityContainer);

                            // Get reference to the newly added .collection-info within this entity
                            var collectionInfoDiv = entityContainer.find('.collection-info');

                            // Loop through the financial metrics and values
                            $.each(row[1], function(metric, value) {
                                // Append to the specific entity's collection-info
                                collectionInfoDiv.append(`
                                        <div class="col-6 ">
                                        <p class="card-text mb-0 text-secondary">${metric}:</p>
                                        <h5 class="card-title">${value}</h5>
                                        </div>
                                    `);
                            });
                        });
                    }

                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                },
            });
        });
    });
</script>
@endsection
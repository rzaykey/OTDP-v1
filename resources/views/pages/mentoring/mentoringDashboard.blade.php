<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mentoring Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        body {
            background-color: #111;
            /* color: #fff; */
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .card {
            padding: 10px;
            background-color: #111;
            border: 1px solid #ddd;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filters {
            margin-top: 20px;
        }

        .filters .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .dashboard {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .dashboard .card {
            flex: 1 1 300px;
        }

        h1 {
            margin-bottom: 30px;
        }

        .scrollable-table-container {
            height: 723px;
            flex: 1 1 auto;
            /* Allow it to grow and shrink */
            overflow-y: auto;
            background-color: #111;
            border: 1px solid white;
            padding: 10px;
            border-radius: 6px;
            min-height: 200px;
            /* Optional minimum height */
        }

        #data-table {
            width: 100%;
            border-collapse: collapse;
        }

        #data-table th,
        #data-table td {
            border: 1px solid #ddd;
            border-color: white;
            padding: 8px;
            font-size: 0.85rem;
            text-align: center;
        }

        #data-table th {
            background-color: #111;
            font-weight: bold;
        }

        th,
        td {

            padding: 8px;
        }

        h6 {
            margin-bottom: 10px;
            font-size: 0.9em;
            text-align: center;
        }

        .white-border td {
            border: 1px solid white;
        }
    </style>
</head>

<body>
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="row"></div>
    <div style="background-color: red">
        <h3>Mentoring Dashboard</h3>
    </div>

    <!-- Filters Section -->
    <div class="row g-3">
        <!-- LEFT SIDE -->
        <div class="col-md-7 d-flex flex-column">
            <!-- FILTERS AND SCORE -->
            <div class="row g-3">
                <!-- Filters -->
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">Site</label>
                                <select id="filterSite" class="form-select">
                                    <option value=''>All</option>
                                    <option>ACP</option>
                                    <option>BCP</option>
                                    <option>KCP</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Year</label>
                                <select id="filterYear" class="form-select">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Month</label>
                                <select id="filterMonth" class="form-select"></select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <select id="filterName" class="form-select" style="width: 100%;"></select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit Category</label>
                                <select id="filterUnit" class="form-select"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score Dashboard -->
                {{-- <div class="col-md-6">
                    <div class="dashboard d-flex gap-2 h-100">
                        <div class="card flex-fill d-flex flex-column justify-content-between">
                            <h6>KPI Average / </h6>
                            <canvas id="dataRequirementChart" style="height: 120px;"></canvas>
                        </div>
                        <div class="card flex-fill d-flex align-items-center justify-content-center">

                            <h6>AVERAGE SCORE <br />MENTORING</h6>
                            <div id="totalScore" style="font-size: 40px;">0.0</div>

                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- BAR CHARTS -->
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6>MONTH AVERAGE MOP</h6>
                        <div style="overflow-x: auto; width: 100%;">
                            <canvas id="monthlyAchChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6>GRADE DISTRIBUTION</h6>
                        <div style="overflow-x: auto; width: 100%;">
                            <canvas id="skillDevChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPEEDOMETER CHARTS -->
            <div class="row g-2 mt-2 text-white">

                <canvas id="cummulativeWeeklyChart" width="800" height="350"></canvas>

            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-5 d-flex flex-column" style="height: 100%;font-size:12px;"> <!-- Flex column layout -->
            <div class="card flex-fill d-flex flex-column"> <!-- Make card fill column height -->
                <div class="scrollable-table-container flex-fill overflow-auto">
                    <!-- Fill remaining space and allow scroll -->
                    <table id="tableDataMOPCompile">
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>JDE</th>
                                <th>Operator</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Objective</th>
                                <th>Point</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Cards -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    {{-- <script src="{{ asset('/js/mentoring.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        // FILTER FORM
        // YEAR
        const selectYear = document.getElementById("filterYear");
        const startYear = 1997;
        const currentYear = new Date().getFullYear();

        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Select Year";
        defaultOption.disabled = true;
        defaultOption.selected = true;
        selectYear.appendChild(defaultOption);

        for (let year = currentYear; year >= startYear; year--) {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            selectYear.appendChild(option);
        }

        // MONTH
        const selectMonth = document.getElementById("filterMonth");

        const months = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        // Employee
        // JavaScript (inside $(document).ready)
        $("#filterName").select2({
            placeholder: "Select Employee",
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('employee.operator') }}",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.employeeId,
                                text: item.employeeId + " - " + item.EmployeeName,
                                employeeName: item.EmployeeName,
                            };
                        }),
                    };
                },
                cache: true,
            },
            templateSelection: function(data) {
                return data.id || data.text;
            },
        });


        // Unit Category
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('unit.modelunit') }}", // Laravel route
                type: 'GET',
                success: function(data) {
                    const $select = $('#filterUnit');
                    $select.append('<option value="">All</option>');
                    data.forEach(function(item) {
                        const label = `${item.model} - ${item.class}`;
                        $select.append(`<option value="${item.id}">${label}</option>`);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading model units:', xhr.responseText);
                }
            });
        });



        // Add "All" option
        const allOption = document.createElement("option");
        allOption.value = "";
        allOption.textContent = "Select Month";
        selectMonth.appendChild(allOption);

        // Populate month options
        months.forEach((monthName, index) => {
            const option = document.createElement("option");
            option.value = index + 1; // Month number (1–12)
            option.textContent = monthName;
            selectMonth.appendChild(option);
        });

        // === Monthly ACH OFM ===
        const monthlyAchChart = new Chart(document.getElementById("monthlyAchChart"), {
            type: "bar",
            data: {
                labels: [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ],
                datasets: [{
                        label: "PLAN ACP",
                        type: "line",
                        backgroundColor: "#000000",
                        borderColor: "red",
                        borderWidth: 2,
                        data: [149, 149, 149, 149, 149, 149, 149, 149, 149, 149, 149, 149],
                        // stack: "stack1"
                    },
                    {
                        label: "ACT ACP",
                        backgroundColor: "#000000",
                        borderColor: "orange",
                        borderWidth: 2,
                        data: [131, 243, 178, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        // stack: "stack1"
                    },
                    {
                        label: "ACH ACP",
                        type: "line",
                        borderColor: "#00b050",
                        borderWidth: 2,
                        fill: false,
                        data: [88, 163, 119, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        yAxisID: "y1",
                        pointBackgroundColor: "#00b050",
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        // stacked: true,
                    },
                    y: {
                        beginAtZero: true,
                        max: 300,
                        // stacked: true,
                    },
                    y1: {
                        position: "right",
                        beginAtZero: true,
                        // max: 200,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                },
            },
        });
        // === Skill Dev OFM ===
        const skillDevChart = new Chart(document.getElementById("skillDevChart"), {
            type: 'bar',
            data: {
                labels: ['Fuel Eff', 'Miss Operation', 'Productivity', 'Safety Awareness'],
                datasets: [{
                        label: 'Avg Observe Point',
                        data: [70, 71, 72, 73],
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    },
                    {
                        label: 'Avg Mentoring Point',
                        data: [74, 75, 76, 77],
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    }
                ]
            },
            options: {
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'start',
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return value.toFixed(2);
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // CummulativeWeeklyChart
        const ctx = document.getElementById("cummulativeWeeklyChart").getContext("2d");

        const weeklyLabels = [
            "W1",
            "W2",
            "W3",
            "W4",
            "W5",
            "W6",
            "W7",
            "W8",
            "W9",
            "W10",
            "W11",
            "W12",
            "W13",
            "W14",
            "W15",
            "W16",
            "W17",
            "W18",
            "W19",
            "W20",
            "W21",
            "W22",
            "W23",
            "W24",
            "W25",
            "W26",
            "W1",
            "W2",
            "W3",
            "W4",
            "W5",
            "W6",
            "W7",
            "W8",
            "W9",
            "W10",
            "W11",
            "W12",
            "W13",
            "W14",
            "W15",
            "W16",
            "W17",
            "W18",
            "W19",
            "W20",
            "W21",
            "W22",
            "W23",
            "W24",
            "W25",
            "W26",
        ];

        const cummPlanData = [
            50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750,
            800, 850, 900, 950, 1000, 1050, 1100, 1150, 1200, 1250, 1300, 50, 100, 150,
            200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900,
            950, 1000, 1050, 1100, 1150, 1200, 1250, 1300,
        ];

        const cummActData = [
            50, 90, 130, 180, 230, 280, 330, 390, 440, 500, 560, 610, 670, 740, 790,
            820, 830, 830, 830, 830, 830, 830, 830, 830, 830, 830, 50, 90, 130, 180,
            230, 280, 330, 390, 440, 500, 560, 610, 670, 740, 790, 820, 830, 830, 830,
            830, 830, 830, 830, 830, 830, 830,
        ];

        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: weeklyLabels,
                datasets: [{
                        label: "CUMM PLAN",
                        data: cummPlanData,
                        borderColor: "#ffc000",
                        borderWidth: 2,
                        fill: false,
                        tension: 0.2,
                        pointRadius: 2,
                    },
                    {
                        label: "CUMM ACT",
                        data: cummActData,
                        borderColor: "#00b050",
                        borderWidth: 2,
                        fill: false,
                        tension: 0.2,
                        pointRadius: 2,
                    },
                ],
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: "CUMMULATIVE WEEKLY OFM",
                        color: "#fff",
                        font: {
                            size: 18,
                            weight: "bold",
                            family: "Arial",
                        },
                    },
                    legend: {
                        labels: {
                            color: "#fff",
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: "#fff",
                        },
                        grid: {
                            color: "#666",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        max: 1400,
                        ticks: {
                            color: "#fff",
                        },
                        grid: {
                            color: "#666",
                        },
                    },
                },
                layout: {
                    padding: 20,
                },
            },
        });

        // Table In Dashboard
        $(document).ready(function() {
            function fetchTableData() {
                $.ajax({
                    url: "{{ route('MentoringDashboard_Table') }}",
                    type: "GET",
                    data: {
                        site: $('#filterSite').val(),
                        year: $('#filterYear').val(),
                        month: $('#filterMonth').val(),
                        name: $('#filterName').val(),
                        unit: $('#filterUnit').val(),
                    },
                    success: function(data) {
                        const tbody = $("#tableDataMOPCompile tbody");
                        tbody.empty(); // Clear table
                        if (data.length === 0) {
                            tbody.append(
                                '<tr><td colspan="7" class="text-center">No data available</td></tr>'
                            );
                        } else {
                            data.forEach(item => {
                                tbody.append(`
                            <tr>
                                <td>${item.site}</td>
                                <td>${item.operator_jde}</td>
                                <td>${item.operator_name}</td>
                                <td>${item.unit_type}</td>
                                <td>${item.unit_model}</td>
                                <td>${item.indicator}</td>
                                <td>${item.point}</td>
                            </tr>
                        `);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching table data:", xhr);
                    }
                });
            }

            // Call once on page load
            fetchTableData();

            // Bind change event on filters
            $('#filterSite, #filterYear, #filterMonth, #filterName, #filterUnit').on('change', fetchTableData);
        });
    </script>

</body>

</html>

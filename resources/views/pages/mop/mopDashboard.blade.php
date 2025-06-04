<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MOP Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />

    <style>
        body {
            background-color: #111;
            color: #fff;
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
        <h3>MOP Dashboard</h3>
    </div>
    <!-- Filters Section -->
    <div class="row g-3">
        <!-- LEFT SIDE -->
        <div class="col-md-8 d-flex flex-column">
            <!-- FILTERS AND SCORE -->
            <div class="row g-3">
                <!-- Filters -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Site</label>
                                <select id="filterSite" class="form-select">
                                    <option value=''>All</option>
                                    <option>ACP</option>
                                    <option>BCP</option>
                                    <option>KCP</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year</label>
                                <select id="filterYear" class="form-select"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Month</label>
                                <select id="filterMonth" class="form-select">
                                    <option value="">All</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-8">
                                <label class="form-label">Name</label>
                                <select id="filterName" class="form-select js-operator-select"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Unit Category</label>
                                <select id="filterUnit" class="form-select"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score Dashboard -->
                <div class="col-md-6">
                    <div class="dashboard d-flex gap-2 h-100">
                        <div class="card flex-fill d-flex flex-column justify-content-between">
                            <h6>DATA REQUIREMENT</h6>
                            <canvas id="dataRequirementChart" style="height: 120px;"></canvas>
                        </div>
                        <div class="card flex-fill d-flex align-items-center justify-content-center">
                            <div>
                                <h6>TOTAL SCORE</h6>
                                <div id="totalScore" style="font-size: 40px;">0.0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAR CHARTS -->
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6>MONTH AVERAGE MOP</h6>
                        <canvas id="kualifikasiChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6>GRADE DISTRIBUTION</h6>
                        <div style="overflow-x: auto; width: 100%;">
                            <div style="width: 900px;"> <!-- Force wider canvas -->
                                <canvas id="gradeDistributionChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPEEDOMETER CHARTS -->
            <div class="row g-2 mt-2 text-white">
                <div class="col-6 col-md-2">
                    <div class="card h-100">
                        <h6>ATTENDANCE RATIO</h6>
                        <canvas id="speedometerChart1"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card h-100">
                        <h6>DISCIPLINE<br /><br /></h6>
                        <canvas id="speedometerChart2"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card h-100">
                        <h6>INCIDENT<br /> <br /></h6>
                        <canvas id="speedometerChart3"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card h-100">
                        <h6>WORKING HOUR<br /> <br /></h6>
                        <canvas id="speedometerChart4"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card h-100">
                        <h6>PRODUCTIVITY<br /> <br /></h6>
                        <canvas id="speedometerChart5"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card h-100" style="text-align: center">
                        <h6>TOP PLAYER</h6>
                        <br /> <br />
                        <h3>Didi</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-4 d-flex flex-column" style="height: 100%;"> <!-- Flex column layout -->
            <div class="card flex-fill d-flex flex-column"> <!-- Make card fill column height -->
                <div class="scrollable-table-container flex-fill overflow-auto">
                    <!-- Fill remaining space and allow scroll -->
                    <table id="tableDataMOPCompile">
                        <thead>
                            <tr>
                                <th>Operator</th>
                                <th>Unit Type</th>
                                <th>Point</th>
                                <th>Grade</th>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="{{ asset('/js/OTPD/mopDashboard.js') }}"></script>


</body>

</html>

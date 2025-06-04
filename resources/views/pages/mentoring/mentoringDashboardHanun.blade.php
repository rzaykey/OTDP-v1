@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @media (max-width: 768px) {
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
            }

            th,
            td {
                white-space: nowrap;
            }
        }
    </style>
@endpush

@push('style')
    <style>
        body {
            /* background-color: #111; */
            color: #000000;
            font-family: Arial, sans-serif;
            /* padding: 20px; */
        }

        .card {
            padding: 10px;
            /* background-color: #111; */
            border: 1px solid #ddd;
            /* color: #fff; */
            /* color: #000000; */
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            align-content: center;
            display: flex;
            justify-content: center;
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
            /* color: #ffffff; */
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
            /* background-color: #111; */
            border: 1px solid white;
            padding: 10px;
            border-radius: 6px;
            min-height: 200px;
            /* Optional minimum height */
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
@endpush

@section('content')
    <div class="row"></div>
    <div style="background-color: red" class="mb-3">
        <h3 class="p-1" style="color: #ffffff">Mentoring Dashboard</h3>
    </div>

    {{--
    <div class="row g-2 mb-2">
        <div class="col-md-6">
            <!-- Filters -->
            <div class="card">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Site</label>
                        <select id="filterSite" class="form-select"></select>
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
                    <div class="col-md-4">
                        <label class="form-label">Year</label>
                        <select id="filterYear" class="form-select"></select>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-8">
                        <label class="form-label">Name</label>
                        <select id="filterName" class="form-select"></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit Type</label>
                        <select id="filterUnit" class="form-select"></select>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- LEFT SIDE -->
    <div class="row g-2">
        <!-- LEFT SIDE -->
        <div class="col-md-8 d-flex flex-column">
            <!-- Filters -->
            <div class="card mb-2">
                <div class="row g-2 p-2">
                    <div class="col-md-2">
                        <label class="form-label">Site</label>
                        <select id="filterSite" class="form-select"></select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Month</label>
                        <select id="filterMonth" class="form-select"></select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <select id="filterYear" class="form-select"></select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Type</label>
                        <select id="filterUnit" class="form-select"></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <select id="filterName" class="form-select"></select>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6 class="text-center mt-2">MONTHLY ACH OFM</h6>
                        <div style="overflow-x: auto; width: 100%;">
                            <div style="width: 500px;">
                                <canvas id="monthlyACHChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <h6 class="text-center mt-2">SKILL DEV OFM</h6>
                        <div style="overflow-x: auto; width: 100%;">
                            <div style="width: 500px;">
                                <canvas id="skillChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cumulative Chart -->
            <div class="row g-2">
                <div class="col-md-12">
                    <div class="card mt-2">
                        <h6 class="text-center mt-2">CUMULATIVE WEEKLY OFM</h6>
                        <canvas id="cumulativeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-4 d-flex flex-column">
            <div class="card h-100 d-flex flex-column flex-fill">
                <h6 class="text-center mt-2">OPERATOR DATA</h6>
                <div class="scrollable-table-container flex-fill overflow-auto p-2">
                    <table id="tableDataMOPCompile" class="table" style="text-align: center;">
                        <thead>
                            <tr class="table-secondary">
                                <th>Site</th>
                                <th>NIK</th>
                                <th>Operator</th>
                                <th>Type Unit</th>
                                <th>Unit <br /> Categories</th>
                                <th>Improvement <br /> Objective</th>
                                <th>Observe <br /> Point</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
@endsection

@push('plugin-scripts')
    <!-- Scripts -->
    <script src="{{ asset('/js/OTPD/mentoring.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
@endpush

@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        tr th {
            vertical-align: middle;
            /* Align cell content vertically */
            text-align: center;
            /* Align cell content horizontally */
        }

        @media (max-width: 768px) {
    .table-responsive table {
        width: 100%;
        min-width: 900px; /* Ensures the table doesn't shrink too much */
    }

    .btn {
        width: 100%; /* Full-width buttons on smaller screens */
        margin-bottom: 5px; /* Add spacing between buttons */
    }

    .modal-dialog {
        max-width: 90%; /* Reduce modal size for mobile devices */
    }

    .form-control-sm {
        font-size: 0.875rem; /* Smaller text in input fields for better spacing */
    }

    .icon-img {
        display: none; /* Optional: Hide icons if they clutter the UI */
    }
}
    </style>
@endpush

@section('content')

    <ul class="container nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active"href="#" role="tab">Persebaran KPI</a>
        </li>
        {{-- <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportGradeMOP')}}" role="tab">Grade Distribusi</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportMOP24')}}" role="tab">MOP 24 Bulan</a>
        </li> --}}
    </ul>

    @php
        $months = [
            '01' => 'JANUARY', '02' => 'FEBRUARY', '03' => 'MARCH',
            '04' => 'APRIL', '05' => 'MAY', '06' => 'JUNE',
            '07' => 'JULY', '08' => 'AUGUST', '09' => 'SEPTEMBER',
            '10' => 'OCTOBER', '11' => 'NOVEMBER', '12' => 'DECEMBER'
        ];
    @endphp

    <div class="card container py-3 px-2 shadow-sm">
        <div class="card-body">
            <div class="tab-pane" id="mop-bulanan" role="tabpanel">
                <div class="row">
                    <form action="{{ Route('DayKPISearch') }}" method="POST" class="col-md-5 col-sm-12">
                        @csrf
                        <table class="w-100">
                            <tr>
                                <td>WEEK</td>
                                <td>:</td>
                                <td>
                                    <select name="Week" id="idWeek" class="form-select">
                                        <option value="">All</option>
                                        @foreach ( $Week as $Week2 )
                                            <option value="{{ $Week2->week }}" {{ request('Week') == $Week2->week ? 'selected' : '' }}>{{ trim($Week2->week) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>MONTH</td>
                                <td>:</td>
                                <td>
                                    <select name="Month" id="idMonth" class="form-select">
                                        <option value="">All</option>
                                        @foreach ($months as $key => $value)
                                            <option value="{{ $key }}" {{ request('Month') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>YEAR</td>
                                <td>:</td>
                                <td>
                                    <select name="Year" id="idYear" class="form-select">
                                        <option value="">All</option>
                                        @foreach ( $Year as $Year2 )
                                            <option value="{{ $Year2->year }}" {{ request('Year') == $Year2->year ? 'selected' : '' }}>{{ trim($Year2->year) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="mt-2 text-end">
                            <button type="submit" class="btn btn-outline-success btn-sm">Search</button>
                        </div>
                    </form>
                </div>

                <div class="row mt-5">
                    <div class="col">
                        <div class="mb-3">
                            <h4 class="text-center">Persebaran Aktivitas Berdasarkan KPI</h4>
                            <h4 class="text-center ">
                                @if(request('Week')) Week {{ request('Week') }}@endif
                                @if(request('Month')) {{ $months[request('Month')] ?? request('Month') }}@endif
                                @if(request('Year')) {{ request('Year') }}@endif
                            </h4>
                        </div>

                        <canvas id="chartjsGroupedBar" class="w-100" style="max-height: 300px; min-height: 100px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="text-center">Persentase Distribusi KPI OTPD ACP</h4>
                        <h4 class="text-center ">
                            @if(request('Week')) Week {{ request('Week') }}@endif
                            @if(request('Month')) {{ $months[request('Month')] ?? request('Month') }}@endif
                            @if(request('Year')) {{ request('Year') }}@endif
                        </h4>
                    </div>

                    <canvas id="chartjsBarACP"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="text-center">Persentase Distribusi KPI OTPD BCP</h4>
                        <h4 class="text-center ">
                            @if(request('Week')) Week {{ request('Week') }}@endif
                            @if(request('Month')) {{ $months[request('Month')] ?? request('Month') }}@endif
                            @if(request('Year')) {{ request('Year') }}@endif
                        </h4>
                    </div>
                    <canvas id="chartjsBarBCP"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="text-center">Persentase Distribusi KPI OTPD KCP</h4>
                        <h4 class="text-center ">
                            @if(request('Week')) Week {{ request('Week') }}@endif
                            @if(request('Month')) {{ $months[request('Month')] ?? request('Month') }}@endif
                            @if(request('Year')) {{ request('Year') }}@endif
                        </h4>
                    </div>
                    <canvas id="chartjsBarKCP"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        const weekSelect = document.getElementById('idWeek');
        const monthSelect = document.getElementById('idMonth');

        function handleFilterRules() {
        if (weekSelect.value !== "") {
            // Disable Month if Week is selected
            monthSelect.disabled = true;
        } else {
            monthSelect.disabled = false;
        }

        if (monthSelect.value !== "") {
            // Disable Week if Month is selected
            weekSelect.disabled = true;
        } else {
            weekSelect.disabled = false;
        }
        }

        handleFilterRules();

        weekSelect.addEventListener('change', handleFilterRules);
        monthSelect.addEventListener('change', handleFilterRules);
    </script>

    <script>
        $(function () {
            'use strict';

            var colors = {
                primary: "#6571ff",
                secondary: "#7987a1",
                warning: "#fbbc06",
                success: "#05a34a",
                info: "#66d1d1",
                danger: "#ff3366",
                light: "#e9ecef",
                dark: "#060c17",
                muted: "#7987a1",
                gridBorder: "rgba(77, 138, 240, .15)",
                bodyColor: "#000",
                cardBg: "#fff"
            };

            const fontFamily = "'Roboto', Helvetica, sans-serif";

            const chartLabels = @json($ChartLabels);
            const rawDatasets = @json($ChartDatasets);

            const fixedColors = [colors.danger, colors.primary, colors.warning, colors.info];

            const chartDatasets = rawDatasets.map((dataset, index) => {
                return {
                label: dataset.label,
                backgroundColor: fixedColors[index % fixedColors.length],
                data: dataset.data
                };
            });

            if($('#chartjsGroupedBar').length) {
                new Chart($('#chartjsGroupedBar'), {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: chartDatasets
                },
                options: {
                    plugins: {
                    legend: {
                        display: true,
                        labels: {
                        color: colors.bodyColor,
                        font: {
                            size: window.innerWidth < 576 ? 8 : 12,
                            family: fontFamily
                        }
                        }
                    },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                            display: true,
                            color: colors.gridBorder,
                            borderColor: colors.gridBorder,
                            },
                            ticks: {
                            color: colors.bodyColor,
                            font: {
                                size: window.innerWidth < 576 ? 6 : 12
                            }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                            display: true,
                            color: colors.gridBorder,
                            borderColor: colors.gridBorder,
                            },
                            ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            },
                            color: colors.bodyColor,
                            font: {
                                size: window.innerWidth < 576 ? 8 : 12
                            }
                            }
                        }
                    }
                }
                });
            }

            const pieData = @json($PieData);
            const pieColors = [colors.primary, colors.danger, colors.info, colors.success, colors.warning];

            function renderPieChart(canvasId, siteKey) {
                if ($('#' + canvasId).length && pieData[siteKey]) {
                    const chartData = pieData[siteKey].data;
                    const total = chartData.reduce((a, b) => Number(a) + Number(b), 0);

                    new Chart($('#' + canvasId), {
                        type: 'pie',
                        data: {
                            labels: pieData[siteKey].labels,
                            datasets: [{
                                backgroundColor: pieColors,
                                borderColor: colors.cardBg,
                                data: chartData
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: colors.bodyColor,
                                        font: {
                                            size: '12px',
                                            family: fontFamily
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.raw;
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            const label = context.label || '';
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                        }
                    });
                }
            }


            renderPieChart('chartjsBarACP', 'ACP');
            renderPieChart('chartjsBarBCP', 'BCP');
            renderPieChart('chartjsBarKCP', 'KCP');
        });
    </script>

@endpush

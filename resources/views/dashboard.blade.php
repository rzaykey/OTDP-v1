@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="justify-content-between align-items-center ">
        <div class="text-center">
            <div class="row">
                <div class="row flex-grow-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6 grid-margin">
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="card-title" style="font-size: 25px">Welcome to OTPD Apps</p>
                                            <p class="fw-bolder" style="font-size: 20px;margin-bottom:5px">{{ Auth::user()->username }} - {{ Auth::user()->name }}</p>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 grid-margin stretch-card">
                                    <div class="card text-white shadow-lg"
                                        style="
                                        background: linear-gradient(135deg, #FF8C00, #fab73a);
                                        position: relative;
                                        overflow: hidden;
                                        border-radius: 12px;
                                    ">
                                        <div class="card-body" style="position: relative; z-index: 2;">
                                            <h6 class="card-title">Operator ACP</h6>
                                            <h4>286 Orang</h4>
                                        </div>

                                        <!-- Operator shadow image -->
                                        <div
                                            style="
                                            position: absolute;
                                            bottom: -10px;
                                            right: -10px;
                                            width: 80px;
                                            height: 80px;
                                            background: url('{{ asset('assets/images/icons/dashboard-driver.png') }}') no-repeat center;
                                            background-size: contain;
                                            opacity: 0.2;
                                            z-index: 1;
                                        ">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 grid-margin stretch-card">
                                    <div class="card text-white shadow-lg"
                                        style="
                                        background: linear-gradient(135deg, #FF8C00, #fab73a);
                                        position: relative;
                                        overflow: hidden;
                                        border-radius: 12px;
                                    ">
                                        <div class="card-body" style="position: relative; z-index: 2;">
                                            <h6 class="card-title">Operator BCP</h6>
                                            <h4>720 Orang</h4>
                                        </div>

                                        <!-- Operator shadow image -->
                                        <div
                                            style="
                                            position: absolute;
                                            bottom: -10px;
                                            right: -10px;
                                            width: 80px;
                                            height: 80px;
                                            background: url('{{ asset('assets/images/icons/dashboard-driver.png') }}') no-repeat center;
                                            background-size: contain;
                                            opacity: 0.2;
                                            z-index: 1;
                                        ">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 grid-margin stretch-card">
                                    <div class="card text-white shadow-lg"
                                        style="
                                        background: linear-gradient(135deg, #FF8C00, #fab73a);
                                        position: relative;
                                        overflow: hidden;
                                        border-radius: 12px;
                                    ">
                                        <div class="card-body" style="position: relative; z-index: 2;">
                                            <h6 class="card-title">Operator KCP</h6>
                                            <h4>374 Orang</h4>
                                        </div>

                                        <!-- Operator shadow image -->
                                        <div
                                            style="
                                            position: absolute;
                                            bottom: -10px;
                                            right: -10px;
                                            width: 80px;
                                            height: 80px;
                                            background: url('{{ asset('assets/images/icons/dashboard-driver.png') }}') no-repeat center;
                                            background-size: contain;
                                            opacity: 0.2;
                                            z-index: 1;
                                        ">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Dashboard Link</h6>
                                            <a class="btn btn-primary btn-sm" href="{{ route('MOPDashboard') }}" target="blank" style="margin:4px">MOP</a><br/>
                                            <a class="btn btn-primary btn-sm" href="{{ route('MentoringDashboard') }}" target="blank">Mentoring</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Doughnut chart</h6>
                                            <canvas id="equipmentChart"></canvas>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Mixed bar chart</h6>
                                            <canvas id="chartjsMixedBar"></canvas>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                 <div class="col-xl-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Bar chart</h6>
                                            <canvas id="gradeChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-xl-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Line chart</h6>
                                            <canvas id="averagePointChart"></canvas>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-xl-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Bubble chart</h6>
                                            <canvas id="chartjsBubble"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Area chart</h6>
                                            <canvas id="chartjsArea"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Radar chart</h6>
                                            <canvas id="chartjsRadar"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Polar area chart</h6>
                                            <canvas id="chartjsPolarArea"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6 grid-margin grid-margin-xl-0 stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Grouped bar chart</h6>
                                            <canvas id="chartjsGroupedBar"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Pie chart</h6>
                                            <canvas id="chartjsPie"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
    <script>
        const gradeData = {
            labels: ["SPECIAL", "BS", "B+", "C+"],
            datasets: [{
                label: 'Jumlah Karyawan',
                data: [3, 5, 5, 3],
                backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#F44336']
            }]
        };

        const avgPointData = {
            labels: ["SPECIAL", "BS", "B+", "C+"],
            datasets: [{
                label: 'Rata-rata Total Point',
                data: [4.8, 4.3, 3.9, 2.8],
                backgroundColor: '#673AB7'
            }]
        };

        const equipmentData = {
            labels: ["MH123", "SYS500", "100", "MH124", "101", "SYS501"],
            datasets: [{
                label: 'Jumlah Karyawan',
                data: [2, 9, 2, 2, 1, 1],
                backgroundColor: ['#3F51B5', '#03A9F4', '#00BCD4', '#8BC34A', '#FFC107', '#FF5722']
            }]
        };

        new Chart(document.getElementById('gradeChart'), {
            type: 'pie',
            data: gradeData
        });
        new Chart(document.getElementById('averagePointChart'), {
            type: 'bar',
            data: avgPointData
        });
        new Chart(document.getElementById('equipmentChart'), {
            type: 'doughnut',
            data: equipmentData
        });

        $(function() {
            'use strict';


            var colors = {
                primary: "#6571ff",
                secondary: "#7987a1",
                success: "#05a34a",
                info: "#66d1d1",
                warning: "#fbbc06",
                danger: "#ff3366",
                light: "#e9ecef",
                dark: "#060c17",
                muted: "#7987a1",
                gridBorder: "rgba(77, 138, 240, .15)",
                bodyColor: "#000",
                cardBg: "#fff"
            }

            var fontFamily = "'Roboto', Helvetica, sans-serif"




            // Bar chart
            if ($('#chartjsBar').length) {
                new Chart($("#chartjsBar"), {
                    type: 'bar',
                    data: {
                        labels: ["China", "America", "India", "Germany", "Oman"],
                        datasets: [{
                            label: "Population",
                            backgroundColor: [colors.primary, colors.danger, colors.warning, colors
                                .success, colors.info
                            ],
                            data: [2478, 5267, 734, 2084, 1433],
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: '#000',
                                anchor: 'end',
                                align: 'top',
                                formatter: (value) => value, // Menampilkan angka langsung
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
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
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }




            // Line Chart
            if ($('#chartjsLine').length) {
                new Chart($('#chartjsLine'), {
                    type: 'line',
                    data: {
                        labels: [1500, 1600, 1700, 1750, 1800, 1850, 1900, 1950, 1999, 2050],
                        datasets: [{
                            data: [86, 114, 106, 106, 107, 111, 133, 221, 783, 2478],
                            label: "Africa",
                            borderColor: colors.info,
                            backgroundColor: "transparent",
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }, {
                            data: [282, 350, 411, 502, 635, 809, 947, 1402, 3700, 5267],
                            label: "Asia",
                            borderColor: colors.danger,
                            backgroundColor: "transparent",
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                            datalabels: {
                                color: '#000',
                                anchor: 'end',
                                align: 'top',
                                formatter: (value) => value, // Menampilkan angka langsung
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
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
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }




            // Doughnut Chart
            if ($('#chartjsDoughnut').length) {
                new Chart($('#chartjsDoughnut'), {
                    type: 'doughnut',
                    data: {
                        labels: ["Africa", "Asia", "Europe"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: [colors.primary, colors.danger, colors.info],
                            borderColor: colors.cardBg,
                            data: [2478, 4267, 1334],
                        }]
                    },
                    options: {
                        aspectRatio: 2,
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                            datalabels: {
                                color: '#000',
                                anchor: 'end',
                                align: 'top',
                                formatter: (value) => value, // Menampilkan angka langsung
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    }
                });
            }




            // Area Chart
            if ($('#chartjsArea').length) {
                new Chart($('#chartjsArea'), {
                    type: 'line',
                    data: {
                        labels: [1500, 1600, 1700, 1750, 1800, 1850, 1900, 1950, 1999, 2050],
                        datasets: [{
                            data: [86, 114, 106, 106, 107, 111, 133, 221, 783, 2478],
                            label: "Africa",
                            borderColor: colors.danger,
                            backgroundColor: 'rgba(255,51,102,.3)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }, {
                            data: [282, 350, 411, 502, 635, 809, 947, 1402, 3700, 5267],
                            label: "Asia",
                            borderColor: colors.info,
                            backgroundColor: 'rgba(102,209,209,.3)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
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
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }




            // Pie Chart
            if ($('#chartjsPie').length) {
                new Chart($('#chartjsPie'), {
                    type: 'pie',
                    data: {
                        labels: ["Africa", "Asia", "Europe"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: [colors.primary, colors.danger, colors.info],
                            borderColor: colors.cardBg,
                            data: [2478, 4267, 1334]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                        },
                        aspectRatio: 2,
                    }
                });
            }




            // Bubble Chart
            if ($('#chartjsBubble').length) {
                new Chart($('#chartjsBubble'), {
                    type: 'bubble',
                    data: {
                        labels: "Africa",
                        datasets: [{
                            label: ["China"],
                            backgroundColor: 'rgba(102,209,209,.3)',
                            borderColor: colors.info,
                            data: [{
                                x: 21269017,
                                y: 5.245,
                                r: 15
                            }]
                        }, {
                            label: ["Denmark"],
                            backgroundColor: "rgba(255,51,102,.3)",
                            borderColor: colors.danger,
                            data: [{
                                x: 258702,
                                y: 7.526,
                                r: 10
                            }]
                        }, {
                            label: ["Germany"],
                            backgroundColor: "rgba(101,113,255,.3)",
                            borderColor: colors.primary,
                            data: [{
                                x: 3979083,
                                y: 6.994,
                                r: 15
                            }]
                        }, {
                            label: ["Japan"],
                            backgroundColor: "rgba(251,188,6,.3)",
                            borderColor: colors.warning,
                            data: [{
                                x: 4931877,
                                y: 5.921,
                                r: 15
                            }]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: "GDP (PPP)"
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: "Happiness"
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                        }
                    }
                });
            }




            // Radar Chart
            if ($('#chartjsRadar').length) {
                new Chart($('#chartjsRadar'), {
                    type: 'radar',
                    data: {
                        labels: ["Africa", "Asia", "Europe", "Latin America", "North America"],
                        datasets: [{
                            label: "1950",
                            fill: true,
                            backgroundColor: "rgba(255,51,102,.3)",
                            borderColor: colors.danger,
                            pointBorderColor: colors.danger,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            data: [8.77, 55.61, 21.69, 6.62, 6.82]
                        }, {
                            label: "2050",
                            fill: true,
                            backgroundColor: "rgba(102,209,209,.3)",
                            borderColor: colors.info,
                            pointBorderColor: colors.info,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            data: [25.48, 54.16, 7.61, 8.06, 4.45]
                        }]
                    },
                    options: {
                        aspectRatio: 2,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true,
                                    color: colors.gridBorder,
                                },
                                grid: {
                                    color: colors.gridBorder
                                },
                                suggestedMin: 0,
                                suggestedMax: 60,
                                ticks: {
                                    backdropColor: colors.cardBg,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 11,
                                        family: fontFamily
                                    }
                                },
                                pointLabels: {
                                    color: colors.bodyColor,
                                    font: {
                                        color: colors.bodyColor,
                                        family: fontFamily,
                                        size: '13px'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                        },
                    }
                });
            }




            // Polar Area Chart
            if ($('#chartjsPolarArea').length) {
                new Chart($('#chartjsPolarArea'), {
                    type: 'polarArea',
                    data: {
                        labels: ["Africa", "Asia", "Europe", "Latin America"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: [colors.primary, colors.danger, colors.success, colors
                                .info
                            ],
                            borderColor: colors.cardBg,
                            data: [3578, 5000, 1034, 2034]
                        }]
                    },
                    options: {
                        aspectRatio: 2,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true,
                                    color: colors.gridBorder,
                                },
                                grid: {
                                    color: colors.gridBorder
                                },
                                suggestedMin: 1000,
                                suggestedMax: 5500,
                                ticks: {
                                    backdropColor: colors.cardBg,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 11,
                                        family: fontFamily
                                    }
                                },
                                pointLabels: {
                                    color: colors.bodyColor,
                                    font: {
                                        color: colors.bodyColor,
                                        family: fontFamily,
                                        size: '13px'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
                                        family: fontFamily
                                    }
                                }
                            },
                        },
                    }
                });
            }




            // Grouped Bar Chart
            if ($('#chartjsGroupedBar').length) {
                new Chart($('#chartjsGroupedBar'), {
                    type: 'bar',
                    data: {
                        labels: ["1900", "1950", "1999", "2050"],
                        datasets: [{
                            label: "Africa",
                            backgroundColor: colors.danger,
                            data: [133, 221, 783, 2478]
                        }, {
                            label: "Europe",
                            backgroundColor: colors.primary,
                            data: [408, 547, 675, 734]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
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
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }




            // Mixed Line Bar Chart
            if ($('#chartjsMixedBar').length) {
                new Chart($('#chartjsMixedBar'), {
                    type: 'bar',
                    data: {
                        labels: ["1900", "1950", "1999", "2050"],
                        datasets: [{
                            label: "Europe",
                            type: "line",
                            borderColor: colors.danger,
                            backgroundColor: "transparent",
                            data: [408, 547, 675, 734],
                            fill: false,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }, {
                            label: "Africa",
                            type: "line",
                            borderColor: colors.primary,
                            backgroundColor: "transparent",
                            data: [133, 221, 783, 2478],
                            fill: false,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3,
                            tension: .3
                        }, {
                            label: "Europe",
                            type: "bar",
                            backgroundColor: colors.danger,
                            data: [408, 547, 675, 734],
                        }, {
                            label: "Africa",
                            type: "bar",
                            backgroundColor: colors.primary,
                            data: [133, 221, 783, 2478]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: '13px',
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
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>
@endpush

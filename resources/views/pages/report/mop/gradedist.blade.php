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
            <a class="nav-link" href="{{route('ReportMOP')}}" role="tab">MOP Bulanan</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="#grade" role="tab">Grade Distribusi</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportMOP24')}}" role="tab">MOP 24 Bulan</a>
        </li>
    </ul>

    @php
        $selectedMonth = request('MonthGrade');
        $selectedYear = request('YearGrade');
        $months = [
            '1' => 'JANUARY', '2' => 'FEBRUARY', '3' => 'MARCH',
            '4' => 'APRIL', '5' => 'MAY', '6' => 'JUNE',
            '7' => 'JULY', '8' => 'AUGUST', '9' => 'SEPTEMBER',
            '10' => 'OCTOBER', '11' => 'NOVEMBER', '12' => 'DECEMBER'
        ];
    @endphp

    <div class="card container py-3 px-2 shadow-sm">
        <div class="card-body">
            <div class="mt-3" id="myTabContent">

                {{-- grade distribusi --}}
                <div id="grade" role="tabpanel">
                    <div class="row">
                        <form action="{{ Route('MOPGradeDistribution') }}" method="POST" class="col-md-4 col-sm-12">
                            @csrf
                            <table class="w-100">
                                <tr>
                                    <td>MONTH</td>
                                    <td>:</td>
                                    <td>
                                        <select name="MonthGrade" id="idMonthGrade" class="form-select" required>
                                            <option value="">Select</option>
                                            @foreach ($months as $key => $value)
                                                <option value="{{ $key }}" {{ request('MonthGrade') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>YEAR</td>
                                    <td>:</td>
                                    <td>
                                        <select name="YearGrade" id="idYearGrade" class="form-select" required>
                                            <option value="">Select</option>
                                            @foreach ( $Year as $Year2 )
                                                <option value="{{ $Year2->year }}" {{ request('YearGrade') == $Year2->year ? 'selected' : '' }}>{{ trim($Year2->year) }}</option>
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
                </div>

            </div>
        </div>
    </div>
    <div class="card container py-3 px-2 shadow-sm mt-3" style="display:{{ request()->is('report/mop-grade') ? 'none' : 'block' }}">
        <div class="card-body">
            <div class="mb-3">
                <h4 class="text-center">GRADE DISTRIBUSI</h4>
                @if($selectedMonth && $selectedYear)
                    <h4 class="text-center">PERIODE : {{ $months[$selectedMonth] ?? '' }} {{ $selectedYear }}</h4>
                @endif
            </div>
            <div >
                <div class="row align-items-stretch">

                    <div class="col-md-8 col-sm-12 py-1">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="chartjsBar"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 py-1">
                        <table id="GradeTable" class="table table-sm table-bordered align-content-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-sm px-2">Grade MOP</th>
                                    <th class="text-center text-sm px-2">Distribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Data as $grade => $count)
                                    <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                        <td class="text-center">{{ $grade }}</td>
                                        <td class="text-center">{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script>
        $(function() {
          'use strict';

          var colors = {
            primary        : "#6571ff",
            secondary      : "#7987a1",
            success        : "#05a34a",
            info           : "#66d1d1",
            warning        : "#fbbc06",
            danger         : "#ff3366",
            light          : "#e9ecef",
            dark           : "#060c17",
            muted          : "#7987a1",
            gridBorder     : "rgba(77, 138, 240, .15)",
            bodyColor      : "#000",
            cardBg         : "#fff"
          }

          var fontFamily = "'Roboto', Helvetica, sans-serif"

          const gradeLabels = {!! json_encode(array_keys($Data)) !!};
          const gradeCounts = {!! json_encode(array_values($Data)) !!};

          // Bar chart
          if($('#chartjsBar').length) {
            new Chart($("#chartjsBar"), {
              type: 'bar',
              data: {
                labels: gradeLabels,
                datasets: [
                  {
                    label: "Distribusi",
                    backgroundColor: [colors.primary, colors.danger, colors.warning, colors.success, colors.info],
                    data: gradeCounts,
                  }
                ]
              },
              options: {
                plugins: {
                  legend: { display: false },
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
                        size: 10
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
                        beginAtZero: true,
                        stepSize: 1,
                        precision: 0,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        },
                        color: colors.bodyColor,
                        font: {
                            size: 10
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

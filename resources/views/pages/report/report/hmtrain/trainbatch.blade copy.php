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
            <a class="nav-link active"href="#" role="tab">HM Train Hours</a>
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

    {{-- mop bulanan --}}
    <div class="card container py-3 px-2 shadow-sm">
        <div class="card-body">
            <div class="tab-pane" id="mop-bulanan" role="tabpanel">
                <div class="row">
                    <form action="{{ Route('MOPSearch') }}" method="POST" class="col-md-4 col-sm-12">
                        @csrf
                        <table class="w-100">
                            <tr>
                                <td>MONTH</td>
                                <td>:</td>
                                <td>
                                    <select name="Month" id="idMonth" class="form-select" required>
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
                                    <select name="Year" id="idYear" class="form-select" required>
                                        <option value="">All</option>
                                        @foreach ( $Year as $Year2 )
                                            <option value="{{ $Year2->year }}" {{ request('Year') == $Year2->year ? 'selected' : '' }}>{{ trim($Year2->year) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>SITE</td>
                                <td>:</td>
                                <td>
                                    <select name="Site" id="idSite" class="form-select" required>
                                        <option value="">All</option>
                                        @foreach ( $Site as $Site2 )
                                            <option value="{{ $Site2->site }}" {{ request('Site') == $Site2->site ? 'selected' : '' }}>{{ trim($Site2->site) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>UNIT CLASS</td>
                                <td>:</td>
                                <td>
                                    <select name="Site" id="idSite" class="form-select" required>
                                        <option value="">All</option>
                                        @foreach ( $Unit_class as $Unit_class2 )
                                            <option value="{{ $Unit_class2->unit_class }}" {{ request('Unit_class') == $Unit_class2->unit_class ? 'selected' : '' }}>{{ trim($Unit_class2->unit_class) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>TRAINING TYPE</td>
                                <td>:</td>
                                <td>
                                    <select name="Site" id="idSite" class="form-select" required>
                                        <option value="">All</option>
                                        @foreach ( $Training_type as $Training_type2 )
                                            <option value="{{ $Training_type2->training_type }}" {{ request('Training_type') == $Training_type2->training_type ? 'selected' : '' }}>{{ trim($Training_type2->training_type) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="mt-1 text-end">
                            <button type="submit" class="btn btn-outline-success btn-sm">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card container py-3 px-2 shadow-sm mt-3"
    {{-- style="display:{{ request()->is('report/hm-train-hours') ? 'none' : 'block' }}" --}}
    >
        <div class="card-body">
            <h4 class="text-center">MONTHLY OPERATOR PERFORMANCE</h4>
                {{-- @if($selectedMonth && $selectedYear)
                    <h4 class="text-center mb-2">PERIODE : {{ $months[$selectedMonth] ?? '' }} {{ $selectedYear }}</h4>
                @endif --}}

            <!-- Ensure table-responsive wrapper works -->
            <div class="table-responsive">
                <table id="MOPTable" class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-sm px-2"></th>
                            <th class="text-center text-sm px-2">Batch</th>
                            <th class="text-center text-sm px-2">Average of Plan Total HM</th>
                            <th class="text-center text-sm px-2">Average of Progress</th>
                            <th class="text-center text-sm px-2">% Progress</th>
                        </tr>

                        <!-- Filter Input Row -->
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Batch"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $batchIndex = 0; @endphp
                        @foreach ($GroupedData as $batch => $records)
                            @php
                                $batchIndex++;
                                $avgProgres = round($records->avg('progres'), 2);
                                $avgPercentage = round(($avgProgres / 56) * 100, 2);
                            @endphp
                            <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary toggle-row" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBatch{{ $batchIndex }}">
                                        +
                                    </button>
                                </td>
                                <td class="text-center">{{ $batch }}</td>
                                <td class="text-center">56</td>
                                <td class="text-center">{{ $avgProgres }}</td>
                                <td class="text-center">{{ $avgPercentage }}%</td>
                            </tr>

                            <tr class="collapse bg-light" id="collapseBatch{{ $batchIndex }}">
                                <td colspan="5" class="p-0">
                                    <table class="table table-sm mb-0">
                                        {{-- <thead class="table-secondary">
                                            <tr>
                                                <th class="text-center">Employee Name</th>
                                                <th class="text-center">Plan Total HM</th>
                                                <th class="text-center">Progress</th>
                                                <th class="text-center">% Progress</th>
                                            </tr>
                                        </thead> --}}
                                        {{-- <tbody> --}}
                                            @foreach ($records as $record)
                                                <tr>
                                                    <td class="text-center">{{ $record->employee_name }}</td>
                                                    <td class="text-center">56</td>
                                                    <td class="text-center">{{ $record->progres }}</td>
                                                    <td class="text-center">{{ round(($record->progres / 56) * 100, 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        {{-- </tbody> --}}
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
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
    <script src="{{ asset('assets/js/chartjs.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#MOPTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                orderCellsTop: true, // Enables the filter inputs under headers
                fixedHeader: true
            });

            // Apply individual column search
            $('#MOPTable thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
        });
    </script>
@endpush

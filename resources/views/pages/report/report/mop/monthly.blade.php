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
            <a class="nav-link active"href="#mop-bulanan" role="tab">MOP Bulanan</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportGradeMOP')}}" role="tab">Grade Distribusi</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportMOP24')}}" role="tab">MOP 24 Bulan</a>
        </li>
    </ul>

    @php
        $selectedMonth = request('Month');
        $selectedYear = request('Year');
        $months = [
            '1' => 'JANUARY', '2' => 'FEBRUARY', '3' => 'MARCH',
            '4' => 'APRIL', '5' => 'MAY', '6' => 'JUNE',
            '7' => 'JULY', '8' => 'AUGUST', '9' => 'SEPTEMBER',
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
                                        <option value="">Select</option>
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
                                        <option value="">Select</option>
                                        @foreach ( $Year as $Year2 )
                                            <option value="{{ $Year2->year }}" {{ request('Year') == $Year2->year ? 'selected' : '' }}>{{ trim($Year2->year) }}</option>
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

    <div class="card container py-3 px-2 shadow-sm mt-3"  style="display:{{ request()->is('report/mop') ? 'none' : 'block' }}">
        <div class="card-body">
            <h4 class="text-center">MONTHLY OPERATOR PERFORMANCE</h4>
                @if($selectedMonth && $selectedYear)
                    <h4 class="text-center mb-2">PERIODE : {{ $months[$selectedMonth] ?? '' }} {{ $selectedYear }}</h4>
                @endif

            <!-- Ensure table-responsive wrapper works -->
            <div class="table-responsive">

                <table id="MOPTable" class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-sm px-2">No</th>
                            <th class="text-center text-sm px-2">JDE</th>
                            <th class="text-right text-sm px-4 font-medium">Nama</th>
                            <th class="text-center text-sm px-2">Poin Alat Produksi</th>
                            <th class="text-center text-sm px-2">Poin Alat Support</th>
                            <th class="text-center text-sm px-2">MOP Final Score</th>
                            <th class="text-center text-sm px-2">MOP Grade</th>
                        </tr>

                        <!-- Filter Input Row -->
                        <tr>
                            <th><input type="text" class="form-control form-control-sm" style="width:4em"
                                    placeholder="No"></th>
                            <th><input type="text" class="form-control form-control-sm" style="width:6em"
                                    placeholder="JDE"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nama"></th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Site">
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Date">
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Jenis KPI"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Aktivitas">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($Data as $index => $header)
                            <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $header->jde_no }}</td>
                                <td class="text-right">{{ $header->employee_name }}</td>
                                <td class="text-center">{{ $header->point_produksi }}</td>
                                <td class="text-center">{{ $header->point_support }}</td>
                                <td class="text-center">{{ $header->final_score }}</td>
                                <td class="text-center">{{ $header->grade }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="22" class="border px-4 py-4 text-center text-gray-500">No data available
                                </td>
                            </tr>
                        @endforelse
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

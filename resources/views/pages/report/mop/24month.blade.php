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
            <a class="nav-link"href="{{route('ReportMOP')}}" role="tab">MOP Bulanan</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{route('ReportGradeMOP')}}" role="tab">Grade Distribusi</a>
        </li>
        <li class="nav-item " role="presentation">
            <a class="nav-link active" href="#" role="tab">MOP 24 Bulan</a>
        </li>
    </ul>

    {{-- mop bulanan --}}
    <div class="card container py-3 px-2 shadow-sm">
        <div class="card-body">
            <div class="tab-pane" id="mop-bulanan" role="tabpanel">
                <form action="{{ Route('MOP24Month') }}" method="POST">
                    @csrf
                    <div class="row">
                        <table class="col-md-3">
                            <tr>
                                <td>PERIODE</td>
                                <td>:</td>
                                <td>
                                    <select name="Year_1" id="idYear_1" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach ($Year as $i => $y)
                                            @php
                                                $nextYear = $Year[$i + 1]->year ?? null;
                                            @endphp
                                            @if ($nextYear)
                                                <option value="{{ $y->year }}" {{ request('Year_1') == $y->year ? 'selected' : '' }}>
                                                    {{ trim($y->year) }} - {{ trim($nextYear) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="col-md-auto py-2">
                            <button type="submit" class="btn btn-outline-success btn-sm">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        {{-- </div>
    </div>

    <div class="card container py-3 px-2 shadow-sm mt-3" style="display:{{ request()->is('report/mop-24-month') ? 'none' : 'block' }}">
        <div class="card-body"> --}}

            @if(count($Data) > 0)

                <div class="py-3">
                    <h4 class="text-center">MONTHLY OPERATOR PERFORMANCE</h4>
                    @if($selectedYear1 && $selectedYear2)
                        <h4 class="text-center">PERIODE : {{ $selectedYear1 }} - {{ $selectedYear2 }}</h4>
                    @endif
                </div>
            <!-- Ensure table-responsive wrapper works -->
                <div class="table-responsive">

                    <table id="MOPTable" class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-sm px-2" rowspan="2">No</th>
                                <th class="text-center text-sm px-2" rowspan="2">JDE</th>
                                <th class="text-right text-sm px-4 font-medium" rowspan="2">Nama</th>
                                <th class="text-center text-sm px-2" rowspan="2">EQP Desc</th>
                                <th class="text-center text-sm px-2" colspan="12">Tahun Ke-1</th>
                                <th class="text-center text-sm px-2" colspan="12">Tahun Ke-2</th>
                                <th class="text-center text-sm px-2" rowspan="2">Average Point (E)</th>
                                <th class="text-center text-sm px-2" rowspan="2">MOP Grade</th>
                            </tr>
                            <tr>
                                <th class="text-center px-2">Jan</th>
                                <th class="text-center px-2">Feb</th>
                                <th class="text-center px-2">Mar</th>
                                <th class="text-center px-2">Apr</th>
                                <th class="text-center px-2">May</th>
                                <th class="text-center px-2">Jun</th>
                                <th class="text-center px-2">Jul</th>
                                <th class="text-center px-2">Aug</th>
                                <th class="text-center px-2">Sep</th>
                                <th class="text-center px-2">Oct</th>
                                <th class="text-center px-2">Nov</th>
                                <th class="text-center px-2">Dec</th>

                                <th class="text-center px-2">Jan</th>
                                <th class="text-center px-2">Feb</th>
                                <th class="text-center px-2">Mar</th>
                                <th class="text-center px-2">Apr</th>
                                <th class="text-center px-2">May</th>
                                <th class="text-center px-2">Jun</th>
                                <th class="text-center px-2">Jul</th>
                                <th class="text-center px-2">Aug</th>
                                <th class="text-center px-2">Sep</th>
                                <th class="text-center px-2">Oct</th>
                                <th class="text-center px-2">Nov</th>
                                <th class="text-center px-2">Dec</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($Data as $index => $row)
                                <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $row['jde_no'] }}</td>
                                    <td class="text-left">{{ $row['employee_name'] }}</td>
                                    <td class="text-left">{{ $row['equipment_type1'] }}</td>

                                    {{-- Year 1 --}}
                                    @foreach ($row['year_1'] as $point)
                                        <td class="text-center">{{ number_format($point, 2) }}</td>
                                    @endforeach

                                    {{-- Year 2 --}}
                                    @foreach ($row['year_2'] as $point)
                                        <td class="text-center">{{ number_format($point, 2) }}</td>
                                    @endforeach

                                    <td class="text-center font-semibold">{{ number_format($row['avg_point'], 2) }}</td>
                                    <td class="text-center font-semibold">{{ $row['grade'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="32" class="border px-4 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

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
                // orderCellsTop: true, // Enables the filter inputs under headers
                fixedHeader: true
            });

            // Apply individual column search
            // $('#MOPTable thead tr:eq(2) th').each(function(i) {
            //     $('input', this).on('keyup change', function() {
            //         if (table.column(i).search() !== this.value) {
            //             table.column(i).search(this.value).draw();
            //         }
            //     });
            // });
        });
    </script>
@endpush

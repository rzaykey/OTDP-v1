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
                min-width: 900px;
                /* Ensures the table doesn't shrink too much */
            }

            .btn {
                width: 100%;
                /* Full-width buttons on smaller screens */
                margin-bottom: 5px;
                /* Add spacing between buttons */
            }

            .modal-dialog {
                max-width: 90%;
                /* Reduce modal size for mobile devices */
            }

            .form-control-sm {
                font-size: 0.875rem;
                /* Smaller text in input fields for better spacing */
            }

            .icon-img {
                display: none;
                /* Optional: Hide icons if they clutter the UI */
            }
        }
    </style>
@endpush

@section('content')
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="card container shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold mb-2">MINE OPERATOR PERFORMANCE DATA</h4>
            @if (session('success'))
                <div class="alert alert-success">
                    <strong>Success:</strong> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif
            <div class="align-items-center text-end mb-2">
                <br />
                <div class="d-flex flex-wrap justify-content-end gap-2 mb-2">
                    <a class="btn btn-primary" href="{{ route('MOPCreate') }}">Tambah</a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#importModal">Imports</button>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#exportModal">Export</button>
                </div>
                <!-- Import Modal -->
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="mopImportForm" method="POST" action="{{ route('MOPImport') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content" style="text-align: left">
                                <div class="modal-header">
                                    <h5 class="modal-title">Import Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- File Upload -->
                                    <div class="mb-3">
                                        <label class="form-label">Upload File:</label>
                                        <input type="file" name="import_file" class="form-control" accept=".xlsx, .xls"
                                            id="importFile">
                                    </div>

                                    <!-- Download Template Button -->
                                    <div class="mb-3">
                                        <a href="{{ asset('templates/MOP_Import_Template.xlsx') }}"
                                            class="btn btn-sm btn-primary" download>Download Template</a>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" onclick="confirmImport()">Import</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Export Modal -->
                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                    aria-hidden="true" style="text-align: left">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('MOPExport') }}" id="mopExportForm">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Export Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Column Selection -->
                                    <div class="mb-3">
                                        <label class="form-label">Select Columns:</label>
                                        <div class="row">
                                            @php
                                                $columns = [
                                                    'jde_no' => 'JDE No',
                                                    'employee_name' => 'Employee Name',
                                                    'site' => 'Site',
                                                    'mop_type' => 'MOP Type',
                                                    'month' => 'Month',
                                                    'year' => 'Year',
                                                    'a_attendance_ratio' => 'Attendance Ratio',
                                                    'b_discipline' => 'Discipline',
                                                    'c_safety_awareness' => 'Safety Awareness',
                                                    'd_wh_waste_equiptype1' => 'EWH',
                                                    'e_pty_equiptype1' => 'PTY',
                                                    'point_a' => 'Point A',
                                                    'point_b' => 'Point B',
                                                    'point_c' => 'Point C',
                                                    'point_d' => 'Point D',
                                                    'point_e' => 'Point E',
                                                    'point_eligibilitas' => 'point_eligibiltas',
                                                    'point_produksi' => 'point_produksi',
                                                    'total_point' => 'total_point',
                                                    'mop_bulanan_grade' => 'Grade',
                                                ];
                                            @endphp

                                            @foreach ($columns as $key => $label)
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                                            value="{{ $key }}" id="column_{{ $key }}"
                                                            checked>
                                                        <label class="form-check-label" for="column_{{ $key }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Date Filter -->
                                    <div class="mb-3">
                                        <label class="form-label">From Date (Month/Year):</label>
                                        <input type="month" name="from_date" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">To Date (Month/Year):</label>
                                        <input type="month" name="to_date" class="form-control">
                                    </div>

                                    <!-- File Name -->
                                    <div class="mb-3">
                                        <label class="form-label">File Name:</label>
                                        <input type="text" name="file_name" class="form-control"
                                            placeholder="Enter file name">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success"
                                        onclick="confirmExport()">Export</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>

            <!-- Ensure table-responsive wrapper works -->
            <div class="table-responsive">
                <table id="mopTable"
                    class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th colspan="6"></th>
                            <th colspan="5" style="background-color:rgb(0, 0, 0);color:white">Aspek</th>
                            <th colspan="8" style="background-color:rgb(57, 2, 207);color:white">Point</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th class="text-center text-sm px-2">No</th>
                            <th class="text-center text-sm px-2">JDE</th>
                            <th class="text-center text-sm px-2">Name</th>
                            <th class="text-center text-sm px-2">Site</th>
                            <th class="text-center text-sm px-2">Equipment Type</th>
                            <th class="text-center text-sm px-2">Period</th>
                            <th class="text-center text-sm px-2" style="background-color:black;color:white">Attendance
                                <br /> Ratio (A)
                            </th>
                            <th class="text-center text-sm px-2" style="background-color:black;color:white">Discipline (B)
                            </th>
                            <th class="text-center text-sm px-2" style="background-color:black;color:white">Safety
                                <br />Awareness (C)
                            </th>
                            <th class="text-center text-sm px-2" style="background-color:black;color:white">Ewh (D)</th>
                            <th class="text-center text-sm px-2" style="background-color:black;color:white">Pty (E)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point<br />(A)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point<br />(B)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point<br />(C)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point<br />(D)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point<br />(E)</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point <br />Eligibility</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Point <br />Produksi</th>
                            <th class="text-center text-sm px-2" style="background-color:rgb(57, 2, 207);color:white">
                                Total <br />Point</th>
                        </tr>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('/js/OTPD/mopCRUD.js') }}"></script>
@endpush

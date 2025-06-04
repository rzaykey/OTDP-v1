@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
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
                width: auto;
                height: auto;
            }

            .btn-top {
                width: 100%;
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
                /* display: none; */
                /* Optional: Hide icons if they clutter the UI */
            }
        }
    </style>
@endpush

@section('content')
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <input type="hidden" id="authJDE" value="{{ Auth::user()->username }}">
    <input type="hidden" id="getModelJson" value='@json($getModel)'>
    <input type="hidden" id="getUnitJson" value='@json($getUnit)'>

    <div class="col-xxl-12 col-md-12 col grid-margin stretch-card">
        <div class="card container">
            <div class="card-body">
                <h4 class="fw-bold">Mentoring Data</h4>
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
                    {{-- <div class="d-flex flex-wrap justify-content-end gap-2 mb-2">
                        <a class="btn btn-primary  btn-top" href="{{ route('MentoringCreate') }}">Tambah</a>
                        <button type="button" class="btn btn-success  btn-top" data-bs-toggle="modal"
                            data-bs-target="#importModal">Import</button>
                        <button type="button" class="btn btn-secondary  btn-top" data-bs-toggle="modal"
                            data-bs-target="#exportModal">Export</button>
                    </div> --}}
                    <!-- Import Modal -->
                    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" id="DayActImportForm" action="{{ route('DayActImport') }}"
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
                                            <input type="file" name="import_file" id='import_file' class="form-control"
                                                accept=".xlsx, .xls">
                                        </div>

                                        <!-- Download Template Button -->
                                        <div class="mb-3">
                                            <a href="{{ asset('templates/DayAct_Template.xlsx') }}"
                                                class="btn btn-sm btn-primary" download>Download Template</a>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success"
                                            onclick="confirmImport()">Import</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Export Modal -->
                    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                        aria-hidden="true" style="text-align: left">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('DayActExport') }}">
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
                                                        'jde_no' => 'JDE',
                                                        'employee_name' => 'Nama',
                                                        'site' => 'Site',
                                                        'date_activity' => 'Date',
                                                        'kpi_type' => 'Jenis KPI',
                                                        'activity' => 'Aktivitas',
                                                        'unit_detail' => 'Unit Detail',
                                                        'total_participant' => 'Jumlah Peserta',
                                                        'total_hour' => 'Total Hours',
                                                    ];
                                                @endphp

                                                @foreach ($columns as $key => $label)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="columns[]"
                                                                value="{{ $key }}"
                                                                id="column_{{ $key }}" checked>
                                                            <label class="form-check-label"
                                                                for="column_{{ $key }}">
                                                                {{ $label }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Date Filter -->
                                        <div class="mb-3">
                                            <label class="form-label">From Date:</label>
                                            <input type="date" name="from_date" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">To Date:</label>
                                            <input type="date" name="to_date" class="form-control">
                                        </div>

                                        <!-- File Name -->
                                        <div class="mb-3">
                                            <label class="form-label">File Name:</label>
                                            <input type="text" name="file_name" class="form-control"
                                                placeholder="Enter file name">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Export</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <div id="loader" class="text-center py-4" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <!-- Ensure table-responsive wrapper works -->
                <div class="table-responsive">
                    <table id="dayactTable" style="font-size: 12px; width: 100%;"
                        class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Trainer</th>
                                <th class="text-center">Operator</th>
                                <th class="text-center">Site</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Hour</th>
                                <th class="text-center">Point Observasi</th>
                                <th class="text-center">Point Mentoring</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            <tr>
                                <th><input type="text" class="form-control form-control-sm" style="width:4em"
                                        placeholder="No"></th>
                                <th><input type="text" class="form-control form-control-sm" style="width:6em"
                                        placeholder="Trainer"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Operator"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Site">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Area">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Unit">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Date">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Hour">
                                </th>
                                <th><input type="text" class="form-control form-control-sm"
                                        placeholder="Point Observasi">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Point Mentoring">
                                </th>
                                <th></th> <!-- No filter for actions -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('/js/OTPD/mentoringCRUD.js') }}"></script>
@endpush

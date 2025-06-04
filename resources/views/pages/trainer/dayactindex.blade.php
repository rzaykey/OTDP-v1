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
    <input type="hidden" id="getActivityJson" value='@json($getActivity)'>
    <input type="hidden" id="getUnitJson" value='@json($getUnit)'>

    <div class="col-xxl-12 col-md-12 col grid-margin stretch-card">
        <div class="card container">
            <div class="card-body">
                <h4 class="fw-bold">DAILY ACTIVITY</h4>
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
                        <a class="btn btn-primary  btn-top" href="{{ route('DayActCreate') }}">Tambah</a>
                        <button type="button" class="btn btn-success  btn-top" data-bs-toggle="modal"
                            data-bs-target="#importModal">Import</button>
                        <button type="button" class="btn btn-secondary  btn-top" data-bs-toggle="modal"
                            data-bs-target="#exportModal">Export</button>
                    </div>
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
                                                                value="{{ $key }}" id="column_{{ $key }}"
                                                                checked>
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
                    <table id="dayactTable" style="font-size: 12px;width100%"
                        class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">JDE</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Site</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Jenis KPI</th>
                                <th class="text-center">Aktivitas</th>
                                <th class="text-center">Unit Detail</th>
                                <th class="text-center">Jumlah Peserta</th>
                                <th class="text-center">Total Hours</th>
                                <th class="text-center"> Actions </th>
                            </tr>

                            <!-- Filter Input Row -->
                            <tr>
                                <th><input type="text" class="form-control form-control-sm" style="width:4em"
                                        placeholder="No"></th>
                                <th><input type="text" class="form-control form-control-sm" style="width:6em"
                                        placeholder="JDE"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Nama"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Site">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Date">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Jenis KPI">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Aktivitas">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Unit Detail">
                                </th>
                                <th><input type="text" class="form-control form-control-sm"
                                        placeholder="Jumlah Peserta">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Total Hours">
                                </th>
                                <th></th> <!-- No filter for actions -->
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @forelse ($data as $index => $header)
                                <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                    @php
                                        $activityData = $getActivity->firstWhere('id', $header->activity);
                                        $unitData = $getUnit->firstWhere('id', $header->unit_detail);
                                    @endphp
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $header->jde_no }}</td>
                                    <td>{{ $header->employee_name }}</td>
                                    <td class="text-center">{{ $header->site }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($header->date_activity)->toDateString() }}</td>
                                    <td>{{ $header->kpi_type }}</td>
                                    <td>
                                        @if ($activityData)
                                            {{ $activityData->kpi . ' - ' . $activityData->activity }}
                                        @else
                                            {{ $header->activity }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($unitData)
                                            {{ $unitData->type . ' - ' . $unitData->model }}
                                        @else
                                            {{ $header->unit_detail }}
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $header->total_participant }}</td>
                                    <td class="text-right">{{ $header->total_hour }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('DayActEdit', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-edit.png') }}" class="icon-img"
                                                style="height: 1em;width:auto">
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('DayActDelete', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-delete.png') }}"
                                                class="icon-img" style="height: 1em;width:auto">
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="border px-4 py-4 text-center text-gray-500">No data
                                        available
                                    </td>
                                </tr>
                            @endforelse --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit Daily Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" id="edit_id" name="edit_id">

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="edit_jde" class="form-label">JDE</label>
                                <input type="text" class="form-control" id="edit_jde" name="edit_jde" readonly
                                    required>
                            </div>

                            <div class="col-md-9 mb-3">
                                <label for="edit_name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="edit_name" name="edit_name" readonly
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_site" class="form-label">Site</label>
                                <input type="text" class="form-control" id="edit_site" name="edit_site" readonly
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="edit_date" name="edit_date" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_kpi" class="form-label">Jenis KPI</label>
                            <select id="edit_kpi" name="edit_kpi" class="form-select js-operator-select"
                                data-width="100%"></select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_activity" class="form-label">Activity</label>
                            <select id="edit_activity" name="edit_activity" class="form-select js-operator-select"
                                data-width="100%"></select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_unit_detail" class="form-label">Unit Detail</label>
                            <select id="edit_unit_detail" name="edit_unit_detail" class="form-select js-operator-select"
                                data-width="100%"></select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_jml_peserta" class="form-label">Jumlah Peserta</label>
                                <input type="number" class="form-control" id="edit_jml_peserta"
                                    name="edit_jml_peserta">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_total_hour" class="form-label">Total Hours</label>
                                <input type="number" class="form-control" id="edit_total_hour" name="edit_total_hour">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
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
    <script src="{{ asset('/js/OTPD/dayactCRUD.js') }}"></script>
@endpush

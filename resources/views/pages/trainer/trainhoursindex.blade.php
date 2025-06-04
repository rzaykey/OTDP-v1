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
    <input type="hidden" id="getClassJson" value='@json($getClassUnit)'>
    <input type="hidden" id="getTypeJson" value='@json($getTypeUnit)'>
    <input type="hidden" id="getCodeJson" value='@json($getCode)'>


    <div class="col-xxl-12 col-md-12 col grid-margin stretch-card">
        <div class=" card container py-3 px-2 shadow-sm">
            <div class="card-body">
                <h4 class="fw-bold">TRAIN HOURS</h4>
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
                <div class="align-items-center text-end mb-3 ">
                    <br />
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-2">
                        <a class="btn btn-primary btn-top" href="{{ route('HMTrainCreate') }}">Tambah</a>
                        <button type="button" class="btn btn-success btn-top" data-bs-toggle="modal"
                            data-bs-target="#importModal">Import</button>
                        <button type="button" class="btn btn-secondary btn-top" data-bs-toggle="modal"
                            data-bs-target="#exportModal">Export</button>
                    </div>
                    <!-- Import Modal -->
                    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" id="HMTrainImportForm" action="{{ route('HMTrainImport') }}"
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
                                            <a href="{{ asset('templates/HMTrain_Template.xlsx') }}"
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
                            <form method="POST" action="{{ route('HMTrainExport') }}">
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
                                                        'position' => 'Jabatan',
                                                        'site' => 'Site',
                                                        'date_activity' => 'Tanggal',
                                                        'training_type' => 'Tipe Training',
                                                        'unit_class' => 'Unit Class',
                                                        'unit_type' => 'Type Unit',
                                                        'code' => 'Code',
                                                        'batch' => 'Batch',
                                                        'hm_start' => 'HM Start',
                                                        'hm_end' => 'HM End',
                                                        'total_hm' => 'Total HM',
                                                        'plan_total_hm' => 'Plan Total',
                                                        'progres' => 'Progres',
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

                <!-- Ensure table-responsive wrapper works -->
                <div class="table-responsive">
                    <table id="trainTable" style="font-size: 12px;width100%"
                        class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-sm px-2">No</th>
                                <th class="text-center text-sm px-2">JDE</th>
                                <th class="text-right text-sm px-4 font-medium">Nama</th>
                                <th class="text-center text-sm px-2">Jabatan</th>
                                <th class="text-center text-sm px-2">Site</th>
                                <th class="text-center text-sm px-2">Tanggal</th>
                                <th class="text-center text-sm px-2">Tipe Training</th>
                                <th class="text-center text-sm px-2">Unit Class</th>
                                <th class="text-center text-sm px-2">Type Unit</th>
                                <th class="text-center text-sm px-2">Code</th>
                                <th class="text-center text-sm px-2">Batch</th>
                                <th class="text-center text-sm px-2">Plan Total</th>
                                <th class="text-center text-sm px-2">HM Start</th>
                                <th class="text-center text-sm px-2">HM End</th>
                                <th class="text-center text-sm px-2">Total HM</th>
                                <th class="text-center text-sm px-2">Progress</th>
                                <th class="text-center text-sm px-2">% Progress</th>
                                <th class="text-center text-sm px-2 text-right">Actions</th>
                            </tr>

                            <!-- Filter Input Row -->
                            <tr>
                                <th><input type="text" class="form-control form-control-sm"
                                        style="width:4em"placeholder="No"></th>
                                <th><input type="text" class="form-control form-control-sm"
                                        style="width:6em"placeholder="JDE"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Nama"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Jabatan">
                                </th>
                                <th><input type="text" class="form-control form-control-sm"placeholder="Site"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Tanggal">
                                </th>
                                <th><input type="text" class="form-control form-control-sm"
                                        placeholder="Tipe Training">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Unit Class">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Type Unit">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Code"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Batch"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Plan Total">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="HM Start">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="HM End"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Total HM">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Progress">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="% Progress">
                                </th>
                                <th></th> <!-- No filter for actions -->
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @forelse ($data as $index => $header)
                                <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                    @php
                                        $unit_class_data = $getClassUnit->firstWhere('id', $header->unit_class);
                                        $code_data = $getCode->firstWhere('id', $header->code);
                                    @endphp
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $header->jde_no }}</td>
                                    <td class="text-right">{{ $header->employee_name }}</td>
                                    <td class="text-center">{{ $header->position }}</td>
                                    <td class="text-center">{{ $header->site }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($header->date_activity)->toDateString() }}</td>
                                    <td class="text-center">{{ $header->training_type }}</td>
                                    <td>{{ $header->unit_type }}</td>
                                    <td>
                                        @if ($unit_class_data)
                                            {{ $unit_class_data->class }}
                                        @else
                                            {{ $header->unit_class }}
                                        @endif
                                    </td>

                                    <td>
                                        @if ($code_data)
                                            {{ $code_data->no_unit }}
                                        @else
                                            {{ $header->code }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $header->batch }}</td>
                                    <td class="text-center">{{ $header->plan_total_hm }}</td>
                                    <td class="text-center">{{ $header->hm_start }}</td>
                                    <td class="text-center">{{ $header->hm_end }}</td>
                                    <td class="text-center">{{ $header->total_hm }}</td>
                                    <td class="text-center">{{ $header->progres }}</td>
                                    <td class="text-center">
                                        {{ $header->plan_total_hm > 0 ? number_format(($header->progres / $header->plan_total_hm) * 100, 0) : 0 }}%
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('HMTrainEdit', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-edit.png') }}" class="icon-img"
                                                style="height: 1em;width:auto">
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('HMTrainDelete', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-delete.png') }}"
                                                class="icon-img" style="height: 1em;width:auto">
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="22" class="border px-4 py-4 text-center text-gray-500">No data
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit Train Hours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" id="edit_id" name="edit_id">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="JDE" class="form-label fw-semibold">JDE</label>
                                    <input type="text" class="form-control" placeholder=""
                                        value="{{ Auth::user()->username }}" id="edit_jde" name="edit_jde" required>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nama</label>
                                    <input type="text" class="form-control" placeholder=""
                                        value="{{ Auth::user()->name }}" id="edit_name" name="edit_name" required>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="site" class="form-label fw-semibold">Site</label>
                                    <input type="text" class="form-control" placeholder=""
                                        value="{{ Auth::user()->site }}" id="edit_site" name="edit_site" required>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="position" class="form-label fw-semibold">Jabatan</label>
                                    <input type="text" class="form-control" placeholder="" id="edit_position"
                                        name="edit_position" required>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date" class="form-label fw-semibold">Tanggal</label>
                                    <input type="date" class="form-control" placeholder="" id="edit_date"
                                        name="edit_date" required>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="train_type" class="form-label fw-semibold">Tipe Training</label>
                                    <select id="edit_train_type" name="edit_train_type"
                                        class="form-select js-operator-select" data-width="100%"></select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_type" class="form-label fw-semibold">Type Unit</label>
                                    <select id="edit_unit_type" name="edit_unit_type"
                                        class="form-select js-operator-select" data-width="100%"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="batch" class="form-label fw-semibold">Batch</label>
                                    <select class="form-select mb-3" aria-label="Select Batch" id="edit_batch"
                                        name="edit_batch" required>
                                        <option value="" selected disabled>Select</option>
                                        <option value="Batch 1">Batch 1</option>
                                        <option value="Batch 2">Batch 2</option>
                                        <option value="Batch 3">Batch 3</option>
                                        <option value="Batch 4">Batch 4</option>
                                        <option value="Batch 5">Batch 5</option>
                                        <option value="Batch 6">Batch 6</option>
                                        <option value="Batch 7">Batch 7</option>
                                        <option value="Batch 8">Batch 8</option>
                                        <option value="Batch 9">Batch 9</option>
                                        <option value="Batch 10">Batch 10</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_class" class="form-label fw-semibold">Unit Class</label>
                                    <select id="edit_unit_class" name="edit_unit_class"
                                        class="form-select js-operator-select" data-width="100%"></select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label fw-semibold">Code</label>
                                    <select id="edit_code" name="edit_code" class="form-select js-operator-select"
                                        data-width="100%"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="HM_start" class="form-label fw-semibold">HM Start</label>
                                    <input type="number" class="form-control" placeholder="" id="edit_hm_start"
                                        name="edit_hm_start" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="HM_end" class="form-label fw-semibold">HM End</label>
                                    <input type="number" class="form-control" placeholder="" id="edit_hm_end"
                                        name="edit_hm_end" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_HM" class="form-label fw-semibold">Total HM</label>
                                    <input type="number" class="form-control" placeholder="" id="edit_total_hm"
                                        name="edit_total_hm" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="plan_total" class="form-label fw-semibold">Plan Total</label>
                                    <input type="number" class="form-control" placeholder="56" id="edit_plan_total"
                                        name="edit_plan_total" value="56">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="progress" class="form-label fw-semibold">Progress</label>
                                    <input type="number" class="form-control" placeholder="" id="edit_progress"
                                        name="edit_progress" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="per_progress" class="form-label fw-semibold">% Progress</label>
                                    <input type="number" class="form-control" placeholder="" id="edit_per_progress"
                                        name="edit_per_progress" readonly>
                                </div>
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
    <script src="{{ asset('/js/OTPD/trainhourCRUD.js') }}"></script>

    {{-- <script>
        $(document).ready(function() {
            $('#edit_train_type').select2({
                placeholder: 'Select Training Type',
                dropdownParent: $('#editModal'),
                ajax: {
                    url: "{{ route('activity.kpi') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            role: "Full",
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.kpi,
                                    text: item.kpi,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });

            $('#edit_unit_type').select2({
                placeholder: 'Select Unit Type',
                dropdownParent: $('#editModal'),
                ajax: {
                    url: "{{ route('unit.classunit') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.class,
                                    text: item.class,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });

            let selectedUnitType = null;

            $('#edit_unit_type').on('change', function() {
                selectedUnitType = $(this).val();
                $('#edit_unit_class').val(null).trigger('change'); // clear current selection
            });

            $('#edit_unit_class').select2({
                placeholder: 'Select Unit Class',
                dropdownParent: $('#editModal'),
                ajax: {
                    url: "{{ route('unit.modelunitbasedtype') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: selectedUnitType // pass the selected unit type
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.model,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });

            let selectedModel = null;

            $("#edit_unit_class").on("select2:select", function(e) {
                selectedModel = e.params.data.text; // or `e.params.data.id` if you use ID
                $("#edit_code").val(null).trigger("change"); // clear current Unit
            });

            $('#edit_code').select2({
                placeholder: 'Select Code',
                dropdownParent: $('#editModal'),
                ajax: {
                    url: "{{ route('unit.unit') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                             model: selectedModel, // pass selected model to backend
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                     text: item.no_unit,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['#edit_train_type', '#edit_unit_class', '#edit_batch', '#edit_hm_start',
            '#edit_hm_end'];
            inputs.forEach(id => {
                document.querySelector(id).addEventListener('change', handleInputs);
            });

            function handleInputs() {
                calculateTotalHM();
                fetchTotalHM();
            }

            function calculateTotalHM() {
                const start = parseFloat(document.querySelector('#edit_hm_start').value) || 0;
                const end = parseFloat(document.querySelector('#edit_hm_end').value) || 0;

                const totalHM = end - start;
                document.querySelector('#edit_total_hm').value = totalHM > 0 ? totalHM : 0;
            }

            function fetchTotalHM() {
                const jde = document.querySelector('#edit_jde').value;
                const training_type = document.querySelector('#edit_train_type').value;
                const unit_class = document.querySelector('#edit_unit_class').value;
                const batch = document.querySelector('#edit_batch').value;
                const id = document.querySelector('#edit_id')?.value;

                if (jde && training_type && unit_class && batch) {
                    fetch(`{{ route('trainer.totalHM') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                jde,
                                training_type,
                                unit_class,
                                batch,
                                id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const totalFromDB = data.total_hm ?? 0;
                            const totalHMInput = parseFloat(document.querySelector('#edit_total_hm').value) ||
                            0;
                            const finalProgress = parseFloat(totalFromDB) + parseFloat(totalHMInput);

                            document.querySelector('#edit_progress').value = finalProgress;

                            const planTotal = parseFloat(document.querySelector('#edit_plan_total').value ||
                            56);
                            const percentProgress = (finalProgress / planTotal) * 100;
                            document.querySelector('#edit_per_progress').value = percentProgress.toFixed(2);
                        })
                        .catch(err => console.error('Error:', err));
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#trainTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                orderCellsTop: true, // Enables the filter inputs under headers
                fixedHeader: true
            });

            // Apply individual column search
            $('#trainTable thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = button.getAttribute('data-id');
                    const url = button.getAttribute('data-url');
                    const getTypeUnit = @json($getTypeUnit);
                    const getClassUnit = @json($getClassUnit);
                    const getCode = @json($getCode);

                    Swal.fire({
                        title: 'Loading...',
                        html: 'Fetching data, please wait...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            // Format date to YYYY-MM-DD for the date input
                            const date = new Date(data.date_activity);
                            date.setDate(date.getDate() + 1); // Add one day
                            const formattedDate = date.toISOString().split('T')[0];

                            document.getElementById('edit_id').value = data.id;
                            document.getElementById('edit_jde').value = data.jde_no;
                            document.getElementById('edit_name').value = data.employee_name;
                            document.getElementById('edit_position').value = data.position;
                            document.getElementById('edit_site').value = data.site;
                            document.getElementById('edit_date').value = formattedDate;

                            if (data.training_type) {
                                const newOption = new Option(data.training_type, data.training_type, true, true);
                                $('#edit_train_type').append(newOption).trigger('change');
                            }

                            if (data.unit_type) {
                                const newOption = new Option(data.unit_type, data.unit_type, true, true);
                                $('#edit_unit_type').append(newOption).trigger('change');
                            }

                            const selected_unit_class = getClassUnit.find(item => item.id == data
                                .unit_class);
                            if (selected_unit_class) {
                                const new_unit_class = new Option(selected_unit_class.class,
                                    selected_unit_class.id, true, true);
                                $('#edit_unit_class').append(new_unit_class).trigger('change');
                            }

                            const selected_code = getCode.find(item => item.id == data
                            .code);
                            if (selected_code) {
                                const new_code = new Option(selected_code.no_unit, selected_code.id, true, true);
                                $('#edit_code').append(new_code).trigger('change');
                            }

                            document.getElementById('edit_batch').value = data.batch;
                            document.getElementById('edit_hm_start').value = data.hm_start;
                            document.getElementById('edit_hm_end').value = data.hm_end;
                            document.getElementById('edit_total_hm').value = data.total_hm;
                            document.getElementById('edit_plan_total').value = data
                                .plan_total_hm;
                            document.getElementById('edit_progress').value = data.progres;
                            document.getElementById('edit_per_progress').value = ((data
                                .progres / data.plan_total_hm) * 100).toFixed(2);

                            const editModal = new bootstrap.Modal(document.getElementById(
                                'editModal'));
                            editModal.show();
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to fetch data. Please try again.'
                            });
                            console.error('Error fetching data:', error);
                        });
                });
            });

            // Handle form submission
            document.getElementById('editForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                Swal.fire({
                    title: 'Updating...',
                    html: 'Please wait while your data is being updated...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`{{ route('HMTrainUpdate') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Data successfully updated!'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Failed to update data.'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating data.'
                        });
                        console.error('Error:', error);
                    });
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const deleteUrl = button.getAttribute('data-url');
                    const recordId = button.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(deleteUrl, {
                                    method: 'DELETE', // Ensure this is DELETE
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json' // Optional but good practice
                                    }
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: result.message,
                                            icon: 'success'
                                        }).then(() => {
                                            location
                                                .reload(); // Refresh table or redirect
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: result.message ||
                                                'Failed to delete the record.',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'An error occurred while deleting the record.',
                                        icon: 'error'
                                    });
                                });
                        }
                    });
                });
            });
        });

        function confirmImport() {
            const fileInput = document.getElementById('import_file').files[0];
            console.log(fileInput);
            if (!fileInput) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a file to import!',
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to import this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Import it!',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Importing...',
                        text: 'Please wait while your file is being processed.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create FormData object and append the file
                    let formData = new FormData();
                    formData.append('_token', document.querySelector('input[name="_token"]').value);
                    formData.append('import_file', fileInput);

                    fetch("{{ route('HMTrainImport') }}", {
                            method: 'POST',
                            body: formData, // Do NOT manually set Content-Type for FormData
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(response => response.json()) // Expect JSON response
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message,
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    location.reload(); // Reload after success
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Import Failed!',
                                    text: data.message || 'An unknown error occurred.',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again later.',
                                confirmButtonColor: '#d33'
                            });
                        });
                }
            });
        }
    </script> --}}
@endpush

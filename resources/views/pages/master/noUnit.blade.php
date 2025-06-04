@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    {{-- tabel --}}
        <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="col-xxl-12 col-md-12 col grid-margin stretch-card">
        <div class="card container">
            <div class="card-body">
                <h4 class="fw-bold">UNIT NUMBER</h4>
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
                        <a class="btn btn-primary btn-top" data-bs-toggle="modal" data-bs-target="#addModal">Tambah</a>
                    </div>
                </div>

                <!-- Ensure table-responsive wrapper works -->
                <div class="table-responsive">
                    <table id="unitTable" style="font-size: 12px;width100%"
                        class="table table-sm accessibility-issue--error table-bordered align-content-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">No Unit</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Class</th>
                                <th class="text-center">Merk</th>
                                <th class="text-center">Model</th>
                                <th class="text-center">Site</th>
                                <th class="text-center"> Actions </th>
                            </tr>

                            <!-- Filter Input Row -->
                            <tr>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="No Unit"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Type">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Class">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Merk">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Model">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Site"></th>
                                <th></th> <!-- No filter for actions -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $header)
                                <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $header->no_unit }}</td>
                                    <td class="text-center">{{ $header->type }}</td>
                                    <td class="text-center">{{ $header->category_mentoring }}</td>
                                    <td class="text-center">{{ $header->merk }}</td>
                                    <td class="text-center">{{ $header->model }}</td>
                                    <td class="text-center">{{ $header->site }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('MasterUnitEdit', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-edit.png') }}" class="icon-img"
                                                style="height: 1em;width:auto">
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('MasterUnitDelete', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-delete.png') }}" class="icon-img"
                                                style="height: 1em;width:auto">
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="border px-4 py-4 text-center text-gray-500">No data
                                        available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalTitle">Tambah No Unit Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="upload-form">
                        @csrf
                        <input type="hidden" id="edit_id" name="edit_id">

                        <div class=" mb-3">
                            <label for="no_unit" class="form-label">No. Unit</label>
                            <input type="text" class="form-control" id="no_unit" name="no_unit" required>
                        </div>

                        <div class=" mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" name="type" class="form-select js-operator-select"
                                data-width="100%" required></select>
                        </div>

                        <div class=" mb-3">
                            <label for="class" class="form-label">Class</label>
                            <select id="class" name="class" class="form-select js-operator-select"
                                data-width="100%" required></select>
                        </div>

                        <div class=" mb-3">
                            <label for="merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" required>
                        </div>

                        <div class=" mb-3">
                            <label for="model" class="form-label">Model</label>
                            <select id="model" name="model" class="form-select js-operator-select"
                                data-width="100%" required></select>
                        </div>

                        <div class="mb-3">
                            <label for="site" class="form-label">Site</label>
                            <select class="form-select" id="site" name="site" required>
                                <option value="">Select</option>
                                <option value="ACP">ACP</option>
                                <option value="BCP">BCP</option>
                                <option value="KCP">KCP</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="cancelButton" class="btn btn-warning"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #1481FF;"
                                id="uploadButton">
                                Submit
                            </button>
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
    <script>
        $(document).ready(function() {
            $('#type').select2({
                placeholder: 'Select Type',
                dropdownParent: $('#addModal'),

                ajax: {
                    url: "{{ route('unit.typeunit') }}",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.type,
                                    text: item.type,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });

            $('#class').select2({
                placeholder: 'Select Class',
                dropdownParent: $('#addModal'),

                ajax: {
                    url: "{{ route('unit.classunit') }}",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.class,
                                    text: item.class,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });

            let selectedUnitType = null;

            $('#type').on('change', function() {
                selectedUnitType = $(this).val();
                $('#model').val(null).trigger('change'); // clear current selection
            });

            $('#model').select2({
                placeholder: 'Select Unit Class',
                dropdownParent: $('#addModal'),
                ajax: {
                    url: "{{ route('unit.modelunitbasedtype') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            unit: selectedUnitType // pass the selected unit type
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.model,
                                    text: item.model,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });
        });

        $(document).ready(function() {
            var table = $('#unitTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                orderCellsTop: true, // Enables the filter inputs under headers
                fixedHeader: true
            });

            // Apply individual column search
            $('#unitTable thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
        });
    </script>

    <script>
        document.querySelector('.btn-top').addEventListener('click', function() {
            document.getElementById('upload-form').reset();
            $('#type, #class, #model').val(null).trigger('change');

            document.getElementById('edit_id').value = '';

            document.getElementById('modalTitle').textContent = 'Tambah No Unit Baru';
            document.getElementById('uploadButton').textContent = 'Submit';

            const modal = new bootstrap.Modal(document.getElementById('addModal'));
            modal.show();
        });

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

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = button.getAttribute('data-id');
                    const url = button.getAttribute('data-url');

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

                            document.getElementById('edit_id').value = data.id;
                            document.getElementById('no_unit').value = data.no_unit;
                            document.getElementById('merk').value = data.merk;
                            document.getElementById('site').value = data.site;

                            $('#type').empty().append(new Option(data.type, data.type, true, true)).trigger('change');
                            $('#class').empty().append(new Option(data.category_mentoring, data.category_mentoring, true, true)).trigger('change');
                            $('#model').empty().append(new Option(data.model, data.model, true, true)).trigger('change');

                            document.getElementById('modalTitle').textContent = 'Edit No Unit';
                            document.getElementById('uploadButton').textContent = 'Update';

                            const modal = new bootstrap.Modal(document.getElementById(
                                'addModal'));
                            modal.show();
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Failed to fetch data', 'error');
                            console.error(error);
                        });
                });
            });

            document.getElementById('upload-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const isEdit = $('#edit_id').val() !== '';
                const url = isEdit ?
                    `{{ route('MasterUnitUpdate') }}` :
                    `{{ route('MasterUnitStore') }}`;
                const formData = new FormData(this);

                Swal.fire({
                    title: isEdit ? 'Updating...' : 'Submitting...',
                    html: 'Please wait while your data is being processed.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(url, {
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
                            })
                            .then(() => location.reload());
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
@endpush

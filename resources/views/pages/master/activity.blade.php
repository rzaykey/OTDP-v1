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
                <h4 class="fw-bold">ACTIVITY</h4>
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
                                <th class="text-center">KPI</th>
                                <th class="text-center">Activity</th>
                                <th class="text-center">Site</th>
                                <th class="text-center"> Actions </th>
                            </tr>

                            <!-- Filter Input Row -->
                            <tr>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="KPI"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Activity"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="site"></th>
                                <th></th> <!-- No filter for actions -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $header)
                                <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $header->kpi }}</td>
                                    <td class="text-center">{{ $header->activity }}</td>
                                    <td class="text-center">{{ $header->site }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm edit-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('MasterActivityEdit', $header->id) }}">
                                            <img src="{{ asset('assets/images/icons/file-edit.png') }}" class="icon-img"
                                                style="height: 1em;width:auto">
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                            data-id="{{ $header->id }}"
                                            data-url="{{ route('MasterActivityDelete', $header->id) }}">
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
                    <h5 class="modal-title" id="modalTitle">Tambah Aktivitas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="upload-form">
                        @csrf
                        <input type="hidden" id="edit_id" name="edit_id">

                        <div class="mb-3">
                            <label for="kpi" class="form-label">KPI</label>
                            <select class="form-select" id="kpi" name="kpi" required>
                                <option value="">Select</option>
                                <option value="Operator Readiness & Training (IDP)">Operator Readiness & Training (IDP)</option>
                                <option value="Operator Continous Dev">Operator Continous Dev</option>
                                <option value="Site Support Project">Site Support Project</option>
                            </select>
                        </div>

                        <div class=" mb-3">
                            <label for="activity" class="form-label">Activity</label>
                            <input type="text" class="form-control" id="activity" name="activity" required>
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

        document.querySelector('.btn-top').addEventListener('click', function() {
            document.getElementById('upload-form').reset();
            $('#type, #class, #model').val(null).trigger('change');

            document.getElementById('edit_id').value = '';

            document.getElementById('modalTitle').textContent = 'Tambah Aktivitas Baru';
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
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
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
                                                .reload();
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
                            document.getElementById('kpi').value = data.kpi;
                            document.getElementById('activity').value = data.activity;
                            document.getElementById('site').value = data.site;

                            document.getElementById('modalTitle').textContent = 'Edit Aktivitas';
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
                    `{{ route('MasterActivityUpdate') }}` :
                    `{{ route('MasterActivityStore') }}`;
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

@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="col-xxl-12 col-md-12 col grid-margin stretch-card">
        <div class="card" style="100%">
            <div class="card-body">
                <h4 class="fw-bold mb-4">INPUT DAILY ACTIVITY</h4>
                @if (session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ session('error') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
                        <ul>
                            <li>{{ session('success') }}</li>
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('DayActStore') }}" id="upload-form">
                    @csrf
                    <div class="row">
                        <input type="hidden" value="{{ env('server') }}" id="ServerDev">
                        <input type="hidden" value="{{ Auth::user()->role }}" id="role">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="JDE" class="form-label fw-semibold">JDE</label>
                                <input type="text" class="form-control" placeholder=""
                                    value="{{ $employeeAuth->EmployeeId }}" id="JDE" name="JDE" required readonly>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama</label>
                                <input type="text" class="form-control" placeholder=""
                                    value="{{ $employeeAuth->EmployeeName }}" id="name" name="name" required
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="site" class="form-label fw-semibold">Site</label>
                                <input type="text" class="form-control" placeholder=""
                                    value="{{ $employeeAuth->LocationGroupName }}" id="site" name="site" required
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" placeholder="" id="date" name="date"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="KPI" class="form-label fw-semibold">Jenis KPI</label>
                                <select class="form-select mb-3" aria-label="Select KPI Type" id="KPI" name="KPI"
                                    required>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label class="form-label" for="activity">Aktivitas</label>
                                <select id="activity" name="activity" class="form-select js-operator-select"
                                    data-width="100%" required></select>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="mb-3">
                                <label for="unit_detail" class="form-label fw-semibold">Unit Detail</label>
                                <select id="unit_detail" name="unit_detail" class="form-select js-operator-select"
                                    data-width="100%" required></select>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="mb-3">
                                <label for="jml_peserta" class="form-label fw-semibold">Jumlah Peserta</label>
                                <input type="number" class="form-control" placeholder="" id="jml_peserta"
                                    name="jml_peserta" required>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="mb-3">
                                <label for="total_hour" class="form-label fw-semibold">Total Hours</label>
                                <input type="number" class="form-control" placeholder="" id="total_hour"
                                    name="total_hour" required>
                            </div>
                        </div>
                    </div>


                    {{-- Submit Button --}}
                    <div class="align-items-center text-end mb-5">
                        <button type="button" id="cancelButton" class="btn btn-warning">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #1481FF;"
                            id="uploadButton">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            let site = $('#site').val(); // grab site value
            let role = $('#role').val();
            $('#KPI').select2({
                placeholder: 'Select KPI',
                ajax: {
                    url: "{{ route('activity.kpi') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            site: site,
                            role: role
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
                }
            });
            let selectedKPI = null;

            $('#KPI').on('change', function() {
                selectedKPI = $(this).val();
                $('#activity').val(null).trigger('change'); // clear current selection
            });



            $('#activity').select2({
                placeholder: 'Select Activity',
                ajax: {
                    url: "{{ route('activity.master') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            kpi: selectedKPI, // pass selected KPI
                            site: site,
                            role: role
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.site + ' - ' + item.kpi + ' - ' + item.activity,
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#unit_detail').select2({
                placeholder: 'Select Unit Detail',
                ajax: {
                    url: "{{ route('unit.modelunit') }}",
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
                                    id: item.id,
                                    text: item.class + ' - ' + item.model,
                                };
                            })
                        };
                    },
                    cache: true
                },
            });
        });

        // Function to clear all input fields
        function clearAllInputs() {
            $('input[type="text"], input[type="number"], select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val(null).trigger('change'); // Clear Select2 dropdown
                } else if ($(this).is('select')) {
                    $(this).prop('selectedIndex', 0); // Reset regular select dropdown
                } else {
                    $(this).val(''); // Clear text and number inputs
                }
            });
        }

        // Attach the function to the Cancel button
        $('#cancelButton').on('click', function(e) {
            e.preventDefault(); // Prevent any unwanted page reload
            clearAllInputs();
        });

        $(document).ready(function() {
            $('#uploadButton').on('click', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = document.getElementById('upload-form');

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Collect form data
                const formData = $('#upload-form').serialize();

                // Show loading alert
                Swal.fire({
                    title: 'Submitting...',
                    text: 'Please wait while your data is being processed.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('DayActStore') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val() // CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Daily Activity record created successfully.',
                            confirmButtonColor: '#1481FF'
                        }).then(() => {
                            window.location.href =
                                "{{ route('DayActIndex') }}"; // Redirect after success
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.error ||
                            'An error occurred. Please contact IT.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // Clear inputs on Cancel button click
            $('#cancelButton').on('click', function(e) {
                e.preventDefault();
                $('#upload-form')[0].reset(); // Reset the form
                $('#KPI').val('').trigger('change'); // Clear Select2 dropdown
            });
        });
    </script>
@endpush

@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="card" style="100%">
        <div class="card-body">
            <h4 class="fw-bold mb-4">INPUT TRAIN HOURS</h4>
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
            <form method="POST" action="{{ route('HMTrainStore') }}" id="upload-form">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="JDE" class="form-label fw-semibold">JDE</label>
                            <input type="text" class="form-control" placeholder=""
                                value="{{ $employeeAuth->EmployeeId }}" id="JDE" name="JDE" required readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama</label>
                            <input type="text" class="form-control" placeholder=""
                                value="{{ $employeeAuth->EmployeeName }}" id="name" name="name" required readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="position" class="form-label fw-semibold">Jabatan</label>
                            <input type="text" class="form-control" placeholder=""
                                value="{{ $employeeAuth->JobTtlName }}" id="position" name="position" required readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site" class="form-label fw-semibold">Site</label>
                            <input type="text" class="form-control" placeholder=""
                                value="{{ $employeeAuth->LocationGroupName }}" id="site" name="site" required
                                readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control" placeholder="" id="date" name="date"
                                required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="train_type" class="form-label fw-semibold">Tipe Training</label>
                            {{-- <input type="text" class="form-control" placeholder="" id="train_type" name="train_type"
                                required> --}}
                            <select id="train_type" name="train_type" class="form-select js-operator-select"
                                data-width="100%" required></select>
                        </div>
                    </div>



                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="batch" class="form-label fw-semibold">Batch</label>
                            <select class="form-select mb-3" aria-label="Select Batch" id="batch" name="batch"
                                required>
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
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="unit_type" class="form-label fw-semibold">Type Unit</label>
                            <select id="unit_type" name="unit_type" class="form-select js-operator-select" data-width="100%"
                                required></select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="unit_class" class="form-label fw-semibold">Unit Class</label>
                            <select id="unit_class" name="unit_class" class="form-select js-operator-select"
                                data-width="100%" required></select>

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">Code</label>
                            <select id="code" name="code" class="form-select js-operator-select"
                                data-width="100%" required></select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="HM_start" class="form-label fw-semibold">HM Start</label>
                            <input type="number" class="form-control" placeholder="" id="HM_start" name="HM_start"
                                required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="HM_end" class="form-label fw-semibold">HM End</label>
                            <input type="number" class="form-control" placeholder="" id="HM_end" name="HM_end"
                                required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_HM" class="form-label fw-semibold">Total HM</label>
                            <input type="number" class="form-control" placeholder="" id="total_HM" name="total_HM"
                                required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="plan_total" class="form-label fw-semibold">Plan Total</label>
                            <input type="number" class="form-control" placeholder="56" id="plan_total"
                                name="plan_total" value="56" required readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="progress" class="form-label fw-semibold">Progress</label>
                            <input type="number" class="form-control" placeholder="" id="progress" name="progress"
                                readonly required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="per_progress" class="form-label fw-semibold">% Progress</label>
                            <input type="number" class="form-control" placeholder="" id="per_progress"
                                name="per_progress" required readonly>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="align-items-center text-end mb-5">
                    <button type="button" id="cancelButton" class="btn btn-warning">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #1481FF;" id="uploadButton">
                        Submit
                    </button>
                </div>
            </form>
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
            $('#train_type').select2({
                placeholder: 'Select Training Type',
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

            $('#unit_type').select2({
                placeholder: 'Select Unit Type',
                // minimumInputLength: 2,
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

            $('#unit_type').on('change', function() {
                selectedUnitType = $(this).val();
                $('#unit_class').val(null).trigger('change'); // clear current selection
            });

            $('#unit_class').select2({
                placeholder: 'Select Unit Class',
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

            $("#unit_class").on("select2:select", function(e) {
                selectedModel = e.params.data.text; // or `e.params.data.id` if you use ID
                $("#code").val(null).trigger("change"); // clear current Unit
            });


            $('#code').select2({
                placeholder: 'Select Code',
                // minimumInputLength: 2,
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
            const inputs = ['#train_type', '#unit_class', '#batch', '#HM_start', '#HM_end'];
            inputs.forEach(id => {
                document.querySelector(id).addEventListener('change', handleInputs);
            });

            function handleInputs() {
                calculateTotalHM();
                fetchTotalHM();
            }

            function calculateTotalHM() {
                const start = parseFloat(document.querySelector('#HM_start').value) || 0;
                const end = parseFloat(document.querySelector('#HM_end').value) || 0;

                const totalHM = end - start;
                document.querySelector('#total_HM').value = totalHM > 0 ? totalHM : 0;
            }

            function fetchTotalHM() {
                const jde = document.querySelector('#JDE').value;
                const training_type = document.querySelector('#train_type').value;
                const unit_class = document.querySelector('#unit_class').value;
                const batch = document.querySelector('#batch').value;

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
                                batch
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const totalFromDB = data.total_hm ?? 0;
                            const totalHMInput = parseFloat(document.querySelector('#total_HM').value) || 0;
                            const finalProgress = parseFloat(totalFromDB) + parseFloat(totalHMInput);

                            document.querySelector('#progress').value = finalProgress;

                            const planTotal = parseFloat(document.querySelector('#plan_total').value || 56);
                            const percentProgress = (finalProgress / planTotal) * 100;
                            document.querySelector('#per_progress').value = percentProgress.toFixed(2);
                        })
                        .catch(err => console.error('Error:', err));
                }
            }
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

                // Show loading Swal
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Collect form data
                const formData = $('#upload-form').serialize();

                $.ajax({
                    url: "{{ route('HMTrainStore') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val() // CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Train Hours record created successfully.',
                        }).then(() => {
                            window.location.href = "{{ route('HMTrainIndex') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.error ||
                            'An error occurred. Please contact IT.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                });
            });

            // Clear inputs on Cancel button click
            $('#cancelButton').on('click', function(e) {
                e.preventDefault();
                $('#upload-form')[0].reset(); // Reset the form
                $('#jde').val(null).trigger('change'); // Clear Select2 field
            });

            $('#upload-form').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission for now

                // Show loading Swal
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Train Hours record created successfully.',
                            confirmButtonColor: '#1481FF'
                        }).then(() => {
                            window.location.href = "{{ route('HMTrainIndex') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menyimpan data, harap menghubungi admin atau IT.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>
@endpush

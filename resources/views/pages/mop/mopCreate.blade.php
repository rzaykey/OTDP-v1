@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="card" style="100%">
        <div class="card-body">
            <h4 class="fw-bold mb-4">TAMBAH OPERATOR PERFORMANCE</h4>
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
            <form method="POST" action="{{ route('MOPStore') }}" id="upload-form">
                @csrf
                {{-- JDE --}}
                <label for="jde" class="form-label fw-semibold" style="color: #333536"> JDE No </label>
                <div class="mb-3">
                    <select name="jde" id="jde" class="form-select" style="width:100%" required>
                        <option value="">Search...</option>
                    </select>
                </div>

                {{-- hidden name --}}
                <div class="mb-3" hidden>
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                        readonly>
                </div>

                <div class="mb-3" >
                    <label for="name" class="form-label">Site</label>
                    <input type="text" class="form-control" id="site" name="site" readonly>
                </div>

                {{-- Department and Position --}}
                <fieldset disabled>
                    <div class="row">
                        <div class="col">
                            <label for="departement" class="form-label fw-semibold" style="color: #333536"> Division
                            </label>
                            <input type="text" class="form-control mb-3" placeholder="Division" id="division"
                                name="division" readonly>
                        </div>
                        <div class="col">
                            <label for="position" class="form-label fw-semibold" style="color: #333536"> Position </label>
                            <input type="text" class="form-control mb-3" placeholder="Position" id="position"
                                name="position" readonly>
                        </div>
                    </div>
                </fieldset>

                <!-- Month and Year -->
                <div class="row">
                    <div class="col">
                        <label for="Month" class="form-label fw-semibold" style="color: #333536"> MOP Type </label>
                        <select class="form-select mb-3" aria-label="Select Type" id="moptype" name="moptype" required>
                            <option value="" selected disabled>Select Type</option>
                            <option value="LOADER">LOADER</option>
                            <option value="SUPPORT">SUPPORT</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="Month" class="form-label fw-semibold" style="color: #333536"> Month </label>
                        <select class="form-select mb-3" aria-label="Select Month" id="month" name="month" required>
                            <option value="" selected disabled>Select Month</option>
                            <option value="01" {{ old('month') == '01' ? 'selected' : '' }}>January</option>
                            <option value="02" {{ old('month') == '02' ? 'selected' : '' }}>February</option>
                            <option value="03" {{ old('month') == '03' ? 'selected' : '' }}>March</option>
                            <option value="04" {{ old('month') == '04' ? 'selected' : '' }}>April</option>
                            <option value="05" {{ old('month') == '05' ? 'selected' : '' }}>May</option>
                            <option value="06" {{ old('month') == '06' ? 'selected' : '' }}>June</option>
                            <option value="07" {{ old('month') == '07' ? 'selected' : '' }}>July</option>
                            <option value="08" {{ old('month') == '08' ? 'selected' : '' }}>August</option>
                            <option value="09" {{ old('month') == '09' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ old('month') == '10' ? 'selected' : '' }}>October</option>
                            <option value="11" {{ old('month') == '11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ old('month') == '12' ? 'selected' : '' }}>December</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="Year" class="form-label fw-semibold" style="color: #333536">Year</label>
                        <select class="form-select mb-3" aria-label="Select Year" id="year" name="year" required>
                            <option value="" disabled selected>Select year</option>
                            @for ($y = date('Y'); $y >= 2000; $y--)
                                <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Attendance Ratio, Discipline, and Safety Awareness --}}
                <div class="row">
                    <div class="col">
                        <label for="Attendance" class="form-label fw-semibold" style="color: #333536">Attendance Ratio -
                            %</label>
                        <input type="number" min="0" max="100" class="form-control mb-3"
                            placeholder="Attendance Ratio " id="a_attendance_ratio" name="a_attendance_ratio"
                            value="{{ old('a_attendance_ratio', 0) }}" required>
                    </div>
                    <div class="col">
                        <label for="Discipline" class="form-label fw-semibold" style="color: #333536">Discipline</label>
                        <select class="form-select mb-3" aria-label="Select Discipline" id="b_discipline"
                            name="b_discipline" required>
                            <option value="" selected disabled>Select a discipline</option>
                            <option value="5">Tidak ada</option>
                            <option value="4">ST</option>
                            <option value="3">SP1</option>
                            <option value="2">SP2</option>
                            <option value="1">SP3</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="SafetyAwareness" class="form-label fw-semibold" style="color: #333536">Safety
                            Awareness</label>
                        <select class="form-select mb-3" aria-label="Select Safety Awareness" id="c_safety_awareness"
                            name="c_safety_awareness" required>
                            <option value="" selected disabled>Select a safety awareness </option>
                            <option value="5">Tidak ada</option>
                            <option value="4">EI</option>
                            <option value="3">PD</option>
                            <option value="2">FAC</option>
                            <option value="1">MTI</option>
                            <option value="0">LTI</option>
                        </select>
                    </div>
                </div>

                {{-- Equip type --}}
                <div class="row">
                    @for ($i = 1; $i <= 1; $i++)
                        <div class="col">
                            <label for="equipment_type{{ $i }}" class="form-label fw-semibold"
                                style="color: #333536"> Equipment Type {{ $i }} </label>
                            <input type="text" class="form-control mb-3" placeholder="Type"
                                id="equipment_type{{ $i }}" name="equipment_type{{ $i }}"
                                value="{{ old('equipment_type' . $i) }}"
                                @if ($i == 1) required @endif>
                        </div>
                    @endfor

                    {{-- WH Waste --}}
                    {{-- <div class="row"> --}}
                    @for ($i = 1; $i <= 1; $i++)
                        <div class="col">
                            <label for="d_wh_waste_equiptype{{ $i }}" class="form-label fw-semibold"
                                style="color: #333536"> WH Waste {{ $i }} - % </label>
                            <input type="number" min="0" step="0.01" class="form-control mb-3"
                                placeholder="0" id="d_wh_waste_equiptype{{ $i }}"
                                name="d_wh_waste_equiptype{{ $i }}"
                                value="{{ old('d_wh_waste_equiptype' . $i, 0) }}"
                                @if ($i == 1) required @endif>
                        </div>
                    @endfor

                    {{-- Points --}}
                    {{-- <div class="row"> --}}
                    @for ($i = 1; $i <= 1; $i++)
                        <div class="col">
                            <label for="e_pty_equiptype{{ $i }}" class="form-label fw-semibold"
                                style="color: #333536"> Point {{ $i }} - % </label>
                            <input type="number" min="0" step="0.01" class="form-control mb-3"
                                placeholder="0" id="e_pty_equiptype{{ $i }}"
                                name="e_pty_equiptype{{ $i }}" value="{{ old('e_pty_equiptype1' . $i, 0) }}"
                                @if ($i == 1) required @endif>
                        </div>
                    @endfor
                </div>

                {{-- counts --}}
                <div class="row">
                    <div class="col">
                        <label for="PointEligibilitas" class="form-label fw-semibold" style="color: #333536">
                            Point Eligibilitas (45%) </label>
                        <input type="number" step="0.01" step="0.01" class="form-control mb-3" placeholder="0"
                            id="point_eligibilitas" name="point_eligibilitas" value="{{ old('point_eligibilitas', 0) }}"
                            readonly>
                    </div>
                    <div class="col">
                        <label for="PointProduksi" class="form-label fw-semibold" style="color: #333536"> + Point
                            Produksi (55%) </label>
                        <input type="number" step="0.01" class="form-control mb-3" placeholder="0"
                            id="point_produksi" name="point_produksi" value="{{ old('point_produksi', 0) }}" readonly>
                    </div>
                    <div class="col">
                        <label for="TotalPoint" class="form-label fw-semibold" style="color: #333536"> = Total
                            Point </label>
                        <input type="number" step="0.01" class="form-control mb-3" placeholder="0" id="total_point"
                            name="total_point" value="{{ old('total_point', 0) }}" readonly>
                    </div>
                    <div class="col">
                        <label for="mop_bulanan_grade" class="form-label fw-semibold" style="color: #333536"> MOP
                            Bulanan Grade </label>
                        <input type="text" class="form-control mb-3" placeholder="Grade"
                            value="{{ old('mop_bulanan_grade') }}" id="mop_bulanan_grade" name="mop_bulanan_grade"
                            readonly>
                    </div>
                </div>

                {{-- hidden to calculate --}}
                <!-- Points and Eligibility Fields -->
                <div class="mb-4 flex flex-wrap space-x-2" style="display: none;">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="flex-1 min-w-[150px]" style="margin-right:3px">
                            <label for="pointkalkulasi{{ $i }}"
                                class="block text-gray-700 font-semibold">POINTKALKULASI
                                {{ $i }}</label>
                            <input type="number" step="0.01" name="pointkalkulasi{{ $i }}"
                                id="pointkalkulasi{{ $i }}"
                                class="w-full border border-gray-300 p-2 rounded-lg mt-1"
                                value="{{ old('pointkalkulasi' . $i, 0) }}">
                        </div>
                    @endfor
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
            calculateAttendanceRatio1Value();
            calculateWHWaste4Value();
            calculatePoint5Value();
            calculatePoints(); // Final calculation to update total points and grade
        });

        // Load Data Employee Operator Master
        $('#jde').select2({
            placeholder: 'Search...',
            allowClear: true,
            ajax: {
                url: "{{ route('employee.operator') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.employeeId,
                                text: item.employeeId + ' - ' + item.EmployeeName + ' - ' + item
                                    .LocationGroupName,
                                additionalData: {
                                    name: item.EmployeeName,
                                    division: item.DivisionName,
                                    position: item.PositionName,
                                    site: item.LocationGroupName
                                }
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Handle data update when selection changes
        $('#jde').on('select2:select', function(e) {
            const selectedData = e.params.data.additionalData;

            $('#name').val(selectedData.name || '');
            $('#division').val(selectedData.division || '');
            $('#position').val(selectedData.position || '');
            $('#site').val(selectedData.site || '');
        });

        // alert if attendance ratio bigger than 100
        document.getElementById('a_attendance_ratio').addEventListener('input', function(e) {
            const value = parseInt(e.target.value, 10);

            if (value > 100) {
                alert('Please enter a value between 0 and 100.');
                e.target.value = ''; // Clear the input if invalid
            }
        });

        $("#b_discipline").on("change", function() {
            // $("#b_discipline").change(function () {
            // Get the selected value from the dropdown
            var selectedValue = $(this).val();
            // Set the value of POINTKALKULASI 2
            $("#pointkalkulasi2").val(selectedValue);
            calculatePoints();
            console.log(selectedValue);
        });

        $("#c_safety_awareness").on("change", function() {
            // $("#b_discipline").change(function () {
            // Get the selected value from the dropdown
            var selectedValue = $(this).val();
            // Set the value of POINTKALKULASI 2
            $("#pointkalkulasi3").val(selectedValue);
            calculatePoints();
            console.log(selectedValue);
        });

        function calculateAttendanceRatio1Value() {
            // Get the attendance ratio value
            let attendanceRatio = parseFloat($("#a_attendance_ratio").val()) || 0;
            let pointKalkulasi1;
            // Determine the value based on the attendance ratio ranges
            if (attendanceRatio <= 96) {
                pointKalkulasi1 = 1;
            } else if (attendanceRatio > 96 && attendanceRatio <= 97) {
                pointKalkulasi1 = 2;
            } else if (attendanceRatio > 97 && attendanceRatio <= 98) {
                pointKalkulasi1 = 3;
            } else if (attendanceRatio > 98 && attendanceRatio <= 99) {
                pointKalkulasi1 = 4;
            } else if (attendanceRatio > 99) {
                pointKalkulasi1 = 5;
            }

            // Update POINTKALKULASI 1 field
            $("#pointkalkulasi1").val(pointKalkulasi1);
            calculatePoints();
            console.log(pointKalkulasi1);
        }

        function calculateWHWaste4Value() {

            // Sum all WH Waste inputs with data-waste attribute
            let sum = 0;
            $('input[id^="d_wh_waste_equiptype"]').each(function() {
                sum += parseFloat($(this).val()) || 0;
            });

            // Determine value based on sum ranges
            let calculatedValue4;
            if (sum < 3) {
                calculatedValue4 = 1;
            } else if (sum >= 3 && sum <= 4.5) {
                calculatedValue4 = 2;
            } else if (sum > 4.5 && sum <= 6) {
                calculatedValue4 = 3;
            } else if (sum > 6 && sum <= 7.5) {
                calculatedValue4 = 4;
            } else if (sum > 7.5) {
                calculatedValue4 = 5;
            }

            // Update the result input with the calculated value
            $("#pointkalkulasi4").val(calculatedValue4);
            calculatePoints();
            console.log(sum);
            console.log(calculatedValue4);
        }

        function calculatePoint5Value() {
            // Sum all WH Waste inputs with data-waste attribute
            let sum = 0;
            $('input[id^="e_pty_equiptype"]').each(function() {
                sum += parseFloat($(this).val()) || 0;
            });

            // Determine value based on sum ranges
            let calculatedValue5;
            if (sum < 76) {
                calculatedValue5 = 1;
            } else if (sum >= 76 && sum <= 84) {
                calculatedValue5 = 2;
            } else if (sum > 84 && sum <= 94) {
                calculatedValue5 = 3;
            } else if (sum > 94 && sum <= 99) {
                calculatedValue5 = 4;
            } else if (sum > 99) {
                calculatedValue5 = 5;
            }

            // Update the result input with the calculated value
            $("#pointkalkulasi5").val(calculatedValue5);
            calculatePoints();
            console.log(sum);
            console.log(pointkalkulasi5);
        }

        function calculatePoints() {
            // Get values from POINTKALKULASI fields
            let point1 = parseFloat($("#pointkalkulasi1").val()) || 0;
            let point2 = parseFloat($("#pointkalkulasi2").val()) || 0;
            let point3 = parseFloat($("#pointkalkulasi3").val()) || 0;
            let point4 = parseFloat($("#pointkalkulasi4").val()) || 0;
            let point5 = parseFloat($("#pointkalkulasi5").val()) || 0;
            console.log(point1 + ' ' + point2 + ' ' + point3 + ' ' + point4 + ' ' + point5)
            // Calculate point_eligibilitas and point_produksi
            let point_eligibilitas = ((point1 + point2 + point3) / 3) * 0.45;
            let point_produksi = ((point4 + point5) / 2) * 0.55;

            // Update the point_eligibilitas and point_produksi fields
            $("#point_eligibilitas").val(point_eligibilitas.toFixed(2));
            $("#point_produksi").val(point_produksi.toFixed(2));

            // Calculate total_point
            let total_point = point_eligibilitas + point_produksi;
            $("#total_point").val(total_point.toFixed(2));

            // Determine mop_bulanan_grade based on total_point
            let mop_bulanan_grade;
            if (total_point < 2) {
                mop_bulanan_grade = "K";
            } else if (total_point >= 2.0 && total_point <= 2.49) {
                mop_bulanan_grade = "C";
            } else if (total_point >= 2.5 && total_point <= 2.99) {
                mop_bulanan_grade = "C+";
            } else if (total_point >= 3 && total_point <= 3.49) {
                mop_bulanan_grade = "B";
            } else if (total_point >= 3.5 && total_point <= 3.99) {
                mop_bulanan_grade = "B+";
            } else if (total_point >= 4 && total_point <= 4.49) {
                mop_bulanan_grade = "BS";
            } else if (total_point >= 4.5 && total_point <= 4.75) {
                mop_bulanan_grade = "BS+";
            } else if (total_point >= 4.5) {
                mop_bulanan_grade = "ISTIMEWA";
            }

            // Update the mop_bulanan_grade field
            $("#mop_bulanan_grade").val(mop_bulanan_grade);
        }

        // Trigger calculation on any input change
        $("#a_attendance_ratio").on("input", calculateAttendanceRatio1Value);
        $('input[id^="d_wh_waste_equiptype"]').on("input", calculateWHWaste4Value);
        $('input[id^="e_pty_equiptype"]').on("input", calculatePoint5Value);

        // Trigger calculation on input change in POINTKALKULASI fields
        $('input[id^="pointkalkulasi"]').on("change", calculatePoints);

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

                // Collect form data
                const formData = $('#upload-form').serialize();

                $.ajax({
                    url: "{{ route('MOPStore') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val() // CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Operation Performance record created successfully.',
                        }).then(() => {
                            window.location.href = "{{ route('MOPCreate') }}";
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
        });


        $('#upload-form').on('submit', function(e) {
            e.preventDefault(); // Prevent form submission for now

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Operation Performance record created successfully.',
                        confirmButtonColor: '#1481FF'
                    }).then(() => {
                        window.location.href =
                            "{{ route('MOPCreate') }}"; // Redirect after success
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menyimpan data, harap menghubungi admin atau IT.';

                    // Check if response has JSON error details
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        // Display raw error text for debugging
                        errorMessage = xhr.responseText;
                    }

                    console.error('Error details:', xhr); // Log detailed error to console

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    </script>
@endpush

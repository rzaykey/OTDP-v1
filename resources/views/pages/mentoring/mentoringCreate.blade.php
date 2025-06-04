@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        @media (max-width: 768px) {
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
            }

            th,
            td {
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    <input type="hidden" value="{{ env('server') }}" id="ServerDev">
    <input type="hidden" value="{{ Auth::user()->role }}" id="role">
    <div class="card container py-3 px-2 shadow-sm">
        @include('components.alert')
        <div class="card-body">
            <h3 class="fw-bold mb-3">FORM TRAINER {{ $type }}</h3>
            <form method="POST" id="form">

                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <input type="hidden" id="IDTypeMentoring" name="IDTypeMentoring" value="{{ $type }}">
                            <label for="IDtrainer" class="form-label fw-semibold">ID Trainer</label>
                            <input type="text" class="form-control" placeholder="" id="IDtrainer" name="IDtrainer"
                                value="{{ $employeeAuth->EmployeeId }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="trainer" class="form-label fw-semibold">Trainer</label>
                            <input type="text" class="form-control" placeholder="" id="trainer" name="trainer"
                                value="{{ $employeeAuth->EmployeeName }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="IDoperator">ID Operator</label>
                        <select id="IDoperator" name="IDoperator" class="form-select js-operator-select"
                            data-width="100%"></select>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="operator" class="form-label fw-semibold">Operator</label>
                            <input type="text" class="form-control" placeholder="" id="operator" name="operator"
                                readonly required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="site" class="form-label fw-semibold">Site</label>
                            <input type="text" class="form-control" placeholder="" id="site" name="site"
                                value="{{ $employeeAuth->LocationGroupName }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="area" class="form-label fw-semibold">Area</label>
                            {{-- <select id="area" name="area" class="form-select js-operator-select"
                                data-width="100%"></select> --}}
                            <input type="text" class="form-control" placeholder="" id="area" name="area"
                                required>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Type Unit</label>
                            <select id="type" name="type" class="form-select js-operator-select" data-width="100%">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="model" class="form-label fw-semibold">Model Unit</label>
                            <select id="model" name="model" class="form-select js-operator-select"
                                data-width="100%"></select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="unit" class="form-label fw-semibold">No. Unit</label>
                            <select id="unit" name="unit" class="form-select js-operator-select"
                                data-width="100%"></select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control" placeholder="" id="date" name="date"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="time_start" class="form-label fw-semibold">Jam Awal</label>
                            <input type="time" class="form-control" placeholder="" id="time_start" name="time_start"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="time_end" class="form-label fw-semibold">Jam Akhir</label>
                            <input type="time" class="form-control" placeholder="" id="time_end" name="time_end"
                                required>
                        </div>
                    </div>
                </div>

                <hr class="solid">

                <div id="mentoring-container" data-sections='@json($points)'>
                    @php
                        $counter = 1;
                        $urut = 1;
                    @endphp
                    @foreach ($data as $section => $indicators)
                        <div class="col-12">
                            <h4 class="fw-bold mb-2 mt-3" style="background-color: cyan"> {{ $counter }}.
                                {{ $section }}</h4>
                            @foreach ($indicators as $indicator)
                                <div class="row border p-2 mb-2">
                                    <div class="col-md-3 col-sm-6" style="margin:2px">
                                        <strong>Indikator Perilaku:</strong><br />{{ $indicator->param1 }}
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin:2px">
                                        <strong>Uraian Mentoring:</strong><br />{{ $indicator->param2 }}
                                    </div>
                                    <div class="col-md-1 col-sm-2 text-center" style="margin:6px">
                                        Observasi: <input type="checkbox"
                                            name="observasi{{ $section }}{{ $urut }}" value="1"
                                            class="form-check-input observation-checkbox">
                                        Mentoring: <input type="checkbox"
                                            name="mentoring{{ $section }}{{ $urut }}" value="1"
                                            class="form-check-input mentoring-checkbox">

                                    </div>
                                    <div class="col-md-3" style="margin:2px">
                                        <textarea name="catatan{{ $section }}{{ $urut }}" rows="2" class="form-control"
                                            placeholder="Catatan Observasi"></textarea>
                                    </div>
                                </div>
                                @php $urut++; @endphp
                            @endforeach
                        </div>
                        @php $counter++; @endphp
                    @endforeach
                    <hr class="solid">
                    <!-- Point Observasi & Mentoring -->
                    <div id="mentoring-container" data-sections='@json($points)'>
                        <div class="row">
                            <!-- POINT OBSERVASI -->
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-bold mt-6 mb-2" style="background-color: cyan">POINT OBSERVASI</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-sm text-left text-gray-700 align-middle w-100"
                                        style="min-width: 100%;">
                                        <thead class="table-secondary">
                                            <tr style="font-size: 12px">
                                                <th class="px-4 py-2 border">Mentoring Focus</th>
                                                <th class="px-4 py-2 border text-center">Y Score</th>
                                                <th class="px-4 py-2 border text-center">Point</th>
                                            </tr>
                                        </thead>
                                        <tbody id="points-observasi-body">
                                            @foreach ($points as $section => $values)
                                                <tr style="font-size: 12px">
                                                    <td class="px-4 py-2 border">{{ $section }}</td>
                                                    <td class="px-4 py-2 border text-center y-score-observasi"
                                                        data-section="{{ $section }}">
                                                        <input type="text" name="observasi{{ $section }}YScore"
                                                            class="form-control text-center small-input"
                                                            value="{{ old($section . 'ObservasiYScore') ?? '0' }}"
                                                            readonly>
                                                    </td>
                                                    <td class="px-4 py-2 border text-center point-score-observasi"
                                                        data-section="{{ $section }}">
                                                        <input type="text" name="observasi{{ $section }}Point"
                                                            class="form-control text-center small-input"
                                                            value="{{ old($section . 'ObservasiPoint') ?? '0' }}"
                                                            readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-light font-bold" style="font-size: 12px">
                                                <td class="px-4 py-2 border text-right">AVERAGE POINT</td>
                                                <td class="px-4 py-2 border text-center">
                                                    <input type="text" id="average-y-score-observasi"
                                                        class="form-control form-control-sm text-center mentoring-y-score"
                                                        style="width:7em" readonly>
                                                </td>
                                                <td class="px-4 py-2 border text-center">
                                                    <input type="text" id="average-point-score-observasi"
                                                        class="form-control form-control-sm text-center mentoring-y-score"
                                                        style="width:7em" readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- POINT MENTORING -->
                            <div class="col-md-6 col-sm-12">
                                <h4 class=" font-bold mt-6 mb-2" style="background-color: cyan">POINT MENTORING</h4>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-bordered text-sm" style="min-width: 100%;">
                                        <thead class="table-secondary" style="font-size: 12px">
                                            <tr>
                                                <th class="px-4 py-2 border">Mentoring Focus</th>
                                                <th class="px-4 py-2 border text-center">Y Score</th>
                                                <th class="px-4 py-2 border text-center">Point</th>
                                            </tr>
                                        </thead>
                                        <tbody id="points-mentoring-body">
                                            @foreach ($points as $section => $values)
                                                <tr style="font-size: 12px">
                                                    <td class="px-4 py-2 border">{{ $section }}</td>

                                                    <td class="px-4 py-2 border text-center y-score-mentoring"
                                                        data-section="mentoring-{{ $section }}">
                                                        <input type="text" name="mentoring{{ $section }}YScore"
                                                            class="form-control text-center mentoring-y-score"
                                                            value="{{ old($section . 'MentoringYScore') ?? '0' }}"
                                                            readonly>
                                                    </td>

                                                    <td class="px-4 py-2 border text-center point-score-mentoring"
                                                        data-section="mentoring-{{ $section }}">
                                                        <input type="text" name="mentoring{{ $section }}Point"
                                                            class="form-control text-center mentoring-point-score"
                                                            value="{{ old($section . 'MentoringPoint') ?? '0' }}"
                                                            readonly>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr class="table-light font-bold" style="font-size: 12px">
                                                <td class="px-4 py-2 border text-right">AVERAGE POINT</td>
                                                <td class="px-4 py-2 border text-center">
                                                    <input type="text" id="average-y-score-mentoring"
                                                        class="form-control form-control-sm text-center mentoring-y-score"
                                                        style="width:7em" readonly>
                                                </td>
                                                <td class="px-4 py-2 border text-center">
                                                    <input type="text" id="average-point-score-mentoring"
                                                        class="form-control form-control-sm text-center mentoring-y-score"
                                                        style="width:7em" readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="align-items-center text-end mt-3">
                            <button type="submit" class="btn btn-primary rounded fw-bold"
                                style="background-color: #1481FF;" id="uploadButton">
                                {{ isset($record) ? 'Change' : 'Submit' }}
                            </button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection


@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#IDoperator").select2({
                placeholder: "Select Operator",
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('employee.operator') }}",
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
                                    id: item.employeeId,
                                    text: item.employeeId + " - " + item.EmployeeName,
                                    employeeName: item.EmployeeName,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                templateSelection: function(data) {
                    return data.id || data.text;
                },
            });

            $("#IDoperator").on("select2:select", function(e) {
                const selected = e.params.data;
                $("#operator").val(selected.employeeName);
            });

            const urlSegments = window.location.pathname.split("/");
            const typeFromUrl = urlSegments[urlSegments.length - 1];
            const formattedType =
                typeFromUrl.charAt(0).toUpperCase() +
                typeFromUrl.slice(1).toLowerCase();

            // Append the option manually
            const newOption = new Option(formattedType, formattedType, true, true);
            $("#type").append(newOption).trigger("change");

            // Now manually set selectedType since select2:select will not fire automatically
            let selectedType = formattedType;

            // OPTIONAL: If you need the event to fire for other listeners (not your current case)
            $("#type").trigger({
                type: "select2:select",
                params: {
                    data: {
                        id: formattedType,
                        text: formattedType,
                    },
                },
            });

            $("#model").select2({
                placeholder: "Select Model Unit",
                ajax: {
                    url: "{{ route('unit.modelunitbasedtype') }}",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: selectedType, // Pass selected type unit to backend
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.model,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });

            let selectedModel = null;

            $("#model").on("select2:select", function(e) {
                selectedModel = e.params.data.text; // or `e.params.data.id` if you use ID
                $("#unit").val(null).trigger("change"); // clear current Unit
            });

            $("#unit").select2({
                placeholder: "Select Unit",
                ajax: {
                    url: "{{ route('unit.unit') }}",
                    dataType: "json",
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
                                    text: item.no_unit, // change if field name is different
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            let mentoringContainer = document.getElementById("mentoring-container");
            let sections = JSON.parse(mentoringContainer.getAttribute("data-sections"));

            function updateScores() {
                let totalYScoreObservasi = 0,
                    totalPointScoreObservasi = 0;
                let totalYScoreMentoring = 0,
                    totalPointScoreMentoring = 0;
                let totalSections = Object.keys(sections).length;

                Object.keys(sections).forEach((section) => {
                    // OBSERVATION
                    let checkedObservasiItems = document.querySelectorAll(
                        `input[name^="observasi${section}"]:checked`
                    ).length;

                    let yScoreObservasi = checkedObservasiItems;
                    let pointScoreObservasi = yScoreObservasi * 12.5;

                    let observasiYScoreElem = document.querySelector(
                        `.y-score-observasi[data-section="${section}"] input`
                    );
                    let observasiPointElem = document.querySelector(
                        `.point-score-observasi[data-section="${section}"] input`
                    );

                    if (observasiYScoreElem)
                        observasiYScoreElem.value = yScoreObservasi;
                    if (observasiPointElem)
                        observasiPointElem.value = pointScoreObservasi.toFixed(1);

                    totalYScoreObservasi += yScoreObservasi;
                    totalPointScoreObservasi += pointScoreObservasi;

                    // MENTORING
                    let checkedMentoringItems = document.querySelectorAll(
                        `input[name^="mentoring${section}"]:checked`
                    ).length;

                    let yScoreMentoring = checkedMentoringItems;
                    let pointScoreMentoring = yScoreMentoring * 12.5;

                    let mentoringYScoreElem = document.querySelector(
                        `.y-score-mentoring[data-section="mentoring-${section}"] input`
                    );
                    let mentoringPointElem = document.querySelector(
                        `.point-score-mentoring[data-section="mentoring-${section}"] input`
                    );

                    if (mentoringYScoreElem)
                        mentoringYScoreElem.value = yScoreMentoring;
                    if (mentoringPointElem)
                        mentoringPointElem.value = pointScoreMentoring.toFixed(1);

                    totalYScoreMentoring += yScoreMentoring;
                    totalPointScoreMentoring += pointScoreMentoring;
                });

                // Averages Calculation
                document.getElementById("average-y-score-observasi").value =
                    totalYScoreObservasi;
                document.getElementById("average-point-score-observasi").value = (
                    totalPointScoreObservasi / totalSections
                ).toFixed(1);

                document.getElementById("average-y-score-mentoring").value =
                    totalYScoreMentoring;
                document.getElementById("average-point-score-mentoring").value = (
                    totalPointScoreMentoring / totalSections
                ).toFixed(1);
            }

            // Add event listeners
            document
                .querySelectorAll(".observation-checkbox, .mentoring-checkbox")
                .forEach((checkbox) => {
                    checkbox.addEventListener("change", updateScores);
                });

            // Initialize scores on page load
            updateScores();
        });

        document.getElementById("form").addEventListener("submit", function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this); // Collect form data

            // Show SweetAlert loading indicator
            Swal.fire({
                title: "Please wait...",
                text: "Submitting your data...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            fetch("{{ route('MentoringStore') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: formData,
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Data successfully submitted!",
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            // Optional: Refresh the page or redirect after success
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: data.message ||
                                "An error occurred while submitting data.",
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Failed!",
                        text: "Failed to submit data. Please try again later.",
                    });
                });
        });
    </script>
@endpush

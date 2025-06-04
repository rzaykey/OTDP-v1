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
            <h3 class="fw-bold mb-3">FORM TRAINER {{ $header->type_mentoring }}</h3>
            <form method="POST" id="form">
                @csrf

                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <input type="hidden" id="edit_id" name="edit_id"
                                value="{{ old('edit_id', $header->id ?? '') }}">
                            <input type="hidden" id="edit_IDTypeMentoring" name="edit_IDTypeMentoring"
                                value="{{ $header->type_mentoring }}">
                            <label for="edit_IDtrainer" class="form-label fw-semibold">ID Trainer</label>
                            <input type="text" class="form-control" id="edit_IDtrainer" name="edit_IDtrainer"
                                value="{{ old('edit_IDtrainer', $header->trainer_jde ?? '') }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="edit_trainer" class="form-label fw-semibold">Trainer</label>
                            <input type="text" class="form-control" id="edit_trainer" name="edit_trainer"
                                value="{{ old('trainer', $header->trainer_name ?? '') }}" readonly required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="edit_IDoperator">ID Operator</label>
                        <select id="edit_IDoperator" name="edit_IDoperator" class="form-select js-operator-select"
                            data-width="100%">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="edit_operator" class="form-label fw-semibold">Operator</label>
                            <input type="text" class="form-control" id="edit_operator" name="edit_operator"
                                value="{{ old('operator', $header->operator_name ?? '') }}" readonly required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_site" class="form-label fw-semibold">Site</label>
                            <input type="text" class="form-control" id="edit_site" name="edit_site"
                                value="{{ old('site', $header->site ?? '') }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_area" class="form-label fw-semibold">Area</label>
                            <input type="text" class="form-control" id="edit_area" name="edit_area"
                                value="{{ old('area', $header->area ?? '') }}" required>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_type" class="form-label fw-semibold">Type Unit</label>
                            {{-- <select id="edit_type" name="edit_type" class="form-select js-operator-select" data-width="100%">
                            </select> --}}
                            <input type="text" class="form-control" id="edit_type" name="edit_type"
                                value="{{ old('type', $header->unit_type ?? '') }}" required>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_model" class="form-label fw-semibold">Model Unit</label>
                            <select id="edit_model" name="edit_model" class="form-select js-operator-select"
                                data-width="100%">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_unit" class="form-label fw-semibold">No. Unit</label>
                            <select id="edit_unit" name="edit_unit" class="form-select js-operator-select"
                                data-width="100%">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_date" class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control" id="edit_date" name="edit_date"
                                value="{{ old('edit_date', isset($header->date_mentoring) ? \Carbon\Carbon::parse($header->date_mentoring)->format('Y-m-d') : '') }}"
                                required>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_time_start" class="form-label fw-semibold">Jam Awal</label>
                            <input type="time" class="form-control" id="edit_time_start" name="edit_time_start"
                                value="{{ old('edit_time_start', $header->start_time ?? '') }}" required>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="mb-3">
                            <label for="edit_time_end" class="form-label fw-semibold">Jam Akhir</label>
                            <input type="time" class="form-control" id="edit_time_end" name="edit_time_end"
                                value="{{ old('edit_time_end', $header->end_time ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <hr class="solid">

                {{-- Mentoring Sections --}}
                <div id="mentoring-container" data-sections='@json($points)'>

                    @php
                        $counter = 1;
                    @endphp

                    @foreach ($data as $section => $indicators)
                        <div class="col-12">
                            <h4 class="fw-bold mb-2 mt-3" style="background-color: cyan">
                                {{ $counter }}. {{ $section }}
                            </h4>
                            @foreach ($indicators as $indicator)
                                @php
                                    $detail = $details->firstWhere('fid_indicator', $indicator->id);
                                    $obs = $penilaian->firstWhere(
                                        fn($p) => $p->indicator == $indicator->id && $p->type_penilaian == 'observasi',
                                    );
                                    $mnt = $penilaian->firstWhere(
                                        fn($p) => $p->indicator == $indicator->id && $p->type_penilaian == 'mentoring',
                                    );
                                @endphp
                                <div class="row border p-2 mb-2">
                                    <div class="col-md-3 col-sm-6" style="margin:2px">
                                        <strong>Indikator Perilaku:</strong><br />{{ $indicator->param1 }}
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin:2px">
                                        <strong>Uraian Mentoring:</strong><br />{{ $indicator->param2 }}
                                    </div>
                                    <div class="col-md-1 col-sm-2 text-center" style="margin:6px">
                                        Observasi:
                                        <input type="checkbox" name="edit_observasi[{{ $indicator->id }}]"
                                            value="1" class="form-check-input observation-checkbox"
                                            data-section="{{ $section }}"
                                            {{ old("observasi.{$indicator->id}", $detail?->is_observasi) ? 'checked' : '' }}>
                                        Mentoring:
                                        <input type="checkbox" name="edit_mentoring[{{ $indicator->id }}]"
                                            value="1" class="form-check-input mentoring-checkbox"
                                            data-section="{{ $section }}"
                                            {{ old("mentoring.{$indicator->id}", $detail?->is_mentoring) ? 'checked' : '' }}>
                                    </div>
                                    <div class="col-md-3" style="margin:2px">
                                        <label class="form-label">Catatan Observasi:</label>
                                        <textarea name="edit_note_observasi[{{ $indicator->id }}]" rows="2" class="form-control"
                                            placeholder="Catatan">{{ old("note_observasi.{$indicator->id}", $detail?->note_observasi) }}</textarea>

                                        {{-- <label class="form-label mt-2">Catatan Mentoring:</label>
                                        <textarea name="note_mentoring[{{ $indicator->id }}]" rows="2" class="form-control"
                                            placeholder="Catatan">{{ old("note_mentoring.{$indicator->id}", $detail?->note_mentoring) }}</textarea> --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @php $counter++; @endphp
                    @endforeach


                    <hr class="solid">

                    {{-- Point Observasi & Mentoring Tables --}}
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
                                                    <input type="text" name="edit_observasi{{ $section }}YScore"
                                                        class="form-control text-center small-input"
                                                        value="{{ old($section . 'ObservasiYScore') ?? '0' }}" readonly>
                                                </td>
                                                <td class="px-4 py-2 border text-center point-score-observasi"
                                                    data-section="{{ $section }}">
                                                    <input type="text" name="edit_observasi{{ $section }}Point"
                                                        class="form-control text-center small-input"
                                                        value="{{ old($section . 'ObservasiPoint') ?? '0' }}" readonly>
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
                            <h4 class="font-bold mt-6 mb-2" style="background-color: cyan">POINT MENTORING</h4>
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
                                                    <input type="text" name="edit_mentoring{{ $section }}YScore"
                                                        class="form-control text-center mentoring-y-score"
                                                        value="{{ old($section . 'MentoringYScore') ?? '0' }}" readonly>
                                                </td>

                                                <td class="px-4 py-2 border text-center point-score-mentoring"
                                                    data-section="mentoring-{{ $section }}">
                                                    <input type="text" name="edit_mentoring{{ $section }}Point"
                                                        class="form-control text-center mentoring-point-score"
                                                        value="{{ old($section . 'MentoringPoint') ?? '0' }}" readonly>
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
                        <button type="submit" class="btn btn-primary rounded fw-bold" style="background-color: #1481FF;"
                            id="uploadButton">
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
            $("#edit_IDoperator").select2({
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

            $("#edit_IDoperator").on("select2:select", function(e) {
                const selected = e.params.data;
                $("#edit_operator").val(selected.employeeName);
            });

            $("#edit_model").select2({
                placeholder: "Select Model Unit",
                ajax: {
                    url: "{{ route('unit.modelunitbasedtype') }}",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: $('#type').val(), // Pass selected type unit to backend
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

            $("#edit_model").on("select2:select", function(e) {
                selectedModel = e.params.data.text; // or `e.params.data.id` if you use ID
                $("#edit_unit").val(null).trigger("change"); // clear current Unit
            });

            $("#edit_unit").select2({
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

            const getModel = @json($getModel);
            const getUnit = @json($getUnit);
            // const getHeader = @json($header);
            const selectedOperatorJDE = @json($header->operator_jde ?? '');
            const selectedModelId = @json($header->unit_model ?? '');
            const selectedUnitId = @json($header->unit_number ?? '');

            if (selectedOperatorJDE) {
                const newOption = new Option(selectedOperatorJDE, selectedOperatorJDE, true, true);
                $('#edit_IDoperator').append(newOption).trigger('change');
            }

            const selected_model = getModel.find(item => item.id == selectedModelId);
            if (selected_model) {
                const newModel = new Option(selected_model.model, selected_model.id, true, true);
                $('#edit_model').append(newModel).trigger('change');
            }

            const selected_unit = getUnit.find(item => item.id == selectedUnitId);
            if (selected_unit) {
                const newUnit = new Option(selected_unit.no_unit, selected_unit.id, true, true);
                $('#edit_unit').append(newUnit).trigger('change');
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            let mentoringContainer = document.getElementById("mentoring-container");
            let sections = JSON.parse(mentoringContainer.getAttribute("data-sections"));

            function updateScores() {
                let totalSections = Object.keys(sections).length;

                let yScoreObservasiPerSection = {};
                let pointScoreObservasiPerSection = {};
                let yScoreMentoringPerSection = {};
                let pointScoreMentoringPerSection = {};

                Object.keys(sections).forEach(section => {
                    // Count checked observation checkboxes in this section
                    let checkedObservasiItems = document.querySelectorAll(
                        `.observation-checkbox[data-section="${section}"]:checked`
                    ).length;

                    yScoreObservasiPerSection[section] = checkedObservasiItems;
                    pointScoreObservasiPerSection[section] = checkedObservasiItems * 12.5;

                    // Count checked mentoring checkboxes in this section
                    let checkedMentoringItems = document.querySelectorAll(
                        `.mentoring-checkbox[data-section="${section}"]:checked`
                    ).length;

                    yScoreMentoringPerSection[section] = checkedMentoringItems;
                    pointScoreMentoringPerSection[section] = checkedMentoringItems * 12.5;
                });

                // Update table inputs per section (observasi)
                Object.keys(yScoreObservasiPerSection).forEach(section => {
                    let observasiYScoreElem = document.querySelector(
                        `.y-score-observasi[data-section="${section}"] input`
                    );
                    let observasiPointElem = document.querySelector(
                        `.point-score-observasi[data-section="${section}"] input`
                    );

                    if (observasiYScoreElem)
                        observasiYScoreElem.value = yScoreObservasiPerSection[section];
                    if (observasiPointElem)
                        observasiPointElem.value = pointScoreObservasiPerSection[section].toFixed(1);
                });

                // Update table inputs per section (mentoring)
                Object.keys(yScoreMentoringPerSection).forEach(section => {
                    let mentoringYScoreElem = document.querySelector(
                        `.y-score-mentoring[data-section="mentoring-${section}"] input`
                    );
                    let mentoringPointElem = document.querySelector(
                        `.point-score-mentoring[data-section="mentoring-${section}"] input`
                    );

                    if (mentoringYScoreElem)
                        mentoringYScoreElem.value = yScoreMentoringPerSection[section];
                    if (mentoringPointElem)
                        mentoringPointElem.value = pointScoreMentoringPerSection[section].toFixed(1);
                });

                // Calculate totals and averages
                let totalYScoreObservasi = Object.values(yScoreObservasiPerSection).reduce((a, b) => a + b, 0);
                let totalPointScoreObservasi = Object.values(pointScoreObservasiPerSection).reduce((a, b) => a + b,
                    0);
                let totalYScoreMentoring = Object.values(yScoreMentoringPerSection).reduce((a, b) => a + b, 0);
                let totalPointScoreMentoring = Object.values(pointScoreMentoringPerSection).reduce((a, b) => a + b,
                    0);

                document.getElementById("average-y-score-observasi").value = totalYScoreObservasi;
                document.getElementById("average-point-score-observasi").value = (totalPointScoreObservasi /
                    totalSections).toFixed(1);

                document.getElementById("average-y-score-mentoring").value = totalYScoreMentoring;
                document.getElementById("average-point-score-mentoring").value = (totalPointScoreMentoring /
                    totalSections).toFixed(1);
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

        document.getElementById('form').addEventListener('submit', function(event) {
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

            fetch(`{{ route('MentoringUpdate') }}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: formData,
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
                            // .then(() => location.reload());
                            .then(() => {
                                window.location.href =
                                    "{{ route('MentoringIndex') }}"; // Redirect after success
                            });

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
    </script>
@endpush

// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

let LinkGetTrainHourData = baseUrl + "api/trainhour-data";
let LinkGetTrainHourDataEdit = baseUrl + "Trainer/hmtrain-hours/{id}/edit";
let LinkTrainHourDataUpdate = baseUrl + "Trainer/hmtrain-hours/update";
let LinkTrainHourImport = baseUrl + "Trainer/hmtrain-hours/import-dayact";

let LinkgetActivity = baseUrl + "api/getActivity";
let LinkgetKPI = baseUrl + "api/getKPI";
let LinkgetTotalHM = baseUrl + "api/getTotalHM";
let LinkgetMasterClassUnit = baseUrl + "api/getMasterClassUnit";
let LinkgetMasterTypeUnit = baseUrl + "api/getMasterTypeUnit";
let LinkgetMasterModelUnit = baseUrl + "api/getMasterModelUnit";
let LinkgetMasterModelUnitbasedType =
    baseUrl + "api/getMasterModelUnitbasedType";
let LinkgetMasterUnit = baseUrl + "api/getMasterUnit";

$(document).ready(function () {
    const getClassUnit = JSON.parse($("#getClassJson").val());
    const getTypeUnit = JSON.parse($("#getTypeJson").val());
    const getCode = JSON.parse($("#getCodeJson").val());
    const authJDE = $("#authJDE").val();

    const $loader = $("#loader");
    const $table = $("#trainTable");

    const initDataTable = () => {
        const table = $table.DataTable({
            paging: true,
            ordering: true,
            info: true,
            searching: true,
            orderCellsTop: true,
            fixedHeader: true,
        });

        $table.find("thead tr:eq(1) th").each(function (i) {
            $("input", this).on("keyup change", function () {
                if (table.column(i).search() !== this.value) {
                    table.column(i).search(this.value).draw();
                }
            });
        });
    };

    const loadTrainHourData = () => {
        $loader.show();
        $.ajax({
            url: LinkGetTrainHourData,
            method: "GET",
            dataType: "json",
            success: function (response) {
                let rows = "";

                $.each(response.data, function (index, item) {
                    const date = new Date(item.date_activity);
                    const formattedDate = date.toISOString().split("T")[0];

                    const selected_class = getClassUnit.find(
                        (unit_class) => String(unit_class.id) === item.unit_class
                    );
                    const selected_code = getCode.find(
                        (code) => String(code.id) === item.code
                    );
                    const canEdit = String(item.jde_no) === String(authJDE);

                    const editButtons = canEdit
                        ? `
                        <button type="button" class="btn btn-sm btn-warning edit-btn" style="font-size:10px"
                            data-id="${item.id}"
                            data-url="${baseUrl}Trainer/hmtrain-hours/${item.id}/edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" style="font-size:10px"
                            data-id="${item.id}"
                            data-url="${baseUrl}Trainer/hmtrain-hours/${item.id}">Delete</button>
                    `
                        : "";

                    rows += `
                        <tr>
                            <td class="text-center">${index + 1 }</td>
                            <td class="text-center">${item.jde_no }</td>
                            <td class="text-right">${item.employee_name }</td>
                            <td class="text-center">${item.position }</td>
                            <td class="text-center">${item.site }</td>
                            <td class="text-center">${formattedDate}</td>
                            <td class="text-center">${item.training_type }</td>
                            <td>${item.unit_type }</td>
                            <td>${selected_class?.class ?? ''}</td>
                            <td>${selected_code?.no_unit ?? ''}</td>
                            <td class="text-center">${item.batch }</td>
                            <td class="text-center">${item.plan_total_hm }</td>
                            <td class="text-center">${item.hm_start }</td>
                            <td class="text-center">${item.hm_end }</td>
                            <td class="text-center">${item.total_hm }</td>
                            <td class="text-center">${item.progres }</td>
                            <td class="text-center">
                                ${item.plan_total_hm > 0 ? Math.round((item.progres / item.plan_total_hm) * 100) : 0 }%
                            </td>

                            <td class="text-center">${editButtons}</td>
                        </tr>
                    `;
                });

                $table.find("tbody").html(rows);
                initDataTable();
                $loader.hide();
            },
            error: function () {
                $loader.hide();
                alert("Failed to load data.");
            },
        });
    };

    loadTrainHourData();
});

$(document).ready(function () {
    $("#edit_train_type").select2({
        placeholder: "Select Training Type",
        dropdownParent: $("#editModal"),
        ajax: {
            url: LinkgetKPI,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    role: "Full",
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.kpi,
                            text: item.kpi,
                        };
                    }),
                };
            },
            cache: true,
        },
    });

    $("#edit_unit_type").select2({
        placeholder: "Select Unit Type",
        dropdownParent: $("#editModal"),
        ajax: {
            url: LinkgetMasterClassUnit,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
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

    $("#edit_unit_type").on("change", function () {
        selectedUnitType = $(this).val();
        $("#edit_unit_class").val(null).trigger("change"); // clear current selection
    });

    $("#edit_unit_class").select2({
        placeholder: "Select Unit Class",
        dropdownParent: $("#editModal"),
        ajax: {
            url: LinkgetMasterModelUnitbasedType,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    type: selectedUnitType, // pass the selected unit type
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
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

    $("#edit_unit_class").on("select2:select", function (e) {
        selectedModel = e.params.data.text; // or `e.params.data.id` if you use ID
        $("#edit_code").val(null).trigger("change"); // clear current Unit
    });

    $("#edit_code").select2({
        placeholder: "Select Code",
        dropdownParent: $("#editModal"),
        ajax: {
            url: LinkgetMasterUnit,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    model: selectedModel, // pass selected model to backend
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.no_unit,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const inputs = [
        "#edit_train_type",
        "#edit_unit_class",
        "#edit_batch",
        "#edit_hm_start",
        "#edit_hm_end",
    ];
    inputs.forEach((id) => {
        document.querySelector(id).addEventListener("change", handleInputs);
    });

    function handleInputs() {
        calculateTotalHM();
        fetchTotalHM();
    }

    function calculateTotalHM() {
        const start =
            parseFloat(document.querySelector("#edit_hm_start").value) || 0;
        const end =
            parseFloat(document.querySelector("#edit_hm_end").value) || 0;

        const totalHM = end - start;
        document.querySelector("#edit_total_hm").value =
            totalHM > 0 ? totalHM : 0;
    }

    function fetchTotalHM() {
        const jde = document.querySelector("#edit_jde").value;
        const training_type = document.querySelector("#edit_train_type").value;
        const unit_class = document.querySelector("#edit_unit_class").value;
        const batch = document.querySelector("#edit_batch").value;
        const id = document.querySelector("#edit_id")?.value;

        if (jde && training_type && unit_class && batch) {
            fetch(LinkgetTotalHM, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    jde,
                    training_type,
                    unit_class,
                    batch,
                    id,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    const totalFromDB = data.total_hm ?? 0;
                    const totalHMInput =
                        parseFloat(
                            document.querySelector("#edit_total_hm").value
                        ) || 0;
                    const finalProgress =
                        parseFloat(totalFromDB) + parseFloat(totalHMInput);

                    document.querySelector("#edit_progress").value =
                        finalProgress;

                    const planTotal = parseFloat(
                        document.querySelector("#edit_plan_total").value || 56
                    );
                    const percentProgress = (finalProgress / planTotal) * 100;
                    document.querySelector("#edit_per_progress").value =
                        percentProgress.toFixed(2);
                })
                .catch((err) => console.error("Error:", err));
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.edit-btn');
        if (!button) return;

        const id = button.getAttribute('data-id');
        const url = button.getAttribute('data-url');
        const getClassUnit = JSON.parse($("#getClassJson").val());
        // const getTypeUnit = JSON.parse($("#getTypeJson").val());
        const getCode = JSON.parse($("#getCodeJson").val());

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

                const date = new Date(data.date_activity);
                date.setDate(date.getDate() + 1);
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

                const selected_unit_class = getClassUnit.find(item => item.id == data.unit_class);
                if (selected_unit_class) {
                    const new_unit_class = new Option(selected_unit_class.class, selected_unit_class.id, true, true);
                    $('#edit_unit_class').append(new_unit_class).trigger('change');
                }

                const selected_code = getCode.find(item => item.id == data.code);
                if (selected_code) {
                    const new_code = new Option(selected_code.no_unit, selected_code.id, true, true);
                    $('#edit_code').append(new_code).trigger('change');
                }

                document.getElementById('edit_batch').value = data.batch;
                document.getElementById('edit_hm_start').value = data.hm_start;
                document.getElementById('edit_hm_end').value = data.hm_end;
                document.getElementById('edit_total_hm').value = data.total_hm;
                document.getElementById('edit_plan_total').value = data.plan_total_hm;
                document.getElementById('edit_progress').value = data.progres;
                document.getElementById('edit_per_progress').value = ((data.progres / data.plan_total_hm) * 100).toFixed(2);

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
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

    // Submit form logic
    document.getElementById('editForm').addEventListener('submit', function (event) {
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

        fetch(LinkTrainHourDataUpdate, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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


document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.delete-btn');
        if (!button) return;

        const deleteUrl = button.getAttribute('data-url');

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    Swal.close();
                    if (result.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: result.message || 'Record has been deleted.',
                            icon: 'success'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: result.message || 'Failed to delete the record.',
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

            fetch(LinkTrainHourImport, {
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

// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

let LinkGetDayActData = baseUrl + "api/dayact-data";
let LinkGetDayActDataEdit = baseUrl + "Trainer/daily-activity/{id}/edit";
let LinkDayActDataUpdate = baseUrl + "Trainer/daily-activity/update";
let LinkDayActImport = baseUrl + "Trainer/daily-activity/import-dayact";


let LinkgetActivity = baseUrl + "api/getActivity";
let LinkgetKPI = baseUrl + "api/getKPI";
let LinkgetMasterClassUnit = baseUrl + "api/getMasterClassUnit";
let LinkgetMasterTypeUnit = baseUrl + "api/getMasterTypeUnit";
let LinkgetMasterModelUnit = baseUrl + "api/getMasterModelUnit";
let LinkgetMasterModelUnitbasedType = baseUrl + "api/getMasterModelUnitbasedType";
let LinkgetMasterUnit = baseUrl + "api/getMasterUnit";

$(document).ready(function () {
    const getActivity = JSON.parse($('#getActivityJson').val());
    const getUnit = JSON.parse($('#getUnitJson').val());
    const authJDE = $('#authJDE').val();

    const $loader = $('#loader');
    const $table = $('#dayactTable');

    const initDataTable = () => {
        const table = $table.DataTable({
            paging: true,
            ordering: true,
            info: true,
            searching: true,
            orderCellsTop: true,
            fixedHeader: true
        });

        $table.find('thead tr:eq(1) th').each(function (i) {
            $('input', this).on('keyup change', function () {
                if (table.column(i).search() !== this.value) {
                    table.column(i).search(this.value).draw();
                }
            });
        });
    };

    const loadDayActData = () => {
        $loader.show();
        $.ajax({
            url: LinkGetDayActData,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                let rows = '';

                $.each(response.data, function (index, item) {
                    const date = new Date(item.date_activity);
                    const formattedDate = date.toISOString().split('T')[0];

                    const selected_activity = getActivity.find(activity => String(activity.id) === item.activity);
                    const selected_unit = getUnit.find(unit => String(unit.id) === item.unit_detail);
                    const canEdit = String(item.jde_no) === String(authJDE);

                    const editButtons = canEdit ? `
                        <button type="button" class="btn btn-sm btn-warning edit-btn" style="font-size:10px"
                            data-id="${item.id}"
                            data-url="${baseUrl}Trainer/daily-activity/${item.id}/edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" style="font-size:10px"
                            data-id="${item.id}"
                            data-url="${baseUrl}Trainer/daily-activity/${item.id}">Delete</button>
                    ` : '';


                    rows += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${item.jde_no }</td>
                            <td>${item.employee_name }</td>
                            <td class="text-center">${item.site }</td>
                            <td class="text-center">
                                ${formattedDate}</td>
                            <td>${item.kpi_type }</td>
                            <td>${selected_activity?.kpi ?? ''} - ${selected_activity?.activity ?? ''}</td>
                            <td>${selected_unit?.type ?? ''} - ${selected_unit?.model ?? ''}</td>
                            <td class="text-right">${item.total_participant }</td>
                            <td class="text-right">${item.total_hour }</td>

                            <td class="text-center">${editButtons}</td>
                        </tr>
                    `;
                });

                $table.find('tbody').html(rows);
                initDataTable();
                $loader.hide();
            },
            error: function () {
                $loader.hide();
                alert('Failed to load data.');
            }
        });
    };

    loadDayActData();
});

$(document).ready(function() {
    $('#edit_kpi').select2({
        placeholder: 'Select KPI',
        dropdownParent: $('#editModal'),

        ajax: {
            url: LinkgetKPI,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    // site: site,
                    site: $('#edit_site').val()
                    // role: role
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

    $('#edit_kpi').on('change', function() {
        selectedKPI = $(this).val();
        $('#edit_activity').val(null).trigger('change'); // clear current selection
    });

    $('#edit_activity').select2({
        placeholder: 'Select Activity',
        dropdownParent: $('#editModal'),
        ajax: {
            url: LinkgetActivity,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    kpi: selectedKPI, // pass selected KPI
                    site: $('#edit_site').val()
                    // role: role
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
        },
    });

    $('#edit_unit_detail').select2({
        placeholder: 'Select Unit Detail',
        dropdownParent: $('#editModal'),
        ajax: {
            url: LinkgetMasterModelUnit,
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

document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.delete-btn');
        if (!button) return;

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
                        }).then(() => {
                            location.reload();
                        });
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

            fetch(LinkDayActImport, {
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

document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.edit-btn');
        if (!button) return;

        const id = button.getAttribute('data-id');
        const url = button.getAttribute('data-url');
        const getActivity = JSON.parse($('#getActivityJson').val());
        const getUnit = JSON.parse($('#getUnitJson').val());

        // Show SweetAlert loading indicator
        Swal.fire({
            title: 'Loading...',
            html: 'Fetching data, please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch data from server
        fetch(url)
            .then(response => response.json())
            .then(data => {
                Swal.close(); // Close loading indicator

                // Format date to YYYY-MM-DD for date input
                const date = new Date(data.date_activity);
                date.setDate(date.getDate() + 1); // Adjust timezone shift if needed
                const formattedDate = date.toISOString().split('T')[0];

                // Populate modal fields
                $('#edit_id').val(data.id);
                $('#edit_jde').val(data.jde_no);
                $('#edit_name').val(data.employee_name);
                $('#edit_site').val(data.site);
                $('#edit_date').val(formattedDate);

                if (data.kpi_type) {
                    const newOption = new Option(data.kpi_type, data.kpi_type, true, true);
                    $('#edit_kpi').append(newOption).trigger('change');
                }

                const selectedActivity = getActivity.find(item => item.id == data.activity);
                if (selectedActivity) {
                    const newActivity = new Option(
                        `${selectedActivity.kpi} - ${selectedActivity.activity}`,
                        selectedActivity.id,
                        true,
                        true
                    );
                    $('#edit_activity').append(newActivity).trigger('change');
                }

                const selectedUnit = getUnit.find(item => item.id == data.unit_detail);
                if (selectedUnit) {
                    const newUnit = new Option(
                        `${selectedUnit.type} - ${selectedUnit.model}`,
                        selectedUnit.id,
                        true,
                        true
                    );
                    $('#edit_unit_detail').append(newUnit).trigger('change');
                }

                $('#edit_jml_peserta').val(data.total_participant);
                $('#edit_total_hour').val(data.total_hour);

                // Show the modal
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

    // Handle edit form submission
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

        fetch(LinkDayActDataUpdate, {
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
                        text: data.message || 'Failed to update data.'
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



// document.addEventListener('DOMContentLoaded', function() {
//     const editButtons = document.querySelectorAll('.edit-btn');

//     editButtons.forEach(button => {
//         button.addEventListener('click', function() {
//             const id = button.getAttribute('data-id');
//             const url = button.getAttribute('data-url');
//             const getActivity = JSON.parse($('#getActivityJson').val());
//             const getUnit = JSON.parse($('#getUnitJson').val());


//             // Show SweetAlert loading indicator
//             Swal.fire({
//                 title: 'Loading...',
//                 html: 'Fetching data, please wait...',
//                 allowOutsideClick: false,
//                 didOpen: () => {
//                     Swal.showLoading();
//                 }
//             });

//             // Fetch data from server
//             fetch(url)
//                 .then(response => response.json())
//                 .then(data => {
//                     Swal.close(); // Close the loading indicator
//                     // Format date to YYYY-MM-DD for the date input
//                     const date = new Date(data.date_activity);
//                     date.setDate(date.getDate() + 1); // Add one day
//                     const formattedDate = date.toISOString().split('T')[0];
//                     // Populate modal fields with new create form fields
//                     document.getElementById('edit_id').value = data.id;
//                     document.getElementById('edit_jde').value = data.jde_no;
//                     document.getElementById('edit_name').value = data.employee_name;
//                     document.getElementById('edit_site').value = data.site;
//                     document.getElementById('edit_date').value = formattedDate;

//                     if (data.kpi_type) {
//                         const newOption = new Option(data.kpi_type, data.kpi_type, true, true);
//                         $('#edit_kpi').append(newOption).trigger('change');
//                     }

//                     const selectedActivity = getActivity.find(item => item.id == data
//                         .activity);
//                     if (selectedActivity) {
//                         const newActivity = new Option(selectedActivity.kpi + ' - ' +
//                             selectedActivity.activity, selectedActivity.id, true,
//                             true);
//                         $('#edit_activity').append(newActivity).trigger('change');
//                     }

//                     const selectedUnit = getUnit.find(item => item.id == data
//                         .unit_detail);
//                     if (selectedUnit) {
//                         const newUnit = new Option(selectedUnit.type + ' - ' +
//                             selectedUnit.model, selectedUnit.id, true, true);
//                         $('#edit_unit_detail').append(newUnit).trigger('change');
//                     }

//                     document.getElementById('edit_jml_peserta').value = data
//                         .total_participant;
//                     document.getElementById('edit_total_hour').value = data.total_hour;

//                     // Show the modal
//                     const editModal = new bootstrap.Modal(document.getElementById('editModal'));
//                     editModal.show();
//                 })
//                 .catch(error => {
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Error',
//                         text: 'Failed to fetch data. Please try again.'
//                     });
//                     console.error('Error fetching data:', error);
//                 });
//         });
//     });

//     // Handle form submission
//     document.getElementById('editForm').addEventListener('submit', function(event) {
//         event.preventDefault();

//         const formData = new FormData(this);

//         Swal.fire({
//             title: 'Updating...',
//             html: 'Please wait while your data is being updated...',
//             allowOutsideClick: false,
//             didOpen: () => {
//                 Swal.showLoading();
//             }
//         });

//         fetch(`{{ route('DayActUpdate') }}`, {
//                 method: 'POST',
//                 headers: {
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
//                         .getAttribute('content')
//                 },
//                 body: formData
//             })
//             .then(response => response.json())
//             .then(data => {
//                 Swal.close();
//                 if (data.success) {
//                     Swal.fire({
//                         icon: 'success',
//                         title: 'Success',
//                         text: 'Data successfully updated!'
//                     }).then(() => location.reload());
//                 } else {
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Failed',
//                         text: 'Failed to update data.'
//                     });
//                 }
//             })
//             .catch(error => {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error',
//                     text: 'An error occurred while updating data.'
//                 });
//                 console.error('Error:', error);
//             });
//     });
// });

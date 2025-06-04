// LINK LINK

// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

let LinkMOPImport = baseUrl + "mop/import-mop";
let LinkMOPExport = baseUrl + "mop/export-mop";
let LinkGetMOPData = baseUrl + "api/mop-data";

console.log("API URL:", LinkGetMOPData);
function confirmExport() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to export the selected data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Export it!",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("mopExportForm").submit();
        }
    });
}

function confirmImport() {
    const fileInput = document.getElementById("importFile").files[0];
    console.log("Selected File:", fileInput); // Debugging: Check file data
    if (!fileInput) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Please select a file to import!",
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to import this file?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Import it!",
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: "Importing...",
                text: "Please wait while your file is being processed.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Submit the form with AJAX
            const formData = new FormData(
                document.getElementById("mopImportForm")
            );

            fetch(LinkMOPImport, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'input[name="_token"]'
                    ).value,
                },
            })
                .then((response) => response.text()) // Inspect response as text first
                .then((text) => {
                    console.log("Raw Response:", text); // Debug raw response
                    return JSON.parse(text); // Attempt to parse JSON
                })
                .then((data) => {
                    console.log("Parsed Data:", data); // Debug parsed data
                    if (data.success) {
                        Swal.fire("Success!", data.message, "success");
                        location.reload(); // Reload the page after success
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch((error) => {
                    console.error("Fetch Error:", error);
                    Swal.fire(error.message, error.message, "error");
                    //   location.reload();
                });
        }
    });
}

$(document).ready(function () {
    $("#mopTable").DataTable({
        ajax: {
            url: LinkGetMOPData,
            dataSrc: "data",
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 }, // No
            { data: "jde_no" },
            { data: "employee_name" },
            { data: "site" },
            { data: "equipment_type1" },
            {
                data: null,
                render: function (data, type, row) {
                    return `${row.month}/${row.year}`;
                },
            },
            { data: "a_attendance_ratio" },
            { data: "b_discipline" },
            { data: "c_safety_awareness" },
            { data: "d_wh_waste_equiptype1" },
            { data: "e_pty_equiptype1" },
            { data: "point_a" },
            { data: "point_b" },
            { data: "point_c" },
            { data: "point_d" },
            { data: "point_e" },
            { data: "point_eligibilitas" },
            { data: "point_produksi" },
            { data: "total_point" },
            {
                data: null,
                render: function (data, type, row) {
                    return `<a href="/mop/edit/${row.id}" class="btn btn-sm btn-primary">Edit</a>`;
                },
            },
        ],
    });
});


// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

let LinkGetMentoringData = baseUrl + "api/mentoring-data";
let LinkGetMentoringDataEdit = baseUrl + "Mentoring/edit/{id}";

$(document).ready(function () {
    const getModel = JSON.parse($('#getModelJson').val());
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

    const loadMentoringData = () => {
        $loader.show();
        $.ajax({
            url: LinkGetMentoringData,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                let rows = '';

                $.each(response.data, function (index, item) {
                    const start = new Date("1970-01-01T" + item.START_TIME);
                    const end = new Date("1970-01-01T" + item.END_TIME);
                    const duration = ((end - start) / 3600000).toFixed(2);

                    const selected_model = getModel.find(model => String(model.id) === item.unit_model);
                    const selected_unit = getUnit.find(unit => String(unit.id) === item.unit_number);
                    const canEdit = String(item.trainer_jde) === String(authJDE);

                    const editButtons = canEdit ? `
                        <button class="btn btn-sm btn-warning edit-btn" style="font-size:10px"
                            data-id="${item.id}"
                            data-url="${baseUrl}Mentoring/edit/${item.id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" style="font-size:10px"
                            data-id="${item.id}">Delete</button>
                    ` : '';

                    rows += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${item.trainer_jde ?? ''} - ${item.trainer_name ?? ''}</td>
                            <td>${item.operator_jde ?? ''} - ${item.operator_name ?? ''}</td>
                            <td>${item.site ?? ''}</td>
                            <td>${item.area ?? ''}</td>
                            <td>${selected_model?.model ?? ''} - ${selected_unit?.no_unit ?? ''}</td>
                            <td>${item.date_mentoring?.split(' ')[0]}</td>
                            <td>${item.start_time ?? ''} - ${item.end_time ?? ''}</td>
                            <td class="text-end">${item.average_point_observation ?? '-'}</td>
                            <td class="text-end">${item.average_point_mentoring ?? '-'}</td>
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

    loadMentoringData();
});

$(document).on('click', '.edit-btn', function () {
    const url = $(this).data('url');

    Swal.fire({
        title: 'Redirecting...',
        html: 'Opening edit page...',
        timer: 1000,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    window.location.href = url;
});

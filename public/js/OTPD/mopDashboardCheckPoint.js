// LINK LINK

// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

// Now build the links dynamically
let LinkGetModelUnit = baseUrl + "api/getMasterModelUnit";
let LinkGetEmployeeOperator = baseUrl + "api/getEmployeeOperator";
let LinkGetMOPDataSimple = baseUrl + "api/mop-dataSimple";
console.log(LinkGetModelUnit);
console.log(LinkGetEmployeeOperator);
console.log(LinkGetMOPDataSimple);


// CALLING2 FUNCTION
function onFilterChange() {
    loadDataRequirement();
    LoadkualifikasiChart();
    loadGradeDistribution();
    getAllSpeedometerCharts();
    loadMOPData();
}

const filterIds = ["#filterSite", "#filterYear", "#filterMonth", "#filterUnit", "#filterName"];

filterIds.forEach((selector) => {
    $(selector).on("change", onFilterChange); // use jQuery .on for all, safe for Select2
});

// Initial load on page load
$(document).ready(function () {
    onFilterChange();
});







// FILTER FORM
// YEAR
const selectYear = document.getElementById("filterYear");
const startYear = 1997;
const currentYear = new Date().getFullYear();

const defaultOption = document.createElement("option");
defaultOption.value = "";
defaultOption.textContent = "Select Year";
defaultOption.disabled = true;
defaultOption.selected = true;
selectYear.appendChild(defaultOption);

for (let year = currentYear; year >= startYear; year--) {
    const option = document.createElement("option");
    option.value = year;
    option.textContent = year;
    selectYear.appendChild(option);
}

// MONTH
const selectMonth = document.getElementById("filterMonth");

const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

// Employee
$("#filterName").select2({
    placeholder: "Select Employee",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: LinkGetEmployeeOperator,
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
                        id: item.employeeId,
                        text: `${item.employeeId} - ${item.EmployeeName}`,
                    };
                }),
            };
        },
        cache: true,
    },
    templateSelection: function (data) {
        return data.text || data.id;
    },
});

// Unit Category
$(document).ready(function () {
    $.ajax({
        url: LinkGetModelUnit, // Laravel route
        type: "GET",
        success: function (data) {
            const $select = $("#filterUnit");
            $select.append('<option value="">All</option>');
            data.forEach(function (item) {
                const label = `${item.model} - ${item.class}`;
                $select.append(`<option value="${item.id}">${label}</option>`);
            });
        },
        error: function (xhr) {
            console.error("Error loading model units:", xhr.responseText);
        },
    });
});

// // Add "All" option
// const allOption = document.createElement("option");
// allOption.value = "";
// allOption.textContent = "Select Month";
// selectMonth.appendChild(allOption);

// // Populate month options
// months.forEach((monthName, index) => {
//     const option = document.createElement("option");
//     option.value = index + 1; // Month number (1–12)
//     option.textContent = monthName;
//     selectMonth.appendChild(option);
// });

// let sampleData = [];

/////////////////////////

let dataRequirementChart;

async function loadDataRequirement() {
    const site = document.getElementById("filterSite").value;
    const year = document.getElementById("filterYear").value;
    const month = document.getElementById("filterMonth").value;
    const name = document.getElementById("filterName").value;
    const unit = document.getElementById("filterUnit").value;

    const params = new URLSearchParams({
        site,
        year,
        month,
        jde_no: name, // Laravel uses 'jde_no'
        equipment_type1: unit, // Laravel uses 'equipment_type1'
    });

    const response = await fetch(
        `${LinkGetMOPDataSimple}?${params.toString()}`
    );
    const result = await response.json();

    const totalEmployee = result.total_employee;
    const totalMOP = result.total_data;
    let sumTotalPoints = 0;
    let totalCount = 0;
    result.data.forEach((item) => {
        let totalPoint = parseFloat(item.total_point); // Convert to number (float)

        if (!isNaN(totalPoint)) {
            // Check if it's a valid number
            sumTotalPoints += totalPoint;
            totalCount++; // Count valid total points
            // console.log(totalPoint); // Add total_point of each item to the sum
        }
    });

    let averageTotalPoints = totalCount > 0 ? sumTotalPoints / totalCount : 0;
    console.log("Average Total Points:", averageTotalPoints);
    const totalAverage = (sumTotalPoints / result.data.length).toFixed(2);
    const data = result.data;
    console.log(sumTotalPoints);
    console.log(result.data.length);
    console.log(totalAverage);
    document.getElementById("totalScore").textContent = totalAverage;

    const chartData = {
        labels: ["Total Employee", "Total MOP"],
        datasets: [
            {
                label: "Data Requirement",
                data: [totalEmployee, totalMOP],
                backgroundColor: ["#66b3ff", "#ffb366"],
            },
        ],
    };

    const ctx = document
        .getElementById("dataRequirementChart")
        .getContext("2d");

    if (dataRequirementChart) {
        dataRequirementChart.data = chartData;
        dataRequirementChart.update();
    } else {
        dataRequirementChart = new Chart(ctx, {
            type: "bar",
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.raw;
                            },
                        },
                    },
                    datalabels: {
                        anchor: "end",
                        align: "top",
                        font: { size: 12 },
                        formatter: Math.round,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
            plugins: [ChartDataLabels],
        });
    }
}

// document
//     .querySelectorAll(
//         "#filterSite, #filterYear, #filterMonth, #filterName, #filterUnit"
//     )
//     .forEach((el) =>
//         el.addEventListener("change", () => {
//             loadDataRequirement(); // and other charts if needed
//         })
//     );

// loadDataRequirement();

// MONTH AVERAGE MOP - KUALIFIKASI TYPES

const kualifikasiTypes = ["K", "C", "C+", "B", "B+", "BS", "BS+", "ISTIMEWA"];

const kualifikasiCount = {
    K: 0,
    C: 0,
    "C+": 0,
    B: 0,
    "B+": 0,
    BS: 0,
    "BS+": 0,
    ISTIMEWA: 0,
};
let kualifikasiChart; // Store chart instance for later update

// Reusable chart update function
async function LoadkualifikasiChart() {
    const site = document.getElementById("filterSite").value;
    const year = document.getElementById("filterYear").value;
    const month = document.getElementById("filterMonth").value;
    const jde_no = document.getElementById("filterName").value;
    const equipment_type1 = document.getElementById("filterUnit").value;

    const params = new URLSearchParams();

    if (site) params.append("site", site);
    if (year) params.append("year", year);
    if (month) params.append("month", month);
    if (jde_no) params.append("jde_no", jde_no);
    if (equipment_type1) params.append("equipment_type1", equipment_type1);

    try {
        const response = await fetch(
            `${LinkGetMOPDataSimple}?${params.toString()}`
        );
        const sampleData = await response.json();

        const kualifikasiCount = Object.fromEntries(
            kualifikasiTypes.map((k) => [k, 0])
        );

        sampleData.data.forEach((item) => {
            const grade = item.mop_bulanan_grade;
            if (kualifikasiCount[grade] !== undefined) {
                kualifikasiCount[grade]++;
            }
        });

        const chartData = {
            labels: kualifikasiTypes,
            datasets: [
                {
                    label: "Total Kualifikasi Count",
                    data: kualifikasiTypes.map((k) => kualifikasiCount[k]),
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                },
            ],
        };

        const ctx = document
            .getElementById("kualifikasiChart")
            .getContext("2d");
        if (kualifikasiChart) {
            kualifikasiChart.data = chartData;
            kualifikasiChart.update();
        } else {
            kualifikasiChart = new Chart(ctx, {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return `Count: ${tooltipItem.raw}`;
                                },
                            },
                        },
                        datalabels: {
                            display: true,
                            color: "#ffffff", // Text color
                            font: { size: 14 }, // Font size
                            align: "center", // Align label at the center of each bar
                            anchor: "end", // Anchor label to the top of the bar
                            formatter: (value) => value, // Display the actual value
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
        }
    } catch (err) {
        console.error("Failed to fetch or render chart:", err);
    }
}

// Attach change listeners to filters
// ["filterSite", "filterYear", "filterMonth", "filterName", "filterUnit"].forEach(
//     (id) => {
//         document
//             .getElementById(id)
//             .addEventListener("change", LoadkualifikasiChart);
//     }
// );
// LoadkualifikasiChart();
///////////////////// DISTRIBUTION CHART
let gradeDistributionChart;
async function loadGradeDistribution() {
    document.getElementById("ServerDev").value == "DEV"
        ? console.log("DEV")
        : console.log("PROD");
    // Get selected filters
    const site = document.getElementById("filterSite").value;
    const year = document.getElementById("filterYear").value;
    const month = document.getElementById("filterMonth").value;
    const name = document.getElementById("filterName").value;
    const unit = document.getElementById("filterUnit").value;

    // Build query string dynamically
    const params = new URLSearchParams();
    if (site) params.append("site", site);
    if (year) params.append("year", year);
    if (month) params.append("month", month);
    if (name) params.append("name", name);
    if (unit) params.append("unit", unit);

    const response = await fetch(
        `${LinkGetMOPDataSimple}?${params.toString()}`
    );
    const sampleData = await response.json();

    // Ordered months
    const orderedMonths = [
        "JANUARY",
        "FEBRUARY",
        "MARCH",
        "APRIL",
        "MAY",
        "JUNE",
        "JULY",
        "AUGUST",
        "SEPTEMBER",
        "OCTOBER",
        "NOVEMBER",
        "DECEMBER",
    ];

    // Unique, ordered month labels
    const monthLabels = [
        ...new Set(
            sampleData.data.map((item) => {
                const date = new Date(item.year, item.month - 1);
                return date
                    .toLocaleString("default", { month: "long" })
                    .toUpperCase();
            })
        ),
    ].sort((a, b) => orderedMonths.indexOf(a) - orderedMonths.indexOf(b));

    // Group by grade and month
    const dataByGrade = kualifikasiTypes.map((kualifikasi) => {
        const data = monthLabels.map((monthLabel) => {
            return sampleData.data.filter((item) => {
                const itemMonth = new Date(item.year, item.month - 1)
                    .toLocaleString("default", { month: "long" })
                    .toUpperCase();
                return (
                    item.mop_bulanan_grade === kualifikasi &&
                    itemMonth === monthLabel
                );
            }).length;
        });

        return {
            label: `Sum of ${kualifikasi}`,
            data: data,
            backgroundColor: getBarColor(kualifikasi),
        };
    });

    // Update chart
    const chartData = {
        labels: monthLabels,
        datasets: dataByGrade,
    };

    const ctx = document
        .getElementById("gradeDistributionChart")
        .getContext("2d");

    if (gradeDistributionChart) {
        gradeDistributionChart.data = chartData;
        gradeDistributionChart.update();
    } else {
        gradeDistributionChart = new Chart(ctx, {
            type: "bar",
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                        ticks: {
                            autoSkip: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return `Count: ${tooltipItem.raw}`;
                            },
                        },
                    },
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            font: {
                                size: 10, // Smaller font
                            },
                        },
                    },
                },
            },
        });
    }
}

// Helper: assign color by grade
function getBarColor(grade) {
    const colors = {
        K: "#FFB3B3",
        C: "#FF9999",
        "C+": "#FF8080",
        B: "#FF6600",
        "B+": "#FF9933",
        BS: "#FFCC66",
        "BS+": "#FFD699",
        ISTIMEWA: "#FFEECC",
    };
    return colors[grade] || "gray";
}

//// Call initially
// document
//     .querySelectorAll(
//         "#filterSite, #filterYear, #filterMonth, #filterName, #filterUnit"
//     )
//     .forEach((el) => el.addEventListener("change", loadGradeDistribution));

// window.addEventListener("DOMContentLoaded", loadGradeDistribution);

// ////////////////// SPEEDOMETER CHART - START

let avg = 0; // global average value for the needle plugin

// Fetch data from the API
fetch(LinkGetMOPDataSimple)
    .then((response) => response.json())
    .then((result) => {
        if (
            result.data &&
            Array.isArray(result.data) &&
            result.data.length > 0
        ) {
            // Helper to calculate average for a given key
            const calcAvg = (key) =>
                result.data.reduce(
                    (sum, item) => sum + parseFloat(item[key] || 0),
                    0
                ) / result.data.length;

            // Render each chart with respective average
            renderSpeedometer("speedometerChart1", calcAvg("point_a"));
            renderSpeedometer("speedometerChart2", calcAvg("point_b"));
            renderSpeedometer("speedometerChart3", calcAvg("point_c"));
            renderSpeedometer("speedometerChart4", calcAvg("point_d"));
            renderSpeedometer("speedometerChart5", calcAvg("point_e"));

            // Example: Accessing total counts for display or other purposes
            const totalEmployee = result.total_employee;
            const totalData = result.total_data;
            console.log(
                `Total Employees: ${totalEmployee}, Total MOP Records: ${totalData}`
            );
        } else {
            console.error("Data is empty or not in the expected format");
        }
    })
    .catch((err) => {
        console.error("Error fetching MOPDataSimple:", err);
    });

// Needle plugin
function needlePlugin(avg) {
    return {
        id: "needle",
        afterDatasetDraw(chart) {
            const { ctx } = chart;

            const meta = chart.getDatasetMeta(0);
            const arc = meta.data[0];
            if (!arc) return;

            const cx = chart.width / 2;
            const needleBaseY = chart.height * 0.7;
            const outerRadius = arc.outerRadius;

            const percent = Math.min(Math.max(avg, 0), 5);
            const angle = 180 - (percent / 5) * -180;
            const angleRad = (angle * Math.PI) / 180;

            const needleLength = outerRadius * 0.9;
            const tipX = cx + needleLength * Math.cos(angleRad);
            const tipY = needleBaseY + needleLength * Math.sin(angleRad);

            ctx.save();
            ctx.beginPath();
            ctx.lineWidth = 3;
            ctx.strokeStyle = "#ff4d4d";
            ctx.moveTo(cx, needleBaseY);
            ctx.lineTo(tipX, tipY);
            ctx.stroke();
            ctx.restore();

            ctx.beginPath();
            ctx.arc(cx, needleBaseY, 5, 0, Math.PI * 2);
            ctx.fillStyle = "#ff4d4d";
            ctx.fill();

            ctx.font = "bold 16px Arial";
            ctx.fillStyle = "#fff";
            ctx.textAlign = "center";
            ctx.fillText(`Avg: ${avg.toFixed(2)}`, cx, needleBaseY + 30);
        },
    };
}

// Render chart function
const chartInstances = {}; // Store the current chart instance

function renderSpeedometer(chartId, avgValue) {
    if (chartInstances[chartId]) {
        chartInstances[chartId].destroy();
    }

    chartInstances[chartId] = new Chart(document.getElementById(chartId), {
        type: "doughnut",
        data: {
            labels: ["0–1", "1–2", "2–3", "3–4", "4–5"],
            datasets: [
                {
                    data: [1, 1, 1, 1, 1],
                    backgroundColor: [
                        "#dbe9f6",
                        "#9ecae1",
                        "#6baed6",
                        "#3182bd",
                        "#08519c",
                    ],
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            cutout: "70%",
            rotation: -90,
            circumference: 180,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
            },
        },
        plugins: [needlePlugin(avgValue)], // Pass avgValue here
    });
}

function getAllSpeedometerCharts() {
    const site = document.getElementById("filterSite").value;
    const jde_no = document.getElementById("filterName").value;
    const year = document.getElementById("filterYear").value;
    const month = document.getElementById("filterMonth").value;
    const equipment = document.getElementById("filterUnit").value;

    const params = new URLSearchParams();
    if (site) params.append("site", site);
    if (jde_no) params.append("jde_no", jde_no);
    if (year) params.append("year", year);
    if (month) params.append("month", month);
    if (equipment) params.append("equipment_type1", equipment);

    fetch(`${LinkGetMOPDataSimple}?${params.toString()}`)
        .then((response) => response.json())
        .then((result) => {
            // Access the data and check if it's valid
            const data = result.data;

            if (!Array.isArray(data) || data.length === 0) {
                console.error("Empty or invalid data");
                return;
            }

            // Helper function to calculate the average for a given key
            const avg = (key) =>
                data.reduce(
                    (sum, item) => sum + parseFloat(item[key] || 0),
                    0
                ) / data.length;

            // Render the speedometers with the calculated averages
            renderSpeedometer("speedometerChart1", avg("point_a"));
            renderSpeedometer("speedometerChart2", avg("point_b"));
            renderSpeedometer("speedometerChart3", avg("point_c"));
            renderSpeedometer("speedometerChart4", avg("point_d"));
            renderSpeedometer("speedometerChart5", avg("point_e"));

            // Optionally, log the total employee and total data for reference
            const totalEmployee = result.total_employee;
            const totalData = result.total_data;
            console.log(
                `Total Employees: ${totalEmployee}, Total MOP Records: ${totalData}`
            );
        })
        .catch((err) => {
            console.error("Fetch error:", err);
        });
}

// ["filterSite", "filterName", "filterYear", "filterMonth", "filterUnit"].forEach(
//     (id) => {
//         document
//             .getElementById(id)
//             .addEventListener("change", getAllSpeedometerCharts);
//     }
// );

/////////////////////////////////////// TABLE
// $("#filterSite, #filterYear, #filterMonth, #filterName, #filterUnit").on(
//     "change",
//     loadMOPData
// );

function loadMOPData() {
    const filters = {
        site: $("#filterSite").val(),
        year: $("#filterYear").val(),
        month: $("#filterMonth").val(),
        name: $("#filterName").val(),
        unit: $("#filterUnit").val(),
    };

    $.ajax({
        url: LinkGetMOPDataSimple,
        type: "GET",
        data: filters,
        success: function (data) {
            console.log("MOP data response:", data);
            const $tbody = $("#tableDataMOPCompile tbody");
            $tbody.empty();

            const items = Array.isArray(data) ? data : data.data || [];

            if (items.length === 0) {
                $tbody.append(
                    "<tr><td colspan='4' class='text-center'>No data found</td></tr>"
                );
                return;
            }

            items.forEach((item) => {
                $tbody.append(`
            <tr>
                <td>${item.employee_name || "-"}</td>
                <td>${item.equipment_type1 || "-"}</td>
                <td>${item.total_point ?? "-"}</td>
                <td>${item.mop_bulanan_grade || "-"}</td>
            </tr>
        `);
            });
        },
        error: function (xhr) {
            console.error("Error loading MOP data:", xhr.responseText);
        },
    });
}

// $(document).ready(function () {
//     loadMOPData();
// });

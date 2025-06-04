// Set baseUrl depending on the environment
const isLocalhost = window.location.hostname === "localhost";
const baseUrl = window.location.origin + (isLocalhost ? "/" : "/OTPD/");

let sampleData = [];

async function fetchDataTable() {
    try {
        const response = await fetch("/api/mentoring-dataDB");
        sampleData = await response.json();
        if (!sampleData || !Array.isArray(sampleData.data)) {
            console.error("Invalid response format:", sampleData);
            return;
        }

        populateFilter("filterSite", getUniqueValues(sampleData.data, "site"));
        populateFilter("filterYear", getUniqueValues(sampleData.data, "year"));
        populateFilter(
            "filterMonth",
            getUniqueValues(sampleData.data, "month")
        );
        // populateFilter(
        //     "filterUnit",
        //     getUniqueValues(sampleData.data, "unit_type")
        // );
        populateFilter(
            "filterUnit",
            getUniqueValues(sampleData.data, "no_unit")
        );

        const uniqueJDE = getUniqueJDEWithNames(sampleData.data);
        populateFilter("filterName", uniqueJDE, "jde_with_name");

        updateTable(); // Show data initially
    } catch (err) {
        console.error("Error loading data:", err);
    }
}

function getUniqueValues(data, key) {
    return [...new Set(data.map((item) => item[key]))];
}

function populateFilter(id, values, displayKey = null) {
    const select = document.getElementById(id);
    select.innerHTML = '<option value="">All</option>';

    values.forEach((item) => {
        const opt = document.createElement("option");

        // Handle the case where the item is an object with jde_no
        if (displayKey === "jde_with_name") {
            opt.value = item.operator_jde;
            opt.textContent = `${item.operator_jde} - ${item.operator_name}`;
        } else {
            opt.value = item;
            opt.textContent = item;
        }

        select.appendChild(opt);
    });
}

const uniqueJDE = getUniqueJDEWithNames(sampleData.data);
populateFilter("filterName", uniqueJDE, "jde_with_name");

function getUniqueJDEWithNames(data) {
    if (!Array.isArray(data)) return [];

    const map = new Map();
    data.forEach((item) => {
        if (item.operator_jde && item.operator_name) {
            map.set(item.operator_jde, item.operator_name);
        }
    });

    return Array.from(map.entries()).map(([operator_jde, operator_name]) => ({
        operator_jde,
        operator_name,
    }));
}

function formatIndicator(label) {
    switch (label) {
        case "FUEL EFFICIENT AWARENESS":
            return "Fuel Eff";
        case "MACHINE HEALTH":
            return "Miss Operation";
        case "PRODUKTIFITAS":
            return "Productivity";
        case "SAFETY AWARENESS":
            return "Safety Awareness";
        default:
            return label;
    }
}

function updateTable() {
    Swal.fire({
        title: "Loading Charts...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const filters = {
        site: document.getElementById("filterSite").value,
        year: document.getElementById("filterYear").value,
        month: document.getElementById("filterMonth").value,
        name: document.getElementById("filterName").value,
        unit: document.getElementById("filterUnit").value,
    };

    const filtered = sampleData.data.filter(
        (item) =>
            // item.type_penilaian === "observasi" &&
            (!filters.site || item.site === filters.site) &&
            (!filters.year || item.year == filters.year) &&
            (!filters.month || item.month == filters.month) &&
            (!filters.name || item.operator_jde === filters.name) &&
            // (!filters.unit || item.unit_type === filters.unit)
            (!filters.unit || item.no_unit === filters.unit)
    );

    const tbody = document.querySelector("#tableDataMOPCompile tbody");
    tbody.innerHTML = "";

    filtered.forEach((row) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
                <td>${row.site}</td>
                <td>${row.operator_jde}</td>
                <td>${row.operator_name}</td>
                <td>${row.no_unit}</td>
                <td>${row.type_mentoring}</td>
                <td>${formatIndicator(row.indicator)}</td>
                <td>${row.point}</td>
            `;
        tbody.appendChild(tr);
    });

    Swal.close();
}

document.addEventListener("DOMContentLoaded", () => {
    fetchDataTable();
    [
        "filterSite",
        "filterYear",
        "filterMonth",
        "filterName",
        "filterUnit",
    ].forEach((id) => {
        document.getElementById(id).addEventListener("change", updateTable);
    });
});

//////////////////////////
// SKILL DEV CHART
//////////////////////////

const skillTypes = [
    "FUEL EFFICIENT AWARENESS",
    "MACHINE HEALTH",
    "PRODUKTIFITAS",
    "SAFETY AWARENESS",
];

let skillChart;

function normalize(str) {
    return (str || "").toUpperCase().trim();
}

async function LoadskillChart() {
    Swal.fire({
        title: "Loading Charts...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const site = document.getElementById("filterSite").value;
    const year = document.getElementById("filterYear").value;
    const month = document.getElementById("filterMonth").value;
    const operator_jde = document.getElementById("filterName").value;
    const unit_type = document.getElementById("filterUnit").value;

    const params = new URLSearchParams();
    if (site) params.append("site", site);
    if (year) params.append("year", year);
    if (month) params.append("month", month);
    if (operator_jde) params.append("operator_jde", operator_jde);
    if (unit_type) params.append("unit_type", unit_type);

    try {
        const response = await fetch(`/api/mentoring-dataDB?${params}`);
        const result = await response.json();
        const data = result.skill;

        // Filter out entries with null indicator/type_penilaian
        const filteredData = data.filter(
            (item) => item.indicator && item.type_penilaian
        );

        // Use skillTypes order as labels
        const indicators = skillTypes;

        // Map to get avg_point for each indicator and type_penilaian
        const observePoints = indicators.map((ind) => {
            const found = filteredData.find(
                (d) =>
                    normalize(d.indicator) === ind &&
                    normalize(d.type_penilaian) === "OBSERVASI"
            );
            return found
                ? parseFloat(parseFloat(found.avg_point).toFixed(1))
                : 0;
        });

        const mentoringPoints = indicators.map((ind) => {
            const found = filteredData.find(
                (d) =>
                    normalize(d.indicator) === ind &&
                    normalize(d.type_penilaian) === "MENTORING"
            );
            return found
                ? parseFloat(parseFloat(found.avg_point).toFixed(1))
                : 0;
        });

        const chartData = {
            labels: indicators.map(formatIndicator),
            // labels: indicators.map((label) => {
            //     switch (label) {
            //         case "FUEL EFFICIENT AWARENESS":
            //             return "Fuel Eff";
            //         case "MACHINE HEALTH":
            //             return "Miss Operation";
            //         case "PRODUKTIFITAS":
            //             return "Produktivity";
            //         case "SAFETY AWARENESS":
            //             return "Safety Awareness";
            //         default:
            //             return label;
            //     }
            // }),
            datasets: [
                {
                    label: "Average of Observe Point",
                    data: observePoints,
                    backgroundColor: "#34556B",
                },
                {
                    label: "Average of Mentoring Point",
                    data: mentoringPoints,
                    backgroundColor: "#3B241C",
                },
            ],
        };

        const ctx = document.getElementById("skillChart").getContext("2d");
        if (skillChart) {
            skillChart.data = chartData;
            skillChart.update();
        } else {
            skillChart = new Chart(ctx, {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true },
                    },
                    plugins: {
                        legend: { position: "bottom" },
                        datalabels: {
                            display: true,
                            color: "white",
                            font: { weight: "bold", size: 12 },
                            formatter: (value) => value.toFixed(1),
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
        }
    } catch (err) {
        console.error("Failed to fetch or render chart:", err);
    }

    Swal.close()
}

["filterSite", "filterYear", "filterMonth", "filterName", "filterUnit"].forEach(
    (id) => {
        document.getElementById(id).addEventListener("change", LoadskillChart);
    }
);

LoadskillChart();

//////////////////////////
// MONTHLY ACH CHART
//////////////////////////

const ctx = document.getElementById("monthlyACHChart").getContext("2d");

const monthlyACHChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        datasets: [
            {
                label: "PLAN ACP",
                data: [
                    149, 149, 149, 149, 149, 149, 149, 149, 149, 149, 149, 149,
                ],
                backgroundColor: "rgba(0,0,0,0)",
                borderColor: "#FF5200",
                borderWidth: 1,
                barThickness: 20,
                grouped: false,
                order: 3,
            },
            {
                label: "ACT ACP",
                data: [131, 243, 178, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: "#FF914D",
                barThickness: 20,
                grouped: false,
                order: 2,
            },
            {
                label: "ACH ACP",
                data: [1.31, 2.43, 1.78, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                type: "line",
                yAxisID: "y1",
                borderColor: "green",
                backgroundColor: "green",
                pointBackgroundColor: "green",
                pointRadius: 5,
                borderWidth: 0,
                showLine: false,
                fill: false,
                tension: 0.3,
                order: 1,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "top",
                labels: { color: "black" },
            },
            // datalabels: {
            //     display: true,
            //     color: "black",
            //     font: { weight: "bold", size: 12 },
            //     formatter: (value) => value.toFixed(1),
            // },
        },
        scales: {
            x: {
                stacked: false,
                ticks: { color: "black" },
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Points",
                    color: "black",
                },
                ticks: { color: "black" },
            },
            y1: {
                position: "right",
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: {
                    display: true,
                    text: "ACH ACP",
                    color: "black",
                },
                ticks: { color: "black" },
            },
        },
    },
    // plugins: [ChartDataLabels]
});

const cummLabels = [
    "W1",
    "W2",
    "W3",
    "W4",
    "W5",
    "W6",
    "W7",
    "W8",
    "W9",
    "W10",
    "W11",
    "W12",
    "W13",
    "W14",
    "W15",
    "W16",
    "W17",
    "W18",
    "W19",
    "W20",
    "W21",
    "W22",
    "W23",
    "W24",
    "W25",
    "W26",
];

const cummPlanData = [
    50, 120, 190, 260, 330, 400, 470, 540, 610, 680, 750, 820, 890, 960, 1030,
    1100, 1170, 1240, 1310, 1380, 1450, 1520, 1590, 1660, 1730, 1800,
];

const cummaAtualData = [
    45, 110, 180, 190, 200, 280, 360, 430, 490, 560, 620, 670, 710, 740, 770,
    750, 750, 750, 750, 750, 750, 750, 750, 750, 750, 750,
];

new Chart(document.getElementById("cumulativeChart"), {
    type: "line",
    data: {
        labels: cummLabels,
        datasets: [
            {
                label: "CUMM PLAN",
                data: cummPlanData,
                borderColor: "orange",
                backgroundColor: "transparent",
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.3,
            },
            {
                label: "CUMM ACT",
                data: cummaAtualData,
                borderColor: "green",
                backgroundColor: "transparent",
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.3,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    font: {
                        weight: "bold",
                    },
                },
            },
            title: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 1400,
            },
        },
    },
});

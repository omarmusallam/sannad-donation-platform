import "./bootstrap";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";

window.Alpine = Alpine;
Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    if (!window.__dashboard) return;

    const isRtl = !!window.__dashboard.isRtl;

    const common = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                rtl: isRtl,
                textDirection: isRtl ? "rtl" : "ltr",
                labels: { boxWidth: 10, boxHeight: 10 },
            },
            tooltip: {
                rtl: isRtl,
                textDirection: isRtl ? "rtl" : "ltr",
            },
        },
    };

    // -----------------------
    // Daily (Line)
    // -----------------------
    const dailyEl = document.getElementById("dailyChart");
    if (dailyEl) {
        dailyEl.parentElement.style.height = "190px";

        new Chart(dailyEl, {
            type: "line",
            data: {
                labels: window.__dashboard.dailyLabels || [],
                datasets: [
                    {
                        label: window.__dashboard.paidLabel || "Paid",
                        data: window.__dashboard.dailyValues || [],
                        tension: 0.35,
                        fill: false,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    },
                ],
            },
            options: {
                ...common,
                plugins: {
                    ...common.plugins,
                    legend: { display: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 0, autoSkip: true },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { drawBorder: false },
                    },
                },
            },
        });
    }

    // -----------------------
    // Status (Doughnut)
    // -----------------------
    const statusEl = document.getElementById("statusChart");
    if (statusEl) {
        statusEl.parentElement.style.height = "220px";

        new Chart(statusEl, {
            type: "doughnut",
            data: {
                labels: window.__dashboard.statusLabels || [],
                datasets: [
                    {
                        data: window.__dashboard.statusValues || [],
                        // ألوان هادئة ومناسبة للإدارة (emerald/amber/rose)
                        backgroundColor: ["#10b981", "#f59e0b", "#f43f5e"],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                ...common,
                cutout: "62%",
                plugins: {
                    ...common.plugins,
                    legend: {
                        position: "bottom",
                        rtl: isRtl,
                        textDirection: isRtl ? "rtl" : "ltr",
                        labels: { boxWidth: 10, boxHeight: 10 },
                    },
                },
            },
        });
    }
});

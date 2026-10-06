/**
 * ELMS - Chart.js Visualizations
 * Dynamic Rendering of Department Distribution, Attendance, and Leave Statistics
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Department Distribution Doughnut Chart
    const deptCanvas = document.getElementById('deptDistributionChart');
    if (deptCanvas && window.deptChartData) {
        new Chart(deptCanvas, {
            type: 'doughnut',
            data: {
                labels: window.deptChartData.labels,
                datasets: [{
                    data: window.deptChartData.counts,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 15, font: { family: 'Inter', size: 12 } }
                    }
                },
                cutout: '68%'
            }
        });
    }

    // 2. Today's Attendance Breakdown Bar Chart
    const attCanvas = document.getElementById('attendanceBreakdownChart');
    if (attCanvas && window.attendanceChartData) {
        new Chart(attCanvas, {
            type: 'bar',
            data: {
                labels: window.attendanceChartData.labels,
                datasets: [{
                    label: 'Employees Today',
                    data: window.attendanceChartData.data,
                    backgroundColor: [
                        '#10b981', // Present
                        '#ef4444', // Absent
                        '#0ea5e9', // Half Day
                        '#64748b', // Leave
                    ],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Leave Request Status Doughnut Chart
    const leaveCanvas = document.getElementById('leaveStatusChart');
    if (leaveCanvas && window.leaveChartData) {
        new Chart(leaveCanvas, {
            type: 'pie',
            data: {
                labels: window.leaveChartData.labels,
                datasets: [{
                    data: window.leaveChartData.data,
                    backgroundColor: [
                        '#10b981', // Approved
                        '#f59e0b', // Pending
                        '#ef4444', // Rejected
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 15, font: { family: 'Inter', size: 12 } }
                    }
                }
            }
        });
    }
});

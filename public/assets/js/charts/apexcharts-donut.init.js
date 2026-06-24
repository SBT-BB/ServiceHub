// ApexCharts Donut Chart for Booking Status Overview
// This script reads booking counts from data attributes on the #booking-status-chart element
// and renders a donut chart using ApexCharts. It follows the project's pattern of
// pushing chart configs into the global `allCharts` array so that theme changes are handled.

(function () {
    const el = document.getElementById('booking-status-chart');
    if (!el) return;

    // Parse data attributes safely, default to 0 if missing
    const pending = parseInt(el.dataset.pending) || 0;
    const confirmed = parseInt(el.dataset.confirmed) || 0;
    const completed = parseInt(el.dataset.completed) || 0;
    const cancelled = parseInt(el.dataset.cancelled) || 0;

    const series = [pending, confirmed, completed, cancelled];
    const options = {
        series: series,
        chart: {
            height: 300,
            type: 'donut',
        },
        labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
        colors: ['--bs-warning', '--bs-primary', '--bs-success', '--bs-danger'],
        legend: {
            position: 'bottom',
        },
        dataLabels: {
            formatter: function (val, opts) {
                const label = opts.w.globals.labels[opts.seriesIndex];
                return `${label}: ${val.toFixed(1)}%`;
            },
        },
    };

    // Register the chart so the existing theme handling updates it
    allCharts.push([{ id: 'booking-status-chart', data: options }]);
})();

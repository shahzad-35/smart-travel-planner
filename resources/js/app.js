import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

// Send the XSRF cookie token with every Livewire request. The cookie is
// refreshed on every response and shared across tabs, unlike the token
// rendered into the page, which goes stale in any tab or history entry
// that predates a logout (logout regenerates the session token).
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('request', ({ options, fail }) => {
        const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        if (match) {
            options.headers['X-XSRF-TOKEN'] = decodeURIComponent(match[1]);
        }

        // Safety net for click-armed overlays (trip create/save): if the
        // request itself dies (network error, 419, 500), tell them to hide
        // so the user is never stuck behind a spinner.
        fail(() => {
            window.dispatchEvent(new Event('livewire-request-failed'));
        });
    });
});


// Travel stats charts — called via Alpine x-init so it runs whenever the
// (possibly lazy-loaded) component's HTML enters the DOM.
window.initTravelStatsCharts = (el) => {
    const stats = JSON.parse(el.dataset.stats || '{}');
    const cssVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    const textColor = cssVar('--color-foreground');
    const gridColor = cssVar('--color-border');

    const statusCtx = el.querySelector('#statusChart');
    const timelineCtx = el.querySelector('#timelineChart');

    [statusCtx, timelineCtx].forEach((c) => {
        if (c && window.Chart) {
            const existing = window.Chart.getChart(c);
            if (existing) existing.destroy();
        }
    });

    if (statusCtx) {
        new window.Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats.status_distribution || {}),
                datasets: [{
                    data: Object.values(stats.status_distribution || {}),
                    backgroundColor: [cssVar('--color-primary'), cssVar('--color-secondary'), cssVar('--color-accent'), cssVar('--color-destructive')],
                    borderColor: cssVar('--color-card'),
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: { legend: { position: 'bottom', labels: { color: textColor, usePointStyle: true, boxWidth: 8 } } },
            },
        });
    }

    if (timelineCtx) {
        new window.Chart(timelineCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(stats.trips_per_year || {}),
                datasets: [{
                    label: 'Trips',
                    data: Object.values(stats.trips_per_year || {}),
                    backgroundColor: cssVar('--color-primary'),
                    hoverBackgroundColor: cssVar('--color-primary-light'),
                    borderRadius: 8,
                    maxBarThickness: 56,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: textColor }, grid: { color: gridColor } },
                    x: { ticks: { color: textColor }, grid: { display: false } },
                },
            },
        });
    }
};

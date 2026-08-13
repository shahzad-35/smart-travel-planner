import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

// Send the XSRF cookie token with every Livewire request. The cookie is
// refreshed on every response and shared across tabs, unlike the token
// rendered into the page, which goes stale in any tab or history entry
// that predates a logout (logout regenerates the session token).
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('request', ({ options }) => {
        const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        if (match) {
            options.headers['X-XSRF-TOKEN'] = decodeURIComponent(match[1]);
        }
    });
});

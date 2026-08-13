import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// NOTE: Do NOT import/start Alpine here — Livewire 3 bundles and starts its
// own Alpine instance. A second instance double-initializes every component.

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js for lightweight interactivity (unit toggle, etc.)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

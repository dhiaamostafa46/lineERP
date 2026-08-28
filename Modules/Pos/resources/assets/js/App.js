import { createApp } from 'vue';
import PosApp from './PosApp.vue';
import axios from 'axios';

// Configure Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Make sure to pass CSRF token
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const app = createApp(PosApp);
app.mount('#pos-app');

import './bootstrap';
import { createApp } from 'vue';
import TableTdComponent from './components/TableTdComponent.vue'; // Make sure this path is correct

document.querySelectorAll('.vue-td').forEach(el => {
    const message = el.dataset.message || 'Default message';
    // Pass the component directly to createApp, and props as the second argument.
    // This will mount the TableTdComponent inside the `el` (the <td>).
    createApp(TableTdComponent, { message: message }).mount(el);
});

import { createApp } from 'vue';
import PlansPage from './components/PlansPage.vue';

const el = document.getElementById('plans-app');
if (el) {
    createApp(PlansPage).mount(el);
}

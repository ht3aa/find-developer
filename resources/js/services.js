import { createApp } from 'vue';
import ServicesPage from './components/ServicesPage.vue';

const el = document.getElementById('services-app');
if (el) {
    const providers = JSON.parse(el.dataset.providers || '[]');
    const badgesUrl = el.dataset.badgesUrl || '/badges';
    createApp(ServicesPage, { providers, badgesUrl }).mount(el);
}

import { createApp } from 'vue';
import BadgesPage from './components/BadgesPage.vue';

const el = document.getElementById('badges-app');
if (el) {
    const badges = JSON.parse(el.dataset.badges || '[]');
    createApp(BadgesPage, { badges }).mount(el);
}

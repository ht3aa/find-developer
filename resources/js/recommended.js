import { createApp } from 'vue';
import RecommendedPage from './components/RecommendedPage.vue';

const el = document.getElementById('recommended-app');
if (el) {
    const developers = JSON.parse(el.dataset.developers || '[]');
    const isAdmin = el.dataset.isAdmin === '1';
    const isLoggedIn = el.dataset.isLoggedIn === '1';
    createApp(RecommendedPage, { developers, isAdmin, isLoggedIn }).mount(el);
}

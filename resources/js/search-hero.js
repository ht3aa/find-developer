import { createApp } from 'vue';
import SearchHeroApp from './components/SearchHeroApp.vue';

const el = document.getElementById('search-hero-app');
if (el) {
    const totalCount = parseInt(el.dataset.totalCount || '0', 10);
    const registerUrl = el.dataset.registerUrl || '/register';
    createApp(SearchHeroApp, { totalCount, registerUrl }).mount(el);
}

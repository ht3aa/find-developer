import { createApp } from 'vue';
import ExperienceTasksPage from './components/ExperienceTasksPage.vue';

const el = document.getElementById('experience-tasks-app');
if (el) {
    const tasks = JSON.parse(el.dataset.tasks || '[]');
    createApp(ExperienceTasksPage, { tasks }).mount(el);
}

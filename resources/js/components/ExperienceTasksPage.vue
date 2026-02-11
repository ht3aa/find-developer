<template>
  <div class="xp-vue-container">
    <!-- Header -->
    <div class="xp-vue-header">
      <DecryptedText
        text="Get Experience"
        :speed="60"
        :sequential="true"
        revealDirection="start"
        animateOn="view"
        className="xp-vue-title-char"
        encryptedClassName="xp-vue-title-char encrypted"
        parentClassName="xp-vue-title"
      />
      <p class="xp-vue-subtitle">Small tasks to build your experience and earn XP.</p>
      <p class="xp-vue-note">All tasks are supervised by the task owner and our team.</p>
    </div>

    <!-- Search -->
    <div class="xp-vue-search">
      <svg class="xp-vue-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input
        v-model="search"
        type="text"
        placeholder="Search tasks..."
        class="xp-vue-search-input"
      />
      <button v-if="search" @click="search = ''" class="xp-vue-search-clear">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Count -->
    <div class="xp-vue-results">
      <span class="xp-vue-results-label">Found</span>
      <span class="xp-vue-results-num">{{ filteredTasks.length }}</span>
      <span class="xp-vue-results-label">task{{ filteredTasks.length !== 1 ? 's' : '' }}</span>
    </div>

    <!-- Grid -->
    <div v-if="filteredTasks.length" class="xp-vue-grid">
      <SpotlightCard
        v-for="task in filteredTasks"
        :key="task.id"
        className="xp-vue-card"
        :spotlightColor="statusSpotlight(task.status)"
      >
        <!-- Card Header -->
        <div class="xp-card-top">
          <h2 class="xp-card-title">{{ task.title }}</h2>
          <span :class="['xp-card-status', `xp-card-status--${task.status}`]">
            {{ statusLabel(task.status) }}
          </span>
        </div>

        <!-- Meta Row -->
        <div class="xp-card-meta">
          <div v-if="task.experience_gain" class="xp-card-meta-item xp-card-meta-xp">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
            <CountUp :to="task.xp_value" :duration="1.5" separator="," class="xp-card-xp-num" />
            <span>XP</span>
          </div>
          <div v-if="task.price" class="xp-card-meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ formatNumber(task.price) }} {{ task.price_currency || 'IQD' }}</span>
          </div>
          <div class="xp-card-meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            <span>{{ task.developers_count }} / {{ task.required_developers_count }}</span>
          </div>
        </div>

        <!-- Body (flex:1 to push footer down) -->
        <div class="xp-card-body">
          <!-- Description -->
          <div class="xp-card-section">
            <p class="xp-card-desc" v-if="!expandedDesc[task.id]">{{ truncate(task.description_plain, 160) }}</p>
            <p class="xp-card-desc" v-else>{{ task.description_plain }}</p>
            <button v-if="task.description_plain && task.description_plain.length > 160" @click="toggleDesc(task.id)" class="xp-card-toggle">
              {{ expandedDesc[task.id] ? 'Read less' : 'Read more' }}
            </button>
          </div>

          <!-- Requirements -->
          <div v-if="task.requirements" class="xp-card-section xp-card-req">
            <strong class="xp-card-section-title">Requirements:</strong>
            <div class="xp-card-req-text" :class="{ 'xp-card-req-collapsed': !expandedReq[task.id] }">
              <p v-for="(line, i) in formatLines(task.requirements)" :key="i" class="xp-card-req-line">{{ line }}</p>
            </div>
            <button @click="toggleReq(task.id)" class="xp-card-toggle">
              {{ expandedReq[task.id] ? 'Read less' : 'Read more' }}
            </button>
          </div>

          <!-- Rewards -->
          <div v-if="task.rewards" class="xp-card-section xp-card-rewards-section">
            <strong class="xp-card-section-title">Rewards:</strong>
            <div class="xp-card-rewards-box">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0016.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.52.952m0 0a6.003 6.003 0 01-2.52-.952" />
              </svg>
              <span>{{ task.rewards }}</span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="xp-card-footer">
          <a
            :href="emailLink(task)"
            class="xp-card-btn"
          >
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.5-9.75-6.5" />
            </svg>
            Email Us
          </a>
          <span class="xp-card-date">{{ task.time_ago }}</span>
        </div>
      </SpotlightCard>
    </div>

    <!-- Empty -->
    <div v-else class="xp-vue-empty">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="xp-vue-empty-icon">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
      <h3>No tasks found</h3>
      <p>Try adjusting your search. New tasks are added regularly.</p>
      <button @click="search = ''" class="xp-vue-empty-btn">Clear search</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import SpotlightCard from './vue-bits/SpotlightCard.vue';
import DecryptedText from './vue-bits/DecryptedText.vue';
import CountUp from './vue-bits/CountUp.vue';

const props = defineProps({
  tasks: { type: Array, default: () => [] },
});

const search = ref('');
const expandedDesc = ref({});
const expandedReq = ref({});

const toggleDesc = (id) => { expandedDesc.value[id] = !expandedDesc.value[id]; };
const toggleReq = (id) => { expandedReq.value[id] = !expandedReq.value[id]; };

const formatLines = (text) => {
  if (!text) return [];
  return text.split('\n').map(l => l.replace(/^[-•]\s*/, '')).filter(l => l.trim());
};

const filteredTasks = computed(() => {
  if (!search.value.trim()) return props.tasks;
  const q = search.value.toLowerCase();
  return props.tasks.filter(t =>
    t.title.toLowerCase().includes(q) ||
    (t.description_plain || '').toLowerCase().includes(q) ||
    (t.rewards || '').toLowerCase().includes(q)
  );
});

const statusLabel = (s) => ({
  open: 'Open',
  in_progress: 'In Progress',
  completed: 'Completed',
  cancelled: 'Cancelled',
}[s] || s);

const statusSpotlight = (s) => ({
  open: 'rgba(16, 185, 129, 0.2)',
  in_progress: 'rgba(236, 159, 22, 0.2)',
  completed: 'rgba(100, 116, 139, 0.15)',
}[s] || 'rgba(255,255,255,0.15)');

const formatNumber = (n) => n ? Number(n).toLocaleString() : '0';

const truncate = (text, len) => {
  if (!text) return '';
  return text.length > len ? text.substring(0, len) + '...' : text;
};

const emailLink = (task) => {
  const subject = encodeURIComponent(`Get Experience: ${task.title}`);
  const body = encodeURIComponent(`Hello, I would like to express interest in the task: ${task.title}`);
  return `mailto:ht3aa2001@gmail.com?subject=${subject}&body=${body}`;
};
</script>

<style>
/* ========== Experience Tasks Vue Page ========== */
.xp-vue-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4rem;
}

/* Header */
.xp-vue-header {
  text-align: center;
  margin-bottom: 2rem;
}

.xp-vue-title {
  font-size: 2.75rem;
  font-weight: 800;
  letter-spacing: -0.025em;
  margin-bottom: 0.5rem;
  display: block;
}

.xp-vue-title-char {
  color: var(--text-primary, #0f172a);
}

.xp-vue-title-char.encrypted {
  color: var(--color-primary, #ec9f16);
}

.xp-vue-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary, #475569);
  margin: 0 0 0.4rem;
}

.xp-vue-note {
  font-size: 0.8rem;
  color: var(--text-muted, #94a3b8);
  margin: 0;
}

/* Search */
.xp-vue-search {
  position: relative;
  max-width: 520px;
  margin: 0 auto 1.5rem;
}

.xp-vue-search-icon {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.1rem;
  height: 1.1rem;
  color: var(--text-muted, #94a3b8);
  pointer-events: none;
}

.xp-vue-search-input {
  width: 100%;
  padding: 0.65rem 2.5rem 0.65rem 2.75rem;
  border: 1px solid var(--border-primary, #e2e8f0);
  border-radius: 999px;
  background: var(--bg-primary, #fff);
  color: var(--text-primary, #0f172a);
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.xp-vue-search-input::placeholder {
  color: var(--text-muted, #94a3b8);
}

.xp-vue-search-input:focus {
  border-color: var(--color-primary, #ec9f16);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 236, 159, 22), 0.12);
}

.xp-vue-search-clear {
  position: absolute;
  right: 0.7rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-muted, #94a3b8);
  cursor: pointer;
  padding: 0.2rem;
  display: flex;
  border-radius: 50%;
  transition: color 0.2s;
}

.xp-vue-search-clear:hover {
  color: var(--text-primary, #0f172a);
}

/* Results */
.xp-vue-results {
  text-align: center;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  font-size: 0.85rem;
}

.xp-vue-results-label {
  color: var(--text-tertiary, #64748b);
}

.xp-vue-results-num {
  font-weight: 700;
  color: var(--color-primary, #ec9f16);
  font-size: 1rem;
}

/* Grid */
.xp-vue-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .xp-vue-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .xp-vue-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* Card (SpotlightCard inner) */
.xp-vue-card {
  background: var(--bg-primary, #fff) !important;
  border-color: var(--border-primary, #e2e8f0) !important;
  border-radius: 1rem !important;
  padding: 0 !important;
  display: flex !important;
  flex-direction: column !important;
  height: 100%;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.xp-vue-card:hover {
  border-color: var(--color-primary, #ec9f16) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

/* Card Top */
.xp-card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1.25rem 1.25rem 0;
}

.xp-card-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text-primary, #0f172a);
  margin: 0;
  line-height: 1.35;
  flex: 1;
}

.xp-card-status {
  font-size: 0.68rem;
  font-weight: 600;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  white-space: nowrap;
  flex-shrink: 0;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.xp-card-status--open {
  background: rgba(16, 185, 129, 0.12);
  color: #059669;
}

.xp-card-status--in_progress {
  background: rgba(236, 159, 22, 0.12);
  color: #b47d0a;
}

.xp-card-status--completed {
  background: rgba(100, 116, 139, 0.12);
  color: #475569;
}

/* Meta */
.xp-card-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid var(--border-primary, #e2e8f0);
}

.xp-card-meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.78rem;
  color: var(--text-tertiary, #64748b);
}

.xp-card-meta-item svg {
  width: 0.9rem;
  height: 0.9rem;
  flex-shrink: 0;
  opacity: 0.6;
}

.xp-card-meta-xp {
  color: var(--color-primary, #ec9f16);
  font-weight: 600;
}

.xp-card-meta-xp svg {
  opacity: 1;
  color: var(--color-primary, #ec9f16);
}

.xp-card-xp-num {
  font-weight: 700;
}

/* Body */
.xp-card-body {
  flex: 1;
  padding: 1rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.xp-card-section {
  position: relative;
}

.xp-card-desc {
  font-size: 0.85rem;
  color: var(--text-secondary, #475569);
  line-height: 1.6;
  margin: 0;
}

.xp-card-toggle {
  display: inline-block;
  margin-top: 0.25rem;
  padding: 0;
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  background: none;
  border: none;
  cursor: pointer;
  transition: opacity 0.2s;
}

.xp-card-toggle:hover {
  opacity: 0.7;
}

/* Requirements */
.xp-card-req {
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-primary, #e2e8f0);
}

.xp-card-section-title {
  display: block;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--text-primary, #0f172a);
  margin-bottom: 0.35rem;
}

.xp-card-req-text {
  font-size: 0.8rem;
  color: var(--text-secondary, #475569);
  line-height: 1.55;
}

.xp-card-req-collapsed {
  max-height: 4rem;
  overflow: hidden;
  -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
  mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
}

.xp-card-req-line {
  margin: 0 0 0.2rem;
  padding-left: 0.85rem;
  position: relative;
}

.xp-card-req-line::before {
  content: '•';
  position: absolute;
  left: 0;
  color: var(--color-primary, #ec9f16);
  font-weight: 700;
}

/* Rewards */
.xp-card-rewards-section {
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-primary, #e2e8f0);
  margin-top: auto;
}

.xp-card-rewards-box {
  display: flex;
  align-items: flex-start;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: var(--text-secondary, #475569);
  padding: 0.55rem 0.7rem;
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.05);
  border: 1px solid rgba(var(--primary-rgb, 236, 159, 22), 0.12);
  border-radius: 0.45rem;
  line-height: 1.5;
}

.xp-card-rewards-box svg {
  width: 0.9rem;
  height: 0.9rem;
  flex-shrink: 0;
  color: var(--color-primary, #ec9f16);
  margin-top: 0.1rem;
}

/* Footer */
.xp-card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1.25rem;
  border-top: 1px solid var(--border-primary, #e2e8f0);
  gap: 0.5rem;
}

.xp-card-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.4rem 0.85rem;
  font-size: 0.78rem;
  font-weight: 600;
  color: #fff;
  background: var(--color-primary, #ec9f16);
  border: none;
  border-radius: 0.4rem;
  text-decoration: none;
  transition: all 0.2s ease;
}

.xp-card-btn svg {
  width: 0.85rem;
  height: 0.85rem;
}

.xp-card-btn:hover {
  background: var(--color-primary-dark, #c98510);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(var(--primary-rgb, 236, 159, 22), 0.3);
}

.xp-card-date {
  font-size: 0.72rem;
  color: var(--text-muted, #94a3b8);
  white-space: nowrap;
}

/* Empty */
.xp-vue-empty {
  text-align: center;
  padding: 4rem 1rem;
}

.xp-vue-empty-icon {
  width: 56px;
  height: 56px;
  color: var(--text-muted, #94a3b8);
  margin: 0 auto 1rem;
  opacity: 0.4;
}

.xp-vue-empty h3 {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.4rem;
}

.xp-vue-empty p {
  font-size: 0.9rem;
  color: var(--text-tertiary, #64748b);
  margin: 0 0 1.25rem;
}

.xp-vue-empty-btn {
  padding: 0.5rem 1.25rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  background: none;
  border: 1.5px solid var(--color-primary, #ec9f16);
  border-radius: 0.4rem;
  cursor: pointer;
  transition: all 0.2s;
}

.xp-vue-empty-btn:hover {
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.08);
}

/* ========== Dark Mode ========== */
html.dark .xp-vue-card {
  background: var(--bg-primary) !important;
  border-color: var(--border-primary) !important;
}

html.dark .xp-vue-card:hover {
  border-color: var(--color-primary) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

html.dark .xp-card-status--open {
  background: rgba(16, 185, 129, 0.18);
  color: #34d399;
}

html.dark .xp-card-status--in_progress {
  background: rgba(236, 159, 22, 0.18);
  color: #f0b844;
}

html.dark .xp-card-status--completed {
  background: rgba(100, 116, 139, 0.18);
  color: #94a3b8;
}

html.dark .xp-vue-search-input {
  background: var(--bg-secondary);
  border-color: var(--border-primary);
  color: var(--text-primary);
}

html.dark .xp-card-rewards-box {
  background: rgba(var(--primary-rgb), 0.08);
  border-color: rgba(var(--primary-rgb), 0.15);
}

html.dark .xp-card-req-collapsed {
  -webkit-mask-image: linear-gradient(to bottom, white 60%, transparent 100%);
  mask-image: linear-gradient(to bottom, white 60%, transparent 100%);
}
</style>

<template>
  <div class="svc-vue-container">
    <!-- Header -->
    <div class="svc-vue-header">
      <DecryptedText
        text="Our Services"
        :speed="60"
        :sequential="true"
        revealDirection="center"
        animateOn="view"
        className="svc-vue-title-char"
        encryptedClassName="svc-vue-title-char encrypted"
        parentClassName="svc-vue-title"
      />
      <p class="svc-vue-subtitle">Professional development services offered by our partners</p>
    </div>

    <!-- Empty State -->
    <div v-if="providers.length === 0" class="svc-vue-empty">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="svc-vue-empty-icon">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3>No services available</h3>
      <p>Check back later for available services.</p>
    </div>

    <!-- Provider Cards -->
    <div v-else class="svc-vue-grid">
      <div v-for="(provider, idx) in providers" :key="idx" class="svc-provider">
        <!-- Provider Header -->
        <div class="svc-provider-header">
          <div class="svc-provider-avatar">
            <span>{{ initials(provider.user.name) }}</span>
          </div>
          <div class="svc-provider-info">
            <h2 class="svc-provider-name">{{ provider.user.name }}</h2>
            <a v-if="provider.user.linkedin_url" :href="provider.user.linkedin_url" target="_blank" rel="noopener noreferrer" class="svc-provider-linkedin">
              <svg fill="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
              LinkedIn
            </a>
          </div>
        </div>

        <!-- Services Grid -->
        <div class="svc-list">
          <SpotlightCard
            v-for="service in provider.services"
            :key="service.id"
            className="svc-card"
            :spotlightColor="service.is_active ? 'rgba(16, 185, 129, 0.15)' : 'rgba(107, 114, 128, 0.1)'"
          >
            <!-- Badges -->
            <div v-if="service.badges && service.badges.length > 0" class="svc-badge-row">
              <a v-for="badge in service.badges" :key="badge.id" :href="badgesUrl" class="svc-badge-icon" :style="{ '--badge-color': badge.color }">
                <component :is="getIcon(badge.icon)" :size="14" :stroke-width="2" />
                <span class="svc-badge-tooltip">{{ badge.name }}</span>
              </a>
            </div>

            <!-- Header -->
            <div class="svc-card-top">
              <h4 class="svc-card-name">{{ service.name }}</h4>
              <span :class="['svc-card-status', service.is_active ? 'svc-card-status--active' : 'svc-card-status--inactive']">
                {{ service.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>

            <!-- Description -->
            <div v-if="service.description" class="svc-card-desc-wrap">
              <div
                class="svc-card-desc"
                :class="{ 'svc-card-desc-collapsed': !expandedDesc[service.id] }"
                v-html="service.description"
              ></div>
              <button v-if="isLongDesc(service.description)" @click="toggleDesc(service.id)" class="svc-card-toggle">
                {{ expandedDesc[service.id] ? 'Show less' : 'Read more' }}
              </button>
            </div>

            <!-- Meta -->
            <div class="svc-card-meta">
              <div v-if="service.price" class="svc-card-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>
                </svg>
                <span>{{ formatNumber(service.price) }} {{ service.price_currency || 'IQD' }}</span>
              </div>
              <div v-if="service.time_minutes" class="svc-card-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>{{ service.time_minutes }} min</span>
              </div>
              <div class="svc-card-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                </svg>
                <span>{{ service.appointments_count }} booked</span>
              </div>
            </div>

            <!-- Contact Button -->
            <a :href="emailLink(service, provider.user)" target="_blank" rel="noopener noreferrer" class="svc-card-btn">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
              Contact Us
            </a>
          </SpotlightCard>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import SpotlightCard from './vue-bits/SpotlightCard.vue';
import DecryptedText from './vue-bits/DecryptedText.vue';
import {
  BadgeCheck,
  ShieldAlert,
  Users,
  Flame,
  CodeXml,
  Star,
  Rocket,
} from 'lucide-vue-next';

const props = defineProps({
  providers: { type: Array, default: () => [] },
  badgesUrl: { type: String, default: '/badges' },
});

const expandedDesc = ref({});

const toggleDesc = (id) => {
  expandedDesc.value[id] = !expandedDesc.value[id];
};

const initials = (name) => {
  if (!name) return '';
  return name.substring(0, 2).toUpperCase();
};

const isLongDesc = (html) => {
  if (!html) return false;
  const text = html.replace(/<[^>]*>/g, '');
  return text.length > 150;
};

const formatNumber = (n) => n ? Number(n).toLocaleString() : '0';

const emailLink = (service, user) => {
  const subject = encodeURIComponent('Service Inquiry: ' + service.name);
  const body = encodeURIComponent('Hello, I would like to inquire about ' + service.name + ' service. For user: ' + user.name);
  return `mailto:ht3aa2001@gmail.com?subject=${subject}&body=${body}`;
};

const iconMap = {
  'badge-check': BadgeCheck,
  'shield-alert': ShieldAlert,
  'users': Users,
  'flame': Flame,
  'code-xml': CodeXml,
  'star': Star,
  'rocket': Rocket,
};

const getIcon = (iconName) => {
  return iconMap[iconName] || BadgeCheck;
};
</script>

<style>
/* ========== Services Vue Page ========== */
.svc-vue-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4rem;
}

/* Header */
.svc-vue-header {
  text-align: center;
  margin-bottom: 3rem;
}

.svc-vue-title {
  font-size: 2.75rem;
  font-weight: 800;
  letter-spacing: -0.025em;
  margin-bottom: 0.5rem;
  display: block;
}

.svc-vue-title-char {
  color: var(--text-primary, #0f172a);
}

.svc-vue-title-char.encrypted {
  color: var(--color-primary, #ec9f16);
}

.svc-vue-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary, #475569);
  margin: 0;
}

/* Grid */
.svc-vue-grid {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

/* Provider */
.svc-provider {
  background: var(--bg-primary, #fff);
  border: 1px solid var(--border-primary, #e2e8f0);
  border-radius: 1.25rem;
  padding: 1.75rem;
  transition: border-color 0.3s ease;
}

.svc-provider:hover {
  border-color: rgba(var(--primary-rgb, 236, 159, 22), 0.3);
}

.svc-provider-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid var(--border-primary, #e2e8f0);
}

.svc-provider-avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary, #ec9f16), var(--color-secondary, #e67e22));
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.svc-provider-avatar span {
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}

.svc-provider-info {
  flex: 1;
}

.svc-provider-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.25rem;
}

.svc-provider-linkedin {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.82rem;
  color: var(--color-primary, #ec9f16);
  text-decoration: none;
  font-weight: 500;
  transition: opacity 0.2s;
}

.svc-provider-linkedin:hover {
  opacity: 0.7;
}

/* Service List */
.svc-list {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
  align-items: stretch;
}

.svc-list > * {
  height: 100%;
  display: flex;
  flex-direction: column;
}

@media (min-width: 768px) {
  .svc-list {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Card */
.svc-card {
  background: var(--bg-primary, #fff) !important;
  border-color: var(--border-primary, #e2e8f0) !important;
  border-radius: 1rem !important;
  padding: 0 !important;
  display: flex !important;
  flex-direction: column !important;
  height: 100%;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.svc-card:hover {
  border-color: var(--color-primary, #ec9f16) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

/* Badges */
.svc-badge-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  padding: 1rem 1.25rem 0;
}

.svc-badge-icon {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--badge-color, #ec9f16) 15%, transparent);
  border: 1.5px solid color-mix(in srgb, var(--badge-color, #ec9f16) 30%, transparent);
  color: var(--badge-color, #ec9f16);
  text-decoration: none;
  transition: transform 0.2s, box-shadow 0.2s;
}

.svc-badge-icon:hover {
  transform: scale(1.15);
  box-shadow: 0 0 8px color-mix(in srgb, var(--badge-color, #ec9f16) 30%, transparent);
}

.svc-badge-tooltip {
  position: absolute;
  bottom: calc(100% + 6px);
  left: 50%;
  transform: translateX(-50%);
  background: var(--bg-primary, #1e293b);
  color: var(--text-primary, #fff);
  font-size: 0.65rem;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  border-radius: 0.3rem;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
  border: 1px solid var(--border-primary, #334155);
  z-index: 10;
}

.svc-badge-icon:hover .svc-badge-tooltip {
  opacity: 1;
}

/* Card Top */
.svc-card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.25rem 0;
}

.svc-card-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text-primary, #0f172a);
  margin: 0;
  line-height: 1.35;
  flex: 1;
}

.svc-card-status {
  font-size: 0.68rem;
  font-weight: 600;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  white-space: nowrap;
  flex-shrink: 0;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.svc-card-status--active {
  background: rgba(16, 185, 129, 0.12);
  color: #059669;
}

.svc-card-status--inactive {
  background: rgba(100, 116, 139, 0.12);
  color: #475569;
}

/* Description */
.svc-card-desc-wrap {
  padding: 0.75rem 1.25rem;
  flex: 1;
}

.svc-card-desc {
  font-size: 0.85rem;
  color: var(--text-secondary, #475569);
  line-height: 1.6;
}

.svc-card-desc-collapsed {
  max-height: 4rem;
  overflow: hidden;
  -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
  mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
}

.svc-card-desc :deep(ul),
.svc-card-desc :deep(ol) {
  margin: 0.5rem 0;
  padding-left: 1.5rem;
}

.svc-card-desc :deep(li) {
  margin: 0.25rem 0;
}

.svc-card-desc :deep(p) {
  margin: 0.5rem 0;
}

.svc-card-desc :deep(p:first-child) {
  margin-top: 0;
}

.svc-card-desc :deep(p:last-child) {
  margin-bottom: 0;
}

.svc-card-desc :deep(strong),
.svc-card-desc :deep(b) {
  font-weight: 600;
  color: var(--text-primary, #0f172a);
}

.svc-card-desc :deep(a) {
  color: var(--color-primary, #ec9f16);
  text-decoration: underline;
}

.svc-card-toggle {
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

.svc-card-toggle:hover {
  opacity: 0.7;
}

/* Meta */
.svc-card-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  padding: 0.85rem 1.25rem;
  border-top: 1px solid var(--border-primary, #e2e8f0);
  margin-top: auto;
}

.svc-card-meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.78rem;
  color: var(--text-tertiary, #64748b);
}

.svc-card-meta-item svg {
  width: 0.9rem;
  height: 0.9rem;
  flex-shrink: 0;
  opacity: 0.6;
}

/* Contact Button */
.svc-card-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  margin: 0 1.25rem 1.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.82rem;
  font-weight: 600;
  color: #fff;
  background: var(--color-primary, #ec9f16);
  border: none;
  border-radius: 0.5rem;
  text-decoration: none;
  transition: all 0.2s ease;
}

.svc-card-btn svg {
  width: 0.9rem;
  height: 0.9rem;
}

.svc-card-btn:hover {
  background: var(--color-primary-dark, #c98510);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(var(--primary-rgb, 236, 159, 22), 0.3);
}

/* Empty */
.svc-vue-empty {
  text-align: center;
  padding: 4rem 1rem;
}

.svc-vue-empty-icon {
  width: 56px;
  height: 56px;
  color: var(--text-muted, #94a3b8);
  margin: 0 auto 1rem;
  opacity: 0.4;
}

.svc-vue-empty h3 {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.4rem;
}

.svc-vue-empty p {
  font-size: 0.9rem;
  color: var(--text-tertiary, #64748b);
  margin: 0;
}

/* Mobile */
@media (max-width: 640px) {
  .svc-vue-container {
    padding: 2rem 1rem 3rem;
  }

  .svc-vue-title {
    font-size: 2rem;
  }

  .svc-provider {
    padding: 1.25rem;
  }

  .svc-provider-header {
    flex-direction: column;
    align-items: flex-start;
  }
}

/* ========== Dark Mode ========== */
html.dark .svc-provider {
  background: var(--bg-primary);
  border-color: var(--border-primary);
}

html.dark .svc-card {
  background: var(--bg-secondary, #1e293b) !important;
  border-color: var(--border-primary) !important;
}

html.dark .svc-card:hover {
  border-color: var(--color-primary) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

html.dark .svc-card-status--active {
  background: rgba(16, 185, 129, 0.18);
  color: #34d399;
}

html.dark .svc-card-status--inactive {
  background: rgba(100, 116, 139, 0.18);
  color: #94a3b8;
}

html.dark .svc-card-desc-collapsed {
  -webkit-mask-image: linear-gradient(to bottom, white 60%, transparent 100%);
  mask-image: linear-gradient(to bottom, white 60%, transparent 100%);
}

html.dark .svc-badge-tooltip {
  background: var(--bg-secondary);
  border-color: var(--border-primary);
}
</style>

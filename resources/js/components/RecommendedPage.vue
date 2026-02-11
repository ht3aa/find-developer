<template>
  <div class="rec-vue-container">
    <!-- Header -->
    <div class="rec-vue-header">
      <DecryptedText
        text="Recommended By Us"
        :speed="60"
        :sequential="true"
        revealDirection="center"
        animateOn="view"
        className="rec-vue-title-char"
        encryptedClassName="rec-vue-title-char encrypted"
        parentClassName="rec-vue-title"
      />
      <p class="rec-vue-subtitle">Handpicked top-tier developers ready to bring your projects to life</p>
    </div>

    <!-- Order Info -->
    <div v-if="developers.length > 0" class="rec-vue-info">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>Results are ordered by number of recommendations</span>
    </div>

    <!-- Grid -->
    <div v-if="developers.length > 0" class="rec-vue-grid">
      <SpotlightCard
        v-for="dev in developers"
        :key="dev.id"
        className="rec-card"
        spotlightColor="rgba(236, 159, 22, 0.15)"
      >
        <!-- Recommended Badge -->
        <div class="rec-card-badge">
          <svg fill="currentColor" viewBox="0 0 20 20" width="14" height="14">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Recommended
        </div>

        <!-- Developer Badges -->
        <div v-if="dev.badges && dev.badges.length > 0" class="rec-badge-row">
          <span v-for="badge in dev.badges" :key="badge.id" class="rec-badge-icon" :style="{ '--badge-color': badge.color }">
            <component :is="getBadgeIcon(badge.icon)" :size="14" :stroke-width="2" />
            <span class="rec-badge-tooltip">{{ badge.name }}</span>
          </span>
        </div>

        <!-- Two Column Layout -->
        <div class="rec-card-grid">
          <!-- Column 1: Portfolio -->
          <div class="rec-col-work">
            <h4 class="rec-section-title">Portfolio & Work</h4>
            <div class="rec-work-items">
              <div v-for="project in dev.projects" :key="project.title" class="rec-work-item">
                <h5 class="rec-work-title">{{ project.title }}</h5>
                <p v-if="project.description" class="rec-work-desc">
                  {{ truncate(project.description, 100) }}
                </p>
                <a v-if="project.link" :href="project.link" target="_blank" class="rec-work-link">
                  View Project
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                </a>
              </div>
              <div v-if="dev.projects.length === 0" class="rec-work-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="28" height="28">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p>No portfolio items yet</p>
              </div>
              <a v-if="dev.projects_count > 6 && dev.projects_url && dev.is_premium" :href="dev.projects_url" class="rec-work-all">
                View All Projects ({{ dev.projects_count }})
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="14" height="14">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Column 2: Details -->
          <div class="rec-col-details">
            <!-- Name & Job -->
            <div class="rec-dev-header">
              <h3 class="rec-dev-name">
                <a v-if="dev.profile_url" :href="dev.profile_url" class="rec-dev-name-link">{{ dev.name }}</a>
                <span v-else>{{ dev.name }}</span>
              </h3>
              <span class="rec-job-badge">{{ dev.job_title }}</span>
            </div>

            <!-- Details -->
            <div class="rec-dev-details">
              <div class="rec-detail-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ dev.years_of_experience }} years experience
              </div>

              <div v-if="dev.location" class="rec-detail-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ dev.location }}
              </div>

              <div v-if="dev.phone" class="rec-detail-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <a :href="'tel:' + dev.phone" class="rec-detail-link">{{ dev.phone }}</a>
              </div>

              <!-- Salary (admin only) -->
              <div v-if="isAdmin && dev.expected_salary_from && dev.expected_salary_to" class="rec-detail-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formatNumber(dev.expected_salary_from) }} - {{ formatNumber(dev.expected_salary_to) }} {{ dev.currency }}/month
              </div>
              <div v-else-if="dev.has_salary && !isAdmin" class="rec-detail-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <a href="mailto:ht3aa2001@gmail.com?subject=Subscription%20Inquiry" target="_blank" class="rec-detail-link">You need to subscribe to see the salary</a>
              </div>

              <!-- Availability -->
              <div class="rec-detail-item">
                <span v-if="dev.is_available" class="rec-avail rec-avail--yes">
                  <svg fill="currentColor" viewBox="0 0 20 20" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Available
                </span>
                <span v-else class="rec-avail rec-avail--no">
                  <svg fill="currentColor" viewBox="0 0 20 20" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  Not Available
                </span>
              </div>

              <!-- Availability Types -->
              <div v-if="dev.availability_type && dev.availability_type.length > 0" class="rec-detail-item rec-avail-types">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="rec-avail-badges">
                  <span v-for="type in dev.availability_type" :key="type.value" :class="['rec-avail-badge', 'rec-avail-badge--' + type.value]">
                    {{ type.label }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Recommendations Count -->
            <div v-if="dev.recommendations_received_count > 0" class="rec-detail-item rec-rec-count">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
              </svg>
              <span class="rec-rec-badge">{{ dev.recommendations_received_count }} {{ dev.recommendations_received_count === 1 ? 'Recommendation' : 'Recommendations' }}</span>
            </div>

            <!-- Bio -->
            <div v-if="dev.bio" class="rec-bio">
              <p v-if="!expandedBio[dev.id]">{{ truncate(dev.bio, 150) }}</p>
              <p v-else>{{ dev.bio }}</p>
              <button v-if="dev.bio.length > 150" @click="toggleBio(dev.id)" class="rec-toggle">
                {{ expandedBio[dev.id] ? 'Show less' : 'Read more' }}
              </button>
            </div>

            <!-- Skills -->
            <div v-if="dev.skills && dev.skills.length > 0" class="rec-skills">
              <span
                v-for="(skill, i) in dev.skills"
                :key="skill"
                v-show="expandedSkills[dev.id] || i < 5"
                class="rec-skill-tag"
              >
                {{ skill }}
              </span>
              <span
                v-if="dev.skills.length > 5"
                class="rec-skill-tag rec-skill-more"
                @click="toggleSkills(dev.id)"
              >
                <template v-if="!expandedSkills[dev.id]">+{{ dev.skills.length - 5 }} more</template>
                <template v-else>Show less</template>
              </span>
            </div>

            <!-- Links -->
            <div class="rec-links">
              <a v-if="dev.portfolio_url" :href="dev.portfolio_url" target="_blank" class="rec-social" title="Portfolio">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="18" height="18">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
              </a>
              <a v-if="dev.github_url" :href="dev.github_url" target="_blank" class="rec-social" title="GitHub">
                <svg fill="currentColor" viewBox="0 0 24 24" width="18" height="18">
                  <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
              </a>
              <a v-if="dev.linkedin_url" :href="dev.linkedin_url" target="_blank" class="rec-social" title="LinkedIn">
                <svg fill="currentColor" viewBox="0 0 24 24" width="18" height="18">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a :href="'mailto:' + dev.email" class="rec-social" title="Email">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="18" height="18">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </a>
            </div>

            <!-- View Profile -->
            <a v-if="dev.profile_url" :href="dev.profile_url" class="rec-profile-btn">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              View Full Profile
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </SpotlightCard>
    </div>

    <!-- Empty -->
    <div v-else class="rec-vue-empty">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="rec-vue-empty-icon">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3>No recommended developers</h3>
      <p>Check back soon for our top picks!</p>
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
  developers: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
  isLoggedIn: { type: Boolean, default: false },
});

const expandedBio = ref({});
const expandedSkills = ref({});

const toggleBio = (id) => { expandedBio.value[id] = !expandedBio.value[id]; };
const toggleSkills = (id) => { expandedSkills.value[id] = !expandedSkills.value[id]; };

const truncate = (text, len) => {
  if (!text) return '';
  return text.length > len ? text.substring(0, len) + '...' : text;
};

const formatNumber = (n) => n ? Number(n).toLocaleString() : '0';

const badgeIconMap = {
  'badge-check': BadgeCheck,
  'shield-alert': ShieldAlert,
  'users': Users,
  'flame': Flame,
  'code-xml': CodeXml,
  'star': Star,
  'rocket': Rocket,
};

const getBadgeIcon = (iconName) => {
  return badgeIconMap[iconName] || BadgeCheck;
};
</script>

<style>
/* ========== Recommended Vue Page ========== */
.rec-vue-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4rem;
}

/* Header */
.rec-vue-header {
  text-align: center;
  margin-bottom: 2rem;
}

.rec-vue-title {
  font-size: 2.75rem;
  font-weight: 800;
  letter-spacing: -0.025em;
  margin-bottom: 0.5rem;
  display: block;
}

.rec-vue-title-char {
  color: var(--text-primary, #0f172a);
}

.rec-vue-title-char.encrypted {
  color: var(--color-primary, #ec9f16);
}

.rec-vue-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary, #475569);
  margin: 0;
}

/* Info Bar */
.rec-vue-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: var(--text-tertiary, #64748b);
  margin-bottom: 2rem;
}

.rec-vue-info svg {
  opacity: 0.5;
}

/* Grid */
.rec-vue-grid {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

/* Card */
.rec-card {
  background: var(--bg-primary, #fff) !important;
  border-color: var(--border-primary, #e2e8f0) !important;
  border-radius: 1.25rem !important;
  padding: 0 !important;
  display: flex !important;
  flex-direction: column !important;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.rec-card:hover {
  border-color: var(--color-primary, #ec9f16) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

/* Recommended Badge */
.rec-card-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(236, 159, 22, 0.12);
  color: var(--color-primary, #ec9f16);
  padding: 0.35rem 0.75rem;
  margin: 1.25rem 1.25rem 0;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  width: fit-content;
}

/* Developer Badges */
.rec-badge-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem 0;
}

.rec-badge-icon {
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
  cursor: default;
  transition: transform 0.2s, box-shadow 0.2s;
}

.rec-badge-icon:hover {
  transform: scale(1.15);
}

.rec-badge-tooltip {
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

.rec-badge-icon:hover .rec-badge-tooltip {
  opacity: 1;
}

/* Two Column Layout */
.rec-card-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  padding: 1.25rem;
}

@media (max-width: 768px) {
  .rec-card-grid {
    grid-template-columns: 1fr;
  }
}

/* Work Column */
.rec-col-work {
  border-right: 1px solid var(--border-primary, #e2e8f0);
  padding-right: 1.25rem;
}

@media (max-width: 768px) {
  .rec-col-work {
    border-right: none;
    border-bottom: 1px solid var(--border-primary, #e2e8f0);
    padding-right: 0;
    padding-bottom: 1.25rem;
  }
}

.rec-section-title {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--text-tertiary, #64748b);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 0.75rem;
}

.rec-work-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.rec-work-item {
  padding: 0.6rem;
  border-radius: 0.5rem;
  border: 1px solid var(--border-primary, #e2e8f0);
  transition: border-color 0.2s;
}

.rec-work-item:hover {
  border-color: rgba(var(--primary-rgb, 236, 159, 22), 0.3);
}

.rec-work-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.25rem;
}

.rec-work-desc {
  font-size: 0.78rem;
  color: var(--text-tertiary, #64748b);
  line-height: 1.5;
  margin: 0 0 0.35rem;
}

.rec-work-link {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  text-decoration: none;
  transition: opacity 0.2s;
}

.rec-work-link:hover {
  opacity: 0.7;
}

.rec-work-empty {
  text-align: center;
  padding: 1.5rem 0.5rem;
  color: var(--text-muted, #94a3b8);
}

.rec-work-empty svg {
  margin: 0 auto 0.4rem;
  opacity: 0.4;
}

.rec-work-empty p {
  font-size: 0.8rem;
  margin: 0;
}

.rec-work-all {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  text-decoration: none;
  transition: opacity 0.2s;
}

.rec-work-all:hover {
  opacity: 0.7;
}

/* Details Column */
.rec-dev-header {
  margin-bottom: 0.75rem;
}

.rec-dev-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.35rem;
}

.rec-dev-name-link {
  color: inherit;
  text-decoration: none;
  transition: color 0.2s;
}

.rec-dev-name-link:hover {
  color: var(--color-primary, #ec9f16);
}

.rec-job-badge {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.1);
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
}

/* Detail Items */
.rec-dev-details {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  margin-bottom: 0.75rem;
}

.rec-detail-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.82rem;
  color: var(--text-secondary, #475569);
}

.rec-detail-item svg {
  flex-shrink: 0;
  color: var(--text-tertiary, #64748b);
}

.rec-detail-link {
  color: inherit;
  text-decoration: none;
}

.rec-detail-link:hover {
  color: var(--color-primary, #ec9f16);
}

/* Availability */
.rec-avail {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.82rem;
  font-weight: 600;
}

.rec-avail--yes {
  color: #10b981;
}

.rec-avail--no {
  color: #94a3b8;
}

.rec-avail-types {
  flex-wrap: wrap;
}

.rec-avail-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.3rem;
}

.rec-avail-badge {
  font-size: 0.68rem;
  font-weight: 600;
  padding: 0.15rem 0.45rem;
  border-radius: 999px;
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.1);
  color: var(--color-primary, #ec9f16);
  border: 1px solid rgba(var(--primary-rgb, 236, 159, 22), 0.2);
}

/* Recommendations */
.rec-rec-count {
  margin-top: 0.25rem;
}

.rec-rec-badge {
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
}

/* Bio */
.rec-bio {
  margin-bottom: 0.75rem;
}

.rec-bio p {
  font-size: 0.82rem;
  color: var(--text-secondary, #475569);
  line-height: 1.55;
  margin: 0 0 0.25rem;
}

.rec-toggle {
  display: inline-block;
  padding: 0;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-primary, #ec9f16);
  background: none;
  border: none;
  cursor: pointer;
  transition: opacity 0.2s;
}

.rec-toggle:hover {
  opacity: 0.7;
}

/* Skills */
.rec-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
  margin-bottom: 0.75rem;
}

.rec-skill-tag {
  font-size: 0.72rem;
  font-weight: 500;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  background: var(--bg-secondary, #f1f5f9);
  color: var(--text-secondary, #475569);
  border: 1px solid var(--border-primary, #e2e8f0);
}

.rec-skill-more {
  cursor: pointer;
  color: var(--color-primary, #ec9f16);
  border-color: rgba(var(--primary-rgb, 236, 159, 22), 0.3);
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.08);
}

/* Links */
.rec-links {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-bottom: 0.75rem;
}

.rec-social {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 0.4rem;
  border: 1px solid var(--border-primary, #e2e8f0);
  color: var(--text-tertiary, #64748b);
  text-decoration: none;
  transition: all 0.2s;
}

.rec-social:hover {
  border-color: var(--color-primary, #ec9f16);
  color: var(--color-primary, #ec9f16);
  background: rgba(var(--primary-rgb, 236, 159, 22), 0.06);
}

/* Profile Button */
.rec-profile-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.45rem 0.85rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #fff;
  background: var(--color-primary, #ec9f16);
  border: none;
  border-radius: 0.45rem;
  text-decoration: none;
  transition: all 0.2s ease;
  margin-top: auto;
}

.rec-profile-btn:hover {
  background: var(--color-primary-dark, #c98510);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(var(--primary-rgb, 236, 159, 22), 0.3);
}

/* Empty */
.rec-vue-empty {
  text-align: center;
  padding: 4rem 1rem;
}

.rec-vue-empty-icon {
  width: 56px;
  height: 56px;
  color: var(--text-muted, #94a3b8);
  margin: 0 auto 1rem;
  opacity: 0.4;
}

.rec-vue-empty h3 {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.4rem;
}

.rec-vue-empty p {
  font-size: 0.9rem;
  color: var(--text-tertiary, #64748b);
  margin: 0;
}

/* Mobile */
@media (max-width: 640px) {
  .rec-vue-container {
    padding: 2rem 1rem 3rem;
  }

  .rec-vue-title {
    font-size: 2rem;
  }
}

/* ========== Dark Mode ========== */
html.dark .rec-card {
  background: var(--bg-primary) !important;
  border-color: var(--border-primary) !important;
}

html.dark .rec-card:hover {
  border-color: var(--color-primary) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

html.dark .rec-work-item {
  background: rgba(255, 255, 255, 0.02);
  border-color: var(--border-primary);
}

html.dark .rec-skill-tag {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(255, 255, 255, 0.1);
}

html.dark .rec-social {
  border-color: var(--border-primary);
}

html.dark .rec-badge-tooltip {
  background: var(--bg-secondary);
  border-color: var(--border-primary);
}

html.dark .rec-col-work {
  border-color: var(--border-primary);
}
</style>

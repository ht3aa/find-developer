<template>
  <div class="badges-vue-container">
    <!-- Header -->
    <div class="badges-vue-header">
      <DecryptedText
        text="Developer Badges"
        :speed="60"
        :sequential="true"
        revealDirection="center"
        animateOn="view"
        className="badges-vue-title-char"
        encryptedClassName="badges-vue-title-char encrypted"
        parentClassName="badges-vue-title"
      />
      <p class="badges-vue-subtitle">Earn badges to showcase your achievements and stand out</p>
    </div>

    <!-- Empty state -->
    <div v-if="badges.length === 0" class="badges-vue-empty">
      <svg class="badges-vue-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
      <h3>No badges available</h3>
      <p>Check back later for available badges.</p>
    </div>

    <!-- Badges Grid -->
    <div v-else class="badges-vue-grid">
      <TiltCard
        v-for="badge in badges"
        :key="badge.id"
        :maxTilt="8"
        :scale="1.03"
        className="badges-vue-tilt-wrapper"
      >
        <SpotlightCard
          className="badges-vue-card"
          :spotlightColor="spotlightColor(badge.color)"
        >
          <!-- Glow orb behind icon -->
          <div class="badge-glow-orb" :style="{ background: `radial-gradient(circle, ${badge.color}30 0%, transparent 70%)` }"></div>

          <!-- Icon -->
          <div class="badge-icon-ring" :style="iconRingStyle(badge.color)">
            <div class="badge-icon-inner" :style="iconInnerStyle(badge.color)">
              <component :is="getIcon(badge.icon)" class="badge-icon-svg" :style="{ color: badge.color }" />
            </div>
          </div>

          <!-- Name -->
          <ShinyText
            :text="badge.name"
            :color="badge.color"
            speed="4s"
            className="badge-card-name"
          />

          <!-- Description -->
          <p class="badge-card-desc" v-if="badge.description">
            {{ badge.description }}
          </p>
          <p class="badge-card-desc" v-else>
            Unlock this badge by demonstrating your skills.
          </p>

          <!-- Status pill -->
          <div class="badge-card-status">
            <span class="badge-status-dot" :style="{ background: badge.color }"></span>
            Available to earn
          </div>
        </SpotlightCard>
      </TiltCard>
    </div>
  </div>
</template>

<script setup>
import SpotlightCard from './vue-bits/SpotlightCard.vue';
import DecryptedText from './vue-bits/DecryptedText.vue';
import ShinyText from './vue-bits/ShinyText.vue';
import TiltCard from './vue-bits/TiltCard.vue';
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
  badges: { type: Array, default: () => [] },
});

const spotlightColor = (color) => {
  return `${color}35`;
};

const iconRingStyle = (color) => ({
  background: `conic-gradient(from 0deg, ${color}15, ${color}40, ${color}15)`,
  border: `2px solid ${color}30`,
});

const iconInnerStyle = (color) => ({
  background: `${color}12`,
  border: `1px solid ${color}25`,
});

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
/* ========== Badges Vue Page ========== */
.badges-vue-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4rem;
}

/* Header */
.badges-vue-header {
  text-align: center;
  margin-bottom: 3.5rem;
}

.badges-vue-title {
  font-size: 2.75rem;
  font-weight: 800;
  letter-spacing: -0.025em;
  margin-bottom: 0.75rem;
  display: block;
}

.badges-vue-title-char {
  color: var(--text-primary, #0f172a);
}

.badges-vue-title-char.encrypted {
  color: var(--color-primary, #ec9f16);
}

.badges-vue-subtitle {
  font-size: 1.1rem;
  color: var(--text-tertiary, #64748b);
  margin: 0;
  font-weight: 400;
}

/* Grid */
.badges-vue-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.75rem;
}

@media (min-width: 640px) {
  .badges-vue-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .badges-vue-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }
}

/* Tilt wrapper */
.badges-vue-tilt-wrapper {
  height: 100%;
}

/* Card */
.badges-vue-card {
  background: var(--bg-primary, #ffffff) !important;
  border-color: var(--border-primary, #e2e8f0) !important;
  border-radius: 1.25rem !important;
  padding: 2rem 1.75rem !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  overflow: hidden;
  height: 100%;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.badges-vue-card:hover {
  border-color: var(--color-primary, #ec9f16) !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
}

/* Glow orb */
.badge-glow-orb {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  width: 200px;
  height: 200px;
  border-radius: 50%;
  pointer-events: none;
  opacity: 0.5;
  transition: opacity 0.4s ease;
}

.badges-vue-card:hover .badge-glow-orb {
  opacity: 0.8;
}

/* Icon ring */
.badge-icon-ring {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

.badges-vue-card:hover .badge-icon-ring {
  transform: scale(1.08);
}

.badge-icon-inner {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.badge-icon-svg {
  width: 28px;
  height: 28px;
}

/* Name */
.badge-card-name {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  letter-spacing: -0.01em;
  color: var(--text-primary, #0f172a);
  position: relative;
  z-index: 1;
}

/* Description */
.badge-card-desc {
  font-size: 0.875rem;
  color: var(--text-tertiary, #64748b);
  line-height: 1.65;
  margin: 0 0 1.25rem;
  flex: 1;
  position: relative;
  z-index: 1;
}

/* Status */
.badge-card-status {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-tertiary, #64748b);
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  border: 1px solid var(--border-primary, #e2e8f0);
  background: var(--bg-secondary, #f8fafc);
  position: relative;
  z-index: 1;
}

.badge-status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
  animation: badge-dot-pulse 2s ease-in-out infinite;
}

@keyframes badge-dot-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Empty state */
.badges-vue-empty {
  text-align: center;
  padding: 4rem 1rem;
}

.badges-vue-empty-icon {
  width: 64px;
  height: 64px;
  color: var(--text-muted, #94a3b8);
  margin: 0 auto 1rem;
  opacity: 0.5;
}

.badges-vue-empty h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary, #0f172a);
  margin: 0 0 0.5rem;
}

.badges-vue-empty p {
  font-size: 0.95rem;
  color: var(--text-tertiary, #64748b);
  margin: 0;
}

/* ========== Dark Mode ========== */
html.dark .badges-vue-card {
  background: var(--bg-primary) !important;
  border-color: var(--border-primary) !important;
}

html.dark .badges-vue-card:hover {
  border-color: var(--color-primary) !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

html.dark .badge-card-status {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(255, 255, 255, 0.1);
}
</style>

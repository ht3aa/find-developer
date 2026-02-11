<template>
  <div class="search-hero">
    <div class="search-hero-inner">
      <div class="search-hero-glow"></div>
      <h1 class="search-hero-title">
        <DecryptedText
          text="Find Your Perfect Developer"
          :speed="40"
          :sequential="true"
          revealDirection="start"
          class-name="search-hero-char"
          encrypted-class-name="search-hero-char-encrypted"
          animateOn="view"
        />
      </h1>
      <p class="search-hero-subtitle">
        <ShinyText
          text="Discover talented developers ready to bring your projects to life"
          color="#ec9f16"
          speed="4s"
        />
      </p>
      <div class="search-hero-stats">
        <div class="search-hero-stat">
          <CountUp :to="totalCount" :duration="2.5" :delay="0.3" className="search-hero-stat-number" separator="," />
          <span class="search-hero-stat-label">Developers</span>
        </div>
      </div>
      <a :href="registerUrl" class="search-hero-cta">
        <span>Register as Developer</span>
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
      </a>
    </div>
  </div>
</template>

<script setup>
import DecryptedText from './vue-bits/DecryptedText.vue';
import ShinyText from './vue-bits/ShinyText.vue';
import CountUp from './vue-bits/CountUp.vue';

defineProps({
  totalCount: { type: Number, default: 0 },
  registerUrl: { type: String, default: '/register' },
});
</script>

<style scoped>
.search-hero {
  position: relative;
  text-align: center;
  padding: 3rem 1rem 2rem;
  overflow: hidden;
}

.search-hero-inner {
  position: relative;
  z-index: 1;
  max-width: 750px;
  margin: 0 auto;
}

.search-hero-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 500px;
  height: 300px;
  background: radial-gradient(ellipse, rgba(236, 159, 22, 0.08) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

html.dark .search-hero-glow {
  background: radial-gradient(ellipse, rgba(236, 159, 22, 0.12) 0%, transparent 70%);
}

.search-hero-title {
  font-size: 2.25rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.2;
  margin-bottom: 1rem;
  color: var(--text-primary, #0f172a);
}

@media (min-width: 768px) {
  .search-hero-title {
    font-size: 3rem;
  }
}

:deep(.search-hero-char) {
  color: #ec9f16;
}

:deep(.search-hero-char-encrypted) {
  color: var(--text-muted, #94a3b8);
}

.search-hero-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary, #475569);
  margin-bottom: 1.5rem;
  font-weight: 400;
}

.search-hero-stats {
  display: flex;
  justify-content: center;
  margin-bottom: 1.75rem;
}

.search-hero-stat {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

:deep(.search-hero-stat-number) {
  font-size: 2rem;
  font-weight: 800;
  color: #ec9f16;
}

.search-hero-stat-label {
  font-size: 1rem;
  font-weight: 500;
  color: var(--text-secondary, #475569);
}

.search-hero-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.85rem 2rem;
  background: #ec9f16;
  color: white;
  border: none;
  border-radius: 9999px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  box-shadow: 0 4px 14px rgba(236, 159, 22, 0.35);
  transition: all 0.3s ease;
  cursor: pointer;
}

.search-hero-cta:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(236, 159, 22, 0.45);
  color: white;
  background: #d48e12;
}

.search-hero-cta:active {
  transform: translateY(-1px);
}

@media (max-width: 767px) {
  .search-hero {
    padding: 2rem 1rem 1.5rem;
  }
  .search-hero-title {
    font-size: 1.75rem;
  }
  :deep(.search-hero-stat-number) {
    font-size: 1.5rem;
  }
}
</style>

<template>
  <div class="plans-vue">
    <!-- Developer Plans -->
    <section class="plans-section">
      <div class="section-header">
        <DecryptedText
          text="Developer Plans"
          :speed="40"
          :sequential="true"
          animateOn="view"
          class-name="section-title-char"
          encrypted-class-name="section-title-char encrypted"
          parent-class-name="section-title"
        />
      </div>

      <div class="cards-grid cards-grid-2">
        <!-- Hosting (Subdomain) - Free -->
        <SpotlightCard
          class-name="plan-card"
          :spotlight-color="spotlightColor"
        >
          <div class="card-content">
            <h3 class="card-plan-name">Hosting (Subdomain)</h3>
            <div class="card-price">
              <span class="price-amount">Free</span>
            </div>
            <p class="price-note">&nbsp;</p>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Hosting with a subdomain (e.g., <a :href="exampleUrl" target="_blank" class="feature-link">yourname.find-developer.com</a>)</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Support for any problem you encounter</span>
              </li>
            </ul>
            <a :href="mailtoHosting" target="_blank" class="plan-btn">Contact Us</a>
          </div>
        </SpotlightCard>

        <!-- Hosting with .iq Domain -->
        <SpotlightCard
          class-name="plan-card"
          :spotlight-color="spotlightColor"
        >
          <div class="card-content">
            <h3 class="card-plan-name">Hosting with .iq Domain</h3>
            <div class="card-price">
              <CountUp :to="80000" :duration="0.8" separator="," class-name="price-amount" />
              <span class="price-currency">IQD</span>
              <span class="price-period">/ year</span>
            </div>
            <p class="price-note">+ domain from <strong>50,000 IQD / year</strong> (depends on name)</p>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Hosting with a custom .iq domain</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Custom domain like <strong>yourname.iq</strong> (price from 50,000 IQD/year). <em class="provider-note">Provided by <a href="https://www.dev-house.com" target="_blank" rel="noopener noreferrer" class="feature-link"><span dir="rtl">شركة بيت التطوير العراقي</span></a></em></span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Support for any problem you encounter</span>
              </li>
            </ul>
            <a :href="mailtoDomain" target="_blank" class="plan-btn">Contact Us</a>
          </div>
        </SpotlightCard>
      </div>
    </section>

    <!-- HR Plans -->
    <section class="plans-section">
      <div class="section-header">
        <DecryptedText
          text="HR Plans"
          :speed="40"
          :sequential="true"
          animateOn="view"
          class-name="section-title-char"
          encrypted-class-name="section-title-char encrypted"
          parent-class-name="section-title"
        />
        <p class="section-subtitle">For Recruiters and HR Professionals</p>
      </div>

      <div class="cards-grid cards-grid-1">
        <SpotlightCard class-name="plan-card plan-card-featured" :spotlight-color="spotlightColor">
          <div class="card-content">
            <div class="badge-row">
              <span class="plan-badge">Professional</span>
            </div>
            <h3 class="card-plan-name">HR</h3>
            <div class="card-price-row">
              <span class="price-original">100,000 IQD</span>
              <span class="discount-badge">50% OFF</span>
            </div>
            <div class="card-price">
              <CountUp :to="50000" :duration="0.8" separator="," class-name="price-amount" />
              <span class="price-currency">IQD</span>
              <span class="price-period">/ month</span>
            </div>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Access to the dashboard as HR user with all admin features</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>See salary and hidden fields in developer cards</span>
              </li>
            </ul>
            <a :href="mailtoHR" target="_blank" class="plan-btn">Contact Us</a>
          </div>
        </SpotlightCard>
      </div>
    </section>

    <!-- Student Plans -->
    <section class="plans-section">
      <div class="section-header">
        <DecryptedText
          text="Student Plans"
          :speed="40"
          :sequential="true"
          animateOn="view"
          class-name="section-title-char"
          encrypted-class-name="section-title-char encrypted"
          parent-class-name="section-title"
        />
        <p class="section-subtitle">Create Your Portfolio</p>
      </div>

      <div class="cards-grid cards-grid-3">
        <!-- Basic -->
        <SpotlightCard class-name="plan-card" :spotlight-color="spotlightColor">
          <div class="card-content">
            <div class="badge-row badge-row-placeholder"></div>
            <h3 class="card-plan-name">Basic Portfolio</h3>
            <div class="card-price-row">
              <span class="price-original">100,000 IQD</span>
              <span class="discount-badge">25% OFF</span>
            </div>
            <div class="card-price">
              <CountUp :to="75000" :duration="0.8" separator="," class-name="price-amount" />
              <span class="price-currency">IQD</span>
            </div>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>An AI-designed portfolio managed by an expert developer</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>A domain with yourname.find-developer.com suffix</span>
              </li>
            </ul>
            <a :href="mailtoBasic" target="_blank" class="plan-btn plan-btn-outline">Contact Us</a>
          </div>
        </SpotlightCard>

        <!-- Standard (Popular) -->
        <SpotlightCard class-name="plan-card plan-card-featured" :spotlight-color="spotlightColor">
          <div class="card-content">
            <div class="badge-row">
              <span class="plan-badge">Popular</span>
            </div>
            <h3 class="card-plan-name">Standard Portfolio</h3>
            <div class="card-price-row">
              <span class="price-original">200,000 IQD</span>
              <span class="discount-badge">25% OFF</span>
            </div>
            <div class="card-price">
              <CountUp :to="150000" :duration="0.8" separator="," class-name="price-amount" />
              <span class="price-currency">IQD</span>
            </div>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>UI/UX design done by an expert designer</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Front end developer to programme the design</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>A domain with yourname.find-developer.com suffix</span>
              </li>
            </ul>
            <a :href="mailtoStandard" target="_blank" class="plan-btn">Contact Us</a>
          </div>
        </SpotlightCard>

        <!-- Premium (Best Value) -->
        <SpotlightCard class-name="plan-card plan-card-featured" :spotlight-color="spotlightColor">
          <div class="card-content">
            <div class="badge-row">
              <span class="plan-badge">Best Value</span>
            </div>
            <h3 class="card-plan-name">Premium Portfolio</h3>
            <div class="card-price-row">
              <span class="price-original">400,000 IQD</span>
              <span class="discount-badge">25% OFF</span>
            </div>
            <div class="card-price">
              <CountUp :to="300000" :duration="0.8" separator="," class-name="price-amount" />
              <span class="price-currency">IQD</span>
            </div>
            <ul class="features-list">
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Detailed and unique UI/UX design by expert designer</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Front end developer to programme the design</span>
              </li>
              <li>
                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>A custom domain specific for you</span>
              </li>
            </ul>
            <a :href="mailtoPremium" target="_blank" class="plan-btn">Contact Us</a>
          </div>
        </SpotlightCard>
      </div>
    </section>
  </div>
</template>

<script setup>
import SpotlightCard from './vue-bits/SpotlightCard.vue';
import DecryptedText from './vue-bits/DecryptedText.vue';
import CountUp from './vue-bits/CountUp.vue';

const spotlightColor = 'rgba(236, 159, 22, 0.15)';
const exampleUrl = 'https://hasan-tahseen.find-developer.com/';

const mailtoHosting = 'mailto:ht3aa2001@gmail.com?subject=Hosting%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Hosting%20plan';
const mailtoDomain = 'mailto:ht3aa2001@gmail.com?subject=Hosting%20with%20.iq%20Domain%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Hosting%20with%20.iq%20Domain%20plan';
const mailtoHR = 'mailto:ht3aa2001@gmail.com?subject=HR%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20HR%20plan';
const mailtoBasic = 'mailto:ht3aa2001@gmail.com?subject=Basic%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Basic%20Portfolio%20plan';
const mailtoStandard = 'mailto:ht3aa2001@gmail.com?subject=Standard%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Standard%20Portfolio%20plan';
const mailtoPremium = 'mailto:ht3aa2001@gmail.com?subject=Premium%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Premium%20Portfolio%20plan';
</script>

<style>
.plans-vue {
  max-width: 1100px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4rem;
}

/* Sections */
.plans-section {
  margin-bottom: 4rem;
}

.section-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 2.25rem;
  font-weight: 800;
  color: var(--text-primary);
  letter-spacing: -0.02em;
  display: block;
}

.section-title-char {
  transition: color 0.3s;
}

.section-title-char.encrypted {
  color: #ec9f16;
}

.section-subtitle {
  font-size: 1.05rem;
  color: var(--text-muted);
  margin-top: 0.5rem;
}

/* Card grids */
.cards-grid {
  display: grid;
  gap: 1.5rem;
  align-items: stretch;
}

.cards-grid-1 {
  grid-template-columns: 1fr;
  max-width: 480px;
  margin: 0 auto;
}

.cards-grid-2 {
  grid-template-columns: repeat(2, 1fr);
}

.cards-grid-3 {
  grid-template-columns: repeat(3, 1fr);
}

/* Force all direct children to stretch in grid */
.cards-grid > * {
  min-height: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
}

@media (max-width: 960px) {
  .cards-grid-3 { grid-template-columns: 1fr; }
}

@media (max-width: 700px) {
  .cards-grid-2 { grid-template-columns: 1fr; }
}

/* SpotlightCard overrides */
.plan-card {
  background: var(--bg-secondary) !important;
  border-color: var(--border-secondary) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.plan-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px -8px rgba(236, 159, 22, 0.12);
}

.plan-card-featured {
  border-color: #ec9f16 !important;
}

/* StarBorder overrides */
.star-card-wrapper {
  width: 100%;
  height: 100%;
}

.star-card-content {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

/* Card content */
.card-content {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.badge-row {
  min-height: 1.5rem;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
}

.badge-row-placeholder {
  visibility: hidden;
}

.plan-badge {
  display: inline-block;
  background: #ec9f16;
  color: #000;
  padding: 0.2rem 0.65rem;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.card-plan-name {
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.card-price {
  display: flex;
  align-items: baseline;
  gap: 0.3rem;
  margin-bottom: 0.25rem;
}

.card-price-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.2rem;
}

.price-amount {
  font-size: 2rem;
  font-weight: 800;
  color: #ec9f16;
  line-height: 1.2;
}

.price-currency {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-tertiary);
}

.price-period {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.price-original {
  font-size: 0.85rem;
  color: var(--text-muted);
  text-decoration: line-through;
}

.price-note {
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.discount-badge {
  background: #dc2626;
  color: white;
  padding: 0.15rem 0.45rem;
  border-radius: 9999px;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

/* Features */
.features-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
  flex: 1;
}

.features-list li {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-primary);
  font-size: 0.9rem;
  color: var(--text-secondary);
  line-height: 1.4;
}

.features-list li:last-child {
  border-bottom: none;
}

.check-icon {
  width: 1.1rem;
  height: 1.1rem;
  color: #ec9f16;
  flex-shrink: 0;
  margin-top: 0.15rem;
}

.feature-link {
  color: #ec9f16;
  text-decoration: underline;
}

.provider-note {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Buttons */
.plan-btn {
  display: block;
  width: 100%;
  text-align: center;
  padding: 0.75rem 1.25rem;
  background: #ec9f16;
  color: #000;
  border: none;
  border-radius: 0.6rem;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  margin-top: auto;
  transition: all 0.2s ease;
  cursor: pointer;
}

.plan-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -4px rgba(236, 159, 22, 0.35);
}

.plan-btn-outline {
  background: transparent;
  border: 2px solid #ec9f16;
  color: #ec9f16;
}

.plan-btn-outline:hover {
  background: #ec9f16;
  color: #000;
}
</style>

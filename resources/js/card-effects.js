/**
 * card-effects.js
 *
 * Applies vue-bits-style visual effects to developer cards:
 * - Spotlight: mouse-tracking radial gradient overlay
 *
 * Glow border on premium/pro/recommended is handled via pure CSS animations.
 * Hooks into Livewire lifecycle to re-apply after every morph/re-render.
 */

const CONFIG = {
    spotlight: {
        normal: 'rgba(255, 255, 255, 0.06)',
        pro: 'rgba(148, 163, 184, 0.15)',
        premium: 'rgba(236, 159, 22, 0.15)',
        recommended: 'rgba(236, 159, 22, 0.15)',
    },
};

const initializedCards = new WeakSet();

const isTouchDevice = () =>
    'ontouchstart' in window || navigator.maxTouchPoints > 0;

// ── Spotlight ────────────────────────────────────────────────

function getSpotlightColor(card) {
    if (card.classList.contains('developer-card-premium')) return CONFIG.spotlight.premium;
    if (card.classList.contains('developer-card-pro')) return CONFIG.spotlight.pro;
    if (card.classList.contains('developer-card-recommended')) return CONFIG.spotlight.recommended;
    return CONFIG.spotlight.normal;
}

function initSpotlight(card) {
    let overlay = card.querySelector('.card-spotlight-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'card-spotlight-overlay';
        card.insertBefore(overlay, card.firstChild);
    }

    const color = getSpotlightColor(card);

    const onMouseMove = (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        overlay.style.background =
            `radial-gradient(circle at ${x}px ${y}px, ${color}, transparent 80%)`;
    };

    const onMouseEnter = () => { overlay.style.opacity = '0.6'; };
    const onMouseLeave = () => { overlay.style.opacity = '0'; };

    card.addEventListener('mousemove', onMouseMove);
    card.addEventListener('mouseenter', onMouseEnter);
    card.addEventListener('mouseleave', onMouseLeave);

    card._spotlightCleanup = () => {
        card.removeEventListener('mousemove', onMouseMove);
        card.removeEventListener('mouseenter', onMouseEnter);
        card.removeEventListener('mouseleave', onMouseLeave);
    };
}

// ── Master Init ──────────────────────────────────────────────

function initCardEffects() {
    const cards = document.querySelectorAll('.developer-card');
    const isTouch = isTouchDevice();

    cards.forEach((card) => {
        if (initializedCards.has(card)) return;

        if (!isTouch) {
            initSpotlight(card);
        }

        initializedCards.add(card);
    });
}

// ── Lifecycle Hooks ──────────────────────────────────────────

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCardEffects);
} else {
    initCardEffects();
}

document.addEventListener('livewire:morphed', () => {
    requestAnimationFrame(initCardEffects);
});

document.addEventListener('livewire:navigated', () => {
    requestAnimationFrame(initCardEffects);
});

if (typeof Livewire !== 'undefined') {
    Livewire.hook('morph.updated', ({ el }) => {
        if (el.querySelector && el.querySelector('.developer-card')) {
            requestAnimationFrame(initCardEffects);
        }
    });
} else {
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el }) => {
            if (el.querySelector && el.querySelector('.developer-card')) {
                requestAnimationFrame(initCardEffects);
            }
        });
    });
}

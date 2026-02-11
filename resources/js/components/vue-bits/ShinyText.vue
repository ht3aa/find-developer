<template>
  <span :class="['shiny-text', className]" :style="cssVars">
    <slot>{{ text }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  text: { type: String, default: '' },
  className: { type: String, default: '' },
  color: { type: String, default: '#ec9f16' },
  speed: { type: String, default: '3s' },
  disabled: { type: Boolean, default: false },
});

const cssVars = computed(() => ({
  '--shiny-color': props.color,
  '--shiny-speed': props.speed,
  animationPlayState: props.disabled ? 'paused' : 'running',
}));
</script>

<style scoped>
.shiny-text {
  background: linear-gradient(
    120deg,
    currentColor 40%,
    var(--shiny-color) 50%,
    currentColor 60%
  );
  background-size: 200% 100%;
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shiny-sweep var(--shiny-speed) ease-in-out infinite;
}

@keyframes shiny-sweep {
  0% { background-position: 100% 0; }
  100% { background-position: -100% 0; }
}
</style>

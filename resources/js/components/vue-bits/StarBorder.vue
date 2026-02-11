<template>
  <component
    :is="as"
    :class="['star-border relative block overflow-hidden', customClass]"
    :style="componentStyle"
  >
    <div
      class="star-glow-bottom absolute rounded-full z-0"
      :style="{
        background: `radial-gradient(circle, ${color}, transparent 10%)`,
        animationDuration: speed
      }"
    ></div>
    <div
      class="star-glow-top absolute rounded-full z-0"
      :style="{
        background: `radial-gradient(circle, ${color}, transparent 10%)`,
        animationDuration: speed
      }"
    ></div>
    <div class="star-border-inner relative z-10">
      <slot />
    </div>
  </component>
</template>

<script setup>
import { computed, useAttrs } from 'vue';

const props = defineProps({
  as: { type: String, default: 'div' },
  customClass: { type: String, default: '' },
  color: { type: String, default: '#ec9f16' },
  speed: { type: String, default: '6s' },
  thickness: { type: Number, default: 1 },
});

const restAttrs = useAttrs();
const componentStyle = computed(() => {
  const base = { padding: `${props.thickness}px 0` };
  const userStyle = restAttrs.style || {};
  return { ...base, ...userStyle };
});
</script>

<style scoped>
.star-border {
  background: transparent;
  border: none;
  border-radius: 20px;
}

.star-border-inner {
  border: 1px solid #333;
  background: var(--bg-secondary, #0b0b0b);
  border-radius: 20px;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.star-glow-bottom {
  width: 300%;
  height: 50%;
  opacity: 0.7;
  bottom: -11px;
  right: -250%;
  animation: star-movement-bottom linear infinite alternate;
}

.star-glow-top {
  width: 300%;
  height: 50%;
  opacity: 0.7;
  top: -10px;
  left: -250%;
  animation: star-movement-top linear infinite alternate;
}

@keyframes star-movement-bottom {
  0% { transform: translate(0%, 0%); opacity: 1; }
  100% { transform: translate(-100%, 0%); opacity: 0; }
}

@keyframes star-movement-top {
  0% { transform: translate(0%, 0%); opacity: 1; }
  100% { transform: translate(100%, 0%); opacity: 0; }
}
</style>

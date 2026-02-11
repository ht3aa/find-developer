<template>
  <div
    ref="cardRef"
    :class="['tilt-card', className]"
    :style="cardStyle"
    @mouseenter="handleEnter"
    @mousemove="handleMove"
    @mouseleave="handleLeave"
  >
    <slot />
  </div>
</template>

<script setup>
import { ref, computed, useTemplateRef } from 'vue';

const props = defineProps({
  className: { type: String, default: '' },
  maxTilt: { type: Number, default: 10 },
  scale: { type: Number, default: 1.02 },
  speed: { type: Number, default: 400 },
  glare: { type: Boolean, default: false },
});

const cardRef = useTemplateRef('cardRef');
const tiltX = ref(0);
const tiltY = ref(0);
const isHovering = ref(false);

const cardStyle = computed(() => ({
  transform: isHovering.value
    ? `perspective(1000px) rotateX(${tiltX.value}deg) rotateY(${tiltY.value}deg) scale3d(${props.scale}, ${props.scale}, ${props.scale})`
    : 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)',
  transition: isHovering.value
    ? 'none'
    : `transform ${props.speed}ms cubic-bezier(0.03, 0.98, 0.52, 0.99)`,
  willChange: 'transform',
}));

const handleEnter = () => { isHovering.value = true; };

const handleMove = (e) => {
  if (!cardRef.value) return;
  const rect = cardRef.value.getBoundingClientRect();
  const x = (e.clientX - rect.left) / rect.width;
  const y = (e.clientY - rect.top) / rect.height;
  tiltX.value = (props.maxTilt * (0.5 - y)).toFixed(2);
  tiltY.value = (props.maxTilt * (x - 0.5)).toFixed(2);
};

const handleLeave = () => {
  isHovering.value = false;
  tiltX.value = 0;
  tiltY.value = 0;
};
</script>

<template>
  <span ref="elementRef" :class="className" />
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed, useTemplateRef } from 'vue';

const props = defineProps({
  to: { type: Number, required: true },
  from: { type: Number, default: 0 },
  direction: { type: String, default: 'up' },
  delay: { type: Number, default: 0 },
  duration: { type: Number, default: 2 },
  className: { type: String, default: '' },
  startWhen: { type: Boolean, default: true },
  separator: { type: String, default: '' },
});

const elementRef = useTemplateRef('elementRef');
const currentValue = ref(props.direction === 'down' ? props.to : props.from);
const isInView = ref(false);
const animationId = ref(null);
const hasStarted = ref(false);

let intersectionObserver = null;
const damping = computed(() => 20 + 40 * (1 / props.duration));
const stiffness = computed(() => 100 * (1 / props.duration));
let velocity = 0;
let startTime = 0;

const formatNumber = (value) => {
  const options = { useGrouping: !!props.separator, minimumFractionDigits: 0, maximumFractionDigits: 0 };
  const formatted = Intl.NumberFormat('en-US', options).format(Number(value.toFixed(0)));
  return props.separator ? formatted.replace(/,/g, props.separator) : formatted;
};

const updateDisplay = () => {
  if (elementRef.value) elementRef.value.textContent = formatNumber(currentValue.value);
};

const springAnimation = (timestamp) => {
  if (!startTime) startTime = timestamp;
  const target = props.direction === 'down' ? props.from : props.to;
  const displacement = target - currentValue.value;
  const springForce = displacement * stiffness.value;
  const dampingForce = velocity * damping.value;
  velocity += (springForce - dampingForce) * 0.016;
  currentValue.value += velocity * 0.016;
  updateDisplay();

  if (Math.abs(displacement) > 0.01 || Math.abs(velocity) > 0.01) {
    animationId.value = requestAnimationFrame(springAnimation);
  } else {
    currentValue.value = target;
    updateDisplay();
    animationId.value = null;
  }
};

const startAnimation = () => {
  if (hasStarted.value || !isInView.value || !props.startWhen) return;
  hasStarted.value = true;
  setTimeout(() => { startTime = 0; velocity = 0; animationId.value = requestAnimationFrame(springAnimation); }, props.delay * 1000);
};

onMounted(() => {
  updateDisplay();
  if (!elementRef.value) return;
  intersectionObserver = new IntersectionObserver(
    ([entry]) => { if (entry.isIntersecting && !isInView.value) { isInView.value = true; startAnimation(); } },
    { threshold: 0, rootMargin: '0px' }
  );
  intersectionObserver.observe(elementRef.value);
});

onUnmounted(() => {
  if (animationId.value) cancelAnimationFrame(animationId.value);
  if (intersectionObserver) intersectionObserver.disconnect();
});

watch([() => props.from, () => props.to, () => props.direction], () => {
  currentValue.value = props.direction === 'down' ? props.to : props.from;
  updateDisplay();
  hasStarted.value = false;
}, { immediate: true });

watch(() => props.startWhen, () => {
  if (props.startWhen && isInView.value && !hasStarted.value) startAnimation();
});
</script>

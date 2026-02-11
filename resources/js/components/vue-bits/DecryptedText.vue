<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick, useTemplateRef } from 'vue';

const props = defineProps({
  text: { type: String, default: '' },
  speed: { type: Number, default: 50 },
  maxIterations: { type: Number, default: 10 },
  sequential: { type: Boolean, default: false },
  revealDirection: { type: String, default: 'start' },
  useOriginalCharsOnly: { type: Boolean, default: false },
  characters: { type: String, default: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()_+' },
  className: { type: String, default: '' },
  encryptedClassName: { type: String, default: '' },
  parentClassName: { type: String, default: '' },
  animateOn: { type: String, default: 'view' },
});

const containerRef = useTemplateRef('containerRef');
const displayText = ref(props.text);
const isHovering = ref(false);
const isScrambling = ref(false);
const revealedIndices = ref(new Set());
const hasAnimated = ref(false);

let interval = null;
let intersectionObserver = null;

watch(
  [
    () => isHovering.value,
    () => props.text,
    () => props.speed,
    () => props.maxIterations,
    () => props.sequential,
    () => props.revealDirection,
    () => props.characters,
    () => props.useOriginalCharsOnly
  ],
  () => {
    let currentIteration = 0;

    const getNextIndex = (revealedSet) => {
      const textLength = props.text.length;
      switch (props.revealDirection) {
        case 'start': return revealedSet.size;
        case 'end': return textLength - 1 - revealedSet.size;
        case 'center': {
          const middle = Math.floor(textLength / 2);
          const offset = Math.floor(revealedSet.size / 2);
          const nextIndex = revealedSet.size % 2 === 0 ? middle + offset : middle - offset - 1;
          if (nextIndex >= 0 && nextIndex < textLength && !revealedSet.has(nextIndex)) return nextIndex;
          for (let i = 0; i < textLength; i++) { if (!revealedSet.has(i)) return i; }
          return 0;
        }
        default: return revealedSet.size;
      }
    };

    const availableChars = props.useOriginalCharsOnly
      ? Array.from(new Set(props.text.split(''))).filter(char => char !== ' ')
      : props.characters.split('');

    const shuffleText = (originalText, currentRevealed) => {
      return originalText
        .split('')
        .map((char, i) => {
          if (char === ' ') return ' ';
          if (currentRevealed.has(i)) return originalText[i];
          return availableChars[Math.floor(Math.random() * availableChars.length)];
        })
        .join('');
    };

    if (interval) { clearInterval(interval); interval = null; }

    if (isHovering.value) {
      isScrambling.value = true;
      interval = setInterval(() => {
        if (props.sequential) {
          if (revealedIndices.value.size < props.text.length) {
            const nextIndex = getNextIndex(revealedIndices.value);
            const newRevealed = new Set(revealedIndices.value);
            newRevealed.add(nextIndex);
            revealedIndices.value = newRevealed;
            displayText.value = shuffleText(props.text, newRevealed);
          } else {
            clearInterval(interval); interval = null; isScrambling.value = false;
          }
        } else {
          displayText.value = shuffleText(props.text, revealedIndices.value);
          currentIteration++;
          if (currentIteration >= props.maxIterations) {
            clearInterval(interval); interval = null; isScrambling.value = false;
            displayText.value = props.text;
          }
        }
      }, props.speed);
    } else {
      displayText.value = props.text;
      revealedIndices.value = new Set();
      isScrambling.value = false;
    }
  }
);

const handleMouseEnter = () => { if (props.animateOn === 'hover') isHovering.value = true; };
const handleMouseLeave = () => { if (props.animateOn === 'hover') isHovering.value = false; };

onMounted(async () => {
  if (props.animateOn === 'view') {
    await nextTick();
    intersectionObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !hasAnimated.value) {
            isHovering.value = true;
            hasAnimated.value = true;
          }
        });
      },
      { root: null, rootMargin: '0px', threshold: 0.1 }
    );
    if (containerRef.value) intersectionObserver.observe(containerRef.value);
  }
});

onUnmounted(() => {
  if (interval) clearInterval(interval);
  if (intersectionObserver && containerRef.value) intersectionObserver.unobserve(containerRef.value);
});
</script>

<template>
  <span
    ref="containerRef"
    :class="`inline-block whitespace-pre-wrap ${props.parentClassName}`"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <span class="sr-only">{{ displayText }}</span>
    <span aria-hidden="true">
      <span
        v-for="(char, index) in displayText.split('')"
        :key="index"
        :class="revealedIndices.has(index) || !isScrambling || !isHovering ? props.className : props.encryptedClassName"
      >{{ char }}</span>
    </span>
  </span>
</template>

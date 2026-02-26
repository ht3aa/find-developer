<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

const props = withDefaults(
    defineProps<{
        size?: string;
        class?: string;
    }>(),
    {
        size: 'size-12',
        class: '',
    },
);

const quackAudio = new Audio('/sound/quack.mp3');

function playQuack(): void {
    quackAudio.currentTime = 0;
    quackAudio.playbackRate = 1.5;
    quackAudio.play().catch(() => {
        // Ignore autoplay policy / user gesture requirements
    });
}
</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger as-child>
                <button
                    type="button"
                    :class="[props.size, props.class]"
                    class="inline-flex shrink-0 cursor-pointer select-none items-center justify-center rounded-full transition-transform hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                    aria-label="Play quack sound"
                    @click="playQuack"
                >
                    <img
                        src="/imgs/Duck.png"
                        alt=""
                        class="pointer-events-none h-full w-full object-contain"
                        draggable="false"
                    />
                </button>
            </TooltipTrigger>
            <TooltipContent>Play quack sound</TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>

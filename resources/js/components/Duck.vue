<script setup lang="ts">
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useQuackAudio } from '@/composables/useQuackAudio';

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

const { playQuack } = useQuackAudio();
</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger as-child>
                <button
                    type="button"
                    :class="[props.size, props.class]"
                    class="inline-flex shrink-0 cursor-pointer items-center justify-center rounded-full transition-transform select-none hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
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

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

type DataPoint = { label: string; count: number };

const props = defineProps<{
    data: DataPoint[];
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

const BACKGROUND_COLORS = [
    'rgba(16, 185, 129, 0.7)',
    'rgba(245, 158, 11, 0.7)',
    'rgba(59, 130, 246, 0.7)',
    'rgba(124, 58, 237, 0.7)',
    'rgba(37, 99, 235, 0.7)',
    'rgba(239, 68, 68, 0.7)',
    'rgba(236, 72, 153, 0.7)',
];

function initChart(): void {
    if (!canvasRef.value || !props.data.length) return;

    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    chartInstance = new Chart(canvasRef.value, {
        type: 'doughnut',
        data: {
            labels: props.data.map((d) => d.label),
            datasets: [
                {
                    data: props.data.map((d) => d.count),
                    backgroundColor: BACKGROUND_COLORS.slice(0, props.data.length),
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
            },
        },
    });
}

onMounted(() => initChart());
watch(() => props.data, () => initChart(), { deep: true });
</script>

<template>
    <Card>
        <CardHeader>
            <h3 class="text-lg font-semibold">
                Developers by availability type
            </h3>
        </CardHeader>
        <CardContent>
            <div v-if="!data.length" class="flex h-64 items-center justify-center text-sm text-muted-foreground">
                No data yet
            </div>
            <div v-else class="mx-auto h-64 max-w-xs">
                <canvas ref="canvasRef" />
            </div>
        </CardContent>
    </Card>
</template>

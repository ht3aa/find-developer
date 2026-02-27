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

function initChart(): void {
    if (!canvasRef.value || !props.data.length) return;

    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    chartInstance = new Chart(canvasRef.value, {
        type: 'bar',
        data: {
            labels: props.data.map((d) => d.label),
            datasets: [
                {
                    label: 'Developers',
                    data: props.data.map((d) => d.count),
                    backgroundColor: 'rgba(124, 58, 237, 0.6)',
                    borderColor: 'rgb(124, 58, 237)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                },
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
                Developers by job title
            </h3>
        </CardHeader>
        <CardContent>
            <div v-if="!data.length" class="flex h-64 items-center justify-center text-sm text-muted-foreground">
                No data yet
            </div>
            <div v-else class="h-80">
                <canvas ref="canvasRef" />
            </div>
        </CardContent>
    </Card>
</template>

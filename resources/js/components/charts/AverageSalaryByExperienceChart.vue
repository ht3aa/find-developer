<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

type DataPoint = { years_of_experience: number; average_salary: number };

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

    const labels = props.data.map(
        (d) => `${d.years_of_experience} ${d.years_of_experience === 1 ? 'year' : 'years'}`,
    );

    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Average salary',
                    data: props.data.map((d) => d.average_salary),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
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
                Average salary by years of experience (IQD)
            </h3>
        </CardHeader>
        <CardContent>
            <div v-if="!data.length" class="flex h-64 items-center justify-center text-sm text-muted-foreground">
                No data yet
            </div>
            <div v-else class="h-64">
                <canvas ref="canvasRef" />
            </div>
        </CardContent>
    </Card>
</template>

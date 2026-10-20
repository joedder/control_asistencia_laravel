<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import {
  ArcElement,
  Chart,
  DoughnutController,
  Legend,
  Tooltip,
} from 'chart.js'

const props = defineProps({
  stats: { type: Object, required: true },
})

Chart.register(DoughnutController, ArcElement, Tooltip, Legend)

const canvas = ref(null)
let chart

const chartData = computed(() => ({
  labels: ['Asistente', 'Inasistente', 'Justificado'],
  datasets: [{
    data: [props.stats.presentes, props.stats.inasistentes, props.stats.justificados],
    backgroundColor: ['#16a34a', '#dc2626', '#f59e0b'],
    hoverBackgroundColor: ['#15803d', '#b91c1c', '#d97706'],
    borderColor: '#ffffff',
    borderWidth: 3,
  }],
}))

const renderChart = () => {
  if (!canvas.value) return
  if (chart) chart.destroy()

  chart = new Chart(canvas.value, {
    type: 'doughnut',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } },
        tooltip: {
          callbacks: {
            label: (context) => ` ${context.label}: ${context.raw}`,
          },
        },
      },
    },
  })
}

onMounted(renderChart)
watch(chartData, renderChart, { deep: true })
onBeforeUnmount(() => chart?.destroy())
</script>

<template>
  <div class="relative mx-auto h-64 w-full max-w-md">
    <canvas ref="canvas" aria-label="Distribución de asistencias del estudiante" role="img" />
    <div class="pointer-events-none absolute inset-x-0 top-0 flex h-[190px] flex-col items-center justify-center">
      <span class="text-3xl font-bold text-slate-800">{{ stats.pct_presentes }}%</span>
      <span class="text-xs uppercase tracking-wide text-slate-500">Asistencia</span>
    </div>
  </div>
</template>

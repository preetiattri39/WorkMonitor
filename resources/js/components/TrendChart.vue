<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend);

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
});

const data = computed(() => ({
  labels: props.chartData.labels,
  datasets: [
    {
      ...props.chartData.datasets?.[0],
      borderColor: '#1f7ef4',
      backgroundColor: 'rgba(31, 126, 244, 0.12)',
      tension: 0.4,
      fill: true,
    },
    {
      ...props.chartData.datasets?.[1],
      borderColor: '#059669',
      backgroundColor: 'rgba(5, 150, 105, 0.12)',
      tension: 0.4,
    },
  ],
}));

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
};
</script>

<template>
  <div class="h-80">
    <Line :data="data" :options="options" />
  </div>
</template>

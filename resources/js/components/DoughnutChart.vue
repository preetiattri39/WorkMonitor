<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
});

const data = computed(() => ({
  labels: props.chartData.labels,
  datasets: [{
    ...props.chartData.datasets?.[0],
    backgroundColor: ['#64748b', '#1f7ef4', '#f59e0b', '#ef4444', '#10b981'],
    borderWidth: 0,
  }],
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
    <Doughnut :data="data" :options="options" />
  </div>
</template>

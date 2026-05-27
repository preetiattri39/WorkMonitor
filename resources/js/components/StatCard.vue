<script setup>
defineProps({
  title: String,
  value: [String, Number],
  subtitle: String,
  tone: {
    type: String,
    default: 'sky',
  },
});

const toneMap = {
  sky: 'from-sky-500/15 to-brand-500/5 text-sky-700',
  amber: 'from-amber-400/20 to-orange-500/5 text-amber-700',
  emerald: 'from-emerald-500/15 to-emerald-300/5 text-emerald-700',
  violet: 'from-violet-500/15 to-fuchsia-300/5 text-violet-700',
};
</script>

<template>
  <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-panel">
    <div class="rounded-2xl bg-gradient-to-br p-4" :class="toneMap[tone]">
      <p class="text-sm font-medium text-slate-500">{{ title }}</p>
      <p class="mt-3 text-3xl font-semibold text-slate-950">{{ value }}</p>
      <p class="mt-2 text-sm text-slate-500">{{ subtitle }}</p>
    </div>
  </div>
</template>

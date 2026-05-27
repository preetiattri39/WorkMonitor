<script setup>
import { computed, onMounted, ref } from 'vue';
import { echo } from '../echo';
import StatCard from '../components/StatCard.vue';
import TrendChart from '../components/TrendChart.vue';
import DoughnutChart from '../components/DoughnutChart.vue';

const defaultOverview = {
  summary: {
    employees: 0,
    active_projects: 0,
    open_tasks: 0,
    completed_tasks_today: 0,
    hours_logged_today: 0,
    avg_productivity_score: 0,
    attendance_rate: 0,
    pending_leave_requests: 0,
  },
  charts: {
    productivityTrend: { labels: [], datasets: [] },
    taskStatusBreakdown: { labels: [], datasets: [] },
  },
  recentTasks: [],
  projectPortfolio: [],
  attendanceSnapshots: [],
  leaveQueue: [],
  performanceBoard: [],
  monitoringFeed: [],
  screenshotReview: [],
};

const overviewElement = document.getElementById('dashboard-overview');
const overview = ref(overviewElement ? JSON.parse(overviewElement.textContent) : defaultOverview);
const notifications = ref([]);
const userRole = document.head.querySelector('meta[name="user-role"]')?.content ?? 'employee';
const userName = document.head.querySelector('meta[name="user-name"]')?.content ?? 'User';
const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';

const summary = computed(() => overview.value.summary ?? defaultOverview.summary);
const charts = computed(() => overview.value.charts ?? defaultOverview.charts);
const recentTasks = computed(() => overview.value.recentTasks ?? []);
const projectPortfolio = computed(() => overview.value.projectPortfolio ?? []);
const attendanceSnapshots = computed(() => overview.value.attendanceSnapshots ?? []);
const leaveQueue = computed(() => overview.value.leaveQueue ?? []);
const performanceBoard = computed(() => overview.value.performanceBoard ?? []);
const monitoringFeed = computed(() => overview.value.monitoringFeed ?? []);
const screenshotReview = computed(() => overview.value.screenshotReview ?? []);
const dashboardLabel = computed(() => {
  if (userRole === 'admin') return 'Admin command center';
  if (userRole === 'manager') return 'Manager control room';
  return 'Employee productivity cockpit';
});

const quickLinks = computed(() => {
  const items = [
    { label: 'Attendance', href: '/attendances', tone: 'from-sky-500 to-cyan-400' },
    { label: 'Work Logs', href: '/work-logs', tone: 'from-emerald-500 to-teal-400' },
    { label: 'Tasks', href: '/tasks', tone: 'from-violet-500 to-fuchsia-400' },
    { label: 'Projects', href: '/projects', tone: 'from-amber-400 to-orange-500' },
    { label: 'Leave', href: '/leave-requests', tone: 'from-slate-800 to-slate-600' },
  ];

  if (userRole !== 'employee') {
    items.push({ label: 'Audit Logs', href: '/audit-logs', tone: 'from-rose-500 to-pink-500' });
  }

  return items;
});

async function loadDashboard() {
  const response = await window.axios.get('/dashboard/overview');
  overview.value = response.data;
}

function statusClasses(status) {
  return {
    todo: 'bg-slate-100 text-slate-700',
    in_progress: 'bg-amber-100 text-amber-700',
    review: 'bg-sky-100 text-sky-700',
    blocked: 'bg-rose-100 text-rose-700',
    done: 'bg-emerald-100 text-emerald-700',
    pending: 'bg-amber-100 text-amber-700',
    approved: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-rose-100 text-rose-700',
    cancelled: 'bg-slate-100 text-slate-700',
    active: 'bg-emerald-100 text-emerald-700',
    planning: 'bg-sky-100 text-sky-700',
    on_hold: 'bg-amber-100 text-amber-700',
    completed: 'bg-slate-100 text-slate-700',
    present: 'bg-emerald-100 text-emerald-700',
    late: 'bg-amber-100 text-amber-700',
    absent: 'bg-rose-100 text-rose-700',
    remote: 'bg-sky-100 text-sky-700',
  }[status] ?? 'bg-slate-100 text-slate-700';
}

function humanize(value) {
  return value.replaceAll('_', ' ');
}

onMounted(async () => {
  await loadDashboard().catch(() => {});

  const userId = document.head.querySelector('meta[name="user-id"]')?.content;

  if (userId) {
    echo.private(`users.${userId}`)
      .listen('.task.updated', (event) => {
        notifications.value.unshift({ type: 'task', message: `${event.task.title} was updated.` });
        loadDashboard();
      })
      .listen('.productivity.snapshot.created', () => {
        notifications.value.unshift({ type: 'activity', message: 'A new productivity snapshot arrived.' });
        loadDashboard();
      });
  }

  if (userRole !== 'employee') {
    echo.private('managers')
      .listen('.task.updated', () => loadDashboard())
      .listen('.productivity.snapshot.created', () => loadDashboard());
  }
});
</script>

<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(52,157,255,0.10),_transparent_35%),linear-gradient(180deg,#f8fbff_0%,#eef4ff_100%)]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <header class="mb-8 rounded-[2rem] bg-slate-950 px-6 py-8 text-white shadow-panel">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
          <div class="space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-200">{{ dashboardLabel }}</p>
            <h1 class="max-w-4xl text-3xl font-semibold md:text-5xl">Welcome back, {{ userName }}. Monitor delivery, productivity, and attendance from one control center.</h1>
            <p class="max-w-3xl text-slate-300">Track attendance, work logs, active window patterns, screenshot reviews, leave approvals, task throughput, and team health with live updates from Laravel Reverb.</p>
          </div>
          <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
              <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Attendance</div>
              <div class="mt-2 text-2xl font-semibold">{{ summary.attendance_rate }}%</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
              <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Productivity</div>
              <div class="mt-2 text-2xl font-semibold">{{ summary.avg_productivity_score }}</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
              <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Projects</div>
              <div class="mt-2 text-2xl font-semibold">{{ summary.active_projects }}</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
              <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Leave Queue</div>
              <div class="mt-2 text-2xl font-semibold">{{ summary.pending_leave_requests }}</div>
            </div>
          </div>
        </div>

        <div class="mt-8 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
          <a
            v-for="link in quickLinks"
            :key="link.href"
            :href="link.href"
            class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10"
          >
            <div :class="`inline-flex rounded-full bg-gradient-to-r ${link.tone} px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white`">{{ link.label }}</div>
            <p class="mt-4 text-sm text-slate-300 group-hover:text-white">Open module</p>
          </a>
        </div>
      </header>

      <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Active Employees" :value="summary.employees" tone="sky" subtitle="Currently enabled users" />
        <StatCard title="Open Tasks" :value="summary.open_tasks" tone="amber" subtitle="Todo, review, and blocked items" />
        <StatCard title="Completed Today" :value="summary.completed_tasks_today" tone="emerald" subtitle="Tasks closed in the current day" />
        <StatCard title="Hours Logged" :value="summary.hours_logged_today" tone="violet" subtitle="Daily work log duration" />
      </section>

      <section class="mt-6 grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <div class="mb-4 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-slate-900">Productivity Trend</h2>
              <p class="text-sm text-slate-500">Seven-day productivity and focused time trend.</p>
            </div>
          </div>
          <TrendChart :chart-data="charts.productivityTrend" />
        </div>
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <h2 class="text-xl font-semibold text-slate-900">Task Status Mix</h2>
          <p class="mb-4 text-sm text-slate-500">Pipeline distribution across current work.</p>
          <DoughnutChart :chart-data="charts.taskStatusBreakdown" />
        </div>
      </section>

      <section class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-slate-900">Recent Tasks</h2>
              <p class="text-sm text-slate-500">Assignments driving the current operating tempo.</p>
            </div>
            <a href="/tasks" class="text-sm font-semibold text-sky-700 transition hover:text-sky-900">View all</a>
          </div>
          <div class="mt-4 space-y-3">
            <a
              v-for="task in recentTasks"
              :key="task.id"
              :href="task.url"
              class="block rounded-2xl border border-slate-100 px-4 py-4 transition hover:border-sky-200 hover:bg-sky-50/60"
            >
              <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                  <p class="font-semibold text-slate-950">{{ task.title }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ task.project }} <span v-if="task.assignee">· {{ task.assignee }}</span></p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                  <span :class="statusClasses(task.status)" class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">{{ humanize(task.status) }}</span>
                  <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ task.completion_percentage }}%</span>
                </div>
              </div>
            </a>
            <div v-if="recentTasks.length === 0" class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">
              No tasks are available for this role yet.
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-slate-900">Realtime Notifications</h2>
              <p class="text-sm text-slate-500">Broadcasted task and monitoring events via Reverb.</p>
            </div>
          </div>
          <div class="mt-4 space-y-3">
            <div v-if="notifications.length === 0" class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">
              No recent events yet.
            </div>
            <div v-for="item in notifications.slice(0, 6)" :key="item.message + item.type" class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-sm font-medium text-slate-900">{{ item.message }}</p>
              <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ item.type }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-slate-900">Project Portfolio</h2>
              <p class="text-sm text-slate-500">The initiatives and client work currently under management.</p>
            </div>
            <a href="/projects" class="text-sm font-semibold text-sky-700 transition hover:text-sky-900">Open projects</a>
          </div>
          <div class="mt-4 space-y-3">
            <a
              v-for="project in projectPortfolio"
              :key="project.id"
              :href="project.url"
              class="block rounded-2xl border border-slate-100 px-4 py-4 transition hover:border-sky-200 hover:bg-sky-50/60"
            >
              <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold text-slate-950">{{ project.name }}</p>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ project.code }}</span>
                  </div>
                  <p class="mt-1 text-sm text-slate-500">{{ project.client_name || 'Internal' }} <span v-if="project.owner">· {{ project.owner }}</span></p>
                </div>
                <div class="text-sm text-slate-500 lg:text-right">
                  <p>{{ project.completed_tasks_count }} / {{ project.tasks_count }} tasks closed</p>
                  <p class="mt-1">{{ project.budget ? `$${project.budget}` : 'Budget pending' }}</p>
                </div>
              </div>
            </a>
            <div v-if="projectPortfolio.length === 0" class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">
              No projects are visible for this role yet.
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <h2 class="text-xl font-semibold text-slate-900">Team Performance</h2>
          <p class="text-sm text-slate-500">Top productivity snapshots from the last operating window.</p>
          <div class="mt-4 space-y-3">
            <div v-for="item in performanceBoard" :key="`${item.employee}-${item.day}`" class="rounded-2xl bg-slate-50 px-4 py-4">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-950">{{ item.employee }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ item.day }}</p>
                </div>
                <div class="text-right">
                  <p class="text-2xl font-semibold text-slate-950">{{ item.score }}</p>
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Score</p>
                </div>
              </div>
              <div class="mt-3 grid gap-3 text-sm text-slate-500 sm:grid-cols-2">
                <p>Focused: {{ item.focused_minutes }} mins</p>
                <p>Productive: {{ item.productive_minutes }} mins</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-6 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <h2 class="text-xl font-semibold text-slate-900">Attendance Snapshot</h2>
          <p class="text-sm text-slate-500">Today&apos;s attendance posture across the visible workforce.</p>
          <div class="mt-4 space-y-3">
            <div v-for="entry in attendanceSnapshots" :key="`${entry.employee}-${entry.clock_in_at}-${entry.status}`" class="rounded-2xl border border-slate-100 px-4 py-4">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-950">{{ entry.employee }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ entry.clock_in_at || 'No clock in' }} <span v-if="entry.clock_out_at">- {{ entry.clock_out_at }}</span></p>
                </div>
                <span :class="statusClasses(entry.status)" class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">{{ humanize(entry.status) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <h2 class="text-xl font-semibold text-slate-900">Monitoring Feed</h2>
          <p class="text-sm text-slate-500">Recent keyboard, mouse, idle-time, and active-window snapshots.</p>
          <div class="mt-4 space-y-3">
            <div v-for="activity in monitoringFeed" :key="`${activity.employee}-${activity.logged_at}`" class="rounded-2xl bg-slate-50 px-4 py-4">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-950">{{ activity.employee }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ activity.window || 'No active window label' }}</p>
                </div>
                <div class="text-right">
                  <p class="text-2xl font-semibold text-slate-950">{{ activity.score }}</p>
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Score</p>
                </div>
              </div>
              <div class="mt-3 grid gap-3 text-sm text-slate-500 sm:grid-cols-3">
                <p>{{ activity.keystrokes }} keys</p>
                <p>{{ activity.mouse_clicks }} clicks</p>
                <p>{{ activity.idle_seconds }}s idle</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <h2 class="text-xl font-semibold text-slate-900">Leave Queue</h2>
          <p class="text-sm text-slate-500">Approval status and staffing impact across current requests.</p>
          <div class="mt-4 space-y-3">
            <div v-for="request in leaveQueue" :key="`${request.employee}-${request.dates}`" class="rounded-2xl border border-slate-100 px-4 py-4">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-950">{{ request.employee }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ request.type }} · {{ request.dates }}</p>
                </div>
                <span :class="statusClasses(request.status)" class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">{{ request.status }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-panel">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-slate-900">Screenshot Review</h2>
              <p class="text-sm text-slate-500">Recent captures uploaded through the secure monitoring API.</p>
            </div>
            <form method="POST" action="/logout">
              <input type="hidden" name="_token" :value="csrfToken">
              <button type="submit" class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Sign out</button>
            </form>
          </div>
          <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div v-for="capture in screenshotReview" :key="`${capture.employee}-${capture.captured_at}`" class="overflow-hidden rounded-2xl border border-slate-100">
              <div class="aspect-[4/3] bg-slate-100">
                <img v-if="capture.image_url" :src="capture.image_url" :alt="`Screenshot from ${capture.employee}`" class="h-full w-full object-cover">
              </div>
              <div class="p-4">
                <div class="flex items-center justify-between gap-3">
                  <p class="font-semibold text-slate-950">{{ capture.employee }}</p>
                  <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ capture.label }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">{{ capture.captured_at }}</p>
                <p v-if="capture.blur_sensitive" class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">Sensitive content blurred</p>
              </div>
            </div>
            <div v-if="screenshotReview.length === 0" class="rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">
              No screenshots have been uploaded yet.
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

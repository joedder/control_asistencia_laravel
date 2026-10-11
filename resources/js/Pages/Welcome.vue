<script setup>
import { Head, Link } from '@inertiajs/vue3'
import BaseIcon from '@/Components/BaseIcon.vue'
import {
  mdiLogin,
  mdiViewDashboard,
  mdiClockOutline,
  mdiAccountGroup,
  mdiChartTimelineVariant,
  mdiShieldCheck,
  mdiArrowRight,
  mdiCheckCircleOutline
} from '@mdi/js'

defineProps({
  canLogin: {
    type: Boolean,
    default: true
  },
  canRegister: {
    type: Boolean,
    default: false
  },
  laravelVersion: String,
  phpVersion: String
})

const currentYear = new Date().getFullYear()

const features = [
  {
    title: 'Control de Asistencia Diario',
    desc: 'Registro en tiempo real, control de retardos, inasistencias y justificaciones con trazabilidad completa.',
    icon: mdiClockOutline,
    color: 'from-blue-500/20 to-cyan-500/20 text-cyan-400 border-cyan-500/30'
  },
  {
    title: 'Gestión de Alumnos y Docentes',
    desc: 'Organización centralizada por niveles académicos, grupos, categorías y asignación de profesores.',
    icon: mdiAccountGroup,
    color: 'from-purple-500/20 to-indigo-500/20 text-indigo-400 border-indigo-500/30'
  },
  {
    title: 'Reportes y Métricas',
    desc: 'Visualización clara del desempeño, historial de movimientos y estadísticas de asistencia exportables.',
    icon: mdiChartTimelineVariant,
    color: 'from-emerald-500/20 to-teal-500/20 text-emerald-400 border-emerald-500/30'
  },
  {
    title: 'Seguridad y Roles',
    desc: 'Permisos granulares y acceso restringido administrado estrictamente por los directivos y administradores.',
    icon: mdiShieldCheck,
    color: 'from-amber-500/20 to-orange-500/20 text-amber-400 border-amber-500/30'
  }
]
</script>

<template>
  <Head title="Bienvenido - Control de Asistencia" />

  <div class="relative min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between overflow-hidden selection:bg-blue-600 selection:text-white">
    <!-- Ambient Background Effects -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute top-1/4 -right-32 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
    </div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/25 border border-white/10">
          <BaseIcon :path="mdiClockOutline" size="24" class="text-white" />
        </div>
        <div>
          <span class="text-lg font-bold tracking-tight text-white block leading-tight">
            Control de Asistencia
          </span>
          <span class="text-xs text-blue-400/90 font-medium tracking-wider uppercase">
            Portal Institucional
          </span>
        </div>
      </div>

      <div v-if="canLogin" class="flex items-center space-x-3">
        <Link
          v-if="$page.props.auth?.user"
          :href="route('admin.dashboard')"
          class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 transition-all duration-200 shadow-md shadow-blue-600/30 active:scale-95"
        >
          <BaseIcon :path="mdiViewDashboard" size="18" />
          <span>Ir al Panel</span>
        </Link>
        <Link
          v-else
          :href="route('login')"
          class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 transition-all duration-200 shadow-lg shadow-blue-600/30 hover:shadow-blue-500/40 active:scale-95"
        >
          <BaseIcon :path="mdiLogin" size="18" />
          <span>Iniciar Sesión</span>
        </Link>
      </div>
    </header>

    <!-- Hero Section -->
    <main class="relative z-10 w-full max-w-7xl mx-auto px-6 py-12 lg:py-16 flex-1 flex flex-col justify-center">
      <div class="text-center max-w-3xl mx-auto">
        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-medium mb-6 backdrop-blur-sm">
          <BaseIcon :path="mdiCheckCircleOutline" size="16" class="text-blue-400" />
          <span>Gestión Académica y Asistencia Centralizada</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-6 leading-tight">
          Sistema Integral de <br class="hidden sm:inline" />
          <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-300 bg-clip-text text-transparent">
            Control de Asistencia
          </span>
        </h1>

        <p class="text-base sm:text-lg text-slate-300 leading-relaxed mb-10 max-w-2xl mx-auto font-normal">
          Optimiza la administración escolar y el seguimiento en tiempo real de alumnos, docentes y personal institucional en una plataforma moderna, ágil y segura.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
          <Link
            v-if="$page.props.auth?.user"
            :href="route('admin.dashboard')"
            class="w-full sm:w-auto inline-flex items-center justify-center space-x-2.5 px-8 py-3.5 rounded-xl text-base font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-xl shadow-blue-600/30 hover:shadow-blue-500/50 transition-all duration-200 active:scale-95 group"
          >
            <span>Ir al Panel de Control</span>
            <BaseIcon :path="mdiArrowRight" size="20" class="transition-transform group-hover:translate-x-1" />
          </Link>
          <Link
            v-else-if="canLogin"
            :href="route('login')"
            class="w-full sm:w-auto inline-flex items-center justify-center space-x-2.5 px-8 py-3.5 rounded-xl text-base font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-xl shadow-blue-600/30 hover:shadow-blue-500/50 transition-all duration-200 active:scale-95 group"
          >
            <BaseIcon :path="mdiLogin" size="20" />
            <span>Acceder al Sistema</span>
            <BaseIcon :path="mdiArrowRight" size="20" class="transition-transform group-hover:translate-x-1" />
          </Link>
        </div>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto w-full">
        <div
          v-for="(feature, idx) in features"
          :key="idx"
          class="relative group rounded-2xl p-6 bg-slate-900/60 hover:bg-slate-900/90 border border-slate-800 hover:border-slate-700 backdrop-blur-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-950/50"
        >
          <div
            :class="feature.color"
            class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center mb-4 border"
          >
            <BaseIcon :path="feature.icon" size="24" />
          </div>
          <h3 class="text-base font-bold text-white mb-2 tracking-tight">
            {{ feature.title }}
          </h3>
          <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
            {{ feature.desc }}
          </p>
        </div>
      </div>

      <!-- Restricted Access Notice -->
      <div class="mt-12 max-w-2xl mx-auto w-full">
        <div class="rounded-xl px-5 py-3.5 bg-slate-900/50 border border-slate-800/80 text-center flex items-center justify-center space-x-2 text-xs sm:text-sm text-slate-400">
          <BaseIcon :path="mdiShieldCheck" size="18" class="text-blue-400 flex-shrink-0" />
          <span>Acceso restringido: El registro de nuevos usuarios es administrado únicamente por el personal directivo.</span>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 border-t border-slate-800/60 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
      <div>
        © {{ currentYear }} Control de Asistencia. Todos los derechos reservados.
      </div>
      <div class="flex items-center space-x-4">
        <span>Gestión Institucional Segura</span>
      </div>
    </footer>
  </div>
</template>
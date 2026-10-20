<script setup>
import { Head } from "@inertiajs/vue3"
import {
  mdiAccountSchool,
  mdiArrowLeftBoldOutline,
  mdiFilePdfBox,
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from "@/Components/BaseButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButtons from "@/Components/BaseButtons.vue"
import StudentAttendanceChart from "@/Components/Charts/StudentAttendanceChart.vue"

defineProps({
  student: { type: Object, required: true },
  attendances: { type: Array, default: () => [] },
  stats: { type: Object, required: true },
})

const statusLabel = (status) => ({
  asistente: 'Asistente',
  inasistente: 'Inasistente',
  justificado: 'Justificado',
}[status] || status)

const statusClass = (status) => ({
  asistente: 'bg-green-100 text-green-700',
  inasistente: 'bg-red-100 text-red-700',
  justificado: 'bg-amber-100 text-amber-700',
}[status] || 'bg-slate-100 text-slate-700')

const formatDate = (date) => {
  if (!date) return '—'
  const [year, month, day] = String(date).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}
</script>

<template>
  <LayoutAuthenticated>
    <Head :title="`Asistencias de ${student.name} ${student.last_name}`" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountSchool"
        title="Reporte de asistencia del estudiante"
        main
      >
        <BaseButtons>
          <BaseButton
            :href="route('admin.student.attendance-report.pdf', student.id)"
            :icon="mdiFilePdfBox"
            label="Exportar PDF"
            color="danger"
            rounded-full
            small
          />
          <BaseButton
            :route-name="route('admin.student.index')"
            :icon="mdiArrowLeftBoldOutline"
            label="Volver a estudiantes"
            color="white"
            rounded-full
            small
          />
        </BaseButtons>
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div><span class="font-semibold">Estudiante:</span> {{ student.last_name }} {{ student.name }}</div>
          <div><span class="font-semibold">Identificación:</span> {{ student.identity_id || 'S/N' }}</div>
          <div><span class="font-semibold">Grupo actual:</span> {{ student.group?.name || '—' }}</div>
        </div>
      </CardBox>

      <div class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
        <CardBox class="text-center"><div class="text-2xl font-bold text-blue-700">{{ stats.total }}</div><div class="text-sm uppercase">Sesiones registradas</div></CardBox>
        <CardBox class="text-center"><div class="text-2xl font-bold text-green-700">{{ stats.presentes }} ({{ stats.pct_presentes }}%)</div><div class="text-sm uppercase">Asistencias</div></CardBox>
        <CardBox class="text-center"><div class="text-2xl font-bold text-red-700">{{ stats.inasistentes }}</div><div class="text-sm uppercase">Inasistencias</div></CardBox>
        <CardBox class="text-center"><div class="text-2xl font-bold text-amber-700">{{ stats.justificados }}</div><div class="text-sm uppercase">Justificadas</div></CardBox>
      </div>

      <CardBox class="mb-6">
        <h2 class="mb-2 text-lg font-semibold">Distribución de asistencias</h2>
        <p class="mb-2 text-sm text-slate-500">Porcentaje calculado sobre las sesiones registradas para este estudiante.</p>
        <StudentAttendanceChart :stats="stats" />
      </CardBox>

      <CardBox has-table>
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Fecha</th>
              <th>Grupo</th>
              <th>Docente</th>
              <th>Estado</th>
              <th>Observación / motivo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(attendance, index) in attendances" :key="attendance.id">
              <td data-label="N°">{{ index + 1 }}</td>
              <td data-label="Fecha">{{ formatDate(attendance.class_date) }}</td>
              <td data-label="Grupo">{{ attendance.group?.name || '—' }}</td>
              <td data-label="Docente">
                {{ attendance.substitute_teacher ? `${attendance.substitute_teacher.name} ${attendance.substitute_teacher.last_name} (suplente)` : `${attendance.teacher?.name || ''} ${attendance.teacher?.last_name || ''}`.trim() || '—' }}
              </td>
              <td data-label="Estado">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="statusClass(attendance.status)">
                  {{ statusLabel(attendance.status) }}
                </span>
              </td>
              <td data-label="Observación / motivo">{{ attendance.social_reason || '—' }}</td>
            </tr>
            <tr v-if="attendances.length === 0">
              <td colspan="6" class="py-6 text-center">Este estudiante todavía no tiene asistencias registradas.</td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

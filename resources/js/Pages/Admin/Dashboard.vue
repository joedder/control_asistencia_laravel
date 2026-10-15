<script setup>
import { Head } from '@inertiajs/vue3'
import { mdiSchool } from '@mdi/js'
import LayoutAuthenticated from '@/Layouts/Admin/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'

defineProps({
  auth: {
    type: Object,
    default: () => ({})
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Inicio" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiSchool"
        title="Bienvenido al Sistema de Control de Asistencia"
        main
      >
        <template #default />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div class="py-8 text-center sm:py-12">
          <h2 class="mb-4 text-2xl font-semibold text-slate-800 dark:text-white">
            ¡Te damos la bienvenida!
          </h2>
          <p class="mx-auto max-w-2xl text-lg text-slate-600 dark:text-slate-300">
            Desde aquí puedes gestionar grupos, estudiantes, docentes y asistencias.
            Usa el menú para acceder a los módulos disponibles según tu rol.
          </p>
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

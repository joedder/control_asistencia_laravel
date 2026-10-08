<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiCheckbook,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  attending: {
    type: Object,
    default: () => ({}),
  },
  group_attendances: {
    type: Array,
    default: () => ([]),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Attendances (Batch)" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="View Attendances (Batch)"
        main
      >
        <BaseButton
          :route-name="route('admin.attending.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6 p-4 bg-gray-50 dark:bg-slate-800 rounded">
          <div>
            <span class="font-bold">Group:</span> {{ attending.group?.name }}
          </div>
          <div>
            <span class="font-bold">Teacher:</span> {{ attending.teacher?.name }}
          </div>
          <div>
            <span class="font-bold">Date:</span> {{ new Date(attending.class_date).toLocaleDateString() }}
          </div>
        </div>

        <h3 class="text-lg font-bold mb-4 px-4">Students List</h3>
        <div class="overflow-x-auto px-4">
          <table class="w-full text-left table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100 dark:bg-slate-700">
                <th class="p-4 border-b">ID</th>
                <th class="p-4 border-b">Student</th>
                <th class="p-4 border-b">Status</th>
                <th class="p-4 border-b">Reason</th>
                <th class="p-4 border-b">Registered By</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="att in group_attendances" :key="att.id" class="border-b">
                <td class="p-4">{{ att.student?.identity_id }}</td>
                <td class="p-4">{{ att.student?.name }} {{ att.student?.last_name || '' }}</td>
                <td class="p-4">
                  <span :class="{
                    'text-green-600 font-bold': att.status === 'asistente',
                    'text-red-600 font-bold': att.status === 'inasistente',
                    'text-yellow-600 font-bold': att.status === 'justificado'
                  }">
                    {{ att.status }}
                  </span>
                </td>
                <td class="p-4">{{ att.social_reason || 'N/A' }}</td>
                <td class="p-4">{{ att.user?.name || 'System' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

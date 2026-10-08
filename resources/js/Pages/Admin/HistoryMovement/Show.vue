<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiTransitTransfer,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  movement: {
    type: Object,
    default: () => ({}),
  },
  batchMovements: {
    type: Array,
    default: () => ([]),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Migration Batch" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="View Migration Batch"
        main
      >
        <BaseButton
          :route-name="route('admin.history-movement.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox class="mb-6">
        <table>
          <tbody>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Batch ID</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ movement.batch_id }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Old Group</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ movement.group?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">New Group</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ movement.new_group?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Migrated Status</td>
              <td class="p-4 border-b dark:border-slate-800">
                <span :class="{
                  'text-green-600 font-bold': movement.migrated,
                  'text-yellow-600 font-bold': !movement.migrated
                }">
                  {{ movement.migrated ? 'Approved' : 'Pending' }}
                </span>
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Registered By</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ movement.user?.name || 'System' }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Date</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ new Date(movement.created_at).toLocaleString() }}
              </td>
            </tr>
          </tbody>
        </table>

        <div class="mt-8 px-4">
          <h3 class="text-lg font-bold mb-4">Students in this Batch</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-left table-auto border-collapse">
              <thead>
                <tr class="bg-gray-100 dark:bg-slate-700">
                  <th class="p-4 border-b">ID</th>
                  <th class="p-4 border-b">Student Name</th>
                  <th class="p-4 border-b">Identity ID</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in batchMovements" :key="m.id" class="border-b hover:bg-gray-50 dark:hover:bg-slate-800">
                  <td class="p-4">{{ m.student.id }}</td>
                  <td class="p-4">{{ m.student.name }} {{ m.student.last_name || '' }}</td>
                  <td class="p-4">{{ m.student.identity_id }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

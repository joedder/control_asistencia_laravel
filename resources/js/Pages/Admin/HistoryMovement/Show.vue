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
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Movement" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="View Movement"
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
              <td class="p-4 font-semibold border-b dark:border-slate-800">Student</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ movement.student?.name }} {{ movement.student?.last_name || '' }} ({{ movement.student?.identity_id }})
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
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

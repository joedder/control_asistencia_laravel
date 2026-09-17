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
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Attendance" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="View Attendance"
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
        <table>
          <tbody>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Student</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.student?.name }} {{ attending.student?.last_name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Group</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.group?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Teacher</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.teacher?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Date</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ new Date(attending.class_date).toLocaleDateString() }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Status</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.status }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Reason</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.social_reason || 'N/A' }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Registered By</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ attending.user?.name || 'System' }}
              </td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiAccountSchool,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  student: {
    type: Object,
    default: () => ({}),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Student" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountSchool"
        title="View Student"
        main
      >
        <BaseButton
          :route-name="route('admin.student.index')"
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
              <td class="p-4 font-semibold border-b dark:border-slate-800">Name</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ student.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Last Name</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ student.last_name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Identity ID</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ student.identity_id }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Group</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ student.group?.name }}
              </td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

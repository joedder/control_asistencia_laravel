<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiAccountGroup,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  group: {
    type: Object,
    default: () => ({}),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Group" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountGroup"
        title="View Group"
        main
      >
        <BaseButton
          :route-name="route('admin.group.index')"
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
                {{ group.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Teacher</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ group.teacher?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Category Group</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ group.category_group?.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Level</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ group.level?.name }}
              </td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

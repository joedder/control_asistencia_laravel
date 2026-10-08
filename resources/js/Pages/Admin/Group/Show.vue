<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiAccountGroup,
  mdiArrowLeftBoldOutline,
  mdiEye,
  mdiAccountSchool
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

      <SectionTitleLineWithButton
        :icon="mdiAccountSchool"
        title="Assigned Students"
        main
      >
      </SectionTitleLineWithButton>

      <CardBox class="mb-6" has-table>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Last Name</th>
              <th>Identity ID</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in group.students" :key="student.id">
              <td data-label="ID">
                {{ student.id }}
              </td>
              <td data-label="Name">
                {{ student.name }}
              </td>
              <td data-label="Last Name">
                {{ student.last_name }}
              </td>
              <td data-label="Identity ID">
                {{ student.identity_id }}
              </td>
              <td class="before:hidden lg:w-1 whitespace-nowrap">
                <BaseButton
                  :route-name="route('admin.student.show', student.id)"
                  color="info"
                  :icon="mdiEye"
                  small
                />
              </td>
            </tr>
            <tr v-if="!group.students || group.students.length === 0">
              <td colspan="5" class="text-center p-4">
                No students assigned to this group.
              </td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

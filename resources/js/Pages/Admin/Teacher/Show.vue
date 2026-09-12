<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiAccountTie,
  mdiArrowLeftBoldOutline,
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from "@/Components/BaseButton.vue"

const props = defineProps({
  teacher: {
    type: Object,
    default: () => ({}),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View teacher" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountTie"
        title="View teacher"
        main
      >
        <BaseButton
          :route-name="route('admin.teacher.index')"
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
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">ID</td>
              <td data-label="ID">{{ teacher.id }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Name</td>
              <td data-label="Name">{{ teacher.name }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Last Name</td>
              <td data-label="Last Name">{{ teacher.last_name }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Identity ID</td>
              <td data-label="Identity ID">{{ teacher.identity_id || 'N/A' }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">English Level</td>
              <td data-label="English Level">{{ teacher.english_level }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Linked User Account</td>
              <td data-label="Linked User Account">
                <span v-if="teacher.user">
                  {{ teacher.user.name }} ({{ teacher.user.email }})
                </span>
                <span v-else class="text-gray-400 italic">Not Linked</span>
              </td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Created At</td>
              <td data-label="Created At">{{ new Date(teacher.created_at).toLocaleString() }}</td>
            </tr>
            <tr>
              <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">Updated At</td>
              <td data-label="Updated At">{{ new Date(teacher.updated_at).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

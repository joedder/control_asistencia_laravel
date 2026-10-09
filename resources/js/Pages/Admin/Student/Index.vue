<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountSchool,
  mdiPlus,
  mdiSquareEditOutline,
  mdiTrashCan,
  mdiAlertBoxOutline,
  mdiEye,
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from "@/Components/BaseButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButtons from "@/Components/BaseButtons.vue"
import NotificationBar from "@/Components/NotificationBar.vue"
import Pagination from "@/Components/Admin/Pagination.vue"

const props = defineProps({
  students: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  groups: {
    type: Object,
    default: () => ({}),
  },
  can: {
    type: Object,
    default: () => ({}),
  },
  error: {
    type: String,
    default: null,
  }
})

const form = useForm({
  search: props.filters.search || '',
  id_group: props.filters.id_group || '',
  sort_dir: new URLSearchParams(window.location.search).get('sort_dir') || 'desc',
})

const submitSearch = () => {
  form.get(route('admin.student.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}

const formDelete = useForm({})

function destroy(id) {
  if (confirm("Are you sure you want to delete?")) {
    formDelete.delete(route("admin.student.destroy", id))
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Students" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountSchool"
        title="Students"
        main
      >
        <BaseButton
          v-if="can.create"
          :route-name="route('admin.student.create')"
          :icon="mdiPlus"
          label="Add"
          color="info"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      
      <NotificationBar
        :key="Date.now()"
        v-if="$page.props.flash?.message"
        color="success"
        :icon="mdiAlertBoxOutline"
      >
        {{ $page.props.flash.message }}
      </NotificationBar>

      <NotificationBar
        :key="Date.now() + 1"
        v-if="error"
        color="danger"
        :icon="mdiAlertBoxOutline"
      >
        {{ error }}
      </NotificationBar>

      <CardBox class="mb-6">
        <form @submit.prevent="submitSearch">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 items-end">
            <div>
              <label class="block text-sm font-medium mb-1">Search</label>
              <input
                type="search"
                v-model="form.search"
                class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700"
                placeholder="Search..."
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-1">Group</label>
              <select v-model="form.id_group" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All Groups</option>
                <option v-for="(name, id) in groups" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Order Dir</label>
              <select v-model="form.sort_dir" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="desc">Descendente</option>
                <option value="asc">Ascendente</option>
              </select>
            </div>

            <div>
              <BaseButton
                label="Filter"
                type="submit"
                color="info"
                class="w-full"
              />
            </div>
          </div>
        </form>
      </CardBox>
      <CardBox class="mb-6" has-table>
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Name</th>
              <th>Last Name</th>
              <th>Identification</th>
              <th>Group</th>
              <th v-if="can.edit || can.delete">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(student, index) in students?.data" :key="student.id">
              <td data-label="N°">
                {{ (students.current_page - 1) * students.per_page + index + 1 }}
              </td>
              <td data-label="Name">
                {{ student.name }}
              </td>
              <td data-label="Last Name">
                {{ student.last_name }}
              </td>
              <td data-label="Identification">
                {{ student.identity_id }}
              </td>
              <td data-label="Group">
                {{ student.group?.name }}
              </td>
              <td
                v-if="can.edit || can.delete"
                class="before:hidden lg:w-1 whitespace-nowrap"
              >
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton
                    :route-name="route('admin.student.show', student.id)"
                    color="success"
                    :icon="mdiEye"
                    small
                  />
                  <BaseButton
                    v-if="can.edit"
                    :route-name="route('admin.student.edit', student.id)"
                    color="info"
                    :icon="mdiSquareEditOutline"
                    small
                  />
                  <BaseButton
                    v-if="can.delete"
                    color="danger"
                    :icon="mdiTrashCan"
                    small
                    @click="destroy(student.id)"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="!students?.data || students.data.length === 0">
              <td colspan="6" class="text-center py-4">
                No students found.
              </td>
            </tr>
          </tbody>
        </table>
        <div class="py-4" v-if="students?.data && students.data.length > 0">
          <Pagination :data="students" />
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

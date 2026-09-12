<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountTie,
  mdiPlus,
  mdiSquareEditOutline,
  mdiTrashCan,
  mdiAlertBoxOutline,
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
  teachers: {
    type: Object,
    default: () => ({}),
  },
  filters: {
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
  search: props.filters.search,
})

const formDelete = useForm({})

function destroy(id) {
  if (confirm("Are you sure you want to delete?")) {
    formDelete.delete(route("admin.teacher.destroy", id))
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Teachers" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountTie"
        title="Teachers"
        main
      >
        <BaseButton
          v-if="can.create"
          :route-name="route('admin.teacher.create')"
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

      <CardBox class="mb-6" has-table>
        <form @submit.prevent="form.get(route('admin.teacher.index'))">
          <div class="py-2 flex">
            <div class="flex pl-4">
              <input
                type="search"
                v-model="form.search"
                class="
                  rounded-md
                  shadow-sm
                  border-gray-300
                  focus:border-indigo-300
                  focus:ring
                  focus:ring-indigo-200
                  focus:ring-opacity-50
                  dark:bg-slate-800
                  dark:border-slate-700
                "
                placeholder="Search..."
              />
              <BaseButton
                label="Search"
                type="submit"
                color="info"
                class="ml-4 inline-flex items-center px-4 py-2"
              />
            </div>
          </div>
        </form>
      </CardBox>
      <CardBox class="mb-6" has-table>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Last Name</th>
              <th>Identity ID</th>
              <th>English Level</th>
              <th v-if="can.edit || can.delete">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="teacher in teachers?.data" :key="teacher.id">
              <td data-label="ID">
                {{ teacher.id }}
              </td>
              <td data-label="Name">
                {{ teacher.name }}
              </td>
              <td data-label="Last Name">
                {{ teacher.last_name }}
              </td>
              <td data-label="Identity ID">
                {{ teacher.identity_id || '-' }}
              </td>
              <td data-label="English Level">
                {{ teacher.english_level }}
              </td>
              <td
                v-if="can.edit || can.delete"
                class="before:hidden lg:w-1 whitespace-nowrap"
              >
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton
                    v-if="can.edit"
                    :route-name="route('admin.teacher.edit', teacher.id)"
                    color="info"
                    :icon="mdiSquareEditOutline"
                    small
                  />
                  <BaseButton
                    v-if="can.delete"
                    color="danger"
                    :icon="mdiTrashCan"
                    small
                    @click="destroy(teacher.id)"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="!teachers?.data || teachers.data.length === 0">
              <td colspan="6" class="text-center py-4">
                No teachers found.
              </td>
            </tr>
          </tbody>
        </table>
        <div class="py-4" v-if="teachers?.data && teachers.data.length > 0">
          <Pagination :data="teachers" />
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

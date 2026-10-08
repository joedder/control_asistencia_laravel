<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3"
import { watch } from "vue"
import {
  mdiCheckbook,
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
  attendings: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  teachers: {
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
  id_teacher: props.filters.id_teacher || '',
  id_group: props.filters.id_group || '',
  class_date: props.filters.class_date || '',
  sort_dir: new URLSearchParams(window.location.search).get('sort_dir') || 'desc',
})

const submitSearch = () => {
  form.get(route('admin.attending.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}

const formDelete = useForm({})

function destroy(id) {
  if (confirm("Are you sure you want to delete all attendances for this group on this date?")) {
    formDelete.delete(route("admin.attending.destroy", id))
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Attendances" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="Attendances"
        main
      >
        <BaseButton
          v-if="can.create"
          :route-name="route('admin.attending.create')"
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
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-5 items-end">
            <div>
              <label class="block text-sm font-medium mb-1">Teacher</label>
              <select v-model="form.id_teacher" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All Teachers</option>
                <option v-for="(name, id) in teachers" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-1">Group</label>
              <select v-model="form.id_group" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All Groups</option>
                <option v-for="(name, id) in groups" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Date</label>
              <input type="date" v-model="form.class_date" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Order Date</label>
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
              <th>Group</th>
              <th>Teacher</th>
              <th>Date</th>
              <th v-if="can.edit || can.delete">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(attending, index) in attendings?.data" :key="attending.id">
              <td data-label="N°">
                {{ (attendings.current_page - 1) * attendings.per_page + index + 1 }}
              </td>
              <td data-label="Group">
                {{ attending.group?.name }}
              </td>
              <td data-label="Teacher">
                {{ attending.teacher?.name }}
              </td>
              <td data-label="Date">
                {{ new Date(attending.class_date).toLocaleDateString() }}
              </td>
              <td
                v-if="can.edit || can.delete"
                class="before:hidden lg:w-1 whitespace-nowrap"
              >
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton
                    :route-name="route('admin.attending.show', attending.id)"
                    color="success"
                    :icon="mdiEye"
                    small
                  />
                  <BaseButton
                    v-if="can.edit"
                    :route-name="route('admin.attending.edit', attending.id)"
                    color="info"
                    :icon="mdiSquareEditOutline"
                    small
                  />
                  <BaseButton
                    v-if="can.delete"
                    color="danger"
                    :icon="mdiTrashCan"
                    small
                    @click="destroy(attending.id)"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="!attendings?.data || attendings.data.length === 0">
              <td colspan="5" class="text-center py-4">
                No attendances found.
              </td>
            </tr>
          </tbody>
        </table>
        <div class="py-4" v-if="attendings?.data && attendings.data.length > 0">
          <Pagination :data="attendings" />
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiTransitTransfer,
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
  movements: {
    type: Object,
    default: () => ({}),
  },
  groups: {
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
  search: props.filters.search || '',
  id_group: props.filters.id_group || '',
  id_new_group: props.filters.id_new_group || '',
  migrated: props.filters.migrated || '',
})

const submitSearch = () => {
  form.get(route('admin.history-movement.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}

const formDelete = useForm({})

function destroy(id) {
  if (confirm("Are you sure you want to delete this movement?")) {
    formDelete.delete(route("admin.history-movement.destroy", id))
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="History Movements" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="History Movements"
        main
      >
        <BaseButton
          v-if="can.create"
          :route-name="route('admin.history-movement.create')"
          :icon="mdiPlus"
          label="Migrate"
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
              <label class="block text-sm font-medium mb-1">Search Student</label>
              <input
                type="search"
                v-model="form.search"
                class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700"
                placeholder="Name or Identity..."
              />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Old Group</label>
              <select v-model="form.id_group" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All</option>
                <option v-for="(name, id) in groups" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">New Group</label>
              <select v-model="form.id_new_group" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All</option>
                <option v-for="(name, id) in groups" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Status</label>
              <select v-model="form.migrated" class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700">
                <option value="">All</option>
                <option value="1">Migrated (Approved)</option>
                <option value="0">Pending</option>
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
              <th>Total Students</th>
              <th>From Group</th>
              <th>To Group</th>
              <th>Migrated</th>
              <th>Date</th>
              <th v-if="can.edit || can.delete">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(movement, index) in movements?.data" :key="movement.id">
              <td data-label="N°">
                {{ (movements.current_page - 1) * movements.per_page + index + 1 }}
              </td>
              <td data-label="Total Students">
                {{ movement.student_count }}
              </td>
              <td data-label="From Group">
                {{ movement.group?.name }}
              </td>
              <td data-label="To Group">
                {{ movement.new_group?.name }}
              </td>
              <td data-label="Migrated">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="{
                        'bg-green-100 text-green-800': movement.migrated,
                        'bg-yellow-100 text-yellow-800': !movement.migrated
                      }">
                  {{ movement.migrated ? 'Approved' : 'Pending' }}
                </span>
              </td>
              <td data-label="Date">
                {{ new Date(movement.created_at).toLocaleDateString() }}
              </td>
              <td
                v-if="can.edit || can.delete"
                class="before:hidden lg:w-1 whitespace-nowrap"
              >
                <BaseButtons type="justify-start lg:justify-end" no-wrap>
                  <BaseButton
                    :route-name="route('admin.history-movement.show', movement.id)"
                    color="success"
                    :icon="mdiEye"
                    small
                  />
                  <BaseButton
                    v-if="can.edit"
                    :route-name="route('admin.history-movement.edit', movement.id)"
                    color="info"
                    :icon="mdiSquareEditOutline"
                    small
                  />
                  <BaseButton
                    v-if="can.delete"
                    color="danger"
                    :icon="mdiTrashCan"
                    small
                    @click="destroy(movement.id)"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="!movements?.data || movements.data.length === 0">
              <td colspan="7" class="text-center py-4">
                No movements found.
              </td>
            </tr>
          </tbody>
        </table>
        <div class="py-4" v-if="movements?.data && movements.data.length > 0">
          <Pagination :data="movements" />
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

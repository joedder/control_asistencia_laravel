<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiTransitTransfer,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'

const props = defineProps({
  movement: {
    type: Object,
    default: () => ({}),
  },
  batchMovements: {
    type: Array,
    default: () => ([]),
  },
  groups: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  id_group: props.movement.id_group,
  id_new_group: props.movement.id_new_group,
  students: props.batchMovements.map(m => m.id_student),
  migrated: props.movement.migrated,
})

const toggleStudent = (studentId) => {
  const index = form.students.indexOf(studentId);
  if (index === -1) {
    form.students.push(studentId);
  } else {
    form.students.splice(index, 1);
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Edit Migration Batch" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="Edit Migration Batch"
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
      <CardBox
        form
        @submit.prevent="form.put(route('admin.history-movement.update', movement.id))"
      >
        <div class="mb-4">
            <h3 class="font-bold text-lg mb-2">Batch Information</h3>
            <p><strong>Batch ID:</strong> {{ movement.batch_id }}</p>
            <p><strong>Created:</strong> {{ new Date(movement.created_at).toLocaleString() }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Old Group"
            :class="{ 'text-red-400': form.errors.id_group }"
            >
            <FormControl
                v-model="form.id_group"
                type="select"
                :options="props.groups"
                placeholder="Select Old Group"
                :error="form.errors.id_group"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_group">
                {{ form.errors.id_group }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="New Group"
            :class="{ 'text-red-400': form.errors.id_new_group }"
            >
            <FormControl
                v-model="form.id_new_group"
                type="select"
                :options="props.groups"
                placeholder="Select New Group"
                :error="form.errors.id_new_group"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_new_group">
                {{ form.errors.id_new_group }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <div class="mt-4 mb-6">
            <label class="flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.migrated" class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <span class="font-bold text-lg">Approve Migration (Migrated)</span>
            </label>
            <div class="text-sm text-gray-500 mt-1">
              If checked, the selected students will be moved to the new group. If unchecked, they will be reverted.
            </div>
            <div class="text-red-400 text-sm" v-if="form.errors.migrated">
                {{ form.errors.migrated }}
            </div>
        </div>

        <div class="mt-6 mb-6">
          <h3 class="text-lg font-bold mb-4">Students in this Batch</h3>
          <p class="text-sm text-gray-500 mb-2">Uncheck a student to remove them from this migration batch.</p>
          <div class="text-red-400 text-sm mb-2" v-if="form.errors.students">
            {{ form.errors.students }}
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left table-auto border-collapse">
              <thead>
                <tr class="bg-gray-100 dark:bg-slate-700">
                  <th class="p-4 border-b w-12">Keep</th>
                  <th class="p-4 border-b">ID</th>
                  <th class="p-4 border-b">Student Name</th>
                  <th class="p-4 border-b">Identity ID</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in batchMovements" :key="m.id" class="border-b hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer" @click="toggleStudent(m.student.id)">
                  <td class="p-4 text-center">
                    <input type="checkbox" :checked="form.students.includes(m.student.id)" @change.stop="toggleStudent(m.student.id)" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                  </td>
                  <td class="p-4">{{ m.student.id }}</td>
                  <td class="p-4">{{ m.student.name }} {{ m.student.last_name || '' }}</td>
                  <td class="p-4">{{ m.student.identity_id }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Update Movement"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing || form.students.length === 0"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

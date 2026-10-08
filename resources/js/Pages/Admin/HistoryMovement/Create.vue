<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import { ref, watch } from "vue"
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
  groups: {
    type: Array,
    default: () => ([]),
  }
})

const groupOptions = props.groups.reduce((acc, group) => {
  acc[group.id] = group.name;
  return acc;
}, {});

const form = useForm({
  id_group: '',
  id_new_group: '',
  migrated: false,
  students: [],
})

const selectedGroup = ref(null);
const allStudentsInGroup = ref([]);

watch(() => form.id_group, (newGroupId) => {
  if (newGroupId) {
    const group = props.groups.find(g => g.id == newGroupId);
    if (group) {
      selectedGroup.value = group;
      allStudentsInGroup.value = group.students || [];
      form.students = []; // reset selected students
    }
  } else {
    selectedGroup.value = null;
    allStudentsInGroup.value = [];
    form.students = [];
  }
});

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
    <Head title="Migrate Students" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="Migrate Students"
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
        @submit.prevent="form.post(route('admin.history-movement.store'))"
      >
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Current Group"
            :class="{ 'text-red-400': form.errors.id_group }"
            >
            <FormControl
                v-model="form.id_group"
                type="select"
                :options="groupOptions"
                placeholder="Select Current Group"
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
                :options="groupOptions"
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
              If checked, the students will be immediately moved to the new group. Otherwise, this record will remain pending.
            </div>
            <div class="text-red-400 text-sm" v-if="form.errors.migrated">
                {{ form.errors.migrated }}
            </div>
        </div>

        <div v-if="selectedGroup && allStudentsInGroup.length > 0" class="mt-6">
          <h3 class="text-lg font-bold mb-4">Select Students to Migrate</h3>
          <div class="text-red-400 text-sm mb-2" v-if="form.errors.students">
            {{ form.errors.students }}
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left table-auto border-collapse">
              <thead>
                <tr class="bg-gray-100 dark:bg-slate-700">
                  <th class="p-4 border-b w-12">Select</th>
                  <th class="p-4 border-b">ID</th>
                  <th class="p-4 border-b">Student Name</th>
                  <th class="p-4 border-b">Identity ID</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="student in allStudentsInGroup" :key="student.id" class="border-b hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer" @click="toggleStudent(student.id)">
                  <td class="p-4 text-center">
                    <input type="checkbox" :checked="form.students.includes(student.id)" @change.stop="toggleStudent(student.id)" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                  </td>
                  <td class="p-4">{{ student.id }}</td>
                  <td class="p-4">{{ student.name }} {{ student.last_name || '' }}</td>
                  <td class="p-4">{{ student.identity_id }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else-if="selectedGroup && allStudentsInGroup.length === 0" class="mt-6 p-4 text-center text-gray-500 border rounded">
          No students found in the selected group.
        </div>

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Create Migration"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing || form.students.length === 0 || !form.id_new_group"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

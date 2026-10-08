<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import { ref, watch } from "vue"
import {
  mdiCheckbook,
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
  teachers: {
    type: Object,
    default: () => ({}),
  },
  groups: {
    type: Array,
    default: () => ([]),
  },
  statuses: {
    type: Object,
    default: () => ({}),
  }
})

// Transform groups for select component
const groupOptions = props.groups.reduce((acc, group) => {
  acc[group.id] = group.name;
  return acc;
}, {});

const form = useForm({
  id_teacher: '',
  id_group: '',
  class_date: new Date().toISOString().split('T')[0], // Today's date automatically
  attendances: [],
})

const selectedGroup = ref(null);

watch(() => form.id_group, (newGroupId) => {
  if (newGroupId) {
    const group = props.groups.find(g => g.id == newGroupId);
    if (group) {
      selectedGroup.value = group;
      form.id_teacher = group.id_teacher || '';
      
      // Initialize attendances array for all students in the group
      form.attendances = group.students.map(student => ({
        id_student: student.id,
        name: student.name + ' ' + (student.last_name || ''),
        identity_id: student.identity_id,
        status: 'asistente', // Default to asistente
        social_reason: ''
      }));
    }
  } else {
    selectedGroup.value = null;
    form.attendances = [];
  }
});
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add Attendance" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="Add Attendances (Batch)"
        main
      >
        <BaseButton
          :route-name="route('admin.attending.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox
        form
        @submit.prevent="form.post(route('admin.attending.store'))"
      >
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <FormField
            label="Group"
            :class="{ 'text-red-400': form.errors.id_group }"
            >
            <FormControl
                v-model="form.id_group"
                type="select"
                :options="groupOptions"
                placeholder="Select a Group"
                :error="form.errors.id_group"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_group">
                {{ form.errors.id_group }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Teacher"
            :class="{ 'text-red-400': form.errors.id_teacher }"
            >
            <FormControl
                v-model="form.id_teacher"
                type="select"
                :options="props.teachers"
                placeholder="Select a Teacher"
                :error="form.errors.id_teacher"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_teacher">
                {{ form.errors.id_teacher }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Date"
            :class="{ 'text-red-400': form.errors.class_date }"
            >
            <FormControl
                v-model="form.class_date"
                type="date"
                :error="form.errors.class_date"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.class_date">
                {{ form.errors.class_date }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <div v-if="selectedGroup && form.attendances.length > 0" class="mt-6">
          <h3 class="text-lg font-bold mb-4">Students List</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-left table-auto">
              <thead>
                <tr>
                  <th class="p-4 border-b">N°</th>
                  <th class="p-4 border-b">Student</th>
                  <th class="p-4 border-b">Status</th>
                  <th class="p-4 border-b">Reason (if Justificado)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(attendance, index) in form.attendances" :key="attendance.id_student" class="border-b">
                  <td class="p-4">{{ attendance.identity_id }}</td>
                  <td class="p-4">{{ attendance.name }}</td>
                  <td class="p-4">
                    <div class="flex items-center space-x-4">
                      <label class="flex items-center cursor-pointer">
                        <input type="radio" v-model="attendance.status" value="asistente" class="mr-2 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"> 
                        <span>Asistente</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input type="radio" v-model="attendance.status" value="inasistente" class="mr-2 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"> 
                        <span>Inasistente</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input type="radio" v-model="attendance.status" value="justificado" class="mr-2 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"> 
                        <span>Justificado</span>
                      </label>
                    </div>
                    <div class="text-red-400 text-sm mt-1" v-if="form.errors[`attendances.${index}.status`]">
                      {{ form.errors[`attendances.${index}.status`] }}
                    </div>
                  </td>
                  <td class="p-4">
                    <FormControl
                      v-if="attendance.status === 'justificado'"
                      v-model="attendance.social_reason"
                      type="text"
                      placeholder="Enter Reason"
                    />
                    <div class="text-red-400 text-sm mt-1" v-if="form.errors[`attendances.${index}.social_reason`]">
                      {{ form.errors[`attendances.${index}.social_reason`] }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else-if="selectedGroup && form.attendances.length === 0" class="mt-6 p-4 text-center text-gray-500 border rounded">
          No students found in this group.
        </div>

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Submit Attendances"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing || form.attendances.length === 0"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

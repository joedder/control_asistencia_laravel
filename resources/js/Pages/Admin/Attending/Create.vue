<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
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
  students: {
    type: Object,
    default: () => ({}),
  },
  groups: {
    type: Object,
    default: () => ({}),
  },
  statuses: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  id_teacher: '',
  id_student: '',
  id_group: '',
  status: 'asistente',
  social_reason: '',
  class_date: ''
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add Attendance" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="Add Attendance"
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
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Student"
            :class="{ 'text-red-400': form.errors.id_student }"
            >
            <FormControl
                v-model="form.id_student"
                type="select"
                :options="props.students"
                placeholder="Select a Student"
                :error="form.errors.id_student"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_student">
                {{ form.errors.id_student }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Group"
            :class="{ 'text-red-400': form.errors.id_group }"
            >
            <FormControl
                v-model="form.id_group"
                type="select"
                :options="props.groups"
                placeholder="Select a Group"
                :error="form.errors.id_group"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_group">
                {{ form.errors.id_group }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Status"
            :class="{ 'text-red-400': form.errors.status }"
            >
            <FormControl
                v-model="form.status"
                type="select"
                :options="props.statuses"
                placeholder="Select a Status"
                :error="form.errors.status"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.status">
                {{ form.errors.status }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Reason (if justified/absent)"
            :class="{ 'text-red-400': form.errors.social_reason }"
            >
            <FormControl
                v-model="form.social_reason"
                type="textarea"
                placeholder="Enter Reason"
                :error="form.errors.social_reason"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.social_reason">
                {{ form.errors.social_reason }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

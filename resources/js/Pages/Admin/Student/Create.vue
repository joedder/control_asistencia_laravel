<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountSchool,
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
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  name: '',
  last_name: '',
  identity_id: '',
  id_group: ''
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add Student" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountSchool"
        title="Add Student"
        main
      >
        <BaseButton
          :route-name="route('admin.student.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox
        form
        @submit.prevent="form.post(route('admin.student.store'))"
      >
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Name"
            :class="{ 'text-red-400': form.errors.name }"
            >
            <FormControl
                v-model="form.name"
                type="text"
                placeholder="Enter Name"
                :error="form.errors.name"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.name">
                {{ form.errors.name }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Last Name"
            :class="{ 'text-red-400': form.errors.last_name }"
            >
            <FormControl
                v-model="form.last_name"
                type="text"
                placeholder="Enter Last Name"
                :error="form.errors.last_name"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.last_name">
                {{ form.errors.last_name }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Identification"
            :class="{ 'text-red-400': form.errors.identity_id }"
            >
            <FormControl
                v-model="form.identity_id"
                type="text"
                placeholder="Enter Identification"
                :error="form.errors.identity_id"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.identity_id">
                {{ form.errors.identity_id }}
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

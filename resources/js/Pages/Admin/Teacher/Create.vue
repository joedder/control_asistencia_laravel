<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountTie,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'

const props = defineProps({
  users: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  name: '',
  last_name: '',
  identity_id: '',
  english_level: '',
  id_user: ''
})

const englishLevelOptions = {
  'A1': 'A1 - Beginner',
  'A2': 'A2 - Elementary',
  'B1': 'B1 - Intermediate',
  'B2': 'B2 - Upper Intermediate',
  'C1': 'C1 - Advanced',
  'C2': 'C2 - Proficient'
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add teacher" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountTie"
        title="Add teacher"
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
      <CardBox
        form
        @submit.prevent="form.post(route('admin.teacher.store'))"
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
            label="Identity ID"
            :class="{ 'text-red-400': form.errors.identity_id }"
            >
            <FormControl
                v-model="form.identity_id"
                type="text"
                placeholder="Enter Identity ID (e.g., 123456789)"
                :error="form.errors.identity_id"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.identity_id">
                {{ form.errors.identity_id }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="English Level"
            :class="{ 'text-red-400': form.errors.english_level }"
            >
            <FormControl
                v-model="form.english_level"
                type="select"
                :options="englishLevelOptions"
                placeholder="Select an English Level"
                :error="form.errors.english_level"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.english_level">
                {{ form.errors.english_level }}
                </div>
            </FormControl>
            </FormField>
        </div>

        <BaseDivider />

        <FormField
          label="Link User Account (Optional)"
          :class="{ 'text-red-400': form.errors.id_user }"
          help="Select a user account to link to this teacher. Only unlinked users are shown."
        >
          <FormControl
            v-model="form.id_user"
            type="select"
            :options="props.users"
            placeholder="Select a User"
            :error="form.errors.id_user"
          >
            <div class="text-red-400 text-sm" v-if="form.errors.id_user">
              {{ form.errors.id_user }}
            </div>
          </FormControl>
        </FormField>

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

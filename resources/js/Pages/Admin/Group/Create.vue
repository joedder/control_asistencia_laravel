<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountGroup,
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
  category_groups: {
    type: Object,
    default: () => ({}),
  },
  levels: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  name: '',
  id_teacher: '',
  id_category_group: '',
  id_level: ''
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add Group" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountGroup"
        title="Add Group"
        main
      >
        <BaseButton
          :route-name="route('admin.group.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox
        form
        @submit.prevent="form.post(route('admin.group.store'))"
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
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormField
            label="Category Group"
            :class="{ 'text-red-400': form.errors.id_category_group }"
            >
            <FormControl
                v-model="form.id_category_group"
                type="select"
                :options="props.category_groups"
                placeholder="Select a Category Group"
                :error="form.errors.id_category_group"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_category_group">
                {{ form.errors.id_category_group }}
                </div>
            </FormControl>
            </FormField>

            <FormField
            label="Level"
            :class="{ 'text-red-400': form.errors.id_level }"
            >
            <FormControl
                v-model="form.id_level"
                type="select"
                :options="props.levels"
                placeholder="Select a Level"
                :error="form.errors.id_level"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.id_level">
                {{ form.errors.id_level }}
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

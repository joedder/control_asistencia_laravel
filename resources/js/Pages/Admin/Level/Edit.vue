<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiViewList,
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
  level: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  name: props.level.name,
  description: props.level.description
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Edit Level" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiViewList"
        title="Edit Level"
        main
      >
        <BaseButton
          :route-name="route('admin.level.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox
        form
        @submit.prevent="form.put(route('admin.level.update', level.id))"
      >
        <div class="grid grid-cols-1 gap-6">
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
            label="Description"
            :class="{ 'text-red-400': form.errors.description }"
            >
            <FormControl
                v-model="form.description"
                type="textarea"
                placeholder="Enter Description"
                :error="form.errors.description"
            >
                <div class="text-red-400 text-sm" v-if="form.errors.description">
                {{ form.errors.description }}
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

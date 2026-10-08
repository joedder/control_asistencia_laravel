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
  groups: {
    type: Object,
    default: () => ({}),
  }
})

const form = useForm({
  id_group: props.movement.id_group,
  id_new_group: props.movement.id_new_group,
  id_student: props.movement.id_student,
  migrated: props.movement.migrated,
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Edit Movement" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiTransitTransfer"
        title="Edit Movement"
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
            <h3 class="font-bold text-lg mb-2">Student Information</h3>
            <p><strong>Name:</strong> {{ movement.student?.name }} {{ movement.student?.last_name || '' }}</p>
            <p><strong>Identity:</strong> {{ movement.student?.identity_id }}</p>
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
              If checked, the student will be moved to the new group. If unchecked, the student will be reverted to the old group.
            </div>
            <div class="text-red-400 text-sm" v-if="form.errors.migrated">
                {{ form.errors.migrated }}
            </div>
        </div>

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Update Movement"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

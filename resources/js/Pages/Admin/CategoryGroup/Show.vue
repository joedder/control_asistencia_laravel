<script setup>
import { Head, Link } from "@inertiajs/vue3"
import {
  mdiFormatListBulletedType,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/Admin/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  category_group: {
    type: Object,
    default: () => ({}),
  }
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="View Category Group" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiFormatListBulletedType"
        title="View Category Group"
        main
      >
        <BaseButton
          :route-name="route('admin.category-group.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox class="mb-6">
        <table>
          <tbody>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Name</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ category_group.name }}
              </td>
            </tr>
            <tr>
              <td class="p-4 font-semibold border-b dark:border-slate-800">Description</td>
              <td class="p-4 border-b dark:border-slate-800">
                {{ category_group.description }}
              </td>
            </tr>
          </tbody>
        </table>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

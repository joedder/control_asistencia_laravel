<script setup>
import { ref, watch, computed } from 'vue'
import { mdiCalendar } from '@mdi/js'
import BaseIcon from '@/Components/BaseIcon.vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'dd/mm/aaaa'
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const pickerRef = ref(null)

// Parse input string to YYYY-MM-DD
const parseToIso = (str) => {
  if (!str) return ''
  const trimmed = String(str).trim()

  // YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(trimmed)) {
    return trimmed
  }

  // DD/MM/YYYY or DD-MM-YYYY
  const m1 = trimmed.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/)
  if (m1) {
    const d = m1[1].padStart(2, '0')
    const m = m1[2].padStart(2, '0')
    const y = m1[3]
    return `${y}-${m}-${d}`
  }

  // 8 continuous digits DDMMYYYY
  const m2 = trimmed.match(/^(\d{2})(\d{2})(\d{4})$/)
  if (m2) {
    return `${m2[3]}-${m2[2]}-${m2[1]}`
  }

  return ''
}

// Format ISO YYYY-MM-DD to DD/MM/YYYY
const toDisplay = (iso) => {
  if (!iso) return ''
  const trimmed = String(iso).trim()
  const m = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (m) {
    return `${m[3]}/${m[2]}/${m[1]}`
  }
  const m1 = trimmed.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/)
  if (m1) {
    return `${m1[1].padStart(2, '0')}/${m1[2].padStart(2, '0')}/${m1[3]}`
  }
  return trimmed
}

const displayValue = ref(toDisplay(props.modelValue))

const pickerValue = computed(() => {
  return parseToIso(props.modelValue) || ''
})

watch(() => props.modelValue, (newVal) => {
  displayValue.value = toDisplay(newVal)
})

const onTextInput = (e) => {
  const val = e.target.value
  displayValue.value = val

  const iso = parseToIso(val)
  if (iso) {
    emit('update:modelValue', iso)
  } else if (!val.trim()) {
    emit('update:modelValue', '')
  }
}

const onTextBlur = () => {
  const trimmed = displayValue.value.trim()
  if (!trimmed) {
    displayValue.value = ''
    emit('update:modelValue', '')
    return
  }

  const iso = parseToIso(trimmed)
  if (iso) {
    displayValue.value = toDisplay(iso)
    emit('update:modelValue', iso)
  }
}

const onPickerInput = (e) => {
  const isoVal = e.target.value
  if (isoVal) {
    displayValue.value = toDisplay(isoVal)
    emit('update:modelValue', isoVal)
  } else {
    displayValue.value = ''
    emit('update:modelValue', '')
  }
}
</script>

<template>
  <div class="relative w-full">
    <input
      type="text"
      :value="displayValue"
      @input="onTextInput"
      @blur="onTextBlur"
      :placeholder="placeholder"
      :disabled="disabled"
      maxlength="10"
      class="w-full rounded-md shadow-sm border-gray-300 dark:bg-slate-800 dark:border-slate-700 text-sm pr-9"
    />
    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400 dark:text-gray-300">
      <BaseIcon :path="mdiCalendar" size="18" />
    </div>
    <input
      ref="pickerRef"
      type="date"
      :value="pickerValue"
      @input="onPickerInput"
      :disabled="disabled"
      class="absolute inset-y-0 right-0 w-9 opacity-0 cursor-pointer"
      tabindex="-1"
      title="Seleccionar fecha"
    />
  </div>
</template>

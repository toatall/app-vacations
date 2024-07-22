<script setup>
import { v4 as uuid } from 'uuid'
import InputText from 'primevue/inputtext'

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  id: {
    type: String,
    default() {
      return `text-input-${uuid()}`
    },
  },
  type: {
    type: String,
    default: 'text',
  },
  error: Array,
  label: String,  
})
</script>

<template>  
  <label v-if="label" class="block mb-2" :for="id">{{ label }}</label>
  <InputText :id="id" class="w-full" ref="input" v-bind="{ ...$attrs, class: null }" :type="type" :invalid="error != undefined" />
  <div v-if="error" class="form-error">{{ Array.isArray(error) ? error.join(', ') : error }}</div>
</template>



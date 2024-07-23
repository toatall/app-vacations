<script setup>
import { v4 as uuid } from 'uuid'
import Dropdown from 'primevue/dropdown'

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  id: {
    type: String,
    default() {
      return `dropdown-input-${uuid()}`
    },
  },
  error: String,
  label: String,  
})
</script>

<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <Dropdown 
      :id="id" 
      v-bind="{ ...$attrs, class: null }" 
      :invalid="error != undefined"
      class="w-full" />    
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

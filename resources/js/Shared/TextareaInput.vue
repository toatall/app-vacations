<script setup>
import { v4 as uuid } from 'uuid'
import { ref } from 'vue';

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  id: {
    type: String,
    default() {
      return `textarea-input-${uuid()}`
    },
  },
  error: String,
  label: String,
  modelValue: String,
})

const emits = defineEmits(['update:modelValue'])
const input = ref(null)

const focus = () => {
  input.value.focus()
}

const select = () => {
  input.value.select()
}

const updateModelValue = (e) => {
  emits('update:modelValue', e.target.value)
}

</script>

<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <textarea :id="id" ref="input" v-bind="{ ...$attrs, class: null }" class="form-textarea" :class="{ error: error }" :value="modelValue" @input="updateModelValue" />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>


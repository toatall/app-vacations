<script setup>
import Icon from '@/Shared/Icon.vue'

const emits = defineEmits(['restore'])
const restore = () => {
  emits('restore')
}

</script>

<template>
  <div class="flex items-center justify-between p-4 max-w-3xl bg-yellow-400 rounded">
    <div class="flex items-center">
      <icon name="trash" class="shrink-0 mr-2 w-4 h-4 fill-yellow-800" />
      <div class="text-yellow-800 text-sm font-medium">
        <slot />
      </div>
    </div>
    <button class="text-yellow-800 hover:underline text-sm" tabindex="-1" type="button" @click="restore">Restore</button>
  </div>
</template>
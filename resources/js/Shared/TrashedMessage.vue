<script setup>
import Button from 'primevue/button';

const emits = defineEmits(['restore'])
const restore = () => {
  emits('restore')
}

</script>

<template>
  <div class="flex items-center justify-between p-2 max-w-3xl bg-yellow-400 rounded">
    <div class="flex items-center">
      <span class="pi pi-trash shrink-0 mr-2 w-4 h-4 text-yellow-800"></span>      
      <div class="text-yellow-800 text-sm font-medium">
        <slot />
      </div>
    </div>   
    <Button severity="warning" @click="restore" label="Восстановить" size="small" />
  </div>
</template>
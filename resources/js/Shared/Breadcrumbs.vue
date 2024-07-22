<script setup>
import Breadcrumb from 'primevue/breadcrumb'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  items: Array,
})

</script>
<template>  
  <Breadcrumb :model="items" class="mb-2">
    <template #item="{ item }">            
      <Link v-if="item.url" :href="item.url" :target="item.target" class="text-indigo-400 hover:text-indigo-600">
          <template v-if="item.icon">
            <i :class="`pi ${item.icon}`" class="me-2"></i>
          </template>
          <template v-if="item.label">
            {{ item.label }}
          </template>          
      </Link>
      <span v-else>        
        <template v-if="item.icon">
            <i :class="`pi ${item.icon}`" class="me-2"></i>
          </template>
          <template v-if="item.label">
            <span class="text-surface-700 dark:text-surface-0">{{ item.label }}</span>
          </template>
      </span>
    </template>
  </Breadcrumb>
</template>
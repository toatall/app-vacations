<script setup>
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const page = usePage();
const toast = useToast();

const liefTimeout = 10000;

watch(
  () => page.props.flash,
  () => {   
    if (page.props.flash.success) {
      toast.add({ 
        severity: 'success', 
        summary: 'Успешно', 
        detail: page.props.flash.success, 
        life: liefTimeout,
      });
    }
    if (page.props.flash.error) {
      toast.add({ 
        severity: 'error', 
        summary: 'Ошибка', 
        detail: page.props.flash.error, 
        life: liefTimeout,
      });
    }
    else if(Object.keys(page.props.errors).length > 0) {
      Object.values(page.props.errors).forEach(error => {
        toast.add({ 
          severity: 'error', 
          summary: 'Ошибка', 
          detail: Array.isArray(error) ? error.join(', ') : error, 
          life: liefTimeout,
        });
      });
    }
  },
  { deep: true }
)

</script>
<template>
  <Toast position="bottom-right" />
</template>
<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import Form from './Form.vue'

defineOptions({
  layout: Layout,
  remember: 'form',
})

const props = defineProps({
  labels: Object,
})

const form = useForm({
  code: null,
  name: null,  
})
  
const store = () => {
  form.post('/organizations')
}

</script>

<template>
  <div>
    <Head title="Создание организации" />

    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: 'Организации', url: '/organizations' },
      { label: 'Создание' }
    ]" />

    <Form @save="store" :organization="null" :form="form" :labels="labels" />

  </div>
</template>


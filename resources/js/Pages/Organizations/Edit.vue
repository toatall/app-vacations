<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { useConfirm } from "primevue/useconfirm"
import Form from './Form.vue'

defineOptions({
  layout: Layout,
  remember: 'form',
})

const props = defineProps({
  organization: Object,
  labels: Object,
})

const form = useForm({
  code: props.organization.code,
  name: props.organization.name,  
})

const confirm = useConfirm()

const update = () => {
  form.put(`/organizations/${props.organization.code}`)
}

const destroy = () => {
  confirm.require({
    header: 'Удаление организации',
    message: 'Вы уверены, что хотите удалить организацию?',
    accept: () => {
      form.delete(`/organizations/${props.organization.code}`)
    },
  })
}

const restore = () => {
  confirm.require({
    header: 'Восстановление организации',
    message: 'Вы уверены, что хотите восстановить организацию?',
    accept: () => {
      form.put(`/organizations/${props.organization.code}/restore`)
    },
  })  
}
</script>

<template>
  <div>
    <Head :title="form.name" />

    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: 'Организации', url: '/organizations' },
      { label: form.name }
    ]" />

    <Form @save="update" @restore="restore" @destroy="destroy" :organization="organization" :form="form" :labels="labels" />
    
  </div>
</template>


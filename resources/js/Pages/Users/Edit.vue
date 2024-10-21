<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import { useConfirm } from "primevue/useconfirm"
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import Form from './Form.vue'
import { computed } from 'vue'

defineOptions({
  layout: Layout,  
  remember: 'form',
})

const props = defineProps({
  auth: Object,
  user: Object,
  organizations: Array,
  labels: Object,
  roles: Object,
})

const form = useForm({
  username: props.user.username,
  full_name: props.user.full_name,
  email: props.user.email,
  newPassword: '',
  org_code: props.user.org_code,
  position: props.user.position,
  roles: props.roles.currentUser,
})

const update = () => {
  form.put(`/users/${props.user.id}`, {
    onSuccess: () => form.reset('password'),
  })
}

const confirm = useConfirm()

const destroy = () => {
  confirm.require({
    header: 'Удаление пользователя',
    message: 'Вы уверены, что хотите удалить пользователя?',
    accept: () => {
      form.delete(`/users/${props.user.id}`)
    },
  })  
}

const restore = () => {
  confirm.require({
    header: 'Восстановление пользователя',
    message: 'Вы уверены, что хотите восстановить пользователя?',
    accept: () => {
      form.put(`/users/${props.user.id}/restore`)
    },
  })
}

const title = computed(() => {
  return `${form.full_name ?? ''} (${form.username})`
})

</script>
<template>
  <div>
    <Head :title="title" />

    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: 'Пользователи', url: '/users' },
      { label: title },
    ]" />    

    <Form 
      :labels="labels" 
      :user="user" 
      :auth="auth" 
      :roles="roles"
      :organizations="organizations" 
      :form="form" 
      :isNew="false" 
      @save="update" 
      @restore="restore" 
      @destroy="destroy" 
    />
    
  </div>
</template>


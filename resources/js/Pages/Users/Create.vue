<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import FileInput from '@/Shared/FileInput.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import Form from './Form.vue'

defineOptions({
  layout: Layout,
  remember: 'form',
})

const props = defineProps({
  auth: Object,
  labels: Object,
  organizations: Array,
})

const form = useForm({
  username: null,
  full_name: null,
  email: null,
  newPassword: null,
  org_code: null,
  userPost: null,
})

const store = () => {
  form.post('/users')
}

const title = 'Создать пользователя'
</script>
<template>
  <div>
    <Head :title="title" />

    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: 'Пользователи', url: '/users' },
      { label: title },
    ]" />    

    <Form :labels="labels" :user="null" :auth="auth" :organizations="organizations" :form="form" @save="store" />
    
  </div>
</template>


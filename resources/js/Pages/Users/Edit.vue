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
  userPost: props.user.userPost,
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

    <!-- <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore"> This user has been deleted. </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.username" :error="form.errors.username" class="pb-8 pr-6 w-full lg:w-1/2" label="Username" />
          <text-input v-model="form.full_name" :error="form.errors.full_name" :disabled="props.auth.useWindowsAuthenticate" class="pb-8 pr-6 w-full lg:w-1/2" label="Full name" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.newPassword" :error="form.errors.newPassword" class="pb-8 pr-6 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />          
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete User</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update User</loading-button>
        </div>
      </form>
    </div> -->
  </div>
</template>


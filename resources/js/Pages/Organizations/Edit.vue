<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'
import Button from 'primevue/button'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { useConfirm } from "primevue/useconfirm"

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
    
    <div class="grid grid-cols-1 gap-9 sm:grid-cols-2">
      <div class="flex flex-col gap-9">
        <div class="rounded border border-stroke bg-white shadow">
          <div class="border-b border-stroke py-4 px-6">
            <h3 class="font-semibold text-black">
              {{ form.name }}
            </h3>
          </div>
          <div class="px-5 pt-3">
            <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Запись была удалена. </trashed-message>
          </div>
          <form @submit.prevent="update">
            <div class="p-6">
              <div class="mb-4 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                  <text-input v-model="form.code" :error="form.errors.code" class="pb-8 pr-6 w-full lg:w-1/2" :label="labels.code" />
                </div>
                <div class="w-full xl:w-1/2">
                  <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" :label="labels.name" />
                </div>
              </div>
            </div>
            <div class="flex items-center justify-between px-8 py-4 bg-gray-50 border-t border-gray-100">          
              <Button v-if="!organization.deleted_at" @click="destroy" severity="danger" label="Удалить" />
              <span v-else></span>
              <loading-button :loading="form.processing" class="btn-indigo" type="submit">Обновить</loading-button>
            </div>
          </form>
        </div>        
      </div>
    </div>      
  </div>
</template>


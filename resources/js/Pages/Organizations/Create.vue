<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

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
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Организации</Link>
      <span class="text-indigo-400 font-medium">/</span> Создание
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.code" :error="form.errors.code" class="pb-8 pr-6 w-full lg:w-1/2" :label="labels.code" />
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" :label="labels.name" />          
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>


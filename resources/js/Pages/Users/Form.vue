<script setup>
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'
import Button from 'primevue/button'
import DropdownInput from '@/Shared/DropdownInput.vue'

const props = defineProps({
  auth: Object,
  form: Object,
  user: Object,
  labels: Object,
  isNew: {
    type: Boolean,
    default() {
      return true;
    }
  },
  organizations: Array,
})
</script>
<template>
  <div class="grid grid-cols-1 gap-9 xl:grid-cols-2">
    <div class="flex flex-col gap-9">
      <div class="rounded border border-stroke bg-white shadow">
        <div class="border-b border-stroke py-4 px-6">
          <h3 class="font-semibold text-black">
            {{ props.form.username ?? 'Новый пользователь' }}
          </h3>
        </div>
        <div class="px-5 pt-3">
          <trashed-message v-if="!isNew && user && user.deleted_at" class="mb-6"
            @restore="$emit('restore')"> Пользователь был удален.
          </trashed-message>
        </div>
        <form @submit.prevent="$emit('save')">
          <div class="p-6">
            <div class="mb-4 flex flex-col gap-6 xl:flex-row">
              <div class="w-full xl:w-1/2">
                <text-input v-model="form.username" :error="form.errors.username" class="pb-8 w-full"
                  :label="labels.username" />
              </div>
              <div class="w-full xl:w-1/2">
                <text-input v-model="form.full_name" :error="form.errors.full_name" class="pb-8 w-full"
                  :label="labels.full_name" />
              </div>
            </div>
            <div class="mb-4 flex flex-col gap-6 xl:flex-row">
              <div class="w-full xl:w-1/2">
                <text-input v-model="form.email" :error="form.errors.email" type="email" class="pb-8 w-full"
                  :label="labels.email" />
              </div>
              <div class="w-full xl:w-1/2">
                <text-input v-model="form.newPassword" :error="form.errors.newPassword" type="password" class="pb-8 w-full"
                  :label="labels.password" />
              </div>
            </div>
            <div class="mb-4 flex flex-col gap-6 xl:flex-row">
              <div class="w-full xl:w-1/2">
                <text-input v-model="form.userPost" :error="form.errors.post" class="pb-8 w-full"
                  :label="labels.post" />
              </div>
              <div class="w-full xl:w-1/2">                
                  <DropdownInput 
                    v-model="form.org_code" 
                    :label="labels.org_code" 
                    :options="organizations" 
                    optionLabel="name" 
                    optionValue="code" 
                    class="pb-8 w-full" />
              </div>
            </div>
          </div>
          <div class="flex items-center justify-between px-8 py-4 bg-gray-50 border-t border-gray-100">
            <Button v-if="!isNew && user && !user.deleted_at" @click="$emit('destroy')"
              severity="danger" label="Удалить" />
            <span v-else></span>
            <loading-button :loading="form.processing" class="btn-indigo" type="submit">Сохранить</loading-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
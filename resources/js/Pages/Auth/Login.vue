<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Logo from "@/Shared/Logo.vue";
import TextInput from "@/Shared/TextInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";

const form = useForm({
  email: 'admin@example.com',
  password: 'secret',
  remember: false,
})

const login = () => {
  form.post('/login');
}
</script>
<template>

  <Head title="Login" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-white" style="width: 150px; height: 150px;" />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-bold">Добро пожаловать!</h1>
          <div class="w-full">
            <div class="w-full mt-4">
              <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2"
                label="Имя пользователя или email" type="text" autofocus autocapitalize="off" />
            </div>
            <div class="w-full mt-4">
              <text-input v-model="form.password" :error="form.errors.password" type="password"
                class="pb-8 pr-6 w-full lg:w-1/2" label="Пароль" />
            </div>
            <div class="w-full mt-4">
              <label class="flex items-center mt-6 select-none" for="remember">
                <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
                <span class="text-sm">Запомнить меня</span>
              </label>
            </div>
          </div>
        </div>
        <div class="flex px-10 py-4 bg-gray-100 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Вход</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import Message from "primevue/message";

const props = defineProps({
  username: String,
})

</script>
<template>

  <Head title="Вход" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <div class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-6 py-6">
          <Message :closable="false" severity="warn">
            Учетная запись {{ username }} не найдена!
          </Message>
        </div>
      </div>
    </div>
  </div>
</template>
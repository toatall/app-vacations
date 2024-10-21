<script setup>
import Checkbox from 'primevue/checkbox'

const props = defineProps({
    roles: Object,
    form: Object,
})

</script>
<template>
    <div class="card flex flex-wrap flex-col justify-center gap-4">        
        <div class="flex items-center" v-for="role in roles.all">
            <Checkbox v-model="form.roles" :inputId="role.name" :name="role.name" :value="role.name" />
            <label :for="role.name" class="ml-2 cursor-pointer"> {{role.description}} </label>
        </div>        
    </div>
</template>
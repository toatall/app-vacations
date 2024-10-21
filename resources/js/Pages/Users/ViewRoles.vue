<script setup>

const props = defineProps({
    form: Object,
    roles: Object,
})
</script>
<template>    
    <div v-if="form.roles?.length > 0" class="card flex flex-wrap flex-col justify-center gap-4">        
        <div class="list-none" v-for="role in form.roles">            
            <li><span class="pi pi-check"></span> {{roles.all[role].name}} </li>
        </div>        
    </div>
    <div v-else class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 w-full" role="alert">
        Роли не назначены.
    </div>
</template>
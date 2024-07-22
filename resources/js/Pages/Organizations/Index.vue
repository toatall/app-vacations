<script setup>
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import { ref } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import { FilterMatchMode } from 'primevue/api'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'

defineOptions({
  layout: Layout,
})

const props = defineProps({
  filters: Object,
  organizations: Object,
})
    
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    code: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },    
})

const selected = ref()
const onRowSelect = (event) => {    
    router.get(`/organizations/${event.data.code}/edit`)
}

const create = () => {
    router.get('/organizations/create')
};

</script>
<template>
  <div>
    <Head title="Организации" />

    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: 'Организации' }
    ]" />    
    
    <DataTable 
      :value="organizations.data" 
      tableStyle="min-width: 50rem" 
      :paginator="true" 
      :rows="10" 
      :globalFilterFields="['code', 'name']"
      v-model:filters="filters"
      showGridlines 
      dataKey="code"
      v-model:selection="selected"
      selectionMode="single"
      @rowSelect="onRowSelect"     
    >
      <template #empty> Нет данных </template>
      <template #header>
          <div class="flex justify-end">
              <Button type="button" class="mr-2" label="Создать" outlined @click="create()" />
              <span class="relative">
                  <i class="pi pi-search absolute top-2/4 -mt-2 left-3 text-surface-400 dark:text-surface-600" />
                  <InputText v-model="filters['global'].value" placeholder="Поиск" class="pl-10 font-normal"/>
              </span>
          </div>
      </template>
      <Column field="code" header="Код" sortable></Column>
      <Column field="name" header="Наименование" sortable></Column>      
    </DataTable>


  </div>
</template>


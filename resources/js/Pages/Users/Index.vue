<script setup>
import { Head, router } from '@inertiajs/vue3'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout.vue'
import throttle from 'lodash/throttle'
import { reactive, ref, watch } from 'vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Toolbar from 'primevue/toolbar'
import IconField from 'primevue/iconfield'
import Dropdown from 'primevue/dropdown'
import InputGroup from 'primevue/inputgroup'
import InputIcon from 'primevue/inputicon'
import Tag from 'primevue/tag'

defineOptions({
  layout: Layout,  
  
})

const props = defineProps({
  filters: Object,
  users: Array,
  labels: Object,
})
  
const form = reactive({
  search: props.filters.search,
  role: props.filters.role,
  trashed: props.filters.trashed,
}) 

watch(
  () => form,
  throttle(() => {
    router.get('/users', pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
)

const reset = () => {
  form.search = null
  form.trashed = null
  form.role = null
}

const selected = ref()
const onRowSelect = (event) => {    
    router.get(`/users/${event.data.id}/edit`)
}

const create = () => {
  router.get('/users/create')
}

const title = 'Пользователи'
</script>
<template>
  <div>
    <Head :title="title" />
    
    <Breadcrumbs :items="[
      { icon: 'pi-home', label: 'Главная', url: '/' }, 
      { label: title }
    ]" />    

    <DataTable 
      :value="users" 
      tableStyle="min-width: 50rem" 
      :paginator="true" 
      :rows="10"       
      showGridlines 
      dataKey="id"
      v-model:selection="selected"
      selectionMode="single"
      @rowSelect="onRowSelect"     
    >
      <template #empty> Нет данных </template>
      <template #header>          
        <Toolbar>          
          <template #start>
            <div class="grid grid-cols-4 gap-3">
              <InputGroup class="col-span-2">
                <IconField iconPosition="left" class="w-full">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Поиск" v-model="form.search" />
                </IconField>                
              </InputGroup>              
              <InputGroup>
                <Dropdown 
                  v-model="form.trashed" 
                  :options="[
                    { name: 'с удаленными', code: 'with' }, 
                    { name: 'только удаленные', code: 'only' },
                  ]" 
                  optionLabel="name" 
                  optionValue="code"
                  placeholder="Удаленные" 
                  class="w-full md:w-[14rem]"
                  showClear 
                />
              </InputGroup>
              <InputGroup>
                <Button icon="pi pi-times" size="small" @click="reset" severity="secondary" />
              </InputGroup>
            </div>
          </template>

          <template #end> 
            <Button @click="create" label="Создать" />
          </template>
        </Toolbar>

      </template>
      <Column field="username" :header="labels.username" sortable>
        <template #body="{ data }">
          <div>{{ data.username }}</div>
          <Tag v-if="data.deleted_at" severity="danger" value="Пользователь удален" class="mt-2" />
        </template>
      </Column>
      <Column field="full_name" :header="labels.full_name" sortable></Column>
      <Column field="email" :header="labels.email" sortable></Column>  
      <Column field="org_code" :header="labels.org_code" sortable></Column>    
    </DataTable>

  </div>
</template>
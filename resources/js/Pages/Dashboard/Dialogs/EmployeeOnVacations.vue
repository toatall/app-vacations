<script setup>
import { inject, onMounted, ref } from 'vue';
import routes from '@/Config/routes'
import axios from 'axios'
import ProgressSpinner from 'primevue/progressspinner'
import Message from 'primevue/message'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { FilterMatchMode } from 'primevue/api'
import InputText from 'primevue/inputtext'
import InputIcon from 'primevue/inputicon'
import IconField from 'primevue/iconfield'
import ProgressBar from 'primevue/progressbar'

const dialogRef = inject('dialogRef')
const moment = inject('moment')

const loading = ref()
const errorMessage = ref()
const items = ref()

onMounted(() => {
  loading.value = true
  axios.get(routes.employeesOnVacations(dialogRef.value.data.code_org, dialogRef.value.data.year))
    .then(({data}) => {
      items.value = data
    })
    .catch((error) => {
        console.log(error)
        errorMessage.value = error.message
    })
    .finally(() => {
        loading.value = false
    })
})

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const calcPercentsBetweenDates = (dateFrom, dateTo) => {
  const momentDateFrom = moment(dateFrom)
  const momentDateTo = moment(dateTo)
  const momentDateNow = moment()

  const countAllDays = momentDateTo.diff(momentDateFrom, 'days') + 1
  const countTodayDays = momentDateNow.diff(momentDateFrom, 'days') + 1

  return Math.round((countTodayDays / countAllDays) * 100);
}
</script>
<template> 

  <Message v-if="errorMessage" severity="error" :closable="false">
    {{ errorMessage }}
  </Message>

  <DataTable v-else 
    v-model:filters="filters"
    :globalFilterFields="['full_name', 'post', 'name']"
    :value="items" 
    tableStyle="min-width: 100%;" 
    class="m-4" 
    selectionMode="single"                 
    dataKey="full_name" 
    :loading="loading"
    paginator 
    :rows="10"
    sortField="full_name"
    :sortOrder="1"
  >  
    <template #header>
        <div class="flex justify-end">
            <IconField>
                <InputIcon>
                    <i class="pi pi-search" />
                </InputIcon>
                <InputText v-model="filters['global'].value" placeholder="Поиск" />
            </IconField>
        </div>
    </template>
  
    <template #empty> Нет данных. </template>
    <template #loading> 
      <ProgressSpinner /> 
    </template>

    <Column field="full_name" header="ФИО" sortable class="w-1/4" />
    <Column field="post" header="Должность" sortable class="w-1/4" />
    <Column field="name" header="Отдел" sortable class="w-1/4" />   
    <Column header="Период" class="w-1/4">
      <template #body="{ data }">  
        <div v-tooltip.bottom="`Отпуск закончится ${moment(data.date_to).endOf('day').fromNow()}`">
          <div class="mb-3">
            {{ moment(data.date_from).format('L') }} - {{ moment(data.date_to).format('L') }}
          </div>        
          <ProgressBar 
            :value="calcPercentsBetweenDates(data.date_from, data.date_to)" 
            :showValue="false" 
            style="height: 0.5rem;"
          />
        </div>
      </template>
    </Column>    
  </DataTable> 

</template>
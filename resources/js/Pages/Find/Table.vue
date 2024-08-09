<script setup>
import { inject, ref, watch } from 'vue';
import axios from 'axios';
import routes from '@/Config/routes.js'
import ProgressSpinner from 'primevue/progressspinner'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import Tag from 'primevue/tag'
import Button from 'primevue/button'

const props = defineProps({
  orgCode: String,
  year: String,
})

const loading = ref(false);
const sourceData = ref()

const items = ref();
const moment = inject('moment')

const sortField = (item) => {
  return (item.sort_index_department ? item.sort_index_department : item.department)
    + '_' + (item.sort_index_employee ? item.sort_index_employee : item.full_name);
}

const expandedRows = ref({});
const expandAll = () => {
    expandedRows.value = items.value.reduce((acc, p) => (acc[p.id_employee] = true) && acc, {});
};
const collapseAll = () => {
    expandedRows.value = null;
};

const transform = (data) => {
  let transformed = [];
  Object.values(data).forEach(item => {
    if (!transformed[item.id_employee]) {
      transformed[item.id_employee] = {
        id_employee: item.id_employee,
        department: item.department,
        sort: sortField(item),
        full_name: item.full_name,
        post: item.post,
        vacations: [],
        isVacation: false,
        returnFromVacation: null,
        dateEndVacation: null,
      }
    }
    transformed[item.id_employee].vacations.push({
      date_from: item.date_from,
      date_to: item.date_to,      
      used: moment().isAfter(item.date_to),
    })
    if (moment().isBetween(item.date_from, item.date_to)) {
      transformed[item.id_employee].isVacation = true;
      transformed[item.id_employee].returnFromVacation = moment(item.date_to).fromNow();
      transformed[item.id_employee].dateEndVacation = moment(item.date_to).format('L');
    }
  })
  return transformed;
}

watch(
  () => [props.orgCode, props.year],
  () => {
    loading.value = true;

    axios.get(routes.vacationsFind(props.orgCode, props.year))
      .then(({ data }) => {

        sourceData.value = data;
        items.value = transform(data);

        loading.value = false;
      })
  }
)

const filterFio = ref()
const filterDateFrom = ref()
const filterDateTo = ref()
watch(
  () => [filterFio.value, filterDateFrom.value, filterDateTo.value],
  () => {
    const dateFrom = filterDateFrom.value ? moment(filterDateFrom.value) : null;
    const dateTo = filterDateTo.value ? moment(filterDateTo.value) : (dateFrom ? dateFrom : null);
    const fio = filterFio.value;

    const newItems = sourceData.value.filter(item => {
      if (fio && !item.full_name.toLowerCase().includes(fio.toLowerCase())) {
        return false;
      }

      if (dateFrom) {        
        if (!(moment(item.date_from).isSameOrAfter(dateFrom) || dateFrom.isBetween(item.date_from, item.date_to, undefined, '[]'))) {
          return false;
        }
      }

      if (dateTo) {        
        if (!(moment(item.date_to).isSameOrBefore(dateTo) || dateTo.isBetween(item.date_from, item.date_to, undefined, '[]'))) {
          return false;
        }
      }

      return true;
    })

    items.value = transform(newItems);

  }
)

</script>
<template>
  <div>      
    <DataTable 
      v-model:expandedRows="expandedRows"      
      :value="items?.filter((item) => item.vacations.length > 0)"
      paginator :rows="10" dataKey="id_employee" :loading="loading"      
      sortField="sort" :sortOrder="1"
      scrollHeight="55vh">
      <template #header>        
        <div class="grid grid-cols-6 gap-4">
          <div>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filterFio" placeholder="Поиск по ФИО" />
            </IconField>
          </div>
          <div>
            <InputText type="date" v-model="filterDateFrom" />
          </div>
          <div class="col-start-5 col-span-2 flex justify-end">
            <Button text severity="secondary" icon="pi pi-plus" label="Развернуть все" @click="expandAll" />
            <Button text severity="secondary" icon="pi pi-minus" label="Свернуть все" @click="collapseAll" />
          </div>
        </div>
      </template>
      <Column expander style="width: 5rem" />
      <Column field="full_name" header="ФИО">
        <template #body="{ data }">
          <span class="block">{{ data.full_name }}</span>
          <Tag v-if="data.isVacation" v-tooltip="`Закончится ${data.returnFromVacation} (${data.dateEndVacation})`">В отпуске</Tag>
        </template>
      </Column>      
      <Column field="department" header="Отдел"></Column>        
      <Column field="post" header="Должность"></Column>
      <template #expansion="{ data }">
        <div class="p-4">
          <h5>Отпуска</h5>
          <DataTable :value="data?.vacations ?? []">
            <Column field="date_from" header="Начало отпуска">
              <template #body="{ data }">
                {{ moment(data?.date_from).format('L') }}
              </template>
            </Column>
            <Column field="date_to" header="Окончание отпуска">
              <template #body="{ data }">
                {{ moment(data?.date_to).format('L') }}
              </template>
            </Column>            
            <Column header="Статус">
              <template #body="{ data }">
                <Tag v-if="data?.used" severity="info">Использован</Tag>
                <Tag v-if="moment().isBetween(data.date_from, data.date_to, undefined, '[]')">В отпуске</Tag>
              </template>                
            </Column>
          </DataTable>
        </div>
      </template>

      <template #empty> Нет данных</template>
      <template #loading> 
        <ProgressSpinner />
      </template>

    </DataTable>    
  </div>
</template>
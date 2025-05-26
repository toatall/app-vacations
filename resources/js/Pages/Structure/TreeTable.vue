<script setup>
import axios from 'axios';
import routes from '@/Config/routes.js'
import { inject, ref, watch } from 'vue';
import TreeTable from 'primevue/treetable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import ProgressSpinner from 'primevue/progressspinner';
import Message from 'primevue/message';

const props = defineProps({
  orgCode: String,
  year: String,
});

const loading = ref(false);
const treeData = ref();
const moment = inject('moment')


watch(
  () => [props.orgCode, props.year],
  () => {
    loading.value = true;

    axios.get(routes.structureData(props.orgCode, props.year))
      .then(({ data }) => {
        treeData.value = toArrayAndSort(data);        
        loading.value = false;
      })
  }
);

// 1. из объекта в массив
// 2. сортировка по столбцу sortIndex
const toArrayAndSort = (arr) => {  
  let resArray = [];
  for(const item of Object.values(arr)) {    
    if (item instanceof Object && 'children' in item) {
      item.children = toArrayAndSort(item.children);
    }
    resArray.push(item);
  }
  return resArray.sort((a, b) => a.sortIndex - b.sortIndex); 
}

// проверить в отпуске сейчас сотрудник
// по списку отпусков
const employeeOnVacation = (vacations) => {
  let res = false;
  const now = moment();  
  for(const vacation of vacations) {
    if (now.isBetween(vacation.data.date_from, vacation.data.date_to)) {
      res = true;
      break;
    }    
  }
  return res;
}

// текущее состояние отпуска
// -1 прошел
// 0 сейчас идет
// 1 в будущем
const periodOnTime = (vacation) => {
  const now = moment();
  if (now.isBetween(vacation.data.date_from, vacation.data.date_to)) {
    return 0;
  }
  else if (now.isBefore(vacation.data.date_from)) {
    return 1;
  }
  else {
    return -1;
  }
}

// событие при разворачивании ветки
const nodeExpand = (node) => {

  // срабатывание только при первом разворачивании
  if ('expanded' in node && node.expanded == true) {
    return;
  }
  node.expanded = true;

  // развернули отдел
  if (node.data.type == 'department') {    
    node.children.forEach((item) => {      
      if (employeeOnVacation(item.children)) {
        item.onVacation = true;
      }
    })
  }
  
  // развернули сотрудника
  if (node.data.type == 'employee') {
    node.children.forEach((item) => {
      item.onTime = periodOnTime(item);
    })
  }

}

</script>
<template>
  <TreeTable :value="treeData" @nodeExpand="nodeExpand" :loading="loading">    
    <Column field="data" header="Структура" expander expandedIcon="pi-minus-circle" collapsedIcon="pi-plus-circle">      
      <template #body="{ node }">
        <template v-if="node?.data?.type == 'department'">
          <i class="pi pi-building me-2"></i>
          {{ node?.data?.name }}
        </template>
        <template v-else-if="node?.data?.type == 'employee'">
          <i class="pi pi-user me-2"></i>
          <i class="pi pi-sun text-yellow-500" v-if="node?.onVacation"></i>
          {{ node?.data?.full_name }} ({{ node?.data?.post }})
          <Tag v-if="node?.onVacation" value="в отпуске" />
        </template>
        <template v-else-if="node?.data?.type == 'vacation'">
          <span :class="{
            'text-yellow-500': node?.onTime === 0,
            'text-green-600': node?.onTime === -1,
          }">
            <i class="pi pi-calendar me-2"></i>
            <i class="pi pi-check text-green-600" v-if="node?.onTime === -1"></i>
            {{ moment(node?.data?.date_from).format('L') }} - {{ moment(node?.data?.date_to).format('L') }}          
            <span>
              <template v-if="node?.onTime === 0">
                (закончится через {{ moment(node?.data?.date_to).endOf('day').fromNow() }})
              </template>
              <template v-else-if="node?.onTime === 1">
                (начнется через {{ moment(node?.data?.date_from).endOf('day').fromNow() }})
              </template>
              <template v-else>
                (уже использован)
              </template>
            </span>          
          </span>
        </template>
      </template>
    </Column>

    <template #empty>       
      <Message :closable="false">
        Нет данных
      </Message>
    </template>
    
    <template #loading> 
      <ProgressSpinner />
    </template>

  </TreeTable>

</template>
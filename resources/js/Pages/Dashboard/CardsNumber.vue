<script setup>
import axios from 'axios';
import { computed, defineAsyncComponent, ref, watch } from 'vue';
import Button from 'primevue/button';
import routes from '@/Config/routes';
import { useDialog } from 'primevue/usedialog'


const props = defineProps({
  orgCode: String,
  speedCounter: {
    type: Number,
    default: 200,
  },
  year: String,
})

watch(
  () => props.year,
  (year) => {    
    axios.get(routes.statisticsTotalEmployees(props.orgCode, year))
      .then(({data}) => {
        animate(data.total, total);
        animate(data.total_on_vacations, totalOnVacations);
        animate(data.total_will_be_on_vacations, totalWillBeOnVacations);
      })
  }
)

const calcPercent = (refValue, refTotal) => {
  if (refTotal.value == 0) {
    return 0;
  }
  const num = ((refValue.value / refTotal.value) * 100);
  return Math.round(num * 100) / 100;
}

const total = ref(0)
const totalOnVacations = ref(0)
const totalWillBeOnVacations = ref(0)
const totalOnVacationsPercent = computed(() => calcPercent(totalOnVacations, total))
const totalWillBeOnVacationsPercent = computed(() => calcPercent(totalWillBeOnVacations, total))

const animate = (total, num) => {
  const value = num.value;
  const time = total / props.speedCounter;
  if (value < total) {
    num.value = Math.ceil(value + time);
    setTimeout(() => animate(total, num), 1);
  } else {
    num.value = total;
  }
}


const dialog = useDialog()
const employeeOnVacationsDialog = defineAsyncComponent(() => import('@/Pages/Dashboard/Dialogs/EmployeeOnVacations.vue'))
const openEmployeeOnVacationsDialog = () => {
    dialog.open(employeeOnVacationsDialog, {
        props: {
            header: 'Сотрудники в отпуске',
            style: {
                width: '80vw',
            },
            breakpoints:{
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true,            
        },      
        data: {
            'code_org': props.orgCode,
            'year': props.year,
        }
    })  
}

const employeesWillBeOnVacations = defineAsyncComponent(() => import('@/Pages/Dashboard/Dialogs/EmployeeWillBeOnVacations.vue'))
const openEmployeeWillBeOnVacationsDialog = () => {
    dialog.open(employeesWillBeOnVacations, {
        props: {
            header: 'Будут в отпуске в ближайшие 7 дней',
            style: {
                width: '80vw',
            },
            breakpoints:{
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true,            
        },      
        data: {
            'code_org': props.orgCode,
            'year': props.year,
        }
    })  
}
</script>
<template>
  
  <!-- Total start -->
  <div class="rounded-lg border border-stroke bg-white hover:bg-stone-50 hover:ring hover:ring-blue-200 px-7 py-6 shadow-lg">
    <div class="flex font-bold text-lg text-neutral-400 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
      Общее количество сотрудников
    </div>
    <div class="mt-4 flex items-center justify-center">
      <span class="font-extrabold text-8xl text-indigo-600">{{ total }}</span>
    </div>
  </div>
  <!-- Total end -->
  
  <!-- Total on vacations start -->
  <div class="rounded-lg border border-stroke bg-white hover:bg-stone-50 hover:ring hover:ring-blue-200 px-7 py-6 shadow-lg">
    <div class="flex font-bold text-lg text-slate-500 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
      Количество сотрудников в отпуске
    </div>
    <div class="mt-4 flex flex-col items-center justify-center">
      <span class="font-extrabold text-8xl text-indigo-600">{{ totalOnVacations }}</span>
      <span class="text-slate-400 text-xl">{{ totalOnVacationsPercent }} %</span>
      <Button v-if="orgCode && year" class="mt-4 font-bold" size="small" @click="openEmployeeOnVacationsDialog" severity="info" label="Подробнее" />
    </div>
  </div>
  <!-- Total on vacations end -->

  <!-- Total start -->
  <div class="rounded-lg border border-stroke bg-white hover:bg-stone-50 hover:ring hover:ring-blue-200 px-7 py-6 shadow-lg">
    <div class="flex font-bold text-lg text-slate-500 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
      Будут в отпуске в ближайшую неделю
    </div>
    <div class="mt-4 flex flex-col items-center justify-center">
      <span class="font-extrabold text-8xl text-indigo-600">{{ totalWillBeOnVacations }}</span>
      <span class="text-slate-400 text-xl">({{ totalWillBeOnVacationsPercent }}%)</span>
      <Button v-if="orgCode && year" class="mt-4 font-bold" size="small" @click="openEmployeeWillBeOnVacationsDialog" severity="info" label="Подробнее" />
    </div>
  </div>
  <!-- Total end -->


</template>
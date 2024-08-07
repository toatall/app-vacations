<script setup>
import axios from 'axios'
import routes from '@/Config/routes.js'
import { computed, ref, watch } from 'vue'
import Tag from 'primevue/tag'
import Button from 'primevue/button'
import ProgressSpinner from 'primevue/progressspinner'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import InputText from 'primevue/inputtext'
import moment from 'moment'
import Worker from './PrepareVacations.worker'
import Message from 'primevue/message'
import Menu from 'primevue/menu'

const props = defineProps({
  orgCode: String,
  year: String,
})

const departments = ref([])
const search = ref('')
const calendar = ref()
const loading = ref(true)

const isFullscreen = ref(false)
const totalEmployees = ref(0)
const tableContainer = ref(null)

const calcTotalPercentEmployeesByMonth = (countByMonth) => {
  if (countByMonth == 0) {
    return 0;
  }
  return Math.round(((countByMonth / totalEmployees.value) * 100) * 100) / 100;
}

const toggleAllMonths = (hide) => {
  Object.values(calendar.value).forEach(month => month.hideDetail = hide)
}


watch(
  () => [props.orgCode, props.year],
  () => {
    loading.value = true;

    axios.get(routes.tableData(props.orgCode, props.year))
      .then(({ data }) => {

        const worker = new Worker();
        worker.onmessage = ({ data }) => {
          
          calendar.value = data.calendar;
          departments.value = data.items;
          totalEmployees.value = data.totalEmployees;
          console.log(departments.value)
          worker.terminate();
          loading.value = false;

          if (moment().format('YYYY') == props.year) {
            setTimeout(() => {
              let scrollLeft = 0;
              const headers = tableContainer.value.querySelectorAll('th.month');
              const month = moment().format('MM')
              for (let i=0; i<headers.length; i++) {              
                if (headers[i].getAttribute('month') < month) {                                
                  scrollLeft += headers[i].offsetWidth;
                }
              }
              tableContainer.value.scrollTo({
                left: scrollLeft,
                behavior: 'smooth',
              });
              
            }, 0);
          }
          
        }

        worker.postMessage({ year: props.year, data: data });

      })      
  }
)

const menuTable = ref();
const itemsMenuTable = ref([
  {
    items: [
      {        
        label: computed(() => isFullscreen.value ? 'Свернуть' : 'Развернуть на весь экран' ),
        icon: computed(() => isFullscreen.value ? 'pi pi-window-minimize' : 'pi pi-window-maximize'),
        command: () => isFullscreen.value = !isFullscreen.value,
      },
      {
        label: 'Развернуть все месяцы',
        icon: 'pi pi-arrow-up-right-and-arrow-down-left-from-center',
        command: () => toggleAllMonths(false),
      },
      {
        label: 'Свернуть все месяцы',
        icon: 'pi pi-arrow-down-left-and-arrow-up-right-to-center',
        command: () => toggleAllMonths(true),
      }
    ]
  }
]);

const toggleMenuTable = (event) => {
    menuTable.value.toggle(event);
};

</script>
<template>
  <div class="overflow-auto bg-white" 
    :class="{ 'max-h-[70vh] min-h-[70vh]': !isFullscreen, 'fixed top-0 left-0 w-full h-full z-50': isFullscreen }" 
    ref="tableContainer"
  >  
    <div v-if="loading" class="flex items-center justify-items-center max-h-[70vh] min-h-[70vh]">
      <ProgressSpinner />
    </div>
    <template v-else>
      <table v-if="departments.length" class="text-sm text-left text-gray-600 w-full bg-white border-0 border-separate border-spacing-0 p-0 position-relative">
        <thead class="text-lg text-gray-700 bg-gray-50 sticky top-0 z-[100] border-0 border-neutral-200">
          <tr class="m-0">
            <th 
              class="px-6 py-3 border-0 border-b border-r border-gray-200 bg-gray-100 sticky min-h-[6rem] max-h-[6rem] min-w-[25rem] max-w-[25rem] top-0 left-0 z-[100]" 
              rowspan="2"
            >
              <div class="flex justify-between">
                Наименование
                <div>                  
                  <Button type="button" size="small" icon="pi pi-ellipsis-v" @click="toggleMenuTable" aria-haspopup="true" aria-controls="menu_table" severity="secondary" />
                  <Menu ref="menuTable" id="menu_table" :model="itemsMenuTable" :popup="true" />
                </div>
              </div>
              <div class="mt-2">
                <IconField>
                  <InputIcon class="pi pi-search" />
                  <InputText v-model="search" placeholder="Поиск по ФИО" size="small" class="text-sm" />
                </IconField>
              </div>
            </th>
            <template v-for="m in calendar">            
                <th v-if="m.hideDetail" 
                  :month="m.month"
                  class="month sticky left-[25rem] top-0 min-w-[15rem]
                    px-6 py-3 border-0 border-b border-r border-gray-200 bg-gray-100 text-gray-700 text-center"                
                >
                  <div class="capitalize"><i class="pi pi-calendar"></i> {{ m.name }}</div>
                  <Button text @click="m.hideDetail = false" class="px-3 py-2" size="small" severity="secondary">
                    <i class="pi pi-plus-circle me-2"></i>Развернуть
                  </Button>
                </th>
                <th v-else
                  class="month sticky left-[25rem] top-0
                    px-6 py-3 border-0 border-b border-l border-gray-200 bg-gray-50 text-gray-500" 
                  :colspan="m.days"
                  :month="m.month"
                >                   
                  <div class="capitalize"><i class="pi pi-calendar"></i> {{ m.name }}</div>
                  <Button text @click="m.hideDetail = true" class="px-3 py-2" size="small" severity="secondary">
                    <i class="pi pi-minus-circle me-2"></i>Свернуть
                  </Button>
                </th>            
            </template>
          </tr>
          <tr class="hover:bg-gray-50 hover:ring-1 hover:ring-blue-100">
            <template v-for="i in calendar">            
              <th v-if="i.hideDetail" class="text-center font-medium bg-white border-0 border-b border-r border-gray-200">              
                <Tag icon="pi pi-users">
                  Всего в отпуске: {{ i?.total }} ({{ calcTotalPercentEmployeesByMonth(i?.total ?? 0) }}%)
                </Tag>              
              </th>
              <th v-else v-for="d in i.dates" :key="d"
                class="border-0 border-b border-r border-gray-200 text-center text-sm w-16 max-w-16 min-w-16"
                :class="{ 'bg-red-50 text-red-700': d.isWeekend, 'bg-blue-50': d.isToday, 'bg-white text-gray-600': !d.isWeekend && !d.isToday }"
              >              
                {{ d.date }} ({{ d.dayOfWeek }})  
              </th>            
            </template>
          </tr>        
        </thead>

        <tbody>
          <template v-for="department in departments">
            <tr 
              class="text-base font-bold"
              v-if="search.length == 0 || Object.values(department.employees).filter((employee) => employee.name.toLowerCase().includes(search.toLowerCase())).length > 0"
            >
              <td 
                class="py-3 ps-2 sticky top-[6rem] min-w-[25rem] left-0 z-10 max-h-[6rem] min-h-[6rem] h-[6rem] bg-white 
                  border-0 border-b border-r border-gray-200 hover:bg-sky-50"              
              >
                <Button 
                  @click="department.hideDetail = !department.hideDetail" 
                  link 
                  :icon="department.hideDetail ? 'pi pi-angle-down' : 'pi pi-angle-up'"
                  :label="`${department.name} (${department.count_employees})`"
                  style="text-align: left !important;"
                  severity="secondary"
                />              
              </td>
              <template v-for="i in calendar">              
                <td v-if="i.hideDetail" 
                  class="border-0 border-b border-r border-gray-200 sticky top-[6rem] bg-white text-center hover:bg-sky-50"
                >               
                  <Tag v-if="department?.months[i.month]?.total.length" severity="info" icon="pi pi-users" v-tooltip="`Количество сотрудников в отпуске`">
                    {{ department?.months[i.month]?.total.length }}
                  </Tag>
                </td>
                <td v-else v-for="d in i.dates"
                  class="border-0 border-b border-r border-gray-200 sticky top-[6rem] text-center hover:bg-sky-50"
                  :class="{ 'bg-red-50': d.isWeekend, 'bg-blue-50': d.isToday, 'bg-white': !d.isWeekend && !d.isToday }"
                >
                  <Tag v-if="department?.months[i.month]?.days[d.date]?.total" severity="info" icon="pi pi-users" v-tooltip="`Количество сотрудников в отпуске`">
                    {{ department.months[i.month].days[d.date].total.length }}
                  </Tag>
                </td>
              </template>            
            </tr>
            <template v-for="employee in Object.values(department.employees)">
              <Transition name="custom-classes" :duration="200"
                enter-active-class="animate__animated animate__fadeInDown" 
                leave-active-class="animate__animated animate__fadeOutUp"
              >
                <tr v-if="!department.hideDetail && (search.length == 0 || employee.name.toLowerCase().includes(search.toLowerCase()))">
                  <td 
                    class="border-0 border-b border-r border-gray-200 text-base py-3 ps-5 sticky min-w-[25rem] left-0 bg-inherit bg-white hover:bg-sky-50" 
                  >
                    <i class="pi pi-user"></i>
                    {{ employee.name }}
                  </td>
                  <template v-for="i in calendar">
                    <td v-if="i.hideDetail" class="border-0 border-b border-r border-gray-200 text-center hover:bg-sky-50">
                      <Tag v-if="employee.months[i.month]?.total?.length" icon="pi pi-calendar" v-tooltip="`Количество дней`">                      
                        {{ employee.months[i.month].total.length }}
                      </Tag>
                    </td>
                    <td v-else v-for="d in i.dates" 
                      class="border-0 border-b border-r border-gray-200 text-center hover:bg-sky-50"
                      :class="{ 'bg-red-50': d.isWeekend, 'bg-blue-50': d.isToday, 'bg-white': !d.isWeekend && !d.isToday }"
                    >
                      <template v-if="employee?.months[i.month] && employee?.months[i.month]['days'][d.date]">                      
                        <Tag icon="pi pi-sun" v-tooltip="`${d.dateStr} - ${employee.months[i.month]['days'][d.date]['vacation']['name']}`">
                          {{ employee.months[i.month]['days'][d.date]['index'] }}
                        </Tag>
                      </template>                    
                    </td>
                  </template>            
                </tr>
              </Transition>
            </template>
          </template>
        </tbody>          
      </table> 
      <Message v-else severity="warn" class="mx-6 text-center" :closable="false">Нет данных</Message>
    </template> 
  </div>
</template>
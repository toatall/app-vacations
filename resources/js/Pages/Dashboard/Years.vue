<script setup>
import { ref, watch } from 'vue';
import SelectButton from 'primevue/selectbutton';
import axios from 'axios'
import { getCookie, setCookie } from '@/Helpers/cookie.js'
import ProgressSpinner from 'primevue/progressspinner';
import Message from 'primevue/message';

const props = defineProps({  
  orgCode: String,
})

const years = ref();
const year = ref();
const loading = ref(false);

const emit = defineEmits(['change-year']);

watch(
  () => year.value,
  (year) => {
    setCookie(`${props.orgCode}-year`, year);
    emit('change-year', year)
  }
)

const update = (orgCode) => {  
  if (orgCode != undefined) {
      loading.value = true;
      axios.get(`/statistics/years/${orgCode}`)
        .then(({ data }) => {
          years.value = data.map((val) => val.year);
          year.value = getCookie(`${orgCode}-year`);            
          if (year.value == undefined) {           
            const index = years.value.indexOf((new Date().getFullYear()))
            if (index !== -1) {
              year.value = years.value[index]
            }
            else {              
              year.value = years.value[0]
            }            
          }          
        })
        .finally(() => loading.value = false)
    }    
}

update(props.orgCode);

</script>
<template>
  <ProgressSpinner v-if="loading" style="width: 50px; height: 50px" strokeWidth="8" class="fill-surface-0 dark:fill-surface-800"
    animationDuration=".5s" />
  <SelectButton v-else v-model="year" :options="years" :allowEmpty="false" class="text-2xl">      
  </SelectButton>
  <div>   
    <Message v-if="!orgCode" severity="warn" :closable="false">
      Организация не выбрана! <br />
      Выберите организацию в правом верхнем углу!
    </Message>
    <Message v-if="!years || (Array.isArray(years) && years.length == 0)" severity="warn" :closable="false">
      Нет данных!
    </Message>    
  </div>
</template>
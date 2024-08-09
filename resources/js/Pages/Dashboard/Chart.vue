<script setup>
import ApexCharts from "vue3-apexcharts"
import { ref, watch } from 'vue'
import axios from 'axios'
import routes from "@/Config/routes"

let ru = require('apexcharts/dist/locales/ru.json')

const props = defineProps({
  orgCode: String,  
  year: String,
})

const options = ref({
  chart: {
    id: 'vacation-area',
    locales: [ru],
    defaultLocale: 'ru',    
  },  
  xaxis: {
    type: 'datetime',    
  },
  yaxis: {
    type: 'integer',
  },
  stroke: {
    curve: 'smooth',
  },
  dataLabels: {
    enabled: false,

  },
  annotations: {
    xaxis: [
      {
        x: new Date().getTime(),
        borderColor: '#00E396',
        label: {
          borderColor: '#00E396',
          style: {
            color: '#fff',
            background: '#00E396',
            fontWeight: 'bold',
          },
          text: "Сегодня"
        }
      }
    ]
  },
})

const series = ref([])

const realtimeChart = ref(null)

watch(
  () => props.year,
  (year) => {    
    updateChart(year)
  }
)

const updateChart = (year) => {
  axios.get(routes.chartCountOfVacationsPerYearByDay(props.orgCode, year))
    .then(({data: {data}}) => {    
      realtimeChart.value.updateSeries([{
        name: 'Количество человек в отпуске',
        data: data
      }])
    })  
}

</script>
<template>
  <ApexCharts ref="realtimeChart" height="340" type="area" :options="options" :series="series"></ApexCharts>
</template>
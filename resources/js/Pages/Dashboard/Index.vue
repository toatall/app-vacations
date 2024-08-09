<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import CardsNumber from './CardsNumber.vue'
import Chart from './Chart.vue'
import Years from '../../Shared/Years.vue'
import { ref } from 'vue'
import Message from 'primevue/message';


defineOptions({
  layout: Layout,
})

const props = defineProps({
  auth: Object,  
})

const year = ref();

const onChangeYear = (yearSelected) => {
  year.value = yearSelected;
}

const orgCodeSelect = props?.auth?.user?.org_code_select

</script>
<template>
  <div>
    <Head title="Главная" />

    <Message v-if="!auth.user.available_organizations || (Array.isArray(auth.user.available_organizations) && auth.user.available_organizations.length == 0)" severity="warn" :closable="false">
      Список организаций пуст!<br />
      Для дальнейшей работы необходимо добавить организацию. Для добавления перейдите в раздел <Link href="/organizations" class="underline">"Организации"</Link>.
    </Message>

    <div v-else>
      <div class="flex items-center justify-center">
        <Years :orgCode="orgCodeSelect" @change-year="onChangeYear" />            
      </div>       
      <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-3 2xl:gap-7">        
        <CardsNumber :orgCode="orgCodeSelect" :year="year" />
      </div>
      <div class="mt-4 grid grid-cols-1 gap-4 md:mt-6 md:gap-6 2xl:mt-7 2xl:gap-7">
        <div class="rounded-lg border border-stroke bg-white px-5 pb-5 pt-7 shadow-lg sm:px-7 xl:col-span-8 hover:ring hover:ring-blue-200">
          <Chart :orgCode="orgCodeSelect" :year="year"  />
        </div>        
      </div>
    </div>
  </div>
</template>

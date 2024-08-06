<script setup>
import Layout from '@/Shared/Layout.vue'
import { Head } from '@inertiajs/vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import Years from '@/Pages/Dashboard/Years.vue'
import { ref } from 'vue'
import Table from './Table.vue'

defineOptions({
  layout: Layout,
})

const props = defineProps({
  auth: Object,
})

const title = 'Табель отпусков';
const orgCodeSelect = props?.auth?.user?.org_code_select;

const year = ref();

const onChangeYear = (yearSelected) => {
  year.value = yearSelected;
}

</script>
<template>
  
  <Head :title="title" />

  <Breadcrumbs :items="[
    { icon: 'pi-home', label: 'Главная', url: '/' }, 
    { label: title }
  ]" />  

  <div class="flex items-center justify-center mb-3">
    <Years :orgCode="orgCodeSelect" @change-year="onChangeYear" />
  </div>
  
  <Table :orgCode="orgCodeSelect" :year="year" />
  
</template>
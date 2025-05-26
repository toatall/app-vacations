<script setup>
import Breadcrumbs from '@/Shared/Breadcrumbs.vue';
import Layout from '@/Shared/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Message from 'primevue/message';
import { inject, onMounted, ref } from 'vue';

const props = defineProps({
  data: Array,
  attributes: Object,
});

defineOptions({
  layout: Layout,
});

const moment = inject('moment');
const visibleDialog = ref(false);
const dataDialog = ref();

</script>
<template>

  <Head title="Журнал импорта" />

  <Breadcrumbs :items="[
    { icon: 'pi-home', label: 'Главная', url: '/' },
    { label: 'Журнал импорта' }
  ]" />

  <DataTable :value="data" :paginator="true" :rows="10" showGridlines dataKey="id">
    <template #empty> Нет данных </template>
    <Column header="">
      <template #body="{ data }">
        <template v-if="data.error">
          <i class="pi pi-times text-red-600"></i>
        </template>
        <template v-else>
          <i class="pi pi-check-circle text-green-600"></i>
        </template>
      </template>
    </Column>
    <Column field="id" :header="attributes.id" sortable></Column>
    <Column field="org_code" :header="attributes.org_code" sortable></Column>
    <Column field="year" :header="attributes.year" sortable></Column>
    <Column field="source" :header="attributes.source" sortable></Column>
    <Column field="source_description" :header="attributes.source_description" sortable></Column>
    <Column field="error" class="text-red-600" :header="attributes.error" sortable>
      <template #body="{ data }">
        <template v-if="data.error">
          <div>{{ data.error }}</div>                   
          <Button @click="dataDialog = data; visibleDialog = true" severity="danger" icon="pi pi-info-circle" label="Подробнее" size="small" class="mt-2" />
        </template>
      </template>
    </Column>
    <Column field="created_at" :header="attributes.created_at" sortable>
      <template #body="{ data }">
        {{ moment(data.created_at).format('LLL') }}
      </template>
    </Column>
  </DataTable>

  <Dialog v-model:visible="visibleDialog" modal header="Подробнее" style="width: 90%;">
    <template #header>
      <h3 class="font-bold text-xl">Подробнее</h3>
    </template>
    <div>
      <Message severity="error" :closable="false" variant="simple">
        <pre>{{ dataDialog.error_trace }}</pre>
      </Message>
    </div>
  </Dialog>
  
</template>
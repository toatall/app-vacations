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

const dialogRef = inject('dialogRef')
const moment = inject('moment')

const loading = ref()
const errorMessage = ref()
const items = ref()

onMounted(() => {
	loading.value = true
	axios.get(routes.employeesWillBeOnVacations(dialogRef.value.data.code_org, dialogRef.value.data.year))
		.then(({ data }) => {
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
				<div>
					<div class="mb-3">
						{{ moment(data.date_from).format('L') }} - {{ moment(data.date_to).format('L') }}
					</div>
					<div>
						{{ moment(data.date_from).endOf('day').fromNow() }}
					</div>
				</div>
			</template>
		</Column>
	</DataTable>

</template>
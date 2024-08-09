<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import routes from '@/Config/routes';

const page = usePage()

const isUrl = (url) => {
  let currentUrl = page.url;
  return url == currentUrl;  
}

const urlHome = routes.home()
const urlOrganizations = routes.organizations()
const urlTable = routes.table()
const urlFind = routes.find()


</script>
<template>
  <div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" :href="urlHome" :class="isUrl(urlHome) ? 'text-white' : 'text-indigo-300 group-hover:text-white'">
        <i class="pi pi-gauge me-2"></i>
        <div >Главная</div>
      </Link>
    </div>    
    <div class="mb-4">
      <Link class="group flex items-center py-3" :href="urlTable" :class="isUrl(urlTable) ? 'text-white' : 'text-indigo-300 group-hover:text-white'">
        <i class="pi pi-table me-2"></i>
        <div>Табель</div>
      </Link>
    </div> 
    <div class="mb-4">
      <Link class="group flex items-center py-3" :href="urlFind" :class="isUrl(urlFind) ? 'text-white' : 'text-indigo-300 group-hover:text-white'">
        <i class="pi pi-search me-2"></i>
        <div>Поиск</div>
      </Link>
    </div>   

    <div class="mb-4">
      <Link class="group flex items-center py-3" :href="urlOrganizations" :class="isUrl(urlOrganizations) ? 'text-white' : 'text-indigo-300 group-hover:text-white'">
        <i class="pi pi-building-columns me-2"></i>
        <div>Организации</div>
      </Link>
    </div>
  </div>
</template>



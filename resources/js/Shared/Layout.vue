<script setup>
import { Link, router } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Logo from '@/Shared/Logo.vue'
import Dropdown from '@/Shared/Dropdown.vue'
import MainMenu from '@/Shared/MainMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import { computed, defineProps, ref } from 'vue'
import ConfirmDialog from 'primevue/confirmdialog'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import axios from 'axios'

const props = defineProps({
    auth: Object,
})

const menu = ref();

const menuItems = computed(() =>{
  return props.auth?.user?.available_organizations?.map(element => ({
    label: element.code,   
    command: () => {
      changeOrganization(element.code)
    } 
  })) || [];
});

const changeOrganization = (code) => {
    if (code !== props.auth.user.org_code_select) {       
        axios.post(`/site/change-organization`, { code: code })
          .then(() => router.visit(window.location.href))        
    }
}

const items = ref([
    {
        label: 'Организации',
        items: menuItems,        
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

</script>
<template>
  <div>
    <div id="dropdown" />
    <div class="md:flex md:flex-col">
      <div class="md:flex md:flex-col md:h-screen">
        <div class="md:flex md:shrink-0">
          <div class="flex items-center justify-between px-6 py-4 bg-indigo-900 md:shrink-0 md:justify-center md:w-56">
            <Link class="mt-1" href="/">
              <logo class="fill-white" width="120" height="28" />
            </Link>
            <dropdown class="md:hidden" placement="bottom-end">
              <template #default>
                <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              </template>
              <template #dropdown>
                <div class="mt-2 px-8 py-4 bg-indigo-800 rounded shadow-lg">
                  <main-menu />
                </div>
              </template>
            </dropdown>
          </div>          
          <div class="md:text-md flex items-center justify-between p-4 w-full text-sm bg-white border-b md:px-12 md:py-0">
            <div class="mr-4 mt-1">
              <span class="text-xl font-bold">
                График отпусков
              </span>
            </div>
            <div class="flex">             
              <div>
                <Button link severity="secondary" class="me-2" @click="toggle" aria-controls="overlay_menu">
                  <span class="text-nowrap">
                    {{ auth.user.org_code_select ?? 'Не выбрана' }}
                    <i class="pi pi-chevron-down"></i>
                  </span>
                </Button>
                <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
              </div>
              <dropdown class="mt-1" placement="bottom-end">
                <template #default>
                  <div class="group flex items-center cursor-pointer select-none">                  
                    <div class="mr-1 text-gray-700 group-hover:text-indigo-600 focus:text-indigo-600 whitespace-nowrap">
                      <span>{{ auth.user.first_name }}</span>
                      <span class="hidden md:inline">&nbsp;{{ auth.user.full_name ?? auth.user.username }}</span>
                    </div>
                    <icon class="w-5 h-5 fill-gray-700 group-hover:fill-indigo-600 focus:fill-indigo-600" name="cheveron-down" />
                  </div>
                </template>
                <template #dropdown>
                  <div class="mt-2 py-2 text-sm bg-white rounded shadow-xl">
                    <Link class="block px-6 py-2 hover:text-white hover:bg-indigo-500" :href="`/users/${auth.user.id}/edit`">Профиль</Link>
                    <Link class="block px-6 py-2 hover:text-white hover:bg-indigo-500" href="/users">Управление пользователями</Link>
                    <Link class="block px-6 py-2 w-full text-left hover:text-white hover:bg-indigo-500" href="/logout" method="delete" as="button">Выход</Link>
                  </div>
                </template>
              </dropdown>
            </div>
          </div>
        </div>
        <div class="md:flex md:grow md:overflow-hidden">
          <main-menu class="hidden shrink-0 p-12 w-56 bg-indigo-800 overflow-y-auto md:block" />
          <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto" scroll-region>
            <flash-messages />            
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
  <ConfirmDialog></ConfirmDialog>  
</template>

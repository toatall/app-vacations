<script setup>
import { Link, router } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import Dropdown from '@/Shared/Dropdown.vue'
import MainMenu from '@/Shared/MainMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import { computed, defineProps, ref } from 'vue'
import ConfirmDialog from 'primevue/confirmdialog'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import axios from 'axios'
import routes from '@/Config/routes'
import DynamicDialog from 'primevue/dynamicdialog'

const props = defineProps({
  auth: Object,
})

const isAdmin = ref(props.auth.user.roles.indexOf('admin') >= 0);

/** Menu organization start */

const menuOrganizations = ref();

const menuItemsOrganizations = computed(() =>{  
  return props.auth?.user?.available_organizations?.map(element => ({
    label: element.code,   
    command: () => {
      changeOrganization(element.code)
    },
    icon: element.code == props.auth.user.org_code_select ? 'pi pi-check-circle' : 'pi pi-circle',
    class: element.code == props.auth.user.org_code_select ? 'font-bold' : '',
    disabled: element.code == props.auth.user.org_code_select,
  })) || [];
});

const changeOrganization = (code) => {
    if (code !== props.auth.user.org_code_select) {       
      axios.post(`/site/change-organization`, { code: code })
        .then(() => router.visit(window.location.href))        
    }
}

const itemsOrganizations = ref([
    {
      label: 'Организации',
      items: menuItemsOrganizations,        
    }
]);

const toggleOrganizations = (event) => {
    menuOrganizations.value.toggle(event);
};

/** Menu organization end */


/** Menu profile start */

const menuProfile = ref()

const itemsProfile = [
  {
    icon: 'pi pi-user-edit',
    label: 'Профиль',
    command: () => router.get(routes.profile(props.auth.user.id)),    
  },
  {
    icon: 'pi pi-users',
    label: 'Управление пользователями',
    command: () => router.get(routes.manageUsers()),
    visible: isAdmin.value,
  },
  {
    icon: 'pi pi-sign-out',
    label: 'Выход',
    command: () => router.delete(routes.logout()),
  },
]

const toggleProfile = (event) => {
  menuProfile.value.toggle(event);
}

/** Menu profile end */



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
                <Button link severity="secondary" class="me-2" @click="toggleOrganizations" aria-controls="overlay_menu">
                  <span class="text-nowrap">
                    {{ auth.user.org_code_select ?? 'Не выбрана' }}
                    <i class="pi pi-chevron-down"></i>
                  </span>
                </Button>
                <Menu ref="menuOrganizations" id="overlay_menu" :model="itemsOrganizations" :popup="true" />
              </div>
              <div>
                <Button link severity="secondary" class="me-2" @click="toggleProfile" aria-controls="overlay_menu">
                  <span class="text-nowrap">
                    {{ auth.user.full_name ?? auth.user.username }}
                    <i class="pi pi-chevron-down"></i>
                  </span>
                </Button>
                <Menu ref="menuProfile" id="overlay_menu" :model="itemsProfile" :popup="true" />
              </div>             
            </div>
          </div>
        </div>
        <div class="md:flex md:grow md:overflow-hidden">
          <main-menu :isAdmin="isAdmin" class="hidden shrink-0 p-12 w-56 bg-indigo-800 overflow-y-auto md:block" />
          <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto" scroll-region>
            <flash-messages />            
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
  <ConfirmDialog></ConfirmDialog>
  <DynamicDialog />
</template>

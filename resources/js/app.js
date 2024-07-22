import '../css/app.css'
import 'primeicons/primeicons.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Lara from '@/presets/lara'
import Aura from '@/presets/aura'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  title: title => title ? `${title} - Ping CRM` : 'Ping CRM',
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ConfirmationService)
      .use(ToastService)
      .use(PrimeVue, {
        unstyled: true,
        pt: Aura,
        locale: {
          accept: 'OK',
          reject: 'Отмена',           
        }
      })
      .mount(el)
  },
})

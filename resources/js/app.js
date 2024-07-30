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
  title: title => title ? `${title} - График отпусков` : 'График отпусков',
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    
    // app.config.devtools = true
    // app.config.debug = true
    // app.config.silent = false

    app.use(plugin)
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

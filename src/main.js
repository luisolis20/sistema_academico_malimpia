/**
 * Este archivo es el punto de entrada principal de tu aplicación
 * Aquí se define la configuración de Vue y se crea la aplicación
 * Vue se encarga de renderizar el componente de la aplicación
 * y también de manejar los cambios en la aplicación
 */
import { createApp } from 'vue'// Importa la función createApp de Vue
import App from './App.vue'// Importa el componente de la aplicación
import router from './router'// Importa la ruta de la aplicación
import store from './store'// Importa la configuración de la aplicación
import '@fortawesome/fontawesome-free/css/all.min.css'// Importa los iconos de FontAwesome
import 'bootstrap/dist/css/bootstrap.min.css'// Importa los estilos de Bootstrap

import 'bootstrap/dist/js/bootstrap.bundle.min.js'// Importa Bootstrap
createApp(App).use(store).use(router).mount('#app')// Crea la aplicación


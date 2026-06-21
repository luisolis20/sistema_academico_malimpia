<template>
  <!-- Llamada al componente Home -->
  <Home/>
</template>
<script>
/**
 * HomeView es un componente de Vista que se encarga de mostrar el componente Home
 * Se importa Home desde @/components/home.vue
 * Luego la usamos en el template de esta vista
 * Aquí usamos getMe para obtener el token del usuario y la ruta no quede vulnerable a ataques CSRF
 */
import Home from '@/components/home.vue'// Importa el componente Home
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista HomeView
 * Usamos solo name para nombrar la vista, components para definir los componentes que se usan en la vista 
 * y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'HomeView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Home
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   * Este método no recibe parámetros
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


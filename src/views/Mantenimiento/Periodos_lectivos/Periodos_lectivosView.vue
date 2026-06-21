<template>
  <!-- Llamada al componente Periodos_lectivos -->
  <Periodos_lectivos/>
</template>
<script>
/**
 * Periodos_lectivosView es un componente de Vista que se encarga de mostrar el componente Periodos_lectivos
 * Se importa Periodos_lectivos desde @/components/mantenimiento/periodos_lectivos/Periodos_lectivos.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Periodos_lectivos from '@/components/mantenimiento/periodos_lectivos/Periodos_lectivos.vue'// Importa el componente Periodos_lectivos
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista Periodos_lectivosView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'Periodos_lectivosView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Periodos_lectivos
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


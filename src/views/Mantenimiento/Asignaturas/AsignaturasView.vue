<template>
  <!-- Llamada al componente Asignaturas -->
  <Asignaturas/>
</template>
<script>
/**
 * AsignaturasView es un componente de Vista que se encarga de mostrar el componente Asignaturas
 * Se importa Asignaturas desde @/components/mantenimiento/asignaturas/Asignaturas.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Asignaturas from '@/components/mantenimiento/asignaturas/Asignaturas.vue'// Importa el componente Asignaturas
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista AsignaturasView
 * Usamos solo name para nombrar la vista, 
 * components para definir los componentes que se usan en la vista y mounted para definir el método 
 * que se ejecuta al montar la vista
 */
export default {
  name: 'AsignaturasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Asignaturas
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


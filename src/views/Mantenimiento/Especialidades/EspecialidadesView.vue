<template>
  <!-- Llamada al componente Especialidades -->
  <Especialidades/>
</template>
<script>
/**
 * EspecialidadesView es un componente de Vista que se encarga de mostrar el componente Especialidades  
 * Se importa Especialidades desde @/components/mantenimiento/especialidades/Especialidades.vue
 * Luego la usamos en el template de esta vista 
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Especialidades from '@/components/mantenimiento/especialidades/Especialidades.vue'// Importa el componente Especialidades
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista EspecialidadesView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'EspecialidadesView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Especialidades
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


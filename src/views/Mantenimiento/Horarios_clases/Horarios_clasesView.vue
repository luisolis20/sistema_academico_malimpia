<template>
  <!-- Llamada al componente Horarios_clases -->
  <Horarios_clases/>
</template>
<script>
/**
 * Horarios_clasesView es un componente de Vista que se encarga de mostrar el componente Horarios_clases
 * Se importa Horarios_clases desde @/components/mantenimiento/horarios_clases/Horarios_clases.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Horarios_clases from '@/components/mantenimiento/horarios_clases/Horarios_clases.vue'// Importa el componente Horarios_clases
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista Horarios_clasesView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'Horarios_clasesView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Horarios_clases
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


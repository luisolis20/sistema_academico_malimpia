<template>
  <!-- Llamada al componente CursosA -->
  <CursosA/>
</template>
<script>
/**
 * Cursos_AsignaturasView es un componente de Vista que se encarga de mostrar el componente CursosA
 * Se importa CursosA desde @/components/mantenimiento/curso_asignaturas/Cursos_Asignaturas.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import CursosA from '@/components/mantenimiento/curso_asignaturas/Cursos_Asignaturas.vue'// Importa el componente CursosA
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista Cursos_AsignaturasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'Cursos_AsignaturasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    CursosA
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


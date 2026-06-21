<template>
  <!-- Llamada al componente Cronograma_Matriculas -->
  <Cronograma_Matriculas/>
</template>
<script>
/**
 * Cronograma_MatriculasView es un componente de Vista que se encarga de mostrar el componente Cronograma_Matriculas
 * Se importa Cronograma_Matriculas desde @/components/mantenimiento/cronograma_matriculas/Cronograma_matriculas.vue
 * Luego la usamos en el template de esta vista 
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Cronograma_Matriculas from '@/components/mantenimiento/cronograma_matriculas/Cronograma_matriculas.vue'// Importa el componente Cronograma_Matriculas
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista Cronograma_MatriculasView
 * Usamos solo name para nombrar la vista, components para definir los componentes que se usan en la vista 
 * y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'Cronograma_MatriculasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Cronograma_Matriculas
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


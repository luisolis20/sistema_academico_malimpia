<template>
  <!-- Llamada al componente CursosFamiliar -->
  <CursosFamiliar/>
</template>
<script>
/**
 * CursosFamiliarView es un componente de Vista que se encarga de mostrar el componente CursosFamiliar
 * Se importa CursosFamiliar desde @/components/cursos/familiar_cursos/familiarcursos.vue
 * Luego la usamos en el template de esta vista
 */
import CursosFamiliar from '@/components/cursos/familiar_cursos/familiarcursos.vue'// Importa el componente CursosFamiliar
/**
 * Exporta la vista CursosFamiliarView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'CursosFamiliarView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    CursosFamiliar
  },
  
}
</script>


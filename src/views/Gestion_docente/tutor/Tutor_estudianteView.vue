<template>
  <!-- Llamada al componente tutor_estudiante -->
  <tutor_estudiante/>
</template>
<script>
/**
 * Tutor_estudianteView es un componente de Vista que se encarga de mostrar el componente tutor_estudiante
 * Se importa tutor_estudiante desde @/components/gestion_docente/tutor/tutor_estudiante.vue
 * Luego la usamos en el template de esta vista
 */
import tutor_estudiante from '@/components/gestion_docente/tutor/tutor_estudiante.vue'// Importa el componente tutor_estudiante
/**
 * Exporta la vista Tutor_estudianteView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'Tutor_estudianteView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    tutor_estudiante
  },
  
}
</script>


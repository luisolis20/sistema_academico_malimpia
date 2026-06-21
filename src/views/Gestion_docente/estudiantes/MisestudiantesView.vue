<template>
  <!-- Llamada al componente misestudiantes -->
  <misestudiantes/>
</template>
<script>
/**
 * MisestudiantesView es un componente de Vista que se encarga de mostrar el componente misestudiantes
 * Se importa misestudiantes desde @/components/gestion_docente/estudiantes/misestudiantes.vue
 * Luego la usamos en el template de esta vista
 */
import misestudiantes from '@/components/gestion_docente/estudiantes/misestudiantes.vue'// Importa el componente misestudiantes
/**
 * Exporta la vista MisestudiantesView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'MisestudiantesView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    misestudiantes
  },
  
}
</script>


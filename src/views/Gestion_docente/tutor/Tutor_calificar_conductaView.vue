<template>
  <!-- Llamada al componente Calificar_conducta -->
  <Calificar_conducta/>
</template>
<script>
/**
 * Tutor_calificar_conductaView es un componente de Vista que se encarga de mostrar el componente Calificar_conducta
 * Se importa Calificar_conducta desde @/components/gestion_docente/tutor/calificar_conducta.vue
 * Luego la usamos en el template de esta vista
 */
import Calificar_conducta from '@/components/gestion_docente/tutor/calificar_conducta.vue'// Importa el componente Calificar_conducta
/**
 * Exporta la vista Tutor_calificar_conductaView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'Tutor_calificar_conductaView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Calificar_conducta
  },
  
}
</script>


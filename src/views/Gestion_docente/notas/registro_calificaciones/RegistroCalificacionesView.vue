<template>
  <!-- Llamada al componente RegistroCalificaciones -->
  <RegistroCalificaciones/>
</template>
<script>
/**
 * RegistroCalificacionesView es un componente de Vista que se encarga de mostrar el componente RegistroCalificaciones  
 * Se importa RegistroCalificaciones desde @/components/gestion_docente/notas/reistro_calificaciones/subida_notas.vue
 * Luego la usamos en el template de esta vista 
 */
import RegistroCalificaciones from '@/components/gestion_docente/notas/reistro_calificaciones/subida_notas.vue'// Importa el componente RegistroCalificaciones
/**
 * Exporta la vista RegistroCalificacionesView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'RegistroCalificacionesView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    RegistroCalificaciones
  },
  
}
</script>


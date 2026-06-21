<template>
  <!-- Llamada al componente calificaiones_alumnos -->
  <calificaiones_alumnos/>
</template>
<script>
/**
 * Tutor_Notas_estudianteView es un componente de Vista que se encarga de mostrar el componente calificaiones_alumnos
 * Se importa calificaiones_alumnos desde @/components/gestion_docente/tutor/calificaciones_alumnos.vue
 * Luego la usamos en el template de esta vista
 */
import calificaiones_alumnos from '@/components/gestion_docente/tutor/calificaciones_alumnos.vue'// Importa el componente calificaiones_alumnos
/**
 * Exporta la vista Tutor_Notas_estudianteView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'Tutor_Notas_estudianteView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    calificaiones_alumnos
  },
  
}
</script>


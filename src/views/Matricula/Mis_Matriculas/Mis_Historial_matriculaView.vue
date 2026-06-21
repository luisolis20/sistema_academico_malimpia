<template>
  <!-- Llamada al componente Mis_Matriculas -->
  <Mis_Matriculas/>
</template>
<script>
/**
 * Mis_Historial_matriculaView es un componente de Vista que se encarga de mostrar el componente Mis_Matriculas
 * Se importa Mis_Matriculas desde @/components/matricula/mis_matriculas/mis_matriculas.vue
 * Luego la usamos en el template de esta vista
 */
import Mis_Matriculas from '@/components/matricula/mis_matriculas/mis_matriculas.vue'// Importa el componente Mis_Matriculas
/**
 * Exporta la vista Mis_Historial_matriculaView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Mis_Historial_matriculaView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Mis_Matriculas
  },
  
}
</script>


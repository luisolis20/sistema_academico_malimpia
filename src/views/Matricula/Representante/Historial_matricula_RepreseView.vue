<template>
  <!-- Llamada al componente Historial_matricula_Representado -->
  <Historial_matricula_Representado/>
</template>
<script>
/**
 * Historial_matricula_RepreseView es un componente de Vista que se encarga de mostrar el componente Historial_matricula_Representado
 * Se importa Historial_matricula_Representado desde @/components/matricula/representante/historial_matriculas_representado.vue
 * Luego la usamos en el template de esta vista
 */
import Historial_matricula_Representado from '@/components/matricula/representante/historial_matriculas_representado.vue'// Importa el componente Historial_matricula_Representado
/**
 * Exporta la vista Historial_matricula_RepreseView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Historial_matricula_RepreseView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Historial_matricula_Representado
  },
  
}
</script>


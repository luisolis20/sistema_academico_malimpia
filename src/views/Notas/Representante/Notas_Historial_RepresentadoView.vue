<template>
  <!-- Llamada al componente Notas_Historial_Representado -->
  <Notas_Historial_Representado/>
</template>
<script>
/**
 * Notas_Historial_RepresentadoView es un componente de Vista que se encarga de mostrar el componente Notas_Historial_Representado
 * Se importa Notas_Historial_Representado desde @/components/Notas/representante/notas_historico_representado.vue
 * Luego la usamos en el template de esta vista
 */
import Notas_Historial_Representado from '@/components/Notas/representante/notas_historico_representado.vue'// Importa el componente Notas_Historial_Representado
/**
 * Exporta la vista Notas_Historial_RepresentadoView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Notas_Historial_RepresentadoView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Notas_Historial_Representado
  },
  
}

</script>


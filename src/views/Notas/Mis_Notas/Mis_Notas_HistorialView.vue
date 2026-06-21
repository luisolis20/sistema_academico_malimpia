<template>
  <!-- Llamada al componente Mis_Notas_Historial -->
  <Mis_Notas_Historial/>
</template>
<script>
/**
 * Mis_Notas_HistorialView es un componente de Vista que se encarga de mostrar el componente Mis_Notas_Historial
 * Se importa Mis_Notas_Historial desde @/components/Notas/mis_notas/mis_notas_historico.vue
 * Luego la usamos en el template de esta vista
 */
import Mis_Notas_Historial from '@/components/Notas/mis_notas/mis_notas_historico.vue'// Importa el componente Mis_Notas_Historial
/**
 * Exporta la vista Mis_Notas_HistorialView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista 
 */
export default {
  name: 'Mis_Notas_HistorialView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Mis_Notas_Historial
  },
  
}

</script>


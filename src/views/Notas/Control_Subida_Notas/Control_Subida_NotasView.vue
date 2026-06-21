<template>
  <!-- Llamada al componente Controlsubidanotas -->
  <Controlsubidanotas/>
</template>
<script>
/**
 * Control_Subida_NotasView es un componente de Vista que se encarga de mostrar el componente Controlsubidanotas
 * Se importa Controlsubidanotas desde @/components/Notas/control_subida_notas/control_subida_notas.vue
 * Luego la usamos en el template de esta vista
 */
import Controlsubidanotas from '@/components/Notas/control_subida_notas/control_subida_notas.vue'// Importa el componente Controlsubidanotas
/**
 * Exporta la vista Control_Subida_NotasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Control_Subida_NotasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Controlsubidanotas
  },
  
}
</script>


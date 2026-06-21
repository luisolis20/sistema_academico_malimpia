<template>
  <!-- Llamada al componente mis_notas -->
  <mis_notas/>
</template>
<script>
/**
 * Mis_NotasView es un componente de Vista que se encarga de mostrar el componente mis_notas
 * Se importa mis_notas desde @/components/Notas/mis_notas/mis_notas.vue
 * Luego la usamos en el template de esta vista
 */
import mis_notas from '@/components/Notas/mis_notas/mis_notas.vue'// Importa el componente mis_notas
/**
 * Exporta la vista Mis_NotasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Mis_NotasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    mis_notas
  },
  
}
</script>


<template>
  <!-- Llamada al componente matricula -->
  <matricula/>
</template>
<script>
/**
 * PanelmatriculaView es un componente de Vista que se encarga de mostrar el componente matricula
 * Se importa matricula desde @/components/matricula/matricula.vue
 * Luego la usamos en el template de esta vista
 */
import matricula from '@/components/matricula/matricula.vue'// Importa el componente matricula
/**
 * Exporta la vista PanelmatriculaView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'PanelmatriculaView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    matricula
  },
  
}
</script>


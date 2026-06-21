<template>
  <!-- Llamada al componente historial_matricula -->
  <historial_matricula/>
</template>
<script>
/**
 * Historial_matriculaView es un componente de Vista que se encarga de mostrar el componente historial_matricula
 * Se importa historial_matricula desde @/components/matricula/lista_matricula/lista_matricula.vue
 * Luego la usamos en el template de esta vista
 */
import historial_matricula from '@/components/matricula/lista_matricula/lista_matricula.vue'// Importa el componente historial_matricula
/**
 * Exporta la vista Historial_matriculaView 
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'Historial_matriculaView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    historial_matricula
  },
  
}
</script>


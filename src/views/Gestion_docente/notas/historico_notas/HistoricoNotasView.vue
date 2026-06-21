<template>
  <!-- Llamada al componente HistoricoNotas -->
  <HistoricoNotas/>
</template>
<script>
/**
 * HistoricoNotasView es un componente de Vista que se encarga de mostrar el componente HistoricoNotas
 * Se importa HistoricoNotas desde @/components/Notas/historico_notas/lista_notas.vue
 * Luego la usamos en el template de esta vista
 */
import HistoricoNotas from '@/components/Notas/historico_notas/lista_notas.vue'// Importa el componente HistoricoNotas
/**
 * Exporta la vista HistoricoNotasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */

export default {
  name: 'HistoricoNotasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    HistoricoNotas
  },
  
}
</script>


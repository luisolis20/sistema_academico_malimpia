<template>
  <!-- Llamada al componente Personas -->
  <Personas/>
</template>
<script>
/**
 * PersonasView es un componente de Vista que se encarga de mostrar el componente Personas
 * Se importa Personas desde @/components/mantenimiento/personas/Personas.vue
 * Luego la usamos en el template de esta vista
 */
import Personas from '@/components/mantenimiento/personas/Personas.vue'// Importa el componente Personas
/**
 * Exporta la vista PersonasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'PersonasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Personas
  },
}
</script>


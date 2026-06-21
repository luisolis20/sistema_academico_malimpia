<template>
  <!-- Llamada al componente Perfil -->
  <Perfil/>
</template>
<script>
/**
 * PerfilView es un componente de Vista que se encarga de mostrar el componente Perfil
 * Se importa Perfil desde @/components/perfil/perfil.vue
 * Luego la usamos en el template de esta vista
 */
import Perfil from '@/components/perfil/perfil.vue'// Importa el componente Perfil
/**
 * Exporta la vista PerfilView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'PerfilView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Perfil
  },
  
}
</script>


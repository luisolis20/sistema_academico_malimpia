<template>
  <!-- Llamada al componente Login -->
    <Login />
</template>

<script>
/**
 * LoginView es un componente de Vista que se encarga de mostrar el componente Login
 * Se importa Login desde @/components/Login.vue
 * Luego la usamos en el template de esta vista
 */
import Login from '@/components/Login.vue'// Importa el componente Login
/**
 * Exporta la vista LoginView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'LoginView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Login
  }
}
</script>

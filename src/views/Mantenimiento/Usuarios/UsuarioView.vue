<template>
  <!-- Llamada al componente Usuarios --> 
  <Usuarios/>
</template>
<script>
/**
 * UsuariosView es un componente de Vista que se encarga de mostrar el componente Usuarios
 * Se importa Usuarios desde @/components/mantenimiento/usuarios/Usuarios.vue
 * Luego la usamos en el template de esta vista
 */
import Usuarios from '@/components/mantenimiento/usuarios/Usuarios.vue'// Importa el componente Usuarios
/**
 * Exporta la vista UsuariosView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan en la vista
 */
export default {
  name: 'UsuariosView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Usuarios
  },
  
}
</script>


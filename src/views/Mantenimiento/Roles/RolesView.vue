<template>
  <!-- Llamada al componente Roles -->
  <Roles/>
</template>
<script>
/**
 * RolesView es un componente de Vista que se encarga de mostrar el componente Roles
 * Se importa Roles desde @/components/mantenimiento/roles/Roles.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Roles from '@/components/mantenimiento/roles/Roles.vue'// Importa el componente Roles
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista RolesView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'RolesView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Roles
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


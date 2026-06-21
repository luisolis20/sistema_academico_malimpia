<template>
  <!-- Llamada al componente Familias -->
  <Familias/>
</template>
<script>
/**
 * FamiliasView es un componente de Vista que se encarga de mostrar el componente Familias
 * Se importa Familias desde @/components/mantenimiento/familias/Familias.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Familias from '@/components/mantenimiento/familias/Familias.vue'// Importa el componente Familias
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista FamiliasView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'FamiliasView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Familias
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


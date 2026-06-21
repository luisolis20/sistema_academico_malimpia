<template>
  <!-- Llamada al componente Niveles_Academicos -->
  <Niveles_Academicos/>
</template>
<script>
/**
 * Nivel_AcademicoView es un componente de Vista que se encarga de mostrar el componente Niveles_Academicos
 * Se importa Niveles_Academicos desde @/components/mantenimiento/niveles_academicos/Niveles_academicos.vue
 * Luego la usamos en el template de esta vista
 * Se importa getMe desde @/assets/js/auth para obtener el token del usuario
 */
import Niveles_Academicos from '@/components/mantenimiento/niveles_academicos/Niveles_academicos.vue'// Importa el componente Niveles_Academicos
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta la vista Nivel_AcademicoView
 * Usamos solo name para nombrar la vista y components para definir los componentes que se usan 
 * en la vista y mounted para definir el método que se ejecuta al montar la vista
 */
export default {
  name: 'Nivel_AcademicoView',// Nombre de la vista
  /**
   * Define los componentes que se usan en la vista
   */
  components: {
    Niveles_Academicos
  },
  /**
   * Mounted se ejecuta al montar la vista, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    await getMe();// Llamada a la API para obtener el token del usuario
  }
}
</script>


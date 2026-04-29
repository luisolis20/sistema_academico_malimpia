<template>
  <Niveles_Academicos/>
</template>
<script>
// @ is an alias to /src
import Niveles_Academicos from '@/components/mantenimiento/niveles_academicos/Niveles_academicos.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'Nivel_AcademicoView',
  components: {
    Niveles_Academicos
  },
  async mounted() {
    await getMe();
  }
}
</script>


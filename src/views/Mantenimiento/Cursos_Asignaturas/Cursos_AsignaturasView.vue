<template>
  <CursosA/>
</template>
<script>
// @ is an alias to /src
import CursosA from '@/components/mantenimiento/curso_asignaturas/Cursos_Asignaturas.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'Cursos_AsignaturasView',
  components: {
    CursosA
  },
  async mounted() {
    await getMe();
  }
}
</script>


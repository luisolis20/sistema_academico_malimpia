<template>
  <Cronograma_Matriculas/>
</template>
<script>
// @ is an alias to /src
import Cronograma_Matriculas from '@/components/mantenimiento/cronograma_matriculas/Cronograma_matriculas.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'Cronograma_MatriculasView',
  components: {
    Cronograma_Matriculas
  },
  async mounted() {
    await getMe();
  }
}
</script>


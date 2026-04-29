<template>
  <Cursos/>
</template>
<script>
// @ is an alias to /src
import Cursos from '@/components/mantenimiento/cursos/Cursos.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'CursosView',
  components: {
    Cursos
  },
  async mounted() {
    await getMe();
  }
}
</script>


<template>
  <Especialidades/>
</template>
<script>
// @ is an alias to /src
import Especialidades from '@/components/mantenimiento/especialidades/Especialidades.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'EspecialidadesView',
  components: {
    Especialidades
  },
  async mounted() {
    await getMe();
  }
}
</script>


<template>
  <Asignaturas/>
</template>
<script>
// @ is an alias to /src
import Asignaturas from '@/components/mantenimiento/asignaturas/Asignaturas.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'AsignaturasView',
  components: {
    Asignaturas
  },
  async mounted() {
    await getMe();
  }
}
</script>


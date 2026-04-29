<template>
  <Periodos_lectivos/>
</template>
<script>
// @ is an alias to /src
import Periodos_lectivos from '@/components/mantenimiento/periodos_lectivos/Periodos_lectivos.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'Periodos_lectivosView',
  components: {
    Periodos_lectivos
  },
  async mounted() {
    await getMe();
  }
}
</script>


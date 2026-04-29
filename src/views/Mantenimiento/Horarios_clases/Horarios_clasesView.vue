<template>
  <Horarios_clases/>
</template>
<script>
// @ is an alias to /src
import Horarios_clases from '@/components/mantenimiento/horarios_clases/Horarios_clases.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'Horarios_clasesView',
  components: {
    Horarios_clases
  },
  async mounted() {
    await getMe();
  }
}
</script>


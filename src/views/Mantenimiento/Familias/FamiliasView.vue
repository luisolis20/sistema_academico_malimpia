<template>
  <Familias/>
</template>
<script>
// @ is an alias to /src
import Familias from '@/components/mantenimiento/familias/Familias.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'FamiliasView',
  components: {
    Familias
  },
  async mounted() {
    await getMe();
  }
}
</script>


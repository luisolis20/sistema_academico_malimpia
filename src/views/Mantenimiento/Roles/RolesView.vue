<template>
  <Roles/>
</template>
<script>
// @ is an alias to /src
import Roles from '@/components/mantenimiento/roles/Roles.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'RolesView',
  components: {
    Roles
  },
  async mounted() {
    await getMe();
  }
}
</script>


<template>
  <Home/>
</template>
<script>
// @ is an alias to /src
import Home from '@/components/home.vue'
import { getMe } from "@/assets/js/auth";
export default {
  name: 'HomeView',
  components: {
    Home
  },
  async mounted() {
    await getMe();
  }
}
</script>


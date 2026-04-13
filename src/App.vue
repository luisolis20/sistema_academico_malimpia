<template>
  <nav class="navbar navbar-expand-lg navbar-dark  bg-success" v-if="$route.path !== '/login'">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Gestion Academica</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" @click="cerrarSesion"  href="#">Cerrar Sesión</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Estudiantes
            </a>
            
            <ul class="dropdown-menu">
              <li class="nav-item">
                <router-link class="nav-link" to="/principal">Inicio</router-link>
              </li>
              <router-link class="dropdown-item" to="/estudiantes">Lista de
                Estudiantes</router-link>
              <router-link class="dropdown-item" to="/createE">Registrar Estudiante</router-link>
              <router-link class="dropdown-item" to="/editE/:id">Editar Estudiante </router-link>
              <router-link class="dropdown-item" to="/viewE/:id">Ver Detalles</router-link>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Mantenimiento
            </a>
            
            <ul class="dropdown-menu">
              <li class="nav-item">
                <router-link class="nav-link" to="/roles">Roles</router-link>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container-fluid">
    <router-view />
  </div>
</template>
<script>
import script2 from '@/store/custom.js';
import API from "@/assets/js/axios"
export default {
  mixins: [script2],
  data() {
    return {
    }
  },
  methods: {
    async cerrarSesion() {
      const token = localStorage.getItem("token_sitma");

        if (!token) {
          console.warn("⚠️ No hay token, cerrando sesión localmente...");
          localStorage.clear();
          window.location.href = "/login";
          return;
        }

        const response = await API.get(
          "/sistma/logout",
          {},
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
      this.$router.push("/login");
    }

  }
}
</script>

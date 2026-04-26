<template>
  <nav class="navbar navbar-expand-lg custom-navbar shadow" v-if="$route.path !== '/login'">
    <div class="container-fluid px-3">

      <router-link class="navbar-brand d-flex align-items-center" to="/principal" @click="closeMenu">
        <img src="@/assets/img/mile.png" alt="Logo Malimpia" width="45" height="45"
          class="d-inline-block align-text-top me-2 bg-white rounded-circle p-1 shadow-sm logo-img">
        <div class="d-flex flex-column">
          <span class="fw-bold brand-text lh-1">U.E. "MALIMPIA"</span>
          <span class="brand-subtext lh-1 mt-1">Gestión Académica</span>
        </div>
      </router-link>

      <button class="navbar-toggler custom-toggler" type="button" @click="isMenuOpen = !isMenuOpen"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" :class="{ 'show': isMenuOpen }" id="navbarNavDropdown">

        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <li class="nav-item">
            <router-link class="nav-link" active-class="active" to="/principal" @click="closeMenu">
              <i class="fas fa-home me-1"></i> Inicio
            </router-link>
          </li>
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('mantenimiento')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('mantenimiento')">
              <i class="fas fa-wrench me-1"></i> Mantenimiento
            </a>
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'mantenimiento' }">
              <li>
                <router-link class="dropdown-item" to="/roles" @click="closeMenu">
                  <i class="fas fa-shield-alt me-2 text-muted"></i> Roles
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/personas" @click="closeMenu">
                  <i class="fas fa-user-friends me-2 text-muted"></i> Personas
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/panel-usuario" @click="closeMenu">
                  <i class="fas fa-user-circle me-2 text-muted"></i> Usuarios
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/familias" @click="closeMenu">
                  <i class="fas fa-users me-2 text-muted"></i> Familias
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/periodos-lectivos" @click="closeMenu">
                  <i class="fas fa-calendar-alt me-2 text-muted"></i> Periodos Lectivos
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/niveles-academicos" @click="closeMenu">
                  <i class="fas fa-graduation-cap me-2 text-muted"></i> Niveles Académicos
                </router-link>
              </li>
            </ul>
          </li>

          <li class="nav-item dropdown" @mouseenter="hoverDropdown('estudiantes')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('estudiantes')">
              <i class="bi bi-people-fill me-1"></i> Estudiantes
            </a>
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'estudiantes' }">
              <li>
                <router-link class="dropdown-item" to="/estudiantes" @click="closeMenu">
                  <i class="bi bi-list-ul me-2 text-muted"></i> Lista de Estudiantes
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/createE" @click="closeMenu">
                  <i class="bi bi-person-plus-fill me-2 text-muted"></i> Registrar Estudiante
                </router-link>
              </li>
            </ul>
          </li>


        </ul>

        <ul
          class="navbar-nav align-items-center mt-3 mt-lg-0 pb-3 pb-lg-0 border-top border-lg-0 pt-2 pt-lg-0 border-secondary custom-mobile-border">
          <li class="nav-item dropdown user-dropdown w-100 text-center text-lg-start"
            @mouseenter="hoverDropdown('usuario')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center justify-content-lg-start px-0 px-lg-3"
              href="#" role="button" @click.prevent="toggleDropdown('usuario')">
              <img src="https://ui-avatars.com/api/?name=Usuario&background=F4B324&color=1D2A68&bold=true" alt="Avatar"
                width="38" height="38" class="rounded-circle me-2 border border-2 border-warning shadow-sm">
              <span class="fw-semibold text-white">Mi Perfil</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 custom-dropdown mt-2 w-100"
              :class="{ 'show': activeDropdown === 'usuario' }">
              <li class="px-3 py-2 text-center border-bottom mb-1 bg-light">
                <span class="d-block fw-bold text-dark">Nombre del Usuario</span>
                <span class="d-block text-muted small">Administrador</span>
              </li>
              <li>
                <router-link class="dropdown-item py-2" to="/perfil" @click="closeMenu">
                  <i class="bi bi-person-circle me-2 text-primary"></i> Ver mi perfil
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider my-1">
              </li>
              <li>
                <a class="dropdown-item py-2 text-danger fw-bold hover-danger" @click.prevent="cerrarSesion" href="#">
                  <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </a>
              </li>
            </ul>
          </li>
        </ul>

      </div>
    </div>
  </nav>

  <div class="container-fluid mt-4">
    <router-view />
  </div>
</template>

<script>
import script2 from '@/store/custom.js';
import API from "@/assets/js/axios";

export default {
  mixins: [script2],
  data() {
    return {
      isMenuOpen: false,
      activeDropdown: null,
    };
  },
  methods: {
    // ABRE el menú si pasamos el mouse (Solo en pantallas grandes)
    hoverDropdown(menuName) {
      if (window.innerWidth >= 992) {
        this.activeDropdown = menuName;
      }
    },
    // CIERRA el menú si quitamos el mouse (Solo en pantallas grandes)
    leaveDropdown() {
      if (window.innerWidth >= 992) {
        this.activeDropdown = null;
      }
    },
    toggleDropdown(menuName) {
      if (this.activeDropdown === menuName) {
        this.activeDropdown = null;
      } else {
        this.activeDropdown = menuName;
      }
    },
    closeMenu() {
      this.isMenuOpen = false;
      this.activeDropdown = null;
    },
    async cerrarSesion() {
      this.closeMenu();
      const token = localStorage.getItem("token_sitma");

      if (!token) {
        localStorage.clear();
        window.location.href = "/login";
        return;
      }

      try {
        await API.get("/sistma/logout", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      } catch (error) {
        console.error("Error al cerrar sesión", error);
      } finally {
        localStorage.clear();
        this.$router.push("/login");
      }
    }
  }
}
</script>

<style scoped>
/* =========================================
   PALETA DE COLORES BASADA EN EL LOGO
   ========================================= */
:root {
  --blue-malimpia: #1D2A68;
  --gold-malimpia: #F4B324;
}

.custom-navbar {
  background-color: #1D2A68;
  padding-top: 0.8rem;
  padding-bottom: 0.8rem;
}

.brand-text {
  color: #F4B324;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

.brand-subtext {
  color: #ffffff;
  font-size: 0.75rem;
  opacity: 0.8;
}

.logo-img {
  transition: transform 0.3s ease;
}

.navbar-brand:hover .logo-img {
  transform: scale(1.05);
}

.navbar-nav .nav-link {
  color: rgba(255, 255, 255, 0.8) !important;
  font-weight: 500;
  padding: 0.5rem 1rem;
  margin: 0 0.2rem;
  transition: all 0.3s ease;
  position: relative;
}

.navbar-nav .nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  background-color: #F4B324;
  transition: all 0.3s ease;
  transform: translateX(-50%);
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
  color: #ffffff !important;
}

.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
  width: 80%;
}

.user-dropdown .nav-link::after {
  display: none;
}

.custom-toggler {
  border-color: rgba(244, 179, 36, 0.5);
}

.custom-toggler:focus {
  box-shadow: 0 0 0 0.25rem rgba(244, 179, 36, 0.25);
}

@media (max-width: 991px) {
  .custom-mobile-border {
    border-color: rgba(255, 255, 255, 0.1) !important;
  }

  .custom-dropdown {
    background-color: rgba(255, 255, 255, 0.05) !important;
  }

  .custom-dropdown .dropdown-item {
    color: #fff !important;
  }

  .custom-dropdown .dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #F4B324 !important;
  }

  .bg-light.border-bottom {
    background-color: transparent !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }

  .bg-light .text-dark {
    color: #F4B324 !important;
  }

  .bg-light .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
  }
}

.custom-dropdown {
  border-radius: 8px;
  overflow: hidden;
  animation: fadeIn 0.2s ease;
}

.custom-dropdown .dropdown-item {
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

@media (min-width: 992px) {

  /* Pegamos el dropdown ligeramente al menú principal para que no se cierre al mover el mouse en el espacio vacío */
  .custom-dropdown {
    margin-top: 0 !important;
  }

  .custom-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #1D2A68;
    transform: translateX(5px);
  }
}

.hover-danger:hover {
  background-color: #fee2e2 !important;
  color: #dc3545 !important;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
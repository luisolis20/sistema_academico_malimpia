<template>
  <!-- Contenedor principal del componente -->
  <div class="container-fluid d-flex vh-100 align-items-center justify-content-center m-0 p-0 login-bg">
    <!-- Contenedor del componente -->
    <div class="card shadow-lg border-0" style="max-width: 400px; width: 100%;" data-aos="zoom-in">
      <!-- Cabecera del componente, contiene el logo y el título -->
      <div class="card-header border-0 text-center py-5 custom-header">
        <div class="logo-wrapper mb-3">
          <img src="@/assets/img/mile.png" alt="milenio" class="img-fluid rounded-circle shadow logo-img">
        </div>
        <h3 class="mb-1 fw-bold text-white">Sistema Académico</h3>
        <p class="text-gold mb-0 small fw-bold">U.E. "MALIMPIA"</p>
      </div>
      <!-- Cuerpo del componente, contiene el formulario de login --> 
      <div class="card-body p-4 pt-5">
        <!-- Formulario de login -->
        <form @submit.prevent="login">
          <!-- Contenedor del campo de usuario -->
          <div class="mb-3">
            <!-- Etiqueta del campo de usuario -->
            <label for="correo" class="form-label small fw-bold text-muted">USUARIO</label>
            <!-- Contenedor del input del campo de usuario, se contiene para agrupar el icono y el input -->
            <div class="input-group shadow-sm">
              <!-- Icono del input del campo de usuario -->
              <span class="input-group-text custom-icon-box"><i class="fas fa-user-circle"></i></span>
              <!-- Input del campo de usuario -->
              <input type="text" id="correo" v-model="correolo" class="form-control custom-input"
                placeholder="ejemplo@correo.com" required />
            </div>
          </div>
          <!-- Contenedor del campo de contraseña -->
          <div class="mb-4">
            <!-- Etiqueta del campo de contraseña -->
            <label for="contrasena" class="form-label small fw-bold text-muted">CONTRASEÑA</label>
            <!-- Contenedor del input del campo de contraseña, se contiene para agrupar el icono, el input y el botón para ver/ocultar la contraseña -->
            <div class="input-group shadow-sm position-relative">
              <!-- Icono del input del campo de contraseña -->
              <span class="input-group-text custom-icon-box"><i class="fas fa-lock"></i></span>
                <!-- Input del campo de contraseña -->  
              <input :type="showPassword ? 'text' : 'password'" id="contrasena" v-model="clave2"
                class="form-control custom-input pe-5" placeholder="********" required />
              <!-- Botón para ver/ocultar la contraseña -->
              <button type="button"
                class="btn position-absolute end-0 top-50 translate-middle-y border-0 z-index-master pe-3"
                @click="togglePassword" style="z-index: 10;">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-muted"></i>
              </button>
            </div>
          </div>
          <!-- Contenedor del botón de login -->
          <div class="d-grid pt-2">
            <!-- Botón de login, si inicia sesión se ihnabilita el botón -->
            <button type="submit" class="btn btn-gold btn-lg shadow-sm text-blue fw-bold" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'VERIFICANDO...' : 'INGRESAR' }}
            </button>
          </div>
          <!-- Contenedor del mensaje de error -->
          <div v-if="error" class="alert alert-danger mt-3 text-center py-2 border-0 shadow-sm" role="alert">
            <small class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ error }}</small>
          </div>
        </form>
      </div>
      <!-- Pie de página del componente -->
      <div class="card-footer bg-white border-0 text-center pb-4">
        <a href="#" class="text-decoration-none small forgot-link fw-bold">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Login es un commponente en donde se encuentra toda la lógica de la aplicación
 * Importamos script3 desde @/assets/js/login.js para usar metodos y variables globales
 * Importamos AOS desde 'aos' para usar animaciones
 */
import script3 from "@/assets/js/login.js";// Importa la función de login
import AOS from 'aos';// Importa la función de animación
/**
 * Exporta el componente Login
 * Usamos solo name para nombrar el componente
 * Usamos mixins para extender la funcionalidad de script3
 * Usamos data para definir las propiedades y variables que se usarán en el componente
 * Usamos methods para definir los métodos que se usarán en el componente
 * Usamos mounted para definir el método que se ejecuta al montar el componente
 */
export default {
  name: 'Login',// Nombra el componente
  mixins: [script3],// Extende la funcionalidad de script3
  /**
   * Definición de las propiedades, se definen las variables que se usarán en el componente
   */
  data() {
    /**
     * Retorna las propiedades, aquí se definen las variables que se usarán en el componente
     */
    return {
      // Estado para mostrar u ocultar contraseña
      showPassword: false,
    }
  },
  /**
   * Métodos que se ejecutan en el componente, se definen aquí para que se puedan llamar desde el template
   */
  methods: {
    /**
     * Método que se ejecuta cuando se hace click en el botón para mostrar/ocultar la contraseña
     * Este método no recibe parámetros
     */
    togglePassword() {
      this.showPassword = !this.showPassword;// Se cambia el estado de la variable showPassword
    }
  },
  /**
   * Mounted se ejecuta al montar el componente, se usa async y await para hacer llamadas a la API
   * Este método no recibe parámetros
   */
  mounted() {
    /**
     * El método $nextTick se ejecuta cuando el componente esté listo para ser renderizado
     * E inicializa la animación con AOS
     */
    this.$nextTick(() => {
      AOS.init({
        once: true, // Evita que se repita la animación innecesariamente
        duration: 800///Duración de la animación en milisegundos
      });
    });
  }
}
</script>

<style scoped>
/* Colores Institucionales */
.text-gold {
  color: #F4B324 !important;
}

.text-blue {
  color: #1D2A68 !important;
}

/* Cabecera Azul con Borde Oro */
.custom-header {
  background-color: #1D2A68;
  border-bottom: 5px solid #F4B324 !important;
  position: relative;
}

.logo-wrapper {
  margin-top: -20px;
}

.logo-img {
  max-height: 110px;
  width: 110px;
  object-fit: cover;
  border: 4px solid #F4B324;
  background-color: white;
  transition: transform 0.5s ease;
}

.card:hover .logo-img {
  transform: rotate(5deg) scale(1.05);
}

.card {
  border-radius: 25px;
  overflow: hidden;
  background-color: #ffffff;
  z-index: 1;
}

/* Iconos y Inputs */
.custom-icon-box {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-right: none;
  color: #1D2A68;
}

.custom-input {
  border: 1px solid #dee2e6;
  border-left: none;
}

.custom-input:focus {
  border-color: #dee2e6;
  box-shadow: none;
}

/* Botón Oro */
.btn-gold {
  background-color: #F4B324;
  border: none;
  color: #1D2A68;
  transition: all 0.3s ease;
}

.btn-gold:hover:not(:disabled) {
  background-color: #e0a31f;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(244, 179, 36, 0.4) !important;
}

.btn-gold:disabled {
  background-color: #d4af37;
  opacity: 0.7;
}

.forgot-link {
  color: #1D2A68;
  opacity: 0.7;
  transition: opacity 0.3s;
}

.forgot-link:hover {
  opacity: 1;
  color: #F4B324;
}
</style>
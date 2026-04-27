<template>
  <div class="login-container d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0" style="max-width: 400px; width: 100%;" data-aos="zoom-in">
      
      <div class="card-header border-0 text-center py-5 custom-header">
        <div class="logo-wrapper mb-3">
          <img src="@/assets/img/mile.png" alt="milenio" class="img-fluid rounded-circle shadow logo-img">
        </div>
        <h3 class="mb-1 fw-bold text-white">Sistema Académico</h3>
        <p class="text-gold mb-0 small fw-bold">U.E. "MALIMPIA"</p>
      </div>

      <div class="card-body p-4 pt-5">
        <form @submit.prevent="login">
          <div class="mb-3">
            <label for="correo" class="form-label small fw-bold text-muted">USUARIO</label>
            <div class="input-group shadow-sm">
              <span class="input-group-text custom-icon-box"><i class="fas fa-user-circle"></i></span>
              <input 
                type="text" 
                id="correo"
                v-model="correolo" 
                class="form-control custom-input" 
                placeholder="ejemplo@correo.com"
                required
              />
            </div>
          </div>

          <div class="mb-4">
            <label for="contrasena" class="form-label small fw-bold text-muted">CONTRASEÑA</label>
            <div class="input-group shadow-sm">
              <span class="input-group-text custom-icon-box"><i class="fas fa-lock"></i></span>
              <input 
                type="password" 
                id="contrasena"
                v-model="clave2" 
                class="form-control custom-input" 
                placeholder="********"
                required
              />
            </div>
          </div>

          <div class="d-grid pt-2">
            <button type="submit" class="btn btn-gold btn-lg shadow-sm text-blue fw-bold" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'VERIFICANDO...' : 'INGRESAR' }}
            </button>
          </div>

          <div v-if="error" class="alert alert-danger mt-3 text-center py-2 border-0 shadow-sm" role="alert">
            <small class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ error }}</small>
          </div>
        </form>
      </div>

      <div class="card-footer bg-white border-0 text-center pb-4">
        <a href="#" class="text-decoration-none small forgot-link fw-bold">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  </div>
</template>

<script>
import script3 from "@/assets/js/login.js";
import AOS from 'aos';

export default {
  name: 'Login',
  mixins: [script3],
  mounted() {
    AOS.init();
  }
}
</script>

<style scoped>
/* Colores Institucionales */
.text-gold { color: #F4B324 !important; }
.text-blue { color: #1D2A68 !important; }

/* Fondo con degradado institucional */
.login-container {
  /* Asegura que ocupe todo el ancho y alto sin dejar huecos */
  width: 100vw; 
  min-height: 100vh;
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #1D2A68 0%, #151e4b 100%);
  /* Evita el scroll horizontal innecesario */
  overflow-x: hidden; 
}

/* Decoración de fondo opcional (puntos o formas) */
.login-container::before {
  content: "";
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
  opacity: 0.1;
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
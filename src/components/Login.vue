<template>
  <div class="login-container d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0" style="max-width: 400px; width: 100%;">
      <div class="card-header border-0 text-center py-4 custom-header">
        <img src="@/assets/img/mile.jpg" alt="milenio" class="img-fluid mb-3 rounded-circle shadow-sm" style="max-height: 120px; width: 120px; object-fit: cover;">
        <h3 class="mb-0 fw-bold text-dark">Sistema Académico</h3>
        <small class="text-secondary">Bienvenido, por favor identifícate</small>
      </div>

      <div class="card-body p-4">
        <form @submit.prevent="login">
          <div class="mb-3">
            <label for="correo" class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
            <div class="input-group">
              <span class="input-group-text custom-icon-box"><i class="bi bi-envelope-fill"></i></span>
              <input 
                type="email" 
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
            <div class="input-group">
              <span class="input-group-text custom-icon-box"><i class="bi bi-lock-fill"></i></span>
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

          <div class="d-grid">
            <button type="submit" class="btn btn-purple btn-lg shadow-sm" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Iniciando sesión...' : 'Ingresar' }}
            </button>
          </div>

          <div v-if="error" class="alert alert-danger mt-3 text-center py-2" role="alert">
            <small>{{ error }}</small>
          </div>
        </form>
      </div>

      <div class="card-footer bg-white border-0 text-center pb-4">
        <a href="#" class="text-decoration-none small forgot-link">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  </div>
</template>

<script>
import script3 from "@/assets/js/login.js";
export default {
  name: 'Login',
  mixins: [script3],
  data() {
    return {
    }
  },
  methods: {
    
    
  }
}
</script>

<style scoped>
/* Fondo general Blanco */
.login-container {
  min-height: 100vh;
  background-color: #ffffff;
}

/* Cabecera Turquesa Claro */
.custom-header {
  background-color: #e0f7f9; /* Turquesa muy suave */
  border-bottom: 2px solid #b2ebf2;
}

.card {
  border-radius: 20px;
  overflow: hidden;
  background-color: #fff;
}

/* Iconos en Turquesa */
.custom-icon-box {
  background-color: #e0f7f9;
  border: 1px solid #b2ebf2;
  color: #00acc1; /* Turquesa oscuro para el icono */
}

.custom-input {
  border: 1px solid #b2ebf2;
}

.custom-input:focus {
  border-color: #4dd0e1;
  box-shadow: 0 0 0 0.25rem rgba(77, 208, 225, 0.25);
}

/* Botón Morado */
.btn-purple {
  background-color: #6f42c1;
  border-color: #6f42c1;
  color: white;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-purple:hover {
  background-color: #59359a;
  border-color: #51308c;
  transform: translateY(-2px);
  color: white;
}

.btn-purple:active {
  transform: translateY(0);
}

.forgot-link {
  color: #6f42c1;
}

.forgot-link:hover {
  color: #00acc1;
}
</style>
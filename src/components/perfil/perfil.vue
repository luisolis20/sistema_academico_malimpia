<template>
  <div class="container mt-5 pb-5">
    <div class="card shadow-lg border-0 overflow-hidden mb-4 rounded-4">
      <div class="profile-header-bg"></div>
      <div class="card-body pt-0 px-4">
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end profile-content">
          <div class="profile-avatar-container shadow-sm mb-3 mb-md-0">
            <img :src="getPhotoUrl(Persona.id_persona)" class="profile-avatar" alt="Foto de perfil">
            <label for="fileInput" class="avatar-edit-badge" title="Cambiar foto">
              <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="fileInput" @change="onFileSelected" hidden accept="image/*">
          </div>

          <div class="ms-md-4 text-center text-md-start mb-3">
            <h2 class="fw-bold text-blue mb-0">{{ Persona.nombres }} {{ Persona.apellidos }}</h2>
            <p class="text-muted mb-0">
              <span class="badge bg-gold-soft text-blue me-2">ID: {{ Persona.cedula }}</span>
              <span class="small fw-bold text-uppercase"><i class="fas fa-id-badge me-1"></i> Perfil de Usuario</span>
              {{ Usuario.nombre_rol }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-header bg-white border-0 p-0">
        <ul class="nav nav-pills custom-nav-pills px-3 pt-3" id="perfilTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personales" type="button">
              <i class="fas fa-user-edit me-2"></i>Datos Personales
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#usuario" type="button">
              <i class="fas fa-shield-alt me-2"></i>Seguridad de Cuenta
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button @click="getFamiliares" class="nav-link" data-bs-toggle="tab" data-bs-target="#familias"
              type="button">
              <i class="fas fa-users me-2"></i>Familias
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-4">
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="personales" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Información Básica</h5>
              <p class="text-muted small">Desde aquí puedes gestionar tu información de contacto. Para cambios en
                nombres o cédula, contacta a secretaría.</p>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <input v-model="Persona.correo" type="email" class="form-control custom-input-profile"
                  placeholder="correo@ejemplo.com">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                <input v-model="Persona.telefono" type="text" class="form-control custom-input-profile"
                  placeholder="09xxxxxxxx">
              </div>
              <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">DIRECCIÓN DE DOMICILIO</label>
                <input v-model="Persona.direccion" type="text" class="form-control custom-input-profile">
              </div>
              <div class="col-12 text-end mt-4">
                <button @click="actualizarDatosPersonales" class="btn btn-gold px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-save me-2"></i> {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="usuario" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Gestión de Acceso</h5>
              <p class="text-muted small">Mantén tu cuenta segura cambiando tu contraseña periódicamente. El nombre de
                usuario no puede ser modificado.</p>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">NOMBRE DE USUARIO</label>
                <input v-model="Usuario.username" type="text" class="form-control bg-light text-muted" readonly>
              </div>
              <div class="col-md-6"></div>

              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">NUEVA CONTRASEÑA</label>
                <input v-model="nuevaClave" type="password" class="form-control custom-input-profile"
                  placeholder="Mínimo 8 caracteres">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">CONFIRMAR CONTRASEÑA</label>
                <input v-model="confirmarClave" type="password" class="form-control custom-input-profile">
              </div>

              <div class="col-12 mt-4">
                <div class="alert alert-info border-0 shadow-sm rounded-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                      <h6 class="mb-0 fw-bold">Recomendación</h6>
                      <small>Tu contraseña debe ser difícil de adivinar y no debes compartirla con nadie.</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 text-end">
                <button @click="actualizarCredenciales" class="btn btn-blue px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-key me-2"></i> Cambiar Contraseña
                </button>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="familias" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Núcleo Familiar Registrado</h5>
              <p class="text-muted small">A continuación se listan los familiares vinculados a su cuenta en el sistema
                académico.</p>
            </div>

            <div v-if="cargandoFamilia" class="text-center py-5">
              <div class="spinner-border text-gold" role="status"></div>
              <p class="mt-2 text-muted">Cargando familiares...</p>
            </div>

            <div v-else-if="familiares.length === 0" class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
              <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-3x me-3 text-warning"></i>
                <div>
                  <h6 class="fw-bold mb-1">Usted no posee familia asignada</h6>
                  <p class="mb-0 small">Debe dirigirse a la institución para registrar a su grupo familiar y completar
                    su expediente.</p>
                </div>
              </div>
            </div>

            <div v-else class="row g-3">
              <div v-for="familiar in familiares" :key="familiar.id_persona" class="col-md-6">
                <div class="card border shadow-sm rounded-4 h-100 hvr-light">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <img :src="familiar.foto ? 'data:image/jpeg;base64,' + familiar.foto : getPhotoUrl(null)"
                        class="rounded-circle border border-2 border-gold shadow-sm"
                        style="width: 70px; height: 70px; object-fit: cover;">
                      <div class="ms-3">
                        <h6 class="mb-0 fw-bold text-blue">{{ familiar.nombres }} {{ familiar.apellidos }}</h6>
                        <span class="badge bg-gold text-blue small mb-1">{{ familiar.parentesco }}</span>
                        <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i> {{ familiar.cedula }}</p>
                        <p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i>
                          {{ familiar.telefono || 'Sinteléfono' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import API from "@/assets/js/axios"
import { mostraralertas } from "@/assets/js/funciones/functions";
import { getMe } from "@/assets/js/auth";

export default {
  data() {
    return {
      baseUrl: "/sistma",
      Persona: {},
      Usuario: {},
      familiares: {},
      nuevaClave: "",
      confirmarClave: "",
      cargando: false,
      refreshKey: Date.now(),
      idpersona: 0,
      idusuario: 0,
      idrole: 0,
      cargandoFamilia: false,
    }
  },
  async mounted() {
    this.cargando = true;
    const me = await getMe();
    this.idpersona = me.id_persona;
    this.idusuario = me.id_usuario;
    await Promise.all([this.getPersona(), this.getUsuario(), this.getFamiliares()]);
    this.cargando = false;
  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async getFamiliares() {
      if (this.familiares.length > 0) return; // Evita recargar si ya hay datos

      try {
        this.cargandoFamilia = true;
        const res = await API.get(`${this.baseUrl}/familiares-de/${this.idpersona}`);
        this.familiares = res.data.data;
      } catch (e) {
        console.error("Error al traer familiares:", e);
        mostraralertas("No se pudo obtener la información de familia", "error");
      } finally {
        this.cargandoFamilia = false;
      }
    },
    async getUsuario() {
      try {
        const res = await API.get(`${this.baseUrl}/usuarios/${this.idusuario}`);
        this.Usuario = res.data.data;
      } catch (err) { console.error(err); }
    },
    getPhotoUrl(ci) {
      if (!ci) return "https://ui-avatars.com/api/?name=User&background=1D2A68&color=fff";
      // Tu endpoint de imagen
      return `${API.defaults.baseURL}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
    },
    async onFileSelected(event) {
      const file = event.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = async (e) => {
        const base64String = e.target.result.split(',')[1];
        try {
          this.cargando = true;
          const params = { ...this.Persona, foto: base64String };
          const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, params);
          if (res.data.mensaje) {
            mostraralertas("Foto actualizada", "success");
            this.refreshKey = Date.now();
          }
        } catch (e) { mostraralertas("Error al subir foto", "error"); }
        finally { this.cargando = false; }
      };
      reader.readAsDataURL(file);
    },
    async actualizarDatosPersonales() {
      try {
        this.cargando = true;
        const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, this.Persona);
        mostraralertas(res.data.mensaje, "success");
      } catch (e) { mostraralertas("Error al actualizar", "error"); }
      finally { this.cargando = false; }
    },
    async actualizarCredenciales() {
      if (this.nuevaClave !== this.confirmarClave) return mostraralertas("Contraseñas no coinciden", "warning");
      if (this.nuevaClave.length < 8) return mostraralertas("Muy corta", "warning");

      try {
        this.cargando = true;
        const params = { ...this.Usuario, clave: this.nuevaClave };
        const res = await API.put(`${this.baseUrl}/usuarios/${this.idusuario}`, params);
        mostraralertas("Contraseña actualizada con éxito", "success");
        this.nuevaClave = ""; this.confirmarClave = "";
      } catch (e) { mostraralertas("Error al cambiar clave", "error"); }
      finally { this.cargando = false; }
    }
  }
}
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}

.bg-gold-soft {
  background-color: rgba(244, 179, 36, 0.2);
}

/* Diseño del Header */
.profile-header-bg {
  height: 120px;
  background: linear-gradient(90deg, #1D2A68 0%, #151e4b 100%);
}

.profile-content {
  margin-top: -60px;
}

.profile-avatar-container {
  position: relative;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  border: 6px solid #fff;
  background-color: #fff;
}

.profile-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-edit-badge {
  position: absolute;
  bottom: 5px;
  right: 5px;
  width: 35px;
  height: 35px;
  background-color: #F4B324;
  color: #1D2A68;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 3px solid #fff;
  transition: 0.3s;
}

.avatar-edit-badge:hover {
  transform: scale(1.1);
}

/* Estilo Nav Pills Institucional */
.custom-nav-pills .nav-link {
  color: #6c757d;
  font-weight: bold;
  border-radius: 10px;
  padding: 10px 20px;
  margin-right: 10px;
}

.custom-nav-pills .nav-link.active {
  background-color: #1D2A68;
  color: #F4B324;
}

/* Inputs */
.custom-input-profile {
  padding: 10px 15px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
}

.custom-input-profile:focus {
  border-color: #F4B324;
  box-shadow: 0 0 0 0.25rem rgba(244, 179, 36, 0.1);
}

.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}

.btn-blue {
  background-color: #1D2A68;
  color: #fff;
  border: none;
}
</style>
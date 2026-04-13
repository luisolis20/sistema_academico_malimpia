<template>
  <div class="container-fluid py-4">
    <header class="row mb-4">
      <div class="col">
        <h1 class="page-title" style="font-family: 'Fraunces', serif; color: var(--green-900);">
          Gestión Institucional
        </h1>
        <p class="text-muted">{{rolUsuario}}: {{nombreUsuario}}</p>
        <p class="text-muted">{{emailUsuario}}</p>
      </div>
    </header>

    <div class="row g-3 mb-4">
      <div class="col-md-3" v-for="card in metricas" :key="card.titulo">
        <div class="info-card h-100 border-0 shadow-sm p-3 d-flex align-items-center bg-white" style="border-radius: 14px;">
          <div class="card-icon me-3" :style="{ background: 'var(--green-500)' }">
             <i :class="card.icono" class="text-white"></i>
          </div>
          <div class="card-content">
            <h3 class="text-uppercase small fw-bold text-muted mb-1">{{ card.titulo }}</h3>
            <p class="big-number mb-0" style="font-size: 1.5rem; color: var(--green-800);">{{ card.valor }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="color: var(--green-900);">Registro de Usuarios</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario">
              <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </button>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead style="background: var(--green-800); color: white;">
                  <tr>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Rol</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in usuarios" :key="user.cedula">
                    <td class="fw-bold">{{ user.cedula }}</td>
                    <td>{{ user.nombre }}</td>
                    <td>
                      <span :class="getBadgeClass(user.rol)">{{ user.rol }}</span>
                    </td>
                    <td>{{ user.email }}</td>
                    <td>
                      <div class="btn-group shadow-sm">
                        <button class="btn btn-light btn-sm text-success"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-light btn-sm text-danger"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <div class="modal-header border-0">
            <h5 class="modal-title fw-bold" style="font-family: 'Fraunces';">Registrar Nuevo Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="guardarUsuario">
              <div class="mb-3">
                <label class="form-label small fw-bold">Cédula de Identidad</label>
                <input v-model="form.cedula" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label small fw-bold">Nombre</label>
                  <input v-model="form.nombre" type="text" class="form-control" required>
                </div>
                <div class="col">
                  <label class="form-label small fw-bold">Rol</label>
                  <select v-model="form.rol" class="form-select">
                    <option value="estudiante">Estudiante</option>
                    <option value="profesor">Profesor</option>
                  </select>
                </div>
              </div>
              <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Guardar Usuario</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import script2 from '@/store/custom.js';
export default {
  mixins: [script2],
  data() {
    return {
      metricas: [
        { titulo: 'Estudiantes', valor: '120', icono: 'fas fa-user-graduate' },
        { titulo: 'Docentes', valor: '15', icono: 'fas fa-chalkboard-teacher' },
        { titulo: 'Cursos', valor: '8', icono: 'fas fa-school' },
        { titulo: 'Aulas', valor: '10', icono: 'fas fa-door-open' }
      ],
      usuarios: [
        { cedula: '0801234567', nombre: 'Ana Gualacata', rol: 'profesor', email: 'ana.g@milenio.edu' },
        { cedula: '0807654321', nombre: 'Carlos Ruiz', rol: 'estudiante', email: 'c.ruiz@est.edu' }
      ],
      form: { cedula: '', nombre: '', rol: 'estudiante' }
    }
  },
  methods: {
    getBadgeClass(rol) {
      return rol === 'profesor' ? 'user-type-badge profesor' : 'user-type-badge estudiante';
    },
    guardarUsuario() {
      // Aquí conectaremos con Axios a tu API de Laravel
      console.log("Guardando...", this.form);
    }
  }
}
</script>
<template>
  <div class="container-fluid py-4">
    <header class="row mb-4 align-items-center">
      <div class="col-md-8">
        <h2 class="fw-bold" style="color: var(--green-900); font-family: 'Fraunces';">
          Configuración de Oferta Académica
        </h2>
        <p class="text-muted">Administración de niveles, paralelos y asignación de docentes.</p>
      </div>
      <div class="col-md-4 text-md-end">
        <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalCurso">
          <i class="fas fa-plus-circle me-2"></i>Nuevo Curso
        </button>
      </div>
    </header>

    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
          <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-success">Cursos Activos</h5>
          </div>
          <div class="list-group list-group-flush p-2">
            <button 
              v-for="curso in cursos" 
              :key="curso.id"
              @click="seleccionarCurso(curso)"
              class="list-group-item list-group-item-action border-0 mb-2 d-flex justify-content-between align-items-center"
              :class="{'active-curso shadow-sm': cursoSeleccionado?.id === curso.id}"
              style="border-radius: 10px;"
            >
              <div>
                <span class="fw-bold d-block">{{ curso.nivel }}</span>
                <small class="text-muted">Paralelo: {{ curso.paralelo }}</small>
              </div>
              <span class="badge rounded-pill bg-success">{{ curso.matriculados }} Est.</span>
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-8" v-if="cursoSeleccionado">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
          <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
            <h5 class="mb-0">Malla Curricular: {{ cursoSeleccionado.nivel }} "{{ cursoSeleccionado.paralelo }}"</h5>
            <button @click="abrirAsignacionMateria" class="btn btn-sm btn-light text-success fw-bold">
              <i class="fas fa-book-medical me-1"></i> Añadir Materia
            </button>
          </div>
          
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">Asignatura</th>
                    <th>Docente Asignado</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="m in mallaActual" :key="m.id">
                    <td class="ps-4 fw-bold text-dark">{{ m.nombre }}</td>
                    <td>
                      <select class="form-select form-select-sm border-0 bg-light" v-model="m.docente_id">
                        <option value="">Sin asignar...</option>
                        <option v-for="d in docentes" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                      </select>
                    </td>
                    <td class="text-center">
                      <span class="badge" :class="m.docente_id ? 'bg-info' : 'bg-warning'">
                        {{ m.docente_id ? 'Vinculada' : 'Pendiente' }}
                      </span>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-sm text-danger"><i class="fas fa-trash-alt"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer bg-white border-0 py-3 text-end">
            <button class="btn btn-success px-5" @click="guardarCambiosMalla">
              <i class="fas fa-save me-2"></i>Guardar Configuración
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-8 text-center py-5" v-else>
        <div class="p-5 border-dashed rounded-4 bg-white shadow-sm">
          <i class="fas fa-school fa-4x text-light mb-3"></i>
          <h4 class="text-muted">Seleccione un curso para configurar su malla curricular</h4>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cursoSeleccionado: null,
      cursos: [
        { id: 1, nivel: 'Primero BGU', paralelo: 'A', matriculados: 15 },
        { id: 2, nivel: 'Segundo BGU', paralelo: 'B', matriculados: 38 }
      ],
      docentes: [
        { id: 101, nombre: 'Ana Karen Gualacata' },
        { id: 102, nombre: 'Carlos Mendoza' }
      ],
      mallaActual: [] // Se cargará dinámicamente al seleccionar curso
    }
  },
  methods: {
    seleccionarCurso(curso) {
      this.cursoSeleccionado = curso;
      // Simulación de carga de materias desde la API (Axios)
      this.mallaActual = [
        { id: 1, nombre: 'Matemáticas', docente_id: 101 },
        { id: 2, nombre: 'Lengua y Literatura', docente_id: '' }
      ];
    },
    guardarCambiosMalla() {
      // Lógica para enviar a Laravel
      console.log("Actualizando malla para el curso:", this.cursoSeleccionado.id, this.mallaActual);
    }
  }
}
</script>

<style scoped>
.active-curso {
  background-color: var(--green-50) !important;
  border-left: 4px solid var(--green-600) !important;
  color: var(--green-900) !important;
}
.border-dashed {
  border: 2px dashed #dee2e6;
}
</style>
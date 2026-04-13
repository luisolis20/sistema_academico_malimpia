<template>
  <div class="container-fluid py-4">
    <header class="mb-4 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="fw-bold" style="color: var(--green-900); font-family: 'Fraunces';">Asignación de Carga Horaria</h2>
        <p class="text-muted">Distribución de docentes y materias por paralelos.</p>
      </div>
      <div class="badge bg-success p-2 shadow-sm">Periodo Lectivo: 2026 - 2027</div>
    </header>

    <div class="row">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
          <div class="card-body">
            <label class="form-label fw-bold small text-success text-uppercase">1. Seleccionar Curso</label>
            <select class="form-select border-0 bg-light mb-3" v-model="cursoId" @change="cargarDatosAsignacion">
              <option value="">Elegir paralelo...</option>
              <option v-for="c in cursos" :key="c.id" :value="c.id">{{ c.nivel }} "{{ c.paralelo }}"</option>
            </select>
            
            <div v-if="cursoId" class="mt-4">
              <label class="form-label fw-bold small text-success text-uppercase">2. Materias del Nivel</label>
              <div class="list-group list-group-flush">
                <div v-for="m in materiasMalla" :key="m.id" 
                     class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                  <span class="small fw-bold">{{ m.nombre }}</span>
                  <i class="fas fa-arrow-right text-muted"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;" v-if="cursoId">
          <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="fas fa-users-cog me-2 text-success"></i>Configuración de Carga</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle border-top">
              <thead class="table-light">
                <tr>
                  <th>Materia</th>
                  <th>Docente Responsable</th>
                  <th>Carga Horaria</th>
                  <th>Aula / Laboratorio</th>
                  <th class="text-center">Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in materiasMalla" :key="m.id">
                  <td class="fw-bold">{{ m.nombre }}</td>
                  <td>
                    <select class="form-select form-select-sm border-success-subtle" v-model="m.docente_id">
                      <option value="">Seleccionar profesor...</option>
                      <option v-for="d in docentes" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                    </select>
                  </td>
                  <td>
                    <div class="input-group input-group-sm w-75">
                      <input type="number" class="form-control" v-model="m.horas" placeholder="Horas/Sem">
                      <span class="input-group-text">H</span>
                    </div>
                  </td>
                  <td>
                    <input type="text" class="form-control form-control-sm" v-model="m.aula" placeholder="Ej: Aula 4, Lab 1">
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-light text-primary" @click="configurarHorario(m)">
                      <i class="fas fa-clock"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer bg-white border-0 py-3 text-end">
            <button class="btn btn-success fw-bold px-4 shadow-sm" @click="guardarAsignacion">
              <i class="fas fa-save me-2"></i>Finalizar Asignación
            </button>
          </div>
        </div>

        <div v-else class="h-100 d-flex flex-column align-items-center justify-content-center bg-white rounded-4 shadow-sm py-5">
          <i class="fas fa-id-card-alt fa-4x text-light mb-3"></i>
          <p class="text-muted">Seleccione un curso para iniciar la asignación de docentes y horarios.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cursoId: '',
      cursos: [
        { id: 1, nivel: 'Primero BGU', paralelo: 'A' },
        { id: 2, nivel: 'Segundo BGU', paralelo: 'B' }
      ],
      docentes: [
        { id: 1, nombre: 'Lic. Josué Lastra' },
        { id: 2, nombre: 'Dra. Ana Gualacata' }
      ],
      materiasMalla: []
    }
  },
  methods: {
    cargarDatosAsignacion() {
      // Simulación: Axios traería las materias asignadas a este nivel en la Malla Curricular
      this.materiasMalla = [
        { id: 10, nombre: 'Matemáticas', docente_id: '', horas: 5, aula: '' },
        { id: 11, nombre: 'Física', docente_id: '', horas: 4, aula: '' },
        { id: 12, nombre: 'Química', docente_id: '', horas: 3, aula: '' }
      ];
    },
    guardarAsignacion() {
      // Petición POST a Laravel para actualizar la tabla 'asignaciones'
      console.log("Carga horaria guardada para el curso", this.cursoId);
    }
  }
}
</script>
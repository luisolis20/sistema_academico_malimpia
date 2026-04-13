<template>
  <div class="container-fluid py-4">
    <header class="mb-4">
      <h2 class="fw-bold" style="color: var(--green-900);">Gestión de Matrículas</h2>
      <p class="text-muted">Asignación de estudiantes a periodos lectivos y cursos.</p>
    </header>

    <div class="row">
      <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 14px;">
          <h5 class="mb-3"><i class="fas fa-file-signature text-success me-2"></i>Nueva Matrícula</h5>
          <form @submit.prevent="registrarMatricula">
            <div class="mb-3">
              <label class="form-label small fw-bold">Buscar Estudiante (Cédula)</label>
              <input type="text" class="form-control" v-model="busqueda" placeholder="Ej: 0801234567">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">Curso a Asignar</label>
              <select class="form-select" v-model="matriculaForm.curso_id">
                <option value="" disabled>Seleccione un curso...</option>
                <option v-for="c in cursos" :key="c.id" :value="c.id">
                  {{ c.nivel }} "{{ c.paralelo }}"
                </option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-check-circle me-2"></i> Confirmar Matrícula
            </button>
          </form>
        </div>
      </div>

      <div class="col-md-7">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Matrículas Recientes</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead style="background: var(--green-100); color: var(--green-900);">
                <tr>
                  <th>Estudiante</th>
                  <th>Curso</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in listaMatriculas" :key="m.id">
                  <td>{{ m.estudiante_nombre }}</td>
                  <td>{{ m.curso_nombre }}</td>
                  <td><span class="badge bg-success">Matriculado</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      busqueda: '',
      matriculaForm: { curso_id: '' },
      cursos: [
        { id: 1, nivel: 'Primero BGU', paralelo: 'A' },
        { id: 2, nivel: 'Segundo BGU', paralelo: 'B' }
      ],
      listaMatriculas: [
        { id: 1, estudiante_nombre: 'Josué Lastra', curso_nombre: 'Primero BGU "A"' }
      ]
    }
  },
  methods: {
    registrarMatricula() {
      // Lógica de Axios para conectar con el backend
      console.log("Matriculando estudiante con cédula:", this.busqueda);
    }
  }
}
</script>
<template>
  <div class="container mt-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-success text-white">
        <h3 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Perfil Académico</h3>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" id="tabEstudiante" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#matricula">Matriculación</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#horario">Horario</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calificaciones">Calificaciones</button>
          </li>
        </ul>

        <div class="tab-content p-4 border border-top-0">
          <div class="tab-pane fade show active" id="matricula">
            <h4>Registrar Matrícula </h4>
            <div class="alert alert-info">Selecciona el curso para el periodo lectivo actual.</div>
            <div class="col-md-6">
              <select v-model="cursoSeleccionado" class="form-select mb-3">
                <option value="" disabled>Seleccione un curso...</option>
                <option v-for="c in cursos" :key="c.id" :value="c.id">{{ c.nombre_curso }} - {{ c.paralelo }}</option>
              </select>
              <button @click="procesarMatricula" class="btn btn-success">Confirmar Matrícula</button>
            </div>
          </div>

          <div class="tab-pane fade" id="horario">
            <h4> Horario de Clases</h4>
            <table class="table table-sm mt-3">
              <thead class="table-dark">
                <tr>
                  <th>Hora</th>
                  <th>Lunes</th>
                  <th>Martes</th>
                  <th>Miércoles</th>
                  <th>Jueves</th>
                  <th>Viernes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="h in horarios" :key="h.id">
                  <td>{{ h.hora_inicio }} - {{ h.hora_fin }}</td>
                  <td colspan="5" class="text-center">{{ h.materia }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="tab-pane fade" id="calificaciones">
            <h4>Mis Calificaciones</h4>
            <table class="table mt-3">
              <thead class="bg-light">
                <tr>
                  <th>Materia</th>
                  <th>Periodo</th>
                  <th>Nota</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="n in notas" :key="n.id">
                  <td>{{ n.materia }}</td>
                  <td>{{ n.periodo }}</td>
                  <td class="fw-bold">{{ n.valor }}</td>
                  <td><span class="badge" :class="n.valor >= 7 ? 'bg-success' : 'bg-danger'">{{ n.valor >= 7 ? 'Aprobado' : 'Reprobado' }}</span></td>
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
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      estudianteId: this.$route.params.id,
      cursoSeleccionado: '',
      cursos: [],
      horarios: [],
      notas: []
    }
  },
  mounted() {
    this.cargarInformacion();
  },
  methods: {
    async cargarInformacion() {
      // Aquí cargarías los datos desde tu API de Laravel
      // Por ahora simulamos los endpoints
      try {
        const resCursos = await axios.get('http://localhost:8000/api/cursos');
        this.cursos = resCursos.data;
        // Cargar notas y horarios igual...
      } catch (e) { console.error(e); }
    },
    procesarMatricula() {
      Swal.fire('Éxito', 'Matrícula registrada correctamente', 'success');
    }
  }
}
</script>
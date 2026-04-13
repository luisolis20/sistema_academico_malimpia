<template>
  <div class="container mt-4">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-success text-white py-3">
        <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i> Panel del Docente</h4>
      </div>
      <div class="card-body bg-light">
        <div class="row">
          <div class="col-md-6"><strong>Docente:</strong> {{ docente.nombre }} {{ docente.apellido }}</div>
          <div class="col-md-6 text-md-end"><strong>Especialidad:</strong> {{ docente.especialidad }}</div>
        </div>
      </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="docenteTab" role="tablist">
      <li class="nav-item">
        <button class="nav-link active text-success" data-bs-toggle="tab" data-bs-target="#horario">Mi Horario</button>
      </li>
      <li class="nav-item">
        <button class="nav-link text-success" data-bs-toggle="tab" data-bs-target="#alumnos">Mis Alumnos</button>
      </li>
      <li class="nav-item">
        <button class="nav-link text-success" data-bs-toggle="tab" data-bs-target="#notas">Subir Notas</button>
      </li>
    </ul>

    <div class="tab-content bg-white p-4 shadow-sm rounded border">
      
      <div class="tab-pane fade show active" id="horario">
        <h5><i class="fas fa-clock me-2"></i>Cronograma de Clases</h5>
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead class="table-success">
              <tr>
                <th>Hora</th>
                <th>Materia</th>
                <th>Curso / Paralelo</th>
                <th>Día</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="h in horarios" :key="h.id">
                <td>{{ h.hora_inicio }} - {{ h.hora_fin }}</td>
                <td>{{ h.materia }}</td>
                <td>{{ h.curso }} "{{ h.paralelo }}"</td>
                <td>{{ h.dia }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="alumnos">
        <h5><i class="fas fa-users me-2"></i>Estudiantes por Curso</h5>
        <div class="mb-3 mt-3">
          <select v-model="cursoSeleccionado" class="form-select w-50" @change="filtrarAlumnos">
            <option value="">Seleccione un curso para ver alumnos...</option>
            <option v-for="c in misCursos" :key="c.id" :value="c.id">{{ c.nombre }}</option>
          </select>
        </div>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Cédula</th>
              <th>Estudiante</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alum in alumnosFiltrados" :key="alum.id">
              <td>{{ alum.cedula }}</td>
              <td>{{ alum.apellido }} {{ alum.nombre }}</td>
              <td><span class="badge bg-info">Matriculado</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="tab-pane fade" id="notas">
        <h5><i class="fas fa-edit me-2"></i>Registro de Calificaciones</h5>
        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="form-label">Materia</label>
            <select class="form-select" v-model="notaForm.materia">
              <option v-for="m in misMaterias" :key="m.id">{{ m.nombre }}</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Estudiante</label>
            <select class="form-select" v-model="notaForm.estudiante">
              <option v-for="a in alumnosFiltrados" :key="a.id" :value="a.id">{{ a.apellido }} {{ a.nombre }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Nota</label>
            <input type="number" step="0.01" class="form-control" v-model="notaForm.valor" max="10" min="0">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-success w-100" @click="guardarNota">Guardar</button>
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
      docente: { nombre: 'Cargando...', apellido: '', especialidad: '' },
      horarios: [],
      misCursos: [],
      misMaterias: [],
      alumnosFiltrados: [],
      cursoSeleccionado: '',
      notaForm: {
        estudiante: '',
        materia: '',
        valor: 0
      }
    }
  },
  mounted() {
    this.cargarDatosDocente();
  },
  methods: {
    async cargarDatosDocente() {
      // Aquí usarías el ID del docente logueado
      const idDocente = 1; 
      try {
        const res = await axios.get(`http://localhost:8000/api/docentes/${idDocente}/panel`);
        this.docente = res.data.perfil;
        this.horarios = res.data.horarios;
        this.misCursos = res.data.cursos;
        this.misMaterias = res.data.materias;
      } catch (e) {
        console.error("Error al cargar panel", e);
      }
    },
    async filtrarAlumnos() {
      if(!this.cursoSeleccionado) return;
      const res = await axios.get(`http://localhost:8000/api/cursos/${this.cursoSeleccionado}/alumnos`);
      this.alumnosFiltrados = res.data;
    },
    async guardarNota() {
      if(this.notaForm.valor > 10 || this.notaForm.valor < 0) {
        return Swal.fire('Error', 'La nota debe estar entre 0 y 10', 'error');
      }
      try {
        await axios.post('http://localhost:8000/api/calificaciones', this.notaForm);
        Swal.fire('¡Éxito!', 'Calificación registrada', 'success');
      } catch (e) {
        Swal.fire('Error', 'No se pudo guardar la nota', 'error');
      }
    }
  }
}
</script>

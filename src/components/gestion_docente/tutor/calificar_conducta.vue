<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h2 class="fw-bold text-blue mb-1">Calificar Conducta</h2>
          <p class="text-muted mb-0 small">
            <i class="fas fa-info-circle me-2 text-gold"></i>
            Tutor de: <span class="fw-bold text-blue">{{ infoTutor.curso }}</span> | Especialidad: <span class="fw-bold text-blue">{{ infoTutor.especialidad }}</span> | Periodo: <span class="badge bg-blue border border-gold text-white">{{ infoTutor.periodo }}</span>
          </p>
        </div>
        <div class="col-md-4 mt-2 mt-md-0">
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Buscar estudiante por apellidos..." v-model="busqueda">
          </div>
        </div>
      </div>
    </header>

    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4" role="alert">
      <h6 class="fw-bold mb-2"><i class="fas fa-calculator me-2"></i> Parámetros de la calificación</h6>
      <ul class="mb-0 small">
        <li><strong>A:</strong> MUY SATISFACTORIO: Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.</li>
        <li><strong>B:</strong> SATISFACTORIO: Cumple con los compromisos establecidos para la sana convivencia social.</li>
        <li><strong>C:</strong> POCO SATISFACTORIO: Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.</li>
        <li><strong>D:</strong> MEJORABLE: Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.</li>
        <li><strong>E:</strong> INSATISFACTORIO: No cumple con los compromisos establecidos para la sana convivencia social.  </li> 
      </ul>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-blue text-white">
                <tr>
                  <th class="rounded-start">Estudiante</th>
                  <th>Cédula</th>
                  <th class="text-center">Q1</th>
                  <th class="text-center">Q2</th>
                  <th class="text-end rounded-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="al in alumnosFiltrados" :key="al.id_matricula" :class="{'table-active-row': selectedAlumno && selectedAlumno.id_matricula === al.id_matricula}">
                  <td>
                    <div class="d-flex align-items-center">
                      <img :src="al.estudiante.foto ? 'data:image/jpeg;base64,' + al.estudiante.foto : '/avatar-default.png'" class="rounded-circle me-2 border" style="width: 40px; height: 40px; object-fit: cover;">
                      <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ al.estudiante.apellidos }} {{ al.estudiante.nombres }}</h6>
                      </div>
                    </div>
                  </td>
                  <td class="small text-muted">{{ al.estudiante.cedula }}</td>
                  <td class="text-center">
                    <span :class="getBadgeClass(getConductaLetra(al, 'Quimestre 1'))">{{ getConductaLetra(al, 'Quimestre 1') }}</span>
                  </td>
                  <td class="text-center">
                    <span :class="getBadgeClass(getConductaLetra(al, 'Quimestre 2'))">{{ getConductaLetra(al, 'Quimestre 2') }}</span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-blue me-2 rounded-pill" @click="seleccionarEstudiante(al)">
                      <i class="fas fa-user-cog"></i> Gestionar
                    </button>
                  </td>
                </tr>
                <tr v-if="alumnosFiltrados.length === 0">
                  <td colspan="5" class="text-center text-muted py-4">No se encontraron estudiantes conformes a la búsqueda.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top text-center" v-if="selectedAlumno" style="top: 20px;">
          <img :src="selectedAlumno.estudiante.foto ? 'data:image/jpeg;base64,' + selectedAlumno.estudiante.foto : '/avatar-default.png'" class="rounded-circle mx-auto mb-3 border border-gold border-3" style="width: 90px; height: 90px; object-fit: cover;">
          <h5 class="fw-bold text-blue mb-1">{{ selectedAlumno.estudiante.apellidos }}</h5>
          <p class="text-muted small mb-4">{{ selectedAlumno.estudiante.nombres }}</p>

          <div class="d-grid gap-3">
            <button class="btn btn-gold fw-bold w-100 py-2.5 rounded-3 shadow-sm" @click="abrirModalCalificar">
              <i class="fas fa-edit me-2"></i> Registrar / Editar Conducta
            </button>
            
            <button class="btn btn-outline-blue w-100 py-2.5 rounded-3" @click="abrirModalHistorial">
              <i class="fas fa-history me-2"></i> Ver Historial de Conductas
            </button>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted h-100 d-flex align-items-center justify-content-center" v-else>
          <div>
            <i class="fas fa-mouse-pointer display-4 opacity-25 mb-3"></i>
            <h6>Seleccione un estudiante de la lista para gestionar su conducta académica.</h6>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalCalificar" data-bs-backdrop="static" tabindex="-1" ref="modalCalificarRef">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
          <div class="modal-header bg-blue text-white rounded-top-4">
            <h5 class="modal-title fw-bold"><i class="fas fa-pen-nib me-2 text-gold"></i>Asignar Calificación de Conducta</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedAlumno">
            <h6 class="fw-bold text-blue mb-3">Alumno: {{ selectedAlumno.estudiante.apellidos }} {{ selectedAlumno.estudiante.nombres }}</h6>
            
            <form @submit.prevent="guardarConducta">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Seleccionar Quimestre Activo</label>
                <select class="form-select border-gold" v-model="formConducta.quimestre" @change="cargarDatosExistentesFase">
                  <option value="" disabled>-- Seleccione Quimestre --</option>
                  <option value="Quimestre 1" :disabled="!fasesConducta.Q1">Quimestre 1 ({{ fasesConducta.Q1 ? 'Habilitado' : 'Cerrado' }})</option>
                  <option value="Quimestre 2" :disabled="!fasesConducta.Q2">Quimestre 2 ({{ fasesConducta.Q2 ? 'Habilitado' : 'Cerrado' }})</option>
                </select>
              </div>

              <div v-if="formConducta.quimestre">
                <div class="mb-3">
                  <label class="form-label fw-bold text-muted small">Calificación (Letra)</label>
                  <select class="form-select" v-model="formConducta.calificacion_letra" required>
                    <option value="A">A - MUY SATISFACTORIO</option>
                    <option value="B">B - SATISFACTORIO</option>
                    <option value="C">C - POCO SATISFACTORIO</option>
                    <option value="D">D - MEJORABLE</option>
                    <option value="E">E - INSATISFACTORIO</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold text-muted small">Observación / Retroalimentación</label>
                  <textarea class="form-control" rows="3" placeholder="Detalle fortalezas o aspectos por mejorar..." v-model="formConducta.observacion"></textarea>
                </div>
                <div class="text-end mt-4">
                  <button type="button" class="btn btn-secondary me-2 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-gold px-4 rounded-pill fw-bold" :disabled="guardando">
                    <span v-if="guardando" class="spinner-border spinner-border-sm me-1"></span> Guardar Registro
                  </button>
                </div>
              </div>
              <div class="alert alert-warning border-0 text-center rounded-3 small my-2" v-else>
                Por favor, seleccione un Quimestre activo en el listado para desplegar los campos.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalHistorial" tabindex="-1" ref="modalHistorialRef">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
          <div class="modal-header bg-secondary text-white rounded-top-4">
            <h5 class="modal-title fw-bold"><i class="fas fa-history me-2"></i>Historial del Periodo Lectivo</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedAlumno">
            <h6 class="fw-bold text-blue mb-4 text-center">{{ selectedAlumno.estudiante.apellidos }} {{ selectedAlumno.estudiante.nombres }}</h6>
            
            <div class="card border-0 bg-light p-3 rounded-3 mb-3">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <span class="fw-bold text-blue">Quimestre 1</span>
                <span :class="getBadgeClass(getConductaLetra(selectedAlumno, 'Quimestre 1'))">{{ getConductaLetra(selectedAlumno, 'Quimestre 1') }}</span>
              </div>
              <p class="mb-0 text-muted small fst-italic">
                <strong>Observación:</strong> {{ getConductaObs(selectedAlumno, 'Quimestre 1') }}
              </p>
            </div>

            <div class="card border-0 bg-light p-3 rounded-3">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <span class="fw-bold text-blue">Quimestre 2</span>
                <span :class="getBadgeClass(getConductaLetra(selectedAlumno, 'Quimestre 2'))">{{ getConductaLetra(selectedAlumno, 'Quimestre 2') }}</span>
              </div>
              <p class="mb-0 text-muted small fst-italic">
                <strong>Observación:</strong> {{ getConductaObs(selectedAlumno, 'Quimestre 2') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { getMe } from "@/assets/js/auth";
import { Modal } from 'bootstrap';

export default {
  data() {
    return {
      idpersona: 0,
      busqueda: "",
      guardando: false,
      infoTutor: { curso: 'Cargando...', especialidad: 'Cargando...', periodo: '...' },
      fasesConducta: { Q1: false, Q2: false },
      alumnos: [],
      selectedAlumno: null,
      formConducta: { quimestre: "", calificacion_letra: "A", observacion: "" },
      baseUrl: "/sistma",
      instanciaModalCalificar: null,
      instanciaModalHistorial: null
    }
  },
  computed: {
    alumnosFiltrados() {
      if (!this.busqueda) return this.alumnos;
      return this.alumnos.filter(al => 
        al.estudiante.apellidos.toLowerCase().includes(this.busqueda.toLowerCase()) ||
        al.estudiante.nombres.toLowerCase().includes(this.busqueda.toLowerCase())
      );
    }
  },
  async mounted() {
    const me = await getMe();
    this.idpersona = me.id_persona;
    await this.fetchData();
    
    // Vinculación nativa con componentes de Bootstrap 5
    this.instanciaModalCalificar = new Modal(this.$refs.modalCalificarRef);
    this.instanciaModalHistorial = new Modal(this.$refs.modalHistorialRef);
  },
  methods: {
    async fetchData() {
      try {
        const res = await API.get(`${this.baseUrl}/datos-tutor-conducta/${this.idpersona}`);
        this.infoTutor = {
          curso: res.data.curso,
          especialidad: res.data.especialidad,
          periodo: res.data.periodo
        };
        this.fasesConducta = res.data.fases_conducta;
        this.alumnos = res.data.alumnos.sort((a, b) => a.estudiante.apellidos.localeCompare(b.estudiante.apellidos));
      } catch (e) {
        mostraralertas("No se pudo cargar la información del tutor o no posee un curso asignado.", "error");
      }
    },
    seleccionarEstudiante(alumno) {
      this.selectedAlumno = alumno;
    },
    getConductaLetra(alumno, quimestre) {
      const cond = alumno.conductas.find(c => c.quimestre === quimestre);
      return cond ? cond.calificacion_letra : 'N/A';
    },
    getConductaObs(alumno, quimestre) {
      const cond = alumno.conductas.find(c => c.quimestre === quimestre);
      return cond ? (cond.observacion || 'Sin observación registrada.') : 'No calificado aún.';
    },
    getBadgeClass(letra) {
      if (letra === 'A' || letra === 'B') return 'badge bg-success';
      if (letra === 'C') return 'badge bg-warning text-dark';
      if (letra === 'D' || letra === 'E') return 'badge bg-danger';
      return 'badge bg-secondary';
    },
    abrirModalCalificar() {
      // Validar si al menos un quimestre general está operativo
      if (!this.fasesConducta.Q1 && !this.fasesConducta.Q2) {
        mostraralertas("No existe un control de subida de notas habilitado para calificar la conducta en este periodo.", "warning");
        return;
      }
      this.formConducta = { quimestre: "", calificacion_letra: "A", observacion: "" };
      this.instanciaModalCalificar.show();
    },
    cargarDatosExistentesFase() {
      const condExistente = this.selectedAlumno.conductas.find(c => c.quimestre === this.formConducta.quimestre);
      if (condExistente) {
        this.formConducta.calificacion_letra = condExistente.calificacion_letra;
        this.formConducta.observacion = condExistente.observacion;
      } else {
        this.formConducta.calificacion_letra = "A";
        this.formConducta.observacion = "";
      }
    },
    abrirModalHistorial() {
      this.instanciaModalHistorial.show();
    },
    async guardarConducta() {
      this.guardando = true;
      try {
        const payload = {
          id_matricula: this.selectedAlumno.id_matricula,
          quimestre: this.formConducta.quimestre,
          calificacion_letra: this.formConducta.calificacion_letra,
          observacion: this.formConducta.observacion
        };

        const res = await API.post(`${this.baseUrl}/guardar_conducta`, payload);

        if (res.data.success) {
          mostraralertas(res.data.message, "success");

          // Actualización de estado local reactiva e inmediata
          const idxCond = this.selectedAlumno.conductas.findIndex(c => c.quimestre === this.formConducta.quimestre);
          if (idxCond !== -1) {
            this.selectedAlumno.conductas[idxCond].calificacion_letra = this.formConducta.calificacion_letra;
            this.selectedAlumno.conductas[idxCond].observacion = this.formConducta.observacion;
          } else {
            this.selectedAlumno.conductas.push({
              id_conducta: res.data.conducta.id_conducta,
              quimestre: this.formConducta.quimestre,
              calificacion_letra: this.formConducta.calificacion_letra,
              observacion: this.formConducta.observacion
            });
          }
          this.instanciaModalCalificar.hide();
        }
      } catch (error) {
        let msg = "Error al intentar guardar la calificación.";
        if (error.response && error.response.data && error.response.data.message) {
          msg = error.response.data.message;
        }
        mostraralertas(msg, "error");
      } finally {
        this.guardando = false;
      }
    }
  }
}
</script>

<style scoped>
.text-blue { color: #1D2A68; }
.text-gold { color: #F4B324; }
.bg-blue { background-color: #1D2A68; }
.border-gold { border-color: #F4B324 !important; }
.btn-gold { background-color: #F4B324; color: #1D2A68; border: none; }
.btn-gold:hover { background-color: #d99d1e; color: #fff; }
.btn-outline-blue { border: 2px solid #1D2A68; color: #1D2A68; background: transparent; }
.btn-outline-blue:hover { background-color: #1D2A68; color: white; }
.table-active-row { background-color: rgba(29, 42, 104, 0.05) !important; border-left: 4px solid #F4B324; }
</style>
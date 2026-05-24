<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5">
      <div class="row align-items-center">
        <div class="col-md-12">
          <h2 class="fw-bold text-blue mb-1">Registro de Calificaciones</h2>
          <p class="text-muted mb-0">
            <i class="fas fa-info-circle me-2 text-gold"></i>
            Registre las calificaciones de sus estudiantes. Solo podrá editar las fases actualmente habilitadas.
          </p>
        </div>
      </div>
    </header>

    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4" role="alert">
      <h6 class="fw-bold mb-2"><i class="fas fa-calculator me-2"></i> ¿Cómo se calculan las notas?</h6>
      <ul class="mb-0 small">
        <li><strong>Parciales (P1, P2, P3):</strong> Se promedian 4 insumos: Tareas, A. Individuales, A. Grupales y
          Lecciones.</li>
        <li><strong>Promedio Anual:</strong> Requiere mínimo 7/10 para aprobación directa.</li>
        <li><strong>Supletorio:</strong> Se habilita si el anual está entre 5 y 6.99. Aprueba con 7.</li>
        <li><strong>Remedial:</strong> Se habilita si el anual es < 5 o reprobó supletorio.</li>
        <li><strong>Gracia:</strong> Se habilita si reprobó remedial en una sola asignatura.</li>
        <li><strong>Nota Final:</strong> Si aprueba en recuperación, la nota final será siempre 7.00.</li>
        <li><strong>Restricciones:</strong> El sistema no permitirá ingresar valores menores a 0 ni mayores a 10.</li>
      </ul>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4">
        <label class="fw-bold text-blue mb-2">Seleccione una Asignatura:</label>
        <select class="form-select border-gold" v-model="asignaturaSeleccionada" @change="cargarEstudiantes">
          <option value="" disabled>Seleccione...</option>
          <option v-for="asig in asignaturas" :key="asig.id_curso_asignatura" :value="asig.id_curso_asignatura">
            {{ asig.asignatura.nombre }} - {{ asig.curso.nivel.nombre }} {{ asig.curso.paralelo }} ({{
              asig.curso.especialidad.nombre }})
          </option>
        </select>
      </div>
    </div>

    <div v-if="asignaturaSeleccionada && !cargandoEstudiantes" class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-0">

        <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center rounded-top-4">
          <div class="input-group w-50">
            <span class="input-group-text bg-light border-gold"><i class="fas fa-search text-gold"></i></span>
            <input type="text" class="form-control border-gold" placeholder="Buscar por cédula o nombre..."
              v-model="busqueda">
          </div>
          <button class="btn btn-gold fw-bold px-4 rounded-pill" @click="guardarTodasLasCalificaciones"
            :disabled="guardando">
            <span v-if="guardando" class="spinner-border spinner-border-sm me-2"></span>
            <i class="fas fa-save me-2" v-else></i> Guardar Cambios
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle mb-0 text-center text-nowrap">
            <thead class="bg-blue text-white">
              <tr>
                <th rowspan="2" class="align-middle text-start ps-4" style="min-width: 280px;">Estudiante</th>
                <th colspan="5" class="bg-secondary bg-opacity-25 border-end text-white">QUIMESTRE 1</th>
                <th colspan="5" class="bg-secondary bg-opacity-10 border-end text-white">QUIMESTRE 2</th>
                <th rowspan="2" class="align-middle">Anual</th>
                <th colspan="3" class="bg-gold text-blue">RECUPERACIÓN</th>
                <th rowspan="2" class="align-middle">Final</th>
                <th rowspan="2" class="align-middle">Estado</th>
              </tr>
              <tr class="small">
                <th>P1</th>
                <th>P2</th>
                <th>P3</th>
                <th>Exam</th>
                <th class="fw-bold">Prom</th>
                <th>P1</th>
                <th>P2</th>
                <th>P3</th>
                <th>Exam</th>
                <th class="fw-bold">Prom</th>
                <th class="bg-light text-dark">Suple</th>
                <th class="bg-light text-dark">Remed</th>
                <th class="bg-light text-dark">Gracia</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="est in estudiantesPaginados" :key="est.id_matricula">
                <td class="text-start ps-4">
                  <div class="d-flex align-items-center">
                    <img
                      :src="est.estudiante.foto ? 'data:image/jpeg;base64,' + est.estudiante.foto : 'https://ui-avatars.com/api/?name=' + est.estudiante.nombres"
                      class="rounded-circle me-3 border border-2 border-gold" width="45" height="45"
                      style="object-fit: cover;">
                    <div>
                      <h6 class="mb-0 fw-bold text-blue">{{ est.estudiante.apellidos }} {{ est.estudiante.nombres }}
                      </h6>
                      <small class="text-muted">CI: {{ est.estudiante.cedula }}</small>
                    </div>
                  </div>
                </td>

                <td :class="colorNota(est.calificaciones.q1_p1)">
                  <button v-if="esFaseActiva('Q1_P1')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q1_p1')"><i class="fas fa-edit"></i> {{ est.calificaciones.q1_p1
                    }}</button>
                  <span v-else>{{ est.calificaciones.q1_p1 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q1_p2)">
                  <button v-if="esFaseActiva('Q1_P2')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q1_p2')"><i class="fas fa-edit"></i> {{ est.calificaciones.q1_p2
                    }}</button>
                  <span v-else>{{ est.calificaciones.q1_p2 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q1_p3)">
                  <button v-if="esFaseActiva('Q1_P3')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q1_p3')"><i class="fas fa-edit"></i> {{ est.calificaciones.q1_p3
                    }}</button>
                  <span v-else>{{ est.calificaciones.q1_p3 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q1_examen)">
                  <input v-if="esFaseActiva('Q1_EXAMEN')" type="number" step="0.01" min="0" max="10"
                    class="form-control form-control-sm text-center mx-auto" style="width: 70px;"
                    v-model.number="est.calificaciones.q1_examen"
                    @input="validarNota(est.calificaciones, 'q1_examen'); recalcular(est)">
                  <span v-else>{{ est.calificaciones.q1_examen }}</span>
                </td>
                <td class="fw-bold bg-light" :class="colorNota(est.calificaciones.q1_promedio)">{{
                  est.calificaciones.q1_promedio }}</td>

                <td :class="colorNota(est.calificaciones.q2_p1)">
                  <button v-if="esFaseActiva('Q2_P1')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q2_p1')"><i class="fas fa-edit"></i> {{ est.calificaciones.q2_p1
                    }}</button>
                  <span v-else>{{ est.calificaciones.q2_p1 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q2_p2)">
                  <button v-if="esFaseActiva('Q2_P2')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q2_p2')"><i class="fas fa-edit"></i> {{ est.calificaciones.q2_p2
                    }}</button>
                  <span v-else>{{ est.calificaciones.q2_p2 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q2_p3)">
                  <button v-if="esFaseActiva('Q2_P3')" class="btn btn-sm btn-outline-primary"
                    @click="abrirModalCriterios(est, 'q2_p3')"><i class="fas fa-edit"></i> {{ est.calificaciones.q2_p3
                    }}</button>
                  <span v-else>{{ est.calificaciones.q2_p3 }}</span>
                </td>
                <td :class="colorNota(est.calificaciones.q2_examen)">
                  <input v-if="esFaseActiva('Q2_EXAMEN')" type="number" step="0.01" min="0" max="10"
                    class="form-control form-control-sm text-center mx-auto" style="width: 70px;"
                    v-model.number="est.calificaciones.q2_examen"
                    @input="validarNota(est.calificaciones, 'q2_examen'); recalcular(est)">
                  <span v-else>{{ est.calificaciones.q2_examen }}</span>
                </td>
                <td class="fw-bold bg-light border-end" :class="colorNota(est.calificaciones.q2_promedio)">{{
                  est.calificaciones.q2_promedio }}</td>

                <td class="fw-bold" :class="colorNota(est.calificaciones.promedio_anual)">{{
                  est.calificaciones.promedio_anual }}</td>
                <td>
                  <input v-if="esFaseActiva('SUPLETORIO') && est.calificaciones.estado_asignatura === 'Supletorio'"
                    type="number" step="0.01" class="form-control form-control-sm text-center mx-auto"
                    style="width: 60px;" v-model.number="est.calificaciones.nota_supletorio"
                    @input="validarNota(est.calificaciones, 'nota_supletorio'); recalcular(est)">
                  <span v-else class="text-muted small">{{ est.calificaciones.nota_supletorio || '-' }}</span>
                </td>

                <td>
                  <input v-if="esFaseActiva('REMEDIAL') && est.calificaciones.estado_asignatura === 'Remedial'"
                    type="number" step="0.01" class="form-control form-control-sm text-center mx-auto"
                    style="width: 60px;" v-model.number="est.calificaciones.nota_remedial"
                    @input="validarNota(est.calificaciones, 'nota_remedial'); recalcular(est)">
                  <span v-else class="text-muted small">{{ est.calificaciones.nota_remedial || '-' }}</span>
                </td>

                <td>
                  <input v-if="esFaseActiva('GRACIA') && est.calificaciones.estado_asignatura === 'Gracia'"
                    type="number" step="0.01" class="form-control form-control-sm text-center mx-auto"
                    style="width: 60px;" v-model.number="est.calificaciones.nota_gracia"
                    @input="validarNota(est.calificaciones, 'nota_gracia'); recalcular(est)">
                  <span v-else class="text-muted small">{{ est.calificaciones.nota_gracia || '-' }}</span>
                </td>

                <td class="fw-bold bg-light" :class="colorNota(est.calificaciones.nota_final_definitiva)">
                  {{ est.calificaciones.nota_final_definitiva }}
                </td>

                <td>
                  <span class="badge" :class="badgeEstado(est.calificaciones.estado_asignatura)">
                    {{ est.calificaciones.estado_asignatura }}
                  </span>
                </td>
              </tr>
              <tr v-if="estudiantesPaginados.length === 0">
                <td colspan="13" class="text-center py-4 text-muted">No se encontraron estudiantes</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center rounded-bottom-4">
          <small class="text-muted">Mostrando {{ estudiantesPaginados.length }} de {{ estudiantesFiltrados.length }}
            estudiantes</small>
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-blue" @click="paginaActual--" :disabled="paginaActual === 1"><i
                class="fas fa-chevron-left"></i> Anterior</button>
            <button class="btn btn-sm btn-outline-blue" @click="paginaActual++"
              :disabled="paginaActual >= totalPaginas">Siguiente <i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalCriterios" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
          <div class="modal-header bg-blue text-white rounded-top-4">
            <h5 class="modal-title fw-bold">Calificar Parcial: {{ modalData.faseLabel }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="alert alert-warning text-dark border-gold py-2 mb-4" v-if="modalData.estudiante">
              <i class="fas fa-user-graduate me-2"></i> {{ modalData.estudiante.nombres }} {{
                modalData.estudiante.apellidos }}
            </div>

            <div class="row g-3">
              <div class="col-6">
                <label class="form-label text-blue fw-bold small">Tareas</label>
                <input type="number" min="0" max="10" step="0.01" class="form-control"
                  v-model.number="modalData.criterios.tareas" @input="validarNota(modalData.criterios, 'tareas')">
              </div>
              <div class="col-6">
                <label class="form-label text-blue fw-bold small">Actividades Individuales</label>
                <input type="number" min="0" max="10" step="0.01" class="form-control"
                  v-model.number="modalData.criterios.individual"
                  @input="validarNota(modalData.criterios, 'individual')">
              </div>
              <div class="col-6">
                <label class="form-label text-blue fw-bold small">Actividades Grupales</label>
                <input type="number" min="0" max="10" step="0.01" class="form-control"
                  v-model.number="modalData.criterios.grupal" @input="validarNota(modalData.criterios, 'grupal')">
              </div>
              <div class="col-6">
                <label class="form-label text-blue fw-bold small">Lecciones</label>
                <input type="number" min="0" max="10" step="0.01" class="form-control"
                  v-model.number="modalData.criterios.lecciones" @input="validarNota(modalData.criterios, 'lecciones')">
              </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded text-center">
              <h6 class="mb-0 text-blue fw-bold">Promedio Parcial Calculado:</h6>
              <h3 class="mb-0 text-gold fw-bold mt-2" :class="colorNota(promedioCriterios)">{{ promedioCriterios }} / 10
              </h3>
            </div>
          </div>
          <div class="modal-footer border-0 d-grid px-4 pb-4">
            <button type="button" class="btn btn-gold fw-bold py-2" @click="aplicarPromedioParcial">Aplicar y
              Cerrar</button>
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
import * as bootstrap from 'bootstrap';

export default {
  data() {
    return {
      idpersona: 0,
      asignaturas: [],
      asignaturaSeleccionada: "",
      estudiantes: [],
      fasesActivas: [],
      busqueda: "",
      cargandoEstudiantes: false,
      guardando: false,

      // Paginación
      paginaActual: 1,
      itemsPorPagina: 15,

      // Modal
      modalInstancia: null,
      modalData: {
        estRef: null, // Referencia al objeto estudiante original
        estudiante: null,
        faseKey: '',
        faseLabel: '',
        criterios: { tareas: 0, individual: 0, grupal: 0, lecciones: 0 }
      },
      baseUrl: "/sistma"
    }
  },
  computed: {
    estudiantesFiltrados() {
      // 1. Nos aseguramos de que 'this.estudiantes' siempre sea tratado como un Array
      const lista = Array.isArray(this.estudiantes) ? this.estudiantes : [];

      if (!this.busqueda) return lista;

      const t = this.busqueda.toLowerCase();
      return lista.filter(e =>
        e.estudiante && (
          (e.estudiante.nombres && e.estudiante.nombres.toLowerCase().includes(t)) ||
          (e.estudiante.apellidos && e.estudiante.apellidos.toLowerCase().includes(t)) ||
          (e.estudiante.cedula && e.estudiante.cedula.includes(t))
        )
      );
    },
    estudiantesPaginados() {
      // 2. Nos aseguramos de que el filtro devuelva algo válido antes de hacer slice
      const filtrados = this.estudiantesFiltrados || [];
      const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
      return filtrados.slice(inicio, inicio + this.itemsPorPagina);
    },
    totalPaginas() {
      const filtrados = this.estudiantesFiltrados || [];
      return Math.ceil(filtrados.length / this.itemsPorPagina) || 1;
    },
    promedioCriterios() {
      const { tareas, individual, grupal, lecciones } = this.modalData.criterios;
      const sum = (Number(tareas) || 0) + (Number(individual) || 0) + (Number(grupal) || 0) + (Number(lecciones) || 0);
      return (sum / 4).toFixed(2);
    }
  },
  watch: {
    busqueda() { this.paginaActual = 1; }
  },
  async mounted() {
    const me = await getMe();
    this.idpersona = me.id_persona;
    this.cargarAsignaturas();
  },
  methods: {
    validarNota(objetoDestino, propiedad) {
      let valor = parseFloat(objetoDestino[propiedad]);

      // Si el campo está vacío o no es un número, no hacemos nada para permitir que borren el campo
      if (isNaN(valor)) return;

      // Forzar límites físicos
      if (valor > 10) {
        objetoDestino[propiedad] = 10;
      } else if (valor < 0) {
        objetoDestino[propiedad] = 0;
      }
    },
    async cargarAsignaturas() {
      try {
        const response = await API.get(`${this.baseUrl}/asignaturas-docente/${this.idpersona}`);
        
        this.asignaturas = response.data;
      } catch (error) {
        mostraralertas("Error al cargar las asignaturas", "error");
      }
    },
    async cargarEstudiantes() {
      if (!this.asignaturaSeleccionada) return;
      this.cargandoEstudiantes = true;
      try {
        const response = await API.get(`${this.baseUrl}/estudiantes-asigna/${this.asignaturaSeleccionada}`);
        this.estudiantes = response.data.estudiantes;
        this.fasesActivas = response.data.fases_activas; // ej: ["Q1_P1", "Q1_EXAMEN"]
        this.paginaActual = 1;
      } catch (error) {
        mostraralertas("Error al cargar los estudiantes", "error");
      } finally {
        this.cargandoEstudiantes = false;
      }
    },
    esFaseActiva(fase) {
      return this.fasesActivas.includes(fase);
    },

    // ------- LÓGICA DE CÁLCULO --------
    recalcular(est) {
      let n = est.calificaciones;

      // 1. CÁLCULO BÁSICO QUIMESTRES (Mantenemos tu lógica)
      const calcQuim = (p1, p2, p3, ex) => {
        let promP = (Number(p1) + Number(p2) + Number(p3)) / 3;
        return (promP * 0.8 + Number(ex) * 0.2).toFixed(2);
      };

      n.q1_promedio = calcQuim(n.q1_p1, n.q1_p2, n.q1_p3, n.q1_examen);
      n.q2_promedio = calcQuim(n.q2_p1, n.q2_p2, n.q2_p3, n.q2_examen);

      // 2. PROMEDIO ANUAL
      n.promedio_anual = ((Number(n.q1_promedio) + Number(n.q2_promedio)) / 2).toFixed(2);

      // Inicializamos la nota final con el promedio anual
      let notaFinalCalculada = Number(n.promedio_anual);
      let estado = '';

      // 3. LÓGICA DE ESTADOS Y RECUPERACIÓN (Reglamento LOEI)

      if (notaFinalCalculada >= 7) {
        estado = 'Aprobado';
      }
      else if (notaFinalCalculada >= 5 && notaFinalCalculada < 7) {
        // --- CASO SUPLETORIO ---
        estado = 'Supletorio';
        if (Number(n.nota_supletorio) >= 7) {
          notaFinalCalculada = 7.00;
          estado = 'Aprobado';
        } else if (n.nota_supletorio !== null && n.nota_supletorio < 7) {
          estado = 'Remedial'; // Falló supletorio, va a remedial
        }
      }
      else {
        // --- CASO REMEDIAL DIRECTO (Menor a 5) ---
        estado = 'Remedial';
      }

      // --- EVALUAR REMEDIAL ---
      if (estado === 'Remedial') {
        if (Number(n.nota_remedial) >= 7) {
          notaFinalCalculada = 7.00;
          estado = 'Aprobado';
        } else if (n.nota_remedial !== null && n.nota_remedial < 7) {
          estado = 'Gracia'; // Falló remedial, va a gracia
        }
      }

      // --- EVALUAR GRACIA ---
      if (estado === 'Gracia') {
        if (Number(n.nota_gracia) >= 7) {
          notaFinalCalculada = 7.00;
          estado = 'Aprobado';
        } else if (n.nota_gracia !== null && n.nota_gracia < 7) {
          estado = 'Reprobado';
        }
      }

      // Asignar resultados finales
      n.nota_final_definitiva = notaFinalCalculada.toFixed(2);
      n.estado_asignatura = estado;
    },
    badgeEstado(estado) {
      switch (estado) {
        case 'Aprobado': return 'bg-success';
        case 'Supletorio': return 'bg-warning text-dark';
        case 'Remedial': return 'bg-info';
        case 'Gracia': return 'bg-primary'; // Azul para diferenciar
        case 'Reprobado': return 'bg-danger';
        default: return 'bg-secondary';
      }
    },

    // ------- MODAL Y CRITERIOS --------
    abrirModalCriterios(est, faseKey) {
      this.modalData.estRef = est;
      this.modalData.estudiante = est.estudiante;
      this.modalData.faseKey = faseKey;
      this.modalData.faseLabel = faseKey.toUpperCase().replace('_', ' - ');

      // Reiniciar inputs (como no se guardan en BD, inician en 0 o en el valor equivalente al promedio)
      let notaActual = Number(est.calificaciones[faseKey]) || 0;
      this.modalData.criterios = { tareas: notaActual, individual: notaActual, grupal: notaActual, lecciones: notaActual };

      if (!this.modalInstancia) {
        this.modalInstancia = new bootstrap.Modal(document.getElementById('modalCriterios'));
      }
      this.modalInstancia.show();
    },
    aplicarPromedioParcial() {
      // Pasamos el promedio calculado a la nota de la tabla
      this.modalData.estRef.calificaciones[this.modalData.faseKey] = this.promedioCriterios;

      // Forzamos recalculo general de promedios para ese estudiante
      this.recalcular(this.modalData.estRef);

      this.modalInstancia.hide();
    },

    // ------- GUARDADO --------
    async guardarTodasLasCalificaciones() {
      this.guardando = true;
      try {
        const payload = { estudiantes: this.estudiantes };
        const response = await API.post(`${this.baseUrl}/calificaciones-guardar`, payload);
        mostraralertas(response.data.mensaje, "success");
      } catch (error) {
        mostraralertas("Error al guardar calificaciones", "error");
      } finally {
        this.guardando = false;
      }
    },

    // ------- UI HELPERS --------
    colorNota(nota) {
      return Number(nota) < 7 ? 'text-danger fw-bold' : '';
    },
  }
}
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}

.text-gold {
  color: #F4B324;
}

.bg-blue {
  background-color: #1D2A68;
}

.border-gold {
  border-color: #F4B324 !important;
}

.bg-gold {
  background-color: #F4B324;
  color: #1D2A68;
}

.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}

.btn-gold:hover {
  background-color: #d99d1e;
  color: #fff;
}

.btn-outline-blue {
  border: 2px solid #1D2A68;
  color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

/* Para esconder las flechitas de los input type number */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
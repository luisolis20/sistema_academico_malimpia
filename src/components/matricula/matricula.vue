<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header
      class="mb-4 d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5">
      <div>
        <h2 class="fw-bold text-blue mb-1">Gestión de Matrículas periodo {{ periodo_activo.nombre }}</h2>
        <p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>Seleccione un familiar para iniciar el proceso
          de asignación académica.</p>
      </div>
      <div class="text-end">
        <span class="badge bg-blue px-3 py-2 rounded-pill">Ciclo Costa</span>
      </div>
    </header>

    <div class="row">
      <div class="col-lg-4">
        <h5 class="fw-bold text-blue mb-3 px-2">1. Seleccionar Estudiante</h5>
        <div v-if="cargandoFamilia" class="text-center p-5">
          <div class="spinner-border text-gold"></div>
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

        <div class="list-group shadow-sm rounded-4 overflow-hidden border-0">
          <button v-for="f in familiares" :key="f.id_persona" @click="seleccionarFamiliar(f)"
            :disabled="estaMatriculado(f.id_persona)"
            class="list-group-item list-group-item-action border-0 p-3 mb-1 transition-all"
            :class="{ 'active-selection': familiarSel?.id_persona === f.id_persona, 'bg-disabled': estaMatriculado(f.id_persona) }">
            <div class="d-flex align-items-center">
              <div class="position-relative">
                <img :src="f.foto ? 'data:image/jpeg;base64,' + f.foto : getPhotoUrl(null)"
                  class="rounded-circle shadow-sm border border-2" width="55" height="55">
                <i v-if="estaMatriculado(f.id_persona)"
                  class="fas fa-check-circle text-success position-absolute bottom-0 end-0 bg-white rounded-circle"></i>
              </div>
              <div class="ms-3 overflow-hidden">
                <h6 class="mb-0 fw-bold" :class="familiarSel?.id_persona === f.id_persona ? 'text-white' : 'text-blue'">
                  {{ f.nombres }}</h6>
                <small :class="familiarSel?.id_persona === f.id_persona ? 'text-white-50' : 'text-muted'">CI: {{
                  f.cedula }}</small>
                <div v-if="estaMatriculado(f.id_persona)" class="text-success x-small fw-bold mt-1">YA MATRICULADO</div>
              </div>
            </div>
          </button>
        </div>
      </div>

      <div class="col-lg-8">
        <div v-if="!familiarSel"
          class="h-100 d-flex flex-column justify-content-center align-items-center bg-white rounded-4 shadow-sm border p-5">
          <i class="fas fa-user-graduate fa-4x text-light mb-3"></i>
          <h5 class="text-muted">Seleccione un familiar a la izquierda</h5>
        </div>

        <div v-else class="animate__animated animate__fadeIn">
          <h5 class="fw-bold text-blue mb-3 px-2">2. Oferta Académica Disponible</h5>

          <div class="alert bg-blue text-white rounded-4 shadow-sm border-0 d-flex align-items-center mb-4">
            <i class="fas fa-user-edit fa-2x me-3"></i>
            <div>
              <p class="mb-0 small opacity-75">Matriculando a:</p>
              <h5 class="mb-0 fw-bold">{{ familiarSel.nombres }} {{ familiarSel.apellidos }}</h5>
            </div>
          </div>

          <div v-if="cronogramas.length === 0" class="alert alert-warning rounded-4">
            No hay cronogramas de matrícula activos para este periodo.
          </div>

          <div class="row g-3">
            <div v-for="c in cronogramas" :key="c.id_cronograma" class="col-md-6">
              <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-gold text-blue fw-bold border-0 py-3">
                  <i class="fas fa-calendar-check me-2"></i> {{ c.nivel.nombre }}
                </div>
                <div class="card-body">
                  <p class="small text-muted mb-2"><strong>Especialidad:</strong> {{ c.especialidad.nombre }}</p>
                  <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded-3 mb-3">
                    <span class="small fw-bold">Finaliza en:</span>
                    <span class="badge bg-danger">{{ calcularDias(c.fecha_fin) }} días</span>
                  </div>

                  <button @click="cargarCursos(c)" class="btn btn-outline-blue w-100 fw-bold rounded-pill">
                    Ver Paralelos Disponibles
                  </button>

                  <div v-if="cronogramaSel === c.id_cronograma" class="mt-3 animate__animated animate__slideInDown">
                    <select v-model="cursoSel" class="form-select border-gold rounded-3 mb-2">
                      <option :value="null" disabled>Seleccione un paralelo</option>
                      <option v-for="curso in cursos" :key="curso.id_curso" :value="curso">
                        Paralelo "{{ curso.paralelo }}"
                      </option>
                    </select>
                    <button v-if="cursoSel" @click="confirmarMatricula"
                      class="btn btn-gold w-100 fw-bold text-blue shadow-sm">
                      <i class="fas fa-save me-2"></i> FINALIZAR MATRÍCULA
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
      <div class="mt-5">
        <h5 class="fw-bold text-blue mb-3 px-2">Historial de Matrículas Realizadas</h5>
        <div class="card border-0 shadow-sm rounded-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="border-0 px-4">Estudiante</th>
                  <th class="border-0">Curso/Paralelo</th>
                  <th class="border-0">Fecha</th>
                  <th class="border-0 text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in matriculasRealizadas" :key="m.id_matricula">
                  <td class="px-4 fw-bold text-blue">{{ m.estudiante_nombre }}</td>
                  <td>{{ m.nivel_nombre }} - "{{ m.paralelo }}"</td>
                  <td class="small text-muted">{{ m.fecha }}</td>
                  <td class="text-center">
                    <button @click="generarPDF(m)" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm">
                      <i class="fas fa-file-pdf me-1"></i> Comprobante
                    </button>
                  </td>
                </tr>
                <tr v-if="matriculasRealizadas.length === 0">
                  <td colspan="4" class="text-center py-4 text-muted small">No ha realizado matrículas todavía.</td>
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
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { getMe } from "@/assets/js/auth";
import Swal from 'sweetalert2';
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: 0,
      familiares: [],
      cronogramas: [],
      cursos: [],
      matriculasRealizadas: [], // Para la tabla
      familiarSel: null,
      cronogramaSel: null,
      cursoSel: null,
      cargandoFamilia: false,
      cargando: false,
      Persona: {},
      periodo_activo: {}
    }
  },
  async mounted() {
    const me = await getMe();
    this.idpersona = me.id_persona;
    await Promise.all([this.getFamiliares(),
    this.getHistorial(), this.getPersona(), this.getPeriodo()]);

  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async getPeriodo() {
      try {
        const res = await API.get(`${this.baseUrl}/periodos_lectivos_activos`);
        this.periodo_activo = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async getFamiliares() {
      this.cargandoFamilia = true;
      try {
        const res = await API.get(`${this.baseUrl}/familiares-de/${this.idpersona}`);
        this.familiares = res.data.data;
      } catch (e) { console.error(e); }
      finally { this.cargandoFamilia = false; }
    },
    async getCronogramas(id_estudiante) {
      try {
        const res = await API.get(`${this.baseUrl}/cronograma_matriculas_activo/${id_estudiante}`);
        this.cronogramas = res.data;
      } catch (err) {
        console.error(err);
      }
    },
    async getHistorial() {
      const res = await API.get(`${this.baseUrl}/historial/${this.idpersona}`);
      this.matriculasRealizadas = res.data;
    },
    seleccionarFamiliar(f) {
      this.familiarSel = f;
      this.cursoSel = null;
      this.cronogramaSel = null;

      // Si NO está matriculado, le buscamos sus cronogramas disponibles
      if (!this.estaMatriculado(f.id_persona)) {
        this.getCronogramas(f.id_persona);
      } else {
        // Si ya está matriculado, limpiamos la lista para que no vea ofertas
        this.cronogramas = [];
      }
    },
    async cargarCursos(cronograma) {
      this.cronogramaSel = cronograma.id_cronograma;
      const res = await API.get(`${this.baseUrl}/cursos_por_cronograma/${cronograma.id_cronograma}`);

      this.cursos = res.data;
    },
    estaMatriculado(id_persona) {
      return this.matriculasRealizadas.some(m => m.id_estudiante === id_persona);
    },
    calcularDias(fecha) {
      const fin = new Date(fecha);
      const hoy = new Date();
      const diff = fin - hoy;
      return Math.ceil(diff / (1000 * 60 * 60 * 24));
    },
    async confirmarMatricula() {
      const result = await Swal.fire({
        title: '¿Confirmar Matrícula?',
        text: `Se matriculará a ${this.familiarSel.nombres} en el paralelo "${this.cursoSel.paralelo}"`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1D2A68',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, matricular'
      });

      if (result.isConfirmed) {
        try {
          const payload = {
            id_estudiante: this.familiarSel.id_persona,
            id_curso: this.cursoSel.id_curso,
            id_representante: this.idpersona,
            id_periodo: this.cursoSel.id_periodo
          };
          await API.post(`${this.baseUrl}/crearmatricula`, payload);
          mostraralertas("¡Matrícula Exitosa!", "success");
          this.familiarSel = null;
          await this.getHistorial();
        } catch (e) {
          mostraralertas(e.response.data.mensaje || "Error al matricular", "error");
        }
      }
    },
    generarPDF(m) {
      console.log(m);
      const doc = new jsPDF();

      // 2. Definición de colores institucionales
      const azulMarino = [29, 42, 104];
      const oro = [244, 179, 36];
      const grisOscuro = [60, 60, 60];

      // --- ENCABEZADO ---
      // Rectángulo azul superior
      doc.setFillColor(...azulMarino);
      doc.rect(0, 0, 210, 45, 'F');

      // Texto del encabezado
      doc.setTextColor(255, 255, 255);
      doc.setFont("helvetica", "bold");
      doc.setFontSize(20);
      doc.text("COMPROBANTE OFICIAL DE MATRÍCULA", 105, 22, { align: "center" });

      doc.setFontSize(10);
      doc.setFont("helvetica", "normal");
      doc.text("SISTEMA DE GESTIÓN ACADÉMICA - PERIODO 2026", 105, 32, { align: "center" });

      // --- DATOS DEL ESTUDIANTE ---
      doc.setTextColor(...azulMarino);
      doc.setFontSize(14);
      doc.setFont("helvetica", "bold");
      doc.text("DATOS DEL ESTUDIANTE", 14, 60);

      // Línea decorativa color Oro
      doc.setDrawColor(...oro);
      doc.setLineWidth(1);
      doc.line(14, 62, 75, 62);

      // Bloque de información
      doc.setTextColor(...grisOscuro);
      doc.setFontSize(11);
      const startY = 75;

      // Etiquetas en negrita
      doc.setFont("helvetica", "bold");
      doc.text("Nombres Completos:", 14, startY);
      doc.text("Cédula de Identidad:", 14, startY + 10);
      doc.text("Representante:", 14, startY + 20);
      doc.text("Fecha de Matrícula:", 14, startY + 30);

      // Valores en fuente normal
      doc.setFont("helvetica", "normal");
      doc.text(m.estudiante_nombre, 60, startY);
      doc.text(m.estudiante_cedula, 60, startY + 10);
      // Asumiendo que 'this.Persona' tiene los datos del usuario logueado
      doc.text(`${this.Persona.nombres} ${this.Persona.apellidos}`, 60, startY + 20);
      doc.text(m.fecha, 60, startY + 30);

      // --- TABLA DE DETALLE (Usando el plugin corregido) ---
      autoTable(doc, {
        startY: 120,
        head: [['NIVEL ACADÉMICO', 'ESPECIALIDAD', 'PARALELO']],
        body: [
          [m.nivel_nombre, m.especialidad, m.paralelo]
        ],
        headStyles: {
          fillColor: azulMarino,
          textColor: [255, 255, 255],
          fontStyle: 'bold',
          halign: 'center'
        },
        styles: {
          halign: 'center',
          lineColor: [220, 220, 220],
          lineWidth: 0.1,
          fontSize: 11
        },
        theme: 'grid'
      });

      // --- SECCIÓN DE FIRMAS ---
      // Obtenemos la posición final de la tabla para dibujar las firmas debajo
      const finalY = doc.lastAutoTable.finalY + 45;

      doc.setDrawColor(180, 180, 180);
      doc.setLineWidth(0.5);

      // Línea izquierda (Representante)
      doc.line(30, finalY, 85, finalY);
      // Línea derecha (Secretaría)
      doc.line(125, finalY, 180, finalY);

      doc.setFontSize(9);
      doc.setTextColor(...grisOscuro);
      doc.text("Firma del Representante", 57.5, finalY + 7, { align: "center" });
      doc.text("Secretaría / Admisiones", 152.5, finalY + 7, { align: "center" });

      // --- PIE DE PÁGINA ---
      doc.setFontSize(8);
      doc.setTextColor(150, 150, 150);
      doc.text("Este documento es un comprobante oficial de asignación de cupo.", 105, 280, { align: "center" });
      doc.text("Cualquier alteración anula la validez de este comprobante.", 105, 285, { align: "center" });

      // Franja decorativa inferior color Oro
      doc.setFillColor(...oro);
      doc.rect(0, 292, 210, 5, 'F');

      // 3. Descargar el archivo
      doc.save(`Comprobante_${m.estudiante_cedula}.pdf`);
    }
  }
}
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}

.bg-blue {
  background-color: #1D2A68;
}

.border-gold {
  border-color: #F4B324 !important;
}

.bg-gold {
  background-color: #F4B324;
}

.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}

.btn-outline-blue {
  border: 2px solid #1D2A68;
  color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

.active-selection {
  background-color: #1D2A68 !important;
  transform: translateX(10px);
}

.bg-disabled {
  background-color: #f8f9fa !important;
  opacity: 0.7;
  cursor: not-allowed;
}

.transition-all {
  transition: all 0.3s ease;
}

.x-small {
  font-size: 0.65rem;
}
</style>
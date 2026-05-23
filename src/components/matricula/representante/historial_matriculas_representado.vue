<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5 no-print">
      <div class="row align-items-center">
        <div class="col-md-12">
          <h2 class="fw-bold text-blue mb-1">
            <i class="fas fa-id-card me-2 text-gold"></i>Historial de Matrículas del Representado
          </h2>
          <p class="text-muted mb-0">
            <i class="fas fa-info-circle me-2 text-gold"></i>
            Seleccione a su representado para consultar sus actas de matrícula, docentes tutores y asignaturas por cada periodo cursado.
          </p>
        </div>
      </div>
    </header>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-blue" role="status">
        <span class="visually-hidden">Cargando representados...</span>
      </div>
      <p class="mt-2 text-muted fw-bold">Obteniendo listado de representados...</p>
    </div>

    <div v-else-if="familiares.length === 0" class="alert alert-info rounded-4 p-4 text-center shadow-sm">
      <i class="fas fa-users-slash fa-2x text-blue mb-2"></i>
      <h5>Sin representados</h5>
      <p class="mb-0 text-muted">Actualmente no consta como representante de ningún estudiante.</p>
    </div>

    <div v-else class="accordion mb-5" id="accordionEstudiantes">
      <div v-for="f in familiares" :key="f.id_persona" class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        
        <h2 class="accordion-header" :id="'heading-est-' + f.id_persona">
          <button class="accordion-button collapsed bg-white text-blue fw-bold p-3" 
                  type="button" 
                  data-bs-toggle="collapse" 
                  :data-bs-target="'#collapse-est-' + f.id_persona" 
                  aria-expanded="false" 
                  :aria-controls="'collapse-est-' + f.id_persona"
                  @click="cargarHistorialMatriculas(f.id_persona)">
            
            <div class="d-flex align-items-center gap-3 w-100 pe-3">
              <img v-if="f.foto" :src="'data:image/jpeg;base64,' + f.foto" class="rounded-circle border border-gold object-cover" style="width: 50px; height: 50px;" alt="Perfil"/>
              <div v-else class="bg-blue text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-user-graduate fa-lg"></i>
              </div>
              
              <div>
                <h5 class="mb-0 fw-bold text-blue">{{ f.nombres }} {{ f.apellidos }}</h5>
                <span class="small text-muted">
                  <span class="badge bg-gold text-blue me-2 text-uppercase font-black">{{ f.parentesco }}</span>
                  | Cédula: <strong>{{ f.cedula }}</strong>
                </span>
              </div>
            </div>
          </button>
        </h2>

        <div :id="'collapse-est-' + f.id_persona" class="accordion-collapse collapse" :aria-labelledby="'heading-est-' + f.id_persona" data-bs-parent="#accordionEstudiantes">
          <div class="accordion-body bg-light border-top p-4">
            
            <div v-if="historialMatriculas[f.id_persona]?.loading" class="text-center py-4">
              <div class="spinner-border spinner-border-sm text-blue" role="status"></div>
              <p class="small text-muted mt-2 mb-0">Buscando matrículas de {{ f.nombres.split(' ')[0] }}...</p>
            </div>

            <div v-else-if="!historialMatriculas[f.id_persona] || historialMatriculas[f.id_persona].data.length === 0" class="text-center py-3 text-muted">
              <i class="fas fa-folder-open mb-2 fa-2x"></i>
              <p class="mb-0">Este estudiante no registra matrículas en el sistema.</p>
            </div>

            <div v-else class="accordion" :id="'accordionMatriculas-' + f.id_persona">
              <div v-for="mat in historialMatriculas[f.id_persona].data" :key="mat.id_matricula" class="accordion-item border shadow-sm rounded-3 mb-2 overflow-hidden transition-all">
                
                <h2 class="accordion-header" :id="'heading-mat-' + mat.id_matricula">
                  <button class="accordion-button collapsed bg-white text-blue fw-bold p-3 fs-6" 
                          type="button" 
                          data-bs-toggle="collapse" 
                          :data-bs-target="'#collapse-mat-' + mat.id_matricula" 
                          aria-expanded="false" 
                          :aria-controls="'collapse-mat-' + mat.id_matricula">
                    <div class="d-flex align-items-center justify-content-between w-100 pe-3">
                      <div>
                        <i class="fas fa-graduation-cap me-2 text-gold"></i>
                        {{ mat.nivel }} — Paralelo "{{ mat.paralelo }}"
                        <span class="badge bg-blue ms-2 fs-7 font-normal">{{ mat.periodo }}</span>
                      </div>
                      <span class="badge rounded-pill text-uppercase px-3 py-1 fs-7 bg-gold text-blue font-black">
                        {{ mat.estado_matricula }}
                      </span>
                    </div>
                  </button>
                </h2>

                <div :id="'collapse-mat-' + mat.id_matricula" class="accordion-collapse collapse" :aria-labelledby="'heading-mat-' + mat.id_matricula" :data-bs-parent="'#accordionMatriculas-' + f.id_persona">
                  <div class="accordion-body bg-white border-top p-4">
                    
                    <div class="row g-3 mb-4">
                      <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-blue border-3">
                          <small class="text-muted d-block text-uppercase fw-bold x-small">Periodo Lectivo</small>
                          <span class="fw-bold text-blue">{{ mat.periodo }}</span>
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-blue border-3">
                          <small class="text-muted d-block text-uppercase fw-bold x-small">Fecha de Matrícula</small>
                          <span class="fw-bold text-dark">{{ mat.fecha_matricula }}</span>
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-blue border-3">
                          <small class="text-muted d-block text-uppercase fw-bold x-small">Docente Tutor</small>
                          <span class="fw-bold text-dark">{{ mat.tutor }}</span>
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-blue border-3">
                          <small class="text-muted d-block text-uppercase fw-bold x-small">Matriculado por</small>
                          <span class="fw-bold text-dark">{{ mat.representante }}</span>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
                      <h6 class="fw-bold text-blue mb-2 mb-sm-0">
                        <i class="fas fa-book me-2 text-gold"></i>Malla Curricular Asignada
                      </h6>
                      <button @click="generarPDFMatricula(mat, f)" class="btn btn-gold btn-sm rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-file-pdf me-2"></i>Descargar Certificado
                      </button>
                    </div>

                    <div class="table-responsive rounded-3 border">
                      <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="bg-blue text-white">
                          <tr>
                            <th scope="col" class="py-2 px-4 text-start" style="width: 60px;">#</th>
                            <th scope="col" class="py-2 text-start">Asignatura</th>
                            <th scope="col" class="py-2 text-center" style="width: 120px;">Tipo</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(asig, aIdx) in mat.asignaturas" :key="asig.id_asignatura">
                            <td class="px-4 text-start fw-bold text-muted">{{ aIdx + 1 }}</td>
                            <td class="text-start fw-bold text-secondary">{{ asig.nombre }}</td>
                            <td class="text-center">
                              <span class="badge bg-light text-blue border border-blue px-2 py-1 rounded-2 small">Regular</span>
                            </td>
                          </tr>
                          <tr v-if="mat.asignaturas.length === 0">
                            <td colspan="3" class="text-center py-3 text-muted">
                              Sin asignaturas registradas en esta matrícula.
                            </td>
                          </tr>
                        </tbody>
                      </table>
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
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import { getMe } from "@/assets/js/auth";

export default {
  name: "HistorialMatriculasEstudiante",
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: null,
      loading: true,
      Persona: {},
      familiares: [], 
      historialMatriculas: {}, // Objeto para almacenar matrículas por ID de estudiante
    }
  },
  async mounted() {
    try {
      const me = await getMe();
      this.idpersona = me.id_persona;
      await Promise.all([this.getPersona(), this.getFamiliares()]);
    } catch (error) {
      console.error("Error inicializando componente:", error);
      mostraralertas("Error al autenticar usuario", "error");
    } finally {
      this.loading = false;
    }
  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { console.error("Error perfil rep:", err); }
    },
    async getFamiliares() {
      try {
        const res = await API.get(`${this.baseUrl}/familiares-de/${this.idpersona}`);
        this.familiares = res.data.data || [];
      } catch (e) {
        console.error("Error al traer familiares:", e);
        mostraralertas("No se pudo obtener la nómina de representados", "error");
      }
    },
    async cargarHistorialMatriculas(studentId) {
      // Lazy loading: si ya están cargadas, no hacer la petición web de nuevo
      if (this.historialMatriculas[studentId]) return;

      this.historialMatriculas[studentId] = { loading: true, data: [] };

      try {
        const res = await API.get(`${this.baseUrl}/historial-matriculas-est/${studentId}`);
        this.historialMatriculas[studentId].data = res.data.data || [];
      } catch (error) {
        console.error(`Error trayendo matriculas para ID ${studentId}:`, error);
        mostraralertas("No se pudo obtener el historial del estudiante", "error");
      } finally {
        this.historialMatriculas[studentId].loading = false;
      }
    },
    generarPDFMatricula(mat, estudiante) {
      const doc = new jsPDF("p", "mm", "a4");

      // Banner Principal
      doc.setFillColor(29, 42, 104);
      doc.rect(0, 0, 210, 24, "F");
      
      doc.setTextColor(244, 181, 36);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(14);
      doc.text("CERTIFICADO DE MATRÍCULA OFICIAL", 14, 11);
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(9);
      doc.setFont("Helvetica", "normal");
      doc.text(`Sistema de Gestión Académica — Periodo Lectivo: ${mat.periodo}`, 14, 18);

      // Cuadro de Información General del Acta
      doc.setFillColor(248, 249, 250);
      doc.rect(14, 30, 182, 34, "F");
      doc.setDrawColor(220, 224, 230);
      doc.rect(14, 30, 182, 34, "S");

      doc.setTextColor(29, 42, 104);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(9);
      doc.text("DATOS DEL ESTUDIANTE", 18, 36);
      doc.text("DETALLES DEL CURSO", 115, 36);

      doc.setDrawColor(244, 181, 36);
      doc.line(18, 38, 60, 38);
      doc.line(115, 38, 150, 38);

      doc.setTextColor(0, 0, 0);
      doc.setFontSize(8.5);
      // Usamos los datos del estudiante seleccionado
      doc.text(`Nombres: ${estudiante.nombres} ${estudiante.apellidos}`, 18, 44);
      doc.text(`Cédula / ID: ${estudiante.cedula}`, 18, 50);
      doc.text(`Representante: ${mat.representante}`, 18, 56);

      doc.text(`Curso: ${mat.nivel} "${mat.paralelo}"`, 115, 44);
      doc.text(`Especialidad: ${mat.especialidad}`, 115, 50);
      doc.text(`Docente Tutor: ${mat.tutor}`, 115, 56);
      doc.text(`Fecha Inscripción: ${mat.fecha_matricula}`, 115, 61);

      // Tabla de Malla Curricular
      const bodyAsig = mat.asignaturas.map((asig, i) => [
        (i + 1).toString(),
        asig.nombre,
        "REGULAR"
      ]);

      autoTable(doc, {
        startY: 70,
        head: [['#', 'Asignatura Registrada', 'Tipo de Materia']],
        body: bodyAsig,
        theme: 'striped',
        headStyles: { fillColor: [29, 42, 104], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 9 },
        styles: { fontSize: 8.5, cellPadding: 3, valign: 'middle' },
        columnStyles: {
          0: { cellWidth: 15, halign: 'center' },
          1: { halign: 'left' },
          2: { cellWidth: 35, halign: 'center' }
        }
      });

      // Firmas de Responsabilidad al final del documento
      const finalY = doc.lastAutoTable.finalY + 35;
      
      doc.setDrawColor(180, 180, 180);
      doc.line(30, finalY, 80, finalY);
      doc.line(130, finalY, 180, finalY);

      doc.setFontSize(8.5);
      doc.setFont("Helvetica", "bold");
      doc.text("Firma del Representante", 40, finalY + 5);
      doc.text("Secretaría General", 143, finalY + 5);

      doc.setFont("Helvetica", "normal");
      doc.setFontSize(7.5);
      doc.setTextColor(120, 120, 120);
      doc.text(`Documento generado electrónicamente el ${new Date().toLocaleDateString()}`, 14, 285);

      // Guardar PDF con nombre del estudiante incluido
      doc.save(`Acta_Matricula_${estudiante.nombres.split(' ')[0]}_${mat.periodo.replace(/\s+/g, '_')}.pdf`);
    }
  }
}
</script>

<style scoped>
.text-blue { color: #1D2A68; }
.text-gold { color: #F4B324; }
.bg-blue { background-color: #1D2A68; }
.border-gold { border-color: #F4B324 !important; }
.bg-gold { background-color: #F4B324; }
.border-blue { border-color: #1D2A68 !important; }

.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}
.btn-gold:hover {
  background-color: #e0a216;
  color: #1D2A68;
}

.font-black { font-weight: 800; }
.transition-all { transition: all 0.3s ease; }
.x-small { font-size: 0.7rem; letter-spacing: 0.3px; }
.fs-7 { font-size: 0.78rem !important; }
.object-cover { object-fit: cover; }

/* Estilos Personalizados del Acordeón para conservar la línea gráfica */
.accordion-item {
  border-color: rgba(29, 42, 104, 0.1) !important;
}

/* Acordeón Nivel 1 (Estudiantes) */
#accordionEstudiantes > .accordion-item > .accordion-header .accordion-button:not(.collapsed) {
  background-color: rgba(29, 42, 104, 0.05) !important;
  color: #1D2A68 !important;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
  border-left: 5px solid #F4B324 !important;
}

/* Acordeón Nivel 2 (Matrículas anidadas) */
.accordion-body .accordion-button:not(.collapsed) {
  background-color: rgba(244, 181, 36, 0.1) !important;
  color: #1D2A68 !important;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
  border-left: 4px solid #1D2A68 !important;
}

.accordion-button:focus {
  box-shadow: none;
  border-color: rgba(29, 42, 104, 0.2);
}
</style>
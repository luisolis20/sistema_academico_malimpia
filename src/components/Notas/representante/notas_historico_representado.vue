<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5 no-print">
      <div class="row align-items-center g-3">
        <div class="col-md-12">
          <h2 class="fw-bold text-blue mb-1">
            <i class="fas fa-users me-2 text-gold"></i>Histórico de Notas de Representados
          </h2>
          <p class="text-muted mb-0">
            Despliegue el acordeón de cualquiera de sus representados registrados para consultar su trayectoria académica completa y exportar sus reportes oficiales.
          </p>
        </div>
      </div>
    </header>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-blue" role="status">
        <span class="visually-hidden">Cargando representados...</span>
      </div>
      <p class="mt-2 text-muted fw-bold">Obteniendo nómina de representados...</p>
    </div>

    <div v-else-if="familiares.length === 0" class="alert alert-info rounded-4 p-4 text-center shadow-sm">
      <i class="fas fa-user-slash fa-2x text-blue mb-2"></i>
      <h5>Sin representados asignados</h5>
      <p class="mb-0 text-muted">Usted no consta como representante legal de ningún estudiante activo en este periodo.</p>
    </div>

    <div v-else class="accordion mb-5" id="accordionEstudiantes">
      <div v-for="(f, index) in familiares" :key="f.id_persona" class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        
        <h2 class="accordion-header" :id="'heading-' + f.id_persona">
          <button class="accordion-button collapsed bg-white text-blue fw-bold p-3" 
                  type="button" 
                  data-bs-toggle="collapse" 
                  :data-bs-target="'#collapse-' + f.id_persona" 
                  aria-expanded="false" 
                  :aria-controls="'collapse-' + f.id_persona"
                  @click="cargarHistorialEstudiante(f.id_persona)">
            
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
                  <span v-if="f.correo" class="d-none d-md-inline"> | Correo: {{ f.correo }}</span>
                </span>
              </div>
            </div>

          </button>
        </h2>

        <div :id="'collapse-' + f.id_persona" class="accordion-collapse collapse" :aria-labelledby="'heading-' + f.id_persona" data-bs-parent="#accordionEstudiantes">
          <div class="accordion-body bg-white border-top p-4">
            
            <div v-if="historiales[f.id_persona]?.loading" class="text-center py-4">
              <div class="spinner-border spinner-border-sm text-blue" role="status"></div>
              <p class="small text-muted mt-2 mb-0">Consultando base de datos académica...</p>
            </div>

            <div v-else-if="!historiales[f.id_persona] || historiales[f.id_persona].data.length === 0" class="text-center py-3 text-muted">
              <i class="fas fa-folder-open mb-2"></i>
              <p class="mb-0 small">Este estudiante no registra calificaciones finales históricas consolidadas.</p>
            </div>

            <div v-else>
              <div class="d-flex justify-content-end mb-3 no-print">
                <button @click="generarPDFHistorial(f)" class="btn btn-gold btn-sm rounded-pill shadow-sm px-4 fw-bold">
                  <i class="fas fa-file-pdf me-2"></i>Descargar PDF Historial ({{ f.nombres.split(' ')[0] }})
                </button>
              </div>

              <div class="table-responsive rounded-3 overflow-hidden">
                <table class="table table-bordered align-middle mb-0 text-center custom-table-grades">
                  <thead class="bg-blue text-white header-double-level">
                    <tr>
                      <th class="bg-blue-dark text-white">Periodo y Curso</th>
                      <th>Asignatura</th>
                      <th>Nota Final</th>
                      <th>Estado Materia</th>
                      <th class="bg-attendance">Asistencia General</th>
                      <th class="bg-status">Estado del Curso</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-for="(curso, cIdx) in historiales[f.id_persona].data" :key="'curso-'+cIdx">
                      <tr v-for="(asig, aIdx) in curso.asignaturas" :key="'asig-'+cIdx+'-'+aIdx" class="grade-row-hover">
                        
                        <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="bg-light border-end-heavy align-middle">
                          <div class="p-2 text-center">
                            <span class="d-block fw-bold text-blue mb-1 fs-6">{{ curso.periodo }}</span>
                            <span class="d-block text-dark fw-bold mb-1">{{ curso.nivel }} "{{ curso.paralelo }}"</span>
                            <span class="badge bg-blue fw-normal text-wrap" style="max-width: 140px;">{{ curso.especialidad }}</span>
                          </div>
                        </td>

                        <td class="text-start fw-bold text-secondary">{{ asig.nombre }}</td>
                        <td class="fw-bold fs-6" :class="asig.nota_final < 7 ? 'text-danger' : 'text-blue'">
                          {{ formatNota(asig.nota_final) }}
                        </td>
                        <td>
                          <span class="badge px-2.5 py-1.5 rounded-pill fs-7 text-uppercase" :class="getBadgeEstado(asig.estado)">
                            {{ asig.estado }}
                          </span>
                        </td>

                        <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="border-start align-middle">
                          <div class="d-flex flex-column align-items-center px-2">
                            <span class="fw-bold fs-5 mb-1" :class="curso.asistencia_curso < 75 ? 'text-danger' : 'text-success'">
                              {{ curso.asistencia_curso }}%
                            </span>
                            <div class="progress w-100" style="height: 5px;">
                              <div class="progress-bar" :class="curso.asistencia_curso < 75 ? 'bg-danger' : 'bg-success'" :style="{ width: curso.asistencia_curso + '%' }"></div>
                            </div>
                          </div>
                        </td>
                        <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="align-middle border-end-heavy">
                          <span class="badge px-3 py-2 fs-7 rounded-pill text-uppercase shadow-sm" :class="getBadgeEstado(curso.estado_curso)">
                            {{ curso.estado_curso }}
                          </span>
                        </td>

                      </tr>
                    </template>
                  </tbody>
                  <tfoot class="bg-gold-light-cell border-top border-3 border-gold">
                    <tr>
                      <td colspan="2" class="text-end fw-black text-blue fs-6 py-3 align-middle">
                        PROMEDIO GENERAL ACUMULADO DEL ALUMNO:
                      </td>
                      <td class="fw-black fs-5 text-center align-middle text-blue">
                        {{ formatNota(historiales[f.id_persona].promedio) }}
                      </td>
                      <td colspan="3" class="bg-white"></td>
                    </tr>
                  </tfoot>
                </table>
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
  name: "HistorialNotasRepresentados",
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: null,
      loading: true,
      Persona: {},
      familiares: [],       // Corregido: inicializado como array vacío para soportar .length y iteración
      historiales: {},      // Objeto indexador estructurado por ID de estudiante -> { data: [], promedio: 0, loading: false }
    };
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
      } catch (err) { 
        console.error("Error cargando perfil del representante:", err); 
      }
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
    async cargarHistorialEstudiante(studentId) {
      // Si los datos ya se cargaron previamente, cancelamos la petición para ahorrar ancho de banda
      if (this.historiales[studentId]) return;

      // Inicializamos el objeto reactivo para este alumno específico
      this.historiales[studentId] = { data: [], promedio: 0, loading: true };

      try {
        const res = await API.get(`${this.baseUrl}/historial-completo-est/${studentId}`);
        this.historiales[studentId].data = res.data.historial || [];
        this.historiales[studentId].promedio = res.data.promedio_general || 0;
      } catch (error) {
        console.error(`Error cargando historial académico del ID ${studentId}:`, error);
        mostraralertas("Error al recopilar el expediente académico", "error");
      } finally {
        this.historiales[studentId].loading = false;
      }
    },
    formatNota(valor) {
      const num = parseFloat(valor);
      return isNaN(num) ? "0.00" : num.toFixed(2);
    },
    getBadgeEstado(estado) {
      if(!estado) return "bg-secondary text-white";
      switch (estado.toLowerCase()) {
        case "aprobado": return "bg-success text-white";
        case "reprobado": return "bg-danger text-white";
        case "supletorio":
        case "en proceso": return "bg-warning text-dark";
        default: return "bg-secondary text-white";
      }
    },
    generarPDFHistorial(estudiante) {
      const infoHistorial = this.historiales[estudiante.id_persona];
      if (!infoHistorial || infoHistorial.data.length === 0) return;

      const doc = new jsPDF("p", "mm", "a4");
      const nombreCompleto = `${estudiante.nombres} ${estudiante.apellidos}`;

      // Cabecera institucional
      doc.setFillColor(29, 42, 104);
      doc.rect(0, 0, 210, 22, "F");
      doc.setTextColor(244, 181, 36);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(13);
      doc.text("EXPEDIENTE ACADÉMICO HISTÓRICO CONSOLIDADO", 14, 10);
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(9);
      doc.setFont("Helvetica", "normal");
      doc.text("Reporte Oficial emitido a petición del Representante Legal", 14, 16);

      // Bloque de Metadatos
      doc.setFillColor(248, 249, 250);
      doc.rect(14, 28, 182, 16, "F");
      doc.setDrawColor(225, 228, 232);
      doc.rect(14, 28, 182, 16, "S");

      doc.setTextColor(29, 42, 104);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(8.5);
      doc.text("Estudiante / Representado:", 18, 34);
      doc.text("Cédula Pasaporte:", 18, 40);
      doc.text("Representante:", 120, 34);

      doc.setTextColor(0, 0, 0);
      doc.setFont("Helvetica", "normal");
      doc.text(nombreCompleto, 58, 34);
      doc.text(estudiante.cedula || 'S/N', 48, 40);
      doc.text(`${this.Persona.nombres || ''} ${this.Persona.apellidos || ''}`.trim(), 142, 34);

      // Procesamiento bidimensional de filas de AutoTable con Rowspan
      const bodyData = [];
      
      infoHistorial.data.forEach(curso => {
        curso.asignaturas.forEach((asig, index) => {
          if (index === 0) {
            bodyData.push([
              { 
                content: `${curso.periodo}\n${curso.nivel} "${curso.paralelo}"\nEsp: ${curso.especialidad}`, 
                rowSpan: curso.asignaturas.length, 
                styles: { valign: 'middle', halign: 'center', fillColor: [248, 249, 250] } 
              },
              asig.nombre,
              this.formatNota(asig.nota_final),
              asig.estado.toUpperCase(),
              { content: `${curso.asistencia_curso}%`, rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center' } },
              { content: curso.estado_curso.toUpperCase(), rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center', fontStyle: 'bold' } }
            ]);
          } else {
            bodyData.push([
              asig.nombre,
              this.formatNota(asig.nota_final),
              asig.estado.toUpperCase()
            ]);
          }
        });
      });

      // Añadir fila de promedio final al PDF
      bodyData.push([
        { content: 'PROMEDIO GENERAL ACUMULADO:', colSpan: 2, styles: { halign: 'right', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: this.formatNota(infoHistorial.promedio), styles: { halign: 'center', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: '', colSpan: 3, styles: { fillColor: [244, 181, 36] } }
      ]);

      autoTable(doc, {
        startY: 50,
        head: [['Periodo y Curso', 'Asignatura', 'Nota Final', 'Estado Materia', 'Asist. General', 'Estado Curso']],
        body: bodyData,
        theme: 'grid',
        headStyles: { fillColor: [29, 42, 104], textColor: [255, 255, 255], fontSize: 8, halign: 'center' },
        styles: { fontSize: 8, cellPadding: 2.5, valign: 'middle' },
        columnStyles: {
          0: { cellWidth: 42 },
          1: { cellWidth: 55 },
          2: { halign: 'center', fontStyle: 'bold' },
          3: { halign: 'center' },
          4: { halign: 'center', cellWidth: 20 },
          5: { halign: 'center', cellWidth: 23 }
        }
      });

      doc.save(`Historial_Academico_${nombreCompleto.replace(/\s+/g, '_')}.pdf`);
    }
  }
};
</script>

<style scoped>
.text-blue { color: #1D2A68; }
.border-blue { border-color: #1D2A68 !important; }
.bg-blue { background-color: #1D2A68 !important; }
.text-gold { color: #F4B324; }
.bg-gold { background-color: #F4B324 !important; }
.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}
.btn-gold:hover {
  background-color: #e0a216;
  color: #1D2A68;
}
.fw-black { font-weight: 900; }
.font-black { font-weight: 800; }
.object-cover { object-fit: cover; }

/* Estructuración de tablas */
.custom-table-grades { border: 2px solid #1D2A68 !important; }
.header-double-level th {
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  padding: 12px 6px;
}
.bg-blue-dark { background-color: #141f4f !important; }
.bg-attendance { background-color: #216d43 !important; }
.bg-status { background-color: #b78311 !important; }
.border-end-heavy { border-right: 3px solid #1D2A68 !important; }
.bg-gold-light-cell { background-color: rgba(244, 181, 36, 0.12) !important; }
.grade-row-hover:hover { background-color: rgba(29, 42, 104, 0.02); }
.fs-7 { font-size: 0.72rem !important; }

/* Ajustes del Acordeón */
.accordion-button:not(.collapsed) {
  background-color: rgba(29, 42, 104, 0.03);
  color: #1D2A68;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}
.accordion-button::after {
  background-size: 1.25rem;
}
</style>
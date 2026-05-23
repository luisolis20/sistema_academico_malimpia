<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5 no-print">
      <div class="row align-items-center g-3">
        <div class="col-md-8">
          <h2 class="fw-bold text-blue mb-1">
            <i class="fas fa-file-invoice-doll me-2 text-gold"></i>Histórico de Notas
          </h2>
          <p class="text-muted mb-0">
            Visualiza la nota final de cada una de tus asignaturas a lo largo de tu trayectoria académica.
          </p>
        </div>
        <div class="col-md-4 text-md-end">
          <button @click="generarPDFHistorial" class="btn btn-gold btn-md font-bold rounded-pill shadow-sm px-4 no-print" :disabled="loading || historial.length === 0">
            <i class="fas fa-file-pdf me-2"></i>Descargar Historial
          </button>
        </div>
      </div>
    </header>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-blue" role="status">
        <span class="visually-hidden">Cargando historial...</span>
      </div>
      <p class="mt-2 text-muted fw-bold">Recopilando tu trayectoria académica...</p>
    </div>

    <div v-else-if="historial.length === 0" class="alert alert-info rounded-4 p-4 text-center shadow-sm">
      <i class="fas fa-folder-open fa-2x text-blue mb-2"></i>
      <h5>Historial Vacío</h5>
      <p class="mb-0 text-muted">Aún no posees registros de calificaciones finales en el sistema.</p>
    </div>

    <div v-else class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-5">
      <div class="table-responsive">
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
            <template v-for="(curso, cIdx) in historial" :key="'curso-'+cIdx">
              <tr v-for="(asig, aIdx) in curso.asignaturas" :key="'asig-'+cIdx+'-'+aIdx" class="grade-row-hover">
                
                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="bg-light border-end-heavy align-middle">
                  <div class="p-2">
                    <span class="d-block fw-bold text-blue mb-1 fs-6">{{ curso.periodo }}</span>
                    <span class="d-block text-dark fw-bold mb-1">{{ curso.nivel }} "{{ curso.paralelo }}"</span>
                    <span class="badge bg-blue fw-normal">{{ curso.especialidad }}</span>
                  </div>
                </td>

                <td class="text-start fw-bold text-secondary">{{ asig.nombre }}</td>
                <td class="fw-bold fs-6" :class="asig.nota_final < 7 ? 'text-danger' : 'text-blue'">
                  {{ formatNota(asig.nota_final) }}
                </td>
                <td>
                  <span class="badge px-2 py-1 rounded-pill" :class="getBadgeEstado(asig.estado)">
                    {{ asig.estado }}
                  </span>
                </td>

                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="border-start align-middle">
                  <div class="d-flex flex-column align-items-center px-3">
                    <span class="fw-bold fs-5 mb-1" :class="curso.asistencia_curso < 75 ? 'text-danger' : 'text-success'">
                      {{ curso.asistencia_curso }}%
                    </span>
                    <div class="progress w-100" style="height: 5px;">
                      <div class="progress-bar" :class="curso.asistencia_curso < 75 ? 'bg-danger' : 'bg-success'" :style="{ width: curso.asistencia_curso + '%' }"></div>
                    </div>
                  </div>
                </td>
                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="align-middle border-end-heavy">
                  <span class="badge px-3 py-2 fs-6 rounded-pill text-uppercase shadow-sm" :class="getBadgeEstado(curso.estado_curso)">
                    {{ curso.estado_curso }}
                  </span>
                </td>

              </tr>
            </template>
          </tbody>
          <tfoot class="bg-gold-light-cell border-top border-3 border-gold">
            <tr>
              <td colspan="2" class="text-end fw-black text-blue fs-5 py-3 align-middle">
                PROMEDIO GENERAL HISTÓRICO ACUMULADO:
              </td>
              <td class="fw-black fs-4 text-center align-middle" :class="promedioGlobal < 7 ? 'text-danger' : 'text-blue'">
                {{ formatNota(promedioGlobal) }}
              </td>
              <td colspan="3"></td>
            </tr>
          </tfoot>
        </table>
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
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: null,
      nombreEstudiante: "",
      cedulaEstudiante: "",
      loading: true,
      Persona: {},
      historial: [],
      promedioGlobal: 0
    };
  },
  async mounted() {
    try {
      const me = await getMe();
      this.idpersona = me.id_persona;
      await this.getPersona();
      await this.getHistorialNotas();
    } catch (error) {
      console.error("Error inicializando componente:", error);
      mostraralertas("Error al autenticar usuario", "error");
      this.loading = false;
    }
  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        this.Persona = res.data.data[0] || res.data.data;
        this.nombreEstudiante = `${this.Persona.nombres || ''} ${this.Persona.apellidos || ''}`.trim();
        this.cedulaEstudiante = this.Persona.cedula || 'S/N';
      } catch (err) { console.error(err); }
    },
    async getHistorialNotas() {
      this.loading = true;
      try {
        const res = await API.get(`${this.baseUrl}/historial-completo-est/${this.idpersona}`);
        this.historial = res.data.historial;
        this.promedioGlobal = res.data.promedio_general;
      } catch (error) {
        console.error("Error obteniendo el historial:", error);
        mostraralertas("No se pudo cargar el historial de notas", "error");
      } finally {
        this.loading = false;
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
    generarPDFHistorial() {
      const doc = new jsPDF("p", "mm", "a4");

      // Encabezado del PDF
      doc.setFillColor(29, 42, 104);
      doc.rect(0, 0, 210, 22, "F");
      doc.setTextColor(244, 181, 36);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(14);
      doc.text("HISTORIAL ACADÉMICO DE CALIFICACIONES", 14, 10);
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(9);
      doc.setFont("Helvetica", "normal");
      doc.text("Reporte Consolidado Estudiantil", 14, 16);

      // Datos del Estudiante
      doc.setTextColor(0, 0, 0);
      doc.setFont("Helvetica", "bold");
      doc.text("Estudiante:", 14, 30);
      doc.text("Cédula / ID:", 140, 30);
      doc.setFont("Helvetica", "normal");
      doc.text(this.nombreEstudiante, 38, 30);
      doc.text(this.cedulaEstudiante, 162, 30);

      // Construcción dinámica de datos con AutoTable usando rowSpan
      const bodyData = [];
      
      this.historial.forEach(curso => {
        curso.asignaturas.forEach((asig, index) => {
          if (index === 0) {
            bodyData.push([
              { content: `${curso.periodo}\n${curso.nivel} "${curso.paralelo}"\nEsp: ${curso.especialidad}`, rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center', fillColor: [248, 249, 250] } },
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

      // Añadir la fila de total global al final de la tabla
      bodyData.push([
        { content: 'PROMEDIO GENERAL HISTÓRICO:', colSpan: 2, styles: { halign: 'right', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: this.formatNota(this.promedioGlobal), styles: { halign: 'center', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: '', colSpan: 3, styles: { fillColor: [244, 181, 36] } }
      ]);

      autoTable(doc, {
        startY: 38,
        head: [['Periodo y Curso', 'Asignatura', 'Nota', 'Estado Mat.', 'Asist. Curso', 'Estado Curso']],
        body: bodyData,
        theme: 'grid',
        headStyles: { fillColor: [29, 42, 104], textColor: [255, 255, 255], fontSize: 8, halign: 'center' },
        styles: { fontSize: 8, cellPadding: 3, valign: 'middle' },
        columnStyles: {
          0: { cellWidth: 45 },
          1: { cellWidth: 60 },
          2: { halign: 'center', fontStyle: 'bold' },
          3: { halign: 'center' },
          4: { halign: 'center', cellWidth: 20 },
          5: { halign: 'center', cellWidth: 25 }
        }
      });

      doc.save(`Historial_Notas_${this.nombreEstudiante.replace(/\s+/g, '_')}.pdf`);
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

.custom-table-grades { border: 2px solid #1D2A68 !important; }
.header-double-level th {
  font-size: 0.9rem;
  letter-spacing: 0.5px;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  padding: 12px 6px;
}
.bg-blue-dark { background-color: #141f4f !important; }
.bg-attendance { background-color: #216d43 !important; }
.bg-status { background-color: #b78311 !important; }
.border-end-heavy { border-right: 3px solid #1D2A68 !important; }
.bg-gold-light-cell { background-color: rgba(244, 181, 36, 0.12) !important; }
.grade-row-hover:hover { background-color: rgba(29, 42, 104, 0.03); }
</style>
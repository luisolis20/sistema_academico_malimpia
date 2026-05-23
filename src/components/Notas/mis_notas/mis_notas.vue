<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5 no-print">
      <div class="row align-items-center g-3">
        <div class="col-md-7">
          <h2 class="fw-bold text-blue mb-1">
            <i class="fas fa-file-invoice-doll me-2 text-gold"></i>Mis Calificaciones Consolidadas
          </h2>
          <p class="text-muted mb-0">
            Consulta tus parciales, exámenes quimestrales, registros definitivos y porcentajes de asistencia por periodo lectivo.
          </p>
        </div>
        <div class="col-md-5 text-md-end">
          <div class="d-inline-block text-start style-select-container">
            <label class="small fw-bold text-blue mb-1 d-block">Seleccionar Periodo Lectivo:</label>
            <div class="input-group">
              <span class="input-group-text bg-blue text-white"><i class="fas fa-calendar-alt"></i></span>
              <select class="form-select border-blue shadow-sm" v-model="selectedPeriodoId" @change="cambiarPeriodo">
                <option v-for="p in periodosHistorial" :key="p.id_periodo" :value="p.id_periodo">
                  {{ p.nombre_periodo }} {{ p.id_periodo === periodoActivoId ? '(Actual)' : '' }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-blue" role="status">
        <span class="visually-hidden">Cargando información académica...</span>
      </div>
      <p class="mt-2 text-muted fw-bold">Obteniendo boleta de calificaciones...</p>
    </div>

    <div v-else-if="periodosHistorial.length === 0" class="alert alert-info rounded-4 p-4 text-center shadow-sm">
      <i class="fas fa-exclamation-circle fa-2x text-blue mb-2"></i>
      <h5>Sin registros académicos</h5>
      <p class="mb-0 text-muted">No se encontraron matrículas o calificaciones vinculadas a tu perfil.</p>
    </div>

    <div v-else>
      <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="bg-blue text-white p-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-gold p-2.5 rounded-3 text-blue font-bold">
              <i class="fas fa-graduation-cap fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold">Nivel: {{ infoCursoActual.nivel }}</h5>
              <span class="small text-white-50">Especialidad: {{ infoCursoActual.especialidad }}</span>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3">
            <div class="text-end">
              <span class="badge bg-gold text-blue font-black px-3 py-2 fs-6 rounded-pill">
                Paralelo: "{{ infoCursoActual.paralelo }}"
              </span>
            </div>
            <button @click="generarPDFBoleta" class="btn btn-gold btn-md font-bold rounded-pill shadow-sm px-4 no-print">
              <i class="fas fa-file-pdf me-2"></i>Exportar Reporte PDF
            </button>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0 text-center custom-table-grades">
            <thead class="bg-blue text-white header-double-level">
              <tr>
                <th rowspan="2" class="align-middle text-start bg-blue-dark border-end-heavy text-white" style="min-width: 220px;">Asignatura</th>
                <th colspan="5" class="bg-quimestre-1 text-white">Primer Quimestre (Q1)</th>
                <th colspan="5" class="bg-quimestre-2 text-white">Segundo Quimestre (Q2)</th>
                <th rowspan="2" class="align-middle bg-summary text-white">Anual</th>
                <th colspan="3" class="bg-remediales text-white">Instancias de Recuperación</th>
                <th rowspan="2" class="align-middle bg-final text-white">Nota Final</th>
                <th rowspan="2" class="align-middle bg-attendance text-white" style="min-width: 90px;">% Asistencia</th>
                <th rowspan="2" class="align-middle bg-status no-print">Estado</th>
              </tr>
              <tr class="sub-headers">
                <th class="bg-q1-sub text-white">P1</th>
                <th class="bg-q1-sub text-white">P2</th>
                <th class="bg-q1-sub text-white">P3</th>
                <th class="bg-q1-sub text-white">EX</th>
                <th class="bg-q1-sub fw-bold text-yellow-gold">PROM</th>

                <th class="bg-q2-sub text-white">P1</th>
                <th class="bg-q2-sub text-white">P2</th>
                <th class="bg-q2-sub text-white">P3</th>
                <th class="bg-q2-sub text-white">EX</th>
                <th class="bg-q2-sub fw-bold text-yellow-gold">PROM</th>

                <th class="bg-rem-sub">Supl.</th>
                <th class="bg-rem-sub">Rem.</th>
                <th class="bg-rem-sub">Grac.</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(nota, idx) in notasActuales" :key="idx" class="grade-row-hover">
                <td class="text-start fw-bold text-blue border-end-heavy">{{ nota.asignatura }}</td>
                <td>{{ formatNota(nota.q1_p1) }}</td>
                <td>{{ formatNota(nota.q1_p2) }}</td>
                <td>{{ formatNota(nota.q1_p3) }}</td>
                <td>{{ formatNota(nota.q1_examen) }}</td>
                <td class="fw-bold table-primary-light">{{ formatNota(nota.q1_promedio) }}</td>
                <td>{{ formatNota(nota.q2_p1) }}</td>
                <td>{{ formatNota(nota.q2_p2) }}</td>
                <td>{{ formatNota(nota.q2_p3) }}</td>
                <td>{{ formatNota(nota.q2_examen) }}</td>
                <td class="fw-bold table-primary-light">{{ formatNota(nota.q2_promedio) }}</td>
                <td class="fw-bold bg-light-blue-container text-blue">{{ formatNota(nota.promedio_anual) }}</td>
                <td class="text-muted-zero">{{ nota.nota_supletorio > 0 ? formatNota(nota.nota_supletorio) : '-' }}</td>
                <td class="text-muted-zero">{{ nota.nota_remedial > 0 ? formatNota(nota.nota_remedial) : '-' }}</td>
                <td class="text-muted-zero">{{ nota.nota_gracia > 0 ? formatNota(nota.nota_gracia) : '-' }}</td>
                <td class="fw-black text-blue bg-gold-light-cell fs-6">{{ formatNota(nota.nota_final_definitiva) }}</td>
                <td>
                  <div class="d-flex flex-column align-items-center">
                    <span class="fw-bold" :class="nota.asistencia < 75 ? 'text-danger' : 'text-success'">
                      {{ nota.asistencia }}%
                    </span>
                    <div class="progress w-100 mt-1" style="height: 4px;">
                      <div class="progress-bar" role="progressbar" 
                           :class="nota.asistencia < 75 ? 'bg-danger' : 'bg-success'"
                           :style="{ width: nota.asistencia + '%' }"></div>
                    </div>
                  </div>
                </td>
                <td class="no-print">
                  <span class="badge px-2.5 py-1.5 rounded-pill fs-7 text-uppercase" :class="getBadgeEstado(nota.estado_asignatura)">
                    {{ nota.estado_asignatura }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
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
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: null,
      nombreEstudiante: "",
      cedulaEstudiante: "",
      loading: true,
      periodosHistorial: [],
      periodoActivoId: null,
      selectedPeriodoId: null,
      notasActuales: [],
      infoCursoActual: {
        nivel: "",
        especialidad: "",
        paralelo: ""
      },
      Persona: {},
    };
  },
  async mounted() {
    try {
      const me = await getMe();
      this.idpersona = me.id_persona;
      await Promise .all([this.getPersona(), this.cargarHistorialNotas()]);
    } catch (error) {
      console.error("Error inicializando componente de notas:", error);
      mostraralertas("Error al autenticar usuario", "error");
      this.loading = false;
    }
  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
        this.nombreEstudiante = `${this.Persona.nombres || ''} ${this.Persona.apellidos || ''}`.trim();
        this.cedulaEstudiante = this.Persona.cedula || 'S/N';
      } catch (err) { console.error(err); }
    },
    async cargarHistorialNotas() {
      this.loading = true;
      try {
        const response = await API.get(`/sistma/mis-notas-est/${this.idpersona}`);
        const data = response.data;

        this.periodosHistorial = data.periodos || [];
        this.periodoActivoId = data.periodo_activo_id;

        if (this.periodosHistorial.length > 0) {
          // Asignar por defecto el periodo prioritario activo
          if (this.periodoActivoId && this.periodosHistorial.some(p => p.id_periodo === this.periodoActivoId)) {
            this.selectedPeriodoId = this.periodoActivoId;
          } else {
            this.selectedPeriodoId = this.periodosHistorial[0].id_periodo;
          }
          this.filtrarDatosPorPeriodo();
        }
      } catch (error) {
        console.error("Error cargando historial de notas:", error);
        mostraralertas("No se pudo compilar el historial de notas", "error");
      } finally {
        this.loading = false;
      }
    },
    filtrarDatosPorPeriodo() {
      const registro = this.periodosHistorial.find(p => p.id_periodo === this.selectedPeriodoId);
      if (registro) {
        this.notasActuales = registro.notas || [];
        this.infoCursoActual = registro.detalles_curso || { nivel: "N/A", especialidad: "General", paralelo: "A" };
      }
    },
    cambiarPeriodo() {
      this.filtrarDatosPorPeriodo();
    },
    formatNota(valor) {
      const num = parseFloat(valor);
      return isNaN(num) ? "0.00" : num.toFixed(2);
    },
    getBadgeEstado(estado) {
      switch (estado.toLowerCase()) {
        case "aprobado":
          return "bg-success text-white";
        case "reprobado":
          return "bg-danger text-white";
        case "supletorio":
        case "en proceso":
          return "bg-warning text-dark";
        default:
          return "bg-secondary text-white";
      }
    },
    generarPDFBoleta() {
      if (this.notasActuales.length === 0) return;

      const doc = new jsPDF({
        orientation: "landscape",
        unit: "mm",
        format: "a4"
      });

      const periodoObj = this.periodosHistorial.find(p => p.id_periodo === this.selectedPeriodoId);
      const nombrePeriodo = periodoObj ? periodoObj.nombre_periodo : "N/A";

      // BANNER INSTITUCIONAL Y LOGO SIMULADO
      doc.setFillColor(29, 42, 104); // #1D2A68
      doc.rect(0, 0, 297, 24, "F");

      doc.setTextColor(244, 181, 36); // #F4B324
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(16);
      doc.text("REPORTE OFICIAL DE RENDIMIENTO ACADÉMICO", 14, 10);
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(10);
      doc.setFont("Helvetica", "normal");
      doc.text(`Sistema de Gestión Escolar v2.0 | Periodo Lectivo: ${nombrePeriodo}`, 14, 16);

      // CUADRO DE METADATOS DEL ALUMNO
      doc.setFillColor(248, 249, 250);
      doc.rect(14, 28, 269, 20, "F");
      doc.setDrawColor(220, 224, 230);
      doc.rect(14, 28, 269, 20, "S");

      doc.setTextColor(29, 42, 104);
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(9);
      
      doc.text("Estudiante:", 18, 35);
      doc.text("Cédula Pasaporte:", 18, 43);
      doc.text("Curso / Nivel:", 140, 35);
      doc.text("Especialidad:", 140, 43);
      doc.text("Paralelo:", 240, 35);

      doc.setTextColor(0, 0, 0);
      doc.setFont("Helvetica", "normal");
      doc.text(this.nombreEstudiante, 38, 35);
      doc.text(this.cedulaEstudiante, 48, 43);
      doc.text(this.infoCursoActual.nivel, 162, 35);
      doc.text(this.infoCursoActual.especialidad, 162, 43);
      doc.text(`"${this.infoCursoActual.paralelo}"`, 256, 35);

      // CONSTRUCCIÓN DE MATRIZ DE DATOS PARA AUTOTABLE
      const bodyData = this.notasActuales.map(n => [
        n.asignatura,
        this.formatNota(n.q1_p1),
        this.formatNota(n.q1_p2),
        this.formatNota(n.q1_p3),
        this.formatNota(n.q1_examen),
        this.formatNota(n.q1_promedio),
        this.formatNota(n.q2_p1),
        this.formatNota(n.q2_p2),
        this.formatNota(n.q2_p3),
        this.formatNota(n.q2_examen),
        this.formatNota(n.q2_promedio),
        this.formatNota(n.promedio_anual),
        n.nota_supletorio > 0 ? this.formatNota(n.nota_supletorio) : "-",
        n.nota_remedial > 0 ? this.formatNota(n.nota_remedial) : "-",
        n.nota_gracia > 0 ? this.formatNota(n.nota_gracia) : "-",
        this.formatNota(n.nota_final_definitiva),
        `${n.asistencia}%`,
        n.estado_asignatura.toUpperCase()
      ]);

      autoTable(doc, {
        startY: 53,
        head: [[
          { content: 'Asignatura', rowSpan: 2, styles: { halign: 'left', valign: 'middle' } },
          { content: 'Primer Quimestre', colSpan: 5, styles: { halign: 'center' } },
          { content: 'Segundo Quimestre', colSpan: 5, styles: { halign: 'center' } },
          { content: 'Anual', rowSpan: 2, styles: { halign: 'center', valign: 'middle', fillClass: true } },
          { content: 'Recuperación', colSpan: 3, styles: { halign: 'center' } },
          { content: 'Final', rowSpan: 2, styles: { halign: 'center', valign: 'middle' } },
          { content: 'Asist.', rowSpan: 2, styles: { halign: 'center', valign: 'middle' } },
          { content: 'Estado', rowSpan: 2, styles: { halign: 'center', valign: 'middle' } }
        ], [
          'P1', 'P2', 'P3', 'EX', 'PR',
          'P1', 'P2', 'P3', 'EX', 'PR',
          'Sup', 'Rem', 'Gra'
        ]],
        body: bodyData,
        theme: 'grid',
        headStyles: {
          fillColor: [29, 42, 104],
          textColor: [255, 255, 255],
          fontSize: 7.5,
          fontStyle: 'bold',
          lineWidth: 0.2,
          lineColor: [255, 255, 255]
        },
        styles: {
          fontSize: 7.5,
          cellPadding: 2,
          halign: 'center'
        },
        columnStyles: {
          0: { halign: 'left', fontStyle: 'bold', fontColor: [29, 42, 104], width: 50 },
          5: { fillColor: [240, 244, 255], fontStyle: 'bold' }, // Q1 Prom
          10: { fillColor: [240, 244, 255], fontStyle: 'bold' }, // Q2 Prom
          11: { fillColor: [230, 235, 255], fontStyle: 'bold' }, // Anual
          15: { fillColor: [254, 249, 231], fontStyle: 'bold', fontSize: 8 } // Final
        },
        didParseCell: function (data) {
          // Ajustes estéticos en headers de subniveles
          if (data.row.section === 'head' && data.row.index === 1) {
            data.cell.styles.fillColor = [44, 62, 140];
          }
        }
      });

      // SECCIÓN DE FIRMAS AL FINAL
      const finalY = doc.lastAutoTable.finalY + 25;
      if (finalY < 180) {
        doc.setLineWidth(0.3);
        doc.setDrawColor(150, 150, 150);
        
        doc.line(40, finalY, 110, finalY);
        doc.line(180, finalY, 250, finalY);

        doc.setFontSize(8);
        doc.setFont("Helvetica", "bold");
        doc.text("SECRETARÍA ACADÉMICA", 75, finalY + 4, { align: "center" });
        doc.text("RECTORADO / TUTOR", 215, finalY + 4, { align: "center" });
      }

      doc.save(`Boleta_Notas_${this.nombreEstudiante.replace(/\s+/g, '_')}_${nombrePeriodo}.pdf`);
    }
  }
};
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}
.border-blue {
  border-color: #1D2A68 !important;
}
.bg-blue {
  background-color: #1D2A68 !important;
}
.text-gold {
  color: #F4B324;
}
.bg-gold {
  background-color: #F4B324 !important;
}
.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}
.btn-gold:hover {
  background-color: #e0a216;
  color: #1D2A68;
}
.fw-black {
  font-weight: 900;
}
.style-select-container {
  min-width: 280px;
}

/* Estilización Avanzada de Tablas Multidimensionales */
.custom-table-grades {
  border: 2px solid #1D2A68 !important;
}
.header-double-level th {
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  padding: 10px 6px;
}
.bg-blue-dark {
  background-color: #141f4f !important;
}
.bg-quimestre-1 {
  background-color: #253684 !important;
}
.bg-quimestre-2 {
  background-color: #2c3e9e !important;
}
.bg-remediales {
  background-color: #5665bb !important;
}
.bg-summary {
  background-color: #1b265c !important;
}
.bg-final {
  background-color: #b78311 !important;
}
.bg-attendance {
  background-color: #216d43 !important;
}
.sub-headers th {
  font-size: 0.72rem !important;
  padding: 6px 2px !important;
}
.bg-q1-sub {
  background-color: #2d419b !important;
}
.bg-q2-sub {
  background-color: #354aba !important;
}
.bg-rem-sub {
  background-color: #6977ca !important;
}
.text-yellow-gold {
  color: #ffdf7e !important;
}
.border-end-heavy {
  border-right: 3px solid #1D2A68 !important;
}
.table-primary-light {
  background-color: rgba(29, 42, 104, 0.05) !important;
  color: #1D2A68;
}
.bg-light-blue-container {
  background-color: rgba(29, 42, 104, 0.1) !important;
}
.bg-gold-light-cell {
  background-color: rgba(244, 181, 36, 0.15) !important;
}
.grade-row-hover:hover {
  background-color: rgba(244, 181, 36, 0.03);
}
.text-muted-zero {
  color: #bfc4cd;
}
.fs-7 {
  font-size: 0.7rem !important;
}
</style>
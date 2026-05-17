<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5">
      <div class="row align-items-center">
        <div class="col-md-7">
          <h2 class="fw-bold text-blue mb-1">Histórico de Notas del Estudiante</h2>
          <p class="text-muted mb-0">
            <i class="fas fa-info-circle me-2 text-gold"></i>
            Ingrese el número de cédula del estudiante para buscar su historial completo de calificaciones.
          </p>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
          <div class="input-group">
            <input type="text" class="form-control border-gold" placeholder="Ej. 0801234567" v-model="cedulaBusqueda"
              @keyup.enter="buscarEstudiante" :disabled="buscando">
            <button class="btn btn-gold fw-bold px-4" @click="buscarEstudiante" :disabled="!cedulaBusqueda || buscando">
              <span v-if="buscando" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-search me-2"></i> Buscar
            </button>
          </div>
        </div>
      </div>
    </header>

    <div v-if="estudiante" class="row g-4">

      <div class="col-lg-4 col-md-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border-top border-blue border-4">
          <div class="card-body text-center p-4">
            <div class="mb-3 position-relative d-inline-block">
              <img :src="estudiante.foto ? 'data:image/jpeg;base64,' + estudiante.foto : '/sistema/default-avatar.png'"
                class="rounded-circle border border-3 border-gold shadow-sm"
                style="width: 110px; height: 110px; object-fit: cover;">
            </div>
            <h5 class="fw-bold text-blue mb-1">{{ estudiante.nombres }} {{ estudiante.apellidos }}</h5>
            <p class="badge bg-blue text-white rounded-pill px-3 py-1 mb-3">Estudiante Regular</p>

            <div class="text-start border-top pt-3 mt-2">
              <p class="mb-2 text-muted small"><i class="fas fa-id-card me-2 text-gold"></i> <strong>Cédula:</strong> {{
                estudiante.cedula }}</p>
              <p class="mb-0 text-muted small"><i class="fas fa-phone me-2 text-gold"></i> <strong>Teléfono:</strong> {{
                estudiante.telefono || 'Sin registro' }}</p>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
          <h6 class="fw-bold text-blue mb-3 pb-2 border-bottom">
            <i class="fas fa-graduation-cap me-2 text-gold"></i> Ruta de Progreso Académico
          </h6>
          <div class="position-relative ps-2">
            <div class="timeline-line"></div>

            <div v-for="nivel in nivelesVisibles" :key="nivel.id_nivel"
              class="d-flex align-items-center mb-3 position-relative timeline-item"
              :class="{ 'opacity-50': nivel.estado === 'PENDIENTE' }">

              <div class="timeline-badge me-3 shadow-sm d-flex align-items-center justify-content-center"
                :class="getNivelBadgeClass(nivel.estado)">
                <i :class="getNivelIconClass(nivel.estado)"></i>
              </div>

              <div>
                <p class="mb-0 fw-bold small text-blue">{{ nivel.nombre }}</p>
                <small class="x-small text-uppercase fw-semibold" :class="getNivelTextClass(nivel.estado)">
                  {{
                    nivel.estado === 'APROBADO' ? 'Aprobado ✓' :
                      (nivel.estado === 'REPITE' ? 'Repite Nivel ⚠' :
                        (nivel.estado === 'CURSANDO' ? 'Cursando Actual' : 'Próximo Nivel'))
                  }}
                </small>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 d-grid">
          <button @click="generarPDF" class="btn btn-outline-blue fw-bold btn-lg rounded-pill shadow-sm">
            <i class="fas fa-file-pdf me-2 text-danger"></i> Generar Certificado PDF
          </button>
        </div>
      </div>

      <div class="col-lg-8 col-md-7">
        <div v-for="periodoMatriculado in historial" :key="periodoMatriculado.id_matricula"
          class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-gold border-5">

          <div class="bg-blue text-white p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-calendar-alt me-2 text-gold"></i> Periodo: {{ periodoMatriculado.periodo }}
              </h6>
              <small class="text-white-50">
                Curso: {{ periodoMatriculado.nivel_nombre }} "{{ periodoMatriculado.paralelo }}"
                <span v-if="periodoMatriculado.especialidad"> | Especialidad: {{ periodoMatriculado.especialidad
                }}</span>
              </small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-white bg-opacity-25 border border-light px-3 py-2 rounded-pill" title="Asistencia General del Periodo">
                <i class="fas fa-user-clock me-1 text-gold"></i> Asistencia: {{ periodoMatriculado.porcentaje_asistencia }}%
              </span>

              <span class="badge rounded-pill px-3 py-2 border border-light"
                :class="periodoMatriculado.estado_curso === 'APROBADO' ? 'bg-success' : 'bg-danger'">
                {{ periodoMatriculado.estado_curso }}
              </span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 text-center">
              <thead class="table-light border-bottom">
                <tr>
                  <th class="text-start ps-4 text-blue py-3">Asignatura / Componente Curricular</th>
                  <th class="text-blue py-3" style="width: 140px;">Nota Final</th>
                  <th class="text-blue py-3" style="width: 160px;">Estado de Rendimiento</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="materia in periodoMatriculado.calificaciones" :key="materia.asignatura">
                  <td class="text-start ps-4 fw-bold text-blue-light">{{ materia.asignatura }}</td>
                  <td class="fw-bold fs-6">{{ materia.nota_final.toFixed(2) }}</td>
                  <td>
                    <span class="badge px-3 py-1.5 rounded-pill"
                      :class="materia.estado === 'APROBADO' || materia.estado === 'Aprobado' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'">
                      <i class="fas me-1" :class="materia.estado === 'APROBADO' || materia.estado === 'Aprobado' ? 'fa-check' : 'fa-times'"></i>
                      {{ materia.estado }}
                    </span>
                  </td>
                </tr>
                <tr class="table-gold-light fw-bold border-top border-blue border-2">
                  <td class="text-end pe-4 text-blue">PROMEDIO GENERAL GENERAL:</td>
                  <td class="text-blue fs-5 fw-black text-center border-bottom border-blue border-2">
                    {{ periodoMatriculado.promedio_general.toFixed(2) }}
                  </td>
                  <td class="text-blue-light small text-start ps-3 align-middle">
                    Promedio acumulado del nivel
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <div v-else-if="!buscando" class="text-center py-5">
      <div class="text-muted mb-3">
        <i class="fas fa-folder-open fa-4x text-opacity-25 text-blue"></i>
      </div>
      <h5 class="text-blue fw-bold">No hay datos que mostrar</h5>
      <p class="text-muted small">Realice la búsqueda mediante una cédula válida para estructurar el expediente
        académico.</p>
    </div>
  </div>
</template>

<script>
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  data() {
    return {
      baseUrl: "/sistma", // Ajustado de /sistma a /sistema
      cedulaBusqueda: "",
      buscando: false,
      estudiante: null,
      historial: [],
      nivelesSistema: []
    }
  },
  computed: {
    nivelesVisibles() {
      if (!this.nivelesSistema.length || !this.historial.length) return [];

      // Helper para extraer de forma segura los números iniciales del varchar
      const parsearJerarquia = (str) => {
        if (!str) return 0;
        const numero = parseInt(str, 10);
        return isNaN(numero) ? 0 : numero;
      };

      // Ordenamos todos los niveles del sistema
      const nivelesOrdenados = [...this.nivelesSistema].sort((a, b) => {
        return parsearJerarquia(a.orden_jerarquia) - parsearJerarquia(b.orden_jerarquia);
      });

      // Mapeamos el historial del estudiante
      const mapaHistorial = {};
      this.historial.forEach(h => {
        mapaHistorial[h.nivel_id] = h.estado_curso;
      });

      let maxOrdenAprobado = -1;
      let tieneNivelActivo = false;

      // Determinamos el estado lógico de cada nodo
      const listaProcesada = nivelesOrdenados.map(nivel => {
        let estado = 'PENDIENTE';
        const ordenNumerico = parsearJerarquia(nivel.orden_jerarquia);

        if (mapaHistorial[nivel.id_nivel]) {
          const estadoBD = mapaHistorial[nivel.id_nivel];

          if (estadoBD === 'APROBADO') {
            estado = 'APROBADO';
            maxOrdenAprobado = Math.max(maxOrdenAprobado, ordenNumerico);
          } else if (estadoBD === 'REPROBADO') {
            tieneNivelActivo = true;
            estado = 'REPITE'; // Nuevo estado asignado si reprobó
          } else {
            tieneNivelActivo = true;
            estado = 'CURSANDO';
          }
        }
        return { ...nivel, ordenNumerico, estado };
      });

      // Si no tiene niveles pendientes trancados, buscamos el inmediato superior
      let proximoNivelId = null;
      if (!tieneNivelActivo) {
        const siguiente = listaProcesada.find(n => n.ordenNumerico > maxOrdenAprobado && n.estado === 'PENDIENTE');
        if (siguiente) {
          proximoNivelId = siguiente.id_nivel;
        }
      }

      // Filtramos para retornar lo cursado y únicamente el siguiente nivel secuencial
      return listaProcesada.filter(n => n.estado !== 'PENDIENTE' || n.id_nivel === proximoNivelId);
    }
  },
  methods: {
    async buscarEstudiante() {
      if (!this.cedulaBusqueda) return;
      this.buscando = true;
      this.estudiante = null;
      this.historial = [];

      try {
        const response = await API.get(`${this.baseUrl}/historico_notas/${this.cedulaBusqueda}`);
        if (response.data.status === "success") {
          this.estudiante = response.data.estudiante;
          this.historial = response.data.historial;
          this.nivelesSistema = response.data.niveles_sistema;
        }
      } catch (error) {
        const msg = error.response?.data?.message || "Error al conectar con el servidor.";
        mostraralertas(msg, "error");
      } finally {
        this.buscando = false;
      }
    },
    getNivelBadgeClass(estado) {
      if (estado === 'APROBADO') return 'bg-success text-white';
      if (estado === 'REPITE') return 'bg-danger text-white';
      if (estado === 'CURSANDO') return 'bg-gold text-blue';
      return 'bg-secondary bg-opacity-25 text-muted';
    },
    getNivelIconClass(estado) {
      if (estado === 'APROBADO') return 'fas fa-check-circle';
      if (estado === 'REPITE') return 'fas fa-redo-alt'; // Ícono de volver a intentar
      if (estado === 'CURSANDO') return 'fas fa-spinner fa-spin';
      return 'fas fa-lock';
    },
    getNivelTextClass(estado) {
      if (estado === 'APROBADO') return 'text-success';
      if (estado === 'REPITE') return 'text-danger';
      if (estado === 'CURSANDO') return 'text-warning text-gold';
      return 'text-muted';
    },
    async generarPDF() {
      // Inicializar el constructor jsPDF en formato A4 (mm)
      const doc = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4"
      });

      // Paletas RGB equivalentes de la marca
      const blue = [29, 42, 104];  // #1D2A68
      const gold = [244, 179, 36];  // #F4B324
      let yOffset = 20;

      // =========================================================
      // 1. ENCABEZADO INSTITUCIONAL DINÁMICO
      // =========================================================
      const xLogo = 172;
      const anchoLogo = 23;
      const xTextoInicio = 22;
      const anchoMaxTexto = xLogo - xTextoInicio - 4;

      doc.setFont("helvetica", "bold");
      doc.setFontSize(14); 
      doc.setTextColor(...blue);

      const nombreInstitucion = 'UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO "MALIMPIA"';
      const lineasNombre = doc.splitTextToSize(nombreInstitucion, anchoMaxTexto);

      let runningY = yOffset + 4;
      lineasNombre.forEach((linea) => {
        doc.text(linea, xTextoInicio, runningY);
        runningY += 6; 
      });

      doc.setFontSize(9);
      doc.setTextColor(...gold);
      doc.text("SISTEMA DE GESTIÓN DE HISTORIAL ACADÉMICO", xTextoInicio, runningY + 2);

      runningY += 8;

      const altoFranjaDecorativa = runningY - yOffset;
      doc.setFillColor(...blue);
      doc.rect(15, yOffset, 4, altoFranjaDecorativa, 'F');

      try {
        const logo = new Image();
        logo.src = '/mile.png';

        await new Promise((resolve, reject) => {
          logo.onload = resolve;
          logo.onerror = reject;
        });
        doc.addImage(logo, 'PNG', xLogo, yOffset - 1, anchoLogo, anchoLogo);
      } catch (error) {
        console.warn("No se pudo cargar el logotipo mile.png para el PDF.", error);
      }

      yOffset = Math.max(runningY, yOffset + anchoLogo) + 4;

      doc.setDrawColor(...blue);
      doc.setLineWidth(0.5);
      doc.line(15, yOffset, 195, yOffset);

      yOffset += 8;
      doc.setFont("helvetica", "bold");
      doc.setFontSize(12);
      doc.setTextColor(...blue);
      doc.text("EXPEDIENTE HISTÓRICO DE CALIFICACIONES GENERALES", 15, yOffset);

      // =========================================================
      // 2. BLOQUE DE DATOS PERSONALES DEL ESTUDIANTE
      // =========================================================
      yOffset += 6;
      doc.setFillColor(248, 249, 250);
      doc.rect(15, yOffset, 180, 20, 'F');
      doc.setDrawColor(220, 224, 230);
      doc.setLineWidth(0.3);
      doc.rect(15, yOffset, 180, 20, 'S');

      doc.setFont("helvetica", "bold");
      doc.setFontSize(9);
      doc.setTextColor(...blue);
      doc.text("Estudiante:", 20, yOffset + 7);
      doc.setFont("helvetica", "normal");
      doc.setTextColor(60, 60, 60);
      doc.text(`${this.estudiante.nombres} ${this.estudiante.apellidos}`, 40, yOffset + 7);

      doc.setFont("helvetica", "bold");
      doc.setTextColor(...blue);
      doc.text("Cédula Num:", 20, yOffset + 14);
      doc.setFont("helvetica", "normal");
      doc.text(`${this.estudiante.cedula}`, 40, yOffset + 14);

      doc.setFont("helvetica", "bold");
      doc.setTextColor(...blue);
      doc.text("Teléfono:", 115, yOffset + 14);
      doc.setFont("helvetica", "normal");
      doc.text(`${this.estudiante.telefono || 'Sin registro'}`, 132, yOffset + 14);

      yOffset += 28;

      // =========================================================
      // 3. GENERACIÓN DE TABLAS DINÁMICAS POR PERIODO CON AUTO-TABLE
      // =========================================================
      this.historial.forEach((periodo, index) => {
        if (yOffset > 240) {
          doc.addPage();
          yOffset = 20;
        }

        doc.setFillColor(...blue);
        doc.rect(15, yOffset, 180, 7, 'F');

        doc.setFont("helvetica", "bold");
        doc.setFontSize(8.5);
        doc.setTextColor(255, 255, 255);
        
        // MODIFICACIÓN: Inyectando la asistencia y el estado en el PDF
        doc.text(`PERIODO: ${periodo.periodo} | CURSO: ${periodo.nivel_nombre} "${periodo.paralelo}"`, 18, yOffset + 4.5);
        doc.text(`ASISTENCIA: ${periodo.porcentaje_asistencia}%  |  ESTADO: ${periodo.estado_curso}`, 130, yOffset + 4.5);

        const rows = periodo.calificaciones.map(m => [
          m.asignatura,
          m.nota_final.toFixed(2),
          m.estado
        ]);

        autoTable(doc, {
          startY: yOffset + 7,
          head: [['Asignatura / Componente Curricular', 'Nota Final', 'Estado']],
          body: rows,
          foot: [['PROMEDIO GENERAL GENERAL:', periodo.promedio_general.toFixed(2), '']],
          theme: 'striped',
          headStyles: {
            fillColor: blue,
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center'
          },
          columnStyles: {
            0: { halign: 'left', cellWidth: 105 },
            1: { halign: 'center', cellWidth: 35 },
            2: { halign: 'center', cellWidth: 40 }
          },
          footStyles: {
            fillColor: [253, 248, 233],
            textColor: blue,
            fontStyle: 'bold',
            halign: 'center'
          },
          didParseCell: function (data) {
            if (data.section === 'foot' && data.column.index === 0) {
              data.cell.styles.halign = 'right';
            }
          },
          margin: { left: 15, right: 15 },
          styles: { font: "helvetica", fontSize: 8.5, cellPadding: 2 }
        });

        yOffset = doc.lastAutoTable.finalY + 12;
      });

      // =========================================================
      // 4. SECCIÓN DE FIRMAS AL FINAL DEL DOCUMENTO
      // =========================================================
      if (yOffset > 245) {
        doc.addPage();
        yOffset = 30;
      } else {
        yOffset += 5;
      }

      doc.setDrawColor(...blue);
      doc.setLineWidth(0.4);
      doc.line(65, yOffset + 15, 145, yOffset + 15);

      doc.setFont("helvetica", "bold");
      doc.setFontSize(9);
      doc.setTextColor(...blue);
      doc.text("f. Secretaria General", 105, yOffset + 20, { align: "center" });

      doc.setFont("helvetica", "normal");
      doc.setFontSize(8);
      doc.setTextColor(110, 110, 110);
      doc.text("Responsable de Registro y Control Académico", 105, yOffset + 24, { align: "center" });
      doc.text('Unidad Educativa Estandarizada del Milenio "Malimpia"', 105, yOffset + 28, { align: "center" });

      // Disparar la descarga directa del PDF
      doc.save(`Historial_Notas_${this.estudiante.cedula}.pdf`);
    }
  }
}
</script>

<style scoped>
/* PALETA DE COLORES */
.text-blue {
  color: #1D2A68;
}

.text-blue-light {
  color: #2c3e8c;
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

.btn-outline-blue {
  border: 2px solid #1D2A68;
  color: #1D2A68;
  background-color: transparent;
  transition: all 0.25s ease-in-out;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

.table-gold-light {
  background-color: rgba(244, 181, 36, 0.08);
}

.fw-black {
  font-weight: 900;
}

/* LÓGICA ESTRUCTURAL DE LA LÍNEA DE TIEMPO DEL PROGRESO */
.timeline-line {
  position: absolute;
  left: 17px;
  top: 15px;
  bottom: 15px;
  width: 3px;
  background-color: #e9ecef;
  z-index: 1;
}

.timeline-item {
  z-index: 2;
}

.timeline-badge {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 0.95rem;
  background: #fff;
  z-index: 3;
}

.x-small {
  font-size: 0.7rem;
  letter-spacing: 0.5px;
}

/* REGLAS DE MEDIOS EXCLUSIVAS PARA IMPRESIÓN Y EXPORTACIÓN A PDF */
@media print {

  /* Ocultar elementos de navegación innecesarios */
  .no-print,
  header,
  .no-print *,
  btn,
  button {
    display: none !important;
  }

  /* Resetear fondos y forzar renderizado de color total en PDF */
  body,
  .container-fluid,
  .min-vh-100 {
    background-color: #ffffff !important;
    color: #000000 !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Ajuste de columnas para ocupar el ancho completo del papel A4 */
  .row {
    display: flex !important;
    flex-flow: row wrap !important;
  }

  .col-md-5,
  .col-lg-4 {
    width: 30% !important;
    float: left !important;
  }

  .col-md-7,
  .col-lg-8 {
    width: 70% !important;
    float: left !important;
  }

  /* Hacer visibles los encabezados institucionales y firmas */
  .print-header,
  .print-signatures-section {
    display: block !important;
  }

  /* Estilización estricta para tablas impresas */
  .card {
    border: 1px solid #dee2e6 !important;
    box-shadow: none !important;
    background-color: #fff !important;
  }

  .bg-blue {
    background-color: #1D2A68 !important;
    color: #ffffff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .table-gold-light {
    background-color: #fdf8e9 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .badge {
    border: 1px solid #000 !important;
    color: #000 !important;
    background: transparent !important;
  }

  /* Evitar saltos de página huérfanos a mitad de una tabla */
  .page-break-inside-avoid {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
}
</style>
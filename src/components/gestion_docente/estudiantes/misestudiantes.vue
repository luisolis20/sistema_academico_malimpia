<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="row mb-4 align-items-center">
      <div class="col-md-8">
        <h2 class="fw-bold text-blue" style="font-family: 'Fraunces';">
          Mis Estudiantes por Asignatura
        </h2>
        <p class="text-muted">Seleccione una asignatura para ver sus estudiantes y gestionar su asistencia.</p>
      </div>
      <div class="col-md-4 text-md-end">
        <div class="badge bg-gold text-blue px-3 py-2 rounded-pill shadow-sm">
          Periodo Lectivo {{ periodo_activo.nombre }}
        </div>
        <button v-if="todoRegistrado" @click="generarPDFGeneral"
          class="btn btn-gold text-blue fw-bold rounded-pill shadow-sm animate__animated animate__pulse animate__infinite">
          <i class="fas fa-file-pdf me-2"></i> Reporte General de Hoy
        </button>

      </div>
    </header>

    <div class="container-fluid py-4 bg-light min-vh-100">
      <div v-if="cargando" class="text-center py-5">
        <div class="spinner-border text-blue" role="status"></div>
        <p class="mt-2 text-blue fw-bold">Cargando nóminas...</p>
      </div>

      <div v-else class="accordion border-0 shadow-sm rounded-4 overflow-hidden" id="accordionEstudiantes">
        <div v-for="(item, index) in asignaturas" :key="item.id_curso_asignatura"
          class="accordion-item border-0 border-bottom">

          <h2 class="accordion-header">
            <button class="accordion-button collapsed py-4 px-4" type="button" data-bs-toggle="collapse"
              :data-bs-target="'#collapse' + index" @click="verificarAsistencia(item, index)">
              <div class="d-flex align-items-center w-100">
                <div class="icon-box bg-blue-soft rounded-3 p-2 me-3">
                  <i class="fas fa-calendar-check text-blue"></i>
                </div>
                <div class="me-auto">
                  <h6 class="mb-0 fw-bold text-blue">{{ item.nombre_asignatura }}</h6>
                  <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                    {{ item.curso_info }} — {{ item.total_estudiantes }} Alumnos
                  </small>
                </div>
                <span v-if="item.asistencia_tomada" class="badge bg-success rounded-pill px-3 me-3">
                  <i class="fas fa-check-circle me-1"></i> Completada Hoy
                </span>
                <span v-else class="badge bg-warning text-dark rounded-pill px-3 me-3">
                  <i class="fas fa-clock me-1"></i> Pendiente
                </span>
              </div>
            </button>
          </h2>

          <div :id="'collapse' + index" class="accordion-collapse collapse" data-bs-parent="#accordionEstudiantes">
            <div class="accordion-body bg-white p-4">

              <div v-if="item.asistencia_tomada"
                class="alert bg-blue-soft border-0 rounded-4 d-flex align-items-center justify-content-between p-3 mb-4">
                <div class="d-flex align-items-center">
                  <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                  <span class="text-blue fw-bold">Asistencia completada satisfactoriamente. Regresa el día de mañana
                    para tomar otra.</span>
                </div>
                <button @click="generarPDFAsignatura(item)" class="btn btn-outline-danger btn-sm rounded-pill">
                  <i class="fas fa-file-pdf me-1"></i> Descargar Reporte
                </button>
              </div>

              <div v-else class="table-responsive rounded-4 shadow-sm">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-blue text-white">
                    <tr>
                      <th class="ps-4">Estudiante</th>
                      <th>Cédula</th>
                      <th class="text-center">Estado de Asistencia</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="est in item.estudiantes" :key="est.id_persona">
                      <td class="ps-4">
                        <div class="d-flex align-items-center">
                          <img :src="est.foto ? 'data:image/jpeg;base64,' + est.foto : getPhotoUrl(est.cedula)"
                            class="rounded-circle border border-2 border-gold me-3" width="45" height="45">
                          <span class="fw-bold text-blue">{{ est.nombres }} {{ est.apellidos }}</span>
                        </div>
                      </td>
                      <td class="text-muted small">{{ est.cedula }}</td>
                      <td style="width: 400px">
                        <div class="d-flex justify-content-center gap-2">
                          <input type="radio" class="btn-check"
                            :name="'asig' + item.id_curso_asignatura + 'est' + est.id_persona"
                            :id="'p' + est.id_persona" value="Presente" v-model="est.asistencia_actual">
                          <label class="btn btn-outline-success btn-sm rounded-pill px-3"
                            :for="'p' + est.id_persona">Presente</label>

                          <input type="radio" class="btn-check"
                            :name="'asig' + item.id_curso_asignatura + 'est' + est.id_persona"
                            :id="'a' + est.id_persona" value="Ausente" v-model="est.asistencia_actual">
                          <label class="btn btn-outline-danger btn-sm rounded-pill px-3"
                            :for="'a' + est.id_persona">Ausente</label>

                          <input type="radio" class="btn-check"
                            :name="'asig' + item.id_curso_asignatura + 'est' + est.id_persona"
                            :id="'at' + est.id_persona" value="Atraso" v-model="est.asistencia_actual">
                          <label class="btn btn-outline-warning btn-sm rounded-pill px-3"
                            :for="'at' + est.id_persona">Atraso</label>

                          <input type="radio" class="btn-check"
                            :name="'asig' + item.id_curso_asignatura + 'est' + est.id_persona"
                            :id="'j' + est.id_persona" value="Justificado" v-model="est.asistencia_actual">
                          <label class="btn btn-outline-primary btn-sm rounded-pill px-3"
                            :for="'j' + est.id_persona">Justificado</label>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div class="p-4 bg-light text-end rounded-bottom-4">
                  <button @click="guardarAsistencia(item)" class="btn btn-blue px-5 py-2 rounded-pill shadow">
                    <i class="fas fa-save me-2"></i> Registrar Asistencia Masiva
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <button @click="generarPDFHistorial" class="btn btn-blue text-white fw-bold rounded-pill shadow-sm me-2">
      <i class="fas fa-history me-2"></i> Descargar Historial Completo
    </button>
  </div>
</template>

<script>
import API from "@/assets/js/axios";
import { getMe } from "@/assets/js/auth";
import { mostraralertas } from "@/assets/js/funciones/functions";
import Swal from 'sweetalert2';
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: 0,
      asignaturas: [],
      cargando: false,
      periodo_activo: {}
    }
  },
  computed: {
    todoRegistrado() {
      return this.asignaturas.length > 0 && this.asignaturas.every(a => a.asistencia_tomada);
    }
  },
  async mounted() {
    this.cargando = true;
    const me = await getMe();
    this.idpersona = me.id_persona;
    await Promise.all([this.fetchEstudiantes(), this.getPeriodo()]);
    this.cargando = false;
  },
  methods: {
    async getPeriodo() {
      try {
        const res = await API.get(`${this.baseUrl}/periodos_lectivos_activos`);
        this.periodo_activo = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async fetchEstudiantes() {
      try {
        const res = await API.get(`${this.baseUrl}/estudiantes-asignatura/${this.idpersona}`);
        this.asignaturas = res.data.map(asig => {
          // Verificamos si al menos un estudiante tiene asistencia hoy
          // para marcar la asignatura como "tomada" automáticamente
          const yaTieneAsistencia = asig.estudiantes.some(e => e.asistencia_guardada !== null);

          return {
            ...asig,
            asistencia_tomada: yaTieneAsistencia,
            estudiantes: asig.estudiantes.map(e => ({
              ...e,
              // Si ya existe en BD, lo ponemos en asistencia_actual para el PDF y el radio button
              asistencia_actual: e.asistencia_guardada
            }))
          };
        });
      } catch (e) {
        console.error("Error al traer estudiantes", e);
      }
    },
    async verificarAsistencia(item, index) {
      try {
        const res = await API.get(`${this.baseUrl}/asistencia-check/${item.id_curso_asignatura}`);
        this.asignaturas[index].asistencia_tomada = res.data.registrada;
      } catch (e) { console.error(e); }
    },
    getPhotoUrl(ci) {
      return ci ? `${API.defaults.baseURL}/sistma/imagenpersona/${ci}` : 'https://via.placeholder.com/150';
    },
    generarPDFAsignatura(item) {
      const doc = new jsPDF();
      const fecha = new Date().toLocaleDateString();
      this.disenoBasePDF(doc, `REPORTE DE ASISTENCIA - ${item.nombre_asignatura}`);

      doc.setFontSize(10);
      doc.text(`Curso: ${item.curso_info}`, 14, 45);
      doc.text(`Especialidad: ${item.especialidad}`, 14, 50);
      doc.text(`Fecha: ${fecha}`, 14, 55);

      autoTable(doc, {
        startY: 65,
        head: [['N°', 'Estudiante', 'Cédula', 'Estado']],
        body: item.estudiantes.map((e, i) => [
          i + 1,
          `${e.apellidos} ${e.nombres}`,
          e.cedula,
          e.asistencia_actual || 'Presente' // En caso de que se consulte después de guardar
        ]),
        headStyles: { fillColor: [29, 42, 104] },
        theme: 'grid'
      });

      doc.save(`Asistencia_${item.nombre_asignatura}_${fecha}.pdf`);
    },

    // REPORTE GENERAL (Consolidado)
    generarPDFGeneral() {
      const doc = new jsPDF();
      const fecha = new Date().toLocaleDateString();

      this.disenoBasePDF(doc, "REPORTE GENERAL DE ASISTENCIAS");

      let finalY = 45;
      console.log(this.asignaturas);
      this.asignaturas.forEach((asig, index) => {
        doc.setFontSize(11);
        doc.setTextColor(29, 42, 104);
        doc.text(`Asignatura: ${asig.nombre_asignatura} (${asig.curso_info})`, 14, finalY);

        autoTable(doc, {
          startY: finalY + 5,
          head: [['Estudiante', 'Estado']],
          body: asig.estudiantes.map(e => [
            `${e.apellidos} ${e.nombres}`,
            e.asistencia_actual ? e.asistencia_actual : 'N/R' // N/R = No Registrado
          ]),
          headStyles: { fillColor: [60, 60, 60] },
          margin: { bottom: 20 },
          theme: 'striped'
        });

        finalY = doc.lastAutoTable.finalY + 15;

        // Agregar nueva página si el contenido se desborda
        if (finalY > 250 && index < this.asignaturas.length - 1) {
          doc.addPage();
          finalY = 20;
        }
      });

      doc.save(`Reporte_General_Asistencia_${fecha}.pdf`);
    },

    disenoBasePDF(doc, titulo) {
      doc.setFillColor(29, 42, 104);
      doc.rect(0, 0, 210, 35, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(16);
      doc.text(titulo, 105, 18, { align: "center" });
      doc.setFontSize(9);
      doc.text("UNIDAD EDUCATIVA MILENIO", 105, 25, { align: "center" });
      doc.setTextColor(0, 0, 0);
    },
    async generarPDFHistorial() {
      try {
        this.cargando = true;
        const res = await API.get(`${this.baseUrl}/historial-asistencia/${this.idpersona}`);
        const historial = res.data;

        if (historial.length === 0) {
          return mostraralertas("No hay historial de asistencia registrado aún.", "info");
        }

        const doc = new jsPDF();
        this.disenoBasePDF(doc, "HISTORIAL COMPLETO DE ASISTENCIAS");

        let finalY = 40;

        historial.forEach((asig, indexAsig) => {
          // Título de la Asignatura
          doc.setFontSize(14);
          doc.setTextColor(29, 42, 104);
          doc.setFont("helvetica", "bold");
          doc.text(`${asig.asignatura} - ${asig.curso}`, 14, finalY);
          finalY += 10;

          asig.registros.forEach((clase) => {
            // Subtítulo: Fecha de la clase
            doc.setFontSize(11);
            doc.setTextColor(100);
            doc.text(`Fecha de clase: ${clase.fecha}`, 14, finalY);

            autoTable(doc, {
              startY: finalY + 2,
              head: [['Estudiante', 'Estado']],
              body: clase.detalle.map(d => [d.estudiante, d.estado]),
              headStyles: { fillColor: [44, 62, 80] },
              theme: 'striped',
              margin: { left: 14, right: 14 },
              didDrawPage: (data) => {
                // Si la tabla salta de página, mantenemos el margen
                finalY = data.cursor.y;
              }
            });

            finalY = doc.lastAutoTable.finalY + 15;

            // Verificar espacio para la siguiente tabla de fecha
            if (finalY > 260) {
              doc.addPage();
              finalY = 20;
            }
          });

          // Espacio mayor entre diferentes asignaturas
          finalY += 10;
          if (finalY > 260 && indexAsig < historial.length - 1) {
            doc.addPage();
            finalY = 20;
          }
        });

        doc.save(`Historial_Asistencia_${new Date().getTime()}.pdf`);
      } catch (e) {
        console.error(e);
        mostraralertas("Error al generar el historial", "error");
      } finally {
        this.cargando = false;
      }
    },

    async guardarAsistencia(item) {
      // 1. Validar que todos tengan selección
      const incompletos = item.estudiantes.some(e => !e.asistencia_actual);

      if (incompletos) {
        return mostraralertas("Debe seleccionar la asistencia de todos los estudiantes", "warning");
      }

      Swal.fire({
        title: '¿Confirmar asistencia?',
        text: `Se registrará la asistencia de ${item.total_estudiantes} estudiantes para hoy.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1D2A68',
        confirmButtonText: 'Sí, registrar'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const payload = {
              id_curso_asignatura: item.id_curso_asignatura,
              asistencias: item.estudiantes.map(e => ({
                id_matricula: e.id_matricula, // Asegúrate que el backend envíe este ID en el fetch
                estado: e.asistencia_actual
              }))
            };

            await API.post(`${this.baseUrl}/asistencias-hoy`, payload);
            item.asistencia_tomada = true;
            mostraralertas("Asistencia guardada con éxito", "success");
          } catch (e) {
            mostraralertas(e.response.data.error || "Error al guardar", "error");
          }
        }
      });
    }
  },
}

</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}

.bg-blue {
  background-color: #1D2A68;
}

.bg-blue-soft {
  background-color: rgba(29, 42, 104, 0.08);
}

.bg-gold {
  background-color: #F4B324;
}

.border-gold {
  border-color: #F4B324 !important;
}

.accordion-item {
  background-color: transparent;
}

.accordion-button:not(.collapsed) {
  background-color: #fff;
  color: #1D2A68;
  box-shadow: none;
}

.accordion-button:focus {
  box-shadow: none;
  border-color: rgba(29, 42, 104, 0.1);
}

.card-estudiante {
  border-radius: 15px;
  background-color: #fcfcfc;
}

.card-estudiante:hover {
  transform: translateY(-3px);
  background-color: #fff;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
}

.icon-box {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.x-small {
  font-size: 0.65rem;
}

.transition-all {
  transition: all 0.3s ease;
}

/* Estilos anteriores + estos para los radio buttons */
.btn-check:checked+.btn-outline-success {
  background-color: #198754;
  color: white;
}

.btn-check:checked+.btn-outline-danger {
  background-color: #dc3545;
  color: white;
}

.btn-check:checked+.btn-outline-warning {
  background-color: #ffc107;
  color: #212529;
}

.btn-check:checked+.btn-outline-primary {
  background-color: #0d6efd;
  color: white;
}

.btn-blue {
  background-color: #1D2A68;
  color: white;
  transition: all 0.3s;
}

.btn-blue:hover {
  background-color: #F4B324;
  transform: scale(1.02);
}

.table th {
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.bg-blue-soft {
  background-color: rgba(29, 42, 104, 0.05);
}
</style>
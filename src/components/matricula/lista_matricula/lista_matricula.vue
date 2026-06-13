<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

      <!-- FILA SUPERIOR: Títulos y Buscador -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">

        <!-- Título e Icono Principal -->
        <div class="mb-2 mb-md-0 d-flex align-items-center">
          <div
            class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3 min-vw-auto"
            style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px; min-width: 55px;">
            <i class="fas fa-user-graduate fs-4"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
              Historial Académico
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
              Ingrese la cédula del estudiante para consultar su expediente de matrículas.
            </p>
          </div>
        </div>

        <!-- Componentes de la Derecha (Buscador) -->
        <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0" style="max-width: 450px; width: 100%;">
          <div class="input-group shadow-sm rounded-pill overflow-hidden border"
            style="border-color: rgba(29, 42, 104, 0.2) !important;">
            <input type="text" class="form-control border-0 px-4 py-2" placeholder="Ej. 0801234567"
              v-model="cedulaBusqueda" @keyup.enter="buscarEstudiante" :disabled="buscando" style="box-shadow: none;">
            <button class="btn fw-bold px-4 border-0 d-flex align-items-center transition-all" @click="buscarEstudiante"
              :disabled="!cedulaBusqueda || buscando" style="background-color: #F4B324; color: #1D2A68;">
              <i class="fas fa-search me-2"></i> Buscar
            </button>
          </div>
        </div>
      </div>

      <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
      <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
        style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
        <i class="fas fa-archive fs-5 mt-1" style="color: #F4B324;"></i>
        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Trazabilidad y Auditoría de Expedientes:</strong> Este motor de búsqueda consolida la trayectoria
          histórica del estudiante a lo largo de múltiples periodos lectivos. A nivel de base de datos, el sistema
          extrae de forma eficiente registros relacionales que son inmutables (matrículas pasadas). Esta consolidación
          garantiza la <strong>persistencia a largo plazo</strong> de la información académica, proveyendo a la
          institución de una herramienta auditable y robusta para la validación de perfiles y emisión de certificados.
        </p>
      </div>

    </header>

    <div class="row" v-if="estudiante">

      <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body text-center p-4">
            <img :src="estudiante.foto ? 'data:image/jpeg;base64,' + estudiante.foto : '/avatar-default.png'"
              alt="Foto Estudiante" class="rounded-circle mb-3 border border-gold border-3"
              style="width: 120px; height: 120px; object-fit: cover;">
            <h5 class="fw-bold text-blue mb-1">{{ estudiante.apellidos }} {{ estudiante.nombres }}</h5>
            <p class="text-muted small mb-2"><i class="fas fa-id-card me-1"></i> Cédula: {{ estudiante.cedula }}</p>
            <span class="badge bg-gold text-blue w-100 py-2 mt-2" v-if="matriculaSeleccionada">
              Matrícula Actual/Seleccionada: {{ matriculaSeleccionada.curso.nivel.nombre }} "{{
                matriculaSeleccionada.curso.paralelo }}"
            </span>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold text-blue mb-0"><i class="fas fa-history me-2"></i> Periodos Matriculados</h6>
          </div>
          <div class="list-group list-group-flush rounded-bottom-4">
            <button v-for="(mat, index) in estudiante.matriculasestudiantes" :key="mat.id_matricula"
              @click="seleccionarMatricula(mat)"
              class="list-group-item list-group-item-action p-3 transition-all border-0 border-bottom"
              :class="{ 'active-selection text-white': matriculaSeleccionada && matriculaSeleccionada.id_matricula === mat.id_matricula }">
              <div class="d-flex w-100 justify-content-between align-items-center">
                <h6 class="mb-1 fw-bold"
                  :class="{ 'text-white': matriculaSeleccionada && matriculaSeleccionada.id_matricula === mat.id_matricula, 'text-blue': !matriculaSeleccionada || matriculaSeleccionada.id_matricula !== mat.id_matricula }">
                  {{ mat.curso.periodo.nombre }}
                </h6>
                <div class="d-flex gap-1 align-items-center">
                  <span :class="mat.estado === 'Anulada' ? 'badge bg-danger x-small' : 'badge bg-success x-small'">
                    {{ mat.estado || 'Activa' }}
                  </span>
                  <span v-if="mat.curso.periodo.estado_activo == 1"
                    class="badge bg-primary x-small text-white">ACTIVO</span>
                  <span v-else class="badge bg-secondary x-small">CERRADO</span>
                </div>
              </div>
              <p class="mb-1 small">
                {{ mat.curso.nivel.nombre }} - {{ mat.curso.especialidad.nombre }}
              </p>
              <small
                :class="{ 'text-light': matriculaSeleccionada && matriculaSeleccionada.id_matricula === mat.id_matricula, 'text-muted': !matriculaSeleccionada || matriculaSeleccionada.id_matricula !== mat.id_matricula }">
                Fecha de matrícula: {{ mat.fecha_matricula }}
              </small>
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100" v-if="matriculaSeleccionada">
          <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                  <h4 class="fw-bold text-blue mb-0">
                    {{ matriculaSeleccionada.curso.nivel.nombre }} "{{ matriculaSeleccionada.curso.paralelo }}"
                  </h4>
                  <span
                    :class="matriculaSeleccionada.estado === 'Anulada' ? 'badge bg-danger fs-6' : 'badge bg-success fs-6'">
                    <i
                      :class="matriculaSeleccionada.estado === 'Anulada' ? 'fas fa-ban me-1' : 'fas fa-check-circle me-1'"></i>
                    {{ matriculaSeleccionada.estado || 'Activa' }}
                  </span>
                </div>
                <h6 class="text-muted">{{ matriculaSeleccionada.curso.especialidad.nombre }}</h6>
              </div>
              <div class="text-end d-flex flex-column align-items-end gap-2">
                <span class="badge bg-blue fs-6 px-3 py-2 border border-gold">
                  {{ matriculaSeleccionada.curso.periodo.nombre }}
                </span>

                <button v-if="matriculaSeleccionada.estado !== 'Anulada'"
                  @click="confirmarAnulacion(matriculaSeleccionada.id_matricula)"
                  class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm mt-1 animate__animated animate__fadeIn">
                  <i class="fas fa-times-circle me-1"></i> Anular Matrícula
                </button>
              </div>
            </div>

            <div class="bg-light p-3 rounded-3 mb-4 border-start border-blue border-4">
              <p class="mb-0 text-blue fw-bold">
                <i class="fas fa-chalkboard-teacher me-2 text-gold"></i> Docente Tutor:
                <span class="fw-normal text-dark ms-2">
                  {{ matriculaSeleccionada.curso.docentetutor ? matriculaSeleccionada.curso.docentetutor.apellidos + ' '
                    + matriculaSeleccionada.curso.docentetutor.nombres : 'Sin asignar' }}
                </span>
              </p>
            </div>

            <h6 class="fw-bold text-blue mb-3"><i class="fas fa-book me-2"></i> Malla Curricular y Docentes</h6>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="bg-blue text-white">
                  <tr>
                    <th class="rounded-start">#</th>
                    <th>Asignatura</th>
                    <th>Docente Designado</th>
                    <th class="text-center rounded-end">Horas Semanales</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(ca, i) in matriculaSeleccionada.curso.curso_asignaturas" :key="ca.id_curso_asignatura">
                    <td class="fw-bold text-muted">{{ i + 1 }}</td>
                    <td class="fw-bold text-dark">{{ ca.asignatura.nombre }}</td>
                    <td>
                      <div class="d-flex align-items-center" v-if="ca.docente">
                        <div
                          class="bg-gold rounded-circle d-flex justify-content-center align-items-center me-2 text-blue fw-bold"
                          style="width: 30px; height: 30px; font-size: 0.8rem;">
                          {{ ca.docente.nombres.charAt(0) }}{{ ca.docente.apellidos.charAt(0) }}
                        </div>
                        {{ ca.docente.apellidos }} {{ ca.docente.nombres }}
                      </div>
                      <span v-else class="text-danger small fst-italic">Sin docente asignado</span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-light text-dark border">{{ ca.horas_semanales }} h</span>
                    </td>
                  </tr>
                  <tr
                    v-if="!matriculaSeleccionada.curso.curso_asignaturas || matriculaSeleccionada.curso.curso_asignaturas.length === 0">
                    <td colspan="4" class="text-center py-4 text-muted">
                      No hay asignaturas registradas para este curso en este periodo.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </div>

    <div v-else class="text-center py-5 mt-5">
      <div class="display-1 text-muted opacity-25 mb-3">
        <i class="fas fa-user-graduate"></i>
      </div>
      <h4 class="text-muted fw-light">Busque un estudiante para ver su historial académico</h4>
    </div>

  </div>
</template>

<script>
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      baseUrl: "/sistma",
      cedulaBusqueda: "",
      buscando: false,
      estudiante: null,
      matriculaSeleccionada: null
    }
  },
  methods: {
    async buscarEstudiante() {
      if (!this.cedulaBusqueda) return;

      this.buscando = true;
      this.estudiante = null;
      this.matriculaSeleccionada = null;

      try {
        // Asegúrate de cambiar esta ruta por la ruta real de tu API en web.php / api.php
        const response = await API.get(`${this.baseUrl}/buscar-historial-matriculas/${this.cedulaBusqueda}`);

        if (response.data.success) {
          this.estudiante = response.data.estudiante;
          // Si tiene matrículas, seleccionamos automáticamente la primera (la más reciente/activa gracias al backend)
          if (this.estudiante.matriculasestudiantes && this.estudiante.matriculasestudiantes.length > 0) {
            this.seleccionarMatricula(this.estudiante.matriculasestudiantes[0]);
          }
        }
      } catch (error) {
        let msj = "Error al buscar el estudiante.";
        if (error.response && error.response.data && error.response.data.message) {
          msj = error.response.data.message;
        }
        mostraralertas(msj, "warning");
      } finally {
        this.buscando = false;
      }
    },

    seleccionarMatricula(matricula) {
      this.matriculaSeleccionada = matricula;
    },
    async confirmarAnulacion(id_matricula) {
      const result = await Swal.fire({
        title: '¿Está seguro de anular esta matrícula?',
        text: "Esta acción cambiará el estado del estudiante a 'Anulada' de manera irreversible dentro de este periodo.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Color rojo descriptivo para acciones críticas
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, anular matrícula',
        cancelButtonText: 'Cancelar'
      });

      if (result.isConfirmed) {
        try {
          // Llamada dinámica mediante axios mutando el estado en la base de datos
          const response = await API.put(`${this.baseUrl}/anular_matricula/${id_matricula}`);

          if (response.data.success) {
            mostraralertas(response.data.message, "success");

            // Actualización reactiva local sin recargar forzosamente la vista completa
            this.matriculaSeleccionada.estado = 'Anulada';

            // Sincronizar el cambio dentro de la lista lateral de periodos matriculados
            const idx = this.estudiante.matriculasestudiantes.findIndex(m => m.id_matricula === id_matricula);
            if (idx !== -1) {
              this.estudiante.matriculasestudiantes[idx].estado = 'Anulada';
            }
          }
        } catch (error) {
          let msj = "No se pudo anular la matrícula.";
          if (error.response && error.response.data && error.response.data.message) {
            msj = error.response.data.message;
          }
          mostraralertas(msj, "error");
        }
      }
    }
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

/* Efecto visual al seleccionar una matrícula */
.active-selection {
  background-color: #1D2A68 !important;
  transform: translateX(10px);
  border-left: 5px solid #F4B324 !important;
  /* Toque dorado */
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

.border-blue {
  border-color: #1D2A68 !important;
}
</style>
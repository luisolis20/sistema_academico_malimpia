<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

      <!-- FILA SUPERIOR: Títulos y Estado del Periodo -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">

        <!-- Título e Icono Principal -->
        <div class="mb-2 mb-md-0 d-flex align-items-center">
          <div
            class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3 min-vw-auto"
            style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px; min-width: 55px;">
            <i class="fas fa-clipboard-check fs-4"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
              Control de Subida de Calificaciones
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
              Panel de monitoreo y autorización para el registro de notas docentes.
            </p>
          </div>
        </div>

        <!-- Componentes de la Derecha (Badge de Periodo Activo) -->
        <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0">
          <div v-if="periodoActivo"
            class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border shadow-sm w-100 justify-content-center"
            style="background-color: rgba(29, 42, 104, 0.05); border-color: rgba(29, 42, 104, 0.2) !important; color: #1D2A68; white-space: nowrap;">
            <i class="fas fa-flag-checkered me-2" style="color: #F4B324;"></i>
            <span class="fw-medium">
              Periodo Activo: <strong class="ms-1">{{ periodoActivo.nombre }}</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
      <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
        style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
        <i class="fas fa-user-shield fs-5 mt-1" style="color: #F4B324;"></i>
        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Monitoreo Transaccional y Cierre de Auditoría:</strong> Este panel ejerce un control estricto sobre
          los permisos de escritura en la base de datos de calificaciones. Gestionar las ventanas de tiempo en las que
          los docentes pueden interactuar con las matrices previene modificaciones extemporáneas y asegura el principio
          de integridad de la información. A nivel de arquitectura, estas restricciones actúan como bloqueos de
          seguridad (<em>locks</em>) que validan el estado del periodo antes de permitir cualquier operación de
          inserción o actualización, garantizando la emisión de actas consolidadas y fiables.
        </p>
      </div>

    </header>

    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-blue text-white">
              <tr>
                <th class="ps-4 py-3">Fase de Evaluación</th>
                <th>Fecha y Hora Inicio</th>
                <th>Fecha y Hora Fin</th>
                <th>Estado</th>
                <th class="text-center pe-4">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fase in fasesPermitidas" :key="fase">
                <td class="ps-4 fw-bold text-blue">
                  <i class="fas fa-calendar-check me-2 text-gold"></i> {{ formatNombreFase(fase) }}
                </td>

                <template v-if="getControl(fase)">
                  <td>{{ formatearFechaVisual(getControl(fase).fecha_inicio) }}</td>
                  <td>{{ formatearFechaVisual(getControl(fase).fecha_fin) }}</td>
                  <td>
                    <span class="badge px-3 py-2"
                      :class="getControl(fase).habilitado == 1 ? 'bg-success' : 'bg-secondary'">
                      {{ getControl(fase).habilitado == 1 ? 'HABILITADO' : 'INHABILITADO' }}
                    </span>
                  </td>
                  <td class="text-center pe-4">
                    <button @click="abrirModalEditar(getControl(fase))"
                      class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 me-2" title="Editar Fechas">
                      <i class="fas fa-edit"></i>
                    </button>

                    <button v-if="getControl(fase).habilitado == 1"
                      @click="cambiarEstado(getControl(fase).id_control, 'inhabilitar')"
                      class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                      <i class="fas fa-times-circle me-1"></i> Deshabilitar
                    </button>
                    <button v-else @click="cambiarEstado(getControl(fase).id_control, 'habilitar')"
                      class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3"
                      :disabled="faseDeshabilitadaPorRegla(fase)"
                      :title="faseDeshabilitadaPorRegla(fase) ? 'Bloqueado por reglas de Quimestre' : ''">
                      <i class="fas fa-check-circle me-1"></i> Habilitar
                    </button>
                  </td>
                </template>

                <template v-else>
                  <td colspan="3" class="text-center text-muted fst-italic py-3">
                    No configurado para este periodo
                  </td>
                  <td class="text-center pe-4">
                    <button @click="abrirModalCrear(fase)" class="btn btn-sm btn-gold fw-bold rounded-pill px-3"
                      :disabled="faseDeshabilitadaPorRegla(fase)">
                      <i class="fas fa-cog me-1"></i> Configurar
                    </button>
                  </td>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalConfigurarControl" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
          <div class="modal-header bg-blue text-white rounded-top-4">
            <h5 class="modal-title fw-bold">
              {{ form.id_control ? 'Editar' : 'Configurar' }} {{ formatNombreFase(form.fase_evaluacion) }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="guardarControl">
              <div class="mb-3">
                <label class="form-label fw-bold text-blue">Fecha y Hora de Inicio</label>
                <input type="datetime-local" class="form-control border-gold" v-model="form.fecha_inicio" required>
              </div>
              <div class="mb-4">
                <label class="form-label fw-bold text-blue">Fecha y Hora de Fin</label>
                <input type="datetime-local" class="form-control border-gold" v-model="form.fecha_fin" required>
              </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="habilitarInmediato" v-model="form.habilitado"
                  :disabled="faseDeshabilitadaPorRegla(form.fase_evaluacion)">
                <label class="form-check-label text-muted" for="habilitarInmediato">Habilitar subida de notas</label>
                <div v-if="faseDeshabilitadaPorRegla(form.fase_evaluacion)" class="text-danger small mt-1">
                  No se puede habilitar debido a reglas de quimestre.
                </div>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-gold fw-bold py-2" :disabled="guardando">
                  <span v-if="guardando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i class="fas fa-save me-2" v-else></i> {{ form.id_control ? 'Actualizar' : 'Guardar' }} Configuración
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap'; // Asegúrate de tener bootstrap importado para el modal

export default {
  data() {
    return {
      fasesPermitidas: ['Q1_P1', 'Q1_P2', 'Q1_P3', 'Q1_EXAMEN', 'Q2_P1', 'Q2_P2', 'Q2_P3', 'Q2_EXAMEN', 'SUPLETORIO', 'REMEDIAL', 'GRACIA'],
      controlesCreados: [],
      periodoActivo: null,
      cargando: false,
      guardando: false,
      modalInstance: null,
      form: {
        id_control: null,
        id_periodo: null,
        fase_evaluacion: '',
        fecha_inicio: '',
        fecha_fin: '',
        habilitado: false
      },
      baesUrl: '/sistma'
    }
  },
  computed: {
    // Verifica si hay ALGUNA fase del Q1 habilitada
    q1TieneActivos() {
      return this.controlesCreados.some(c => c.fase_evaluacion.startsWith('Q1') && c.habilitado == 1);
    },
    // Verifica si hay ALGUNA fase del Q2 habilitada
    q2TieneActivos() {
      return this.controlesCreados.some(c => c.fase_evaluacion.startsWith('Q2') && c.habilitado == 1);
    }
  },
  mounted() {
    this.cargarControles();
  },
  methods: {
    async cargarControles() {
      this.cargando = true;
      try {
        const response = await API.get(`${this.baesUrl}/control_subida_notas`);
        if (response.status === 200) {
          this.controlesCreados = response.data.data;
          this.periodoActivo = response.data.periodo_activo;
        }
      } catch (error) {
        mostraralertas("Error al cargar los controles", "error");
      } finally {
        this.cargando = false;
      }
    },

    getControl(fase) {
      return this.controlesCreados.find(c => c.fase_evaluacion === fase) || null;
    },

    faseDeshabilitadaPorRegla(fase) {
      if (!fase) return false;
      if (fase.startsWith('Q1') && this.q2TieneActivos) return true;
      if (fase.startsWith('Q2') && this.q1TieneActivos) return true;
      return false;
    },

    formatNombreFase(fase) {
      if (!fase) return '';
      const nombres = {
        'Q1_P1': 'Quimestre 1 - Parcial 1',
        'Q1_P2': 'Quimestre 1 - Parcial 2',
        'Q1_P3': 'Quimestre 1 - Parcial 3',
        'Q1_EXAMEN': 'Quimestre 1 - Examen',
        'Q2_P1': 'Quimestre 2 - Parcial 1',
        'Q2_P2': 'Quimestre 2 - Parcial 2',
        'Q2_P3': 'Quimestre 2 - Parcial 3',
        'Q2_EXAMEN': 'Quimestre 2 - Examen',
        'SUPLETORIO': 'Supletorio',
        'REMEDIAL': 'Remedial',
        'GRACIA': 'Gracia'
      };
      return nombres[fase] || fase;
    },
    formatearParaInput(fecha) {
      if (!fecha) return '';
      return fecha.replace(' ', 'T').slice(0, 16);
    },
    // Formatea para mostrar en la tabla más bonito
    formatearFechaVisual(fecha) {
      if (!fecha) return '';
      return new Date(fecha).toLocaleString('es-ES', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
      });
    },
    abrirModalCrear(fase) {
      this.form = {
        id_control: null,
        id_periodo: this.periodoActivo.id_periodo,
        fase_evaluacion: fase,
        fecha_inicio: '',
        fecha_fin: '',
        habilitado: false
      };
      this.mostrarModal();
    },
    abrirModalEditar(control) {
      this.form = {
        id_control: control.id_control,
        id_periodo: control.id_periodo,
        fase_evaluacion: control.fase_evaluacion,
        fecha_inicio: this.formatearParaInput(control.fecha_inicio),
        fecha_fin: this.formatearParaInput(control.fecha_fin),
        habilitado: control.habilitado == 1
      };
      this.mostrarModal();
    },
    mostrarModal() {
      const modalElement = document.getElementById('modalConfigurarControl');
      if (!this.modalInstance) {
        this.modalInstance = new bootstrap.Modal(modalElement);
      }
      this.modalInstance.show();
    },

    async guardarControl() {
      this.guardando = true;
      try {
        const payload = { ...this.form, habilitado: this.form.habilitado ? 1 : 0 };

        let response;
        if (this.form.id_control) {
          // Si tiene ID, hacemos un UPDATE (PUT)
          response = await API.put(`${this.baesUrl}/control_subida_notas/${this.form.id_control}`, payload);
        } else {
          // Si no tiene ID, hacemos un CREATE (POST)
          response = await API.post(`${this.baesUrl}/control_subida_notas`, payload);
        }

        mostraralertas(response.data.mensaje, "success");
        this.modalInstance.hide();
        this.cargarControles();
      } catch (error) {
        let msj = "Error al guardar el control";
        if (error.response && error.response.data.mensaje) {
          msj = error.response.data.mensaje;
        }
        mostraralertas(msj, "warning");
      } finally {
        this.guardando = false;
      }
    },

    async cambiarEstado(id, accion) {
      try {
        //'destroy' es para inhabilitar y 'habilitar' es para habilitar
        const ruta = accion === 'habilitar' ? `${this.baesUrl}/habilitar_control_subida_notas/${id}` : `${this.baesUrl}/control_subida_notas/${id}`;
        const metodo = accion === 'habilitar' ? API.delete : API.get; // Ajusta según tu routes/api.php

        const response = await metodo(ruta);

        mostraralertas(response.data.mensaje, "success");
        this.cargarControles();
      } catch (error) {
        let msj = "Error al cambiar el estado";
        if (error.response && error.response.data.mensaje) {
          msj = error.response.data.mensaje;
        }
        mostraralertas(msj, "warning");
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

.btn-gold:hover {
  background-color: #df9e19;
  color: #1D2A68;
}

.btn-outline-blue {
  border: 2px solid #1D2A68;
  color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}
</style>
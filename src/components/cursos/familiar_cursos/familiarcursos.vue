<template>
  <div class="container-fluid py-4">
    <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">
        <div class="mb-0 d-flex align-items-center">
          <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
            style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px; min-width: 55px;">
            <i class="fas fa-users fs-4"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
              Cursos del Familiar Matriculado
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
              Lista de cursos asignados a sus estudiantes representados.
            </p>
          </div>
        </div>
      </div>

      <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
        style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
        <i class="fas fa-link fs-5 mt-1" style="color: #F4B324;"></i>
        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Vinculación Parental y Control de Accesos (RBAC):</strong> Este panel materializa la relación de
          dependencia entre los perfiles de representante y estudiante. A nivel de arquitectura de software, el sistema
          emplea un estricto control de acceso para filtrar y desplegar de manera exclusiva la información de los
          familiares vinculados. Esta segmentación de consultas asegura la total privacidad de los datos frente a
          terceros, fomentando al mismo tiempo la transparencia institucional y el seguimiento continuo del rendimiento
          por parte del núcleo familiar.
        </p>
      </div>

    </header>

    <div class="row g-4">
      <div v-for="m in matriculas" :key="m.id_matricula" class="col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="card h-100 shadow-sm border-0 card-square-hover" @click="verDetalle(m)">
          <div class="card-body d-flex flex-column align-items-center text-center p-4">
            <div class="avatar-estudiante mb-3">
              <img :src="getPhotoUrl(m.estudiante.id_persona)" class="rounded-circle border border-3 border-gold"
                width="80" height="80">
            </div>
            <h6 class="fw-bold text-blue mb-1">{{ m.estudiante.nombres }}</h6>
            <p class="text-muted small mb-3">{{ m.estudiante.apellidos }}</p>

            <hr class="w-100 my-2">

            <div class="info-curso mt-2">
              <span class="badge bg-blue mb-2">{{ m.curso.nivel.nombre }} "{{ m.curso.paralelo }}"</span>
              <p class="small fw-bold text-gold text-uppercase mb-1">{{ m.curso.especialidad.nombre }}</p>
              <p class="x-small text-muted">
                <i class="fas fa-chalkboard-teacher me-1"></i> Tutor:
                {{ m.curso.docentetutor ?
                  m.curso.docentetutor.nombres + ' ' +
                  m.curso.docentetutor.apellidos : 'No asignado' }}
              </p>
            </div>

            <div class="mt-auto pt-3">
              <button class="btn btn-outline-blue btn-sm rounded-pill px-4">Ver Horario</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="matriculas.length === 0" class="col-12 text-center py-5">
        <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
        <p class="text-muted">No se encontraron familiares matriculados.</p>
      </div>
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
          <div class="modal-header bg-blue text-white rounded-top-4 p-4">
            <h5 class="modal-title fw-bold">
              <i class="fas fa-graduation-cap me-2 text-gold"></i>
              Detalle Académico: {{ detalleSel?.estudiante.nombres }} {{ detalleSel?.estudiante.apellidos }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 bg-light">
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                  <h6 class="fw-bold text-blue border-bottom pb-2">Información del Curso</h6>
                  <p class="mb-1 small"><strong>Nivel:</strong> {{ detalleSel?.curso.nivel.nombre }}</p>
                  <p class="mb-1 small"><strong>Paralelo:</strong> {{ detalleSel?.curso.paralelo }}</p>
                  <p class="mb-1 small"><strong>Especialidad:</strong> {{ detalleSel?.curso.especialidad.nombre }}</p>
                  <p class="mb-0 small"><strong>Tutor:</strong> {{ detalleSel?.curso.docentetutor?.nombres }} {{
                    detalleSel?.curso.docentetutor?.apellidos }}</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-3">
                  <h6 class="fw-bold text-blue border-bottom pb-2">Asignaturas y Docentes</h6>
                  <div class="list-group list-group-flush">
                    <div v-for="asig in detalleSel?.curso.curso_asignaturas" :key="asig.id_curso_asignatura"
                      class="py-2">
                      <p class="mb-0 fw-bold small text-blue">{{ asig.asignatura.nombre }}</p>
                      <p class="mb-0 x-small text-muted">{{ asig.docente.nombres }} {{ asig.docente.apellidos }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-8">
                <div class="table-responsive shadow-sm rounded-3">
                  <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="bg-blue text-white">
                      <tr>
                        <th style="width: 15%">Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(fila, index) in horarioAgrupado" :key="index">
                        <td class="fw-bold text-blue bg-light">
                          {{ fila.rango }}
                        </td>

                        <td v-if="fila.esRecreo" colspan="5" class="bg-recreo text-blue fw-bold py-3">
                          R E C R E O
                        </td>

                        <template v-else>
                          <td v-for="dia in ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']" :key="dia"
                            class="p-2" :class="{ 'bg-clase': fila[dia] }">
                            <div v-if="fila[dia]">
                              <div class="fw-bold text-blue mb-1" style="font-size: 0.8rem;">
                                {{ fila[dia].asignatura }}
                              </div>
                            </div>
                          </td>
                        </template>
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
</template>

<script>
import API from "@/assets/js/axios";
import { getMe } from "@/assets/js/auth";
import * as bootstrap from 'bootstrap';

export default {
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: 0,
      matriculas: [],
      detalleSel: null,
      horarioAgrupado: [],
      modal: null
    }
  },
  async mounted() {
    const me = await getMe();
    this.idpersona = me.id_persona;
    this.modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
    this.getMatriculas();
  },
  methods: {
    async getMatriculas() {
      try {
        const res = await API.get(`${this.baseUrl}/matriculas-representante/${this.idpersona}`);
        this.matriculas = res.data;
      } catch (e) {
        console.error(e);
      }
    },
    getPhotoUrl(ci) {
      return ci ? `${API.defaults.baseURL}/sistma/imagenpersona/${ci}` : 'https://via.placeholder.com/150';
    },
    verDetalle(m) {
      this.detalleSel = m;
      this.procesarHorario(m.curso.curso_asignaturas);
      this.modal.show();
    },
    procesarHorario(cursoAsignaturas) {
      const grupos = {};

      // 1. Agrupar las materias por rango de horas
      cursoAsignaturas.forEach(ca => {
        ca.horarios_clases.forEach(h => {
          // Formateamos el rango para que sea la llave (ej: "07:15 - 08:00")
          const inicio = h.hora_inicio.substring(0, 5);
          const fin = h.hora_fin.substring(0, 5);
          const rango = `${inicio} - ${fin}`;

          if (!grupos[rango]) {
            grupos[rango] = {
              rango,
              Lunes: null, Martes: null, Miércoles: null, Jueves: null, Viernes: null,
              esRecreo: false
            };
          }

          grupos[rango][h.dia_semana] = {
            asignatura: ca.asignatura.nombre,
          };
        });
      });

      // 2. Convertir a array y ordenar por hora de inicio
      let horarioFinal = Object.values(grupos).sort((a, b) => a.rango.localeCompare(b.rango));

      // 3. INYECTAR EL RECREO
      // Definimos a qué hora empieza el recreo según tu imagen (09:30)
      const HORA_RECREO_INICIO = "09:30";
      const HORA_RECREO_FIN = "10:00";
      const filaRecreo = {
        rango: `${HORA_RECREO_INICIO} - ${HORA_RECREO_FIN}`,
        esRecreo: true
      };

      // Buscamos la posición correcta para insertar el recreo (después de las 09:30 o antes de las 10:00)
      const indexInsert = horarioFinal.findIndex(h => h.rango.split(' - ')[0] >= HORA_RECREO_INICIO);

      if (indexInsert !== -1) {
        horarioFinal.splice(indexInsert, 0, filaRecreo);
      } else {
        horarioFinal.push(filaRecreo);
      }

      this.horarioAgrupado = horarioFinal;
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

.bg-blue-soft {
  background-color: rgba(29, 42, 104, 0.05);
}

.text-gold {
  color: #F4B324;
}

.bg-gold {
  background-color: #F4B324;
}

.btn-outline-blue {
  border-color: #1D2A68;
  color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

.card-square-hover {
  aspect-ratio: 1 / 1;
  cursor: pointer;
  transition: all 0.3s ease;
}

.card-square-hover:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.x-small {
  font-size: 0.7rem;
}

.avatar-estudiante img {
  object-fit: cover;
}

.text-blue {
  color: #1D2A68 !important;
}

.bg-blue {
  background-color: #1D2A68 !important;
}

.text-gold {
  color: #F4B324 !important;
}

/* Estilo exacto para la fila de RECREO */
.bg-recreo {
  background-color: #F4B324 !important;
  /* Color Oro/Naranja */
  letter-spacing: 15px;
  /* Espaciado entre letras como en la imagen */
  font-size: 1.2rem;
  border-left: none !important;
  border-right: none !important;
}

/* Color de fondo suave para las celdas con materia */
.bg-clase {
  background-color: #ffffff;
}

.table-bordered {
  border: 1px solid #dee2e6 !important;
}

.table th {
  text-transform: uppercase;
  font-size: 0.85rem;
  padding: 12px;
}

/* Efecto hover para las filas */
tbody tr:hover:not(:has(.bg-recreo)) {
  background-color: rgba(29, 42, 104, 0.02);
}
</style>
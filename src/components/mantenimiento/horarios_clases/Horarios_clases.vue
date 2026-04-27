<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header"
            style="border-left: 6px solid #F4B324;">
            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                    style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                    <i class="fas fa-calendar-alt fs-4" style="color: #F4B324;"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                        Gestión de Horarios de Clases
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración de horarios para asignaturas, cursos y docentes. Crea, edita y organiza los
                        horarios de clases de manera eficiente para garantizar una planificación académica óptima.
                    </p>
                </div>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre del curso...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de un curso para buscar en la
                    base de datos.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #1D2A68 !important;">
                        <tr>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Id Curso</th>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Curso</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Periodo</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Estado Horario</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="curso in objetoList" :key="curso.id_curso">
                            <td class="ps-4 fw-bold text-secondary">{{ curso.id_curso }}</td>
                            <td>
                                <div class="d-flex align-items-center" v-if="curso.nivel === '0'">
                                    <span>{{ curso.nombre_especialidad }} "{{ curso.paralelo }}"</span>
                                </div>
                                <div class="d-flex align-items-center" v-else>
                                    <span>{{ curso.nivel }} {{ curso.nombre_especialidad }} "{{ curso.paralelo
                                        }}"</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ curso.nombre_periodo }}</span>
                            </td>
                            <td class="text-center">
                                <span v-if="curso.tiene_horario" class="badge text-white"
                                    style="background-color: #1D2A68;">Horario Creado</span>
                                <span v-else class="badge fw-bold"
                                    style="background-color: #F4B324; color: #1D2A68;">Sin Horario</span>
                            </td>
                            <td class="text-center">
                                <button v-if="curso.tiene_horario" @click="abrirModalVista(curso)"
                                    class="btn btn-sm interactive-btn me-2"
                                    style="color: #1D2A68; border: 1px solid rgba(29, 42, 104, 0.5); background-color: transparent;">
                                    <i class="fas fa-eye" style="color: #F4B324;"></i> Ver Horario
                                </button>
                                <button v-if="curso.tiene_horario" @click="abrirModal(curso)"
                                    class="btn btn-sm interactive-btn fw-bold"
                                    style="background-color: #F4B324; color: #1D2A68; border: none;">
                                    <i class="fas fa-edit"></i> Editar Horario
                                </button>
                                <button v-else @click="abrirModal(curso)"
                                    class="btn btn-sm text-white interactive-btn fw-bold"
                                    style="background-color: #1D2A68; border: none;">
                                    <i class="fas fa-calendar-plus" style="color: #F4B324;"></i> Crear Horario
                                </button>
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #1D2A68;"></i>
                                Cargando
                                información...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3"
                v-if="lastPage > 1">
                <span class="text-muted small">
                    Página <strong style="color: #1D2A68;">{{ currentPage }}</strong> de <strong
                        style="color: #1D2A68;">{{ lastPage }}</strong>
                </span>

                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                            <button class="page-link shadow-none" style="color: #1D2A68; border-color: #dee2e6;"
                                @click="cambiarPagina(currentPage - 1)" :disabled="currentPage <= 1">
                                Anterior
                            </button>
                        </li>

                        <li class="page-item" v-for="page in paginasMostradas" :key="page"
                            :class="{ active: page === currentPage }">
                            <button class="page-link shadow-none" :style="page === currentPage
                                ? 'background-color: #F4B324 !important; border-color: #F4B324 !important; color: #1D2A68 !important; font-weight: bold;'
                                : 'color: #1D2A68; border-color: #dee2e6;'" @click="cambiarPagina(page)">
                                {{ page }}
                            </button>
                        </li>

                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link shadow-none" style="color: #1D2A68; border-color: #dee2e6;"
                                @click="cambiarPagina(currentPage + 1)" :disabled="currentPage >= lastPage">
                                Siguiente
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="modal fade" id="horarioModal" tabindex="-1" aria-labelledby="horarioModalLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white"
                        style="background-color: #1D2A68; border-bottom: 4px solid #F4B324;">
                        <h5 class="modal-title fw-bold" id="horarioModalLabel" v-if="cursoSeleccionado?.nivel === '0'">
                            <i class="fas fa-calendar-alt me-2" style="color: #F4B324;"></i> Configurar Horario: {{
                                cursoSeleccionado?.nombre_especialidad }} - "{{ cursoSeleccionado?.paralelo }}"
                        </h5>
                        <h5 class="modal-title fw-bold" id="horarioModalLabel" v-else>
                            <i class="fas fa-calendar-alt me-2" style="color: #F4B324;"></i> Configurar Horario: {{
                                cursoSeleccionado?.nivel }}
                            {{ cursoSeleccionado?.nombre_especialidad }} - "{{ cursoSeleccionado?.paralelo }}"
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close" @click="limpiarModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7 border-end">
                                <h6 class="fw-bold mb-3" style="color: #1D2A68;">
                                    <i class="fas fa-eye" style="color: #F4B324;"></i> Vista Previa del Horario
                                </h6>
                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-bordered table-sm text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="color: #1D2A68;">Día</th>
                                                <th style="color: #1D2A68;">Materia</th>
                                                <th style="color: #1D2A68;">Horario</th>
                                                <th style="color: #1D2A68;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in horarioPreview" :key="index">
                                                <td class="align-middle fw-bold">{{ item.dia_semana }}</td>
                                                <td class="align-middle">
                                                    {{ obtenerNombreAsignatura(item.id_curso_asignatura) }}<br>
                                                    <small class="text-muted">{{
                                                        obtenerNombreDocente(item.id_curso_asignatura) }}</small>
                                                </td>
                                                <td class="align-middle">{{ item.hora_inicio }} - {{ item.hora_fin }}
                                                </td>
                                                <td class="align-middle">
                                                    <button @click="quitarFila(index)"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="horarioPreview.length === 0">
                                                <td colspan="4" class="text-muted py-3">No hay asignaturas en el horario
                                                    aún.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <h6 class="fw-bold mb-3" style="color: #1D2A68;">
                                    <i class="fas fa-plus-circle" style="color: #F4B324;"></i> Asignar Materia
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold" style="color: #1D2A68;">Asignatura</label>
                                    <select class="form-select" v-model="formHorario.id_curso_asignatura"
                                        @change="calcularHorasRestantes">
                                        <option value="" disabled>Seleccione una asignatura...</option>
                                        <option v-for="asig in cursoSeleccionado?.asignaturas_asignadas"
                                            :key="asig.id_curso_asignatura" :value="asig.id_curso_asignatura">
                                            {{ asig.asignatura }} ({{ asig.horas_semanales }} h/sem)
                                        </option>
                                    </select>
                                    <small class="fw-bold mt-1 d-block" style="color: #1D2A68;"
                                        v-if="formHorario.id_curso_asignatura">
                                        Horas restantes por asignar: <span style="color: #F4B324;">{{
                                            horasRestantesFormateadas }}</span>
                                    </small>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label small fw-bold" style="color: #1D2A68;">Día de la
                                            semana</label>
                                        <select class="form-select" v-model="formHorario.dia_semana">
                                            <option value="Lunes">Lunes</option>
                                            <option value="Martes">Martes</option>
                                            <option value="Miércoles">Miércoles</option>
                                            <option value="Jueves">Jueves</option>
                                            <option value="Viernes">Viernes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold" style="color: #1D2A68;">Hora
                                            Inicio</label>
                                        <input type="time" class="form-control" v-model="formHorario.hora_inicio">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold" style="color: #1D2A68;">Hora Fin</label>
                                        <input type="time" class="form-control" v-model="formHorario.hora_fin">
                                    </div>
                                </div>

                                <button class="btn text-white fw-bold w-100 mt-2" @click="agregarAlPreview"
                                    :disabled="!formValido" style="background-color: #1D2A68;">
                                    <i class="fas fa-arrow-left" style="color: #F4B324;"></i> Añadir a la Vista Previa
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="limpiarModal">Cancelar</button>
                        <button type="button" class="btn fw-bold" @click="guardarHorarioGeneral" :disabled="guardando"
                            style="background-color: #F4B324; color: #1D2A68; border: none;">
                            <i class="fas fa-save"></i> {{ guardando ? 'Guardando...' : 'Guardar Horario Completo' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="vistaHorarioModal" tabindex="-1" aria-labelledby="vistaHorarioModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white"
                        style="background-color: #1D2A68; border-bottom: 4px solid #F4B324;">
                        <h5 class="modal-title fw-bold" id="vistaHorarioModalLabel">
                            <i class="fas fa-eye me-2" style="color: #F4B324;"></i> Vista de Horario Académico
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-5 bg-light" id="area-imprimir">
                        <div class="d-flex flex-column flex-md-row align-items-center mb-4 pb-3 border-bottom border-2"
                            style="border-color: #1D2A68 !important;">

                            <div class="mb-3 mb-md-0 text-center text-md-start" style="width: 130px;">
                                <img src="@/assets/img/mile.png" alt="Escudo Malimpia" class="img-fluid"
                                    style="max-height: 110px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                            </div>

                            <div class="text-center flex-grow-1 px-3">
                                <h3 class="fw-bold mb-1" style="font-family: 'Fraunces', serif; color: #1D2A68;">
                                    UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO "MALIMPIA"
                                </h3>
                                <h5 class="fw-bold mb-2" v-if="cursoSeleccionado" style="color: #444;">
                                    HORARIO DE CLASES:
                                    <span v-if="cursoSeleccionado.nivel === '0'" style="color: #1D2A68;">
                                        {{ cursoSeleccionado.nombre_especialidad }} "{{ cursoSeleccionado.paralelo }}"
                                    </span>
                                    <span v-else style="color: #1D2A68;">
                                        {{ cursoSeleccionado.nivel }} {{ cursoSeleccionado.nombre_especialidad }} "{{
                                            cursoSeleccionado.paralelo }}"
                                    </span>
                                </h5>
                                <p class="mb-0 text-muted">
                                    <strong style="color: #1D2A68;">Periodo Lectivo:</strong> {{
                                        cursoSeleccionado?.nombre_periodo }}
                                </p>
                                <p class="mb-0 text-muted">
                                    <strong style="color: #1D2A68;">Malimpia - Quinindé - Esmeraldas</strong>
                                </p>
                            </div>

                            <div class="d-none d-md-block" style="width: 130px;"></div>
                        </div>

                        <div class="table-responsive bg-white shadow-sm p-2 rounded">
                            <table class="table table-bordered text-center align-middle matriz-horario mb-0">
                                <thead class="text-white" style="background-color: #1D2A68;">
                                    <tr>
                                        <th style="width: 15%; border-color: #1D2A68;">Hora</th>
                                        <th style="width: 17%; border-color: #1D2A68;">Lunes</th>
                                        <th style="width: 17%; border-color: #1D2A68;">Martes</th>
                                        <th style="width: 17%; border-color: #1D2A68;">Miércoles</th>
                                        <th style="width: 17%; border-color: #1D2A68;">Jueves</th>
                                        <th style="width: 17%; border-color: #1D2A68;">Viernes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fila, index) in horarioMatriz" :key="index">
                                        <td class="fw-bold bg-light" style="color: #1D2A68; border-color: #dee2e6;">
                                            {{ fila.rango }}
                                        </td>

                                        <td v-if="fila.isRecreo" colspan="5" class="fw-bold fs-5"
                                            style="background-color: #F4B324; color: #1D2A68; letter-spacing: 8px; border-color: #dee2e6;">
                                            RECREO
                                        </td>

                                        <template v-else>
                                            <td v-for="dia in diasSemana" :key="dia"
                                                style="border-color: #dee2e6; height: 80px;">
                                                <div v-if="fila[dia]" class="p-1">
                                                    <span class="d-block fw-bold"
                                                        style="color: #1D2A68; line-height: 1.1;">
                                                        {{ fila[dia].asignatura }}
                                                    </span>
                                                    <small class="text-muted d-block mt-1"
                                                        style="font-size: 0.7rem; font-style: italic;">
                                                        <i class="fas fa-user-circle me-1" style="color: #F4B324;"></i>
                                                        {{ fila[dia].docente }}
                                                    </small>
                                                </div>
                                                <div v-else class="text-muted opacity-25">
                                                    <i class="fas fa-minus small"></i>
                                                </div>
                                            </td>
                                        </template>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-between text-muted small px-2">
                            <span>Generado por: Sistema de Gestión Académica</span>
                            <span>Fecha: {{ new Date().toLocaleDateString() }}</span>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn text-white fw-bold px-4 shadow-sm"
                            style="background-color: #1D2A68;" @click="imprimirHorario">
                            <i class="fas fa-file-pdf me-2" style="color: #F4B324;"></i> Exportar / Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios"
import { mostraralertas2 } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            cargando: false,
            guardando: false,
            currentPage: 1,
            lastPage: 1,

            // Modal state
            modalInstance: null,
            modalVistaInstance: null,
            cursoSeleccionado: null,
            horarioPreview: [],
            horasRestantes: 0,

            formHorario: {
                id_curso_asignatura: '',
                dia_semana: 'Lunes',
                hora_inicio: '',
                hora_fin: ''
            },
            // Datos del Modal Vista Final/PDF
            diasSemana: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
            horarioMatriz: []
        }
    },
    computed: {
        formValido() {
            return this.formHorario.id_curso_asignatura &&
                this.formHorario.dia_semana &&
                this.formHorario.hora_inicio &&
                this.formHorario.hora_fin;
        },
        horasRestantesFormateadas() {
            return Math.max(0, this.horasRestantes).toFixed(2);
        },
        paginasMostradas() {
            let pages = [];
            let start = Math.max(1, this.currentPage - 2);
            let end = Math.min(this.lastPage, start + 4);

            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    watch: {
        busqueda() {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
            }, 500);
        }
    },
    async mounted() {
        // Inicializar instancia de modal Bootstrap
        const modalElement = document.getElementById('horarioModal');
        if (modalElement) {
            this.modalInstance = new bootstrap.Modal(modalElement);
        }

        const modalVistaElement = document.getElementById('vistaHorarioModal');
        if (modalVistaElement) {
            this.modalVistaInstance = new bootstrap.Modal(modalVistaElement);
        }

        await this.getData();
    },
    methods: {
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },
        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/horarios_clases`, {
                    params: { page: this.currentPage, search_query: this.busqueda }
                });
                this.objetoList = response.data?.data || [];
                const pagination = response.data?.pagination || {};
                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
            } catch (error) {
                console.error(error);
            } finally {
                this.cargando = false;
            }
        },
        // ---------- LÓGICA MODAL VISTA FINAL / PDF ----------
        abrirModalVista(curso) {
            this.cursoSeleccionado = curso;
            this.generarMatrizHorario(curso);
            this.modalVistaInstance.show();
        },

        generarMatrizHorario(curso) {
            let slotsUnicos = new Set();
            let clasesExtraidas = [];

            // 1. Extraer todas las clases y horas únicas
            if (curso.tiene_horario && curso.asignaturas_asignadas) {
                curso.asignaturas_asignadas.forEach(asig => {
                    asig.horarios.forEach(hor => {
                        // Formatear horas para quitar los segundos (Ej: 07:00:00 -> 07:00)
                        let h_ini = hor.hora_inicio.substring(0, 5);
                        let h_fin = hor.hora_fin.substring(0, 5);
                        let rango = `${h_ini} - ${h_fin}`;

                        slotsUnicos.add(rango);
                        clasesExtraidas.push({
                            rango: rango,
                            dia_semana: hor.dia_semana,
                            asignatura: asig.asignatura,
                            docente: asig.nombre_docente
                        });
                    });
                });
            }

            // 2. Insertar el bloque de recreo por defecto
            slotsUnicos.add('09:30 - 10:00');

            // 3. Ordenar las horas de menor a mayor
            let slotsOrdenados = Array.from(slotsUnicos).sort((a, b) => {
                let horaA = a.split(' - ')[0];
                let horaB = b.split(' - ')[0];
                return horaA.localeCompare(horaB);
            });

            // 4. Construir la matriz de filas
            this.horarioMatriz = slotsOrdenados.map(rango => {
                let fila = { rango: rango, isRecreo: rango === '09:30 - 10:00' };

                this.diasSemana.forEach(dia => {
                    let clase = clasesExtraidas.find(c => c.rango === rango && c.dia_semana === dia);
                    fila[dia] = clase || null;
                });

                return fila;
            });
        },

        imprimirHorario() {
            // 1. Inicializar documento A4 en formato Horizontal (Landscape)
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            const anchoPagina = doc.internal.pageSize.getWidth();

            // --- 2. LOGO Y CABECERA ---
            // Intentar añadir el logo (Asegúrate que la imagen sea accesible en tu carpeta pública)
            try {
                // x, y, width, height (Ajusta según necesites)
                doc.addImage('mile.png', 'PNG', 15, 10, 25, 25);
            } catch (e) {
                console.warn("No se pudo cargar el logo en el PDF");
            }

            // Título de la Institución (Azul Marino)
            doc.setTextColor(29, 42, 104);
            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.text("UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO \"MALIMPIA\"", anchoPagina / 2 + 10, 18, { align: "center" });

            // Subtítulo del Curso
            doc.setFontSize(11);
            doc.setTextColor(68, 68, 68); // Gris oscuro
            let nombreCurso = this.cursoSeleccionado.nivel === '0'
                ? `${this.cursoSeleccionado.nombre_especialidad} "${this.cursoSeleccionado.paralelo}"`
                : `${this.cursoSeleccionado.nivel} ${this.cursoSeleccionado.nombre_especialidad} "${this.cursoSeleccionado.paralelo}"`;

            doc.text(`HORARIO DE CLASES: ${nombreCurso}`, anchoPagina / 2 + 10, 25, { align: "center" });

            // Periodo y Ubicación
            doc.setFontSize(9);
            doc.setFont("helvetica", "normal");
            doc.setTextColor(100, 100, 100);
            doc.text(`Periodo Lectivo: ${this.cursoSeleccionado?.nombre_periodo}  |  Malimpia - Quinindé - Esmeraldas`, anchoPagina / 2 + 10, 31, { align: "center" });

            // --- 3. PREPARAR DATOS DE LA TABLA ---
            const bodyData = [];

            this.horarioMatriz.forEach(fila => {
                if (fila.isRecreo) {
                    bodyData.push([
                        { content: fila.rango, styles: { fontStyle: 'bold', fillColor: [245, 245, 245], valign: 'middle' } },
                        {
                            content: 'R E C R E O',
                            colSpan: 5,
                            styles: {
                                halign: 'center',
                                valign: 'middle',
                                fontStyle: 'bold',
                                fillColor: [244, 179, 36], // ORO
                                textColor: [29, 42, 104],  // AZUL MARINO
                                fontSize: 10,
                                cellPadding: 3
                            }
                        }
                    ]);
                } else {
                    let row = [
                        { content: fila.rango, styles: { fontStyle: 'bold', fillColor: [245, 245, 245], valign: 'middle', textColor: [29, 42, 104] } }
                    ];

                    this.diasSemana.forEach(dia => {
                        if (fila[dia]) {
                            row.push({
                                content: `${fila[dia].asignatura}\n(${fila[dia].docente})`,
                                styles: {
                                    valign: 'middle',
                                    halign: 'center',
                                    textColor: [29, 42, 104], // AZUL MARINO
                                    fontStyle: 'bold',
                                    fontSize: 7.5
                                }
                            });
                        } else {
                            row.push({
                                content: '-',
                                styles: { valign: 'middle', halign: 'center', textColor: [200, 200, 200] }
                            });
                        }
                    });
                    bodyData.push(row);
                }
            });

            // --- 4. DIBUJAR TABLA ---
            autoTable(doc, {
                startY: 38,
                head: [['Hora', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']],
                body: bodyData,
                theme: 'grid',
                headStyles: {
                    fillColor: [29, 42, 104], // AZUL MARINO
                    textColor: [255, 255, 255],
                    halign: 'center',
                    fontSize: 10,
                    lineColor: [255, 255, 255],
                    lineWidth: 0.1
                },
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineColor: [200, 200, 200],
                    lineWidth: 0.1,
                    overflow: 'linebreak'
                },
                columnStyles: {
                    0: { cellWidth: 30 }, // Columna Hora
                },
                margin: { left: 15, right: 15 }
            });

            // --- 5. FIRMA Y PIE DE PÁGINA ---
            const finalY = doc.lastAutoTable.finalY || 40;
            const altoPagina = doc.internal.pageSize.getHeight();
            let firmaY = finalY + 20;

            if (firmaY > altoPagina - 20) {
                doc.addPage();
                firmaY = 40;
            }

            // Línea de firma (Azul Marino)
            doc.setDrawColor(29, 42, 104);
            doc.setLineWidth(0.5);
            doc.line(anchoPagina / 2 - 35, firmaY, anchoPagina / 2 + 35, firmaY);

            doc.setTextColor(29, 42, 104);
            doc.setFontSize(9);
            doc.setFont("helvetica", "bold");
            doc.text("Firma Autorizada", anchoPagina / 2, firmaY + 5, { align: "center" });

            doc.setFont("helvetica", "normal");
            doc.setFontSize(8);
            doc.text("Rectorado / Coordinación Académica", anchoPagina / 2, firmaY + 10, { align: "center" });

            // Pie de página con fecha
            doc.setTextColor(150, 150, 150);
            doc.text(`Generado por SGA: ${new Date().toLocaleString()}`, 15, altoPagina - 10);

            // --- 6. DESCARGAR ---
            let nombreArchivo = `Horario_${nombreCurso.replace(/[^a-zA-Z0-9 ]/g, "").replace(/\s+/g, "_")}.pdf`;
            doc.save(nombreArchivo);
        },

        // ---------- LÓGICA DEL MODAL ----------

        abrirModal(curso) {
            this.cursoSeleccionado = curso;
            this.horarioPreview = [];

            // Si el curso ya tiene horarios, mapearlos a la vista previa
            if (curso.tiene_horario) {
                curso.asignaturas_asignadas.forEach(asig => {
                    asig.horarios.forEach(hor => {
                        this.horarioPreview.push({
                            id_curso_asignatura: asig.id_curso_asignatura,
                            dia_semana: hor.dia_semana,
                            hora_inicio: hor.hora_inicio,
                            hora_fin: hor.hora_fin
                        });
                    });
                });
            }
            this.modalInstance.show();
        },

        limpiarModal() {
            this.cursoSeleccionado = null;
            this.horarioPreview = [];
            this.formHorario = { id_curso_asignatura: '', dia_semana: 'Lunes', hora_inicio: '', hora_fin: '' };
            this.horasRestantes = 0;
        },

        obtenerNombreAsignatura(id_ca) {
            const asig = this.cursoSeleccionado?.asignaturas_asignadas.find(a => a.id_curso_asignatura === id_ca);
            return asig ? asig.asignatura : 'Desconocido';
        },

        obtenerNombreDocente(id_ca) {
            const asig = this.cursoSeleccionado?.asignaturas_asignadas.find(a => a.id_curso_asignatura === id_ca);
            return asig ? asig.nombre_docente : '';
        },

        calcularDiferenciaHoras(inicio, fin) {
            // Convierte formato "HH:MM" a horas decimales para restar (Ej. 09:30 - 08:00 = 1.5 horas)
            const [hInicio, mInicio] = inicio.split(':').map(Number);
            const [hFin, mFin] = fin.split(':').map(Number);
            const totalMinutosInicio = hInicio * 60 + mInicio;
            const totalMinutosFin = hFin * 60 + mFin;
            return (totalMinutosFin - totalMinutosInicio) / 60;
        },

        calcularHorasRestantes() {
            if (!this.formHorario.id_curso_asignatura) return;

            const asignatura = this.cursoSeleccionado.asignaturas_asignadas.find(
                a => a.id_curso_asignatura === this.formHorario.id_curso_asignatura
            );

            // Sumamos las horas de esta asignatura que ya están en el preview
            let horasOcupadas = 0;
            this.horarioPreview.forEach(h => {
                if (h.id_curso_asignatura === this.formHorario.id_curso_asignatura) {
                    horasOcupadas += this.calcularDiferenciaHoras(h.hora_inicio, h.hora_fin);
                }
            });

            this.horasRestantes = asignatura.horas_semanales - horasOcupadas;
        },

        agregarAlPreview() {
            // Validar que la hora de inicio sea menor a la de fin
            const horasAAsignar = this.calcularDiferenciaHoras(this.formHorario.hora_inicio, this.formHorario.hora_fin);
            if (horasAAsignar <= 0) {
                mostraralertas2('La hora de fin debe ser mayor a la hora de inicio.', 'error');
                return;
            }

            // Validar horas semanales excedidas
            if (horasAAsignar > this.horasRestantes) {
                mostraralertas2('No puedes asignar más horas de las permitidas para esta asignatura.', 'warning');
                return;
            }

            // Añadir al arreglo de previsualización
            this.horarioPreview.push({ ...this.formHorario });

            // Recalcular y limpiar el formulario base
            this.calcularHorasRestantes();
            this.formHorario.hora_inicio = '';
            this.formHorario.hora_fin = '';
        },

        quitarFila(index) {
            this.horarioPreview.splice(index, 1);
            this.calcularHorasRestantes();
        },

        async guardarHorarioGeneral() {
            if (this.horarioPreview.length === 0) {
                mostraralertas2('Agregue al menos una materia al horario.', 'warning');
                return;
            }

            this.guardando = true;
            try {
                // Hacemos el POST al endpoint nuevo que creamos en Laravel
                const response = await API.post(`${this.baseUrl}/crearhorario`, {
                    id_curso: this.cursoSeleccionado.id_curso,
                    horarios: this.horarioPreview
                });

                mostraralertas2('Horario guardado correctamente.', 'success');
                this.modalInstance.hide();
                this.getData(); // Refrescar tabla principal
                this.limpiarModal();
            } catch (error) {
                // Si el backend lanza error 422 (Cruce de docente)
                if (error.response && error.response.status === 422) {
                    mostraralertas2(error.response.data.error, 'error');
                } else {
                    mostraralertas2('Ocurrió un error al guardar el horario.', 'error');
                }
            } finally {
                this.guardando = false;
            }
        }
    }
}
</script>
<style scoped>
/* Contenedor principal del header con un borde lateral sutil */
.custom-header {
    border-left: 5px solid #198754;
    /* Cambia al color de tu var(--green-800) si lo prefieres */
    transition: all 0.3s ease;
}

.custom-header:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
}

/* Animación del ícono principal cuando pasas el mouse por el header */
.header-icon {
    width: 55px;
    height: 55px;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.custom-header:hover .header-icon {
    transform: rotate(-10deg) scale(1.1);
}

/* Efecto hover interactivo para el contador (Badge) */
.stat-badge {
    transition: all 0.3s ease;
    cursor: default;
}

.stat-badge:hover {
    transform: translateY(-2px);
    background-color: #198754 !important;
    /* Verde success de Bootstrap */
    color: white !important;
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

/* Efecto hover para el botón principal */
.interactive-btn {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    /* Opcional: puedes ponerle un gradiente en lugar de un color plano */
    /* background: linear-gradient(135deg, #198754, #20c997); */
    /* border: none; */
}

.interactive-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 12px rgba(25, 135, 84, 0.25) !important;
}

.interactive-btn:active {
    transform: translateY(1px);
}
</style>
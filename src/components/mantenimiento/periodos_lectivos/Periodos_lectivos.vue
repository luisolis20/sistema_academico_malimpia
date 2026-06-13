<template>
    <div class="container-fluid py-4">
        <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

            <!-- FILA SUPERIOR: Títulos, Estadísticas y Botón de Acción -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">

                <!-- Título e Icono Principal -->
                <div class="mb-3 mb-md-0 d-flex align-items-center">
                    <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                        style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                        <i class="fas fa-calendar-alt fs-4" style="color: #F4B324;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                            Gestión de Periodos Lectivos
                        </h2>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                            Administración y control de los periodos lectivos
                        </p>
                    </div>
                </div>

                <!-- Componentes de la Derecha (Badge y Botón) -->
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border shadow-sm"
                        style="background-color: rgba(244, 179, 36, 0.1); border-color: #F4B324 !important; color: #1D2A68;">
                        <i class="fas fa-book me-2" style="color: #F4B324;"></i>
                        <span class="fw-bold">
                            Total: <span v-if="totaldata > 0">{{ totaldata }}</span><span v-else>0</span>
                        </span>
                    </div>

                    <button v-if="!hayPeriodoAbiertoYActivo"
                        class="btn btn-lg shadow-sm rounded-pill d-flex align-items-center interactive-btn px-4 border-0"
                        data-bs-toggle="modal" data-bs-target="#modalUsuario" @click="limpiar"
                        style="background-color: #1D2A68; color: white;">
                        <i class="fas fa-plus-circle me-2" style="color: #F4B324;"></i>
                        <span class="fw-bold fs-6">Nuevo Registro</span>
                    </button>
                </div>
            </div>

            <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
            <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
                style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
                <i class="fas fa-history fs-5 mt-1" style="color: #F4B324;"></i>
                <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
                    <strong>Planificación Estructural del Cronograma:</strong> Los periodos lectivos constituyen la base
                    temporal y el eje central de toda la arquitectura del sistema. Cada ciclo determina de forma directa
                    el alcance y disponibilidad de los módulos de matriculación, la oferta académica de asignaturas y el
                    registro de calificaciones. Una gestión precisa de estos lapsos asegura la trazabilidad cronológica
                    de la información institucional y la inmutabilidad de los históricos estudiantiles.
                </p>
            </div>

        </header>

        <div v-if="hayPeriodoAbiertoYActivo" class="alert border-0 shadow-sm d-flex align-items-center mb-4 rounded-3"
            style="background-color: rgba(244, 179, 36, 0.15); border-left: 4px solid #F4B324 !important; color: #1D2A68;"
            role="alert">
            <i class="fas fa-exclamation-triangle fs-4 me-3" style="color: #F4B324;"></i>
            <div>
                <strong>¡Atención!</strong> Ya existe un periodo lectivo <b>Activo</b> y con <b>Matrículas Abiertas</b>.
                Debe inhabilitarlo o cerrar sus matrículas antes de poder habilitar uno nuevo.
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre del periodo lectivo...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de un periodo lectivo para buscar en la
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
                                Id</th>
                            <th class="py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Periodo Lectivo</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha
                                Inicio</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha Fin
                            </th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Matrículas Abiertas</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Estado
                                del Periodo</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_periodo">
                            <td class="ps-4 fw-bold text-secondary">{{ user.id_periodo }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold" style="color: #1D2A68;">{{ user.nombre }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1 shadow-sm">
                                    <i class="far fa-calendar-alt me-1" style="color: #F4B324;"></i> {{
                                        user.fecha_inicio }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1 shadow-sm">
                                    <i class="far fa-calendar-check me-1" style="color: #F4B324;"></i> {{ user.fecha_fin
                                    }}
                                </span>
                            </td>

                            <td class="text-center" v-if="user.matriculas_abiertas == 1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success px-3 rounded-pill shadow-sm">Sí</span>
                            </td>
                            <td class="text-center" v-else>
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger px-3 rounded-pill shadow-sm">No</span>
                            </td>

                            <td class="text-center" v-if="user.estado_activo == 1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success px-3 rounded-pill shadow-sm">Activo</span>
                            </td>
                            <td class="text-center" v-else>
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger px-3 rounded-pill shadow-sm">Inactivo</span>
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light border shadow-sm" style="color: #1D2A68;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditUsuario"
                                        @click="cargarDatosEdicion(user)" title="Editar detalles">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger border shadow-sm ms-1"
                                        @click="eliminar(user.id_periodo, user.nombre)" v-if="user.estado_activo == 1"
                                        title="Inhabilitar este periodo">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success border shadow-sm ms-1"
                                        @click="habilitar(user.id_periodo, user.nombre)" v-else
                                        :disabled="hayPeriodoAbiertoYActivo"
                                        :title="hayPeriodoAbiertoYActivo ? 'Bloqueado: Ya existe un periodo activo con matrículas abiertas' : 'Habilitar este periodo'">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 mb-3 d-block"
                                    style="color: #1D2A68; opacity: 0.3;"></i>
                                No se encontraron periodos lectivos. ¡Haz clic en "Nuevo Registro" para empezar!
                            </td>
                        </tr>

                        <tr v-if="cargando">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #F4B324;"></i>
                                Cargando información...
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

        <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-plus-circle me-2" style="color: #F4B324;"></i>Registrar nuevo Periodo
                            Lectivo
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalCrear"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(29, 42, 104, 0.05); border-left: 4px solid #F4B324 !important;"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small text-dark">
                                <strong style="color: #1D2A68;">¿Qué hacer aquí?</strong><br>
                                Registra un nuevo periodo lectivo (ej. <em>"2023-2024"</em>).
                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="col-md-12">
                                <div class="row g-3">
                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input v-model="objetoData.nombre" type="text" class="form-control"
                                                :class="{ 'is-invalid': errorsData.nombre }" id="crearNombre"
                                                placeholder="Nombre del Periodo Lectivo"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                            <label for="crearNombre">Nombre del Periodo Lectivo</label>
                                            <div class="invalid-feedback">Por favor, ingrese el nombre del periodo
                                                lectivo.</div>
                                        </div>
                                        <div class="form-text text-muted small ms-1">Ej: 2023-2024, 2024-2025</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input v-model="objetoData.fecha_inicio" type="date" class="form-control"
                                                :class="{ 'is-invalid': errorsData.fecha_inicio }" id="crearFecha"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                            <label for="crearFecha">Fecha de Inicio</label>
                                            <div class="invalid-feedback">Seleccione una fecha.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input v-model="objetoData.fecha_fin" type="date" class="form-control"
                                                :class="{ 'is-invalid': errorsData.fecha_fin }" id="crearFechaFin"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                            <label for="crearFechaFin">Fecha de Fin</label>
                                            <div class="invalid-feedback">Seleccione una fecha.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <select v-model="objetoData.matriculas_abiertas" class="form-select"
                                                :class="{ 'is-invalid': errorsData.matriculas_abiertas }"
                                                id="crearMatriculas" style="border-color: rgba(29, 42, 104, 0.2);">
                                                <option value="" disabled selected>Seleccione</option>
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                            <label for="crearMatriculas">Matrículas Abiertas</label>
                                            <div class="invalid-feedback">Seleccione una opción.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <button type="submit" class="btn w-100 py-2 shadow-sm rounded-3 fw-bold border-0"
                                style="background-color: #1D2A68; color: white;">
                                <i class="fas fa-save me-2" style="color: #F4B324;"></i>Crear Periodo Lectivo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-edit me-2" style="color: #F4B324;"></i>Editar Periodo Lectivo
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalEditar"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <form @submit.prevent="editarData">
                            <div class="col-md-12">
                                <div class="row g-3">
                                    <div class="form-floating mb-3">
                                        <input v-model="objetoEdit.nombre" type="text" class="form-control"
                                            :class="{ 'is-invalid': errorsEdit.nombre }" id="editNombre"
                                            placeholder="Nombre del Periodo Lectivo"
                                            style="border-color: rgba(29, 42, 104, 0.2);">
                                        <label for="editNombre">Nombre del Periodo Lectivo</label>
                                        <div class="invalid-feedback">Por favor, ingrese el nombre del periodo lectivo.
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input v-model="objetoEdit.fecha_inicio" type="date" class="form-control"
                                                :class="{ 'is-invalid': errorsEdit.fecha_inicio }" id="editFecha"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                            <label for="editFecha">Fecha de Inicio</label>
                                            <div class="invalid-feedback">Seleccione una fecha.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input v-model="objetoEdit.fecha_fin" type="date" class="form-control"
                                                :class="{ 'is-invalid': errorsEdit.fecha_fin }" id="editFechaFin"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                            <label for="editFechaFin">Fecha de Fin</label>
                                            <div class="invalid-feedback">Seleccione una fecha.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select v-model="objetoEdit.matriculas_abiertas" class="form-select"
                                                :class="{ 'is-invalid': errorsEdit.matriculas_abiertas }"
                                                id="editMatriculas" style="border-color: rgba(29, 42, 104, 0.2);">
                                                <option value="" disabled selected>Seleccione</option>
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                            <label for="editMatriculas">Matrículas Abiertas</label>
                                            <div class="invalid-feedback">Seleccione una opción.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select v-model="objetoEdit.estado_activo" class="form-select"
                                                :class="{ 'is-invalid': errorsEdit.estado_activo }" id="editEstado"
                                                style="border-color: rgba(29, 42, 104, 0.2);">
                                                <option value="" disabled selected>Seleccione un estado</option>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                            <label for="editEstado">Estado actual</label>
                                            <div class="invalid-feedback">Seleccione un estado válido.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <button type="submit" class="btn w-100 py-2 shadow-sm rounded-3 fw-bold border-0"
                                style="background-color: #1D2A68; color: white;">
                                <i class="fas fa-sync-alt me-2" style="color: #F4B324;"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
import API from "@/assets/js/axios"
import { confimar, confimarhabi, mostraralertas2 } from "@/assets/js/funciones/functions";

export default {
    data() {
        return {
            baseUrl: "/sistma",
            objetoData: {
                nombre: "",
                fecha_inicio: "",
                fecha_fin: "",
                matriculas_abiertas: "",
                estado_activo: 0,
            },
            errorsData: {
                nombre: false,
                fecha_inicio: false,
                fecha_fin: false,
                matriculas_abiertas: false,
            },
            objetoEdit: {
                id_periodo: 0,
                nombre: "",
                fecha_inicio: "",
                fecha_fin: "",
                matriculas_abiertas: "",
                estado_activo: "",
            },
            errorsEdit: {
                nombre: false,
                fecha_inicio: false,
                fecha_fin: false,
                matriculas_abiertas: false,
                estado_activo: false
            },
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            totaldata: 0,
        }
    },
    computed: {
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
        },
        // NUEVA COMPUTED: Verifica si ya hay en la tabla un periodo que cumple ambas condiciones
        hayPeriodoAbiertoYActivo() {
            return this.objetoList.some(p => p.matriculas_abiertas == 1 && p.estado_activo == 1);
        }
    },
    watch: {
        busqueda(newVal) {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
            }, 500);
        },
    },
    async mounted() {
        await this.getData();
    },
    methods: {

        cargarDatosEdicion(user) {
            this.errorsEdit = { nombre: false, fecha_inicio: false, fecha_fin: false, matriculas_abiertas: false, estado_activo: false };
            this.objetoEdit = {
                id_periodo: user.id_periodo,
                nombre: user.nombre,
                fecha_inicio: user.fecha_inicio,
                fecha_fin: user.fecha_fin,
                matriculas_abiertas: user.matriculas_abiertas,
                estado_activo: user.estado_activo,
            };
        },

        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },

        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/periodos_lectivos`, {
                    params: { page: this.currentPage, search_query: this.busqueda }
                });

                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.totaldata = pagination.total || 0;
                this.objetoList = data;

            } catch (error) {
                console.warn("⚠️ Error al obtener datos:", error?.response?.data || error);
                this.objetoList = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.cargando = false;
            }
        },

        validarFormularioCrear() {
            this.errorsData.nombre = this.objetoData.nombre === "";
            this.errorsData.fecha_inicio = this.objetoData.fecha_inicio === "";
            this.errorsData.fecha_fin = this.objetoData.fecha_fin === "";
            this.errorsData.matriculas_abiertas = this.objetoData.matriculas_abiertas === "";
            return !Object.values(this.errorsData).some(val => val === true);
        },

        async guardarData() {
            if (!this.validarFormularioCrear()) {
                return;
            }

            // REGLA DE NEGOCIO: Bloquear si intenta crear matrículas abiertas y ya hay uno activo
            if (this.objetoData.matriculas_abiertas == 1 && this.hayPeriodoAbiertoYActivo) {
                mostraralertas2("No puede crear un periodo con matrículas abiertas porque ya existe uno. Ciérrelo primero.", "warning");
                return;
            }

            try {
                const response = await API.post(`${this.baseUrl}/periodos_lectivos`, this.objetoData);
                if (response) {
                    mostraralertas2("Periodo lectivo creado exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalCrear').click();
                } else {
                    mostraralertas2("Periodo lectivo creado, pero se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                console.error("❌ Error al crear periodo lectivo:", error?.response?.data || error);
                mostraralertas2("Error al crear periodo lectivo. Por favor, inténtelo de nuevo.", "error");
            }
        },

        validarFormularioEditar() {
            this.errorsEdit.nombre = this.objetoEdit.nombre.toString().trim() === "";
            this.errorsEdit.fecha_inicio = this.objetoEdit.fecha_inicio === "";
            this.errorsEdit.fecha_fin = this.objetoEdit.fecha_fin === "";
            this.errorsEdit.matriculas_abiertas = this.objetoEdit.matriculas_abiertas === "";
            this.errorsEdit.estado_activo = this.objetoEdit.estado_activo === "";
            return !Object.values(this.errorsEdit).some(val => val === true);
        },

        async editarData() {
            if (!this.validarFormularioEditar()) {
                return;
            }

            // REGLA DE NEGOCIO: Evitar que el usuario guarde el edit si quiere poner 1 y 1
            // y ya existe OTRO periodo que ocupa ese lugar.
            if (this.objetoEdit.estado_activo == 1 && this.objetoEdit.matriculas_abiertas == 1) {
                const existeOtro = this.objetoList.some(p =>
                    p.id_periodo !== this.objetoEdit.id_periodo &&
                    p.estado_activo == 1 &&
                    p.matriculas_abiertas == 1
                );

                if (existeOtro) {
                    mostraralertas2("Ya existe otro periodo activo con matrículas abiertas. Debe cerrarlo primero.", "warning");
                    return; // Detenemos la petición
                }
            }

            try {
                const params = {
                    nombre: this.objetoEdit.nombre,
                    fecha_inicio: this.objetoEdit.fecha_inicio,
                    fecha_fin: this.objetoEdit.fecha_fin,
                    matriculas_abiertas: this.objetoEdit.matriculas_abiertas,
                    estado_activo: this.objetoEdit.estado_activo,
                    id_periodo: this.objetoEdit.id_periodo,
                };

                const response = await API.put(`${this.baseUrl}/periodos_lectivos/${this.objetoEdit.id_periodo}`, params);
                if (response) {
                    mostraralertas2("Periodo lectivo actualizado exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Periodo lectivo actualizado, pero se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                console.error("❌ Error al actualizar periodo lectivo:", error?.response?.data || error);
                mostraralertas2("Error al actualizar periodo lectivo. Por favor, inténtelo de nuevo.", "error");
            }
        },

        limpiar() {
            this.objetoEdit = { id_periodo: 0, nombre: "", fecha_inicio: "", fecha_fin: "", matriculas_abiertas: "", estado_activo: "" };
            this.objetoData = { nombre: "", fecha_inicio: "", fecha_fin: "", matriculas_abiertas: "", estado_activo: 0 };
            this.errorsData = { nombre: false, fecha_inicio: false, fecha_fin: false, matriculas_abiertas: false, estado_activo: false };
            this.errorsEdit = { nombre: false, fecha_inicio: false, fecha_fin: false, matriculas_abiertas: false, estado_activo: false };
        },

        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_periodo_lectivo/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar el periodo lectivo ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar el periodo lectivo:", error);
            }
        },

        async habilitar(id, nombre) {
            // REGLA DE NEGOCIO: Validar también programáticamente por si acaso
            if (this.hayPeriodoAbiertoYActivo) {
                mostraralertas2("No puede habilitar. Cierre primero el periodo actual activo con matrículas abiertas.", "warning");
                return;
            }

            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_periodo_lectivo/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar el periodo lectivo ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar el periodo lectivo:", error);
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
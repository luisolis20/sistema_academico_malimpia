<template>
    <div class="container-fluid py-4">
        <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

            <!-- FILA SUPERIOR: Título e Icono Principal -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">
                <div class="mb-0 d-flex align-items-center">
                    <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                        style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                        <i class="fas fa-user-edit fs-4" style="color: #F4B324;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                            Gestión Global de Asignaturas en cada Curso
                        </h2>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                            Administración de asignaturas, aquí se asignarán materias de cada curso a los docentes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
            <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
                style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
                <i class="fas fa-network-wired fs-5 mt-1" style="color: #F4B324;"></i>
                <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
                    <strong>Articulación del distributivo y Carga Académica:</strong> Este módulo actúa como el puente
                    integrador definitivo del sistema, donde converge el catálogo maestro de asignaturas con la
                    estructura de cursos vigentes. Desde aquí se parametriza la malla curricular específica de cada aula
                    y se delega la responsabilidad evaluativa a los docentes correspondientes. Un correcto mapeo en este
                    panel es crucial, ya que provee al backend las relaciones necesarias para habilitar los perfiles de
                    ingreso de calificaciones de los profesores y estructurar los horarios de clases de la institución.
                </p>
            </div>

        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" v-model="busqueda" @input="busqueda = busqueda.replace(/[^0-9]/g, '')"
                                class="form-control border-0 shadow-none"
                                placeholder="Buscar por cédula de la persona... (Solo números)">
                        </div>
                    </div>

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
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                ¿Tiene Asignaturas?</th>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                ¿Tiene Asignaturas?</th>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                ¿Es docente tutor?</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Periodo</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.personID">

                            <td class="ps-4 fw-bold text-secondary" v-if="user.tiene_asignaturas">{{ user.personID }}
                            </td>
                            <td class="ps-4 text-muted small" v-else>Sin asignar Materias</td>

                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0; color: #1D2A68; border: 1px solid rgba(244, 179, 36, 0.5);">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold" style="color: #1D2A68;">{{ user.nombres }} {{
                                            user.apellidos }}</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                        <span title="Rol">
                                            Rol: {{ user.nombre_rol }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span v-if="user.tiene_asignaturas" class="badge"
                                    style="background-color: #1D2A68; color: white;">Sí</span>
                                <span v-else class="badge bg-danger">No</span>
                            </td>

                            <td>
                                <span v-if="user.es_tutor_general === 1" class="badge fw-bold"
                                    style="background-color: #F4B324; color: #1D2A68;">Sí</span>
                                <span v-else class="text-muted small">No</span>
                            </td>

                            <td class="text-center">
                                <span v-if="user.tiene_asignaturas && user.nombre_periodo"
                                    class="badge bg-light text-dark border">{{ user.nombre_periodo }}</span>
                                <span v-else
                                    class="badge bg-danger-subtle text-danger border border-danger px-3">-</span>
                            </td>

                            <td class="text-center">
                                <button v-if="!user.tiene_asignaturas" @click="abrirModalAsignacion(user, 'crear')"
                                    class="btn btn-sm text-white interactive-btn"
                                    style="background-color: #1D2A68; border: none;">
                                    <i class="fas fa-plus" style="color: #F4B324;"></i> Asignar
                                </button>
                                <button v-else @click="abrirModalAsignacion(user, 'editar')"
                                    class="btn btn-sm interactive-btn fw-bold"
                                    style="background-color: #F4B324; color: #1D2A68; border: none;">
                                    <i class="fas fa-eye"></i> Ver/Editar
                                </button>
                            </td>

                        </tr>

                        <tr v-if="cargando">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #1D2A68;"></i>
                                Cargando...
                            </td>
                        </tr>
                        <tr v-if="!cargando && objetoList.length === 0">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-search fs-2 mb-2 d-block" style="color: #F4B324;"></i>
                                No se encontraron registros en el sistema.
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
        <div class="modal fade" id="modalAsignacion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white"
                        style="background-color: #1D2A68; border-bottom: 3px solid #F4B324;">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-book-open me-2" style="color: #F4B324;"></i>
                            {{ modoModal === 'crear' ? 'Asignar Materias' : 'Gestionar Materias Asignadas' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            id="btnCloseModalAsignacion" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body text-center">
                                        <img :src="personaSeleccionada.foto" @error="handleImageError"
                                            class="rounded-circle mb-3 border border-3"
                                            style="width: 100px; height: 100px; object-fit: cover; border-color: #F4B324 !important;">
                                        <h5 class="fw-bold mb-0" style="color: #1D2A68;">{{ personaSeleccionada.nombres
                                        }} {{ personaSeleccionada.apellidos }}</h5>
                                        <p class="text-muted small mb-0"><i class="far fa-id-card me-1"></i>{{
                                            personaSeleccionada.cedula }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-bold border-bottom pb-2 mb-3"
                                            style="color: #1D2A68; border-color: rgba(244, 179, 36, 0.5) !important;">
                                            Cursos a Asignar
                                        </h6>

                                        <div v-if="personaSeleccionada.es_tutor">
                                            <div class="alert py-2 small mb-3"
                                                style="background-color: rgba(29, 42, 104, 0.05); color: #1D2A68; border-left: 4px solid #F4B324;">
                                                <strong>Docente Tutor de:</strong><br>
                                                {{ personaSeleccionada.tutor_nivel }} - {{
                                                    personaSeleccionada.tutor_especialidad }} "{{
                                                    personaSeleccionada.tutor_paralelo }}"
                                            </div>
                                            <div v-if="esTutorRestringido" class="text-danger small mb-2">
                                                <i class="fas fa-exclamation-triangle"></i> Por su nivel/especialidad,
                                                solo puede dar clases en su curso tutor.
                                            </div>
                                        </div>

                                        <div class="form-group"
                                            v-if="!personaSeleccionada.es_tutor || !esTutorRestringido">
                                            <label class="small text-muted mb-1">Seleccionar Cursos Adicionales:</label>
                                            <div class="border rounded p-2"
                                                style="max-height: 200px; overflow-y: auto;">
                                                <div class="form-check" v-for="curso in objetoCursoList"
                                                    :key="curso.id_curso">
                                                    <input class="form-check-input" type="checkbox"
                                                        :value="curso.id_curso" v-model="cursosSeleccionados"
                                                        @change="actualizarTablaSeleccion">
                                                    <label class="form-check-label small">
                                                        {{ curso.nombre_nivel }} {{ curso.nombre_especialidad }} "{{
                                                            curso.paralelo }}"
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3"
                                            style="border-color: rgba(244, 179, 36, 0.5) !important;">
                                            <h6 class="fw-bold mb-0" style="color: #1D2A68;">Seleccionar Asignaturas
                                            </h6>
                                            <input type="text" class="form-control form-control-sm w-50"
                                                placeholder="Buscar asignatura..." v-model="busquedaAsignatura">
                                        </div>

                                        <div v-if="cursosSeleccionados.length === 0 && (!personaSeleccionada.es_tutor || !cursoTutorSeleccionado)"
                                            class="text-center text-muted py-3">
                                            <i class="fas fa-arrow-left me-2"></i> Seleccione al menos un curso a la
                                            izquierda.
                                        </div>

                                        <div v-else>
                                            <div class="table-responsive" style="max-height: 250px;">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Cód</th>
                                                            <th>Asignatura</th>
                                                            <th class="text-center">Seleccionar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="asig in asignaturasFiltradas"
                                                            :key="asig.id_asignatura">
                                                            <td class="text-muted small">{{ asig.id_asignatura }}</td>
                                                            <td>{{ asig.nombre }}</td>
                                                            <td class="text-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    :value="asig" v-model="asignaturasTemporales">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-end mt-2">
                                                <button class="btn btn-sm text-white" style="background-color: #1D2A68;"
                                                    @click="agregarAlResumen"
                                                    :disabled="asignaturasTemporales.length === 0">
                                                    Añadir a la lista <i class="fas fa-arrow-down"
                                                        style="color: #F4B324;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm" style="background-color: rgba(29, 42, 104, 0.05);">
                                    <div class="card-body">
                                        <h6 class="fw-bold border-bottom pb-2 mb-3"
                                            style="color: #1D2A68; border-color: #F4B324 !important;">
                                            Resumen de Asignación
                                        </h6>
                                        <div class="table-responsive" style="max-height: 200px;">
                                            <table class="table table-sm table-bordered bg-white mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="color: #1D2A68;">Curso</th>
                                                        <th style="color: #1D2A68;">Asignaturas</th>
                                                        <th class="text-center" style="color: #1D2A68;">Quitar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, indexCurso) in asignacionesResumen"
                                                        :key="indexCurso">
                                                        <td class="small fw-bold align-middle">{{ item.curso_nombre }}
                                                        </td>
                                                        <td class="small p-2">
                                                            <div v-for="(asig, indexAsig) in item.asignaturas"
                                                                :key="asig.id_asignatura"
                                                                class="d-flex align-items-center mb-2 bg-light border rounded p-1">
                                                                <span class="flex-grow-1 fw-semibold ps-2 text-dark">{{
                                                                    asig.nombre }}</span>

                                                                <div class="input-group input-group-sm ms-2"
                                                                    style="width: 110px;">
                                                                    <span
                                                                        class="input-group-text bg-white border-end-0">Hrs</span>
                                                                    <input type="number"
                                                                        class="form-control text-center"
                                                                        v-model="asig.horas_semanales" min="1"
                                                                        placeholder="0">
                                                                </div>

                                                                <button class="btn btn-sm btn-link text-danger ms-1"
                                                                    @click="quitarAsignaturaEspecifica(indexCurso, indexAsig)">
                                                                    <i class="fas fa-times-circle"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <button class="btn btn-sm btn-outline-danger py-1 px-2"
                                                                @click="removerCursoDelResumen(indexCurso)"
                                                                title="Quitar todo el curso">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="asignacionesResumen.length === 0">
                                                        <td colspan="3" class="text-center text-muted small py-3">Aún no
                                                            hay asignaturas configuradas.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn text-white fw-bold" style="background-color: #1D2A68;"
                            @click="guardarAsignaciones" :disabled="asignacionesResumen.length === 0">
                            <i class="fas fa-save me-1" style="color: #F4B324;"></i> Guardar Asignaciones
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import API from "@/assets/js/axios";
import { confimar, confimarhabi, mostraralertas2, confimardesasignar } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap';

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            objetoCursoList: [],
            objetoEspecialidadList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            refreshKey: Date.now(),

            // Variables para Modal individual
            personaSeleccionada: {},
            totaldata: 0,
            asignaturasList: [],
            busquedaAsignatura: '',
            modoModal: 'crear',

            // Modal State
            cursosSeleccionados: [], // IDs de cursos checkeados a la izquierda
            asignaturasTemporales: [], // Asignaturas seleccionadas en la tabla derecha antes de añadirlas
            asignacionesResumen: [], // Array final: [{ curso_id, curso_nombre, asignaturas: [{id, nombre}] }]
            cursoTutorSeleccionado: true // Para forzar que el curso tutor siempre esté disponible si aplica

        }
    },
    computed: {
        asignaturasFiltradas() {
            if (!this.busquedaAsignatura) return this.asignaturasList;
            return this.asignaturasList.filter(a => a.nombre.toLowerCase().includes(this.busquedaAsignatura.toLowerCase()));
        },
        esTutorRestringido() {
            if (!this.personaSeleccionada.es_tutor) return false;

            const especialidadesRestringidas = ['Básica', 'Inicial'];
            const nivelesRestringidos = ['0', '1ro', '2do', '3ro', '4to', '5to', '6to', '7mo'];

            const especialidad = this.personaSeleccionada.tutor_especialidad;
            const nivel = this.personaSeleccionada.tutor_nivel;

            return especialidadesRestringidas.includes(especialidad) && nivelesRestringidos.includes(nivel);
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
        busqueda(newVal) {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
            }, 500);
        }
    },
    async mounted() {
        await this.getData();
    },
    methods: {
        async GetAsignaturas() {
            try {
                const response = await API.get(`${this.baseUrl}/asignaturas_activos`);
                this.asignaturasList = response.data?.data || [];
            } catch (error) {
                console.error("Error al obtener asignaturas:", error);
            }
        },
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },
        async abrirModalAsignacion(user, modo) {
            this.modoModal = modo;

            // Datos vitales que el endpoint principal de Docentes debe retornar
            this.personaSeleccionada = {
                id_docente: user.personID,
                nombres: user.nombres,
                apellidos: user.apellidos,
                cedula: user.cedula,
                foto: this.getPhotoUrl(user.personID),
                es_tutor: user.es_tutor_general,
                // Asumiendo que el backend retorna esta info si es tutor:
                tutor_curso_id: user.tutor_curso_id,
                tutor_nivel: user.tutor_nivel,
                tutor_especialidad: user.tutor_especialidad,
                tutor_paralelo: user.tutor_paralelo
            };
            this.limpiarModalAsignacion();
            await this.GetCursos();
            await this.GetAsignaturas();

            // Si el docente es tutor, pre-seleccionamos su curso base internamente
            if (this.personaSeleccionada.es_tutor && this.personaSeleccionada.tutor_curso_id) {
                this.cursosSeleccionados.push(this.personaSeleccionada.tutor_curso_id);
            }

            // Si es modo editar, cargamos las materias que ya tiene
            if (modo === 'editar') {
                await this.cargarAsignacionesActuales(user.personID);
            }

            const modal = new bootstrap.Modal(document.getElementById('modalAsignacion'));
            modal.show();
        },
        quitarAsignaturaEspecifica(indexCurso, indexAsig) {
            this.asignacionesResumen[indexCurso].asignaturas.splice(indexAsig, 1);

            // Si el curso se queda sin materias asignadas, quitamos todo el curso por limpieza
            if (this.asignacionesResumen[indexCurso].asignaturas.length === 0) {
                this.removerCursoDelResumen(indexCurso);
            }
        },
        removerCursoDelResumen(indexCurso) {
            const idCursoRemovido = this.asignacionesResumen[indexCurso].curso_id;

            // Quitamos el curso del arreglo del resumen
            this.asignacionesResumen.splice(indexCurso, 1);

            // Lo desmarcamos dinámicamente de los checkboxes de la izquierda
            this.cursosSeleccionados = this.cursosSeleccionados.filter(id => id !== idCursoRemovido);
        },
        agregarAlResumen() {
            if (this.cursosSeleccionados.length === 0 || this.asignaturasTemporales.length === 0) return;

            // Recorremos cada ID de curso que esté seleccionado (los 3 cursos del ejemplo)
            this.cursosSeleccionados.forEach(idCurso => {

                // Buscamos el nombre del curso en la lista
                let cursoObj = this.objetoCursoList.find(c => c.id_curso === idCurso);
                let nombreCurso = "";

                if (cursoObj) {
                    nombreCurso = `${cursoObj.nombre_nivel} ${cursoObj.nombre_especialidad} "${cursoObj.paralelo}"`;
                } else if (this.personaSeleccionada.es_tutor && idCurso === this.personaSeleccionada.tutor_curso_id) {
                    // Caso especial por si el ID corresponde al curso del tutor y no está en la lista principal
                    nombreCurso = `${this.personaSeleccionada.tutor_nivel} ${this.personaSeleccionada.tutor_especialidad} "${this.personaSeleccionada.tutor_paralelo}"`;
                }

                if (!nombreCurso) return; // Si por alguna razón no existe, saltamos

                // Verificamos si este curso ya tiene una fila creada en el resumen
                let cursoEnResumen = this.asignacionesResumen.find(item => item.curso_id === idCurso);

                if (!cursoEnResumen) {
                    // Si no existe, creamos el bloque del curso en el resumen
                    cursoEnResumen = {
                        curso_id: idCurso,
                        curso_nombre: nombreCurso,
                        asignaturas: []
                    };
                    this.asignacionesResumen.push(cursoEnResumen);
                }

                // Inyectamos las materias seleccionadas dentro de este curso
                this.asignaturasTemporales.forEach(asigTemp => {
                    // Evitamos duplicar materias en un mismo curso
                    const existeAsig = cursoEnResumen.asignaturas.find(a => a.id_asignatura === asigTemp.id_asignatura);

                    if (!existeAsig) {
                        cursoEnResumen.asignaturas.push({
                            id_asignatura: asigTemp.id_asignatura,
                            nombre: asigTemp.nombre,
                            horas_semanales: 1 // Valor base para el input
                        });
                    }
                });
            });

            // Limpiamos la selección temporal de materias para que el usuario pueda hacer otra búsqueda
            this.asignaturasTemporales = [];
        },

        removerDelResumen(index) {
            this.asignacionesResumen.splice(index, 1);
        },

        limpiarModalAsignacion() {
            this.cursosSeleccionados = [];
            this.asignaturasTemporales = [];
            this.asignacionesResumen = [];
            this.busquedaAsignatura = '';
        },

        async cargarAsignacionesActuales(id_docente) {
            try {
                const response = await API.get(`${this.baseUrl}/curso_asignaturas/docente/${id_docente}`);
                // El backend debe enviar la data estructurada para llenar 'asignacionesResumen' directamente
                this.asignacionesResumen = response.data?.data || [];

                // Llenar cursos seleccionados basado en lo que ya tiene
                this.cursosSeleccionados = this.asignacionesResumen.map(item => item.curso_id);
            } catch (error) {
                console.error("Error al cargar data actual", error);
            }
        },
        actualizarTablaSeleccion() {
            // Si el usuario desmarca un curso a la izquierda, 
            // lo eliminamos automáticamente de la tabla de resumen a la derecha.
            this.asignacionesResumen = this.asignacionesResumen.filter(item =>
                this.cursosSeleccionados.includes(item.curso_id)
            );
        },
        calcularEdad(fechaNacimiento) {
            if (!fechaNacimiento) return 0;
            const hoy = new Date();
            const nac = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - nac.getFullYear();
            const m = hoy.getMonth() - nac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) {
                edad--;
            }
            return edad;
        },
        async guardarAsignaciones() {
            // Validar que todas las materias tengan horas antes de enviar
            for (let item of this.asignacionesResumen) {
                for (let asig of item.asignaturas) {
                    if (!asig.horas_semanales || asig.horas_semanales <= 0) {
                        mostraralertas2(`Por favor, asigne horas válidas para "${asig.nombre}" en el curso ${item.curso_nombre}`, "warning");
                        return;
                    }
                }
            }

            try {
                const payload = {
                    id_docente: this.personaSeleccionada.id_docente,
                    asignaciones: this.asignacionesResumen.map(item => ({
                        id_curso: item.curso_id || item.id_curso,
                        asignaturas: item.asignaturas.map(a => ({
                            id_asignatura: a.id_asignatura,
                            horas_semanales: a.horas_semanales
                        }))
                    }))
                };

                const url = this.modoModal === 'crear'
                    ? `${this.baseUrl}/curso_asignaturas_lote/crear`
                    : `${this.baseUrl}/curso_asignaturas_lote/actualizar`;

                const response = this.modoModal === 'crear'
                    ? await API.post(url, payload)
                    : await API.put(url, payload);

                if (response) {
                    mostraralertas2(`Asignaciones guardadas correctamente`, "success");
                    document.getElementById('btnCloseModalAsignacion').click();
                    await this.getData();
                }
            } catch (error) {
                // --- AQUÍ ATRAPAMOS EL CONFLICTO 409 ---
                if (error.response && error.response.status === 409 && error.response.data.conflictos) {

                    let listaErrores = "Ya hay otro docente impartiendo:\n\n";

                    // Recorremos los conflictos devueltos por el backend
                    error.response.data.conflictos.forEach(conflicto => {

                        // Buscamos el nombre del curso en nuestro resumen
                        let curso = this.asignacionesResumen.find(c => (c.curso_id || c.id_curso) == conflicto.id_curso);
                        let nombreCurso = curso ? curso.curso_nombre : `Curso ${conflicto.id_curso}`;

                        // Buscamos el nombre de la asignatura en nuestro resumen
                        let nombreAsig = `Asignatura ${conflicto.id_asignatura}`;
                        if (curso) {
                            let asig = curso.asignaturas.find(a => a.id_asignatura == conflicto.id_asignatura);
                            if (asig) {
                                nombreAsig = asig.nombre;
                            }
                        }

                        listaErrores += `• ${nombreAsig} en ${nombreCurso}\n`;
                    });

                    // Mostramos la alerta con el detalle exacto
                    mostraralertas2(listaErrores, "error");

                } else {
                    // Manejo de errores generales (500, etc)
                    mostraralertas2(error.response?.data?.mensaje || "Error al procesar la solicitud", "error");
                }
            }
        },
        async GetCursos() {
            try {
                const response = await API.get(`${this.baseUrl}/cursos_activos`);
                this.objetoCursoList = response.data?.data || [];
            } catch (error) {
                console.error("❌ Error al obtener roles:", error);
            }
        },

        getPhotoUrl(ci) {
            if (!ci) return "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
            return `${API.defaults.baseURL}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
        },
        handleImageError(event) {
            event.target.src = "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
        },
        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/curso_asignaturas`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda,
                    }
                });
                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.totaldata = pagination.total || 0;
                this.objetoList = data;
            } catch (error) {
                this.objetoList = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.cargando = false;
            }
        },
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

/* Transiciones suaves y retoques extra */
.avatar-sm img {
    transition: transform 0.3s ease;
}

table tbody tr:hover .avatar-sm img {
    transform: scale(1.1);
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}

.modal-content {
    overflow: hidden;
}

.form-control:focus,
.form-select:focus {
    box-shadow: none;
    border-color: var(--bs-primary);
}
</style>
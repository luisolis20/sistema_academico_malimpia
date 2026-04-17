<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div
                    class="header-icon shadow-sm bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3">
                    <i class="fas fa-user-edit fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--green-900); font-family: 'Fraunces', serif;">
                        Gestión Global de Cursos
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración de cursos, aquí se asignarán los docentes tutores de cada curso..
                    </p>
                </div>
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
                    <thead style="background: var(--green-800); color: white;">
                        <tr>
                            <th class="ps-4">Id</th>
                            <th class="ps-4">Docente</th>
                            <th>Curso Asignado</th>
                            <th>Paralelo</th>
                            <th>Periodo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha de Creación</th>
                            <th class="text-center">Fecha de Modificación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.personID">
                            <td class="ps-4 fw-bold text-secondary" v-if="user.CursoID">{{ user.CursoID }}</td>
                            <td class="ps-4 text-muted small" v-else>Sin asignar</td>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light text-success rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0;">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold text-dark">{{ user.nombres }} {{ user.apellidos }}</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                        <span title="Paralelo">
                                            Rol: {{ user.nombre_rol }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td v-if="user.CursoID && user.EspecialidadID">
                                <div class="fw-bold text-dark" v-if="user.nombre_nivel === '0'">
                                    {{ user.nombre_especialidad }}
                                </div>
                                <div class="fw-bold text-dark" v-else>
                                    {{ user.nombre_nivel }} {{ user.nombre_especialidad }}
                                </div>
                            </td>
                            <td class="text-muted small" v-else>Sin Curso Asignado</td>
                            <td v-if="user.CursoID"><span class="badge bg-light text-dark border">{{ user.paralelo }}
                                </span>
                            </td>
                            <td class="text-muted small" v-else>Sin Curso Asignado</td>
                            <td v-if="user.paralelo"><span class="badge bg-light text-dark border">{{ user.nombre_periodo }}
                                </span>
                            </td>
                            <td class="text-muted small" v-else>Sin Curso Asignado</td>

                            <td class="text-center">
                                <span v-if="user.estado_curso == 1"
                                    class="badge bg-success-subtle text-success border border-success px-3">Activo</span>
                                <span v-else-if="user.CursoID"
                                    class="badge bg-danger-subtle text-danger border border-danger px-3">Inactivo</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-calendar-plus text-success me-1"></i> {{ user.created_at }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-edit text-primary me-1"></i> {{ user.updated_at }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light text-primary border shadow-sm"
                                        @click="abrirModalCrear(user)" v-if="!user.CursoID" title="Asignar curso">
                                        <i class="fas fa-user-plus me-1"></i> Asignar Curso
                                    </button>
                                    <button class="btn btn-sm btn-light text-info border shadow-sm ms-1"
                                        @click="abrirModalEditar(user)" v-if="user.CursoID"
                                        title="Editar el curso asignado">
                                        <i class="fas fa-user-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger"
                                        @click="eliminar(user.CursoID, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.estado_curso == 1 && user.CursoID" title="Inhabilitar este curso">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success"
                                        @click="habilitar(user.CursoID, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.estado_curso == 0 && user.CursoID"
                                        title="Habilitar este curso">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 text-primary mb-2 d-block"></i> Cargando...
                            </td>
                        </tr>

                        <tr v-if="!cargando && objetoList.length === 0">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-search fs-2 text-secondary mb-2 d-block"></i>
                                No se encontraron registros en el sistema.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3"
                v-if="lastPage > 1">
                <span class="text-muted small">Página <strong>{{ currentPage }}</strong> de <strong>{{ lastPage
                }}</strong></span>
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                            <button class="page-link" @click="cambiarPagina(currentPage - 1)"
                                :disabled="currentPage <= 1">Anterior</button>
                        </li>
                        <li class="page-item" v-for="page in paginasMostradas" :key="page"
                            :class="{ active: page === currentPage }">
                            <button class="page-link" @click="cambiarPagina(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link" @click="cambiarPagina(currentPage + 1)"
                                :disabled="currentPage >= lastPage">Siguiente</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="fas fa-user-plus me-2"></i>Asignar docente al curso
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalCrear"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert alert-success bg-success-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 text-success me-3"></i>
                            <div class="small text-dark">
                                <strong>Guía de Registro:</strong><br>
                                Aquí debes asignar un docente tutor a un curso específico.
                                Asegúrate de seleccionar el nivel académico y la especialidad correctos
                                para que el curso se configure adecuadamente.
                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="row align-items-center">

                                <div class="col-md-3 text-center border-end mb-4 mb-md-0">
                                    <h6 class="text-muted mb-3 fw-bold">Foto de Perfil</h6>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <div class="rounded-circle shadow-sm border overflow-hidden"
                                            style="width: 150px; height: 150px; background-color: #f8f9fa;">
                                            <img :src="personaSeleccionada.foto" class="w-100 h-100"
                                                style="object-fit: cover;" alt="Vista previa">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-3">

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light text-secondary fw-bold"
                                                    id="crearCedula" readonly :value="personaSeleccionada.cedula">
                                                <label for="crearCedula">Cédula</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light text-secondary fw-bold"
                                                    id="crearNombres" readonly
                                                    :value="personaSeleccionada.nombres + ' ' + personaSeleccionada.apellidos">
                                                <label for="crearNombres">Nombres y Apellidos</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="crearNivel"
                                                    v-model="objetoData.id_nivel">
                                                    <option value="" disabled>Seleccione un nivel...</option>
                                                    <option v-for="nivel in objetoNivelList" :key="nivel.id_nivel"
                                                        :value="nivel.id_nivel">
                                                        {{ nivel.nombre }}
                                                    </option>
                                                </select>
                                                <label for="crearNivel">Nivel Académico</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="crearEspecialidad"
                                                    v-model="objetoData.id_especialidad">
                                                    <option value="" disabled>Seleccione una especialidad...</option>
                                                    <option v-for="especialidad in objetoEspecialidadList"
                                                        :key="especialidad.id_especialidad"
                                                        :value="especialidad.id_especialidad">
                                                        {{ especialidad.nombre }}
                                                    </option>
                                                </select>
                                                <label for="crearEspecialidad">Especialidad</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="crearParalelo"
                                                    v-model="objetoData.paralelo">
                                                    <option value="" disabled selected>Seleccione un paralelo</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                </select>
                                                <label for="crearParalelo">Paralelo</label>
                                                <div class="invalid-feedback">Seleccione un paralelo.</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <button type="submit" class="btn btn-success w-100 py-3 shadow-sm rounded-3 fw-bold fs-6">
                                <i class="fas fa-save me-2"></i>Guardar Asignación
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary">
                            <i class="fas fa-user-edit me-2"></i>Editar Asignación de Curso
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalEditar"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert alert-primary bg-primary-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                            <div class="small text-dark">
                                <strong>Actualización de datos:</strong><br>
                                Actualiza al docente tutor del curso. Ten en cuenta que si cambias el estado a
                                <em>Inactivo</em>, los usuarios podrían perder acceso.
                            </div>
                        </div>

                        <form @submit.prevent="editarData">
                            <div class="row align-items-center">

                                <div class="col-md-3 text-center border-end mb-4 mb-md-0">
                                    <h6 class="text-muted mb-3 fw-bold">Foto de Perfil</h6>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <div class="rounded-circle shadow-sm border overflow-hidden"
                                            style="width: 150px; height: 150px; background-color: #f8f9fa;">
                                            <img :src="personaSeleccionada.foto" class="w-100 h-100"
                                                style="object-fit: cover;" alt="Vista previa">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-3">

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light text-secondary fw-bold"
                                                    id="editarCedula" readonly :value="personaSeleccionada.cedula">
                                                <label for="editarCedula">Cédula</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light text-secondary fw-bold"
                                                    id="editarNombres" readonly
                                                    :value="personaSeleccionada.nombres + ' ' + personaSeleccionada.apellidos">
                                                <label for="editarNombres">Nombres y Apellidos</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="editarNivel"
                                                    v-model="objetoEdit.id_nivel">
                                                    <option value="" disabled>Seleccione un nivel...</option>
                                                    <option v-for="nivel in objetoNivelList" :key="nivel.id_nivel"
                                                        :value="nivel.id_nivel">
                                                        {{ nivel.nombre }}
                                                    </option>
                                                </select>
                                                <label for="editarNivel">Nivel Académico</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="editarEspecialidad"
                                                    v-model="objetoEdit.id_especialidad">
                                                    <option value="" disabled>Seleccione una especialidad...</option>
                                                    <option v-for="especialidad in objetoEspecialidadList"
                                                        :key="especialidad.id_especialidad"
                                                        :value="especialidad.id_especialidad">
                                                        {{ especialidad.nombre }}
                                                    </option>
                                                </select>
                                                <label for="editarEspecialidad">Especialidad</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="editarParalelo"
                                                    v-model="objetoEdit.paralelo">
                                                    <option value="" disabled selected>Seleccione un paralelo</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                </select>
                                                <label for="editarParalelo">Paralelo</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="editarEstado"
                                                    v-model="objetoEdit.estado">
                                                    <option value="" disabled selected>Seleccione un estado</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                                <label for="editarEstado">Estado del Curso</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm rounded-3 fw-bold fs-6">
                                <i class="fas fa-sync-alt me-2"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios";
import { confimar, confimarhabi, mostraralertas2, confimarreseteo } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap';

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            peridoactivo: null,
            objetoNivelList: [],
            objetoEspecialidadList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            refreshKey: Date.now(),

            // Variables para Modal individual
            personaSeleccionada: {},
            objetoData: {
                id_periodo: "",
                id_nivel: "",
                id_especialidad: "",
                paralelo: "",
                id_docente_tutor: "",
                estado: 1,
            },
            objetoEdit: { // Para Editar
                id_curso: "",
                id_periodo: "",
                id_nivel: "",
                id_especialidad: "",
                paralelo: "",
                id_docente_tutor: "",
                estado: "",
            },

            // Variables para registro masivo
            procesandoMasivo: false,
            progresoMasivo: 0,
            erroresMasivos: [],
            totalPendientesMasivo: [],
            totalPendientesMasivoMayores20: [],
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
        await Promise.all([
            this.GetObjetoListPeriodo(),
            this.GetObjetoListNivel(),
            this.GetObjetoListEspecialidad()
        ]);
    },
    methods: {
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
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

        abrirModalCrear(user) {
            this.personaSeleccionada = {
                nombres: user.nombres,
                apellidos: user.apellidos,
                cedula: user.cedula,
                foto: this.getPhotoUrl(user.personID)
            };
            this.objetoData = {
                id_docente_tutor: user.personID,
                id_periodo: this.peridoactivo,
            };
            const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
            modal.show();
        },
        abrirModalEditar(user) {
            this.personaSeleccionada = {
                nombres: user.nombres,
                apellidos: user.apellidos,
                cedula: user.cedula,
                foto: this.getPhotoUrl(user.personID)
            };

            this.objetoEdit = {
                id_curso: user.CursoID,
                id_periodo: user.PeriodoID,
                id_nivel: user.NivelID,
                id_especialidad: user.EspecialidadID,
                paralelo: user.paralelo,
                id_docente_tutor: user.personID,
                estado: user.estado_curso,
            };

            const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        },

        async guardarData() {
            try {
                const params = {
                    id_periodo: this.peridoactivo,
                    id_nivel: this.objetoData.id_nivel,
                    id_especialidad: this.objetoData.id_especialidad,
                    paralelo: this.objetoData.paralelo,
                    id_docente_tutor: this.objetoData.id_docente_tutor,
                    estado: 1,
                };
                // LLamada al endpoint individual
                const response = await API.post(`${this.baseUrl}/cursos`, params);
                if (response) {
                    mostraralertas2("Curso asignado exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalCrear').click();
                } else {
                    mostraralertas2("Curso asignado, pero se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                mostraralertas2("Error al crear curso", "error");
            }
        },
        async limpiar() {
            this.objetoEdit = { id_curso: 0, id_periodo: 0, id_nivel: 0, id_especialidad: 0, paralelo: "", id_docente_tutor: 0, estado: 0 };
            this.objetoData = { id_periodo: 0, id_nivel: 0, id_especialidad: 0, paralelo: "", id_docente_tutor: 0, estado: 1 };

        },
        async editarData() {
            try {
                // Asegúrate de que esta ruta coincida con la que tienes en tu Laravel
                const response = await API.put(`${this.baseUrl}/cursos/${this.objetoEdit.id_curso}`, {
                    id_periodo: this.objetoEdit.id_periodo,
                    id_nivel: this.objetoEdit.id_nivel,
                    id_especialidad: this.objetoEdit.id_especialidad,
                    paralelo: this.objetoEdit.paralelo,
                    id_docente_tutor: this.objetoEdit.id_docente_tutor,
                    estado: this.objetoEdit.estado,
                });

                if (response) {
                    mostraralertas2(" Curso actualizado correctamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Curso actualizado, pero se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                mostraralertas2("Error al actualizar el curso", "error");
                console.error(error);
            }
        },

        async GetObjetoListPeriodo() {
            try {
                const response = await API.get(`${this.baseUrl}/periodos_lectivos_activos`); // Usa tu endpoint Roleshabilitados
                this.peridoactivo = response.data?.data[0].id_periodo || [];
            } catch (error) {
                console.error("❌ Error al obtener roles:", error);
            }
        },
        async GetObjetoListNivel() {
            try {
                const response = await API.get(`${this.baseUrl}/niveles_academicos_activos`); // Usa tu endpoint Roleshabilitados
                this.objetoNivelList = response.data?.data || [];
            } catch (error) {
                console.error("❌ Error al obtener roles:", error);
            }
        },
        async GetObjetoListEspecialidad() {
            try {
                const response = await API.get(`${this.baseUrl}/especialidades_activos`); // Usa tu endpoint Roleshabilitados
                this.objetoEspecialidadList = response.data?.data || [];
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
                const response = await API.get(`${this.baseUrl}/cursos`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda,
                    }
                });
                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.totalPersonas = pagination.total || 0;
                this.objetoList = data;
            } catch (error) {
                this.objetoList = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.cargando = false;
            }
        },
        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_curso/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar a ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar:", error);
            }
        },

        async habilitar(id, nombre) {
            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_curso/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar a ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar:", error);
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
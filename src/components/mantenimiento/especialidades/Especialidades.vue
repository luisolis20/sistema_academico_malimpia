<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div
                    class="header-icon shadow-sm bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3">
                    <i class="fas fa-graduation-cap fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--green-900); font-family: 'Fraunces', serif;">
                        Gestión de Especialidades
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración y configuración de especialidades académicas
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div
                    class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border border-success bg-success-subtle text-success">
                    <i class="fas fa-award me-2"></i>
                    <span class="fw-medium">
                        Total: <span v-if="totaldata > 0">{{ totaldata }}</span><span v-else>0</span>
                    </span>
                </div>

                <button
                    class="btn btn-success btn-lg shadow-sm rounded-pill d-flex align-items-center interactive-btn px-4"
                    data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    <i class="fas fa-plus-circle me-2"></i>
                    <span class="fw-bold fs-6">Nuevo Registro</span>
                </button>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre de la especialidad...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de una especialidad para buscar en la
                    base de datos.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: var(--green-800); color: white;">
                        <tr>
                            <th class="ps-4">Id</th>
                            <th>Nombre </th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha de Creación</th>
                            <th class="text-center">Fecha de Modificación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_especialidad">
                            <td class="ps-4 fw-bold text-secondary">{{ user.id_especialidad }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span>{{ user.nombre }}</span>
                                </div>
                            </td>
                            <td class="text-center" v-if="user.estado == 1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success px-3">Activo</span>
                            </td>
                            <td class="text-center" v-else>
                                <span
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
                                    <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalEditUsuario" @click="cargarDatosEdicion(user)"
                                        title="Editar detalles de esta especialidad">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger"
                                        @click="eliminar(user.id_especialidad, user.nombre)" v-if="user.estado == 1"
                                        title="Inhabilitar esta especialidad (ocultarla del sistema)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success"
                                        @click="habilitar(user.id_especialidad, user.nombre)" v-else
                                        title="Habilitar esta especialidad nuevamente">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 text-light mb-3 d-block"></i>
                                No se encontraron especialidades. ¡Haz clic en "Nuevo Registro" para empezar!
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 text-primary mb-2 d-block"></i>
                                Cargando información...
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
                                :disabled="currentPage <= 1">
                                Anterior
                            </button>
                        </li>

                        <li class="page-item" v-for="page in paginasMostradas" :key="page"
                            :class="{ active: page === currentPage }">
                            <button class="page-link" @click="cambiarPagina(page)">{{ page }}</button>
                        </li>

                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link" @click="cambiarPagina(currentPage + 1)"
                                :disabled="currentPage >= lastPage">
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
                        <h5 class="modal-title fw-bold text-success"><i class="fas fa-plus-circle me-2"></i>Registrar
                            nueva Especialidad</h5>
                        <button type="button" class="btn-close" id="btnCloseModalCrear"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert alert-success bg-success-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 text-success me-3"></i>
                            <div class="small text-dark">
                                <strong>¿Qué hacer aquí?</strong><br>
                                Registra una especialidad (ej. <em>"Básica" (para escuela)</em>, <em>Informática </em>,
                                <em>Contabilidad</em>)

                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input v-model="objetoData.nombre" type="text" class="form-control"
                                        :class="{ 'is-invalid': errorsData.nombre }" id="crearNombre"
                                        placeholder="Nombre de la Especialidad">
                                    <label for="crearNombre">Nombre de la Especialidad</label>
                                    <div class="invalid-feedback">Por favor, ingrese el nombre de la especialidad.
                                    </div>
                                </div>
                                <div class="form-text text-muted small ms-1">Debe ser un nombre corto y descriptivo. Ej:
                                    Básica, Informática, Contabilidad...</div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2 shadow-sm rounded-3 fw-bold">
                                <i class="fas fa-save me-2"></i>Crear Especialidad
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
                        <h5 class="modal-title fw-bold text-primary"><i class="fas fa-edit me-2"></i>Editar Especialidad
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
                                Modifica la información de la especialidad. Ten en cuenta que si cambias el estado a
                                <em>Inactivo</em>, las especialidades podrían perder acceso.
                            </div>
                        </div>

                        <form @submit.prevent="editarData">
                            <div class="form-floating mb-3">
                                <input v-model="objetoEdit.nombre" type="text" class="form-control"
                                    :class="{ 'is-invalid': errorsEdit.nombre }" id="editNombre"
                                    placeholder="Nombre de la Especialidad">
                                <label for="editNombre">Nombre de la Especialidad</label>
                                <div class="invalid-feedback">Por favor, ingrese el nombre de la especialidad.</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <select v-model="objetoEdit.estado" class="form-select border-primary"
                                        :class="{ 'is-invalid': errorsEdit.estado }" id="editEstado">
                                        <option value="" disabled selected>Seleccione un estado</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <label for="editEstado">Estado actual de la Especialidad</label>
                                    <div class="invalid-feedback">Seleccione un estado válido.</div>
                                </div>
                                <div class="form-text text-muted small ms-1">Especialidades inactivas no pueden ser
                                    asignadas a nuevos usuarios.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm rounded-3 fw-bold">
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
import API from "@/assets/js/axios"
import { confimar, confimarhabi, mostraralertas2 } from "@/assets/js/funciones/functions";

export default {
    data() {
        return {
            baseUrl: "/sistma",
            objetoData: {
                nombre: "",
                estado: 1,
            },
            errorsData: {
                nombre: false,
            },
            objetoEdit: {
                id_especialidad: 0,
                nombre: "",
                estado: "",
            },
            errorsEdit: {
                nombre: false,
                estado: false
            },
            busqueda: '',
            timeoutBusqueda: null, // Para manejar el retraso de la búsqueda
            objetoList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            totaldata: 0,
        }
    },
    computed: {
        // Genera los números de página a mostrar en la paginación dinámicamente
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

    },
    watch: {
        // Escucha cambios en el input de búsqueda
        busqueda(newVal) {
            clearTimeout(this.timeoutBusqueda);
            // Espera 500ms después de que el usuario deje de escribir para hacer la petición
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1; // Volver a la primera página al buscar
                this.getData();
            }, 500);
        },

    },
    async mounted() {
        await this.getData();
    },
    methods: {

        cargarDatosEdicion(user) {
            this.errorsEdit = { nombre: false, estado: false };
            this.objetoEdit = {
                id_especialidad: user.id_especialidad,
                nombre: user.nombre,
                estado: user.estado,
            };
        },

        // Nueva función para cambiar de página
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData(); // Vuelve a consultar la base de datos con la página nueva
            }
        },

        async getData() {
            this.cargando = true;
            try {
                // Enviamos 'page' y 'search_query' para aprovechar la paginación y filtros de Laravel
                const response = await API.get(`${this.baseUrl}/especialidades`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda
                    }
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
            this.errorsData.nombre = this.objetoData.nombre.trim() === "";
            return !this.errorsData.nombre && !this.errorsData.orden_jerarquia;
        },

        async guardarData() {
            if (!this.validarFormularioCrear()) {
                return;
            }

            try {
                const response = await API.post(`${this.baseUrl}/especialidades`, this.objetoData);
                if (response) {
                    mostraralertas2("Especialidad creada exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalCrear').click();
                } else {
                    mostraralertas2("Especialidad creada, pero se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                console.error("❌ Error al crear especialidad:", error?.response?.data || error);
                mostraralertas2("Error al crear especialidad. Por favor, inténtelo de nuevo.", "error");
            }
        },

        validarFormularioEditar() {
            this.errorsEdit.nombre = this.objetoEdit.nombre.toString().trim() === "";
            this.errorsEdit.estado = this.objetoEdit.estado === "";
            return !this.errorsEdit.nombre && !this.errorsEdit.estado;
        },

        async editarData() {
            if (!this.validarFormularioEditar()) {
                return;
            }

            try {
                const params = {
                    nombre: this.objetoEdit.nombre,
                    id_especialidad: this.objetoEdit.id_especialidad,
                    estado: this.objetoEdit.estado,
                };

                const response = await API.put(`${this.baseUrl}/especialidades/${this.objetoEdit.id_especialidad}`, params);
                if (response) {
                    mostraralertas2("Especialidad actualizada exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Especialidad actualizada, pero se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                console.error("❌ Error al actualizar especialidad:", error?.response?.data || error);
                mostraralertas2("Error al actualizar especialidad. Por favor, inténtelo de nuevo.", "error");
            }
        },

        limpiar() {
            this.objetoEdit = { id_especialidad: 0, nombre: "", estado: "" };
            this.objetoData = { nombre: "", estado: 1 };
            this.errorsData = { nombre: false, orden_jerarquia: false };
            this.errorsEdit = { nombre: false, orden_jerarquia: false, estado: false };
        },

        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_especialidad/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar la especialidad ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar la especialidad:", error);
            }
        },

        async habilitar(id, nombre) {
            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_especialidad/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar la especialidad ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar la especialidad:", error);
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
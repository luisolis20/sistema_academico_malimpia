<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header"
            style="border-left: 6px solid #F4B324;">
            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                    style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                    <i class="fas fa-book-open fs-4" style="color: #F4B324;"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                        Gestión de Asignaturas
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración del catálogo de materias
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border shadow-sm"
                    style="background-color: rgba(244, 179, 36, 0.1); border-color: #F4B324 !important; color: #1D2A68;">
                    <i class="fas fa-layer-group me-2" style="color: #F4B324;"></i>
                    <span class="fw-bold">
                        Total: <span v-if="totaldata > 0">{{ totaldata }}</span><span v-else>0</span>
                    </span>
                </div>

                <button
                    class="btn btn-lg shadow-sm rounded-pill d-flex align-items-center interactive-btn px-4 border-0"
                    data-bs-toggle="modal" data-bs-target="#modalUsuario"
                    style="background-color: #1D2A68; color: white;">
                    <i class="fas fa-plus-circle me-2" style="color: #F4B324;"></i>
                    <span class="fw-bold fs-6">Nuevo Registro</span>
                </button>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre de la asignatura...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de una asignatura para buscar en la
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
                                Nombre</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Estado
                            </th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha de
                                Creación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha de
                                Modificación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_asignatura">
                            <td class="ps-4 fw-bold text-secondary">{{ user.id_asignatura }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold" style="color: #1D2A68;">{{ user.nombre }}</span>
                                </div>
                            </td>

                            <td class="text-center" v-if="user.estado == 1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success px-3 rounded-pill shadow-sm">Activo</span>
                            </td>
                            <td class="text-center" v-else>
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger px-3 rounded-pill shadow-sm">Inactivo</span>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1 shadow-sm">
                                    <i class="far fa-calendar-plus me-1" style="color: #F4B324;"></i> {{ user.created_at
                                    }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1 shadow-sm">
                                    <i class="far fa-edit me-1" style="color: #F4B324;"></i> {{ user.updated_at }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light border shadow-sm" style="color: #1D2A68;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditUsuario"
                                        @click="cargarDatosEdicion(user)" title="Editar detalles de esta asignatura">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger border shadow-sm ms-1"
                                        @click="eliminar(user.id_asignatura, user.nombre)" v-if="user.estado == 1"
                                        title="Inhabilitar esta asignatura (ocultarla del sistema)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success border shadow-sm ms-1"
                                        @click="habilitar(user.id_asignatura, user.nombre)" v-else
                                        title="Habilitar esta asignatura nuevamente">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 mb-3 d-block"
                                    style="color: #1D2A68; opacity: 0.3;"></i>
                                No se encontraron asignaturas. ¡Haz clic en "Nuevo Registro" para empezar!
                            </td>
                        </tr>

                        <tr v-if="cargando">
                            <td colspan="6" class="text-center py-5 text-muted">
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
                            <i class="fas fa-plus-circle me-2" style="color: #F4B324;"></i>Registrar nueva Asignatura
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
                                Registra una asignatura (ej. <em>"Matemáticas" (para escuela)</em>, <em>Lenguaje</em>,
                                <em>Física</em>)
                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input v-model="objetoData.nombre" type="text" class="form-control"
                                        :class="{ 'is-invalid': errorsData.nombre }" id="crearNombre"
                                        placeholder="Nombre de la Asignatura"
                                        style="border-color: rgba(29, 42, 104, 0.2);">
                                    <label for="crearNombre">Nombre de la Asignatura</label>
                                    <div class="invalid-feedback">Por favor, ingrese el nombre de la asignatura.
                                    </div>
                                </div>
                                <div class="form-text text-muted small ms-1">Debe ser un nombre corto y descriptivo. Ej:
                                    Matemáticas, Lenguaje, etc.</div>
                            </div>
                            <button type="submit" class="btn w-100 py-2 shadow-sm rounded-3 fw-bold border-0"
                                style="background-color: #1D2A68; color: white;">
                                <i class="fas fa-save me-2" style="color: #F4B324;"></i>Crear Asignatura
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
                            <i class="fas fa-edit me-2" style="color: #F4B324;"></i>Editar Asignatura
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalEditar"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(29, 42, 104, 0.05); border-left: 4px solid #F4B324 !important;"
                            role="alert">
                            <i class="fas fa-info-circle fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small text-dark">
                                <strong style="color: #1D2A68;">Actualización de datos:</strong><br>
                                Modifica la información de la asignatura. Ten en cuenta que si cambias el estado a
                                <em>Inactivo</em>, las asignaturas podrían perder acceso.
                            </div>
                        </div>

                        <form @submit.prevent="editarData">
                            <div class="form-floating mb-3">
                                <input v-model="objetoEdit.nombre" type="text" class="form-control"
                                    :class="{ 'is-invalid': errorsEdit.nombre }" id="editNombre"
                                    placeholder="Nombre de la Asignatura" style="border-color: rgba(29, 42, 104, 0.2);">
                                <label for="editNombre">Nombre de la Asignatura</label>
                                <div class="invalid-feedback">Por favor, ingrese el nombre de la asignatura.</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <select v-model="objetoEdit.estado" class="form-select"
                                        :class="{ 'is-invalid': errorsEdit.estado }" id="editEstado"
                                        style="border-color: rgba(29, 42, 104, 0.2);">
                                        <option value="" disabled selected>Seleccione un estado</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <label for="editEstado">Estado actual de la Asignatura</label>
                                    <div class="invalid-feedback">Seleccione un estado válido.</div>
                                </div>
                                <div class="form-text text-muted small ms-1">Asignaturas inactivas no pueden ser
                                    asignadas a nuevos usuarios.</div>
                            </div>

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
                estado: 1,
            },
            errorsData: {
                nombre: false,
            },
            objetoEdit: {
                id_asignatura: 0,
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
                id_asignatura: user.id_asignatura,
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
                const response = await API.get(`${this.baseUrl}/asignaturas`, {
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
            return !this.errorsData.nombre;
        },

        async guardarData() {
            if (!this.validarFormularioCrear()) {
                return;
            }

            try {
                const response = await API.post(`${this.baseUrl}/asignaturas`, this.objetoData);
                if (response) {
                    mostraralertas2("Asignatura creada exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalCrear').click();
                } else {
                    mostraralertas2("Asignatura creada, pero se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                console.error("❌ Error al crear asignatura:", error?.response?.data || error);
                mostraralertas2("Error al crear asignatura. Por favor, inténtelo de nuevo.", "error");
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
                    id_asignatura: this.objetoEdit.id_asignatura,
                    estado: this.objetoEdit.estado,
                };

                const response = await API.put(`${this.baseUrl}/asignaturas/${this.objetoEdit.id_asignatura}`, params);
                if (response) {
                    mostraralertas2("Asignatura actualizada exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Asignatura actualizada, pero se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                console.error("❌ Error al actualizar asignatura:", error?.response?.data || error);
                mostraralertas2("Error al actualizar asignatura. Por favor, inténtelo de nuevo.", "error");
            }
        },

        limpiar() {
            this.objetoEdit = { id_asignatura: 0, nombre: "", estado: "" };
            this.objetoData = { nombre: "", estado: 1 };
            this.errorsData = { nombre: false, orden_jerarquia: false };
            this.errorsEdit = { nombre: false, orden_jerarquia: false, estado: false };
        },

        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_asignatura/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar la asignatura ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar la asignatura:", error);
            }
        },

        async habilitar(id, nombre) {
            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_asignatura/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar la asignatura ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar la asignatura:", error);
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
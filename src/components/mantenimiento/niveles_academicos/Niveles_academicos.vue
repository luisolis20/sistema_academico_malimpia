<template>
    <div class="container-fluid py-4">
        <header class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold" style="color: var(--green-900); font-family: 'Fraunces';">
                    Gestión de Niveles Académicos
                </h2>
                <p class="text-muted">Administración de niveles académicos</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary ms-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Registro
                </button>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre del nivel académico...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de un nivel académico para buscar en la
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
                            <th>Orden Jerarquía</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha de Creación</th>
                            <th class="text-center">Fecha de Modificación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_nivel">
                            <td class="ps-4 fw-bold text-secondary">{{ user.id_nivel }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span>{{ user.nombre }}</span>
                                </div>
                            </td>
                            <td style="max-width: 250px;">
                                <div>
                                    {{ user.orden_jerarquia }}
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
                                        title="Editar detalles de este nivel académico">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger"
                                        @click="eliminar(user.id_nivel, user.nombre)" v-if="user.estado == 1"
                                        title="Inhabilitar este nivel académico (ocultarlo del sistema)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success"
                                        @click="habilitar(user.id_nivel, user.nombre)" v-else
                                        title="Habilitar este nivel académico nuevamente">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 text-light mb-3 d-block"></i>
                                No se encontraron niveles académicos. ¡Haz clic en "Nuevo Registro" para empezar!
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
                            nuevo Nivel Académico</h5>
                        <button type="button" class="btn-close" id="btnCloseModalCrear"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert alert-success bg-success-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 text-success me-3"></i>
                            <div class="small text-dark">
                                <strong>¿Qué hacer aquí?</strong><br>
                                Registra un nivel académico (ej. <em>Inicial</em>, <em>1ro </em>, <em>... 10mo, 1ro
                                    Bachillerato</em>)
                                y añade el orden de jerarquía correspondiente.
                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input v-model="objetoData.nombre" type="text" class="form-control"
                                        :class="{ 'is-invalid': errorsData.nombre }" id="crearNombre"
                                        placeholder="Nombre del Nivel Académico">
                                    <label for="crearNombre">Nombre del Nivel Académico</label>
                                    <div class="invalid-feedback">Por favor, ingrese el nombre del nivel académico.
                                    </div>
                                </div>
                                <div class="form-text text-muted small ms-1">Debe ser un nombre corto y descriptivo. Ej:
                                    0, 1ro, 2do, 3er, 4to, 5to, 6to...</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <select v-model="objetoData.orden_jerarquia" class="form-select"
                                        :class="{ 'is-invalid': errorsData.orden_jerarquia }"
                                        id="crearOrdenJerarquizado">
                                        <option value="" disabled>Seleccione el nivel jerarquizado...</option>
                                        <option v-for="item in opcionesJerarquiaData" :key="item.value"
                                            :value="item.value">
                                            {{ item.label }}
                                        </option>
                                    </select>
                                    <label for="crearNivelJerarquizado">Nivel Jerarquía</label>
                                    <div class="invalid-feedback">Por favor, seleccione el nivel jerarquizado.</div>
                                </div>
                                <div class="form-text text-muted small ms-1">Ej: 0, 1ro, 2do, 3ro, 4to, 5to,
                                    6to...</div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 shadow-sm rounded-3 fw-bold">
                                <i class="fas fa-save me-2"></i>Crear Nivel Académico
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
                        <h5 class="modal-title fw-bold text-primary"><i class="fas fa-edit me-2"></i>Editar Nivel
                            Académico</h5>
                        <button type="button" class="btn-close" id="btnCloseModalEditar"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert alert-primary bg-primary-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                            <div class="small text-dark">
                                <strong>Actualización de datos:</strong><br>
                                Modifica la información del nivel académico. Ten en cuenta que si cambias el estado a
                                <em>Inactivo</em>, los niveles académicos podrían perder acceso.
                            </div>
                        </div>

                        <form @submit.prevent="editarData">
                            <div class="form-floating mb-3">
                                <input v-model="objetoEdit.nombre" type="text" class="form-control"
                                    :class="{ 'is-invalid': errorsEdit.nombre }" id="editNombre"
                                    placeholder="Nombre del Nivel Académico">
                                <label for="editNombre">Nombre del Nivel Académico</label>
                                <div class="invalid-feedback">Por favor, ingrese el nombre del nivel académico.</div>
                            </div>

                            <div class="form-floating mb-3">
                                <select v-model="objetoEdit.orden_jerarquia" class="form-select"
                                    :class="{ 'is-invalid': errorsEdit.orden_jerarquia }" id="editOrdenJerarquizado">
                                    <option value="" disabled>Seleccione el nivel jerarquizado...</option>
                                    <option v-for="item in opcionesJerarquiaEdit" :key="item.value" :value="item.value">
                                        {{ item.label }}
                                    </option>
                                </select>
                                <label for="crearNivelJerarquizado">Nivel Jerarquía</label>
                                <div class="invalid-feedback">Por favor, seleccione el nivel jerarquizado.</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <select v-model="objetoEdit.estado" class="form-select border-primary"
                                        :class="{ 'is-invalid': errorsEdit.estado }" id="editEstado">
                                        <option value="" disabled selected>Seleccione un estado</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <label for="editEstado">Estado actual del Nivel Académico</label>
                                    <div class="invalid-feedback">Seleccione un estado válido.</div>
                                </div>
                                <div class="form-text text-muted small ms-1">Niveles académicos inactivos no pueden ser
                                    asignados a nuevos usuarios.</div>
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
                orden_jerarquia: "",
                estado: 1,
            },
            errorsData: {
                nombre: false,
                orden_jerarquia: false
            },
            objetoEdit: {
                id_nivel: 0,
                nombre: "",
                orden_jerarquia: "",
                estado: "",
            },
            errorsEdit: {
                nombre: false,
                orden_jerarquia: false,
                estado: false
            },
            busqueda: '',
            timeoutBusqueda: null, // Para manejar el retraso de la búsqueda
            objetoList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            jerarquiaList: [
                { value: '0', label: '0' },
                { value: '1ro', label: '1ro' },
                { value: '2do', label: '2do' },
                { value: '3ro', label: '3ro' },
                { value: '4to', label: '4to' },
                { value: '5to', label: '5to' },
                { value: '6to', label: '6to' },
                { value: '7mo', label: '7mo' },
                { value: '8vo', label: '8vo' },
                { value: '9no', label: '9no' },
                { value: '10mo', label: '10mo' },
                { value: '1ro Bachillerato', label: '1ro Bachillerato' },
                { value: '2do Bachillerato', label: '2do Bachillerato' },
                { value: '3ro Bachillerato', label: '3ro Bachillerato' },
                { value: 'Graduado', label: 'Graduado' },
            ],
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
        opcionesJerarquiaData() {
            return this.obtenerSiguienteNivel(this.objetoData.nombre);
        },
        opcionesJerarquiaEdit() {
            return this.obtenerSiguienteNivel(this.objetoEdit.nombre);
        }
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
        'objetoData.nombre'(newVal) {
            const opciones = this.obtenerSiguienteNivel(newVal);
            // Si solo queda 1 opción válida, la seleccionamos automáticamente
            if (opciones.length === 1 && newVal.trim() !== '') {
                this.objetoData.orden_jerarquia = opciones[0].value;
            } else {
                this.objetoData.orden_jerarquia = "";
            }
        },
        'objetoEdit.nombre'(newVal) {
            // Hacemos lo mismo para el modal de edición
            const opciones = this.obtenerSiguienteNivel(newVal);
            if (opciones.length === 1 && newVal.trim() !== '') {
                this.objetoEdit.orden_jerarquia = opciones[0].value;
            }
        }
    },
    async mounted() {
        await this.getData();
    },
    methods: {
        obtenerSiguienteNivel(nombreIngresado) {
            // Si el input está vacío, devolvemos toda la lista
            if (!nombreIngresado || nombreIngresado.trim() === '') {
                return this.jerarquiaList; 
            }

            const nombreStr = nombreIngresado.trim().toLowerCase();
            // Buscamos si lo que escribió el usuario coincide con algún nivel actual
            const index = this.jerarquiaList.findIndex(
                item => item.label.toLowerCase() === nombreStr || item.value.toLowerCase() === nombreStr
            );

            // Si encontró una coincidencia y no es el último elemento de la lista
            if (index >= 0 && index < this.jerarquiaList.length - 1) {
                // Devolvemos estrictamente el SIGUIENTE elemento en un arreglo
                return [this.jerarquiaList[index + 1]]; 
            }
            
            // Si escribe algo que no coincide con la jerarquía base (ej: "Maternal"), mostramos todo por defecto
            return this.jerarquiaList; 
        },
        cargarDatosEdicion(user) {
            this.errorsEdit = { nombre: false, orden_jerarquia: false, estado: false };
            this.objetoEdit = {
                id_nivel: user.id_nivel,
                nombre: user.nombre,
                orden_jerarquia: user.orden_jerarquia,
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
                const response = await API.get(`${this.baseUrl}/niveles_academicos`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda
                    }
                });

                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
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
            this.errorsData.orden_jerarquia = this.objetoData.orden_jerarquia.trim() === "";
            return !this.errorsData.nombre && !this.errorsData.orden_jerarquia;
        },

        async guardarData() {
            if (!this.validarFormularioCrear()) {
                return;
            }

            try {
                const response = await API.post(`${this.baseUrl}/niveles_academicos`, this.objetoData);
                if (response) {
                    mostraralertas2("Nivel académico creado exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalCrear').click();
                } else {
                    mostraralertas2("Nivel académico creado, pero se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                console.error("❌ Error al crear nivel académico:", error?.response?.data || error);
                mostraralertas2("Error al crear nivel académico. Por favor, inténtelo de nuevo.", "error");
            }
        },

        validarFormularioEditar() {
            this.errorsEdit.nombre = this.objetoEdit.nombre.toString().trim() === "";
            this.errorsEdit.orden_jerarquia = this.objetoEdit.orden_jerarquia.toString().trim() === "";
            this.errorsEdit.estado = this.objetoEdit.estado === "";
            return !this.errorsEdit.nombre && !this.errorsEdit.orden_jerarquia && !this.errorsEdit.estado;
        },

        async editarData() {
            if (!this.validarFormularioEditar()) {
                return;
            }

            try {
                const params = {
                    nombre: this.objetoEdit.nombre,
                    orden_jerarquia: this.objetoEdit.orden_jerarquia,
                    id_nivel: this.objetoEdit.id_nivel,
                    estado: this.objetoEdit.estado,
                };

                const response = await API.put(`${this.baseUrl}/niveles_academicos/${this.objetoEdit.id_nivel}`, params);
                if (response) {
                    mostraralertas2("Nivel académico actualizado exitosamente", "success");
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Nivel académico actualizado, pero se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                console.error("❌ Error al actualizar nivel académico:", error?.response?.data || error);
                mostraralertas2("Error al actualizar nivel académico. Por favor, inténtelo de nuevo.", "error");
            }
        },

        limpiar() {
            this.objetoEdit = { id_nivel: 0, nombre: "", orden_jerarquia: "", estado: "" };
            this.objetoData = { nombre: "", orden_jerarquia: "", estado: 1 };
            this.errorsData = { nombre: false, orden_jerarquia: false };
            this.errorsEdit = { nombre: false, orden_jerarquia: false, estado: false };
        },

        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_nivel/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar el nivel académico ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar el nivel académico:", error);
            }
        },

        async habilitar(id, nombre) {
            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_nivel/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar el nivel académico ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar el nivel académico:", error);
            }
        }
    }
}
</script>
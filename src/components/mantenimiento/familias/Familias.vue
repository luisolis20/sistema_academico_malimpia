<template>
    <div class="container-fluid py-4">
        <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">

            <!-- FILA SUPERIOR: Título e Icono Principal -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">
                <div class="mb-0 d-flex align-items-center">
                    <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                        style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                        <i class="fas fa-users-cog fs-4"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                            Gestión de Familias
                        </h2>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                            Administración y control de las familias asignadas a los representantes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
            <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
                style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
                <i class="fas fa-sitemap fs-5 mt-1" style="color: #F4B324;"></i>
                <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
                    <strong>Consistencia de Vínculos Relacionales:</strong> Este panel regula la arquitectura de tutoría
                    legal del sistema, enlazando de forma estricta a los representantes con la población estudiantil.
                    Con el propósito de salvaguardar la coherencia transaccional de los datos, el software restringe la
                    modificación o eliminación de <strong>tu propio núcleo familiar asignado</strong> (en caso de que
                    cuentes con uno). Esta política previene la existencia de registros académicos huérfanos y garantiza
                    la fiabilidad de las bitácoras institucionales.
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
                                placeholder="Buscar por cédula... (Solo números)">
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
                                Persona</th>
                            <th class="py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Familias</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.personID">
                            <td class="ps-4 fw-bold text-secondary">{{ user.personID }}</td>
                            <td class="ps-4 py-3" v-if="user.personID === idpersonalogueado">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0; background-color: rgba(244, 179, 36, 0.15); border: 2px solid #F4B324;">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold text-dark">Yo</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                    </div>
                                </div>
                            </td>
                            <td class="ps-4 py-3" v-else>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0; background-color: rgba(244, 179, 36, 0.15); border: 2px solid #F4B324;">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold text-dark">{{ user.nombres }} {{ user.apellidos }}</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                    </div>
                                </div>
                            </td>


                            <td>
                                <div v-if="user.familiares && user.familiares.length > 0"
                                    class="d-flex flex-wrap gap-2">
                                    <div v-for="(fam, index) in user.familiares" :key="index"
                                        class="avatar-sm rounded-circle overflow-hidden shadow-sm border border-2 border-white cursor-pointer"
                                        style="width: 40px; height: 40px;" @click="abrirModalDetalleFamiliar(fam, user)"
                                        title="Ver detalle del familiar">
                                        <img :src="getPhotoUrl(fam.id_estudiante)" @error="handleImageError"
                                            class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                </div>
                                <div v-else>
                                    <span class="badge text-dark px-3 py-2 rounded-pill shadow-sm border"
                                        style="background-color: rgba(244, 179, 36, 0.2); border-color: #F4B324 !important;">
                                        <i class="fas fa-exclamation-circle me-1" style="color: #F4B324;"></i> Sin
                                        asignar familia
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <button v-if="user.familiares && user.familiares.length > 0 "
                                    class="btn btn-sm btn-light border shadow-sm rounded-pill px-3"
                                    style="color: #1D2A68;" @click="abrirModalActualizar(user)">
                                    <i class="fas fa-user-edit me-1"></i> Actualizar Familia
                                </button>
                                <button v-if="!user.familiares && user.familiares.length < 0 && user.personID !== idpersonalogueado" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3"
                                    style="color: #F4B324;" @click="abrirModalAsignar(user)">
                                    <i class="fas fa-users-cog me-1"></i> Asignar Familia
                                </button>
                                <button v-if="user.personID === idpersonalogueado" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3"
                                    style="color: #F4B324;" disabled>
                                    <i class="fas fa-users-cog me-1"></i> No puedes asignar familia a ti mismo
                                </button>       
                            </td>
                        </tr>

                        <tr v-if="cargando">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #F4B324;"></i>
                                Cargando...
                            </td>
                        </tr>
                        <tr v-if="!cargando && objetoList.length === 0">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-search fs-2 mb-2 d-block" style="color: #1D2A68; opacity: 0.5;"></i>
                                No se encontraron registros.
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

        <div class="modal fade" id="modalAsignarFamilia" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">

                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas me-2" :class="isUpdating ? 'fa-user-edit' : 'fa-user-plus'"
                                style="color: #F4B324;"></i>
                            {{ isUpdating ? 'Actualizar' : 'Asignar' }} Familia a {{ representanteActual?.nombres }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body bg-light p-4">
                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(29, 42, 104, 0.05); border-left: 4px solid #F4B324 !important;"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small text-dark">
                                <strong style="color: #1D2A68;">Guía de Registro:</strong><br>
                                Ingresa la cédula del familiar que deseas asignar. Si el familiar ya existe en el
                                sistema,
                                aparecerá su información para que puedas seleccionar el parentesco y añadirlo a la
                                lista.
                                Puedes agregar varios familiares antes de guardar los cambios.
                            </div>
                        </div>

                        <div class="row mb-4 align-items-end">
                            <div class="col-md-8">
                                <label class="fw-bold mb-1" style="color: #1D2A68;">Cédula del Familiar a
                                    buscar:</label>
                                <input type="text" v-model="busquedaFamiliar" class="form-control"
                                    style="border-color: rgba(29, 42, 104, 0.2);"
                                    placeholder="Ingrese número de cédula">
                            </div>
                            <div class="col-md-4">
                                <button class="btn w-100 fw-bold shadow-sm border-0" @click="buscarFamiliar"
                                    style="background-color: #1D2A68; color: white;" :disabled="!busquedaFamiliar">
                                    <i class="fas fa-search me-1" style="color: #F4B324;"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4" v-if="familiarEncontrado">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3" style="color: #1D2A68;">
                                    <i class="fas fa-user-check me-2" style="color: #F4B324;"></i>Persona Encontrada:
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-muted">Nombres y Apellidos</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            :value="familiarEncontrado.nombres + ' ' + familiarEncontrado.apellidos"
                                            disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Cédula</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            :value="familiarEncontrado.cedula" disabled>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="small text-muted fw-bold text-dark">Parentesco</label>
                                        <select class="form-select" style="border-color: rgba(29, 42, 104, 0.3);"
                                            v-model="parentescoSeleccionado">
                                            <option value="" disabled>Seleccione parentesco...</option>
                                            <option value="Hijo/a">Hijo/a</option>
                                            <option value="Hermano/a">Hermano/a</option>
                                            <option value="Sobrino/a">Sobrino/a</option>
                                            <option value="Nieto/a">Nieto/a</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn w-100 fw-bold shadow-sm border-0"
                                            @click="agregarFamiliarATabla"
                                            style="background-color: #F4B324; color: #1D2A68;"
                                            :disabled="!parentescoSeleccionado">
                                            <i class="fas fa-plus me-1"></i> Añadir a Tabla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mt-2" style="color: #1D2A68;">
                            <i class="fas fa-list me-2" style="color: #F4B324;"></i>Familiares en la lista:
                        </h6>
                        <div class="table-responsive bg-white rounded shadow-sm border"
                            style="border-color: rgba(29, 42, 104, 0.1) !important;">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="background-color: rgba(29, 42, 104, 0.05);">
                                    <tr>
                                        <th class="ps-3 py-2" style="color: #1D2A68; border-bottom: none;">Cédula</th>
                                        <th class="py-2" style="color: #1D2A68; border-bottom: none;">Nombres</th>
                                        <th class="py-2" style="color: #1D2A68; border-bottom: none;">Parentesco</th>
                                        <th class="text-center py-2" style="color: #1D2A68; border-bottom: none;">
                                            Remover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fam, index) in familiaresAgregados" :key="index">
                                        <td class="align-middle ps-3">{{ fam.cedula }}</td>
                                        <td class="align-middle fw-bold text-dark">{{ fam.nombres }} {{ fam.apellidos }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge border"
                                                style="background-color: rgba(244, 179, 36, 0.15); border-color: #F4B324 !important; color: #1D2A68;">
                                                {{ fam.parentesco }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm"
                                                @click="familiaresAgregados.splice(index, 1)"
                                                title="Eliminar de la lista">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="familiaresAgregados.length === 0">
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fs-3 mb-2 d-block"
                                                style="opacity: 0.3; color: #1D2A68;"></i>
                                            Aún no has añadido familiares a la lista.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 rounded-bottom-4">
                        <button type="button" class="btn btn-light border shadow-sm"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn fw-bold px-4 shadow-sm border-0"
                            @click="guardarFamiliaAsignada" style="background-color: #1D2A68; color: white;"
                            :disabled="familiaresAgregados.length === 0 && !isUpdating">
                            <i class="fas fa-save me-2" style="color: #F4B324;"></i>
                            {{ isUpdating ? 'Guardar Cambios' : 'Asignar Familia Definitivamente' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDetalleFamiliar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 pb-0 justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-4">

                        <div class="avatar-xl mx-auto mb-3 rounded-circle overflow-hidden shadow-sm"
                            style="width: 100px; height: 100px; border: 3px solid #F4B324; background-color: rgba(244, 179, 36, 0.1);">
                            <img :src="detalleFamiliarData ? getPhotoUrl(detalleFamiliarData.familiar.id_estudiante) : ''"
                                @error="handleImageError" class="w-100 h-100" style="object-fit: cover;">
                        </div>

                        <h5 class="fw-bold mb-0" style="color: #1D2A68;">
                            {{ detalleFamiliarData?.familiar.nombres }} {{ detalleFamiliarData?.familiar.apellidos }}
                        </h5>
                        <p class="text-muted small mb-2">
                            <i class="far fa-id-card me-1" style="color: #F4B324;"></i>{{
                                detalleFamiliarData?.familiar.cedula }}
                        </p>

                        <div class="p-3 rounded-4 mt-3 border"
                            style="background-color: rgba(29, 42, 104, 0.03); border-color: rgba(29, 42, 104, 0.1) !important;">
                            <p class="mb-2 text-secondary small">
                                Parentesco con <br>
                                <strong style="color: #1D2A68;">
                                    {{ detalleFamiliarData?.representante.nombres }} {{
                                        detalleFamiliarData?.representante.apellidos }}
                                </strong>
                            </p>
                            <span class="badge border px-3 py-2 fs-6 shadow-sm"
                                style="background-color: rgba(244, 179, 36, 0.15); border-color: #F4B324 !important; color: #1D2A68;">
                                {{ detalleFamiliarData?.familiar.parentesco }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import API from "@/assets/js/axios";
import { mostraralertas2 } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap';
import { getMe } from "@/assets/js/auth";

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            refreshKey: Date.now(),

            // Variables para el Modal Asignar/Actualizar
            representanteActual: null,
            isUpdating: false, // ¡Nueva variable de estado!
            busquedaFamiliar: '',
            familiarEncontrado: null,
            parentescoSeleccionado: '',
            familiaresAgregados: [],

            // Variables para Modal Detalle
            detalleFamiliarData: null,
            idpersonalogueado: null,
        }
    },
    computed: {
        paginasMostradas() {
            let pages = [];
            let start = Math.max(1, this.currentPage - 2);
            let end = Math.min(this.lastPage, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);
            for (let i = start; i <= end; i++) pages.push(i);
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
        const me = await getMe();
        this.idpersonalogueado = me.id_persona;
        await this.getData();
    },
    methods: {
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
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },
        getPhotoUrl(ci) {
            if (!ci) return "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
            return `${API.defaults.baseURL}${this.baseUrl}/imagenpersona/${ci}?v=${this.refreshKey}`;
        },
        handleImageError(event) {
            event.target.src = "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
        },
        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/familia`, {
                    params: { page: this.currentPage, search_query: this.busqueda }
                });
                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.objetoList = data;
            } catch (error) {
                this.objetoList = [];
            } finally {
                this.cargando = false;
            }
        },

        // --- MÉTODO PARA NUEVA ASIGNACIÓN ---
        abrirModalAsignar(user) {
            this.representanteActual = user;
            this.isUpdating = false;
            this.busquedaFamiliar = '';
            this.familiarEncontrado = null;
            this.parentescoSeleccionado = '';
            this.familiaresAgregados = [];
            const modal = new bootstrap.Modal(document.getElementById('modalAsignarFamilia'));
            modal.show();
        },

        // --- MÉTODO NUEVO PARA ACTUALIZAR ---
        abrirModalActualizar(user) {
            this.representanteActual = user;
            this.isUpdating = true;
            this.busquedaFamiliar = '';
            this.familiarEncontrado = null;
            this.parentescoSeleccionado = '';

            // Clonamos el array de familiares para no alterar los datos de la tabla 
            // principal visualmente antes de guardar en el backend.
            this.familiaresAgregados = JSON.parse(JSON.stringify(user.familiares));

            const modal = new bootstrap.Modal(document.getElementById('modalAsignarFamilia'));
            modal.show();
        },

        async buscarFamiliar() {
            if (!this.busquedaFamiliar) return;
            try {
                const response = await API.get(`${this.baseUrl}/familiar/${this.busquedaFamiliar}`);

                if (response.data && response.data.data && response.data.data.cedula) {
                    this.familiarEncontrado = response.data.data;
                    mostraralertas2("Familiar encontrado", "success");
                } else {
                    this.familiarEncontrado = null;
                    mostraralertas2("No se encontró una persona con esa cédula", "warning");
                }
            } catch (error) {
                console.error("Error al buscar familiar:", error);
                mostraralertas2("Error al realizar la búsqueda", "error");
            }
        },

        agregarFamiliarATabla() {
            if (!this.familiarEncontrado || !this.parentescoSeleccionado) return;

            const existe = this.familiaresAgregados.some(f => f.cedula === this.familiarEncontrado.cedula);
            if (existe) {
                mostraralertas2("Esta persona ya fue añadida a la lista", "warning");
                return;
            }

            this.familiaresAgregados.push({
                ...this.familiarEncontrado,
                parentesco: this.parentescoSeleccionado
            });

            this.familiarEncontrado = null;
            this.busquedaFamiliar = '';
            this.parentescoSeleccionado = '';
        },

        async guardarFamiliaAsignada() {
            try {
                // Mapeamos los familiares para asegurar que Laravel reciba la propiedad 'personID'
                const familiaresFormateados = this.familiaresAgregados.map(fam => ({
                    ...fam,
                    // Si viene de "Actualizar" usa id_estudiante, si es de "Asignar" puede traer personID
                    personID: fam.personID || fam.id_estudiante
                }));

                const payload = {
                    id_representante: this.representanteActual.personID,
                    familiares: familiaresFormateados
                };

                await API.post(`${this.baseUrl}/familia/asignar`, payload);

                let msj = this.isUpdating ? "Familia actualizada correctamente" : "Familia asignada correctamente";
                mostraralertas2(msj, "success");

                this.getData();
                document.getElementById('modalAsignarFamilia').querySelector('.btn-close').click();
            } catch (error) {
                console.error(error);
                // Mostrar error de validación del backend si existe para facilitar la depuración
                let msjError = "Error al guardar la familia";
                if (error.response?.data?.message) {
                    msjError = error.response.data.message;
                }
                mostraralertas2(msjError, "error");
            }
        },

        abrirModalDetalleFamiliar(familiar, representante) {
            this.detalleFamiliarData = { familiar, representante };
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleFamiliar'));
            modal.show();
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

/* Transiciones de la tabla e interacciones */
.avatar-sm img,
.avatar-xl img {
    transition: transform 0.3s ease;
}

.cursor-pointer:hover img {
    transform: scale(1.1);
}


table tbody tr:hover .avatar-sm img {
    transform: scale(1.1);
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}

.cursor-pointer {
    cursor: pointer;
}

.modal-content {
    border-radius: 15px;
}

/* Estilos de Perfil del Modal (Los que hicimos anteriormente) */
.profile-modal-radius {
    border-radius: 20px;
}

.profile-avatar {
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 10;
}

.profile-avatar:hover {
    transform: scale(1.08) translateY(-5px);
}

.profile-img {
    transition: filter 0.3s ease;
}

.profile-avatar:hover .profile-img {
    filter: brightness(1.1);
}

.info-pill {
    transition: all 0.2s ease;
}

.info-pill:hover {
    background-color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
}

.parentesco-card {
    transition: all 0.3s ease;
    box-shadow: 0 0.125rem 0.25rem rgba(25, 135, 84, 0.05);
}

.parentesco-card:hover {
    transform: translateY(-4px);
    border-color: var(--bs-success) !important;
    box-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.15);
}

.parentesco-badge {
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.parentesco-card:hover .parentesco-badge {
    transform: scale(1.05);
    background-color: var(--green-800) !important;
}
</style>
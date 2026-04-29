<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header"
            style="border-left: 6px solid #F4B324;">
            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                    style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                    <i class="fas fa-user-edit fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                        Gestión Global de Usuarios
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración de credenciales y perfiles de acceso.
                    </p>
                </div>
            </div>
        </header>
        <div class="row mb-4"
            v-if="estadisticas && estadisticas.totales_por_rol && Object.keys(estadisticas.totales_por_rol).length > 0">
            <div class="col-12 col-md-3 mb-3 mb-md-0" v-for="(total, rol) in estadisticas.totales_por_rol" :key="rol">
                <div class="card shadow-sm border-0 h-100"
                    style="background-color: white; border-radius: 15px; border-left: 5px solid #F4B324 !important;">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-muted fw-bold text-uppercase mb-1"
                                style="letter-spacing: 1px; font-size: 0.8rem;">Rol: {{ rol }}</h6>
                            <h2 class="fw-bold mb-0" style="color: #1D2A68;">{{ total }}</h2>
                        </div>
                        <div style="color: #1D2A68; opacity: 0.8;">
                            <i class="fas fa-user-tag fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" id="filtroMenores" v-model="filtroMenores20"
                                @change="filtrarYBuscar" :disabled="filtroMayores20">
                            <label class="form-check-label fw-bold text-secondary ms-2" for="filtroMenores">
                                Filtrar menores de 20 años
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" id="filtroMayores" v-model="filtroMayores20"
                                @change="filtrarYMayoBuscar" :disabled="filtroMenores20">
                            <label class="form-check-label fw-bold text-secondary ms-2" for="filtroMayores">
                                Filtrar mayores de 20 años
                            </label>
                        </div>
                    </div>
                </div>

                <div v-if="filtroMenores20 && totalPendientesMasivo.length > 0"
                    class="mt-4 p-3 bg-success-subtle border border-success rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-success mb-1"><i class="fas fa-users me-2"></i>Registro Masivo
                                Disponible</h6>
                            <small class="text-success">Se encontraron {{ totalPendientesMasivo.length }} personas
                                menores de 20 años sin usuario en toda la base de datos.</small>
                        </div>
                        <button class="btn btn-success shadow-sm" @click="ejecutarRegistroMasivo"
                            :disabled="procesandoMasivo">
                            <i class="fas fa-cogs me-2" :class="{ 'fa-spin': procesandoMasivo }"></i>
                            {{ procesandoMasivo ? 'Procesando...' : 'Crear Usuarios Masivamente' }}
                        </button>

                    </div>

                    <div class="progress mt-3" v-if="procesandoMasivo" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success fw-bold"
                            role="progressbar" :style="{ width: progresoMasivo + '%' }">
                            {{ progresoMasivo }}%
                        </div>
                    </div>

                    <div v-if="erroresMasivos.length > 0" class="alert alert-danger mt-3 mb-0 py-2 small">
                        <strong><i class="fas fa-exclamation-triangle me-1"></i> Errores en las siguientes
                            cédulas:</strong>
                        {{ erroresMasivos.join(', ') }}
                    </div>
                </div>
                <div v-if="filtroMenores20 && totalPendientesMasivo.length === 0"
                    class="alert alert-success bg-success-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                    role="alert">
                    <i class="fas fa-user-clock fs-4 text-success me-3"></i>
                    <div class="small text-dark">
                        <strong>¡Al día!</strong> Ya no hay registros de personas menores de 20 años bajo estos
                        criterios.
                    </div>
                </div>
                <div v-if="filtroMayores20 && totalPendientesMasivoMayores20.length > 0"
                    class="mt-4 p-3 bg-success-subtle border border-success rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-success mb-1"><i class="fas fa-users me-2"></i>Registro Masivo
                                Disponible</h6>
                            <small class="text-success">Se encontraron {{ totalPendientesMasivoMayores20.length }}
                                personas
                                mayores de 20 años sin usuario en toda la base de datos.</small>
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
                                Usuario</th>
                            <th class="py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Rol</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Estado</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha de Creación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Fecha de Modificación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.personID">
                            <td class="ps-4 fw-bold text-secondary" v-if="user.id_usuario">{{ user.id_usuario }}</td>
                            <td class="ps-4 text-muted small" v-else>Sin asignar</td>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0; background-color: rgba(244, 179, 36, 0.15); border: 2px solid #F4B324;">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div v-if="user.id_usuario === idusuariolog">
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold text-dark">Yo</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                        <span v-if="user.sexo === 'M' || user.sexo === 'Masculino'" title="Masculino">
                                            <i class="fas fa-mars fs-6" style="color: #3b82f6;"></i>
                                        </span>
                                        <span v-else-if="user.sexo === 'F' || user.sexo === 'Femenino'"
                                            title="Femenino">
                                            <i class="fas fa-venus fs-6" style="color: #ec4899;"></i>
                                        </span>
                                        <span v-else title="Otro">
                                            <i class="fas fa-genderless fs-6 text-secondary"></i> {{ user.sexo }}
                                        </span>
                                    </div>
                                    <div v-else>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{
                                            user.cedula }}</div>
                                        <div class="fw-bold text-dark">{{ user.nombres }} {{ user.apellidos }}</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{
                                            calcularEdad(user.fecha_nacimiento) }} años</div>
                                        <span v-if="user.sexo === 'M' || user.sexo === 'Masculino'" title="Masculino">
                                            <i class="fas fa-mars fs-6" style="color: #3b82f6;"></i>
                                        </span>
                                        <span v-else-if="user.sexo === 'F' || user.sexo === 'Femenino'"
                                            title="Femenino">
                                            <i class="fas fa-venus fs-6" style="color: #ec4899;"></i>
                                        </span>
                                        <span v-else title="Otro">
                                            <i class="fas fa-genderless fs-6 text-secondary"></i> {{ user.sexo }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td v-if="user.username"><span class="fw-bold" style="color: #1D2A68;">{{ user.username
                            }}</span></td>
                            <td class="text-muted small" v-else>Sin usuario</td>
                            <td v-if="user.nombre_rol"><span class="badge text-dark border"
                                    style="background-color: rgba(244, 179, 36, 0.2);">{{ user.nombre_rol }}</span></td>
                            <td class="text-muted small" v-else>Sin rol</td>
                            <td class="text-center">
                                <span v-if="user.estado == 1"
                                    class="badge bg-success-subtle text-success border border-success px-3">Activo</span>
                                <span v-else-if="user.id_usuario"
                                    class="badge bg-danger-subtle text-danger border border-danger px-3">Inactivo</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-calendar-plus me-1" style="color: #F4B324;"></i> {{ user.created_at
                                    }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-edit me-1" style="color: #1D2A68;"></i> {{ user.updated_at }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light text-primary border shadow-sm"
                                        @click="abrirModalCrear(user)" v-if="!user.id_usuario && user.id_usuario !== idusuariolog"
                                        title="Asignar y crear usuario">
                                        <i class="fas fa-user-plus me-1"></i> Crear Usuario
                                    </button>
                                    <button class="btn btn-sm btn-light border shadow-sm ms-1" style="color: #1D2A68;"
                                        @click="abrirModalEditar(user)" v-if="user.id_usuario && user.id_usuario !== idusuariolog"
                                        title="Editar Rol de Usuario">
                                        <i class="fas fa-user-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-light border shadow-sm ms-1" style="color: #F4B324;"
                                        @click="resetearClave(user.id_usuario, user.cedula, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.id_usuario" title="Resetear contraseña (usará la cédula)">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger border shadow-sm ms-1"
                                        @click="eliminar(user.id_usuario, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.estado == 1 && user.id_usuario && user.id_usuario !== idusuariolog" title="Inhabilitar esta persona">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success border shadow-sm ms-1"
                                        @click="habilitar(user.id_usuario, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.estado == 0 && user.id_usuario && user.id_usuario !== idusuariolog"
                                        title="Habilitar esta persona nuevamente">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #F4B324;"></i>
                                Cargando registros...
                            </td>
                        </tr>
                        <tr v-if="!cargando && objetoList.length === 0">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-search fs-2 mb-2 d-block" style="color: #1D2A68; opacity: 0.5;"></i>
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

        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg"
                    style="border-radius: 15px; border-top: 5px solid #1D2A68 !important;">
                    <div class="modal-header border-0 bg-light">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-user-plus me-2" style="color: #F4B324;"></i>Crear Cuenta de Usuario
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            id="btnCloseModalUser"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(244, 179, 36, 0.15);" role="alert">
                            <i class="fas fa-lightbulb fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small" style="color: #1D2A68;">
                                <strong>Guía de Registro:</strong><br>
                                El usuario de la persona por defecto será su número de cédula. Solo debes asignarle un
                                rol y confirmar para crear su cuenta.
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <img :src="personaSeleccionada.foto" class="rounded-circle shadow"
                                style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #F4B324;">
                            <h5 class="mt-3 fw-bold" style="color: #1D2A68;">{{ personaSeleccionada.nombres }} {{
                                personaSeleccionada.apellidos }}</h5>
                            <span class="badge" style="background-color: #1D2A68;">C.I. {{ personaSeleccionada.cedula
                            }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Nombre de Usuario (Automático)</label>
                            <input type="text" class="form-control bg-light border-0 shadow-sm"
                                v-model="objetoData.username" readonly style="color: #1D2A68; font-weight: 500;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">Rol del Sistema</label>
                            <select class="form-select border shadow-sm" v-model="objetoData.id_rol"
                                style="border-color: rgba(29, 42, 104, 0.2);">
                                <option value="" disabled>Seleccione un rol...</option>
                                <option v-for="rol in rolesDisponibles" :key="rol.id_rol" :value="rol.id_rol">
                                    {{ rol.nombre }}
                                </option>
                            </select>
                        </div>

                        <button class="btn w-100 py-2 fw-bold border-0 shadow-sm"
                            style="background-color: #1D2A68; color: white;" @click="guardarUsuario"
                            :disabled="!objetoData.id_rol">
                            <i class="fas fa-save me-2" style="color: #F4B324;"></i> Confirmar y Crear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg"
                    style="border-radius: 15px; border-top: 5px solid #F4B324 !important;">
                    <div class="modal-header border-0 bg-light">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-user-edit me-2" style="color: #F4B324;"></i>Editar Rol de Usuario
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            id="btnCloseModalEditUser"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="text-center mb-4">
                            <img :src="personaSeleccionada.foto" class="rounded-circle shadow"
                                style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #1D2A68;">
                            <h5 class="mt-3 fw-bold" style="color: #1D2A68;">{{ personaSeleccionada.nombres }} {{
                                personaSeleccionada.apellidos }}</h5>
                            <span class="badge" style="background-color: #1D2A68;">C.I. {{ personaSeleccionada.cedula
                            }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Nombre de Usuario (Bloqueado)</label>
                            <input type="text" class="form-control bg-light text-muted border-0 shadow-sm"
                                v-model="objetoEdit.username" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">Actualizar Rol del Sistema</label>
                            <select class="form-select border shadow-sm" v-model="objetoEdit.id_rol"
                                style="border-color: rgba(29, 42, 104, 0.2);">
                                <option value="" disabled>Seleccione un rol...</option>
                                <option v-for="rol in rolesDisponibles" :key="rol.id_rol" :value="rol.id_rol">
                                    {{ rol.nombre }}
                                </option>
                            </select>
                        </div>

                        <button class="btn w-100 py-2 fw-bold shadow-sm"
                            style="background-color: #F4B324; color: #1D2A68; border: none;" @click="actualizarUsuario"
                            :disabled="!objetoEdit.id_rol">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar Rol
                        </button>
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
import { getMe } from "@/assets/js/auth";

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            filtroMenores20: false,
            filtroMayores20: false,
            timeoutBusqueda: null,
            objetoList: [],
            objetoRolesList: [],
            rolesDisponibles: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            refreshKey: Date.now(),

            // Variables para Modal individual
            personaSeleccionada: {},
            objetoData: {
                id_persona: "",
                id_rol: "",
                username: "",
            },
            objetoEdit: { // Para Editar
                id_usuario: "",
                id_persona: "",
                id_rol: "",
                username: ""
            },

            // Variables para registro masivo
            procesandoMasivo: false,
            progresoMasivo: 0,
            erroresMasivos: [],
            totalPendientesMasivo: [],
            totalPendientesMasivoMayores20: [],
            estadisticas: {
                total_general: 0,
                totales_por_rol: {}
            },
            idusuariolog: 0,
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
        const me = await getMe();
        this.idusuariolog = me.id_usuario;
        await Promise.all([this.getData(), this.GetObjetoList()]);
    },
    methods: {
        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },
        async obtenerPendientesMasivo() {
            if (!this.filtroMenores20) {
                this.totalPendientesMasivo = [];
                return;
            }
            try {
                const res = await API.get(`${this.baseUrl}/pendientes_masivo`);
                this.totalPendientesMasivo = res.data?.data || [];
            } catch (error) {
                console.error("Error obteniendo la lista global de masivos", error);
            }
        },
        async obtenerPendientesMasivoMayores20() {
            if (!this.filtroMayores20) {
                this.totalPendientesMasivoMayores20 = [];
                return;
            }
            try {
                const res = await API.get(`${this.baseUrl}/pendientes_mayores_masivo`);
                this.totalPendientesMasivoMayores20 = res.data?.data || [];
            } catch (error) {
                console.error("Error obteniendo la lista global de masivos mayores de 20", error);
            }
        },

        filtrarYBuscar() {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
                this.obtenerPendientesMasivo(); // <-- Llamada nueva
            }, 500)
        },
        filtrarYMayoBuscar() {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
                this.obtenerPendientesMasivoMayores20(); // <-- Llamada nueva para mayores de 20
            }, 500)
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
                id_persona: user.personID,
                username: user.cedula, // username = cedula
                id_rol: ""
            };
            this.filtrarRolesPorEdad(user.fecha_nacimiento);
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
                id_usuario: user.id_usuario,
                id_persona: user.personID,
                id_rol: user.id_rol || "", // Se asegura que tome el ID del rol actual
                username: user.username
            };

            // Volvemos a filtrar los roles para que no pueda asignarle algo indebido
            this.filtrarRolesPorEdad(user.fecha_nacimiento);

            const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        },
        filtrarRolesPorEdad(fechaNacimiento) {
            const edad = this.calcularEdad(fechaNacimiento);

            // Suponiendo que el rol de estudiante tiene la palabra "estudiante" en su nombre
            if (edad > 20) {
                // Si es mayor a 20, mostramos todos los roles que NO sean estudiante
                this.rolesDisponibles = this.objetoRolesList.filter(
                    rol => !rol.nombre.toLowerCase().includes('estudiante')
                );
            } else {
                // Si tiene 20 o menos, mostramos SOLO el rol de estudiante
                this.rolesDisponibles = this.objetoRolesList.filter(
                    rol => rol.nombre.toLowerCase().includes('estudiante')
                );
            }
        },

        async guardarUsuario() {
            try {
                // LLamada al endpoint individual
                const response = await API.post(`${this.baseUrl}/usuarios/store`, this.objetoData);
                mostraralertas2("Usuario creado con éxito", "success");
                document.getElementById('btnCloseModalUser').click();
                this.getData();
                if (this.filtroMayores20) {
                    this.obtenerPendientesMasivoMayores20(); // <-- Llamada nueva para mayores de 20
                } else {
                    this.obtenerPendientesMasivo(); // <-- Llamada nueva para menores de 20
                }
            } catch (error) {
                mostraralertas2("Error al crear usuario", "error");
            }
        },
        async actualizarUsuario() {
            try {
                // Asegúrate de que esta ruta coincida con la que tienes en tu Laravel
                const response = await API.put(`${this.baseUrl}/usuarios/update/${this.objetoEdit.id_usuario}`, {
                    id_persona: this.objetoEdit.id_persona,
                    id_rol: this.objetoEdit.id_rol,
                    username: this.objetoEdit.username
                });

                mostraralertas2("Rol actualizado correctamente", "success");
                document.getElementById('btnCloseModalEditUser').click();
                this.getData();
            } catch (error) {
                mostraralertas2("Error al actualizar el usuario", "error");
                console.error(error);
            }
        },

        async ejecutarRegistroMasivo() {
            this.procesandoMasivo = true;
            this.progresoMasivo = 0;
            this.erroresMasivos = [];

            const chunk_size = 5;
            const dataToProcess = this.totalPendientesMasivo;

            const totalChunks = Math.ceil(dataToProcess.length / chunk_size);

            for (let i = 0; i < totalChunks; i++) {
                const chunk = dataToProcess.slice(i * chunk_size, i * chunk_size + chunk_size);
                try {
                    const res = await API.post(`${this.baseUrl}/usuarios/store_masivo`, { personas: chunk });
                    if (res.data.errores && res.data.errores.length > 0) {
                        this.erroresMasivos.push(...res.data.errores);
                    }
                } catch (error) {
                    console.error("Error en bloque masivo", error);
                }

                this.progresoMasivo = Math.round(((i + 1) / totalChunks) * 100);
            }

            setTimeout(() => {
                this.procesandoMasivo = false;
                if (this.erroresMasivos.length === 0) {
                    mostraralertas2("Registro masivo completado sin errores", "success");
                } else {
                    mostraralertas2("Proceso finalizado, con errores en algunas cédulas", "warning");
                }
                this.getData(); // Refrescamos la tabla
                this.obtenerPendientesMasivo(); // Refrescamos el banner verde (debería desaparecer si ya no hay)
            }, 1000);
        },

        async GetObjetoList() {
            try {
                const response = await API.get(`${this.baseUrl}/roleshabilitados`); // Usa tu endpoint Roleshabilitados
                this.objetoRolesList = response.data?.data || [];
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
                let filtromayomeno;
                if (this.filtroMenores20) {
                    filtromayomeno = 'under_20';
                } else if (this.filtroMayores20) {
                    filtromayomeno = 'over_20';
                }
                const response = await API.get(`${this.baseUrl}/usuarios`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda,
                        filter_age: filtromayomeno
                    }
                });
                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.totalPersonas = pagination.total || 0;
                const stats = response.data?.estadisticas || { total_general: 0, totales_por_rol: {} };
                this.estadisticas = stats;
                this.objetoList = data;
            } catch (error) {
                this.objetoList = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.cargando = false;
            }
        },
        async resetearClave(id_usuario, cedula, nombreCompleto) {
            try {
                // Ejecutamos la alerta y esperamos el resultado (true si confirmó, false si canceló)
                const seReseteo = await confimarreseteo(
                    `${this.baseUrl}/resetear_clave/`,
                    id_usuario,
                    cedula,
                    'Resetear Clave',
                    `¿Realmente desea resetear la clave de ${nombreCompleto}? (Se usará su número de cédula)`
                );

                // Si la operación fue un éxito, actualizamos la tabla
                if (seReseteo) {
                    setTimeout(() => { this.getData(); }, 500);
                }
            } catch (error) {
                console.error("Error al resetear clave:", error);
            }
        },
        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_usuario/`,
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
                    `${this.baseUrl}/habilitar_usuario/`,
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
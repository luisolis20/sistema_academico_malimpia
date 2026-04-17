<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div
                    class="header-icon shadow-sm bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3">
                    <i class="fas fa-users-cog fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--green-900); font-family: 'Fraunces', serif;">
                        Gestión de Familias
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración y control de las familias asignadas a los representantes.
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
                                placeholder="Buscar por cédula... (Solo números)">
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
                            <th class="ps-4">Persona</th>
                            <th>Familias</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.personID">
                            <td class="ps-4 fw-bold text-secondary">{{ user.personID }}</td>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light text-success rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0;">
                                        <img :src="getPhotoUrl(user.personID)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1"><i class="far fa-id-card me-1"></i>{{ user.cedula }}</div>
                                        <div class="fw-bold text-dark">{{ user.nombres }} {{ user.apellidos }}</div>
                                         <div class="text-muted small" v-if="user.fecha_nacimiento">Edad: {{ calcularEdad(user.fecha_nacimiento) }} años</div>
                                    </div>
                                </div>
                            </td>
                    
                            <td>
                                <div v-if="user.familiares && user.familiares.length > 0" class="d-flex flex-wrap gap-2">
                                    <div v-for="(fam, index) in user.familiares" :key="index" 
                                         class="avatar-sm rounded-circle overflow-hidden shadow-sm border border-2 border-white cursor-pointer"
                                         style="width: 40px; height: 40px;"
                                         @click="abrirModalDetalleFamiliar(fam, user)"
                                         title="Ver detalle del familiar">
                                        <img :src="getPhotoUrl(fam.id_estudiante)" @error="handleImageError" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                </div>
                                <div v-else>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-exclamation-circle me-1"></i> Sin asignar familia
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <button v-if="user.familiares && user.familiares.length > 0"
                                        class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" 
                                        @click="abrirModalActualizar(user)">
                                    <i class="fas fa-user-edit me-1"></i> Actualizar Familia
                                </button>
                                <button v-else
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" 
                                        @click="abrirModalAsignar(user)">
                                    <i class="fas fa-users-cog me-1"></i> Asignar Familia
                                </button>
                            </td>
                        </tr>
                        
                        <tr v-if="cargando">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 text-primary mb-2 d-block"></i> Cargando...
                            </td>
                        </tr>
                        <tr v-if="!cargando && objetoList.length === 0">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-search fs-2 text-secondary mb-2 d-block"></i>
                                No se encontraron registros.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3" v-if="lastPage > 1">
                <span class="text-muted small">Página <strong>{{ currentPage }}</strong> de <strong>{{ lastPage }}</strong></span>
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                            <button class="page-link" @click="cambiarPagina(currentPage - 1)">Anterior</button>
                        </li>
                        <li class="page-item" v-for="page in paginasMostradas" :key="page" :class="{ active: page === currentPage }">
                            <button class="page-link" @click="cambiarPagina(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link" @click="cambiarPagina(currentPage + 1)">Siguiente</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="modalAsignarFamilia" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background: var(--green-800);">
                        <h5 class="modal-title fw-bold">
                            <i class="fas" :class="isUpdating ? 'fa-user-edit' : 'fa-user-plus'"></i> 
                            {{ isUpdating ? 'Actualizar' : 'Asignar' }} Familia a {{ representanteActual?.nombres }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="alert alert-success bg-success-subtle border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 text-success me-3"></i>
                            <div class="small text-dark">
                                <strong>Guía de Registro:</strong><br>
                                Ingresa la cédula del familiar que deseas asignar. Si el familiar ya existe en el sistema, 
                                aparecerá su información para que puedas seleccionar el parentesco y añadirlo a la lista. 
                                Puedes agregar varios familiares antes de guardar los cambios.
                            </div>
                        </div>
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-8">
                                <label class="fw-bold mb-1">Cédula del Familiar a buscar:</label>
                                <input type="text" v-model="busquedaFamiliar" class="form-control" placeholder="Ingrese número de cédula">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100 fw-bold" @click="buscarFamiliar" :disabled="!busquedaFamiliar">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4" v-if="familiarEncontrado">
                            <div class="card-body">
                                <h6 class="fw-bold text-success mb-3">Persona Encontrada:</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-muted">Nombres y Apellidos</label>
                                        <input type="text" class="form-control bg-white" :value="familiarEncontrado.nombres + ' ' + familiarEncontrado.apellidos" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Cédula</label>
                                        <input type="text" class="form-control bg-white" :value="familiarEncontrado.cedula" disabled>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="small text-muted fw-bold text-dark">Parentesco</label>
                                        <select class="form-select border-primary" v-model="parentescoSeleccionado">
                                            <option value="" disabled>Seleccione parentesco...</option>
                                            <option value="Hijo/a">Hijo/a</option>
                                            <option value="Hermano/a">Hermano/a</option>
                                            <option value="Sobrino/a">Sobrino/a</option>
                                            <option value="Nieto/a">Nieto/a</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn btn-success w-100 fw-bold" @click="agregarFamiliarATabla" :disabled="!parentescoSeleccionado">
                                            <i class="fas fa-plus me-1"></i> Añadir a Tabla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mt-2"><i class="fas fa-list me-2 text-primary"></i>Familiares en la lista:</h6>
                        <div class="table-responsive bg-white rounded shadow-sm">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombres</th>
                                        <th>Parentesco</th>
                                        <th class="text-center">Remover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fam, index) in familiaresAgregados" :key="index">
                                        <td>{{ fam.cedula }}</td>
                                        <td>{{ fam.nombres }} {{ fam.apellidos }}</td>
                                        <td><span class="badge bg-info text-dark">{{ fam.parentesco }}</span></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger rounded-circle" @click="familiaresAgregados.splice(index, 1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="familiaresAgregados.length === 0">
                                        <td colspan="4" class="text-center text-muted py-3">Aún no has añadido familiares a la lista.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success fw-bold px-4" @click="guardarFamiliaAsignada" :disabled="familiaresAgregados.length === 0 && !isUpdating">
                            <i class="fas fa-save me-2"></i> {{ isUpdating ? 'Guardar Cambios' : 'Asignar Familia Definitivamente' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

         <div class="modal fade" id="modalDetalleFamiliar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0 justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-4">
                        <div class="avatar-xl mx-auto mb-3 border border-3 border-success rounded-circle overflow-hidden shadow" style="width: 100px; height: 100px;">
                            <img :src="detalleFamiliarData ? getPhotoUrl(detalleFamiliarData.familiar.id_estudiante) : ''" @error="handleImageError" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <h5 class="fw-bold text-dark mb-0">{{ detalleFamiliarData?.familiar.nombres }} {{ detalleFamiliarData?.familiar.apellidos }}</h5>
                        <p class="text-muted small mb-2">{{ detalleFamiliarData?.familiar.cedula }}</p>
                        <div class="bg-light p-2 rounded-3 mt-3 border">
                            <p class="mb-1 text-secondary small">Parentesco con {{ detalleFamiliarData?.representante.nombres }} {{ detalleFamiliarData?.representante.apellidos }}</p>
                            <span class="badge bg-success fs-6">{{ detalleFamiliarData?.familiar.parentesco }}</span>
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
    border-left: 5px solid #198754; /* Cambia al color de tu var(--green-800) si lo prefieres */
    transition: all 0.3s ease;
}
.custom-header:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
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
    background-color: #198754 !important; /* Verde success de Bootstrap */
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
.avatar-sm img, .avatar-xl img {
    transition: transform 0.3s ease;
}
.cursor-pointer:hover img {
    transform: scale(1.1);
}
.cursor-pointer {
    cursor: pointer;
}
.modal-content {
    border-radius: 15px;
}

/* Estilos de Perfil del Modal (Los que hicimos anteriormente) */
.profile-modal-radius { border-radius: 20px; }
.profile-avatar { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; }
.profile-avatar:hover { transform: scale(1.08) translateY(-5px); }
.profile-img { transition: filter 0.3s ease; }
.profile-avatar:hover .profile-img { filter: brightness(1.1); }
.info-pill { transition: all 0.2s ease; }
.info-pill:hover { background-color: #fff !important; transform: translateY(-2px); box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important; }
.parentesco-card { transition: all 0.3s ease; box-shadow: 0 0.125rem 0.25rem rgba(25, 135, 84, 0.05); }
.parentesco-card:hover { transform: translateY(-4px); border-color: var(--bs-success) !important; box-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.15); }
.parentesco-badge { transition: transform 0.3s ease, background-color 0.3s ease; }
.parentesco-card:hover .parentesco-badge { transform: scale(1.05); background-color: var(--green-800) !important; }
</style>
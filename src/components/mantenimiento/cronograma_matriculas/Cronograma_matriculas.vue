<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div
                    class="header-icon shadow-sm bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3">
                    <i class="fas fa-layer-group fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--green-900); font-family: 'Fraunces', serif;">
                        Gestión del cronograma de matrículas
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración y estructuración del cronograma de matrículas para cada nivel académico
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">

                <button
                    class="btn btn-success btn-lg shadow-sm rounded-pill d-flex align-items-center interactive-btn px-4"
                    data-bs-toggle="modal" data-bs-target="#modalCrear" @click="prepararCreacion">
                    <i class="fas fa-plus-circle me-2"></i>
                    <span class="fw-bold fs-6">Crear Cronograma</span>
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
                            <th>Nivel </th>
                            <th>Especialidad</th>
                            <th class="text-center">Periodo</th>
                            <th class="text-center">Matriculas Abiertas</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fin</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_nivel + '-' + user.id_especialidad">
                            <td class="ps-4 fw-bold text-secondary" v-if="user.id_cronograma">{{ user.id_cronograma }}</td>
                            <td class="ps-4 fw-bold text-secondary" v-else>
                                <span class="text-muted small"><i class="fas fa-exclamation-circle text-warning"></i> Sin asignar</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span>{{ user.nivel_academico }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span>{{ user.especialidad }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ user.periodo_lectivo }}</span>
                            </td>
                            <td class="text-center" v-if="user.matriculas_abiertas == 1">
                                <span class="badge bg-success-subtle text-success border border-success px-3">Si</span>
                            </td>
                            <td class="text-center" v-else>
                                <span class="badge bg-danger-subtle text-danger border border-danger px-3">No</span>
                            </td>
                            <td class="text-center">
                                <span v-if="user.fecha_inicio" class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-calendar-plus text-success me-1"></i> {{ user.fecha_inicio }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td class="text-center">
                                <span v-if="user.fecha_fin" class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-edit text-primary me-1"></i> {{ user.fecha_fin }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button v-if="user.id_cronograma" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#modalEditar" @click="prepararEdicion(user)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 text-light mb-3 d-block"></i>
                                No se encontraron niveles académicos.
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 text-primary mb-2 d-block"></i>
                                Cargando información...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3"
                v-if="lastPage > 1">
                <span class="text-muted small">Página <strong>{{ currentPage }}</strong> de <strong>{{ lastPage }}</strong></span>
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                            <button class="page-link" @click="cambiarPagina(currentPage - 1)" :disabled="currentPage <= 1">Anterior</button>
                        </li>
                        <li class="page-item" v-for="page in paginasMostradas" :key="page" :class="{ active: page === currentPage }">
                            <button class="page-link" @click="cambiarPagina(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link" @click="cambiarPagina(currentPage + 1)" :disabled="currentPage >= lastPage">Siguiente</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success-subtle text-success">
                        <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus me-2"></i> Crear Cronograma Masivo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7 border-end">
                                <h6 class="fw-bold mb-3 text-secondary">Seleccione los niveles:</h6>
                                
                                <div v-if="cargandoNiveles" class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin text-success"></i> Cargando niveles...
                                </div>

                                <div class="list-group" v-else style="max-height: 400px; overflow-y: auto;">
                                    <label class="list-group-item d-flex justify-content-between align-items-center list-group-item-action" 
                                           v-for="nv in listaTodosNiveles" :key="'modal-'+nv.id_nivel+'-'+nv.id_especialidad"
                                           :class="{'bg-light': nv.id_cronograma}">
                                        
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" type="checkbox" 
                                                   :value="nv" v-model="nivelesSeleccionados" 
                                                   :disabled="nv.id_cronograma != null">
                                            <div>
                                                <span class="fw-bold" :class="{'text-muted': nv.id_cronograma}">{{ nv.nivel_academico }}</span>
                                                <small class="d-block text-muted">{{ nv.especialidad }}</small>
                                            </div>
                                        </div>
                                        
                                        <span v-if="nv.id_cronograma" class="badge bg-secondary">Ya asignado</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-5 ps-4">
                                <h6 class="fw-bold mb-3 text-secondary">Configurar Fechas:</h6>
                                
                                <div class="alert alert-info py-2" v-if="nivelesSeleccionados.length === 0">
                                    <small><i class="fas fa-info-circle"></i> Selecciona al menos un nivel a la izquierda para habilitar las fechas.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Fecha y Hora de Inicio</label>
                                    <input type="datetime-local" class="form-control" v-model="formCrear.fecha_inicio" 
                                           :disabled="nivelesSeleccionados.length === 0">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted">Fecha y Hora de Fin</label>
                                    <input type="datetime-local" class="form-control" v-model="formCrear.fecha_fin"
                                           :disabled="nivelesSeleccionados.length === 0">
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-success" :disabled="nivelesSeleccionados.length === 0 || !formCrear.fecha_inicio || !formCrear.fecha_fin"
                                            @click="guardarCronogramaMasivo">
                                        <i class="fas fa-save me-2"></i> Crear para {{ nivelesSeleccionados.length }} nivel(es)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary-subtle text-primary">
                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Editar Cronograma</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalEditar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Nivel y Especialidad</label>
                            <input type="text" class="form-control bg-light" :value="formEditar.nombre_mostrar" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha y Hora de Inicio</label>
                            <input type="datetime-local" class="form-control" v-model="formEditar.fecha_inicio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha y Hora de Fin</label>
                            <input type="datetime-local" class="form-control" v-model="formEditar.fecha_fin">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="actualizarCronograma">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios"
import { confimar, confimarhabi, mostraralertas2 } from "@/assets/js/funciones/functions"; // Asumo que esto usa SweetAlert2 u otra librería

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
            
            // --- Nuevas variables para los modales ---
            listaTodosNiveles: [], // Todos los niveles sin paginación para el modal
            cargandoNiveles: false,
            nivelesSeleccionados: [],
            
            formCrear: {
                fecha_inicio: '',
                fecha_fin: ''
            },
            
            formEditar: {
                id_cronograma: null,
                id_nivel: null,
                id_especialidad: null,
                id_periodo: null,
                nombre_mostrar: '',
                fecha_inicio: '',
                fecha_fin: ''
            }
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
         cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },

        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/cronograma_matriculas`, {
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
            } finally {
                this.cargando = false;
            }
        },

        // ==========================================
        // MÉTODOS PARA EL MODAL DE CREAR
        // ==========================================
        async prepararCreacion() {
            // Limpiamos formularios
            this.nivelesSeleccionados = [];
            this.formCrear.fecha_inicio = '';
            this.formCrear.fecha_fin = '';
            this.cargandoNiveles = true;

            try {
                // Truco: Llamamos al mismo endpoint pero le pedimos 1000 registros para que no pague 
                // y traiga todos los niveles disponibles para el listado del lado izquierdo del modal.
                const response = await API.get(`${this.baseUrl}/cronograma_matriculas`, {
                    params: { per_page: 1000 }
                });
                this.listaTodosNiveles = response.data?.data || [];
            } catch (error) {
                console.error("Error al cargar lista completa de niveles", error);
            } finally {
                this.cargandoNiveles = false;
            }
        },

        async guardarCronogramaMasivo() {
            try {
                // Preparamos el payload con los IDs que necesita la DB
                const payload = {
                    fecha_inicio: this.formCrear.fecha_inicio,
                    fecha_fin: this.formCrear.fecha_fin,
                    // Extraemos solo los ids (nivel, especialidad y periodo) de los seleccionados
                    niveles: this.nivelesSeleccionados.map(n => ({
                        id_nivel: n.id_nivel,
                        id_especialidad: n.id_especialidad,
                        id_periodo: n.id_periodo
                    }))
                };

                // Petición al backend (Debes crear este endpoint en Laravel)
                await API.post(`${this.baseUrl}/crearcronograma_matriculas`, payload);
                
                mostraralertas2('Cronogramas creados correctamente', "success");

                
                // Cerrar modal usando Bootstrap (opcional, si usas jquery o refs)
                document.querySelector('#modalCrear .btn-close').click();
                
                // Refrescamos la tabla
                this.getData();
            } catch (error) {
                console.error("Error al guardar", error);
                mostraralertas2('error', 'Ocurrió un error al guardar los cronogramas');
            }
        },

        // ==========================================
        // MÉTODOS PARA EL MODAL DE EDITAR
        // ==========================================
        prepararEdicion(item) {
            // Llenamos el formulario de edición con los datos de la fila
            this.formEditar.id_cronograma = item.id_cronograma;
            this.formEditar.nombre_mostrar = `${item.nivel_academico} - ${item.especialidad}`;
            this.formEditar.id_nivel = item.id_nivel;
            this.formEditar.id_especialidad = item.id_especialidad;
            this.formEditar.id_periodo = item.id_periodo;
            // Formatear la fecha para que el input datetime-local lo acepte (ej: 2026-04-15T10:30)
            this.formEditar.fecha_inicio = item.fecha_inicio ? item.fecha_inicio.replace(' ', 'T') : '';
            this.formEditar.fecha_fin = item.fecha_fin ? item.fecha_fin.replace(' ', 'T') : '';
            
        },

        async actualizarCronograma() {
            try {
                const payload = {
                    id_nivel: this.formEditar.id_nivel,
                    id_especialidad: this.formEditar.id_especialidad,
                    id_periodo: this.formEditar.id_periodo,
                    fecha_inicio: this.formEditar.fecha_inicio,
                    fecha_fin: this.formEditar.fecha_fin

                };

                await API.put(`${this.baseUrl}/cronograma_matriculas/${this.formEditar.id_cronograma}`, payload);
                
                
                mostraralertas2("ronograma actualizado correctamente", "success");
                document.getElementById('closeModalEditar').click();
                this.getData();
            } catch (error) {
                console.error("Error al actualizar", error);
                mostraralertas2('error', 'Ocurrió un error al actualizar');
            }
        }
    }
}
</script>

<style scoped>
/* Estilos anteriores mantenidos... */
.custom-header {
    border-left: 5px solid #198754;
    transition: all 0.3s ease;
}
.custom-header:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
}
.header-icon {
    width: 55px;
    height: 55px;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.custom-header:hover .header-icon {
    transform: rotate(-10deg) scale(1.1);
}
.interactive-btn {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.interactive-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 12px rgba(25, 135, 84, 0.25) !important;
}
.interactive-btn:active {
    transform: translateY(1px);
}
</style>
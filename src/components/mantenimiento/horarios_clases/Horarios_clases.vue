<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div
                    class="header-icon shadow-sm bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3">
                    <i class="fas fa-book-open fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--green-900); font-family: 'Fraunces', serif;">
                        Gestión de Horarios de Clases
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración de horarios para asignaturas, cursos y docentes. Crea, edita y organiza los
                        horarios de clases de manera eficiente para garantizar una planificación académica óptima.
                    </p>
                </div>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" class="form-control border-0 shadow-none"
                        placeholder="Buscar por nombre del curso...">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe el nombre de un curso para buscar en la
                    base de datos.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: var(--green-800); color: white;">
                        <tr>
                            <th class="ps-4">Id Curso</th>
                            <th>Curso</th>
                            <th class="text-center">Periodo</th>
                            <th class="text-center">Estado Horario</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="curso in objetoList" :key="curso.id_curso">
                            <td class="ps-4 fw-bold text-secondary">{{ curso.id_curso }}</td>
                            <td>
                                <div class="d-flex align-items-center" v-if="curso.nivel === '0'">
                                    <span>{{ curso.nombre_especialidad }} "{{ curso.paralelo }}"</span>
                                </div>
                                <div class="d-flex align-items-center" v-else>
                                    <span>{{ curso.nivel }} {{ curso.nombre_especialidad }} "{{ curso.paralelo }}"</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ curso.nombre_periodo }}</span>
                            </td>
                            <td class="text-center">
                                <span v-if="curso.tiene_horario" class="badge bg-success">Horario Creado</span>
                                <span v-else class="badge bg-warning text-dark">Sin Horario</span>
                            </td>
                            <td class="text-center">
                                <button v-if="curso.tiene_horario" @click="abrirModalVista(curso)" class="btn btn-sm btn-outline-info interactive-btn me-2">
                                    <i class="fas fa-eye"></i> Ver Horario
                                </button>
                                <button v-if="curso.tiene_horario" @click="abrirModal(curso)" class="btn btn-sm btn-outline-primary interactive-btn">
                                    <i class="fas fa-edit"></i> Editar Horario
                                </button>
                                <button v-else @click="abrirModal(curso)" class="btn btn-sm btn-success interactive-btn">
                                    <i class="fas fa-calendar-plus"></i> Crear Horario
                                </button>
                            </td>
                        </tr>
                        <tr v-if="cargando">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 text-primary mb-2 d-block"></i> Cargando información...
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
        <div class="modal fade" id="horarioModal" tabindex="-1" aria-labelledby="horarioModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="horarioModalLabel" v-if="cursoSeleccionado?.nivel==='0'">
                            <i class="fas fa-calendar-alt me-2" ></i> Configurar Horario: {{ cursoSeleccionado?.nombre_especialidad }} - "{{ cursoSeleccionado?.paralelo }}"
                        </h5>
                        <h5 class="modal-title" id="horarioModalLabel" v-else>
                            <i class="fas fa-calendar-alt me-2"></i> Configurar Horario: {{ cursoSeleccionado?.nivel }} {{ cursoSeleccionado?.nombre_especialidad }} - "{{ cursoSeleccionado?.paralelo }}"
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" @click="limpiarModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7 border-end">
                                <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-eye"></i> Vista Previa del Horario</h6>
                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-bordered table-sm text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Día</th>
                                                <th>Materia</th>
                                                <th>Horario</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in horarioPreview" :key="index">
                                                <td class="align-middle fw-bold">{{ item.dia_semana }}</td>
                                                <td class="align-middle">
                                                    {{ obtenerNombreAsignatura(item.id_curso_asignatura) }}<br>
                                                    <small class="text-muted">{{ obtenerNombreDocente(item.id_curso_asignatura) }}</small>
                                                </td>
                                                <td class="align-middle">{{ item.hora_inicio }} - {{ item.hora_fin }}</td>
                                                <td class="align-middle">
                                                    <button @click="quitarFila(index)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr v-if="horarioPreview.length === 0">
                                                <td colspan="4" class="text-muted py-3">No hay asignaturas en el horario aún.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-plus-circle"></i> Asignar Materia</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Asignatura</label>
                                    <select class="form-select" v-model="formHorario.id_curso_asignatura" @change="calcularHorasRestantes">
                                        <option value="" disabled>Seleccione una asignatura...</option>
                                        <option v-for="asig in cursoSeleccionado?.asignaturas_asignadas" :key="asig.id_curso_asignatura" :value="asig.id_curso_asignatura">
                                            {{ asig.asignatura }} ({{ asig.horas_semanales }} h/sem)
                                        </option>
                                    </select>
                                    <small class="text-primary fw-bold" v-if="formHorario.id_curso_asignatura">
                                        Horas restantes por asignar: {{ horasRestantesFormateadas }}
                                    </small>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label small fw-bold">Día de la semana</label>
                                        <select class="form-select" v-model="formHorario.dia_semana">
                                            <option value="Lunes">Lunes</option>
                                            <option value="Martes">Martes</option>
                                            <option value="Miércoles">Miércoles</option>
                                            <option value="Jueves">Jueves</option>
                                            <option value="Viernes">Viernes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Hora Inicio</label>
                                        <input type="time" class="form-control" v-model="formHorario.hora_inicio">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Hora Fin</label>
                                        <input type="time" class="form-control" v-model="formHorario.hora_fin">
                                    </div>
                                </div>

                                <button class="btn btn-outline-success w-100" @click="agregarAlPreview" :disabled="!formValido">
                                    <i class="fas fa-arrow-left"></i> Añadir a la Vista Previa
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="limpiarModal">Cancelar</button>
                        <button type="button" class="btn btn-success" @click="guardarHorarioGeneral" :disabled="guardando">
                            <i class="fas fa-save"></i> {{ guardando ? 'Guardando...' : 'Guardar Horario Completo' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="vistaHorarioModal" tabindex="-1" aria-labelledby="vistaHorarioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold" id="vistaHorarioModalLabel">
                            <i class="fas fa-eye me-2"></i> Vista de Horario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4 bg-light" id="area-imprimir">
                        <div class="text-center mb-4 pb-2 border-bottom border-2 border-dark">
                            <h3 class="fw-bold mb-1" style="font-family: 'Fraunces', serif;">UNIDAD EDUCATIVA</h3>
                            <h5 class="fw-bold text-secondary mb-2" v-if="cursoSeleccionado">
                                Curso: 
                                <span v-if="cursoSeleccionado.nivel === '0'">{{ cursoSeleccionado.nombre_especialidad }} "{{ cursoSeleccionado.paralelo }}"</span>
                                <span v-else>{{ cursoSeleccionado.nivel }} {{ cursoSeleccionado.nombre_especialidad }} "{{ cursoSeleccionado.paralelo }}"</span>
                            </h5>
                            <p class="mb-0 text-muted"><strong>Periodo:</strong> {{ cursoSeleccionado?.nombre_periodo }}</p>
                        </div>

                        <div class="table-responsive bg-white shadow-sm p-3 rounded">
                            <table class="table table-bordered text-center align-middle matriz-horario">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 15%;">Hora</th>
                                        <th style="width: 17%;">Lunes</th>
                                        <th style="width: 17%;">Martes</th>
                                        <th style="width: 17%;">Miércoles</th>
                                        <th style="width: 17%;">Jueves</th>
                                        <th style="width: 17%;">Viernes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fila, index) in horarioMatriz" :key="index">
                                        <td class="fw-bold bg-light">{{ fila.rango }}</td>
                                        
                                        <td v-if="fila.isRecreo" colspan="5" class="bg-warning text-dark fw-bold fs-5" style="letter-spacing: 5px;">
                                            RECREO
                                        </td>

                                        <template v-else>
                                            <td v-for="dia in diasSemana" :key="dia">
                                                <div v-if="fila[dia]" class="p-1">
                                                    <span class="d-block fw-bold text-success">{{ fila[dia].asignatura }}</span>
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ fila[dia].docente }}</small>
                                                </div>
                                                <div v-else class="text-muted opacity-25">-</div>
                                            </td>
                                        </template>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" @click="imprimirHorario">
                            <i class="fas fa-file-pdf me-2"></i> Imprimir / PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios"
import { mostraralertas2 } from "@/assets/js/funciones/functions";
import * as bootstrap from 'bootstrap';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

export default {
    data() {
        return {
            baseUrl: "/sistma",
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            cargando: false,
            guardando: false,
            currentPage: 1,
            lastPage: 1,
            
            // Modal state
            modalInstance: null,
            modalVistaInstance: null,
            cursoSeleccionado: null,
            horarioPreview: [],
            horasRestantes: 0,
            
            formHorario: {
                id_curso_asignatura: '',
                dia_semana: 'Lunes',
                hora_inicio: '',
                hora_fin: ''
            },
            // Datos del Modal Vista Final/PDF
            diasSemana: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
            horarioMatriz: []
        }
    },
    computed: {
        formValido() {
            return this.formHorario.id_curso_asignatura && 
                   this.formHorario.dia_semana && 
                   this.formHorario.hora_inicio && 
                   this.formHorario.hora_fin;
        },
        horasRestantesFormateadas() {
            return Math.max(0, this.horasRestantes).toFixed(2);
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
        busqueda() {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
            }, 500);
        }
    },
    async mounted() {
        // Inicializar instancia de modal Bootstrap
        const modalElement = document.getElementById('horarioModal');
        if (modalElement) {
            this.modalInstance = new bootstrap.Modal(modalElement);
        }

        const modalVistaElement = document.getElementById('vistaHorarioModal');
        if (modalVistaElement) {
            this.modalVistaInstance = new bootstrap.Modal(modalVistaElement);
        }
        
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
                const response = await API.get(`${this.baseUrl}/horarios_clases`, {
                    params: { page: this.currentPage, search_query: this.busqueda }
                });
                this.objetoList = response.data?.data || [];
                const pagination = response.data?.pagination || {};
                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
            } catch (error) {
                console.error(error);
            } finally {
                this.cargando = false;
            }
        },
        // ---------- LÓGICA MODAL VISTA FINAL / PDF ----------
        abrirModalVista(curso) {
            this.cursoSeleccionado = curso;
            this.generarMatrizHorario(curso);
            this.modalVistaInstance.show();
        },

        generarMatrizHorario(curso) {
            let slotsUnicos = new Set();
            let clasesExtraidas = [];

            // 1. Extraer todas las clases y horas únicas
            if(curso.tiene_horario && curso.asignaturas_asignadas) {
                curso.asignaturas_asignadas.forEach(asig => {
                    asig.horarios.forEach(hor => {
                        // Formatear horas para quitar los segundos (Ej: 07:00:00 -> 07:00)
                        let h_ini = hor.hora_inicio.substring(0, 5);
                        let h_fin = hor.hora_fin.substring(0, 5);
                        let rango = `${h_ini} - ${h_fin}`;
                        
                        slotsUnicos.add(rango);
                        clasesExtraidas.push({
                            rango: rango,
                            dia_semana: hor.dia_semana,
                            asignatura: asig.asignatura,
                            docente: asig.nombre_docente
                        });
                    });
                });
            }

            // 2. Insertar el bloque de recreo por defecto
            slotsUnicos.add('09:30 - 10:00');

            // 3. Ordenar las horas de menor a mayor
            let slotsOrdenados = Array.from(slotsUnicos).sort((a, b) => {
                let horaA = a.split(' - ')[0];
                let horaB = b.split(' - ')[0];
                return horaA.localeCompare(horaB);
            });

            // 4. Construir la matriz de filas
            this.horarioMatriz = slotsOrdenados.map(rango => {
                let fila = { rango: rango, isRecreo: rango === '09:30 - 10:00' };
                
                this.diasSemana.forEach(dia => {
                    let clase = clasesExtraidas.find(c => c.rango === rango && c.dia_semana === dia);
                    fila[dia] = clase || null;
                });
                
                return fila;
            });
        },

        imprimirHorario() {
            // 1. Inicializar documento A4 en formato Horizontal (Landscape)
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            const anchoPagina = doc.internal.pageSize.getWidth();

            // 2. Títulos y Cabecera del PDF (Tamaños y espacios reducidos)
            doc.setFontSize(14); // Antes 16
            doc.setFont("helvetica", "bold");
            doc.text("UNIDAD EDUCATIVA", anchoPagina / 2, 15, { align: "center" }); // Subido de Y=20 a Y=15
            
            doc.setFontSize(10); // Antes 12
            let nombreCurso = this.cursoSeleccionado.nivel === '0' 
                ? `${this.cursoSeleccionado.nombre_especialidad} "${this.cursoSeleccionado.paralelo}"`
                : `${this.cursoSeleccionado.nivel} ${this.cursoSeleccionado.nombre_especialidad} "${this.cursoSeleccionado.paralelo}"`;
                
            doc.text(`Curso: ${nombreCurso}`, anchoPagina / 2, 21, { align: "center" }); // Subido a Y=21
            
            doc.setFontSize(9); // Antes 10
            doc.setFont("helvetica", "normal");
            doc.text(`Periodo: ${this.cursoSeleccionado?.nombre_periodo}`, anchoPagina / 2, 26, { align: "center" }); // Subido a Y=26

            // 3. Preparar la Matriz de Datos para la Tabla del PDF
            const bodyData = [];
            
            this.horarioMatriz.forEach(fila => {
                if (fila.isRecreo) {
                    bodyData.push([
                        { content: fila.rango, styles: { fontStyle: 'bold', fillColor: [240, 240, 240], valign: 'middle' } },
                        { 
                            content: 'R E C R E O', 
                            colSpan: 5, 
                            styles: { halign: 'center', valign: 'middle', fontStyle: 'bold', fillColor: [255, 193, 7], textColor: [0, 0, 0], fontSize: 10 } // Reducido a 10
                        }
                    ]);
                } else {
                    let row = [
                        { content: fila.rango, styles: { fontStyle: 'bold', fillColor: [240, 240, 240], valign: 'middle' } }
                    ];
                    
                    this.diasSemana.forEach(dia => {
                        if (fila[dia]) {
                            row.push({ 
                                content: `${fila[dia].asignatura}\n(${fila[dia].docente})`,
                                styles: { valign: 'middle', halign: 'center', textColor: [25, 135, 84], fontStyle: 'bold' } 
                            });
                        } else {
                            row.push({ 
                                content: '-', 
                                styles: { valign: 'middle', halign: 'center', textColor: [180, 180, 180] } 
                            });
                        }
                    });
                    bodyData.push(row);
                }
            });

            // 4. Dibujar la Tabla en el PDF (Más compacta)
            autoTable(doc, {
                startY: 32, // Empezar más arriba (antes 45)
                head: [['Hora', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']],
                body: bodyData,
                theme: 'grid',
                headStyles: { fillColor: [33, 37, 41], textColor: [255, 255, 255], halign: 'center', fontSize: 10 }, // Fuente cabecera reducida
                styles: { fontSize: 8, cellPadding: 2, lineColor: [0, 0, 0], lineWidth: 0.1 }, // Letra 8 y padding 2 (más compacto)
                alternateRowStyles: { fillColor: [252, 252, 252] },
                margin: { left: 15, right: 15 }
            });

            // 5. Añadir Espacio de Firma Dinámico al final de la tabla
            const finalY = doc.lastAutoTable.finalY || 32; 
            const altoPagina = doc.internal.pageSize.getHeight();
            
            // Reducimos la distancia de la firma para asegurar que entre
            let firmaY = finalY + 25; // Antes 40
            
            // Protección por si de verdad la tabla es colosalmente larga
            if (firmaY > altoPagina - 20) {
                doc.addPage();
                firmaY = 50; 
            }

            // Dibujar la línea de firma un poco más estrecha
            doc.setLineWidth(0.5);
            doc.line(anchoPagina / 2 - 30, firmaY, anchoPagina / 2 + 30, firmaY); 
            
            // Texto de firma
            doc.setFontSize(9); // Antes 10
            doc.setFont("helvetica", "bold");
            doc.text("Firma Autorizada", anchoPagina / 2, firmaY + 5, { align: "center" });
            
            doc.setFont("helvetica", "normal");
            doc.text("Rectorado / Coordinación Académica", anchoPagina / 2, firmaY + 10, { align: "center" });

            // 6. Generar y Descargar el Archivo
            let nombreArchivo = `Horario_${nombreCurso.replace(/[^a-zA-Z0-9 ]/g, "").replace(/\s+/g, "_")}.pdf`;
            doc.save(nombreArchivo);
        },

        // ---------- LÓGICA DEL MODAL ----------

        abrirModal(curso) {
            this.cursoSeleccionado = curso;
            this.horarioPreview = [];
            
            // Si el curso ya tiene horarios, mapearlos a la vista previa
            if(curso.tiene_horario) {
                curso.asignaturas_asignadas.forEach(asig => {
                    asig.horarios.forEach(hor => {
                        this.horarioPreview.push({
                            id_curso_asignatura: asig.id_curso_asignatura,
                            dia_semana: hor.dia_semana,
                            hora_inicio: hor.hora_inicio,
                            hora_fin: hor.hora_fin
                        });
                    });
                });
            }
            this.modalInstance.show();
        },

        limpiarModal() {
            this.cursoSeleccionado = null;
            this.horarioPreview = [];
            this.formHorario = { id_curso_asignatura: '', dia_semana: 'Lunes', hora_inicio: '', hora_fin: '' };
            this.horasRestantes = 0;
        },

        obtenerNombreAsignatura(id_ca) {
            const asig = this.cursoSeleccionado?.asignaturas_asignadas.find(a => a.id_curso_asignatura === id_ca);
            return asig ? asig.asignatura : 'Desconocido';
        },

        obtenerNombreDocente(id_ca) {
            const asig = this.cursoSeleccionado?.asignaturas_asignadas.find(a => a.id_curso_asignatura === id_ca);
            return asig ? asig.nombre_docente : '';
        },

        calcularDiferenciaHoras(inicio, fin) {
            // Convierte formato "HH:MM" a horas decimales para restar (Ej. 09:30 - 08:00 = 1.5 horas)
            const [hInicio, mInicio] = inicio.split(':').map(Number);
            const [hFin, mFin] = fin.split(':').map(Number);
            const totalMinutosInicio = hInicio * 60 + mInicio;
            const totalMinutosFin = hFin * 60 + mFin;
            return (totalMinutosFin - totalMinutosInicio) / 60;
        },

        calcularHorasRestantes() {
            if(!this.formHorario.id_curso_asignatura) return;

            const asignatura = this.cursoSeleccionado.asignaturas_asignadas.find(
                a => a.id_curso_asignatura === this.formHorario.id_curso_asignatura
            );

            // Sumamos las horas de esta asignatura que ya están en el preview
            let horasOcupadas = 0;
            this.horarioPreview.forEach(h => {
                if(h.id_curso_asignatura === this.formHorario.id_curso_asignatura) {
                    horasOcupadas += this.calcularDiferenciaHoras(h.hora_inicio, h.hora_fin);
                }
            });

            this.horasRestantes = asignatura.horas_semanales - horasOcupadas;
        },

        agregarAlPreview() {
            // Validar que la hora de inicio sea menor a la de fin
            const horasAAsignar = this.calcularDiferenciaHoras(this.formHorario.hora_inicio, this.formHorario.hora_fin);
            if (horasAAsignar <= 0) {
                mostraralertas2('La hora de fin debe ser mayor a la hora de inicio.', 'error');
                return;
            }

            // Validar horas semanales excedidas
            if (horasAAsignar > this.horasRestantes) {
                mostraralertas2('No puedes asignar más horas de las permitidas para esta asignatura.', 'warning');
                return;
            }

            // Añadir al arreglo de previsualización
            this.horarioPreview.push({ ...this.formHorario });
            
            // Recalcular y limpiar el formulario base
            this.calcularHorasRestantes();
            this.formHorario.hora_inicio = '';
            this.formHorario.hora_fin = '';
        },

        quitarFila(index) {
            this.horarioPreview.splice(index, 1);
            this.calcularHorasRestantes();
        },

        async guardarHorarioGeneral() {
            if (this.horarioPreview.length === 0) {
                mostraralertas2('Agregue al menos una materia al horario.', 'warning');
                return;
            }

            this.guardando = true;
            try {
                // Hacemos el POST al endpoint nuevo que creamos en Laravel
                const response = await API.post(`${this.baseUrl}/crearhorario`, {
                    id_curso: this.cursoSeleccionado.id_curso,
                    horarios: this.horarioPreview
                });

                mostraralertas2('Horario guardado correctamente.', 'success');
                this.modalInstance.hide();
                this.getData(); // Refrescar tabla principal
                this.limpiarModal();
            } catch (error) {
                // Si el backend lanza error 422 (Cruce de docente)
                if (error.response && error.response.status === 422) {
                    mostraralertas2(error.response.data.error, 'error');
                } else {
                    mostraralertas2('Ocurrió un error al guardar el horario.', 'error');
                }
            } finally {
                this.guardando = false;
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
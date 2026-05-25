<template>
    <div class="container-fluid py-4 bg-light min-vh-100">
        <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div
                        class="col-md-4 bg-blue text-white p-4 d-flex flex-column justify-content-center rounded-start-4 position-relative overflow-hidden">
                        <div class="position-relative z-1">
                            <h3 class="fw-bold mb-1" style="font-family: 'Fraunces';">Panel del docente tutor</h3>
                            <h1 class="fw-extrabold text-gold mb-3" style="font-family: 'Fraunces';">Reporte de
                                Calificaciones</h1>
                            <span class="badge bg-gold text-blue px-3 py-2 rounded-pill fw-bold shadow-sm">
                                Periodo: {{ infoTutor.periodo }}
                            </span>
                        </div>
                        <div class="deco-circle"></div>
                    </div>

                    <div class="col-md-8 p-4 d-flex align-items-center bg-white rounded-end-4">
                        <div class="row w-100 align-items-center">
                            <div class="col-sm-2 text-center d-none d-sm-block">
                                <i class="fas fa-chalkboard-teacher fa-4x text-blue-soft"></i>
                            </div>
                            <div class="col-sm-10">
                                <h5 class="text-muted small text-uppercase fw-bold mb-1">Curso Asignado</h5>
                                <h2 class="fw-bold text-blue mb-1">{{ infoTutor.curso }}</h2>
                                <p class="text-gold fw-bold mb-0 text-uppercase small">
                                    <i class="fas fa-graduation-cap me-1"></i> Especialidad: {{ infoTutor.especialidad
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 animate__animated animate__fadeIn animate__delay-1s">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div
                            class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 gap-2">
                            <h5 class="fw-bold text-blue mb-0">
                                <i class="fas fa-users me-2 text-gold"></i>Calificaciones de mis Alumnos
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                <button @click="generarCertificadosMasivosAprobacion"
                                    class="btn btn-gold text-blue btn-sm rounded-pill px-3 shadow-sm fw-bold">
                                    <i class="fas fa-file-pdf me-1"></i> Certificado Masivo de Aprobación
                                </button>

                                <div class="input-group input-group-sm w-auto">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" v-model="searchQuery" class="form-control border-0 bg-light"
                                        placeholder="Buscar alumno...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle custom-table text-center">
                                <thead>
                                    <tr>
                                        <th class="ps-3 text-start">Estudiante</th>
                                        <th>Nota Final</th>
                                        <th>Asistencia</th>
                                        <th>Conducta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="alumno in alumnosFiltrados" :key="alumno.id_matricula"
                                        class="transition-all">
                                        <td class="ps-3 py-3 text-start">
                                            <div class="d-flex align-items-center">
                                                <img :src="getPhotoUrl(alumno.estudiante.foto)"
                                                    class="rounded-circle border border-3 border-blue-soft me-3 shadow-sm"
                                                    width="50" height="50" style="object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold text-blue fs-6">{{ alumno.estudiante.apellidos
                                                        }} {{ alumno.estudiante.nombres }}</div>
                                                    <small class="text-gold fw-bold x-small">{{ alumno.estudiante.cedula
                                                        }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-black text-blue fs-6">{{ alumno.nota_final.toFixed(2) }}</td>

                                        <td>
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <span class="fw-bold mb-1 x-small"
                                                    :class="getColorTextoAsistencia(alumno.porcentaje_asistencia)">
                                                    {{ alumno.porcentaje_asistencia }}%
                                                </span>
                                                <div class="progress shadow-sm"
                                                    style="width: 60px; height: 6px; background-color: #e9ecef;">
                                                    <div class="progress-bar rounded-pill"
                                                        :class="getColorBarraAsistencia(alumno.porcentaje_asistencia)"
                                                        role="progressbar"
                                                        :style="{ width: alumno.porcentaje_asistencia + '%' }"
                                                        :aria-valuenow="alumno.porcentaje_asistencia" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold shadow-xs"
                                                :class="getBadgeConducta(alumno.conducta.final)">
                                                {{ alumno.conducta.final }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge px-3 py-1.5 rounded-pill fw-bold"
                                                :class="alumno.estado === 'APROBADO' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'">
                                                <i class="fas me-1"
                                                    :class="alumno.estado === 'APROBADO' ? 'fa-check' : 'fa-times'"></i>
                                                {{ alumno.estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <button @click="seleccionarEstudiante(alumno)"
                                                class="btn btn-blue btn-sm rounded-pill px-3 shadow-sm me-1"
                                                data-bs-toggle="modal" data-bs-target="#modalNotas">
                                                <i class="fas fa-eye me-1"></i> Desglose
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="alumnosFiltrados.length === 0" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                            <h5>No se encontraron registros de estudiantes.</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 border-top border-gold border-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-blue mb-3 pb-2 border-bottom">
                            <i class="fas fa-trophy text-gold me-2"></i> Cuadro de Honor (Top 3)
                        </h5>

                        <div v-for="(top, index) in rankingEstudiantes" :key="top.id_matricula"
                            class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3 border position-relative overflow-hidden bg-white shadow-xs">

                            <div class="position-absolute end-0 top-0 p-2 opacity-25">
                                <i class="fas fa-medal fa-3x"
                                    :class="index === 0 ? 'text-warning' : (index === 1 ? 'text-secondary' : 'text-danger')"></i>
                            </div>

                            <div class="d-flex align-items-center position-relative z-1">
                                <span class="fw-black fs-4 me-3 text-blue-soft" style="width: 20px;">#{{ index + 1
                                    }}</span>
                                <img :src="getPhotoUrl(top.estudiante.foto)" class="rounded-circle border border-2 me-2"
                                    width="42" height="42" style="object-fit: cover;">
                                <div>
                                    <p class="mb-0 fw-bold text-blue small text-truncate" style="max-width: 150px;">
                                        {{ top.estudiante.apellidos }} {{ top.estudiante.nombres.split(' ')[0] }}
                                    </p>
                                    <small class="badge bg-gold text-blue x-small fw-bold">Promedio: {{
                                        top.nota_final.toFixed(2) }}</small>
                                </div>
                            </div>

                            <button @click="generarCertificadoExcelencia(top)"
                                class="btn btn-outline-blue btn-xs rounded-pill px-2.5 shadow-sm fw-bold position-relative z-1">
                                <i class="fas fa-certificate text-gold me-1"></i> Diploma
                            </button>
                        </div>

                        <div v-if="rankingEstudiantes.length === 0" class="text-center py-4 text-muted small">
                            No hay datos suficientes para estructurar el ranquin.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalNotas" static tabindex="-1" aria-labelledby="modalNotasLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header bg-blue text-white rounded-top-4 py-3">
                        <h6 class="modal-title fw-bold" id="modalNotasLabel">
                            <i class="fas fa-book-reader me-2 text-gold"></i> Desglose de Asignaturas
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-shadow="none"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" v-if="selectedAlumno">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <img :src="getPhotoUrl(selectedAlumno.estudiante.foto)"
                                class="rounded-circle border border-3 border-blue-soft me-3" width="60" height="60"
                                style="object-fit: cover;">
                            <div>
                                <h5 class="fw-bold text-blue mb-0">{{ selectedAlumno.estudiante.apellidos }} {{
                                    selectedAlumno.estudiante.nombres }}</h5>
                                <small class="text-muted fw-semibold">Cédula: {{ selectedAlumno.estudiante.cedula
                                    }}</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle text-center custom-table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-start ps-2">Materia / Componente</th>
                                        <th>Calificación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="nota in selectedAlumno.calificaciones" :key="nota.asignatura">
                                        <td class="text-start ps-2 fw-semibold text-blue-light py-2">{{ nota.asignatura
                                            }}</td>
                                        <td class="fw-bold">{{ nota.nota_final.toFixed(2) }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill px-2.5 py-1 text-uppercase x-small font-weight-bold"
                                                :class="nota.estado === 'APROBADO' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                                                {{ nota.estado }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded border text-center">
                                    <small class="text-muted d-block fw-semibold text-uppercase x-small">Conducta
                                        Q1</small>
                                    <span class="fw-bold text-blue fs-5">{{ selectedAlumno.conducta.q1 }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded border text-center">
                                    <small class="text-muted d-block fw-semibold text-uppercase x-small">Conducta
                                        Q2</small>
                                    <span class="fw-bold text-blue fs-5">{{ selectedAlumno.conducta.q2 }}</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border-start border-blue border-3">
                            <div>
                                <span class="fw-bold text-blue small d-block">PROMEDIO GENERAL ACUMULADO:</span>
                                <small class="text-muted fw-bold x-small">CONDUCTA ANUAL: <span class="text-gold">{{
                                    selectedAlumno.conducta.final }}</span></small>
                            </div>
                            <span class="fs-5 fw-black text-blue">{{ selectedAlumno.nota_final.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios";
import { getMe } from "@/assets/js/auth";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { jsPDF } from "jspdf";

export default {
    data() {
        return {
            baseUrl: "/sistma",
            idpersona: 0,
            infoTutor: { curso: 'Cargando...', especialidad: 'Cargando...', periodo: '...' },
            alumnos: [],
            selectedAlumno: null,
            searchQuery: "",
            cargando: false,
        }
    },
    computed: {
        alumnosFiltrados() {
            if (!this.searchQuery) return this.alumnos;
            const query = this.searchQuery.toLowerCase().trim();
            return this.alumnos.filter(a =>
                a.estudiante.nombres.toLowerCase().includes(query) ||
                a.estudiante.apellidos.toLowerCase().includes(query) ||
                a.estudiante.cedula.includes(query)
            );
        },
        rankingEstudiantes() {
            return [...this.alumnos]
                .filter(alumno => alumno.estado === 'APROBADO')
                .sort((a, b) => b.nota_final - a.nota_final)
                .slice(0, 3);
        }
    },
    async mounted() {
        this.cargando = true;
        const me = await getMe();
        this.idpersona = me.id_persona;
        await this.fetchData();
        this.cargando = false;
    },
    methods: {
        async fetchData() {
            try {
                const res = await API.get(`${this.baseUrl}/datos-notas-alumno-tutor/${this.idpersona}`);
                console.log(res.data);
                this.infoTutor = res.data;
                this.alumnos = res.data.alumnos.sort((a, b) => a.estudiante.apellidos.localeCompare(b.estudiante.apellidos));
            } catch (e) {
                mostraralertas("No se pudo cargar la información del tutor", "error");
                console.error(e);
            }
        },
        seleccionarEstudiante(alumno) {
            this.selectedAlumno = alumno;
        },
        getPhotoUrl(fotoBase64) {
            return fotoBase64 ? `data:image/jpeg;base64,${fotoBase64}` : 'https://ui-avatars.com/api/?name=Estudiante&background=1D2A68&color=fff';
        },

        // --- MÉTODOS VISUALES PARA LA ASISTENCIA ---
        getColorTextoAsistencia(porcentaje) {
            if (porcentaje >= 90) return 'text-success';
            if (porcentaje >= 75) return 'text-warning text-darken';
            return 'text-danger';
        },
        getColorBarraAsistencia(porcentaje) {
            if (porcentaje >= 90) return 'bg-success';
            if (porcentaje >= 75) return 'bg-warning';
            return 'bg-danger';
        },
        getBadgeConducta(letra) {
            switch (letra) {
                case 'A':
                    return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                case 'B':
                    return 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                case 'C':
                    return 'bg-warning bg-opacity-10 text-warning text-darken border border-warning border-opacity-25';
                case 'D':
                case 'E':
                    return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                default:
                    return 'bg-secondary bg-opacity-10 text-secondary';
            }
        },

        // DIPLOMA 1: GENERACIÓN INDIVIDUAL DE EXCELENCIA ACADÉMICA
        generarCertificadoExcelencia(alumno) {
            const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });
            this.disenarPlantillaDiploma(doc, "CERTIFICADO DE EXCELENCIA ACADÉMICA");

            doc.setFont("helvetica", "normal");
            doc.setFontSize(15);
            doc.setTextColor(60, 60, 60);
            doc.text("Concedido con honor y distinción especial a:", 148, 92, { align: "center" });

            doc.setFont("helvetica", "bold");
            doc.setFontSize(24);
            doc.setTextColor(29, 42, 104);
            doc.text(`${alumno.estudiante.nombres} ${alumno.estudiante.apellidos}`, 148, 106, { align: "center" });

            doc.setDrawColor(244, 179, 36);
            doc.setLineWidth(0.8);
            doc.line(78, 112, 218, 112);

            doc.setFont("helvetica", "normal");
            doc.setFontSize(13);
            doc.setTextColor(80, 80, 80);

            const parrafo = `Por haber obtenido el más alto rendimiento estudiantil en el ${this.infoTutor.curso}, especialidad ${this.infoTutor.especialidad}, alcanzando un promedio general sobresaliente de:`;
            const lineasParrafo = doc.splitTextToSize(parrafo, 200);
            doc.text(lineasParrafo, 148, 122, { align: "center" });

            doc.setFont("helvetica", "bold");
            doc.setFontSize(26);
            doc.setTextColor(244, 179, 36);
            doc.text(`${alumno.nota_final.toFixed(2)} / 10.00`, 148, 144, { align: "center" });

            this.dibujarFirmasDiploma(doc);
            doc.save(`Diploma_Excelencia_${alumno.estudiante.cedula}.pdf`);
        },

        // DIPLOMA 2: GENERACIÓN MASIVA DE CERTIFICADOS DE APROBACIÓN
        generarCertificadosMasivosAprobacion() {
            const aprobados = this.alumnos.filter(a => a.estado === 'APROBADO');

            if (aprobados.length === 0) {
                mostraralertas("No existen alumnos con estado APROBADO en este curso.", "warning");
                return;
            }

            const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });

            aprobados.forEach((alumno, index) => {
                if (index > 0) doc.addPage({ orientation: "landscape", format: "a4" });

                this.disenarPlantillaDiploma(doc, "CERTIFICADO DE APROBACIÓN ACADÉMICA");

                doc.setFont("helvetica", "normal");
                doc.setFontSize(15);
                doc.setTextColor(60, 60, 60);
                doc.text("Certifica que el/la estudiante regular:", 148, 92, { align: "center" });

                doc.setFont("helvetica", "bold");
                doc.setFontSize(24);
                doc.setTextColor(29, 42, 104);
                doc.text(`${alumno.estudiante.nombres} ${alumno.estudiante.apellidos}`, 148, 106, { align: "center" });

                doc.setDrawColor(244, 179, 36);
                doc.setLineWidth(0.8);
                doc.line(78, 112, 218, 112);

                doc.setFont("helvetica", "normal");
                doc.setFontSize(13);
                doc.setTextColor(80, 80, 80);

                const parrafo = `Aprobó satisfactoriamente las mallas curriculares correspondientes al curso ${this.infoTutor.curso}, especialidad ${this.infoTutor.especialidad}, durante el periodo lectivo ${this.infoTutor.periodo}, registrando un promedio de:`;
                const lineasParrafo = doc.splitTextToSize(parrafo, 200);
                doc.text(lineasParrafo, 148, 122, { align: "center" });

                doc.setFont("helvetica", "bold");
                doc.setFontSize(22);
                doc.setTextColor(29, 42, 104);
                doc.text(`${alumno.nota_final.toFixed(2)} / 10.00`, 148, 144, { align: "center" });

                this.dibujarFirmasDiploma(doc);
            });

            doc.save(`Certificados_Masivos_Aprobacion_${this.infoTutor.curso.replace(/ /g, "_")}.pdf`);
        },

        disenarPlantillaDiploma(doc, titulo) {
            const blue = [29, 42, 104];
            const gold = [244, 179, 36];

            doc.setDrawColor(...blue);
            doc.setLineWidth(1.2);
            doc.rect(8, 8, 281, 194, 'S');

            doc.setDrawColor(...gold);
            doc.setLineWidth(0.5);
            doc.rect(10.5, 10.5, 276, 189, 'S');

            doc.setFillColor(...blue);
            doc.rect(8, 8, 12, 12, 'F');
            doc.rect(277, 8, 12, 12, 'F');
            doc.rect(8, 190, 12, 12, 'F');
            doc.rect(277, 190, 12, 12, 'F');

            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.setTextColor(...blue);
            doc.text("UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO \"MALIMPIA\"", 148, 34, { align: "center" });

            doc.setFontSize(10);
            doc.setTextColor(...gold);
            doc.text("REPÚBLICA DEL ECUADOR - MINISTERIO DE EDUCACIÓN", 148, 41, { align: "center" });

            doc.setFillColor(...blue);
            doc.rect(48, 52, 200, 14, 'F');

            doc.setFont("helvetica", "bold");
            doc.setFontSize(14);
            doc.setTextColor(255, 255, 255);
            doc.text(titulo, 148, 61, { align: "center" });
        },

        dibujarFirmasDiploma(doc) {
            const blue = [29, 42, 104];

            doc.setDrawColor(...blue);
            doc.setLineWidth(0.4);

            doc.line(45, 174, 115, 174);
            doc.line(182, 174, 252, 174);

            doc.setFont("helvetica", "bold");
            doc.setFontSize(9);
            doc.setTextColor(...blue);
            doc.text("Autoridad Institucional", 80, 179, { align: "center" });
            doc.text("Prof. / Ing. Docente Tutor", 217, 179, { align: "center" });

            doc.setFont("helvetica", "normal");
            doc.setFontSize(8);
            doc.setTextColor(100, 100, 100);
            doc.text("Rectorado / Dirección", 80, 183, { align: "center" });
            doc.text("Registro de Control Académico", 217, 183, { align: "center" });
        }
    }
}
</script>

<style scoped>
.text-blue {
    color: #1D2A68 !important;
}

.text-blue-light {
    color: #2c3e8c !important;
}

.bg-blue {
    background-color: #1D2A68 !important;
}

.text-blue-soft {
    color: rgba(29, 42, 104, 0.1) !important;
}

.border-blue-soft {
    border-color: rgba(29, 42, 104, 0.15) !important;
}

.text-gold {
    color: #F4B324 !important;
}

.bg-gold {
    background-color: #F4B324 !important;
}

.fw-black {
    font-weight: 900;
}

.fw-extrabold {
    font-weight: 800;
}

.shadow-xs {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Custom table refinements */
.custom-table thead th {
    color: #1D2A68;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(29, 42, 104, 0.1);
}

.custom-table-sm thead th {
    font-size: 0.78rem;
    background-color: #f8f9fa;
    color: #1D2A68;
    font-weight: 700;
}

.transition-all {
    transition: all 0.2s ease;
}

tr.transition-all:hover {
    background-color: rgba(244, 179, 36, 0.04) !important;
}

/* Botones */
.btn-blue {
    background-color: #1D2A68;
    color: white;
    border: 1px solid #1D2A68;
}

.btn-blue:hover {
    background-color: #121b44;
    color: white;
}

.btn-gold {
    background-color: #F4B324;
    color: #1D2A68;
    border: none;
}

.btn-gold:hover {
    background-color: #e0a216;
    color: #1D2A68;
}

.btn-outline-blue {
    color: #1D2A68;
    border-color: #1D2A68;
    background-color: transparent;
}

.btn-outline-blue:hover {
    background-color: #1D2A68;
    color: white;
}

.btn-xs {
    padding: 0.25rem 0.6rem;
    font-size: 0.72rem;
}

/* Fallbacks de sub-estilos Bootstrap */
.bg-success-subtle {
    background-color: #e8f5e9;
}

.bg-danger-subtle {
    background-color: #ffebee;
}

.text-warning.text-darken {
    color: #d39e00 !important;
}

/* Amarillo más oscuro para legibilidad */
.deco-circle {
    position: absolute;
    width: 200px;
    height: 200px;
    background: rgba(244, 179, 36, 0.1);
    border-radius: 50%;
    top: -50px;
    left: -100px;
    z-index: 0;
}

.x-small {
    font-size: 0.7rem;
}
</style>
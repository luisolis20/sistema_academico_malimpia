<template>
  <div class="container-fluid py-4 bg-light min-vh-100">
    <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
      <div class="card-body p-0">
        <div class="row g-0">
          <div
            class="col-md-4 bg-blue text-white p-4 d-flex flex-column justify-content-center rounded-start-4 position-relative overflow-hidden">
            <div class="position-relative z-1">
              <h3 class="fw-bold mb-1" style="font-family: 'Fraunces';">Panel del docente tutor</h3>
              <h1 class="fw-extrabold text-gold mb-3" style="font-family: 'Fraunces';">Reporte de Asistencia</h1>
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
                  <i class="fas fa-graduation-cap me-1"></i> Especialidad: {{ infoTutor.especialidad }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeIn animate__delay-1s">
      <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
          <h5 class="fw-bold text-blue mb-0">
            <i class="fas fa-users me-2 text-gold"></i>Listado de mis Alumnos Matriculados
          </h5>
          <div class="d-flex gap-2">
            <button @click="generarReporteGeneralCurso"
              class="btn btn-gold text-blue btn-sm rounded-pill px-3 shadow-sm fw-bold">
              <i class="fas fa-file-pdf me-1"></i> Descargar Todas las Asistencias
            </button>

            <div class="input-group input-group-sm w-auto">
              <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
              <input type="text" class="form-control border-0 bg-light" placeholder="Buscar alumno...">
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle custom-table">
            <thead>
              <tr>
                <th class="ps-3">Estudiante</th>
                <th>Cédula</th>
                <th>Edad / Sexo</th>
                <th>Representante</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="alumno in alumnos" :key="alumno.id_matricula" class="transition-all">
                <td class="ps-3 py-3">
                  <div class="d-flex align-items-center">
                    <img
                      :src="alumno.estudiante.foto ? 'data:image/jpeg;base64,' + alumno.estudiante.foto : getPhotoUrl(null)"
                      class="rounded-circle border border-3 border-blue-soft me-3 shadow-sm" width="55" height="55"
                      style="object-fit: cover;">
                    <div>
                      <div class="fw-bold text-blue fs-6">{{ alumno.estudiante.apellidos }} {{ alumno.estudiante.nombres
                      }}</div>
                      <small class="text-gold fw-bold x-small">ALUMNO REGULAR</small>
                    </div>
                  </div>
                </td>
                <td class="text-muted small">{{ alumno.estudiante.cedula }}</td>
                <td>
                  <span class="fw-bold">{{ calcularEdad(alumno.estudiante.fecha_nacimiento) }} años</span>
                  <br>
                  <span class="text-muted small">{{ alumno.estudiante.sexo }}</span>
                </td>
                <td>
                  <div class="fw-bold text-blue">{{ alumno.representante.apellidos }} {{ alumno.representante.nombres }}
                  </div>
                  <small class="text-muted"><i class="fas fa-phone me-1 x-small"></i> {{ alumno.representante.telefono
                  }}</small>
                </td>
                <td class="text-center">
                  <button @click="seleccionarEstudiante(alumno)" class="btn btn-blue btn-sm rounded-pill px-3 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalAsistencia">
                    <i class="fas fa-calendar-alt me-1"></i> Ver Asistencia
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="alumnos.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
          <h5>No hay alumnos matriculados en su curso aún.</h5>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalAsistencia" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
          <div class="modal-header bg-blue text-white rounded-top-4 p-4">
            <div class="d-flex align-items-center">
              <img
                :src="selectedAlumno?.estudiante.foto ? 'data:image/jpeg;base64,' + selectedAlumno.estudiante.foto : getPhotoUrl(null)"
                class="rounded-circle border border-3 border-gold me-3" width="60" height="60"
                style="object-fit: cover;">
              <div>
                <h5 class="modal-title fw-bold mb-0">Historial de Asistencia</h5>
                <p class="mb-0 text-gold-soft small">{{ selectedAlumno?.estudiante.apellidos }} {{
                  selectedAlumno?.estudiante.nombres }}</p>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4 bg-light">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
              <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <i class="fas fa-filter text-gold me-2"></i>
                  <label class="small fw-bold text-blue mb-0">Filtrar por Mes:</label>
                  <select v-model="filtroMes" class="form-select form-select-sm rounded-pill border-blue-soft"
                    style="width: 180px;">
                    <option value="">Todos los meses</option>
                    <option v-for="m in 12" :key="m" :value="m">{{ nombreMes(m) }}</option>
                  </select>
                </div>
                <div class="small text-muted">
                  Mostrando <span class="fw-bold text-blue">{{ asistenciasFiltradas.length }}</span> registros
                </div>
              </div>
            </div>

            <div class="table-responsive rounded-3 shadow-sm bg-white" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-sm table-hover align-middle mb-0 custom-table-sm">
                <thead class="table-light sticky-top">
                  <tr>
                    <th class="ps-3">Fecha</th>
                    <th>Asignatura</th>
                    <th class="text-center">Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="asist in asistenciasFiltradas" :key="asist.fecha + asist.asignatura">
                    <td class="ps-3 fw-bold">{{ asist.fecha }}</td>
                    <td class="text-blue">{{ asist.asignatura }}</td>
                    <td class="text-center">
                      <span :class="badgeEstado(asist.estado)">{{ asist.estado }}</span>
                    </td>
                  </tr>
                  <tr v-if="asistenciasFiltradas.length === 0">
                    <td colspan="3" class="text-center py-4 text-muted">No hay registros de asistencia para este filtro.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
              <button @click="generarPDF(false)" class="btn btn-outline-blue rounded-pill px-4" :disabled="!filtroMes">
                <i class="fas fa-file-pdf me-2"></i>Generar Reporte Mensual
              </button>
              <button @click="generarPDF(true)" class="btn btn-gold text-blue fw-bold rounded-pill px-4 shadow">
                <i class="fas fa-file-invoice me-2"></i>Generar Reporte Total
              </button>
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
import autoTable from "jspdf-autotable";
// Nota: Asegúrate de tener instalada animate.css para las animaciones

export default {
  data() {
    return {
      baseUrl: "/sistma",
      idpersona: 0,
      nombreDocente: "",
      infoTutor: {
        curso: 'Cargando...',
        especialidad: 'Cargando...',
        periodo: '...'
      },
      Persona: {},
      alumnos: [],
      selectedAlumno: null,
      filtroMes: "",
      cargando: false,
    }
  },
  computed: {
    asistenciasFiltradas() {
      if (!this.selectedAlumno) return [];
      let asist = this.selectedAlumno.historial_asistencia;
      if (this.filtroMes) {
        // Ordenar cronológicamente descendente
        asist = asist.filter(a => new Date(a.fecha).getMonth() + 1 == this.filtroMes);
      }
      return asist.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
    }
  },
  async mounted() {
    this.cargando = true;
    const me = await getMe();
    this.idpersona = me.id_persona;
    await Promise.all([this.getPersona(), this.fetchData()]);
    this.cargando = false;
  },
  methods: {
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async fetchData() {
      try {
        const res = await API.get(`${this.baseUrl}/datos-tutor/${this.idpersona}`);

        this.infoTutor = res.data;
        // Ordenar alumnos por apellido por defecto
        this.alumnos = res.data.alumnos.sort((a, b) => a.estudiante.apellidos.localeCompare(b.estudiante.apellidos));
      } catch (e) {
        mostraralertas("No se pudo cargar la información del tutor", "error");
        console.error(e);
      }
    },
    calcularEdad(fecha) {
      if (!fecha) return '...';
      const hoy = new Date();
      const cumple = new Date(fecha);
      let edad = hoy.getFullYear() - cumple.getFullYear();
      if (hoy.getMonth() < cumple.getMonth() || (hoy.getMonth() === cumple.getMonth() && hoy.getDate() < cumple.getDate())) edad--;
      return edad;
    },
    seleccionarEstudiante(alumno) {
      this.selectedAlumno = alumno;
      this.filtroMes = ""; // Resetear filtro al cambiar de alumno
    },
    getPhotoUrl(fotoBase64) {
      return fotoBase64 ? `data:image/jpeg;base64,${fotoBase64}` : 'https://ui-avatars.com/api/?name=Estudiante&background=1D2A68&color=fff';
    },
    badgeEstado(estado) {
      return {
        'badge bg-success-subtle text-success rounded-pill px-3 x-small': estado === 'Presente',
        'badge bg-danger-subtle text-danger rounded-pill px-3 x-small': estado === 'Ausente',
        'badge bg-warning-subtle text-warning-dark rounded-pill px-3 x-small': estado === 'Atraso',
        'badge bg-info-subtle text-info-dark rounded-pill px-3 x-small': estado === 'Justificado',
      }
    },
    nombreMes(m) {
      return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(new Date(2024, m - 1)).toUpperCase();
    },
    // --- GENERACIÓN DE PDF MEJORADA ---
    generarPDF(esTotal) {
      // Configuración de hoja A4: [210mm x 297mm]
      const doc = new jsPDF({
        orientation: "p",
        unit: "mm",
        format: "a4"
      });

      const alumno = this.selectedAlumno;
      const asistencias = esTotal
        ? alumno.historial_asistencia.sort((a, b) => new Date(a.fecha) - new Date(b.fecha))
        : this.asistenciasFiltradas;

      if (asistencias.length === 0) return mostraralertas("No hay datos para generar este reporte", "warning");

      const colAzul = [29, 42, 104];
      const colOro = [244, 179, 36];

      // 1. HEADER DEL PDF
      doc.setFillColor(...colAzul);
      doc.rect(0, 0, 210, 40, 'F');

      // CARGA DEL LOGO (mile.png desde public)
      // Nota: Al estar en public, la ruta es simplemente '/mile.png'
      const logoImg = new Image();
      logoImg.src = '/mile.png';

      // Usamos el evento onload para asegurar que la imagen existe, 
      // pero para evitar que el método sea async, la dibujamos directamente 
      // (jsPDF suele manejar el caché de imágenes cargadas en el navegador)
      try {
        doc.addImage(logoImg, 'PNG', 14, 8, 35, 23);
      } catch (e) {
        // Si falla el logo, dibujamos el placeholder para que no se rompa el PDF
        doc.setFillColor(255, 255, 255);
        doc.roundedRect(14, 10, 35, 20, 3, 3, 'F');
      }

      // Nombre Institución con ajuste de texto (Wrap text)
      doc.setTextColor(255, 255, 255);
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16); // Bajamos un poco el tamaño para nombres largos

      const nombreInst = "UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO \"MALIMPIA\"";
      // Ajustamos el texto a un ancho de 140mm para que no choque con los bordes
      const textSplit = doc.splitTextToSize(nombreInst, 140);
      doc.text(textSplit, 55, 16);

      doc.setFont("helvetica", "normal");
      doc.setFontSize(10);
      doc.setTextColor(...colOro);
      // Calculamos la posición Y dependiendo de si el nombre ocupó 1 o 2 líneas
      const subTitleY = textSplit.length > 1 ? 28 : 24;
      doc.text("REPORTE OFICIAL DE ASISTENCIA INDIVIDUAL", 55, subTitleY);
      doc.setDrawColor(...colOro);
      doc.line(55, subTitleY + 2, 195, subTitleY + 2);

      // 2. DATOS DE CONTEXTO
      doc.setTextColor(0, 0, 0);
      doc.setFontSize(10);
      doc.setFont("helvetica", "bold");

      doc.text("DATOS DEL ESTUDIANTE", 14, 50);
      doc.setFont("helvetica", "normal");
      doc.text(`Nombres: ${alumno.estudiante.apellidos} ${alumno.estudiante.nombres}`, 14, 55);
      doc.text(`Cédula: ${alumno.estudiante.cedula}`, 14, 60);
      doc.text(`Curso: ${this.infoTutor.curso} - ${this.infoTutor.especialidad}`, 14, 65);

      doc.setFont("helvetica", "bold");
      doc.text("DATOS DEL REPORTE", 130, 50);
      doc.setFont("helvetica", "normal");
      // Ajustado a this.Persona según tu código
      doc.text(`Tutor: ${this.Persona.nombres} ${this.Persona.apellidos}`, 130, 55);
      doc.text(`Periodo: ${this.infoTutor.periodo}`, 130, 60);
      doc.text(`Emisión: ${new Date().toLocaleString()}`, 130, 65);

      // Título del Reporte
      const subTitulo = esTotal ? "HISTORIAL COMPLETO DE ASISTENCIA" : `REPORTE MENSUAL: ${this.nombreMes(this.filtroMes)}`;
      doc.setFillColor(...colOro);
      doc.roundedRect(14, 72, 182, 8, 2, 2, 'F');
      doc.setTextColor(...colAzul);
      doc.setFont("helvetica", "bold");
      doc.setFontSize(11);
      doc.text(subTitulo, 105, 77.5, { align: 'center' });

      // 3. TABLA DE DATOS
      autoTable(doc, {
        startY: 85,
        head: [['Fecha', 'Asignatura / Módulo', 'Estado']],
        body: asistencias.map(a => [a.fecha, a.asignatura, a.estado.toUpperCase()]),
        headStyles: {
          fillColor: colAzul,
          textColor: [255, 255, 255],
          fontStyle: 'bold',
          halign: 'center'
        },
        bodyStyles: { textColor: [50, 50, 50] },
        alternateRowStyles: { fillColor: [240, 243, 250] },
        columnStyles: {
          0: { halign: 'center', cellWidth: 35 },
          2: { halign: 'center', cellWidth: 40, fontStyle: 'bold' }
        },
        theme: 'grid',
        didParseCell: function (data) {
          if (data.section === 'body' && data.column.index === 2) {
            const estado = data.cell.raw;
            if (estado === 'AUSENTE') data.cell.styles.textColor = [220, 53, 69];
            if (estado === 'ATRASO') data.cell.styles.textColor = [255, 193, 7];
            if (estado === 'PRESENTE') data.cell.styles.textColor = [25, 135, 84];
          }
        }
      });

      // 4. PIE DE PÁGINA Y FIRMA
      let finalY = doc.lastAutoTable.finalY + 30;
      if (finalY > 260) { // Margen de seguridad para hoja A4
        doc.addPage();
        finalY = 40;
      }

      doc.setDrawColor(150);
      doc.setLineWidth(0.5);
      doc.line(70, finalY, 140, finalY);

      doc.setFontSize(10);
      doc.setTextColor(0);
      doc.setFont("helvetica", "bold");
      doc.text(`${this.Persona.nombres} ${this.Persona.apellidos}`, 105, finalY + 5, { align: 'center' });
      doc.setFont("helvetica", "normal");
      doc.text("Docente Tutor", 105, finalY + 10, { align: 'center' });

      // Línea final decorativa (Posicionada al final de la hoja A4: 297mm)
      doc.setFillColor(...colAzul);
      doc.rect(0, 287, 210, 10, 'F');
      doc.setFillColor(...colOro);
      doc.rect(0, 285, 210, 2, 'F');

      doc.save(`Reporte_Asistencia_${alumno.estudiante.cedula}.pdf`);
    },
    generarReporteGeneralCurso() {
      if (this.alumnos.length === 0) return mostraralertas("No hay alumnos para reportar", "warning");

      const doc = new jsPDF({
        orientation: "l", // Horizontal
        unit: "mm",
        format: "a4"
      });

      const colAzul = [29, 42, 104];
      const colOro = [244, 179, 36];
      const logoImg = new Image();
      logoImg.src = '/mile.png';

      // 1. ORGANIZAR Y AGRUPAR DATOS POR MES (Evitando desfase de zona horaria)
      const dataPorMes = {};

      this.alumnos.forEach(alumno => {
        alumno.historial_asistencia.forEach(asist => {
          // FIX CRÍTICO: Dividimos el string directamente para no usar 'new Date()' y evitar que reste horas
          const partesFecha = asist.fecha.split('-'); // ["2026", "05", "01"]
          if (partesFecha.length !== 3) return;

          const mesIndex = parseInt(partesFecha[1], 10); // "05" -> 5
          const dia = partesFecha[2]; // "01"
          const mesStr = partesFecha[1]; // "05"

          const nombreMes = this.nombreMes(mesIndex); // Convertimos el 5 en "Mayo"
          const fechaCorta = `${dia}/${mesStr}`; // "01/05"

          // Inicializar el mes si no existe
          if (!dataPorMes[nombreMes]) {
            dataPorMes[nombreMes] = { fechas: new Set(), filas: {}, totales: { p: 0, a: 0, atr: 0, j: 0 } };
          }

          // Guardar la fecha como columna
          dataPorMes[nombreMes].fechas.add(fechaCorta);

          // Agrupar por estudiante y asignatura para tener una sola fila
          const key = `${alumno.estudiante.cedula}-${asist.asignatura}`;
          if (!dataPorMes[nombreMes].filas[key]) {
            dataPorMes[nombreMes].filas[key] = {
              estudiante: `${alumno.estudiante.apellidos} ${alumno.estudiante.nombres}`,
              asignatura: asist.asignatura,
              asistencias: {}
            };
          }

          // Conteo y simbología
          let simbolo = '';
          switch (asist.estado) {
            case 'Presente': simbolo = '1'; dataPorMes[nombreMes].totales.p++; break;
            case 'Ausente': simbolo = '0'; dataPorMes[nombreMes].totales.a++; break;
            case 'Atraso': simbolo = '-'; dataPorMes[nombreMes].totales.atr++; break;
            case 'Justificado': simbolo = 'J'; dataPorMes[nombreMes].totales.j++; break;
          }

          // Asignar el símbolo a la fecha exacta de esa materia
          dataPorMes[nombreMes].filas[key].asistencias[fechaCorta] = simbolo;
        });
      });

      // 2. CONSTRUIR EL PDF MES POR MES
      const mesesEncontrados = Object.keys(dataPorMes);
      if (mesesEncontrados.length === 0) return mostraralertas("No hay datos de asistencia procesables", "info");

      mesesEncontrados.forEach((mes, index) => {
        if (index > 0) doc.addPage();

        // HEADER ESTILO IMAGEN (Azul Marino y Oro)
        doc.setFillColor(...colAzul);
        doc.rect(0, 0, 297, 35, 'F');
        try { doc.addImage(logoImg, 'PNG', 12, 5, 28, 25); } catch (e) { }

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO \"MALIMPIA\"", 45, 14);

        doc.setFontSize(11);
        doc.setTextColor(...colOro);
        doc.text(`REPORTE ESTRATIFICADO DE ASISTENCIAS | CURSO: ${this.infoTutor.curso}`, 45, 21);

        doc.setFontSize(14);
        doc.text(`MES: ${mes.toUpperCase()}`, 45, 29);

        // INFO DOCENTE Y PERIODO
        doc.setTextColor(50);
        doc.setFontSize(9);
        doc.setFont("helvetica", "bold");
        doc.text(`Docente Tutor: ${this.Persona.nombres} ${this.Persona.apellidos}`, 12, 42);
        doc.text(`Periodo Lectivo: ${this.infoTutor.periodo}`, 120, 42);
        doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 250, 42);

        // TABLA: Ordenar las fechas de menor a mayor para las columnas (Ej: 01/05, 02/05)
        const fechasMes = Array.from(dataPorMes[mes].fechas).sort((a, b) => {
          return parseInt(a.split('/')[0]) - parseInt(b.split('/')[0]);
        });

        const tableHead = [['Estudiante', 'Asignatura', ...fechasMes]];

        // TABLA: Ordenar los estudiantes alfabéticamente
        const tableBody = Object.values(dataPorMes[mes].filas)
          .sort((a, b) => {
            if (a.estudiante === b.estudiante) return a.asignatura.localeCompare(b.asignatura);
            return a.estudiante.localeCompare(b.estudiante);
          })
          .map(f => [
            f.estudiante,
            f.asignatura,
            ...fechasMes.map(fecha => f.asistencias[fecha] || '')
          ]);

        autoTable(doc, {
          startY: 48,
          head: tableHead,
          body: tableBody,
          theme: 'grid',
          headStyles: { fillColor: colAzul, halign: 'center', fontSize: 8 },
          styles: { fontSize: 8, cellPadding: 2, valign: 'middle' },
          columnStyles: {
            0: { cellWidth: 60, fontStyle: 'bold' },
            1: { cellWidth: 50 }
          },
          didParseCell: (data) => {
            // Colorear '1' de verde y '0' de rojo como en la imagen
            if (data.column.index > 1 && data.section === 'body') {
              data.cell.styles.halign = 'center';
              const valor = data.cell.raw;
              if (valor === '1') data.cell.styles.textColor = [25, 135, 84]; // Verde
              if (valor === '0') data.cell.styles.textColor = [220, 53, 69]; // Rojo
              if (valor === '-') data.cell.styles.textColor = [244, 179, 36]; // Oro
              if (valor === 'J') data.cell.styles.textColor = [13, 110, 253]; // Azul
            }
          }
        });

        // --- NUEVA LÓGICA DE SALTO DE PÁGINA PARA LOS TOTALES ---
        let finalY = doc.lastAutoTable.finalY + 15;
        const altoPagina = doc.internal.pageSize.getHeight(); // Para formato A4 horizontal es 210mm

        // Necesitamos aproximadamente 30-35mm de espacio para imprimir todos los totales.
        // Si la tabla terminó muy abajo, creamos una página nueva.
        if (finalY + 35 > altoPagina) {
          doc.addPage();
          finalY = 20; // Reiniciamos la posición Y al inicio de la nueva página
        }

        // TOTALES POR MES (Alineado igual a la imagen)
        doc.setFontSize(10);
        doc.setTextColor(0);

        // Columna Izquierda (Leyenda)
        doc.text("1 = Presente", 20, finalY);
        doc.text("0 = Ausente", 20, finalY + 8);
        doc.text("- = Atraso", 20, finalY + 16);
        doc.text("J = Justificado", 20, finalY + 24);

        // Columna Derecha (Totales)
        const totalX = 200;
        doc.text(`Total de Presente = ${dataPorMes[mes].totales.p}`, totalX, finalY);
        doc.text(`Total de Ausente = ${dataPorMes[mes].totales.a}`, totalX, finalY + 8);
        doc.text(`Total de Atraso = ${dataPorMes[mes].totales.atr}`, totalX, finalY + 16);
        doc.text(`Total de Justificaciones = ${dataPorMes[mes].totales.j}`, totalX, finalY + 24);
      });

      doc.save(`Reporte_Asistencias_General_${this.infoTutor.curso.replace(/ /g, '_')}.pdf`);
    }
  }
}
</script>

<style scoped>
/* Definición de Colores y Variables */
:root {
  --blue-institucional: #1D2A68;
  --gold-institucional: #F4B324;
}

.text-blue {
  color: #1D2A68 !important;
}

.bg-blue {
  background-color: #1D2A68 !important;
}

.text-blue-soft {
  color: rgba(29, 42, 104, 0.1) !important;
}

.border-blue-soft {
  border-color: rgba(29, 42, 104, 0.2) !important;
}

.text-gold {
  color: #F4B324 !important;
}

.text-gold-soft {
  color: rgba(244, 179, 36, 0.7) !important;
}

.bg-gold {
  background-color: #F4B324 !important;
}

/* Estilos de Tabla */
.custom-table thead th {
  color: #1D2A68;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.5px;
  border-bottom: 2px solid rgba(29, 42, 104, 0.1);
}

.custom-table-sm thead th {
  font-size: 0.75rem;
  background-color: #f8f9fa;
  color: #1D2A68;
}

.transition-all {
  transition: all 0.3s ease;
}

tr.transition-all:hover {
  background-color: rgba(244, 179, 36, 0.05) !important;
  /* Hover color Oro muy suave */
}

/* Badges Subtles (Bootstrap 5.3 style fallback) */
.bg-success-subtle {
  background-color: #d1e7dd;
}

.bg-danger-subtle {
  background-color: #f8d7da;
}

.bg-warning-subtle {
  background-color: #fff3cd;
}

.bg-info-subtle {
  background-color: #cff4fc;
}

.text-warning-dark {
  color: #997404;
}

.text-info-dark {
  color: #087990;
}

/* Botones Personalizados */
.btn-blue {
  background-color: #1D2A68;
  color: white;
  border: 1px solid #1D2A68;
}

.btn-blue:hover {
  background-color: #141d4a;
  color: white;
}

.btn-outline-blue {
  color: #1D2A68;
  border-color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

/* Decoraciones Header */
.fw-extrabold {
  font-weight: 800;
}

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

/* Utilidades de texto */
.x-small {
  font-size: 0.7rem;
}
</style>
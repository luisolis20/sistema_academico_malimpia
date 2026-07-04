<template>
  <!-- Contenedor principal del componente -->
  <div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Cabecera del componente -->
    <header class="mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-gold border-5 no-print">
      <!-- FILA SUPERIOR: Títulos y Estado del Periodo -->
      <div class="row align-items-center g-3">
        <!-- Título e Icono Principal -->
        <div class="col-md-8">
          <!-- Título -->
          <h2 class="fw-bold text-blue mb-1">
            <i class="fas fa-file-invoice-doll me-2 text-gold"></i>Histórico de Notas
          </h2>
          <!-- Subtítulo -->
          <p class="text-muted mb-0">
            Visualiza la nota final de cada una de tus asignaturas a lo largo de tu trayectoria académica.
          </p>
        </div>
        <!-- Botón para generar el PDF -->
        <div class="col-md-4 text-md-end">
          <button @click="generarPDFHistorial" class="btn btn-gold btn-md font-bold rounded-pill shadow-sm px-4 no-print" :disabled="loading || historial.length === 0">
            <i class="fas fa-file-pdf me-2"></i>Descargar Historial
          </button>
        </div>
      </div>
    </header>
    <!-- Contenedor del cuerpo, solo se muestra si hay notas estudiantes -->
    <div v-if="loading" class="text-center py-5">
      <!-- Indicador de carga -->
      <div class="spinner-border text-blue" role="status">
        <!-- Indicador de carga -->
        <span class="visually-hidden">Cargando historial...</span>
      </div>
      <!-- Texto -->
      <p class="mt-2 text-muted fw-bold">Recopilando tu trayectoria académica...</p>
    </div>
    <!-- Contenedor del cuerpo, solo se muestra si no hay datos -->
    <div v-else-if="historial.length === 0" class="alert alert-info rounded-4 p-4 text-center shadow-sm">
      <!-- Icono -->
      <i class="fas fa-folder-open fa-2x text-blue mb-2"></i>
      <!-- Título -->
      <h5>Historial Vacío</h5>
      <!-- Subtítulo -->
      <p class="mb-0 text-muted">Aún no posees registros de calificaciones finales en el sistema.</p>
    </div>
    <!-- Contenedor del cuerpo, solo se muestra si hay notas estudiantes -->
    <div v-else class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-5">
      <!-- Contenedor de la tabla -->
      <div class="table-responsive">
        <!-- Contenedor de la tabla -->
        <table class="table table-bordered align-middle mb-0 text-center custom-table-grades">
          <!-- Contenedor de la cabecera -->
          <thead class="bg-blue text-white header-double-level">
            <!-- Contenedor de la cabecera -->
            <tr>
              <th class="bg-blue-dark text-white">Periodo y Curso</th>
              <th>Asignatura</th>
              <th>Nota Final</th>
              <th>Estado Materia</th>
              <th class="bg-attendance">Asistencia General</th>
              <th class="bg-status">Estado del Curso</th>
            </tr>
          </thead>
          <!-- Contenedor de las filas -->
          <tbody>
            <!-- Elemento del cuerpo, se usa v-for para recorrer el array de asignaturas y cursos y agregar datos a la tabla -->
            <template v-for="(curso, cIdx) in historial" :key="'curso-'+cIdx">
              <!-- Elemento del cuerpo, se usa v-for para recorrer el array de asignaturas y agregar datos a la tabla -->
              <tr v-for="(asig, aIdx) in curso.asignaturas" :key="'asig-'+cIdx+'-'+aIdx" class="grade-row-hover">
                <!-- Contenedor del componente, se muestra el curso en el que se encuentra la asignatura -->
                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="bg-light border-end-heavy align-middle">
                  <!-- Contenedor del componente, información del curso -->
                  <div class="p-2">
                    <span class="d-block fw-bold text-blue mb-1 fs-6">{{ curso.periodo }}</span>
                    <span class="d-block text-dark fw-bold mb-1">{{ curso.nivel }} "{{ curso.paralelo }}"</span>
                    <span class="badge bg-blue fw-normal">{{ curso.especialidad }}</span>
                  </div>
                </td>
                <!-- Contenedor del componente, se muestra la asignatura y su nota final -->
                <td class="text-start fw-bold text-secondary">{{ asig.nombre }}</td>
                <!-- Contenedor del componente, se muestra la nota final de la asignatura -->
                <td class="fw-bold fs-6" :class="asig.nota_final < 7 ? 'text-danger' : 'text-blue'">
                  {{ formatNota(asig.nota_final) }}
                </td>
                <!-- Contenedor del componente, se muestra el estado de la asignatura -->
                <td>
                  <span class="badge px-2 py-1 rounded-pill" :class="getBadgeEstado(asig.estado)">
                    {{ asig.estado }}
                  </span>
                </td>
                <!-- Contenedor del componente, se muestra el asistente y su asistencia -->
                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="border-start align-middle">
                  <!-- Contenedor del componente, se muestra el asistente y su asistencia -->
                  <div class="d-flex flex-column align-items-center px-3">
                    <span class="fw-bold fs-5 mb-1" :class="curso.asistencia_curso < 75 ? 'text-danger' : 'text-success'">
                      {{ curso.asistencia_curso }}%
                    </span>
                    <div class="progress w-100" style="height: 5px;">
                      <div class="progress-bar" :class="curso.asistencia_curso < 75 ? 'bg-danger' : 'bg-success'" :style="{ width: curso.asistencia_curso + '%' }"></div>
                    </div>
                  </div>
                </td>
                <!-- Contenedor del componente, se muestra el estado del curso -->
                <td v-if="aIdx === 0" :rowspan="curso.asignaturas.length" class="align-middle border-end-heavy">
                  <span class="badge px-3 py-2 fs-6 rounded-pill text-uppercase shadow-sm" :class="getBadgeEstado(curso.estado_curso)">
                    {{ curso.estado_curso }}
                  </span>
                </td>

              </tr>
            </template>
          </tbody>
          <!-- Contenedor del pie de la tabla, calcula el promedio general del historial -->
          <tfoot class="bg-gold-light-cell border-top border-3 border-gold">
            <tr>
              <td colspan="2" class="text-end fw-black text-blue fs-5 py-3 align-middle">
                PROMEDIO GENERAL HISTÓRICO ACUMULADO:
              </td>
              <td class="fw-black fs-4 text-center align-middle" :class="promedioGlobal < 7 ? 'text-danger' : 'text-blue'">
                {{ formatNota(promedioGlobal) }}
              </td>
              <td colspan="3"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * mis_notas_historico: Es un componente en el que se encuentra toda la lógica de la aplicación, para realizar la búsqueda de notas del estudiante.
 * Importamos la API de Axios para realizar peticiones al backend
 * Importamos la función mostraralertas para mostrar mensajes de alerta
 * Importamos la librería jsPDF para generar el PDF
 * Importamos la librería autoTable para generar la tabla de notas
 * Importamos la función getMe para obtener la información del usuario logueado
 */
import API from "@/assets/js/axios";
import { mostraralertas } from "@/assets/js/funciones/functions";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import { getMe } from "@/assets/js/auth";
/**
 * Exporta el componente Vue con su configuración, datos, métodos y ciclo de vida
 * Usamos data para definir las variables reactivas del componente
 * Usamos computed para definir propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
 * Usamos mounted para ejecutar código cuando el componente se monta en el DOM, como obtener la información del usuario logueado y su historial de notas
 * Usamos methods para definir funciones que realizan acciones específicas, como buscar estudiantes, generar PDF, etc.
 */
export default {
  /**
   * Data: Define las variables reactivas del componente, que se pueden usar en el template y en los métodos
   * @returns {Object} Objeto con las variables reactivas
   */
  data() {
    /**
     * Return: Devuelve un objeto con las variables reactivas del componente
     * baseUrl: URL base para las peticiones al backend
     * idpersona: Número que identifica al usuario logueado
     * nombreEstudiante: Nombre del estudiante logueado
     * cedulaEstudiante: Cédula del estudiante logueado
     * loading: Boolean que indica si se está cargando información del estudiante logueado
     * Persona: Objeto con la información del estudiante logueado
     * historial: Array con los datos de las calificaciones del estudiante
     * promedioGlobal: Número que representa el promedio general del historial
     */
    return {
      baseUrl: "/sistma",
      idpersona: null,
      nombreEstudiante: "",
      cedulaEstudiante: "",
      loading: true,
      Persona: {},
      historial: [],
      promedioGlobal: 0
    };
  },
  /**
   * mounted: Es un hook del ciclo de vida de Vue que se ejecuta cuando el componente se monta en el DOM. Se usa para inicializar datos y hacer peticiones al backend.
   * Se obtiene la información del usuario logueado con getMe(), se asignan los valores de idpersona y idusuario, se obtienen las informaciones de la persona y del usuario logueado, y se inicializa el estado cargando. 
   * Se utiliza async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
   */
  async mounted() {
    try {
      // Resuelve la identidad del usuario autenticado en la sesión actual.
      const me = await getMe();
      // Asigna el identificador único de la persona obtenido de los metadatos de la sesión.
      this.idpersona = me.id_persona;
      // Ejecuta peticiones concurrentes/secuenciales dependientes del ID de la persona.
      // Obtiene la información detallada del perfil (nombres, cédula, etc.).
      await this.getPersona();
      // Carga la sábana o historial completo de calificaciones del estudiante.
      await this.getHistorialNotas();
    } catch (error) {
      // Captura cualquier fallo crítico en la cadena de promesas (falla de red, token expirado, etc.).
      console.error("Error inicializando componente:", error);
      // Notifica visualmente al usuario que su sesión o los datos no pudieron ser validados.
      mostraralertas("Error al autenticar usuario", "error");
      // Apaga el indicador global de carga para permitir que la interfaz reaccione o muestre un estado de error vacio.
      this.loading = false;
    }
  },
  /**
   * methods: Define las funciones que realizan acciones específicas, como buscar estudiantes, generar PDF, etc. Se pueden llamar desde el template o desde otros métodos.
   * getPersona: Obtiene la información de la persona logueada desde el backend y la asigna a las variables reactivas correspondientes. Maneja errores de red o de datos. 
   * getHistorialNotas: Obtiene la información de la sábana o historial completo de calificaciones del estudiante desde el backend y la asigna a las variables reactivas correspondientes
   * formatNota: Formatea un valor numérico a dos decimales y lo devuelve como string. Si el valor no es un número, devuelve "0.00".
   * getBadgeEstado: Devuelve la clase CSS correspondiente al estado de la asignatura o curso.
   * generarPDFHistorial: Genera y descarga un documento PDF formal con el historial de calificaciones del estudiante.
   */
  methods: {
    /**
     * getPersona: Recupera el perfil detallado de la persona asociada al usuario autenticado desde el servidor.
     * Extrae, normaliza y concatena las propiedades de identidad necesarias para poblar la visualización en la UI.
     * @async Metodo asíncrono que devuelve una promesa
     * @returns {Promise<void>} No devuelve ningún valor, pero actualiza la UI y muestra alertas según el resultado de la operación.
     */
    async getPersona() {
      try {
        // Ejecuta una petición HTTP GET para obtener los datos de la persona por su ID único.
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Desestructuración defensiva / Fallback de respuesta: Resuelve la asignación si la API devuelve
        // el registro dentro de una colección indexada (array) o directamente como un objeto plano.
        this.Persona = res.data.data[0] || res.data.data;
        // Formatea e hidrata el nombre completo combinando campos, previniendo strings nulos ("null") 
        // y removiendo espacios remanentes en los extremos con la función .trim().
        this.nombreEstudiante = `${this.Persona.nombres || ''} ${this.Persona.apellidos || ''}`.trim();
        // Asigna la cédula de identidad o un valor de contingencia por defecto ('S/N') si está ausente en la base de datos.
        this.cedulaEstudiante = this.Persona.cedula || 'S/N';
      } catch (err) { 
        // Captura fallas de red o respuestas de error de la petición HTTP, registrándolas de forma interna en la consola.
        console.error(err); 
      }
    },
    /**
     * getHistorialNotas: Consulta y descarga la sábana o historial completo de calificaciones del estudiante desde la API.
     * Extrae la colección de periodos académicos cursados y el promedio general acumulado,
     * controlando de manera segura los estados de carga globales de la UI.
     * @async Metodo asíncrono que devuelve una promesa
     * @returns {Promise<void>} No devuelve ningún valor, pero actualiza la UI y muestra alertas según el resultado de la operación.
     */
    async getHistorialNotas() {
      // Inicialización de UI: Activa el spinner o pantalla de carga ("skeleton") para congelar interacciones pendientes.
      this.loading = true;
      try {
        // Consume el endpoint especializado de historial completo filtrando por el identificador de la persona.
        const res = await API.get(`${this.baseUrl}/historial-completo-est/${this.idpersona}`);
        // Distribuye los nodos de datos devueltos por el backend en el estado reactivo local.
        this.historial = res.data.historial;// Estructura ordenada de asignaturas, notas y periodos.
        this.promedioGlobal = res.data.promedio_general;// Calificación ponderada acumulativa a lo largo de su carrera académica.
      } catch (error) {
        // Captura excepciones de red, códigos de estado HTTP erróneos (4xx, 5xx) o tokens de sesión corruptos.
        console.error("Error obteniendo el historial:", error);
        // Despliega una alerta emergente notificando al estudiante la anomalía en la sincronización de sus notas.
        mostraralertas("No se pudo cargar el historial de notas", "error");
      } finally {
        // Bloque definitivo: Restablece de forma estricta el estado de carga a 'false' garantizando que se libere 
        // la interfaz visual tanto si los datos se procesaron correctamente como si el catch interceptó una falla.
        this.loading = false;
      }
    },
    /**
     * formatNota: Normaliza y formatea un valor numérico o cadena de texto que represente una calificación.
     * Asegura que el retorno cumpla estrictamente con el estándar tipográfico de dos decimales,
     * actuando como una red de seguridad (fallback) frente a valores corruptos o nulos.
     * @param {number|string} valor - El dato bruto de la calificación a ser procesado.
     * @returns {string} El valor normalizado y formateado como string con dos decimales.   
     */
    formatNota(valor) {
      // Intenta realizar un casteo explícito del parámetro hacia un tipo de dato numérico de punto flotante.
      const num = parseFloat(valor);
      // Validación de sanidad (Sanitization): Si el resultado es indeterminado (NaN) debido a caracteres, 
      // nulos o indefinidos, retorna el valor base "0.00" para no romper la consistencia visual de las celdas de la UI.
      // De lo contrario, formatea el número fijando rígidamente dos posiciones decimales.
      return isNaN(num) ? "0.00" : num.toFixed(2);
    },
    /**
     * getBadgeEstado: Asigna dinámicamente clases utilitarias de Bootstrap para las etiquetas de estado (Badges).
     * Transforma el estado textual de la materia en una especificación cromática que denota su urgencia o éxito.     
     * * @param {string} estado - El estado académico actual de la asignatura (ej. "APROBADO", "Supletorio").
     * @returns {string} Cadena de clases CSS que definen el color de fondo y de texto del componente visual. 
     */
    getBadgeEstado(estado) {
      // Cláusula de guarda: Si el estado es nulo, indefinido o vacío, aplica el estilo neutral gris por defecto.
      if(!estado) return "bg-secondary text-white";
      // Normalización defensiva: Convierte la cadena a minúsculas estrictas para evitar fallos si el backend 
      // envía variaciones de escritura como "Aprobado", "APROBADO" o "aprobado".
      switch (estado.toLowerCase()) {
        // Condición de éxito absoluto: Materia completada de forma satisfactoria.
        case "aprobado": return "bg-success text-white";// Fondo verde, texto blanco.
        // Condición de alerta/fallo académico: El estudiante no alcanzó la nota mínima.
        case "reprobado": return "bg-danger text-white";// Fondo rojo, texto blanco.
        // Agrupamiento lógico (Fall-through): Tanto los estados en tránsito ("en proceso") como las 
        // instancias de recuperación ("supletorio") comparten la misma urgencia visual preventiva.
        case "supletorio":
        case "en proceso": return "bg-warning text-dark";// Fondo amarillo/dorado, texto oscuro para garantizar contraste de accesibilidad.
        // Bloque de contingencia (Fallback): Si la API introduce un nuevo estado no mapeado en este switch.
        default: return "bg-secondary text-white";// Fondo gris, texto blanco.
      }
    },
    /**
     * generarPDFHistorial: Generates and downloads a unified academic record report in PDF format.
     * Transforms a nested array structure into a flattened, two-dimensional matrix,
     * using advanced AutoTable features like conditional row-spanning and col-spanning.
     * Includes explicit sanitization for document naming conventions.
     * Genera y descarga un reporte unificado del historial académico en formato PDF.
     * Transforma una estructura de arreglos anidados en una matriz bidimensional aplanada,
     * utilizando características avanzadas de AutoTable como combinación condicional de celdas (rowSpan y colSpan).
     * Incluye sanitización explícita para las convenciones de nomenclatura del documento descargable.
     * @returns {void} Direct file download injection into the client browser.
     */
    generarPDFHistorial() {
      // Inicializa el constructor de jsPDF configurando las dimensiones de la página (A4 estándar en milímetros).
      const doc = new jsPDF("p", "mm", "a4");

      // =========================================================
      // 1. ENCABEZADO INSTITUCIONAL / BANNER TOP
      // =========================================================
      doc.setFillColor(29, 42, 104);// Azul institucional corporativo.
      doc.rect(0, 0, 210, 22, "F");// Dibuja un rectángulo de ancho completo (210mm) actuando como franja superior.
      doc.setTextColor(244, 181, 36);// Dorado institucional de contraste.
      doc.setFont("Helvetica", "bold");
      doc.setFontSize(14);
      doc.text("HISTORIAL ACADÉMICO DE CALIFICACIONES", 14, 10);
      
      doc.setTextColor(255, 255, 255);// Texto blanco puro para el subtítulo del reporte.
      doc.setFontSize(9);
      doc.setFont("Helvetica", "normal");
      doc.text("Reporte Consolidado Estudiantil", 14, 16);

      // =========================================================
      // 2. METADATOS / BLOQUE INFORMACIÓN DEL ESTUDIANTE
      // =========================================================
      doc.setTextColor(0, 0, 0);// Restablece el color de la fuente a negro para los campos del cuerpo.
      doc.setFont("Helvetica", "bold");
      doc.text("Estudiante:", 14, 30);
      doc.text("Cédula / ID:", 140, 30);
      // Inyección de variables reactivas hidratadas previamente desde el estado global del componente.
      doc.setFont("Helvetica", "normal");
      doc.text(this.nombreEstudiante, 38, 30);
      doc.text(this.cedulaEstudiante, 162, 30);

      // =========================================================
      // 3. ESTRATEGIA ALGORÍTMICA: CONSTRUCCIÓN Y APLANAMIENTO DE MATRIZ (ROWSPAN)
      // =========================================================
      // Estructura bidimensional (Array de Arrays) requerida por el motor de AutoTable.
      const bodyData = [];
      // Procesa recursivamente la colección anidada: Cursos -> Asignaturas.
      this.historial.forEach(curso => {
        curso.asignaturas.forEach((asig, index) => {
          // CASO A: Primera asignatura del bloque del curso.
          // Aquí se inicializan las celdas maestras del periodo, asistencia y estado que abarcarán
          // verticalmente (rowSpan) el número total de asignaturas pertenecientes a este mismo nivel.
          if (index === 0) {
            bodyData.push([
              { content: `${curso.periodo}\n${curso.nivel} "${curso.paralelo}"\nEsp: ${curso.especialidad}`, rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center', fillColor: [248, 249, 250] } },
              asig.nombre,
              this.formatNota(asig.nota_final),
              asig.estado.toUpperCase(),
              { content: `${curso.asistencia_curso}%`, rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center' } },
              { content: curso.estado_curso.toUpperCase(), rowSpan: curso.asignaturas.length, styles: { valign: 'middle', halign: 'center', fontStyle: 'bold' } }
            ]);
          } 
          // CASO B: Asignaturas secuenciales del mismo curso.
          // Se omiten las celdas combinadas de los extremos para evitar colisiones lógicas en la grilla,
          // inyectando exclusivamente las columnas intermedias correspondientes a la materia actual.
          else {
            bodyData.push([
              asig.nombre,
              this.formatNota(asig.nota_final),
              asig.estado.toUpperCase()
            ]);
          }
        });
      });

      // =========================================================
      // 4. PIE DE TABLA / INYECCIÓN DE TOTALES HISTÓRICOS (COLSPAN)
      // =========================================================
      // Añade una fila especial de clausura al final de la matriz de datos.
      // Utiliza colSpan para unificar columnas e invierte la paleta institucional (fondo dorado, texto azul) para denotar jerarquía sintáctica.
      bodyData.push([
        { content: 'PROMEDIO GENERAL HISTÓRICO:', colSpan: 2, styles: { halign: 'right', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: this.formatNota(this.promedioGlobal), styles: { halign: 'center', fontStyle: 'bold', fillColor: [244, 181, 36], textColor: [29, 42, 104] } },
        { content: '', colSpan: 3, styles: { fillColor: [244, 181, 36] } }
      ]);
      // =========================================================
      // 5. COMPILACIÓN DE LA TABLA Y MAQUETACIÓN VISUAL (AUTOTABLE)
      // =========================================================
      autoTable(doc, {
        startY: 38,// Inicia la grilla justo por debajo del bloque de metadatos del estudiante.
        head: [['Periodo y Curso', 'Asignatura', 'Nota', 'Estado Mat.', 'Asist. Curso', 'Estado Curso']],
        body: bodyData,
        theme: 'grid',// Tema cuadriculado explícito necesario para apreciar correctamente las celdas combinadas por rowSpan.
        headStyles: { fillColor: [29, 42, 104], textColor: [255, 255, 255], fontSize: 8, halign: 'center' },
        styles: { fontSize: 8, cellPadding: 3, valign: 'middle' },// Alineación vertical media para celdas multi-línea.
        columnStyles: {
          0: { cellWidth: 45 },// Ancho reservado para el bloque combinado de información del curso.
          1: { cellWidth: 60 },// Espacio prioritario para el nombre completo de la asignatura.
          2: { halign: 'center', fontStyle: 'bold' },// Resalte visual de la calificación final.
          3: { halign: 'center' },// Centrado para estados ("APROBADO", "REPROBADO").
          4: { halign: 'center', cellWidth: 20 },
          5: { halign: 'center', cellWidth: 25 }
        }
      });
      // =========================================================
      // 6. SANITIZACIÓN DE CADENAS Y DISPARO DE DESCARGA
      // =========================================================
      // Reemplaza todos los espacios en blanco del nombre del alumno por guiones bajos usando expresiones regulares (RegEx).
      // Previene que los navegadores corruptores o sistemas operativos rompan el enlace o guarden archivos con nombres truncados.
      doc.save(`Historial_Notas_${this.nombreEstudiante.replace(/\s+/g, '_')}.pdf`);
    }
  }
};
</script>

<style scoped>
.text-blue { color: #1D2A68; }
.border-blue { border-color: #1D2A68 !important; }
.bg-blue { background-color: #1D2A68 !important; }
.text-gold { color: #F4B324; }
.bg-gold { background-color: #F4B324 !important; }
.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}
.btn-gold:hover {
  background-color: #e0a216;
  color: #1D2A68;
}
.fw-black { font-weight: 900; }

.custom-table-grades { border: 2px solid #1D2A68 !important; }
.header-double-level th {
  font-size: 0.9rem;
  letter-spacing: 0.5px;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  padding: 12px 6px;
}
.bg-blue-dark { background-color: #141f4f !important; }
.bg-attendance { background-color: #216d43 !important; }
.bg-status { background-color: #b78311 !important; }
.border-end-heavy { border-right: 3px solid #1D2A68 !important; }
.bg-gold-light-cell { background-color: rgba(244, 181, 36, 0.12) !important; }
.grade-row-hover:hover { background-color: rgba(29, 42, 104, 0.03); }
</style>
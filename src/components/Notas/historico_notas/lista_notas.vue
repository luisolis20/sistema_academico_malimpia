<template>
  <!-- Contenedor principal del componente -->
  <div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Cabecera del componente -->
    <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">
      <!-- FILA SUPERIOR: Títulos y Estado del Periodo -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">
        <!-- Título e Icono Principal -->
        <div class="mb-2 mb-md-0 d-flex align-items-center">
          <!-- Icono -->
          <div
            class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3 min-vw-auto"
            style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px; min-width: 55px;">
            <i class="fas fa-clipboard-list fs-4"></i>
          </div>
          <!-- Título --> 
          <div>
            <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
              Histórico de Notas del Estudiante
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
              Ingrese la cédula del estudiante para buscar su historial completo de calificaciones.
            </p>
          </div>
        </div>
        <!-- Contenedor de la barra de búsqueda --> 
        <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0" style="max-width: 450px; width: 100%;">
          <div class="input-group shadow-sm rounded-pill overflow-hidden border"
            style="border-color: rgba(29, 42, 104, 0.2) !important;">
            <input type="text" class="form-control border-0 px-4 py-2" placeholder="Ej. 0801234567"
              v-model="cedulaBusqueda" @keyup.enter="buscarEstudiante" :disabled="buscando" style="box-shadow: none;">
            <button class="btn fw-bold px-4 border-0 d-flex align-items-center transition-all" @click="buscarEstudiante"
              :disabled="!cedulaBusqueda || buscando" style="background-color: #F4B324; color: #1D2A68;">
              <span v-if="buscando" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-search me-2"></i> Buscar
            </button>
          </div>
        </div>
      </div>
      <!-- Contenedor del contenido, texto inicial informativo-->  
      <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
        style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
        <i class="fas fa-database fs-5 mt-1" style="color: #F4B324;"></i>
        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Consolidación Longitudinal e Inmutabilidad de Datos:</strong> Este motor de búsqueda ejecuta consultas
          relacionales profundas para unificar el récord académico del estudiante a través del tiempo. Extrae las
          métricas de rendimiento de periodos cerrados (actas finales), lo que garantiza la estricta inmutabilidad de la
          información pasada ante cualquier intento de alteración. Esta estructura es vital para dotar al sistema de la
          fiabilidad necesaria al momento de generar mallas de promoción, reportes analíticos y la emisión de
          certificados oficiales.
        </p>
      </div>

    </header>
    <!-- Contenedor del cuerpo, solo se muestra si hay notas estudiantes -->
    <div v-if="estudiante" class="row g-4">
      <!-- Contenedor de cada fila de notas, se usa el evento click para abrir el modal de generación de certificado -->
      <div class="col-lg-4 col-md-5">
        <!-- Contenedor del componente -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border-top border-blue border-4">
          <!-- Contenedor del cuerpo -->
          <div class="card-body text-center p-4">
            <!-- Contenedor de la foto del estudiante -->
            <div class="mb-3 position-relative d-inline-block">
              <!-- Imagen del estudiante, usamos el método getPhotoUrl para obtener la URL de la imagen -->
              <img :src="estudiante.foto ? 'data:image/jpeg;base64,' + estudiante.foto : '/sistema/default-avatar.png'"
                class="rounded-circle border border-3 border-gold shadow-sm"
                style="width: 110px; height: 110px; object-fit: cover;">
            </div>
            <!-- Nombres y apellidos del estudiante -->
            <h5 class="fw-bold text-blue mb-1">{{ estudiante.nombres }} {{ estudiante.apellidos }}</h5>
            <!-- Etiqueta de estado del estudiante -->
            <p class="badge bg-blue text-white rounded-pill px-3 py-1 mb-3">Estudiante Regular</p>
            <!-- Contenedor de la información -->
            <div class="text-start border-top pt-3 mt-2">
              <p class="mb-2 text-muted small"><i class="fas fa-id-card me-2 text-gold"></i> <strong>Cédula:</strong> {{
                estudiante.cedula }}</p>
              <p class="mb-0 text-muted small"><i class="fas fa-phone me-2 text-gold"></i> <strong>Teléfono:</strong> {{
                estudiante.telefono || 'Sin registro' }}</p>
            </div>
          </div>
        </div>
        <!-- Contenedor del componente, muestra el progreso académico del estudiante -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
          <!-- Contenedor del encabezado -->
          <h6 class="fw-bold text-blue mb-3 pb-2 border-bottom">
            <!-- Icono + Texto -->
            <i class="fas fa-graduation-cap me-2 text-gold"></i> Ruta de Progreso Académico
          </h6>
          <!-- Contenedor del contenido -->
          <div class="position-relative ps-2">
            <!-- Línea de tiempo -->
            <div class="timeline-line"></div>
            <!-- Contenedor de cada nivel de la ruta de progreso -->
            <div v-for="nivel in nivelesVisibles" :key="nivel.id_nivel"
              class="d-flex align-items-center mb-3 position-relative timeline-item"
              :class="{ 'opacity-50': nivel.estado === 'PENDIENTE' }">
              <!-- Contenedor del componente -->
              <div class="timeline-badge me-3 shadow-sm d-flex align-items-center justify-content-center"
                :class="getNivelBadgeClass(nivel.estado)">
                <i :class="getNivelIconClass(nivel.estado)"></i>
              </div>
              <!-- Contenedor del contenido -->
              <div>
                <!-- Nombre del nivel -->
                <p class="mb-0 fw-bold small text-blue">{{ nivel.nombre }}</p>
                <!-- Texto de estado -->
                <small class="x-small text-uppercase fw-semibold" :class="getNivelTextClass(nivel.estado)">
                  {{
                    nivel.estado === 'APROBADO' ? 'Aprobado ✓' :
                      (nivel.estado === 'REPITE' ? 'Repite Nivel ⚠' :
                        (nivel.estado === 'CURSANDO' ? 'Cursando Actual' : 'Próximo Nivel'))
                  }}
                </small>
              </div>
            </div>
          </div>
        </div>
        <!-- Botón para generar el certificado PDF -->
        <div class="mt-4 d-grid">
          <button @click="generarPDF" class="btn btn-outline-blue fw-bold btn-lg rounded-pill shadow-sm">
            <i class="fas fa-file-pdf me-2 text-danger"></i> Generar Certificado PDF
          </button>
        </div>
      </div>
      <!-- Contenedor de cada fila de notas, se usa el evento click para abrir el modal de generación de certificado -->
      <div class="col-lg-8 col-md-7">
        <div v-for="periodoMatriculado in historial" :key="periodoMatriculado.id_matricula"
          class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-gold border-5">
          <!-- Contenedor del cuerpo -->
          <div class="bg-blue text-white p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <!-- Contenedor del encabezado -->
            <div>
              <!-- Contenedor del encabezado -->
              <h6 class="mb-0 fw-bold">
                <!-- Icono + Texto -->
                <i class="fas fa-calendar-alt me-2 text-gold"></i> Periodo: {{ periodoMatriculado.periodo }}
              </h6>
              <!-- Texto -->
              <small class="text-white-50">
                Curso: {{ periodoMatriculado.nivel_nombre }} "{{ periodoMatriculado.paralelo }}"
                <span v-if="periodoMatriculado.especialidad"> | Especialidad: {{ periodoMatriculado.especialidad
                }}</span>
              </small>
            </div>
            <!-- Contenedor de la información -->
            <div class="d-flex align-items-center gap-2">
              <!-- Contenedor del componente -->
              <span class="badge bg-white bg-opacity-25 border border-light px-3 py-2 rounded-pill"
                title="Asistencia General del Periodo">
                <!-- Icono + Texto -->
                <i class="fas fa-user-clock me-1 text-gold"></i> Asistencia: {{ periodoMatriculado.porcentaje_asistencia
                }}%
              </span>
              <!-- Contenedor del componente -->
              <span class="badge rounded-pill px-3 py-2 border border-light"
                :class="periodoMatriculado.estado_curso === 'APROBADO' ? 'bg-success' : 'bg-danger'">
                {{ periodoMatriculado.estado_curso }}
              </span>
            </div>
          </div>
          <!-- Contenedor de la tabla -->
          <div class="table-responsive">
            <!-- Contenedor de la tabla -->
            <table class="table table-hover table-striped align-middle mb-0 text-center">
              <!-- Contenedor de la cabecera -->
              <thead class="table-light border-bottom">
                <!-- Contenedor de la cabecera -->
                <tr>
                  <th class="text-start ps-4 text-blue py-3">Asignatura / Componente Curricular</th>
                  <th class="text-blue py-3" style="width: 140px;">Nota Final</th>
                  <th class="text-blue py-3" style="width: 160px;">Estado de Rendimiento</th>
                </tr>
              </thead>
              <!-- Contenedor de las filas -->
              <tbody>
                <!-- Elemento del cuerpo, se usa el evento click para abrir el modal de generación de certificado -->
                <tr v-for="materia in periodoMatriculado.calificaciones" :key="materia.asignatura">
                  <!-- Contenedor del cuerpo, muestra la asignatura -->
                  <td class="text-start ps-4 fw-bold text-blue-light">{{ materia.asignatura }}</td>
                  <!-- Contenedor del cuerpo, muestra la nota final -->
                  <td class="fw-bold fs-6">{{ materia.nota_final.toFixed(2) }}</td>
                  <!-- Contenedor del cuerpo, muestra el estado de la materia -->
                  <td>
                    <span class="badge px-3 py-1.5 rounded-pill"
                      :class="materia.estado === 'APROBADO' || materia.estado === 'Aprobado' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'">
                      <i class="fas me-1"
                        :class="materia.estado === 'APROBADO' || materia.estado === 'Aprobado' ? 'fa-check' : 'fa-times'"></i>
                      {{ materia.estado }}
                    </span>
                  </td>
                </tr>
                <!-- Elemento del cuerpo, muestra el promedio general -->
                <tr class="table-gold-light fw-bold border-top border-blue border-2">
                  <td class="text-end pe-4 text-blue">PROMEDIO GENERAL GENERAL:</td>
                  <td class="text-blue fs-5 fw-black text-center border-bottom border-blue border-2">
                    {{ periodoMatriculado.promedio_general.toFixed(2) }}
                  </td>
                  <td class="text-blue-light small text-start ps-3 align-middle">
                    Promedio acumulado del nivel
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <!-- Contenedor del componente, solo se muestra si no hay datos -->
    <div v-else-if="!buscando" class="text-center py-5">
      <!-- Contenedor del texto -->
      <div class="text-muted mb-3">
        <i class="fas fa-folder-open fa-4x text-opacity-25 text-blue"></i>
      </div>
      <!-- Contenedor del texto -->
      <h5 class="text-blue fw-bold">No hay datos que mostrar</h5>
      <!-- Contenedor del texto -->
      <p class="text-muted small">Realice la búsqueda mediante una cédula válida para estructurar el expediente
        académico.</p>
    </div>
  </div>
</template>

<script>
/**
 * lista_notas es un componente en el que se encuentra toda la lógica de la aplicación, para realizar la búsqueda de notas del estudiante.
 * Importamos la API de Axios para realizar peticiones al backend
 * Importamos la función mostraralertas para mostrar mensajes de alerta
 * Importamos la librería jsPDF para generar el PDF
 * Importamos la librería autoTable para generar la tabla de notas
 */
import API from "@/assets/js/axios";// Importa la API de Axios
import { mostraralertas } from "@/assets/js/funciones/functions";// Importa la función para mostrar alertas
import { jsPDF } from "jspdf";// Importa la librería jsPDF
import autoTable from "jspdf-autotable";// Importa la librería autoTable
/**
 * Exporta el componente Vue con su configuración, datos, métodos y ciclo de vida
 * Usamos data para definir las variables reactivas del componente
 * Usamos computed para definir propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
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
     * cedulaBusqueda: Cadena de texto que contiene la cédula del estudiante buscado
     * buscando: Boolean que indica si se está buscando información del estudiante
     * estudiante: Objeto con la información del estudiante buscado
     * historial: Array con los datos de las calificaciones del estudiante
     * nivelesSistema: Array con los niveles del sistema
     */
    return {
      baseUrl: "/sistma", 
      cedulaBusqueda: "",
      buscando: false,
      estudiante: null,
      historial: [],
      nivelesSistema: []
    }
  },
  /**
   * Computed: Define propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
   * nivelesVisibles: Retorna un array con los niveles visibles del sistema, solo se muestran los niveles que tienen datos
   * @returns {Array} Array con los niveles visibles del sistema
   */
  computed: {
    /**
     * nivelesVisibles: Filtra y calcula la visibilidad secuencial de los niveles académicos de un estudiante.
     * Procesa la malla curricular basándose en el historial académico, determinando qué niveles 
     * ya cursó (aprobados/reprobados), cuál está cursando y cuál es el próximo nivel inmediato disponible.
     * @returns {Array<Object>} Listado depurado de niveles académicos habilitados para visualización en la interfaz.
     */
    nivelesVisibles() {
      // Cláusula de guarda: Si el sistema de niveles o el historial del estudiante están vacíos, detiene el proceso.
      if (!this.nivelesSistema.length || !this.historial.length) return [];

      /**
       * Helper / Función utilitaria local para extraer de forma segura los números iniciales de un varchar.
       * Evita fallos de ordenamiento si la jerarquía viene con caracteres alfanuméricos desde la base de datos.
       * @param {string} str - Cadena de texto que representa el orden jerárquico.
       * @returns {number} Valor entero de la jerarquía o 0 en caso de error/nulo.
       */
      const parsearJerarquia = (str) => {
        if (!str) return 0;
        const numero = parseInt(str, 10);
        return isNaN(numero) ? 0 : numero;
      };

      // Inmutabilidad: Clonamos el array original con spread operator y ordenamos de menor a mayor según su jerarquía.
      const nivelesOrdenados = [...this.nivelesSistema].sort((a, b) => {
        return parsearJerarquia(a.orden_jerarquia) - parsearJerarquia(b.orden_jerarquia);
      });

      // Indexación (Lookup Map): Mapeamos el historial a un objeto clave-valor para búsquedas eficientes con coste constante O(1).
      const mapaHistorial = {};
      this.historial.forEach(h => {
        mapaHistorial[h.nivel_id] = h.estado_curso;
      });
      // Variables de control de estado del flujo académico
      let maxOrdenAprobado = -1;// Registra el nivel más alto completado con éxito
      let tieneNivelActivo = false;// Bandera para saber si el estudiante tiene una carga en curso o estancada

      // Máquina de estados: Evaluamos y asignamos el estado lógico correspondiente a cada nodo/nivel del sistema
      const listaProcesada = nivelesOrdenados.map(nivel => {
        let estado = 'PENDIENTE';// Estado base por defecto
        const ordenNumerico = parsearJerarquia(nivel.orden_jerarquia);// Obtenemos el número de orden del nivel
        // Si el nivel actual existe dentro del historial registrado del estudiante
        if (mapaHistorial[nivel.id_nivel]) {
          const estadoBD = mapaHistorial[nivel.id_nivel];// Obtenemos el estado del nivel del historial
          // Si el nivel está aprobado
          if (estadoBD === 'APROBADO') {
            estado = 'APROBADO';// Asignamos el estado aprobado
            maxOrdenAprobado = Math.max(maxOrdenAprobado, ordenNumerico);// Actualizamos el nivel máximo aprobado
          } else if (estadoBD === 'REPROBADO') {
            // Si el nivel está reprobado, se asigna el estado reprobado
            tieneNivelActivo = true; // Bandera para saber si el estudiante tiene una carga en curso o estancada
            estado = 'REPITE'; // Nuevo estado asignado si reprobó
          } else {
            // Si el nivel está en curso, se asigna el estado en curso
            tieneNivelActivo = true;// Bandera para saber si el estudiante tiene una carga en curso o estancada
            estado = 'CURSANDO';// Nuevo estado asignado si en curso
          }
        }
        // Devolvemos el objeto con el estado lógico y el número de orden del nivel
        return { ...nivel, ordenNumerico, estado };
      });

      // Lógica de Desbloqueo: Si el alumno no tiene materias activas o trancadas, buscamos el siguiente paso en la malla curricular
      let proximoNivelId = null;
      if (!tieneNivelActivo) {
        // Encuentra el primer nivel pendiente cuyo orden sea estrictamente superior al último aprobado
        const siguiente = listaProcesada.find(n => n.ordenNumerico > maxOrdenAprobado && n.estado === 'PENDIENTE');
        // Si se encuentra un siguiente nivel, se asigna el ID del mismo
        if (siguiente) {
          proximoNivelId = siguiente.id_nivel;// Asignamos el ID del siguiente nivel
        }
      }
      // Filtro final de Renderizado: Retorna todo lo que ya interactuó con el estudiante (aprobado/cursando/repite)
      // y estrictamente el único nivel superior inmediato disponible (para evitar que vea niveles avanzados bloqueados).
      return listaProcesada.filter(n => n.estado !== 'PENDIENTE' || n.id_nivel === proximoNivelId);
    }
  },
  /**
   * Methods: Define los métodos que se pueden llamar desde el template o desde otros métodos
   * Los método utilizados en este componente son:
   * buscarEstudiante: Método para buscar información del estudiante en la base de datos
   * getNivelBadgeClass: Método para obtener la clase CSS del badge según el estado del nivel
   * getNivelIconClass: Método para obtener la clase CSS del icono según el estado del nivel
   * getNivelTextClass: Método para obtener la clase CSS del texto según el estado del nivel
   * generarPDF: Método para generar el PDF del historial académico del estudiante
   */
  methods: {
    /**
     * buscarEstudiante: Realiza una consulta asíncrona a la API para buscar la información de un estudiante en particular.
     * @async Metodo asíncrono que devuelve una promesa
     * @returns {Promise<void>} No devuelve ningún valor, pero actualiza la UI y muestra alertas según el resultado de la operación.
     */
    async buscarEstudiante() {
      // Cláusula de guarda: Si el campo de búsqueda está vacío, aborta la operación de forma inmediata.
      if (!this.cedulaBusqueda) return;
      // Control de estado de la UI: Activa el indicador de carga.
      this.buscando = true;
      // Limpieza preventiva de estado: Resetea los datos previos para evitar incongruencias visuales 
      // o persistencia de datos obsoletos ("stale data") mientras se procesa la nueva consulta.
      this.estudiante = null;
      this.historial = [];

      try {
        // Ejecuta la petición HTTP GET apuntando al endpoint del histórico de notas.
        const response = await API.get(`${this.baseUrl}/historico_notas/${this.cedulaBusqueda}`);
        // Si la respuesta del servidor es exitosa, distribuye la información en las propiedades reactivas.
        if (response.data.status === "success") {
          this.estudiante = response.data.estudiante;// Asigna el valor de response.data.estudiante a la propiedad estudiante
          this.historial = response.data.historial;// Asigna el valor de response.data.historial a la propiedad historial
          this.nivelesSistema = response.data.niveles_sistema;// Asigna el valor de response.data.niveles_sistema a la propiedad nivelesSistema
        }
      } catch (error) {
        // Captura el mensaje de error controlado enviado por el backend mediante encadenamiento opcional (?.).
        // Si la API no responde o el error es de red, se aplica un mensaje de respaldo (fallback).
        const msg = error.response?.data?.message || "Error al conectar con el servidor.";
        // Despliega una alerta visual notificando el fallo de la operación.
        mostraralertas(msg, "error");
      } finally {
        // Bloque de garantía: Asegura el apagado del spinner/indicador de carga sin importar 
        // si la petición culminó con éxito o falló estrepitosamente.
        this.buscando = false;
      }
    },
    /**
     * getNivelBadgeClass: Mapea el estado académico de un nivel a clases utilitarias de CSS (Bootstrap / Custom).
     * Se utiliza para renderizar de forma condicional las etiquetas visuales (badges) en la UI.
     * @param {'APROBADO'|'REPITE'|'CURSANDO'|'PENDIENTE'} estado - El estado lógico del nivel académico.
     * @returns {string} Cadena de clases CSS listas para ser inyectadas mediante v-bind:class o :class.
     */
    getNivelBadgeClass(estado) {
      // Estado: El estudiante completó y aprobó con éxito todas las materias de este nivel.
      if (estado === 'APROBADO') return 'bg-success text-white';
      // Estado: El estudiante reprobó el nivel y requiere cursarlo/cursándolo nuevamente.
      if (estado === 'REPITE') return 'bg-danger text-white';
      // Estado: El nivel se encuentra actualmente activo en el periodo vigente del estudiante.
      if (estado === 'CURSANDO') return 'bg-gold text-blue';
      // Estado por defecto (generalmente 'PENDIENTE'): Nivel bloqueado o en cola dentro de la malla.
      // Aplica opacidad para denotar visualmente un estado desactivado o grisáceo.
      return 'bg-secondary bg-opacity-25 text-muted';
    },
    /**
     * getNivelIconClass: Mapea el estado académico de un nivel a clases utilitarias de CSS (Bootstrap / Custom).
     * Determina el símbolo visual que acompaña a la etiqueta del nivel para mejorar la accesibilidad y UX.
     * @param {'APROBADO'|'REPITE'|'CURSANDO'|'PENDIENTE'} estado - El estado lógico del nivel académico.
     * @returns {string} Cadena de clases de Font Awesome lista para inyectarse en etiquetas de icono (ej. `<i :class="..."></i>`). 
     */
    getNivelIconClass(estado) {
      // Símbolo de verificación/check encerrado en un círculo para niveles culminados con éxito.
      if (estado === 'APROBADO') return 'fas fa-check-circle';
      // Símbolo de flecha circular que denota la acción de reintentar o recursar el nivel.
      if (estado === 'REPITE') return 'fas fa-redo-alt'; 
      // Símbolo de carga en movimiento continuo (animación de rotación) para reflejar progreso activo.
      if (estado === 'CURSANDO') return 'fas fa-spinner fa-spin';
      // Símbolo de candado cerrado por defecto para niveles bloqueados o que aún no se han desbloqueado.
      return 'fas fa-lock';
    },
    /**
     * getNivelTextClass: Mapea el estado académico de un nivel a clases utilitarias de CSS (Bootstrap / Custom).
     * Determina el color del texto que acompaña a la etiqueta del nivel para mejorar la accesibilidad y UX.
     * @param {'APROBADO'|'REPITE'|'CURSANDO'|'PENDIENTE'} estado - El estado lógico del nivel académico.
     * @returns {string} Cadena de clases CSS listas para ser inyectadas mediante v-bind:class o :class.
     */
    getNivelTextClass(estado) {
      // Color verde para destacar texto de niveles superados de forma limpia.
      if (estado === 'APROBADO') return 'text-success';
      // Color rojo de alerta para denotar de forma explícita la condición de repetición.
      if (estado === 'REPITE') return 'text-danger';
      // Combinación de clases de advertencia y color personalizado (gold) para el estado activo.
      if (estado === 'CURSANDO') return 'text-warning text-gold';
      // Tono gris atenuado por defecto para elementos pendientes, minimizando su peso visual.
      return 'text-muted';
    },
    /**
     * generarPDF: Genera y descarga un documento PDF formal con el expediente histórico de calificaciones del estudiante.
     * Configura un diseño institucional estandarizado con maquetación de coordenadas mm, control de asincronía
     * para recursos gráficos, tablas dinámicas autogeneradas (AutoTable) con paginación integrada y pie de firma institucional.
     * @async Metodo asíncrono que devuelve una promesa
     * @returns {Promise<void>} No devuelve ningún valor, Descarga directa del archivo PDF en el navegador del cliente.
     */
    async generarPDF() {
      // Inicializa el constructor de jsPDF configurando parámetros físicos de impresión estándar (A4 en milímetros).
      const doc = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4"
      });

      // Definición de constantes cromáticas basadas en la paleta RGB institucional de la marca.
      const blue = [29, 42, 104];  // #1D2A68
      const gold = [244, 179, 36];  // #F4B324
      // Puntero de control vertical (Cursor Y) utilizado para posicionar dinámicamente los elementos y evitar solapamientos.
      let yOffset = 20;

      // =========================================================
      // 1. ENCABEZADO INSTITUCIONAL DINÁMICO
      // =========================================================
      const xLogo = 172;// Posición X fija para el inicio del logotipo.
      const anchoLogo = 23;// Dimensión horizontal asignada al logotipo.
      const xTextoInicio = 22;// Margen izquierdo para el bloque tipográfico del encabezado.
      const anchoMaxTexto = xLogo - xTextoInicio - 4;// Espacio horizontal disponible para el texto antes de colisionar con el logo.
      
      doc.setFont("helvetica", "bold");// Fuente institucional para títulos y encabezados.
      doc.setFontSize(14);// Tamaño de fuente ligeramente mayor para resaltar el nombre de la institución.
      doc.setTextColor(...blue);// Uso de operador spread para expandir el array RGB.

      const nombreInstitucion = 'UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO "MALIMPIA"';// Nombre completo de la institución educativa, se ajustará automáticamente al ancho disponible.
      // Ajusta y fragmenta el texto largo en un array de líneas según el ancho máximo permitido para evitar desbordes.
      const lineasNombre = doc.splitTextToSize(nombreInstitucion, anchoMaxTexto);

      let runningY = yOffset + 4;// Cursor interno temporal para la escritura secuencial del nombre.
      // Escribe cada línea del nombre en el documento.
      lineasNombre.forEach((linea) => {
        doc.text(linea, xTextoInicio, runningY);// Escribe la línea en el documento.
        runningY += 6;// Incrementa el cursor interno para la siguiente línea.
      });
      // Configuración del subtítulo del sistema de gestión.  
      doc.setFontSize(9);// Tamaño de fuente reducido para el subtítulo.
      doc.setTextColor(...gold);// Uso de operador spread para expandir el array RGB.
      doc.text("SISTEMA DE GESTIÓN DE HISTORIAL ACADÉMICO", xTextoInicio, runningY + 2);// Escribe el subtítulo en el documento.

      runningY += 8;// Incrementa el cursor interno para la siguiente sección del encabezado.
      // Renderizado del elemento gráfico ornamental izquierdo (Franja azul vertical de marca).
      const altoFranjaDecorativa = runningY - yOffset;
      doc.setFillColor(...blue);// Color de relleno de la franja.
      doc.rect(15, yOffset, 4, altoFranjaDecorativa, 'F');// 'F' indica modo Fill (Relleno sólido).
      // Flujo asíncrono controlado para la inyección del recurso gráfico (Logotipo).
      try {
        const logo = new Image();// Crear instancia de Image para cargar el logotipo.
        logo.src = '/mile.png';// Ruta de acceso al logotipo.
        // Flujo asíncrono controlado para la inyección del recurso gráfico (Logotipo).
        await new Promise((resolve, reject) => {
          logo.onload = resolve;
          logo.onerror = reject;
        });
        // Inyección del recurso gráfico (Logotipo) en el documento.
        doc.addImage(logo, 'PNG', xLogo, yOffset - 1, anchoLogo, anchoLogo);
      } catch (error) {
        // Captura de errores controlados para la inyección del recurso gráfico (Logotipo).
        console.warn("No se pudo cargar el logotipo mile.png para el PDF.", error);
      }
      // Sincronización del cursor vertical general tomando el punto más bajo entre el texto procesado y la altura del logo.
      yOffset = Math.max(runningY, yOffset + anchoLogo) + 4;
      // Renderizado de línea divisoria horizontal decorativa.
      doc.setDrawColor(...blue);
      doc.setLineWidth(0.5);
      doc.line(15, yOffset, 195, yOffset);
      // Título del reporte de calificaciones.
      yOffset += 8;
      doc.setFont("helvetica", "bold");
      doc.setFontSize(12);
      doc.setTextColor(...blue);
      doc.text("EXPEDIENTE HISTÓRICO DE CALIFICACIONES GENERALES", 15, yOffset);

      // =========================================================
      // 2. BLOQUE DE DATOS PERSONALES DEL ESTUDIANTE
      // =========================================================
      yOffset += 6;
      doc.setFillColor(248, 249, 250); // Fondo gris claro estructural (Bootstrap Light).
      doc.rect(15, yOffset, 180, 20, 'F'); // 'F' indica modo Fill (Relleno sólido).
      doc.setDrawColor(220, 224, 230);// Color de borde gris claro estructural (Bootstrap Light).
      doc.setLineWidth(0.3);// Ancho de línea de borde.
      doc.rect(15, yOffset, 180, 20, 'S');// 'S' indica modo Stroke (Bordes con relleno).

      // Renderizado estructurado de metadatos del estudiante dentro del recuadro contenedor.
      doc.setFont("helvetica", "bold");
      doc.setFontSize(9);
      doc.setTextColor(...blue);
      doc.text("Estudiante:", 20, yOffset + 7);
      doc.setFont("helvetica", "normal");
      doc.setTextColor(60, 60, 60); // Color neutro oscuro para legibilidad de datos variables.
      doc.text(`${this.estudiante.nombres} ${this.estudiante.apellidos}`, 40, yOffset + 7);

      doc.setFont("helvetica", "bold");
      doc.setTextColor(...blue);
      doc.text("Cédula Num:", 20, yOffset + 14);
      doc.setFont("helvetica", "normal");
      doc.text(`${this.estudiante.cedula}`, 40, yOffset + 14);

      doc.setFont("helvetica", "bold");
      doc.setTextColor(...blue);
      doc.text("Teléfono:", 115, yOffset + 14);
      doc.setFont("helvetica", "normal");
      doc.text(`${this.estudiante.telefono || 'Sin registro'}`, 132, yOffset + 14);

      yOffset += 28;

      // =========================================================
      // 3. GENERACIÓN DE TABLAS DINÁMICAS POR PERIODO CON AUTO-TABLE
      // =========================================================
      this.historial.forEach((periodo, index) => {
        // Control manual de desborde de página: Si el espacio libre es inferior a 50mm (umbral crítico),
        // realiza un salto preventivo de página para evitar tablas huérfanas o cortadas drásticamente.
        if (yOffset > 240) {
          doc.addPage();
          yOffset = 20; // Reinicia el puntero al margen superior de la nueva página.
        }
        // Renderizado del banner/subcabecera del periodo académico actual.
        doc.setFillColor(...blue);
        doc.rect(15, yOffset, 180, 7, 'F');

        doc.setFont("helvetica", "bold");
        doc.setFontSize(8.5);
        doc.setTextColor(255, 255, 255);

        // Inyección de información analítica de control (Identificadores, asistencias y estados globales).
        doc.text(`PERIODO: ${periodo.periodo} | CURSO: ${periodo.nivel_nombre} "${periodo.paralelo}"`, 18, yOffset + 4.5);
        doc.text(`ASISTENCIA: ${periodo.porcentaje_asistencia}%  |  ESTADO: ${periodo.estado_curso}`, 130, yOffset + 4.5);
        
        // Mapeo y transformación de la colección de asignaturas en filas aptas para la estructura de AutoTable
        const rows = periodo.calificaciones.map(m => [
          m.asignatura,
          m.nota_final.toFixed(2),// Forzado riguroso a dos decimales para reportes financieros/académicos
          m.estado
        ]);
        // Ejecución del plugin autoTable para construir la grilla de calificaciones de manera automatizada.
        autoTable(doc, {
          startY: yOffset + 7,// Posición inicial de la grilla en el documento.
          head: [['Asignatura / Componente Curricular', 'Nota Final', 'Estado']],// Cabecera de la tabla
          body: rows,// Cuerpo de la tabla con datos dinámicos
          foot: [['PROMEDIO GENERAL GENERAL:', periodo.promedio_general.toFixed(2), '']],// Pie de página con promedio general
          theme: 'striped',// Estilo de color de fondo de la tabla
          // Estilos de la cabecera de la tabla
          headStyles: {
            fillColor: blue,
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center'
          },
          // Estilos de las columnas de la tabla
          columnStyles: {
            0: { halign: 'left', cellWidth: 105 },
            1: { halign: 'center', cellWidth: 35 },
            2: { halign: 'center', cellWidth: 40 }
          },
          // Estilos de la pie de página de la tabla
          footStyles: {
            fillColor: [253, 248, 233],
            textColor: blue,
            fontStyle: 'bold',
            halign: 'center'
          },
          // Interceptor del ciclo de renderizado de celdas: Formatea la alineación horizontal de la fila del pie de página.
          didParseCell: function (data) {
            if (data.section === 'foot' && data.column.index === 0) {
              data.cell.styles.halign = 'right';// Desplaza la etiqueta del promedio hacia la derecha para colindar con su número.
            }
          },
          margin: { left: 15, right: 15 },
          styles: { font: "helvetica", fontSize: 8.5, cellPadding: 2 }
        });
        // Actualiza el cursor Y global leyendo dinámicamente la posición donde finalizó la tabla actual,
        // agregando un margen de separación de 12mm antes de evaluar el próximo periodo.
        yOffset = doc.lastAutoTable.finalY + 12;
      });

      // =========================================================
      // 4. SECCIÓN DE FIRMAS AL FINAL DEL DOCUMENTO
      // =========================================================
      // Evaluación del cuadrante inferior: Si no hay suficiente espacio para la línea de firmas,
      // se empuja el bloque entero a una página de cierre limpia.
      if (yOffset > 245) {
        doc.addPage();
        yOffset = 30;
      } else {
        yOffset += 5;
      }
      // Renderizado de la línea horizontal de firma digital/física.
      doc.setDrawColor(...blue);
      doc.setLineWidth(0.4);
      doc.line(65, yOffset + 15, 145, yOffset + 15);

      // Tipografía y textos de responsabilidad institucional del firmante.
      doc.setFont("helvetica", "bold");
      doc.setFontSize(9);
      doc.setTextColor(...blue);
      doc.text("f. Secretaria General", 105, yOffset + 20, { align: "center" });

      doc.setFont("helvetica", "normal");
      doc.setFontSize(8);
      doc.setTextColor(110, 110, 110);
      doc.text("Responsable de Registro y Control Académico", 105, yOffset + 24, { align: "center" });
      doc.text('Unidad Educativa Estandarizada del Milenio "Malimpia"', 105, yOffset + 28, { align: "center" });

      // Desencadena el guardado binario y abre la interfaz de descarga de archivos del sistema operativo cliente.
      doc.save(`Historial_Notas_${this.estudiante.cedula}.pdf`);
    }
  }
}
</script>

<style scoped>
/* PALETA DE COLORES */
.text-blue {
  color: #1D2A68;
}

.text-blue-light {
  color: #2c3e8c;
}

.text-gold {
  color: #F4B324;
}

.bg-blue {
  background-color: #1D2A68;
}

.border-gold {
  border-color: #F4B324 !important;
}

.bg-gold {
  background-color: #F4B324;
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
  border: 2px solid #1D2A68;
  color: #1D2A68;
  background-color: transparent;
  transition: all 0.25s ease-in-out;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}

.table-gold-light {
  background-color: rgba(244, 181, 36, 0.08);
}

.fw-black {
  font-weight: 900;
}

/* LÓGICA ESTRUCTURAL DE LA LÍNEA DE TIEMPO DEL PROGRESO */
.timeline-line {
  position: absolute;
  left: 17px;
  top: 15px;
  bottom: 15px;
  width: 3px;
  background-color: #e9ecef;
  z-index: 1;
}

.timeline-item {
  z-index: 2;
}

.timeline-badge {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 0.95rem;
  background: #fff;
  z-index: 3;
}

.x-small {
  font-size: 0.7rem;
  letter-spacing: 0.5px;
}

/* REGLAS DE MEDIOS EXCLUSIVAS PARA IMPRESIÓN Y EXPORTACIÓN A PDF */
@media print {

  /* Ocultar elementos de navegación innecesarios */
  .no-print,
  header,
  .no-print *,
  btn,
  button {
    display: none !important;
  }

  /* Resetear fondos y forzar renderizado de color total en PDF */
  body,
  .container-fluid,
  .min-vh-100 {
    background-color: #ffffff !important;
    color: #000000 !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Ajuste de columnas para ocupar el ancho completo del papel A4 */
  .row {
    display: flex !important;
    flex-flow: row wrap !important;
  }

  .col-md-5,
  .col-lg-4 {
    width: 30% !important;
    float: left !important;
  }

  .col-md-7,
  .col-lg-8 {
    width: 70% !important;
    float: left !important;
  }

  /* Hacer visibles los encabezados institucionales y firmas */
  .print-header,
  .print-signatures-section {
    display: block !important;
  }

  /* Estilización estricta para tablas impresas */
  .card {
    border: 1px solid #dee2e6 !important;
    box-shadow: none !important;
    background-color: #fff !important;
  }

  .bg-blue {
    background-color: #1D2A68 !important;
    color: #ffffff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .table-gold-light {
    background-color: #fdf8e9 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .badge {
    border: 1px solid #000 !important;
    color: #000 !important;
    background: transparent !important;
  }

  /* Evitar saltos de página huérfanos a mitad de una tabla */
  .page-break-inside-avoid {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
}
</style>
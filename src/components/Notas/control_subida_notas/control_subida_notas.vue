<template>
  <!-- Contenedor principal del componente -->
  <div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Cabecera del componente -->
    <header class="bg-white p-4 rounded-4 shadow-sm mb-4 custom-header" style="border-left: 6px solid #F4B324;">
      <!-- FILA SUPERIOR: Títulos y Estado del Periodo -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">
        <!-- Título e Icono Principal -->
        <div class="mb-2 mb-md-0 d-flex align-items-center">
          <div
            class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3 min-vw-auto"
            style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px; min-width: 55px;">
            <i class="fas fa-clipboard-check fs-4"></i>
          </div>
          <div>
            <!-- Título -->
            <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
              Control de Subida de Calificaciones
            </h2>
            <!-- Subtítulo -->
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
              Panel de monitoreo y autorización para el registro de notas docentes.
            </p>
          </div>
        </div>

        <!-- Componentes de la Derecha (Badge de Periodo Activo) -->
        <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0">
          <!-- Estado del periodo, solo se muestra si hay periodo activo -->
          <div v-if="periodoActivo"
            class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border shadow-sm w-100 justify-content-center"
            style="background-color: rgba(29, 42, 104, 0.05); border-color: rgba(29, 42, 104, 0.2) !important; color: #1D2A68; white-space: nowrap;">
            <i class="fas fa-flag-checkered me-2" style="color: #F4B324;"></i>
            <span class="fw-medium">
              Periodo Activo: <strong class="ms-1">{{ periodoActivo.nombre }}</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- FILA INFERIOR: Texto de Guía Informativo e Instructivo -->
      <div class="mt-3 p-3 rounded-3 d-flex align-items-start gap-3"
        style="background-color: rgba(29, 42, 104, 0.04); border: 1px dashed rgba(29, 42, 104, 0.15);">
        <i class="fas fa-user-shield fs-5 mt-1" style="color: #F4B324;"></i>
        <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Monitoreo Transaccional y Cierre de Auditoría:</strong> Este panel ejerce un control estricto sobre
          los permisos de escritura en la base de datos de calificaciones. Gestionar las ventanas de tiempo en las que
          los docentes pueden interactuar con las matrices previene modificaciones extemporáneas y asegura el principio
          de integridad de la información. A nivel de arquitectura, estas restricciones actúan como bloqueos de
          seguridad (<em>locks</em>) que validan el estado del periodo antes de permitir cualquier operación de
          inserción o actualización, garantizando la emisión de actas consolidadas y fiables.
        </p>
      </div>
    </header>
    <!-- Contenedor del cuerpo -->
    <div class="card border-0 shadow-sm rounded-4">
      <!-- Contenedor del card-body -->
      <div class="card-body p-0">
        <!-- Contenedor de la tabla -->
        <div class="table-responsive">
          <!-- Tabla -->
          <table class="table table-hover align-middle mb-0">
            <!-- Encabezado de la tabla -->
            <thead class="bg-blue text-white">
              <tr>
                <th class="ps-4 py-3">Fase de Evaluación</th>
                <th>Fecha y Hora Inicio</th>
                <th>Fecha y Hora Fin</th>
                <th>Estado</th>
                <th class="text-center pe-4">Acciones</th>
              </tr>
            </thead>
            <!-- Cuerpo de la tabla -->
            <tbody>
              <!-- Elemento del cuerpo, se usa v-for para recorrer el array de fases permitidas y agregar datos a la tabla -->
              <tr v-for="fase in fasesPermitidas" :key="fase">
                <!-- Columna que muestra la información de la fase, se llama al método formatNombreFase para obtener el nombre de la fase -->
                <td class="ps-4 fw-bold text-blue">
                  <i class="fas fa-calendar-check me-2 text-gold"></i> {{ formatNombreFase(fase) }}
                </td>
                <!-- Contenedor del control de la fase, se usa el metodo getControl para obtener el control de la fase, solo se muestra si hay control de la fase -->
                <template v-if="getControl(fase)">
                  <!-- Columna que muestra la fecha de inicio de la fase, se llama al método formatearFechaVisual para obtener la fecha formateada -->
                  <td>{{ formatearFechaVisual(getControl(fase).fecha_inicio) }}</td>
                  <!-- Columna que muestra la fecha de fin de la fase, se llama al método formatearFechaVisual para obtener la fecha formateada -->
                  <td>{{ formatearFechaVisual(getControl(fase).fecha_fin) }}</td>
                  <!-- Columna que muestra el estado del control, se llama al método getControl para obtener el control de la fase, se usa el método habilitado para obtener el estado del control -->
                  <td>
                    <span class="badge px-3 py-2"
                      :class="getControl(fase).habilitado == 1 ? 'bg-success' : 'bg-secondary'">
                      {{ getControl(fase).habilitado == 1 ? 'HABILITADO' : 'INHABILITADO' }}
                    </span>
                  </td>
                  <!-- Columna que muestra las acciones, se usa el evento click para abrir el modal de edición de control, se utiliza el método getControl para obtener el control de la fase, se utiliza el método habilitado para determinar si el control está habilitado -->
                  <td class="text-center pe-4">
                    <!-- Botón para abrir el modal de edición de control, se utiliza el evento click para llamar al método abrirModalEditar, se utiliza el método getControl para obtener el control de la fase, se utiliza el método habilitado para determinar si el control está habilitado -->
                    <button @click="abrirModalEditar(getControl(fase))"
                      class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 me-2" title="Editar Fechas">
                      <i class="fas fa-edit"></i>
                    </button>
                    <!-- Botón para inhabilitar el control, se utiliza el evento click para llamar al método cambiarEstado, se utiliza el método getControl para obtener el control de la fase, se utiliza el método habilitado para determinar si el control está habilitado, solo se muestra si el control no está habilitado -->
                    <button v-if="getControl(fase).habilitado == 1"
                      @click="cambiarEstado(getControl(fase).id_control, 'inhabilitar')"
                      class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                      <i class="fas fa-times-circle me-1"></i> Deshabilitar
                    </button>
                    <!-- Botón para habilitar el control, se utiliza el evento click para llamar al método cambiarEstado, se utiliza el método getControl para obtener el control de la fase, se utiliza el método habilitado para determinar si el control está habilitado, solo se muestra si el control está habilitado -->  
                    <button v-else @click="cambiarEstado(getControl(fase).id_control, 'habilitar')"
                      class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3"
                      :disabled="faseDeshabilitadaPorRegla(fase)"
                      :title="faseDeshabilitadaPorRegla(fase) ? 'Bloqueado por reglas de Quimestre' : ''">
                      <i class="fas fa-check-circle me-1"></i> Habilitar
                    </button>
                  </td>
                </template>
                <!-- Se muestra siempre y cuando no se haya creado el control de subida de notas -->
                <template v-else>
                  <td colspan="3" class="text-center text-muted fst-italic py-3">
                    No configurado para este periodo
                  </td>
                  <td class="text-center pe-4">
                    <!-- Botón para abrir el modal de creación de control, se utiliza el evento click para llamar al método abrirModalCrear, solo se muestra si no hay control creado -->
                    <button @click="abrirModalCrear(fase)" class="btn btn-sm btn-gold fw-bold rounded-pill px-3"
                      :disabled="faseDeshabilitadaPorRegla(fase)">
                      <i class="fas fa-cog me-1"></i> Configurar
                    </button>
                  </td>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Contenedor del modal de configuración de control -->
    <div class="modal fade" id="modalConfigurarControl" tabindex="-1" aria-hidden="true">
      <!-- Contenedor del cuerpo del modal -->
      <div class="modal-dialog modal-dialog-centered">
        <!-- Contenedor del cuerpo -->
        <div class="modal-content border-0 shadow rounded-4">
          <!-- Contenedor del encabezado -->
          <div class="modal-header bg-blue text-white rounded-top-4">
            <!-- Título, debependiendo del tipo de modal se muestra el título de edición o creación -->
            <h5 class="modal-title fw-bold">
              {{ form.id_control ? 'Editar' : 'Configurar' }} {{ formatNombreFase(form.fase_evaluacion) }}
            </h5>
            <!-- Botón para cerrar el modal -->
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <!-- Contenedor del cuerpo -->
          <div class="modal-body p-4">
            <!-- Formulario para guardar el control -->
            <form @submit.prevent="guardarControl">
              <!-- Contenedor de los campos de fecha y hora de inicio -->
              <div class="mb-3">
                <label class="form-label fw-bold text-blue">Fecha y Hora de Inicio</label>
                <input type="datetime-local" class="form-control border-gold" v-model="form.fecha_inicio" required>
              </div>
              <!-- Contenedor de los campos de fecha y hora de fin -->
              <div class="mb-4">
                <label class="form-label fw-bold text-blue">Fecha y Hora de Fin</label>
                <input type="datetime-local" class="form-control border-gold" v-model="form.fecha_fin" required>
              </div>
              <!-- Contenedor de los campos de habilitar o inhabilitar el control -->
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="habilitarInmediato" v-model="form.habilitado"
                  :disabled="faseDeshabilitadaPorRegla(form.fase_evaluacion)">
                <label class="form-check-label text-muted" for="habilitarInmediato">Habilitar subida de notas</label>
                <!-- Mensaje de advertencia, solo se muestra si la fase está deshabilitada por reglas de quimestre -->
                <div v-if="faseDeshabilitadaPorRegla(form.fase_evaluacion)" class="text-danger small mt-1">
                  No se puede habilitar debido a reglas de quimestre.
                </div>
              </div>
              <!-- Contenedor del botón de guardar -->
              <div class="d-grid">
                <button type="submit" class="btn btn-gold fw-bold py-2" :disabled="guardando">
                  <span v-if="guardando" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <!-- Botón para guardar el control, se utiliza el evento click para llamar al método guardarControl, dependiendo del tipo de modal se muestra el título de Actualizar o Guardar -->
                  <i class="fas fa-save me-2" v-else></i> {{ form.id_control ? 'Actualizar' : 'Guardar' }} Configuración
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
/**
 * control_subida_notas es un componente en el que se encuentra toda la lógica de la aplicación, para realizar el control de subida de calificaciones.
 * Importamos la API de Axios para realizar peticiones al backend
 * Importamos la función mostraralertas para mostrar mensajes de alerta
 * Importamos el componente bootstrap para usar los modales de bootstrap
 */
import API from "@/assets/js/axios";// Importa la API de Axios
import { mostraralertas } from "@/assets/js/funciones/functions";// Importa la función para mostrar alertas
import * as bootstrap from 'bootstrap'; // Importa Bootstrap para usar los modales de Bootstrap
/**
 * Exporta el componente Vue con su configuración, datos, métodos y ciclo de vida
 * Usamos data para definir las variables reactivas del componente
 * Usamos computed para definir propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
 * Usamos mounted para ejecutar código cuando el componente se monta en el DOM
 * Usamos methods para definir los métodos que se pueden llamar desde el template o desde otros métodos
 */
export default {
  /**
   * Data: Define las variables reactivas del componente, que se pueden usar en el template y en los métodos
   * @returns {Object} Objeto con las variables reactivas
   */
  data() {
    /**
     * Return: Devuelve un objeto con las variables reactivas del componente
     * las variables reactivas son:
     * - fasesPermitidas: Array con las fases de evaluación permitidas: Q1_P1, Q1_P2, Q1_P3, Q1_EXAMEN, Q2_P1, Q2_P2, Q2_P3, Q2_EXAMEN, SUPLETORIO, REMEDIAL, GRACIA
     * - controlesCreados: Array con los controles creados
     * - periodoActivo: Objeto con la información del periodo activo
     * - cargando: Boolean que indica si se está cargando información del usuario logueado
     * - guardando: Boolean que indica si se está guardando información del usuario logueado
     * - modalInstance: Objeto con la instancia del modal de reporte de calificaciones
     * - form: Objeto con la información del formulario de configuración de control: id_control, id_periodo, fase_evaluacion, fecha_inicio, fecha_fin, habilitado
     * - baesUrl: String con la URL de la API para realizar peticiones al backend
     */
    return {
      fasesPermitidas: ['Q1_P1', 'Q1_P2', 'Q1_P3', 'Q1_EXAMEN', 'Q2_P1', 'Q2_P2', 'Q2_P3', 'Q2_EXAMEN', 'SUPLETORIO', 'REMEDIAL', 'GRACIA'],
      controlesCreados: [],
      periodoActivo: null,
      cargando: false,
      guardando: false,
      modalInstance: null,
      form: {
        id_control: null,
        id_periodo: null,
        fase_evaluacion: '',
        fecha_inicio: '',
        fecha_fin: '',
        habilitado: false
      },
      baesUrl: '/sistma'
    }
  },
  /**
   * Computed: Define propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
   * Verifica si hay ALGUNA fase del Q1 y Q2 habilitadas
   * @returns {Boolean} Retorna true/false dependiendo si hay ALGUNA fase del Q1 y Q2 habilitadas
   */
  computed: {
    /**
     * q1TieneActivos: Verifica si hay ALGUNA fase del Q1 habilitada
     * @returns {Boolean} Retorna true/false dependiendo si hay ALGUNA fase del Q1 habilitada
     */
    q1TieneActivos() {
      //Retorma true/false dependiendo si hay ALGUNA fase del Q1 habilitada
      return this.controlesCreados.some(c => c.fase_evaluacion.startsWith('Q1') && c.habilitado == 1);
    },
    /**
     * q2TieneActivos: Verifica si hay ALGUNA fase del Q2 habilitada
     * @returns {Boolean} Retorna true/false dependiendo si hay ALGUNA fase del Q2 habilitada
     */
    q2TieneActivos() {
      //Retorna true/false dependiendo si hay ALGUNA fase del Q2 habilitada
      return this.controlesCreados.some(c => c.fase_evaluacion.startsWith('Q2') && c.habilitado == 1);
    }
  },
  /**
   * Mounted: Es un hook del ciclo de vida de Vue que se ejecuta cuando el componente se monta en el DOM. Se usa para inicializar datos y hacer peticiones al backend.
   * Se hace el llamado al método cargarControles para cargar los controles del usuario logueado.
   */
  mounted() {
    this.cargarControles();
  },
  /**
   * Methods: Define los métodos que se pueden llamar desde el template o desde otros métodos
   * Los método utilizados en este componente son:
   * cargarControles: Método para cargar los controles del usuario logueado
   * getControl: Método para obtener el control de la fase
   * faseDeshabilitadaPorRegla: Método para verificar si la fase está deshabilitada por reglas de quimestre
   * formatNombreFase: Método para formatear el nombre de la fase
   * formatearParaInput: Método para formatear la fecha para que sea compatible con la API
   * formatearFechaVisual: Método para formatear la fecha para mostrarla en la tabla
   * abrirModalCrear: Método para abrir el modal de creación de control
   * abrirModalEditar: Método para abrir el modal de edición de control
   * mostrarModal: Método para mostrar el modal
   * async guardarControl: Método para guardar el control
   * async cambiarEstado: Método para cambiar el estado del control
   */
  methods: {
    /**
     * cargarControles: Método para cargar los controles del usuario logueado
     * Este método va a cargar el control de subida de calificaciones creados
     * Si la petición es exitosa, se actualiza la variable controlesCreados con los datos obtenidos y se actualiza la variable periodoActivo con el periodo activo
     * Si la petición falla, se muestra un mensaje de error
     */
    async cargarControles() {
      this.cargando = true;// Inicializar el estado cargando
      try {
        const response = await API.get(`${this.baesUrl}/control_subida_notas`);// Llamada a la API para obtener los controles del usuario logueado
        //Si la respuesta es exitosa, se muestra el mensaje de alerta y se actualiza la variable controlesCreados con los datos obtenidos
        if (response.status === 200) {
          this.controlesCreados = response.data.data;// Asignar el valor de response.data.data a la variable controlesCreados
          this.periodoActivo = response.data.periodo_activo;// Asignar el valor de response.data.periodo_activo a la variable periodoActivo
        }
      } catch (error) {
        //Si hay un error, mostrar un mensaje de error
        mostraralertas("Error al cargar los controles", "error");
      } finally {
        //Limpiar el estado cargando
        this.cargando = false;
      }
    },
    /**
     * getControl: Método para obtener el control de la fase
     * Este método se utiliza para obtener el control de la fase en la página de control de subida de calificaciones
     * Se utiliza la propiedad controlesCreados para obtener el control de la fase  
     * Si no se encuentra el control, se devuelve null
     * Se recibe como parámetro fase, que es la fase de evaluación del control
     * @param fase 
     */
    getControl(fase) {
      //Se busca el control de la fase en la variable controlesCreados
      return this.controlesCreados.find(c => c.fase_evaluacion === fase) || null;
    },
    /**
     * faseDeshabilitadaPorRegla: Este método evalúa si una fase específica debe deshabilitarse según las reglas de negocio de los quimestres.
     * Aplica una regla de exclusión mutua: bloquea un quimestre si el opuesto tiene elementos activos.
      *  @param {string} fase - El identificador o código de la fase a evaluar (ej. 'Q1...', 'Q2...').
      * @returns {boolean} `true` si la fase debe deshabilitarse; `false` si permanece habilitada.
    */ 
    faseDeshabilitadaPorRegla(fase) {
      // Guard clause: Si el parámetro es nulo, indefinido o vacío, no se deshabilita
      if (!fase) return false;
      // Si la fase es del Quimestre 1 (Q1) pero el Quimestre 2 ya tiene registros activos, se bloquea Q1
      if (fase.startsWith('Q1') && this.q2TieneActivos) return true;
      // Si la fase es del Quimestre 2 (Q2) pero el Quimestre 1 ya tiene registros activos, se bloquea Q2.
      if (fase.startsWith('Q2') && this.q1TieneActivos) return true;
      // Si no cumple ninguna de las condiciones de bloqueo anteriores, la fase está disponible.
      return false;
    },
    /**
     * formatNombreFase: Formatea el código técnico de una fase o periodo académico a su nombre legible para el usuario.
     * Funciona como un diccionario de traducción para la interfaz.
     * @param {string} fase - El código identificador de la fase (ej. 'Q1_P1', 'SUPLETORIO'). 
     * @returns {string} El nombre formateado y amigable de la fase, o el mismo código si no hay coincidencia.
     */
    formatNombreFase(fase) {
      // Cláusula de guarda: Retorna un string vacío si la fase es nula o indefinida.
      if (!fase) return '';
      // Diccionario de traducciones: Mapea cada código identificador de fase a su nombre legible.
      const nombres = {
        'Q1_P1': 'Quimestre 1 - Parcial 1',
        'Q1_P2': 'Quimestre 1 - Parcial 2',
        'Q1_P3': 'Quimestre 1 - Parcial 3',
        'Q1_EXAMEN': 'Quimestre 1 - Examen',
        'Q2_P1': 'Quimestre 2 - Parcial 1',
        'Q2_P2': 'Quimestre 2 - Parcial 2',
        'Q2_P3': 'Quimestre 2 - Parcial 3',
        'Q2_EXAMEN': 'Quimestre 2 - Examen',
        'SUPLETORIO': 'Supletorio',
        'REMEDIAL': 'Remedial',
        'GRACIA': 'Gracia'
      };
      // Retorna la traducción correspondiente. Si el código no existe en el diccionario, 
      // se aplica un 'fallback' que devuelve el código original para evitar romper la UI.
      return nombres[fase] || fase;
    },
    /**
     * formatearParaInput: Adapta una cadena de fecha proveniente del servidor al formato requerido por los inputs HTML de tipo datetime-local.
     * Convierte un formato estándar de base de datos (ej. "YYYY-MM-DD HH:mm:ss") a formato ISO corto ("YYYY-MM-DDTHH:mm")
     * @param {string} fecha - La cadena de fecha y hora original a formatear. 
     * @returns {string} La fecha formateada lista para el input, o un string vacío si no es válida.
     */
    formatearParaInput(fecha) {
      // Cláusula de guarda: Si no hay fecha, retorna un string vacío para evitar errores en el input.
      if (!fecha) return '';
      // Reemplaza el espacio en blanco por la 'T' (separador ISO) y corta los primeros 16 caracteres 
      // para remover los segundos y milisegundos, dejando solo 'YYYY-MM-DDTHH:mm'.
      return fecha.replace(' ', 'T').slice(0, 16);
    },
    /**
     * formatearFechaVisual: Formatea una fecha en una cadena legible para el usuario final en formato localizado en español.
     * Transforma un objeto Date o un string de fecha en una estructura "DD/MM/YYYY, HH:mm".
     * @param {string|Date} fecha - La fecha o cadena de texto que representa la fecha a transformar.
     * @returns {string} La fecha formateada para visualización en la UI, o un string vacío si el parámetro es nulo.
     */
    formatearFechaVisual(fecha) {
      // Cláusula de guarda: Evita intentar parsear valores nulos o indefinidos que romperían la función.
      if (!fecha) return '';
      // Instancia un nuevo objeto Date y aplica el formato regional 'es-ES' (Español).
      // Se configuran las opciones para asegurar dos dígitos en días, meses y horas.
      return new Date(fecha).toLocaleString('es-ES', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
      });
    },
    /**
     * abrirModalCrear: Prepara el estado del formulario e inicializa las propiedades para la creación de un nuevo registro.
     * Configura los valores por defecto basados en el periodo activo y la fase seleccionada, y despliega el modal.
     * @param {string} fase - El código identificador de la fase de evaluación que se va a configurar.
     * @returns {void} No devuelve ningún valor.
     */
    abrirModalCrear(fase) {
      // Inicializa el objeto reactivo 'form' con un esquema limpio para la creación.
      this.form = {
        id_control: null,// Nulo porque es un registro nuevo (lo genera la DB).
        id_periodo: this.periodoActivo.id_periodo,// Vincula automáticamente el formulario al periodo que está activo en la app.
        fase_evaluacion: fase,// Asigna la fase que se pasa como argumento al método.
        fecha_inicio: '',// Campo de texto vacío listo para el input de fecha.
        fecha_fin: '',// Campo de texto vacío listo para el input de fecha.
        habilitado: false// Por defecto, el nuevo registro se crea en estado inactivo.
      };
      // Invoca el método encargado de alterar el estado de la UI para mostrar la ventana modal.
      this.mostrarModal();
    },
    /**
     * abrirModalEditar: Prepara el estado del formulario e inicializa las propiedades para la edición de un registro existente.    
     * Configura los valores basados en el control seleccionado y despliega el modal.
     * @param {object} control - El objeto de control que se va a editar. 
     * @returns {void} No devuelve ningún valor.
     */
    abrirModalEditar(control) {
      // Inicializa el objeto reactivo 'form' con un esquema limpio para la edición.
      this.form = {
        id_control: control.id_control, // Vincula automáticamente el formulario al control que se pasa como argumento al método.
        id_periodo: control.id_periodo,// Vincula automáticamente el formulario al periodo que está activo en la app.
        fase_evaluacion: control.fase_evaluacion,// Asigna la fase que se pasa como argumento al método.
        fecha_inicio: this.formatearParaInput(control.fecha_inicio),// Formatea la fecha inicial en el formato requerido por el input de fecha.
        fecha_fin: this.formatearParaInput(control.fecha_fin),// Formatea la fecha final en el formato requerido por el input de fecha.
        habilitado: control.habilitado == 1 // Convierte el valor numérico de habilitado a booleano para el checkbox.
      };
      // Invoca el método encargado de alterar el estado de la UI para mostrar la ventana modal.
      this.mostrarModal();
    },
    /**
     * mostrarModal: Gestiona la inicialización y apertura de la ventana modal de Bootstrap.
     * Utiliza un enfoque de inicialización perezosa (lazy initialization) para reutilizar
     * la instancia del modal si ya existe en el ciclo de vida del componente.
     * @returns {void} No devuelve ningún valor.
     */
    mostrarModal() {
      // Captura el elemento del DOM que contiene la estructura HTML del modal.
      const modalElement = document.getElementById('modalConfigurarControl');
      // Patrón Singleton: Si la instancia de Bootstrap no ha sido creada previamente, 
      // la inicializa vinculándola al elemento del DOM.
      if (!this.modalInstance) {
        this.modalInstance = new bootstrap.Modal(modalElement);
      }
      // Invoca el método nativo de la API de Bootstrap para desplegar el modal en pantalla.
      this.modalInstance.show();
    },
    /**
     * guardarControl: Envía los datos del formulario a la API para crear o actualizar un registro de control de notas.
     * Modela el payload, gestiona de forma asíncrona la petición HTTP (POST/PUT),
     * controla los estados de carga de la UI y maneja las respuestas de éxito o error.
     * @async
     * @returns {Promise<void>} No devuelve ningún valor, pero actualiza la UI y muestra alertas según el resultado de la operación.
     */
    async guardarControl() {
      // Activa el estado de carga en la UI para deshabilitar botones y prevenir envíos duplicados.
      this.guardando = true;
      try {
        // Clonación del formulario y casteo del booleano 'habilitado' a formato binario (1 o 0) requerido por la base de datos.
        const payload = { ...this.form, habilitado: this.form.habilitado ? 1 : 0 };

        let response;
        // Determinación del flujo de persistencia según la existencia de un ID.
        if (this.form.id_control) {
          // Modo Edición: Se ejecuta una petición HTTP PUT apuntando al ID específico del registro.
          response = await API.put(`${this.baesUrl}/control_subida_notas/${this.form.id_control}`, payload);
        } else {
          // Modo Creación: Se ejecuta una petición HTTP POST para crear un nuevo registro.
          response = await API.post(`${this.baesUrl}/control_subida_notas`, payload);
        }
        // Notificación de éxito al usuario utilizando el mensaje devuelto por el servidor.
        mostraralertas(response.data.mensaje, "success");
        // Cierre programático de la ventana modal tras una operación exitosa.
        this.modalInstance.hide();
        // Refresco de la lista principal para reflejar los cambios en la interfaz de usuario.
        this.cargarControles();
      } catch (error) {
        // Definición de un mensaje de error por defecto (fallback) en caso de fallo de red o servidor.
        let msj = "Error al guardar el control";
        // Si la API responde con un mensaje de error controlado, se captura para mostrarlo en la UI.
        if (error.response && error.response.data.mensaje) {
          msj = error.response.data.mensaje;
        }
        // Despliegue de la alerta visual de advertencia con el detalle del error.
        mostraralertas(msj, "warning");
      } finally {
        // Bloque de finalización: Restablece de forma segura el estado de carga, sin importar si la petición fue exitosa o falló.
        this.guardando = false;
      }
    },
    /**
     * cambiarEstado: Modifica el estado de habilitación (activar/inactivar) de un control de subida de notas en el servidor.
     * Determina dinámicamente el endpoint y el método HTTP (verbs) requeridos en base a la acción solicitada.
     * @async Metodo asíncrono que devuelve una promesa
     * @param {number|string} id - El identificador único del registro de control que se desea modificar. 
     * @param {'habilitar'|'destroy'} accion - La operación a realizar: 'habilitar' para activar o 'destroy' para inhabilitar.
     * @returns {Promise<void>} No devuelve ningún valor, pero actualiza la UI y muestra alertas según el resultado de la operación.
     */
    async cambiarEstado(id, accion) {
      try {
        // Configuración dinámica del endpoint basándose en la acción solicitada.
        const ruta = accion === 'habilitar' ? 
        `${this.baesUrl}/habilitar_control_subida_notas/${id}` 
        : `${this.baesUrl}/control_subida_notas/${id}`;
        // Selección dinámica del método Axios (API) correspondiente según la arquitectura del enrutador backend.
        // 'habilitar' ejecuta un DELETE simulado/real y el deshabilitar (destroy) ejecuta un GET (según definición en routes/api.php).
        const metodo = accion === 'habilitar' ? API.delete : API.get; // Ajusta según tu routes/api.php
        // Ejecución de la petición HTTP asíncrona usando la función de método asignada dinámicamente.
        const response = await metodo(ruta);
        // Despliega una alerta de éxito con la retroalimentación del servidor.
        mostraralertas(response.data.mensaje, "success");
        // Refresco de la lista principal para reflejar los cambios en la interfaz de usuario.
        this.cargarControles();
      } catch (error) {
        // Definición de un mensaje de contingencia si falla la comunicación o el proceso en el servidor.
        let msj = "Error al cambiar el estado";
        // Extracción de la respuesta de error controlada enviada por el backend.
        if (error.response && error.response.data.mensaje) {
          msj = error.response.data.mensaje;
        }
        // Notificación visual de advertencia al usuario con el detalle del error.
        mostraralertas(msj, "warning");
      }
    }
  }
}
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
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
  background-color: #df9e19;
  color: #1D2A68;
}

.btn-outline-blue {
  border: 2px solid #1D2A68;
  color: #1D2A68;
}

.btn-outline-blue:hover {
  background-color: #1D2A68;
  color: white;
}
</style>
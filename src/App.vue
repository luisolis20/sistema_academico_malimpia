<template>
  <!-- Navbar, este se va a mostrar siempre y cuando no se encuentre en la ruta de login -->
  <nav class="navbar navbar-expand-lg custom-navbar shadow" v-if="$route.path !== '/login'">
    <!-- Contenedor de la navbar, con el logo y el menú de navegación -->
    <div class="container-fluid px-3">
      <!-- Logo de la navbar, con el logo de Malimpia y el texto "U.E. MALIMPIA", usamos router-link para que se pueda hacer click en el logo,
      se redirige a la ruta /principal -->
      <router-link class="navbar-brand d-flex align-items-center" to="/principal" @click="closeMenu">
        <img src="@/assets/img/mile.png" alt="Logo Malimpia" width="45" height="45"
          class="d-inline-block align-text-top me-2 bg-white rounded-circle p-1 shadow-sm logo-img">
        <div class="d-flex flex-column">
          <span class="fw-bold brand-text lh-1">U.E. "MALIMPIA"</span>
          <span class="brand-subtext lh-1 mt-1">Gestión Académica</span>
        </div>
      </router-link>
      <!-- Botón de toggle de la navbar, se usa para mostrar/ocultar el menú de navegación -->
      <button class="navbar-toggler custom-toggler" type="button" @click="isMenuOpen = !isMenuOpen"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Contenedor de la navbar-collapse, se usa para mostrar/ocultar el menú de navegación -->
      <div class="collapse navbar-collapse" :class="{ 'show': isMenuOpen }" id="navbarNavDropdown">
        <!-- Navegación principal, se usa ul para contener los elementos y nav-item para definir el elemento -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <!-- Primera opción de menu, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /principal -->
          <li class="nav-item">
            <router-link class="nav-link" active-class="active" to="/principal" @click="closeMenu">
              <i class="fas fa-home me-1"></i> Inicio
            </router-link>
          </li>
          <!-- Segunda opción de menu, este va a contener un dropdown con opciones de mantenimiento, esta opción solo se muestra si el rol es Admin o Secretaria --> 
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('mantenimiento')" @mouseleave="leaveDropdown" v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('mantenimiento')">
              <i class="fas fa-wrench me-1"></i> Mantenimiento
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'mantenimiento' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /roles -->
              <li>
                <router-link class="dropdown-item" to="/roles" @click="closeMenu">
                  <i class="fas fa-shield-alt me-2 text-muted"></i> Roles
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /personas -->
              <li>
                <router-link class="dropdown-item" to="/personas" @click="closeMenu">
                  <i class="fas fa-user-friends me-2 text-muted"></i> Personas
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /panel-usuario -->
              <li>
                <router-link class="dropdown-item" to="/panel-usuario" @click="closeMenu">
                  <i class="fas fa-user-circle me-2 text-muted"></i> Usuarios
                </router-link>
              </li>
              <!-- Cuarta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /familias -->
              <li>
                <router-link class="dropdown-item" to="/familias" @click="closeMenu">
                  <i class="fas fa-users me-2 text-muted"></i> Familias
                </router-link>
              </li>
              <!-- Quinta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /periodos-lectivos -->
              <li>
                <router-link class="dropdown-item" to="/periodos-lectivos" @click="closeMenu">
                  <i class="fas fa-calendar-alt me-2 text-muted"></i> Periodos Lectivos
                </router-link>
              </li>
              <!-- Sexta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /niveles-academicos -->
              <li>
                <router-link class="dropdown-item" to="/niveles-academicos" @click="closeMenu">
                  <i class="fas fa-graduation-cap me-2 text-muted"></i> Niveles Académicos
                </router-link>
              </li>
              <!-- Séptima opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /especialidades -->
              <li>
                <router-link class="dropdown-item" to="/especialidades" @click="closeMenu">
                  <i class="fas fa-stethoscope me-2 text-muted"></i> Especialidades
                </router-link>
              </li>
              <!-- Octava opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /asignaturas -->
              <li>
                <router-link class="dropdown-item" to="/asignaturas" @click="closeMenu">
                  <i class="fas fa-book-open me-2 text-muted"></i> Asignaturas
                </router-link>
              </li>
            </ul>
          </li>
          <!-- Tercera opción de menu, este va a contener un dropdown con opciones de cursos, estas opciones y subopciones solo se muestran debepndiendo del rol -->
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('cursos')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('cursos')">
              <i class="fas fa-chalkboard-teacher me-1"></i> Cursos
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown" :class="{ 'show': activeDropdown === 'cursos' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /cursos, solo se muestra si el rol es Admin o Secretaria -->
              <li v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
                <router-link class="dropdown-item" to="/cursos" @click="closeMenu">
                  <i class="fas fa-layer-group me-2 text-muted"></i> Lista de Cursos
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /cursos-asignaturas, solo se muestra si el rol es Admin o Secretaria -->  
              <li v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
                <router-link class="dropdown-item" to="/cursos-asignaturas" @click="closeMenu">
                  <i class="fas fa-book me-2 text-muted"></i> Cursos Asignaturas
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /horarios-clases, solo se muestra si el rol es Representante o Docente -->  
              <li v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
                <router-link class="dropdown-item" to="/horarios-clases" @click="closeMenu">
                  <i class="fas fa-calendar-day me-2 text-muted"></i> Horarios Clases
                </router-link>
              </li>
              <!-- Cuarta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /cursos-familiar, solo se muestra si el rol es Representante o Docente --> 
              <li v-if="rolUsuario === 'Representante' || rolUsuario === 'Docente'">
                <router-link class="dropdown-item" to="/cursos-familiar" @click="closeMenu">
                  <i class="fas fa-chalkboard-teacher me-2 text-muted"></i> Cursos Familiares
                </router-link>
              </li>
            </ul>
          </li>
          <!-- Cuarta opción de menu, este va a contener un dropdown con opciones de matrículas, estas opciones y subopciones solo se muestran debepndiendo del rol -->
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('matriculas')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('matriculas')">
              <i class="fas fa-clipboard-list me-1"></i> Matrículas
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'matriculas' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /cronograma-matriculas, solo se muestra si el rol es Admin o Secretaria -->
              <li v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
                <router-link class="dropdown-item" to="/cronograma-matriculas" @click="closeMenu">
                  <i class="fas fa-clock me-2 text-muted"></i> Cronograma de matrículas
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /matricula, solo se muestra si el rol es Representante o posee familia --> 
              <li v-if="rolUsuario === 'Representante' || esFamiliar">
                <router-link class="dropdown-item" to="/matricula" @click="closeMenu">
                  <i class="fas fa-file-signature me-2 text-muted"></i> Matricular estudiante
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /matricula/historial-matricula, solo se muestra si el rol es Admin o Secretaria -->
              <li v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
                <router-link class="dropdown-item" to="/matricula/historial-matricula" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Historial de matrículas
                </router-link>
              </li>
              <!-- Cuarta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /matricula/mis-historial-matricula, solo se muestra si el rol es Estudiante -->
              <li v-if="rolUsuario === 'Estudiante'">
                <router-link class="dropdown-item" to="/matricula/mis-historial-matricula" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Mis matrículas
                </router-link>
              </li>
              <!-- Quinta opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /matricula/representante/historial-matricula, solo se muestra si posee familia --> 
              <li v-if="esFamiliar">
                <router-link class="dropdown-item" to="/matricula/representante/historial-matricula" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Historial de matrículas
                </router-link>
              </li> 
            </ul>
          </li>
          <!-- Quinta opción de menu, este va a contener un dropdown con opciones de gestión de docentes, esta opción solo se muestra si el rol es Docente -->
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('gestiondocente')" @mouseleave="leaveDropdown" v-if="rolUsuario === 'Docente'">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('gestiondocente')">
              <i class="fas fa-user-graduate me-2"></i> Gestión Docente
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'gestiondocente' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-docente/mis-estudiantes -->  
              <li>
                <router-link class="dropdown-item" to="/gestion-docente/mis-estudiantes" @click="closeMenu">
                  <i class="fas fa-users me-2 text-muted"></i> Registrar Asistencia
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-docente/notas/registro-calificaciones -->  
              <li>
                <router-link class="dropdown-item" to="/gestion-docente/notas/registro-calificaciones" @click="closeMenu">
                  <i class="fas fa-file-signature me-2 text-muted"></i> Registro de calificaciones
                </router-link>
              </li>
            </ul>
            
          </li>
          <!-- Sexta opción de menu, este va a contener un dropdown con opciones de tutorías, esta opción solo se muestra si el rol es Docente y si es tutor --> 
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('turias')" @mouseleave="leaveDropdown" v-if="rolUsuario === 'Docente' && esTutor">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('turias')">
              <i class="fas fa-user-graduate me-2"></i> Tutorías
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'turias' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-docente/tutor/estudiantes -->  
              <li>
                <router-link class="dropdown-item" to="/gestion-docente/tutor/estudiantes" @click="closeMenu">
                  <i class="fas fa-chalkboard-teacher me-2 text-muted"></i> Reporte asistencia
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-docente/tutor/reporte-asistencia/consolidado --> 
              <li>
                <router-link class="dropdown-item" to="/gestion-docente/tutor/reporte-asistencia/consolidado" @click="closeMenu">
                  <i class="fas fa-file-alt me-2 text-muted"></i> Reporte de notas
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-docente/tutor/calificar-conducta -->
              <li>
                <router-link class="dropdown-item" to="/gestion-docente/tutor/calificar-conducta" @click="closeMenu">
                  <i class="fas fa-pencil-ruler me-2 text-muted"></i> Calificar Conducta
                </router-link>
              </li>
            </ul>
            
          </li>
            <!-- Sexta opción de menu, este va a contener un dropdown con opciones de notas, esta opción solo se muestra si el rol es Admin o Secretaria -->
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('notas')" @mouseleave="leaveDropdown" v-if="rolUsuario === 'Administrador' || rolUsuario === 'Secretaria'">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('notas')">
              <i class="fas fa-file-signature me-2"></i> Notas
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'notas' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /control-subida-notas -->
              <li>
                <router-link class="dropdown-item" to="/control-subida-notas" @click="closeMenu">
                  <i class="fas fa-file-signature me-2 text-muted"></i> Control de la subida de calificaciones
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /gestion-administrativa/notas/historico-notas -->
              <li>
                <router-link class="dropdown-item" to="/gestion-administrativa/notas/historico-notas" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Historico de Notas
                </router-link>
              </li>
            </ul>
          </li>
          <!-- Octava opción de menu, este va a contener un dropdown con opciones de notas de estudiantes, estas opciones y subopciones solo se muestran debepndiendo del rol -->
          <li class="nav-item dropdown" @mouseenter="hoverDropdown('notasE')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" @click.prevent="toggleDropdown('notasE')">
              <i class="fas fa-file-signature me-2"></i> Notas Estudiante
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu shadow border-0 custom-dropdown"
              :class="{ 'show': activeDropdown === 'notasE' }">
              <!-- Primera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /notas/mis-notas, solo se muestra si el rol es Estudiante -->
              <li v-if="rolUsuario === 'Estudiante'">
                <router-link class="dropdown-item" to="/notas/mis-notas" @click="closeMenu">
                  <i class="fas fa-file-signature me-2 text-muted"></i> Mis Notas
                </router-link>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /notas/mis-notas-historico, solo se muestra si el rol es Estudiante -->
              <li v-if="rolUsuario === 'Estudiante'">
                <router-link class="dropdown-item" to="/notas/mis-notas-historico" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Historico de Notas
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /notas/representante/historial-notas, solo se muestra si posee familia -->
              <li v-if="esFamiliar">
                <router-link class="dropdown-item" to="/notas/representante/historial-notas" @click="closeMenu">
                  <i class="fas fa-history me-2 text-muted"></i> Historial de Notas
                </router-link>                
              </li>
            </ul>
          </li>
        </ul>
        <!-- Navegación móvil, se usa ul para contener los elementos y nav-item para definir el elemento -->
        <ul
          class="navbar-nav align-items-center mt-3 mt-lg-0 pb-3 pb-lg-0 border-top border-lg-0 pt-2 pt-lg-0 border-secondary custom-mobile-border">
          <!-- Primera opción de menu, se muestra el avatar del usuario y el nombre -->
          <li class="nav-item dropdown user-dropdown w-100 text-center text-lg-start"
            @mouseenter="hoverDropdown('usuario')" @mouseleave="leaveDropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center justify-content-lg-start px-0 px-lg-3"
              href="#" role="button" @click.prevent="toggleDropdown('usuario')">
              <img :src="getPhotoUrl(idUsuario)" @error="handleImageError" alt="Avatar" width="38" height="38"
                class="rounded-circle me-2 border border-2 border-warning shadow-sm">
              <span class="fw-semibold text-white">{{ nombreUsuario }}</span>
            </a>
            <!-- Contenedor del dropdown, se usa ul para contener los elementos y dropdown-item para definir el elemento -->
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 custom-dropdown mt-2 w-100"
              :class="{ 'show': activeDropdown === 'usuario' }">
              <!-- Primera opción del dropdown, se muestra el rol del usuario -->
              <li class="px-3 py-2 text-center border-bottom mb-1 bg-light">
                <span class="d-block text-muted small">{{ rolUsuario }}</span>
              </li>
              <!-- Segunda opción del dropdown, se usa router-link para que se pueda hacer click en el elemento y redirigir a la ruta /perfil -->
              <li>
                <router-link class="dropdown-item py-2" to="/perfil" @click="closeMenu">
                  <i class="bi bi-person-circle me-2 text-primary"></i> Ver mi perfil
                </router-link>
              </li>
              <!-- Tercera opción del dropdown, divide el dropdown -->
              <li>
                <hr class="dropdown-divider my-1">
              </li>
              <!-- Cuarta opción del dropdown, se usa para cerrar sesión -->
              <li>
                <a class="dropdown-item py-2 text-danger fw-bold hover-danger" @click="cerrarSesion" href="#">
                  <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Contenedor de la vista, se usa router-view para mostrar el componente de la ruta actual -->
  <div class="container-fluid mt-4">
    <router-view />
  </div>
</template>

<script>
/**
 * Aquí se importan y generan metodos para darle funcionalidad al componente
 */
import script2 from '@/store/custom.js';// Importa el script de custom.js
import API from "@/assets/js/axios";// Importa la API
import { getMe } from "@/assets/js/auth";// Importa la función getMe
/**
 * Exporta el componente, aquí se definen las propiedades y los métodos
 * que se pueden usar en el componente
 * Usamos solo name para nombrar el componente,
 * mixins para extender la funcionalidad de script2,
 * data para definir las propiedades y variables que se usarán en el componente,
 * methods para definir los métodos que se usarán en el componente y
 * watch para detectar cambios en las propiedades y ejecutar el método cuando se detecta un cambio en la propiedad,
 */
export default {
  mixins: [script2],// Llamada al script de custom.js, se usa mixin para extender la funcionalidad
  /**
   * Definición de las propiedades, se definen las variables que se usarán en el componente
   * y se definen las variables que se usarán
   */
  data() {
    /**
     * Retorna las propiedades, aquí se definen las variables que se usarán en el componente
     */
    return {
      isMenuOpen: false,// Variable que controla si el menú está abierto o no
      activeDropdown: null,// Variable que controla el dropdown activo
      esTutor: false,// Variable que controla si el usuario es tutor
      esFamiliar: false,// Variable que controla si el usuario posee familia
    };
  },
  /**
   * Método que se ejecuta al montar el componente, se usa async y await para hacer llamadas a la API 
   * y obtener datos
   */
  async mounted() {
    // Si no es el login, verificar si es tutor y familia
    if (this.$route.path !== '/login') {
      // Verificar si es tutor, se usa Promise.all para ejecutar múltiples llamadas a la API
      await Promise.all([this.verificarSiEsTutor(), this.VerificarFamilia()]);
    }
  },
  /**
   * Método que se ejecuta en el watcher, se usa watch para detectar cambios en las propiedades
   * y ejecutar el método cuando se detecta un cambio en la propiedad
   */
  watch: {
    /**
     * Protección en el watcher, se usa para evitar bucles infinitos para que no ejecute llamadas constantes
     */
    rolUsuario: {
      immediate: true,// Se ejecuta inmediatamente
      handler(newRol) {// Se ejecuta cuando se detecta un cambio en la propiedad
        // SI ESTÁ EN EL LOGIN, ABORTAR INMEDIATAMENTE (Evita el bucle infinito)
        if (this.$route.path === '/login') return;

        if (newRol === 'Docente') {// Si el rol es docente
          this.verificarSiEsTutor();// Verificar si es tutor
          this.VerificarFamilia();// Verificar si posee familia
        }
      }
    },
    // Monitorear la ruta: Si pasa al login, limpiamos los estados internos
    '$route.path'(newPath) {
      if (newPath === '/login') {// Si pasa al login
        this.esTutor = false;// Limpiar el estado de tutor
        this.esFamiliar = false;// Limpiar el estado de familia
      }
    }
  },
  /**
   * Métodos que se ejecutan en el componente, se definen aquí para que se puedan llamar desde el template
   */
  methods: {
    /**
     * Método que verifica si el usuario es tutor, se usa async y await para hacer llamadas a la API 
     * y obtener datos
     * Este metodo no recibe parámetros
     */
    async verificarSiEsTutor() {
      // 1. Doble protección: Si está en login o no es Docente, salir.
      if (this.$route.path === '/login' || this.rolUsuario !== 'Docente') return;

      // 2. Si no hay token, no tiene sentido consultar a la API
      const token = localStorage.getItem("token_sitma");
      if (!token) return;// Si no hay token, salir

      try {
        const response = await API.get("/sistma/verificar-tutor", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });// Llamada a la API, se envia la cabecera con el token  obtenido
        
        this.esTutor = response.data.es_tutor;// Asignar el valor de es_tutor a la variable esTutor
      } catch (error) {
        // Si hay un error, mostrar un mensaje de error
        console.error("❌ Error al verificar si el docente es tutor:", error);
        this.esTutor = false;// Asignar el valor de esTutor a false
      }
    },
    /**
     * Método que verifica si el usuario posee familia, se usa async y await para hacer llamadas a la API 
     * y obtener datos
     * Este metodo no recibe parámetros
     */
    async VerificarFamilia(){
      // 1. Protección de ruta
      if (this.$route.path === '/login') return;

      // 2. Protección de token vacío
      const token = localStorage.getItem("token_sitma");
      if (!token) return;// Si no hay token, salir

      try {
        const response = await API.get("/sistma/tiene-familia", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });// Llamada a la API, se envia la cabecera con el token  obtenido

        this.esFamiliar = response.data.tiene_familia;// Asignar el valor de tiene_familia a la variable esFamiliar
      } catch (error) { 
        // Si hay un error, mostrar un mensaje de error
        console.error("❌ Error al verificar si el usuario es familiar:", error);
        this.esFamiliar = false;// Asignar el valor de esFamiliar a false
      }
    },
    /**
     * Metodo que devuelve la URL de la imagen de la persona, si no existe, se devuelve una imagen por defecto
     * @param ci Id de la persona
     */
    getPhotoUrl(ci) {
      //Si no existe, devuelve una imagen por defecto
      if (!ci) return "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/250px-User_icon_2.svg.png";
      // Devuelve la URL de la imagen de la persona
      return `${API.defaults.baseURL}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
    },
    /**
     * Metodo que se ejecuta cuando ocurre un error en la imagen de la persona
     * @param event evento que se ejecuta
     */
    handleImageError(event) {
      // Se establece la imagen por defecto
      event.target.src = "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/250px-User_icon_2.svg.png";
    },
    /**
     * Metodo que se ejecuta cuando pasamos el mouse sobre el menú
     * @param menuName nombre del menú que se pasa
     */
    hoverDropdown(menuName) {
      // Si la pantalla es mayor o igual a 992px, se establece el nombre del menú activo
      if (window.innerWidth >= 992) {
        this.activeDropdown = menuName;// Se establece el nombre del menú activo
      }
    },
    /**
     * Metodo que se ejecuta cuando quitamos el mouse sobre el menú
     * Este metodo no recibe parámetros
     */
    leaveDropdown() {
      if (window.innerWidth >= 992) {// Si la pantalla es mayor o igual a 992px
        this.activeDropdown = null;// Se establece el nombre del menú activo a null
      }
    },
    /**
     * Metodo que se ejecuta cuando se hace click en el menú, para mostraar el dropdown
     * @param menuName  nombre del menú que se hace click 
     */
    toggleDropdown(menuName) {
      if (this.activeDropdown === menuName) {// Si el nombre del menú activo es el mismo que el que se hace click
        this.activeDropdown = null;// Se establece el nombre del menú activo a null
      } else {
        // Si no es el mismo, se establece el nombre del menú activo
        this.activeDropdown = menuName;
      }
    },
    /**
     * Método que se ejecuta cuando se hace click en el botón de cerrar el menú
     * Este metodo no recibe parámetros   
     */
    closeMenu() {
      this.isMenuOpen = false;// Se establece el estado del menú a false
      this.activeDropdown = null;// Se establece el nombre del menú activo a null
    },
    /**
     * Método que se ejecuta cuando se hace click en el botón de cerrar sesión  
     * Este metodo no recibe parámetros 
     */
    async cerrarSesion() {
      this.closeMenu();// Se ejecuta el método closeMenu
      const token = localStorage.getItem("token_sitma");// Se obtiene el token

      if (!token) {// Si no hay token, se redirige al login
        localStorage.clear();// Se borra el token
        this.$router.push("/login");// Se redirige al login
        return;// Salir del método  
      }

      try {
        // CORRECCIÓN: Axios GET recibe (url, config)
        const response = await API.get("/sistma/logout", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });// Llamada a la API, se envia la cabecera con el token  obtenido

        console.log("Sesión cerrada en servidor:", response.data);// Se muestra un mensaje de logueo en la consola
      } catch (error) {
        // Si hay un error, mostrar un mensaje de error
        console.error("❌ Error al cerrar sesión en servidor:", error);
      } finally {
        // Siempre limpiamos localmente y redirigimos, falle o no la red
        localStorage.clear();

        // Usar window.location.href es más seguro para un logout 
        // porque fuerza una limpieza total del estado de la app
        window.location.href = "/login";
      }
    }
  }
}
</script>

<style scoped>
/* =========================================
   Estilos para el componente
   ========================================= */
:root {
  --blue-malimpia: #1D2A68;
  --gold-malimpia: #F4B324;
}

.custom-navbar {
  background-color: #1D2A68;
  padding-top: 0.8rem;
  padding-bottom: 0.8rem;
}

.brand-text {
  color: #F4B324;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

.brand-subtext {
  color: #ffffff;
  font-size: 0.75rem;
  opacity: 0.8;
}

.logo-img {
  transition: transform 0.3s ease;
}

.navbar-brand:hover .logo-img {
  transform: scale(1.05);
}

.navbar-nav .nav-link {
  color: rgba(255, 255, 255, 0.8) !important;
  font-weight: 500;
  padding: 0.5rem 1rem;
  margin: 0 0.2rem;
  transition: all 0.3s ease;
  position: relative;
}

.navbar-nav .nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  background-color: #F4B324;
  transition: all 0.3s ease;
  transform: translateX(-50%);
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
  color: #ffffff !important;
}

.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
  width: 80%;
}

/* --- ESTILOS DE DROPDOWN (ESCRITORIO) --- */
.custom-dropdown {
  border-radius: 8px;
  overflow: hidden;
  animation: fadeIn 0.2s ease;
  padding: 0.5rem 0;
}

.custom-dropdown .dropdown-item {
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  color: #1D2A68;
  /* Texto base en azul */
}

@media (min-width: 992px) {
  .custom-dropdown {
    margin-top: 0 !important;
  }

  /* EFECTO HOVER ORO */
  .custom-dropdown .dropdown-item:hover {
    background-color: #F4B324 !important;
    /* Fondo ORO */
    color: #1D2A68 !important;
    /* Texto AZUL */
    font-weight: 600;
    transform: translateX(5px);
  }

  /* Cambiar color de iconos al hacer hover */
  .custom-dropdown .dropdown-item:hover i {
    color: #1D2A68 !important;
  }
}

/* --- AJUSTES MÓVILES --- */
@media (max-width: 991px) {
  .custom-mobile-border {
    border-color: rgba(255, 255, 255, 0.1) !important;
  }

  .custom-dropdown {
    background-color: rgba(255, 255, 255, 0.05) !important;
  }

  .custom-dropdown .dropdown-item {
    color: #fff !important;
  }

  .custom-dropdown .dropdown-item:hover {
    background-color: #F4B324 !important;
    /* Fondo ORO en móvil */
    color: #1D2A68 !important;
    /* Texto AZUL en móvil */
  }

  .bg-light .text-dark {
    color: #F4B324 !important;
  }
}

.hover-danger:hover {
  background-color: #dc3545 !important;
  color: #ffffff !important;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
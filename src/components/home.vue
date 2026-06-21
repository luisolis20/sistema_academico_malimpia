<template>
  <!-- Contenedor principal del componente -->
  <div class="container-fluid px-0">
    <!-- Contenedor del componente -->
    <div class="home-container">
      <!-- Sección de la página de inicio, contiene el carousel -->
      <section class="hero-section position-relative overflow-hidden">
        <!-- Carousel del componente -->
        <div id="heroCarousel" ref="heroCarousel" class="carousel slide carousel-fade">
          <!-- Contenedor del carousel -->
          <div class="carousel-inner">
            <!-- Elemento activo del carousel -->
            <div class="carousel-item active" data-bs-interval="5000">
              <!-- Capa de fondo -->
              <div class="overlay"></div>
              <!-- Imagen principal -->
              <img src="@/assets/img/1.jpg" class="d-block w-100 hero-img" alt="Campus">
            </div>
            <!-- Elemento del carousel -->
            <div class="carousel-item" data-bs-interval="5000">
              <!-- Capa de fondo -->
              <div class="overlay"></div>
              <!-- Imagen principal -->
              <img src="@/assets/img/2.jpg" class="d-block w-100 hero-img" alt="Estudiantes">
            </div>
          </div>
        </div>
        <!-- Contenedor del contenido, texto inicial y botón -->
        <div class="hero-content text-center text-white position-absolute top-50 start-50 translate-middle">
          <!-- Título -->
          <h1 class="display-2 fw-bold mb-3 shadow-text" data-aos="zoom-in">UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO
            "MALIMPIA"</h1>
          <!-- Subtítulo -->
          <p class="fs-4 mb-4 fw-light shadow-text" data-aos="fade-up" data-aos-delay="200">
            Formando líderes con valores, ciencia y tecnología
          </p>
          <!-- Botón -->
          <div data-aos="fade-up" data-aos-delay="400">
            <a href="#info" class="btn btn-gold btn-lg rounded-pill px-5 fw-bold shadow">
              Explorar Institución <i class="fas fa-chevron-down ms-2"></i>
            </a>
          </div>
        </div>
      </section>
      <!-- Sección de información, contiene las estadísticas y el cronograma -->
      <section id="info" class="py-5 bg-white">
        <!-- Contenedor del componente -->
        <div class="container py-5">
          <!-- Contenedor de las estadísticas -->
          <div class="row g-4 text-center">
            <!-- Estadística de estudiantes -->
            <div class="col-md-4" data-aos="fade-up">
              <!-- Estadística del componente -->
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <!-- Icono -->
                <i class="fas fa-user-graduate fa-3x mb-3 text-blue"></i>
                <!-- Título -->
                <h2 class="fw-bold text-blue">{{ stats.estudiantes }}</h2>
                <!-- Subtítulo -->
                <p class="text-muted mb-0">Estudiantes Matriculados</p>
              </div>
            </div>
            <!-- Estadística de docentes -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
              <!-- Estadística del componente -->
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <!-- Icono -->
                <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-blue"></i>
                <!-- Título --> 
                <h2 class="fw-bold text-blue">{{ stats.docentes }}</h2>
                <!-- Subtítulo -->
                <p class="text-muted mb-0">Docentes Calificados</p>
              </div>
            </div>
            <!-- Estadística de años de excelencia -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
              <!-- Estadística del componente --> 
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                  <!-- Icono -->
                <i class="fas fa-award fa-3x mb-3 text-blue"></i>
                <!-- Título -->
                <h2 class="fw-bold text-blue">25</h2>
                <!-- Subtítulo -->  
                <p class="text-muted mb-0">Años de Excelencia</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Sección de matriculas abiertas, contiene el mensaje y el botón -->
      <section v-if="matriculasAbiertas" class="py-5 bg-light-blue border-top border-bottom border-warning">
        <!-- Contenedor del componente, animación Zoom In -->
        <div class="container text-center" data-aos="zoom-in">
          <!-- Título, más icono y texto -->
          <h2 class="fw-bold text-blue mb-3">
            <i class="fas fa-door-open text-gold me-2"></i> ¡Matrículas Abiertas!
          </h2>
          <!-- Texto -->
          <p class="lead text-muted mb-4">El proceso de matriculación para el periodo lectivo actual se encuentra
            habilitado para los siguientes niveles:</p>
            <!-- Contenedor de los cronogramas -->
          <div class="row justify-content-center mb-4">
            <!-- Contenedor del cronograma -->
            <div class="col-md-8">
                <!-- Contenedor de la lista de grupos -->
              <ul class="list-group shadow-sm">
                <!-- Elemento del grupo, se usa v-for para recorrer el array de cronogramas -->
                <li v-for="(crono, index) in cronogramas" :key="index"
                  class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <!-- Contenedor del texto -->
                  <div class="text-start">
                    <!-- Nivel -->
                    <span class="fw-bold text-blue">{{ crono.nivel }}</span>
                    <!-- Salto de línea -->
                    <br>
                    <!-- Especialidad -->
                    <small class="text-muted">Especialidad: {{ crono.especialidad }}</small>
                  </div>
                  <!-- Fecha de inicio y fin -->  
                  <span class="badge bg-gold text-blue rounded-pill px-3 py-2">
                    {{ formatoFecha(crono.fecha_inicio) }} - {{ formatoFecha(crono.fecha_fin) }}
                  </span>
                </li>
              </ul>
            </div>
          </div>
          <!-- Botón, al darle clic redirecciona a la ruta /matricula -->
          <router-link to="/matricula" class="btn btn-gold btn-lg rounded-pill px-5 fw-bold shadow">
            Proceder a Matrícula <i class="fas fa-arrow-right ms-2"></i>
          </router-link>
        </div>
      </section>
      <!-- Sección de cronograma de notas si el array de notas no está vacío se muestra-->
      <section v-if="cronogramaNotas.length > 0" class="py-5 bg-white border-bottom">
        <!-- Contenedor del componente -->
        <div class="container">
          <!-- Título -->
          <div class="text-center mb-5" data-aos="fade-up">
            <!-- Texto más icono -->  
            <h2 class="fw-bold text-blue">
              <i class="fas fa-calendar-alt text-gold me-2"></i> Cronograma Escolar de Calificaciones
            </h2>
            <!-- Texto -->
            <p class="lead text-muted">Fechas límites y periodos establecidos para el registro de evaluaciones.</p>
          </div>
            <!-- Contenedor de la tabla -->
          <div class="row justify-content-center">
              <!-- Contenedor de la tabla, animación Fade Up -->
            <div class="col-md-10" data-aos="fade-up" data-aos-delay="150">
                <!-- Contenedor de la tabla --> 
              <div class="table-responsive shadow-sm rounded-4 border">
                <!-- Contenedor de la tabla -->
                <table class="table table-hover align-middle mb-0 bg-white">
                  <!-- Contenedor de la cabecera -->
                  <thead class="bg-blue text-white">
                    <!-- cabecera de la tabla -->
                    <tr>
                      <th class="ps-4 py-3">Fase de Evaluación</th>
                      <th class="py-3 text-center">Fecha Inicio</th>
                      <th class="py-3 text-center">Fecha Cierre</th>
                      <th class="pe-4 py-3 text-end">Estado</th>
                    </tr>
                  </thead>
                  <!-- Contenedor de la cuerpo -->
                  <tbody>
                    <!-- Elemento del cuerpo, se usa v-for para recorrer el array de notas y agregar datos a la tabla -->
                    <tr v-for="(item, idx) in cronogramaNotas" :key="idx" :class="{'table-success-light': item.habilitado}">
                      <!-- Fase de evaluación -->
                      <td class="ps-4 fw-bold text-blue">
                        <!-- Icono, más texto -->
                        <i class="fas fa-chevron-right text-gold me-2 small"></i>
                        {{ getNombreFase(item.fase) }}
                      </td>
                      <!-- Fecha de inicio -->
                      <td class="text-center text-muted">{{ formatoFecha(item.fecha_inicio) }}</td>
                      <!-- Fecha de fin -->
                      <td class="text-center text-muted fw-semibold">{{ formatoFecha(item.fecha_fin) }}</td>
                      <!-- Estado -->
                      <td class="pe-4 text-end">
                        <!-- Si el estado es habilitado, se muestra el icono -->
                        <span v-if="item.habilitado" class="badge bg-success rounded-pill px-3 py-2 shadow-sm animate-pulse">
                          <i class="fas fa-lock-open me-1"></i> Habilitado
                        </span>
                        <!-- Si el estado no es habilitado, se muestra el icono -->
                        <span v-else class="badge bg-secondary rounded-pill px-3 py-2 text-wrap">
                          <i class="fas fa-lock me-1"></i> Cerrado
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Sección de cuadro de honor si el array de estudiantes no está vacío se muestra-->
      <section v-if="cuadroHonor.length > 0" class="py-5 bg-light-blue">
        <!-- Contenedor del componente -->
        <div class="container text-center">
          <!-- Título más ícono -->
          <h2 class="fw-bold text-blue mb-5" data-aos="fade-up">
            <i class="fas fa-trophy text-gold me-2"></i> Cuadro de Honor Estudiantil
          </h2>
          <!-- Contenedor del carousel, animación Zoom In -->
          <div id="honorCarousel" ref="honorCarousel" class="carousel slide" data-aos="zoom-in">
            <!-- Contenedor de los botones de navegación -->
            <div class="carousel-indicators diploma-indicators">
              <button v-for="(estudiante, index) in cuadroHonor" :key="'ind-' + index" type="button"
                data-bs-target="#honorCarousel" :data-bs-slide-to="index" :class="{ active: index === 0 }"
                aria-current="true" :aria-label="'Estudiante ' + (index + 1)"></button>
            </div>  
            <!-- Contenedor del contenido --> 
            <div class="carousel-inner pb-5">
              <!-- Elemento del contenido, se usa v-for para recorrer el array de estudiantes y agregar datos a la página -->
              <div v-for="(estudiante, index) in cuadroHonor" :key="index" class="carousel-item"
                :class="{ active: index === 0 }" data-bs-interval="5000">
                  <!-- Contenedor del contenido --> 
                <div class="row justify-content-center">
                  <!-- Contenedor del contenido -->
                  <div class="col-lg-8 col-md-10">
                    <!-- Contenedor de la tarjeta -->
                    <div class="card border-0 shadow-lg rounded-0 diploma-card mx-auto">
                      <!-- Contenedor del cuerpo -->  
                      <div class="card-body p-5 position-relative text-center">
                        <!-- Contenedor del borde --> 
                        <div class="diploma-border"></div>
                        <!-- Contenedor del icono -->
                        <div class="diploma-icon-wrapper mb-4">
                          <i class="fas fa-medal fa-3x text-gold"></i>
                        </div>
                          <!-- Contenedor de la foto del estudiante -->
                        <div class="mb-4">
                          <!-- Imagen del estudiante, usamos el método getFoto para obtener la URL de la imagen -->
                          <img :src="getFoto(estudiante.foto)" alt="Foto Estudiante"
                            class="rounded-circle border border-4 border-gold shadow-sm" width="130" height="130"
                            style="object-fit: cover;">
                        </div>
                          <!-- Título -->
                        <h5 class="text-uppercase tracking-widest text-muted mb-2">Certificado de Excelencia</h5>
                          <!-- Nombre del estudiante -->
                        <h2 class="fw-bold text-blue mb-3 diploma-name">{{ estudiante.estudiante }}</h2>
                          <!-- Divisor -->
                        <hr class="w-25 mx-auto bg-gold opacity-100 border-2 mb-4">
                          <!-- Contenedor de la información -->
                        <div class="row justify-content-center mb-4">
                          <!-- Contenedor del texto -->
                          <div class="col-md-10">
                              <!-- Texto -->
                            <p class="fs-5 mb-1 text-dark">
                              Otorgado por su destacado desempeño académico en el nivel <strong>{{ estudiante.nivel
                              }}</strong>.
                            </p>
                              <!-- Texto -->
                            <p class="fs-6 text-muted mb-0">
                              Especialidad: <strong>{{ estudiante.especialidad }}</strong> &nbsp;|&nbsp; Paralelo:
                              <strong>"{{ estudiante.paralelo }}"</strong>
                            </p>
                          </div>
                        </div>
                          <!-- Contenedor de la etiqueta -->
                        <div class="mt-4">
                          <span
                            class="badge bg-gold text-blue fs-5 px-5 py-3 rounded-pill shadow-sm border border-white border-2">
                            Promedio General: {{ estudiante.promedio }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Botón de navegación -->
            <button class="carousel-control-prev" type="button" data-bs-target="#honorCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-blue rounded-circle p-3 shadow" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <!-- Botón de navegación -->
            <button class="carousel-control-next" type="button" data-bs-target="#honorCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-blue rounded-circle p-3 shadow" aria-hidden="true"></span>
              <span class="visually-hidden">Siguiente</span>
            </button>
          </div>
        </div>
      </section>
      <!-- Sección de nuestra ubicación, contiene la información de la instalación -->
      <section class="py-5 bg-light-blue">
        <!-- Contenedor del componente -->
        <div class="container">
          <!-- Contenedor de la información -->
          <div class="row align-items-center">
            <!-- Contenedor del texto, animación Fade Right -->
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <!-- Título -->
              <h2 class="fw-bold text-blue mb-4">Nuestra Ubicación</h2>
                <!-- Texto -->  
              <p class="lead text-muted">Visítanos en el corazón de Malimpia. Nuestras instalaciones cuentan con
                espacios modernos diseñados para el aprendizaje óptimo.</p>
                <!-- Contenedor de la lista de grupos --> 
              <ul class="list-unstyled mt-4">
                <!-- Elemento del grupo -->
                <li class="mb-3 d-flex align-items-center">
                    <!-- Icono -->  
                  <i class="fas fa-map-marker-alt text-gold me-3 fs-4"></i>
                    <!-- Texto -->  
                  <span>Parroquia Malimpia, Esmeraldas, Ecuador</span>
                </li>
                <!-- Elemento del grupo -->
                <li class="mb-3 d-flex align-items-center">
                    <!-- Icono -->
                  <i class="fas fa-phone-alt text-gold me-3 fs-4"></i>
                    <!-- Texto -->
                  <span>+593 99 999 9999</span>
                </li>
              </ul>
            </div>
            <!-- Contenedor del mapa, animación Fade Left -->
            <div class="col-lg-6" data-aos="fade-left">
                <!-- Contenedor del mapa -->
              <div class="map-container rounded-4 shadow overflow-hidden">
                  <!-- Mapa -->
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15951.109720464673!2d-79.4000!3d0.5000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMMKwMzAnMDAuMCJOIDc5wrAyNCcwMC4wIlc!5e0!3m2!1ses!2sec!4v1620000000000!5m2!1ses!2sec"
                  width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Sección de preguntas frecuentes, contiene las preguntas frecuentes del usuario -->
      <section class="py-5 bg-white">
        <!-- Contenedor del componente -->
        <div class="container py-4">
          <!-- Título -->
          <h2 class="text-center fw-bold text-blue mb-5" data-aos="fade-up">Preguntas Frecuentes</h2>
          <!-- Contenedor del contenido, animación Fade Up -->
          <div class="accordion accordion-custom" id="faqAccordion" data-aos="fade-up">
            <!-- Elemento del contenido -->
            <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
              <!-- Título del contenido -->
              <h2 class="accordion-header">
                <!-- Botón -->
                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  ¿Cuáles son los requisitos de matrícula?
                </button>
              </h2>
              <!-- Contenedor del contenido -->
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <!-- Contenedor del cuerpo -->
                <div class="accordion-body text-muted">
                  Para el proceso de matrícula se requiere la copia de cédula del estudiante, representante, y los
                  reportes de calificaciones del año anterior. Todo el proceso se puede gestionar a través de nuestro
                  SGA.
                </div>
              </div>
            </div>
            <!-- Elemento del contenido -->
            <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
              <!-- Título del contenido -->
              <h2 class="accordion-header">
                 <!-- Botón -->
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq2">
                  ¿Cuentan con transporte escolar?
                </button>
              </h2>
              <!-- Contenedor del contenido -->
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <!-- Contenedor del cuerpo -->
                <div class="accordion-body text-muted">
                  Sí, la institución cuenta con rutas de transporte autorizadas que cubren los principales sectores de
                  la parroquia y alrededores.
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script>
/**
 * home es un componente en el que se encuentra toda la lógica de la aplicación
 * Importamos script3 desde @/assets/js/home.js para usar metodos y variables globales
 * Importamos AOS desde 'aos' para usar animaciones
 * Importamos el componente Carousel de Bootstrap
 * Importamos la API de Axios para realizar solicitudes al backend
 */
import AOS from 'aos';// Importa la función de animación
import 'aos/dist/aos.css';// Importa los estilos de animación
import { Carousel } from 'bootstrap';// Importa el componente Carousel de Bootstrap
import API from "@/assets/js/axios"; // Importa la API de Axios
/**
 * Exporta el componente home
 * Usamos solo name para nombrar el componente
 * Usamos data para definir las propiedades y variables que se usarán en el componente
 * Usamos mounted para definir el método que se ejecuta al montar el componente
 * Usamos beforeUnmount para definir el método que se ejecuta antes de desmontar el componente
 * Usamos methods para definir los métodos que se usarán en el componente
 */
export default {
  name: 'home',// Nombra el componente
  /**
   * Definición de las propiedades, se definen las variables que se usarán en el componente
   */
  data() {
    /**
     * Retorna las propiedades, aquí se definen las variables que se usarán en el componente
     */
    return {
      /**
       * Objeto Estadísticas del componente
       */
      stats: {
        docentes: 0,// Estadística de docentes
        estudiantes: 0,// Estadística de estudiantes
      },
      matriculasAbiertas: false,// Variable que controla si las matrículas están abiertas
      cronogramas: [],// Array  que almacena los cronogramas
      cuadroHonor: [],// Array que almacena los cuadros de honor
      cronogramaNotas: [],//Objeto que almacena los cronogramas de subida de notas
    }
  },
  /**
   * Método que se ejecuta al montar el componente, se usa async y await para hacer llamadas a la API
   */
  async mounted() {
    /**
     * Inicializa la animación con AOS, se usa el método init para inicializar la animación con 
     * sus propiedades duration, once, easing
     */
    AOS.init({
      duration: 1000,// Duración de la animación en milisegundos
      once: true,// Ejecuta la animación una vez
      easing: 'ease-in-out'// Efecto de animación
    });
    const myCarousel = this.$refs.heroCarousel;// Obtener el carousel
    if (myCarousel) {// Si el carousel existe
      // Crear instancia del carousel
      this._carouselInstance = new Carousel(myCarousel, {
        interval: 5000,// Intervalo de tiempo entre cada imagen
        ride: 'carousel',// Tipo de animación
        pause: false// Pausa la animación
      });
    }

    // Cargar los datos dinámicos al montar el componente
    await this.cargarDatosLanding();
  },
  /**
   * Método que se ejecuta antes de desmontar el componente, se usa async y await para hacer llamadas a la API
   */
  beforeUnmount() {
    // 1. Forzar la remoción de clases de animación para cancelar cualquier evento 'transitionend' pendiente
    if (this.$refs.heroCarousel) {
      this.$refs.heroCarousel.classList.remove('slide', 'carousel-fade');// Remover clases de animación
    }
    if (this.$refs.honorCarousel) {//si el carousel de cuadros de honor existe
      this.$refs.honorCarousel.classList.remove('slide');// Remover clases de animación
    }

    // 2. Desvincular y destruir las instancias de Bootstrap limpiamente
    if (this._carouselInstance) {// Si la instancia del carousel existe
      this._carouselInstance.pause();// Pausar la animación
      this._carouselInstance.dispose();// Destruir la instancia
      this._carouselInstance = null;// Limpiar la instancia
    }
    if (this._honorCarouselInstance) {// Si la instancia del carousel de cuadros de honor existe
      this._honorCarouselInstance.pause();// Pausar la animación
      this._honorCarouselInstance.dispose();// Destruir la instancia
      this._honorCarouselInstance = null;// Limpiar la instancia
    }
  },
  /**
   * Métodos que se ejecutan en el componente, se definen aquí para que se puedan llamar desde el template
   */
  methods: {
    /**
     * Método para cargar los Datos para llenar los objetos y arrays
     * Este método no recibe parámetros
     * Ruta api usada: /sistma/informacion-inicio
     */
    async cargarDatosLanding() {
      try {
        const response = await API.get('/sistma/informacion-inicio');// Llamada a la API
        const data = response.data;// Obtener los datos

        // Animación sencilla de conteo (opcional, visualmente agradable)
        this.animarConteo('docentes', data.docentes_activos);// Animar conteo de docentes
        this.animarConteo('estudiantes', data.estudiantes_matriculados);// Animar conteo de estudiantes

        this.matriculasAbiertas = data.matriculas_abiertas;// Asignar el valor de matriculasAbiertas a la variable
        this.cronogramas = data.cronogramas;// Asignar el valor de cronogramas a la variable
        this.cronogramaNotas = data.cronograma_notas || [];// Asignar el valor de cronogramaNotas a la variable
        this.cuadroHonor = data.cuadro_honor;//Asignar el valor de cuadroHonor a la variable
        /**
         * Llamada a la función $nextTick para ejecutar el código después de que el componente haya sido renderizado
         */
        this.$nextTick(() => {
          const honorCarouselEl = this.$refs.honorCarousel;// Obtener el carousel de cuadros de honor
          if (this.cuadroHonor.length > 0 && honorCarouselEl) {// Si hay cuadros de honor y el carousel existe
            this._honorCarouselInstance = new Carousel(honorCarouselEl, {// Crear instancia del carousel
              interval: 5000,// Intervalo de tiempo entre cada imagen 5 segundos
              ride: 'carousel',// Tipo de animación
              pause: 'hover'// Pausa la animación al pasar el mouse
            });
          }
        });

      } catch (error) {
        // Si hay un error, mostrar un mensaje de error
        console.error("Error cargando la información de inicio:", error);
      }
    },
    /**
     * Metodo para animar el conteo de docentes y estudiantes
     * @param prop: propiedad que se va a animar
     * @param meta: valor que se va a animar
     */
    animarConteo(prop, meta) {
      let current = 0;// Variable que almacena el valor actual
      const incremento = Math.ceil(meta / 50) || 1; // Velocidad de animación
      const timer = setInterval(() => {// Intervalo de tiempo
        current += incremento;// Incrementar el valor actual
        if (current >= meta) {// Si el valor actual es mayor o igual al valor meta
          this.stats[prop] = meta;// Asignar el valor meta a la propiedad
          clearInterval(timer);// Limpiar el intervalo
        } else {
          //Caso contrario, asignar el valor actual a la propiedad
          this.stats[prop] = current;
        }
      }, 30);// Tiempo de ejecución del intervalo de tiempo
    },
    /**
     * Metodo para formatear la fecha
     * @param fechaStr: fecha en formato string
     * @returns: Retorna la fecha formateada
     */
    formatoFecha(fechaStr) {
      if (!fechaStr) return '';// Si la fecha no existe, devuelve una cadena vacía
      const opciones = { day: '2-digit', month: 'short', year: 'numeric' };// Opciones de formato de fecha
      return new Date(fechaStr).toLocaleDateString('es-ES', opciones);// Devuelve la fecha formateada
    },
    /**
     * Metodo para obtener la URL de la foto
     * @param fotoBase64: URL de la foto en base64
     * @returns: Retorna la URL de la foto
     */
    getFoto(fotoBase64) {
      // Si la foto no existe, devuelve una URL de avatar
      return fotoBase64 ? `data:image/jpeg;base64,${fotoBase64}` : 'https://ui-avatars.com/api/?name=Estudiante&background=1D2A68&color=fff';
    },
    /**
     * Metodo para obtener el nombre de la fase
     * @param fase: Fase de evaluación
     * @returns: Retorna el nombre de la fase
     */
    getNombreFase(fase) {
      const mapaFases = {
        'Q1_P1': 'Primer Quimestre / Parcial 1',
        'Q1_P2': 'Primer Quimestre / Parcial 2',
        'Q1_P3': 'Primer Quimestre / Parcial 3',
        'Q1_EXAMEN': 'Primer Quimestre / Examen',
        'Q2_P1': 'Segundo Quimestre / Parcial 1',
        'Q2_P2': 'Segundo Quimestre / Parcial 2',
        'Q2_P3': 'Segundo Quimestre / Parcial 3',
        'Q2_EXAMEN': 'Segundo Quimestre / Examen',
        'SUPLETORIO': 'Examen Supletorio',
        'REMEDIAL': 'Examen Remedial'
      };// Mapa de las fases
      return mapaFases[fase] || fase;// Devuelve el nombre de la fase
    }
  }
}
</script>

<style scoped>
/* (Tus estilos CSS se mantienen idénticos) */
.text-blue {
  color: #1D2A68;
}
.text-gold {
  color: #F4B324;
}
.bg-light-blue {
  background-color: rgba(29, 42, 104, 0.03);
}
.border-gold {
  border-color: #F4B324 !important;
}
.hero-section {
  height: 90vh;
  min-height: 500px;
}
.hero-img {
  height: 90vh;
  width: 100%;
  object-fit: cover;
}
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(rgba(29, 42, 104, 0.7), rgba(0, 0, 0, 0.4));
  z-index: 1;
}
.hero-content {
  z-index: 2;
  width: 80%;
}
.shadow-text {
  text-shadow: 2px 4px 10px rgba(0, 0, 0, 0.5);
}
.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
  transition: all 0.3s ease;
}
.btn-gold:hover {
  background-color: #e0a31f;
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
}
.stat-card {
  transition: transform 0.3s ease;
  background: #fff;
}
.stat-card:hover {
  transform: translateY(-10px);
}
.accordion-button:not(.collapsed) {
  background-color: #1D2A68;
  color: white;
}
.accordion-button:focus {
  border-color: #F4B324;
  box-shadow: 0 0 0 0.25rem rgba(244, 179, 36, 0.25);
}
.accordion-button::after {
  filter: brightness(0) saturate(100%) invert(80%) sepia(50%) saturate(3000%) hue-rotate(350deg);
}

/* ESTILOS DE LA NUEVA TABLA */
.bg-blue {
  background-color: #1D2A68 !important;
}
.table-success-light {
  background-color: rgba(40, 167, 69, 0.06);
}

/* Efecto sutil de pulso para la fase que esté activa */
.animate-pulse {
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
  70% { transform: scale(1.03); box-shadow: 0 0 0 6px rgba(40, 167, 69, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

@media (max-width: 768px) {
  .hero-section { height: 70vh; }
  .display-2 { font-size: 2.5rem; }
}

.diploma-card {
  background: #ffffff;
  background-image: radial-gradient(#F4B324 0.5px, transparent 0.5px), radial-gradient(#F4B324 0.5px, #ffffff 0.5px);
  background-size: 20px 20px;
  background-position: 0 0, 10px 10px;
  background-blend-mode: multiply;
  padding: 10px;
  border: 1px solid #e0e0e0 !important;
}
.diploma-border {
  position: absolute;
  top: 15px; left: 15px; right: 15px; bottom: 15px;
  border: 2px solid #1D2A68;
  outline: 4px double #F4B324;
  outline-offset: -8px;
  pointer-events: none;
  z-index: 0;
}
.card-body {
  background-color: rgba(255, 255, 255, 0.95);
  z-index: 1;
}
.diploma-name {
  font-family: 'Georgia', serif;
  font-size: 2.2rem;
  letter-spacing: 1px;
}
.tracking-widest {
  letter-spacing: 0.15em;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 2.5rem;
  height: 2.5rem;
}
.diploma-indicators {
  bottom: -40px;
}
.diploma-indicators button {
  background-color: #1D2A68 !important;
  height: 8px !important;
  width: 30px !important;
  border-radius: 4px;
}
</style>
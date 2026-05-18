<template>
  <div class="container-fluid px-0">

    <div class="home-container">
      <section class="hero-section position-relative overflow-hidden">
        <div id="heroCarousel" ref="heroCarousel" class="carousel slide carousel-fade">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
              <div class="overlay"></div>
              <img src="@/assets/img/1.jpg" class="d-block w-100 hero-img" alt="Campus">
            </div>
            <div class="carousel-item" data-bs-interval="5000">
              <div class="overlay"></div>
              <img src="@/assets/img/2.jpg" class="d-block w-100 hero-img" alt="Estudiantes">
            </div>
          </div>
        </div>

        <div class="hero-content text-center text-white position-absolute top-50 start-50 translate-middle">
          <h1 class="display-2 fw-bold mb-3 shadow-text" data-aos="zoom-in">UNIDAD EDUCATIVA ESTANDARIZADA DEL MILENIO
            "MALIMPIA"</h1>
          <p class="fs-4 mb-4 fw-light shadow-text" data-aos="fade-up" data-aos-delay="200">
            Formando líderes con valores, ciencia y tecnología
          </p>
          <div data-aos="fade-up" data-aos-delay="400">
            <a href="#info" class="btn btn-gold btn-lg rounded-pill px-5 fw-bold shadow">
              Explorar Institución <i class="fas fa-chevron-down ms-2"></i>
            </a>
          </div>
        </div>
      </section>

      <section id="info" class="py-5 bg-white">
        <div class="container py-5">
          <div class="row g-4 text-center">
            <div class="col-md-4" data-aos="fade-up">
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <i class="fas fa-user-graduate fa-3x mb-3 text-blue"></i>
                <h2 class="fw-bold text-blue">{{ stats.estudiantes }}</h2>
                <p class="text-muted mb-0">Estudiantes Matriculados</p>
              </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-blue"></i>
                <h2 class="fw-bold text-blue">{{ stats.docentes }}</h2>
                <p class="text-muted mb-0">Docentes Calificados</p>
              </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <i class="fas fa-award fa-3x mb-3 text-blue"></i>
                <h2 class="fw-bold text-blue">25</h2>
                <p class="text-muted mb-0">Años de Excelencia</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="matriculasAbiertas" class="py-5 bg-light-blue border-top border-bottom border-warning">
        <div class="container text-center" data-aos="zoom-in">
          <h2 class="fw-bold text-blue mb-3">
            <i class="fas fa-door-open text-gold me-2"></i> ¡Matrículas Abiertas!
          </h2>
          <p class="lead text-muted mb-4">El proceso de matriculación para el periodo lectivo actual se encuentra
            habilitado para los siguientes niveles:</p>

          <div class="row justify-content-center mb-4">
            <div class="col-md-8">
              <ul class="list-group shadow-sm">
                <li v-for="(crono, index) in cronogramas" :key="index"
                  class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div class="text-start">
                    <span class="fw-bold text-blue">{{ crono.nivel }}</span>
                    <br>
                    <small class="text-muted">Especialidad: {{ crono.especialidad }}</small>
                  </div>
                  <span class="badge bg-gold text-blue rounded-pill px-3 py-2">
                    {{ formatoFecha(crono.fecha_inicio) }} - {{ formatoFecha(crono.fecha_fin) }}
                  </span>
                </li>
              </ul>
            </div>
          </div>

          <router-link to="/matricula" class="btn btn-gold btn-lg rounded-pill px-5 fw-bold shadow">
            Proceder a Matrícula <i class="fas fa-arrow-right ms-2"></i>
          </router-link>
        </div>
      </section>

      <section v-if="cuadroHonor.length > 0" class="py-5 bg-light-blue">
        <div class="container text-center">
          <h2 class="fw-bold text-blue mb-5" data-aos="fade-up">
            <i class="fas fa-trophy text-gold me-2"></i> Cuadro de Honor Estudiantil
          </h2>

          <div id="honorCarousel" ref="honorCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="zoom-in">
            <div class="carousel-indicators diploma-indicators">
              <button v-for="(estudiante, index) in cuadroHonor" :key="'ind-' + index" type="button"
                data-bs-target="#honorCarousel" :data-bs-slide-to="index" :class="{ active: index === 0 }"
                aria-current="true" :aria-label="'Estudiante ' + (index + 1)"></button>
            </div>

            <div class="carousel-inner pb-5">
              <div v-for="(estudiante, index) in cuadroHonor" :key="index" class="carousel-item"
                :class="{ active: index === 0 }" data-bs-interval="5000">
                <div class="row justify-content-center">
                  <div class="col-lg-8 col-md-10">
                    <div class="card border-0 shadow-lg rounded-0 diploma-card mx-auto">
                      <div class="card-body p-5 position-relative text-center">
                        <div class="diploma-border"></div>

                        <div class="diploma-icon-wrapper mb-4">
                          <i class="fas fa-medal fa-3x text-gold"></i>
                        </div>

                        <div class="mb-4">
                          <img :src="getFoto(estudiante.foto)" alt="Foto Estudiante"
                            class="rounded-circle border border-4 border-gold shadow-sm" width="130" height="130"
                            style="object-fit: cover;">
                        </div>

                        <h5 class="text-uppercase tracking-widest text-muted mb-2">Certificado de Excelencia</h5>
                        <h2 class="fw-bold text-blue mb-3 diploma-name">{{ estudiante.estudiante }}</h2>

                        <hr class="w-25 mx-auto bg-gold opacity-100 border-2 mb-4">

                        <div class="row justify-content-center mb-4">
                          <div class="col-md-10">
                            <p class="fs-5 mb-1 text-dark">
                              Otorgado por su destacado desempeño académico en el nivel <strong>{{ estudiante.nivel
                              }}</strong>.
                            </p>
                            <p class="fs-6 text-muted mb-0">
                              Especialidad: <strong>{{ estudiante.especialidad }}</strong> &nbsp;|&nbsp; Paralelo:
                              <strong>"{{ estudiante.paralelo }}"</strong>
                            </p>
                          </div>
                        </div>

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

            <button class="carousel-control-prev" type="button" data-bs-target="#honorCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-blue rounded-circle p-3 shadow" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#honorCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-blue rounded-circle p-3 shadow" aria-hidden="true"></span>
              <span class="visually-hidden">Siguiente</span>
            </button>
          </div>
        </div>
      </section>

      <section class="py-5 bg-light-blue">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
              <h2 class="fw-bold text-blue mb-4">Nuestra Ubicación</h2>
              <p class="lead text-muted">Visítanos en el corazón de Malimpia. Nuestras instalaciones cuentan con
                espacios modernos diseñados para el aprendizaje óptimo.</p>
              <ul class="list-unstyled mt-4">
                <li class="mb-3 d-flex align-items-center">
                  <i class="fas fa-map-marker-alt text-gold me-3 fs-4"></i>
                  <span>Parroquia Malimpia, Esmeraldas, Ecuador</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="fas fa-phone-alt text-gold me-3 fs-4"></i>
                  <span>+593 99 999 9999</span>
                </li>
              </ul>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
              <div class="map-container rounded-4 shadow overflow-hidden">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15951.109720464673!2d-79.4000!3d0.5000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMMKwMzAnMDAuMCJOIDc5wrAyNCcwMC4wIlc!5e0!3m2!1ses!2sec!4v1620000000000!5m2!1ses!2sec"
                  width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-5 bg-white">
        <div class="container py-4">
          <h2 class="text-center fw-bold text-blue mb-5" data-aos="fade-up">Preguntas Frecuentes</h2>
          <div class="accordion accordion-custom" id="faqAccordion" data-aos="fade-up">
            <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
              <h2 class="accordion-header">
                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  ¿Cuáles son los requisitos de matrícula?
                </button>
              </h2>
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted">
                  Para el proceso de matrícula se requiere la copia de cédula del estudiante, representante, y los
                  reportes de calificaciones del año anterior. Todo el proceso se puede gestionar a través de nuestro
                  SGA.
                </div>
              </div>
            </div>

            <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq2">
                  ¿Cuentan con transporte escolar?
                </button>
              </h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
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
import AOS from 'aos';
import 'aos/dist/aos.css';
import { Carousel } from 'bootstrap';
import API from "@/assets/js/axios"; // Asegúrate de que esta ruta sea correcta

export default {
  name: 'home',
  data() {
    return {
      carouselInstance: null,
      honorCarouselInstance: null,
      stats: {
        docentes: 0,
        estudiantes: 0,
      },
      matriculasAbiertas: false,
      cronogramas: [],
      cuadroHonor: []
    }
  },
  async mounted() {
    AOS.init({
      duration: 1000,
      once: true,
      easing: 'ease-in-out'
    });
    const myCarousel = this.$refs.heroCarousel;
    this.carouselInstance = new Carousel(myCarousel, {
      interval: 5000,
      ride: 'carousel',
      pause: false
    });

    // Cargar los datos dinámicos al montar el componente
    await this.cargarDatosLanding();
  },
  beforeUnmount() {
    if (this.carouselInstance) {
      this.carouselInstance.pause();
      this.carouselInstance.dispose();
      this.carouselInstance = null;
    }
    if (this.honorCarouselInstance) {
      this.honorCarouselInstance.pause();
      this.honorCarouselInstance.dispose();
    }
  },
  methods: {
    async cargarDatosLanding() {
      try {
        const response = await API.get('/sistma/informacion-inicio');
        const data = response.data;

        // Animación sencilla de conteo (opcional, visualmente agradable)
        this.animarConteo('docentes', data.docentes_activos);
        this.animarConteo('estudiantes', data.estudiantes_matriculados);

        this.matriculasAbiertas = data.matriculas_abiertas;
        this.cronogramas = data.cronogramas;
        this.cuadroHonor = data.cuadro_honor;
        console.log(data);
        this.$nextTick(() => {
          if (this.cuadroHonor.length > 0) {
            const honorCarouselEl = this.$refs.honorCarousel;
            this.honorCarouselInstance = new Carousel(honorCarouselEl, {
              interval: 5000,
              ride: 'carousel',
              pause: 'hover'
            });
          }
        });

      } catch (error) {
        console.error("Error cargando la información de inicio:", error);
      }
    },
    animarConteo(prop, meta) {
      let current = 0;
      const incremento = Math.ceil(meta / 50) || 1; // Ajusta la velocidad
      const timer = setInterval(() => {
        current += incremento;
        if (current >= meta) {
          this.stats[prop] = meta;
          clearInterval(timer);
        } else {
          this.stats[prop] = current;
        }
      }, 30);
    },
    formatoFecha(fechaStr) {
      if (!fechaStr) return '';
      const opciones = { day: '2-digit', month: 'short', year: 'numeric' };
      return new Date(fechaStr).toLocaleDateString('es-ES', opciones);
    },
    getFoto(fotoBase64) {
      return fotoBase64 ? `data:image/jpeg;base64,${fotoBase64}` : 'https://ui-avatars.com/api/?name=Estudiante&background=1D2A68&color=fff';
    }
  }
}
</script>

<style scoped>
/* PALETA INSTITUCIONAL */
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

/* HERO SECTION */
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

/* BOTÓN ORO */
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

/* STAT CARDS */
.stat-card {
  transition: transform 0.3s ease;
  background: #fff;
}

.stat-card:hover {
  transform: translateY(-10px);
}

/* ACCORDION PERSONALIZADO */
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

@media (max-width: 768px) {
  .hero-section {
    height: 70vh;
  }

  .display-2 {
    font-size: 2.5rem;
  }
}

/* ESTILOS DEL CUADRO DE HONOR TIPO DIPLOMA */
.diploma-card {
  background: #ffffff;
  background-image: radial-gradient(#F4B324 0.5px, transparent 0.5px), radial-gradient(#F4B324 0.5px, #ffffff 0.5px);
  background-size: 20px 20px;
  background-position: 0 0, 10px 10px;
  background-blend-mode: multiply; /* Textura muy sutil de fondo */
  padding: 10px;
  border: 1px solid #e0e0e0 !important;
}

.diploma-border {
  position: absolute;
  top: 15px;
  left: 15px;
  right: 15px;
  bottom: 15px;
  border: 2px solid #1D2A68;
  outline: 4px double #F4B324;
  outline-offset: -8px;
  pointer-events: none;
  z-index: 0;
}

.card-body {
  background-color: rgba(255, 255, 255, 0.95); /* Para tapar la textura en el texto */
  z-index: 1;
}

.diploma-name {
  font-family: 'Georgia', serif; /* Fuente elegante para el nombre */
  font-size: 2.2rem;
  letter-spacing: 1px;
}

.tracking-widest {
  letter-spacing: 0.15em;
}

.bg-blue {
  background-color: #1D2A68 !important;
}

/* Modificar las flechas del carrusel para que se vean bien en fondo claro */
.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 2.5rem;
  height: 2.5rem;
}

/* Ajuste de los indicadores del carrusel para que estén abajo del diploma */
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
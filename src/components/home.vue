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
                <h2 class="fw-bold text-blue">+850</h2>
                <p class="text-muted mb-0">Estudiantes Matriculados</p>
              </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
              <div class="stat-card p-4 rounded-4 shadow-sm border-bottom border-4 border-gold">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-blue"></i>
                <h2 class="fw-bold text-blue">45</h2>
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

export default {
  name: 'home',
  data() {
    return {
      carouselInstance: null
    }
  },
  mounted() {
    AOS.init({
      duration: 1000,
      once: true,
      easing: 'ease-in-out'
    });
    const myCarousel = this.$refs.heroCarousel;
    this.carouselInstance = new Carousel(myCarousel, {
      interval: 5000, // Tiempo entre cambios
      ride: 'carousel',
      pause: false    // Para que no se detenga al poner el mouse
    });
  },
  beforeUnmount() {
    if (this.carouselInstance) {
      this.carouselInstance.pause(); // Detiene el movimiento
      this.carouselInstance.dispose(); // Limpia la memoria de Bootstrap
      this.carouselInstance = null;
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
</style>
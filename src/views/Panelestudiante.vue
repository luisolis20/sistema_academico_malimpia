<template>
  <div class="container mt-4">
    <div class="card shadow-sm border-0 mb-4 bg-light">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="me-4">
            <i class="fas fa-user-circle fa-4x text-success"></i>
          </div>
          <div>
            <h3 class="mb-0">{{ estudiante.nombre }} {{ estudiante.apellido }}</h3>
            <p class="text-muted mb-0">Cédula: {{ estudiante.cedula }} | Ciclo Costa 2026</p>
          </div>
        </div>
      </div>
    </div>

    <ul class="nav nav-tabs" id="estudianteTab" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#matricula">
          <i class="fas fa-file-alt me-1"></i> Matriculación
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#horario">
          <i class="fas fa-calendar-alt me-1"></i> Mi Horario
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calificaciones">
          <i class="fas fa-graduation-cap me-1"></i> Mis Notas
        </button>
      </li>
    </ul>

    <div class="tab-content bg-white p-4 border border-top-0 shadow-sm">
      <div class="tab-pane fade show active" id="matricula">
      </div>
    </div>
  </div>
</template>

<script>
import API from "@/assets/js/axios"
import { mostraralertas2 } from "@/assets/js/funciones/functions";

export default {
  data() {
    return {

    }
  },
  methods: {

  }
}
</script>
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
        <button @click="salir" class="btn btn-outline-danger">
          <i class="fas fa-sign-out-alt me-1"></i> Salir del Sistema
        </button>
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
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      estudiante: { nombre: 'Josué', apellido: 'Lastra', cedula: '123456789' },
      // ... resto de tus datos (cursos, horarios, notas)
    }
  },
  methods: {
    salir() {
      Swal.fire({
        title: '¿Cerrar sesión?',
        text: "Tendrás que ingresar tus credenciales nuevamente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754', // Verde success
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // 1. Limpiar datos de sesión (si usas localStorage)
          localStorage.removeItem('user_token');
          
          // 2. Redirigir al Login
          this.$router.push('/login');
          
          // 3. Alerta de despedida rápida
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
          });
          Toast.fire({
            icon: 'success',
            title: 'Sesión cerrada correctamente'
          });
        }
      });
    },
    procesarMatricula() {
      // Tu lógica de Axios
    }
  }
}
</script>
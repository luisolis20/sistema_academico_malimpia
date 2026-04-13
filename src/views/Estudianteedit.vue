<template>
  <div class="row mt-5">
    <div class="col-md-6 offset-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
          <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Estudiante</h4>
        </div>
        <div class="card-body">
          <div v-if="cargando" class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2">Cargando datos del estudiante...</p>
          </div>

          <form v-else @submit.prevent="actualizarEstudiante">
            <div class="mb-3">
              <label class="form-label fw-bold">Nombre</label>
              <input v-model="form.nombre" type="text" class="form-control" placeholder="Ej: Juan" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Apellido</label>
              <input v-model="form.apellido" type="text" class="form-control" placeholder="Ej: Pérez" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Cédula / Identificación</label>
              <input v-model="form.cedula" type="text" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Correo Electrónico</label>
              <input v-model="form.correo" type="email" class="form-control" required>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Cambios
              </button>
              <router-link to="/principal" class="btn btn-outline-secondary">
                Cancelar
              </router-link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      id: this.$route.params.id, // Obtenemos el ID desde la URL definida en el router
      form: {
        nombre: '',
        apellido: '',
        cedula: '',
        correo: ''
      },
      cargando: false
    }
  },
  mounted() {
    this.getEstudiante();
  },
  methods: {
    async getEstudiante() {
      this.cargando = true;
      try {
        // Petición al backend para traer los datos del estudiante específico
        const response = await axios.get(`http://localhost:8000/api/estudiantes/${this.id}`);
        this.form = response.data;
      } catch (error) {
        Swal.fire('Error', 'No se pudo obtener la información del estudiante', 'error');
        this.$router.push('/principal');
      } finally {
        this.cargando = false;
      }
    },

    async actualizarEstudiante() {
      try {
        await axios.put(`http://localhost:8000/api/estudiantes/${this.id}`, this.form);
        
        Swal.fire({
          title: '¡Actualizado!',
          text: 'Los datos han sido guardados correctamente.',
          icon: 'success',
          confirmButtonColor: '#198754' // Color success de Bootstrap
        });

        this.$router.push('/principal'); // Redirigir a la lista
      } catch (error) {
        Swal.fire('Error', 'Hubo un problema al actualizar los datos', 'error');
      }
    }
  }
}
</script>
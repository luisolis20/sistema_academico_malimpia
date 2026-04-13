<template>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white"><h5>Registrar Nuevo Estudiante</h5></div>
          <div class="card-body">
            <form @submit.prevent="guardar">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="form.nombre" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Cédula</label>
                <input v-model="form.cedula" type="text" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-success w-100">Guardar Registro</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() { return { form: { nombre: '', cedula: '' } } },
  methods: {
    guardar() { /* lógica axios.post */ }
  }
}
</script>
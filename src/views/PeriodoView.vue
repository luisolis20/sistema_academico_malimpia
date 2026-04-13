<template>
  <div class="container-fluid py-4">
    <header class="mb-4">
      <h2 class="fw-bold" style="color: var(--green-900);">Gestión de Periodos Lectivos</h2>
      <p class="text-muted">Define el ciclo escolar vigente. Solo un periodo debe estar "Activo".</p>
    </header>

    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
      <form @submit.prevent="guardarPeriodo" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label small fw-bold">Nombre del Periodo</label>
          <input type="text" class="form-control" v-model="form.nombre" placeholder="Ej: 2026 - 2027">
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold">Fecha de Inicio</label>
          <input type="date" class="form-control" v-model="form.inicio">
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold">Estado</label>
          <select class="form-select" v-model="form.estado">
            <option value="activo">Activo (Vigente)</option>
            <option value="cerrado">Cerrado</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success w-100 fw-bold"><i class="fas fa-save me-2"></i>Guardar</button>
        </div>
      </form>
    </div>

    <div class="mt-4">
      <table class="table align-middle shadow-sm bg-white rounded-3 overflow-hidden">
        <thead class="table-success">
          <tr><th>Periodo</th><th>Inicio</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <tr v-for="p in periodos" :key="p.id">
            <td class="fw-bold">{{ p.nombre }}</td>
            <td>{{ p.inicio }}</td>
            <td>
              <span :class="['badge', p.estado === 'activo' ? 'bg-success' : 'bg-secondary']">
                {{ p.estado }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-edit"></i></button>
              <button class="btn btn-sm btn-outline-danger"><i class="fas fa-archive"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: { nombre: '', inicio: '', estado: 'activo' },
      periodos: [
        { id: 1, nombre: '2025 - 2026', inicio: '2025-05-01', estado: 'cerrado' },
        { id: 2, nombre: '2026 - 2027', inicio: '2026-05-01', estado: 'activo' }
      ]
    }
  },
  methods: {
    guardarPeriodo() {
      // Petición a Laravel para crear nuevo ciclo lectivo
      console.log("Creando periodo:", this.form);
    }
  }
}
</script>
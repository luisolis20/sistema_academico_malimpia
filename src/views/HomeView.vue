<template>
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="table-responsive">
        <table class="table table-bordered table-striped mt-4">
          <thead>
            <tr>
              <th>#</th>
              <th>ID</th>
              <th>Foto</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Registro</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="table-group-divider" id="contenido">
            <tr v-if="cargando">
              <td colspan="7" class="text-center">Cargando estudiantes...</td>
            </tr>

            <template v-else-if="estudiantes && estudiantes.length > 0">
              <tr v-for="(est, i) in estudiantes" :key="est.id">
                <td>{{ i + 1 }}</td>
                <td>{{ est.id }}</td>
                <td>
                  <img :src="est.foto" class="img-thumbnail" style="width: 50px;" alt="foto">
                </td>
                <td>{{ est.nombre }}</td>
                <td>{{ est.apellido }}</td>
                <td>{{ est.registro }}</td>
                <td>
                  <router-link :to="{path: '/editE/'+est.id}" class="btn btn-warning btn-sm me-1">
                    <i class="fa-solid fa-edit"></i>
                  </router-link>
                </td>
              </tr>
            </template>

            <tr v-else>
              <td colspan="7" class="text-center">No hay estudiantes registrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      estudiantes: [], // Inicializado como array vacío para evitar errores de .length
      cargando: false,
    };
  },
  mounted() {
    // Corregido: el nombre debe coincidir con el método de abajo
    this.getEstudiantes();
  },
  methods: {
    async getEstudiantes() {
      this.cargando = true;
      try {
        // Asegúrate de que el puerto 3000 sea el de tu API
        //const response = await axios.get('http://localhost:3000/estudiantes');
        //this.estudiantes = response.data;
      } catch (error) {
        console.error('Error al cargar estudiantes:', error);
      } finally {
        this.cargando = false;
      }
    }
  }
};
</script>

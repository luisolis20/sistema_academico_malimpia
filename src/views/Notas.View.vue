<template>
    <div class="container py-4">
        <h2 class="mb-4">Gestión de Notas</h2>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white py-3">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Subir Notas por Curso</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="cursoSelect" class="form-label">Selecciona un curso:</label>
                    <select id="cursoSelect" v-model="cursoSeleccionado" class="form-select w-50" @change="cargarAlumnos">
                        <option value="">-- Elige un curso --</option>
                        <option v-for="curso in cursos" :key="curso.id" :value="curso.id">{{ curso.nivel }} "{{ curso.paralelo }}"</option>
                    </select>
                </div>

                <div v-if="alumnos.length > 0" class="table-responsive mt-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Estudiante</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="alumno in alumnos" :key="alumno.id">
                                <td>{{ alumno.nombre }} {{ alumno.apellido }}</td>
                                <td>
                                    <input type="number" v-model.number="alumno.nota" class="form-control" min="0" max="100">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button @click="guardarNotas" class="btn btn-success mt-3">
                        <i class="fas fa-save me-2"></i> Guardar Notas
                    </button>
                </div>

                <div v-else-if="cursoSeleccionado" class="alert alert-info mt-4">
                    No hay alumnos registrados en este curso.
                </div>
            </div>
        </div>
    </div>

</template>
<script>    
export default {
    data() {
        return {
            cursoSeleccionado: '',
            cursos: [],
            alumnos: []
        }
    },
    methods: {
        cargarAlumnos() {
            // Aquí iría la lógica para cargar los alumnos del curso seleccionado
            // Por ejemplo, podrías hacer una petición a tu API para obtener los alumnos
            // y luego asignarlos a this.alumnos
        },
        guardarNotas() {
            // Aquí iría la lógica para guardar las notas de los alumnos
            // Podrías enviar un objeto con el curso seleccionado y las notas de los alumnos a tu API
        }
    },
    mounted() {
        // Aquí podrías cargar la lista de cursos al montar el componente
        // Por ejemplo, podrías hacer una petición a tu API para obtener los cursos
        // y luego asignarlos a this.cursos
    }
}

</script>
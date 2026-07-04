<template>
  <!-- Contenedor principal del componente -->
  <div class="container mt-5 pb-5">
    <!-- Contenedor del componente -->
    <div class="card shadow-lg border-0 overflow-hidden mb-4 rounded-4">
      <!-- Contenedor del header -->
      <div class="profile-header-bg"></div>
      <!-- Contenedor del cuerpo -->
      <div class="card-body pt-0 px-4">
        <!-- Contenedor de la información -->
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end profile-content">
          <!-- Contenedor del avatar -->
          <div class="profile-avatar-container shadow-sm mb-3 mb-md-0">
            <!-- Imagen del perfil, usamos el método getPhotoUrl para obtener la URL de la imagen -->
            <img :src="getPhotoUrl(Persona.id_persona)" class="profile-avatar" alt="Foto de perfil">
            <!-- Etiqueta para cambiar la foto -->
            <label for="fileInput" class="avatar-edit-badge" title="Cambiar foto">
              <i class="fas fa-camera"></i>
            </label>
            <!-- Botón para seleccionar la foto, se usa el evento change para ejecutar el método onFileSelected -->
            <input type="file" id="fileInput" @change="onFileSelected" hidden accept="image/*">
          </div>
          <!-- Contenedor de la información -->
          <div class="ms-md-4 text-center text-md-start mb-3">
            <!-- Título con nombre y apellidos del usuario logueado-->
            <h2 class="fw-bold text-blue mb-0">{{ Persona.nombres }} {{ Persona.apellidos }}</h2>
            <!-- Subtítulo -->
            <p class="text-muted mb-0">
              <!-- Cédula del usuario -->
              <span class="badge bg-gold-soft text-blue me-2">Cédula: {{ Persona.cedula }}</span>
              <!-- Rol del usuario -->
              <span class="small fw-bold text-uppercase"><i class="fas fa-id-badge me-1"></i> Perfil de Usuario</span>
              {{ Usuario.nombre_rol }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- Contenedor del componente -->
    <div class="card shadow-sm border-0 rounded-4">
      <!-- Contenedor del header -->
      <div class="card-header bg-white border-0 p-0">
        <!-- Contenedor de las navegaciones -->
        <ul class="nav nav-pills custom-nav-pills px-3 pt-3" id="perfilTabs" role="tablist">
          <!-- Navegación del perfil -->
          <li class="nav-item" role="presentation">
            <!-- Nav de Datos Personales -->
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personales" type="button">
              <i class="fas fa-user-edit me-2"></i>Datos Personales
            </button>
          </li>
          <!-- Navegación de Seguridad -->
          <li class="nav-item" role="presentation">
            <!-- Nav de Seguridad -->
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#usuario" type="button">
              <i class="fas fa-shield-alt me-2"></i>Seguridad de Cuenta
            </button>
          </li>
          <!-- Navegación de Familias, solo se muestra si el rol es diferente a Estudiante -->
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol !== 'Estudiante'">
            <!-- Nav de Familias, se llama al método getFamiliares para obtener las familias del usuario -->
            <button @click="getFamiliares" class="nav-link" data-bs-toggle="tab" data-bs-target="#familias"
              type="button">
              <i class="fas fa-users me-2"></i>Familias
            </button>
          </li>
          <!-- Navegación de Representantes, solo se muestra si el rol es Estudiante -->
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Estudiante'">
            <!-- Nav de Representantes, se llama al método getEstFamiliares para obtener los representantes del usuario -->
            <button @click="getEstFamiliares" class="nav-link" data-bs-toggle="tab" data-bs-target="#estufamilias"
              type="button">
              <i class="fas fa-users me-2"></i>Mi(s) Representante
            </button>
          </li>
          <!-- Navegación de Cursos, solo se muestra si el rol es Docente -->
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <!-- Nav de Cursos, se llama al método getCargaDocente para obtener los cursos del usuario -->
            <button @click="getCargaDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#mis-tutorias"
              type="button">
              <i class="fas fa-chalkboard-teacher me-2"></i>Mis Tutorías
            </button>
          </li>
          <!-- Navegación de Asignaturas, solo se muestra si el rol es Docente -->
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <!-- Nav de Asignaturas, se llama al método getCargaDocente para obtener las asignaturas del usuario -->
            <button @click="getCargaDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#mis-asignaturas"
              type="button">
              <i class="fas fa-book me-2"></i>Asignaturas Asignadas
            </button>
          </li>
          <!-- Navegación de Horarios, solo se muestra si el rol es Docente -->
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <!-- Nav de Horarios, se llama al método getHorarioDocente para obtener el horario del usuario -->  
            <button @click="getHorarioDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#horario-clases"
              type="button">
              <i class="fas fa-calendar-alt me-2"></i>Mi Horario
            </button>
          </li>
        </ul>
      </div>
      <!-- Contenedor del cuerpo -->
      <div class="card-body p-4">
        <!-- Contenedor de la información de cada nav tap -->
        <div class="tab-content" id="myTabContent">
          <!-- Contenedor del contenido, se muestra si se da click en Datos personales -->
          <div class="tab-pane fade show active" id="personales" role="tabpanel">
            <!-- Contenedor del cuerpo -->    
            <div class="info-section mb-4">
              <!-- Título -->
              <h5 class="text-blue fw-bold border-bottom pb-2">Información Básica</h5>
              <!-- Texto -->
              <p class="text-muted small">Desde aquí puedes gestionar tu información de contacto. Para cambios en
                nombres o cédula, contacta a secretaría.</p>
            </div>
              <!-- Contenedor de los campos de datos -->
            <div class="row g-3">
                <!-- Campo de correo -->  
              <div class="col-md-6">
                <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <!-- Campo de correo, se llena con el valor de la propiedad Persona.correo -->
                <input v-model="Persona.correo" type="email" class="form-control custom-input-profile"
                  placeholder="correo@ejemplo.com">
              </div>
                <!-- Campo de teléfono -->  
              <div class="col-md-6">
                  <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                <!-- Campo de teléfono, se llena con el valor de la propiedad Persona.telefono -->
                <input v-model="Persona.telefono" type="text" class="form-control custom-input-profile"
                  placeholder="09xxxxxxxx">
              </div>
                <!-- Campo de dirección -->
              <div class="col-md-12">
                  <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">DIRECCIÓN DE DOMICILIO</label>
                <!-- Campo de dirección, se llena con el valor de la propiedad Persona.direccion -->
                <input v-model="Persona.direccion" type="text" class="form-control custom-input-profile">
              </div>
                <!-- Botón de guardar -->
              <div class="col-12 text-end mt-4">
                <!-- Botón, se usa el evento click para llamar al método actualizarDatosPersonales, se usa v-bind:disabled="cargando" para deshabilitar el botón si está cargando -->
                <button @click="actualizarDatosPersonales" class="btn btn-gold px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-save me-2"></i> {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
              </div>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Seguridad de Cuenta -->
          <div class="tab-pane fade" id="usuario" role="tabpanel">
            <!-- Contenedor del cuerpo -->
            <div class="info-section mb-4">
              <!-- Título -->
              <h5 class="text-blue fw-bold border-bottom pb-2">Gestión de Acceso</h5>
              <!-- Texto -->
              <p class="text-muted small">Mantén tu cuenta segura cambiando tu contraseña periódicamente. El nombre de
                usuario no puede ser modificado.</p>
            </div>
              <!-- Contenedor de los campos de datos -->
            <div class="row g-3">
                <!-- Campo de nombre de usuario -->
              <div class="col-md-6">
                  <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">NOMBRE DE USUARIO</label>
                <!-- Campo de nombre de usuario, se llena con el valor de la propiedad Usuario.username -->
                <input v-model="Usuario.username" type="text" class="form-control bg-light text-muted" readonly>
              </div>
              <!-- Contenedor del campo de contraseña -->
              <div class="col-md-6">
                <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">NUEVA CONTRASEÑA</label>
                <!-- Campo de contraseña, se llena con el valor de la propiedad nuevaClave -->
                <input v-model="nuevaClave" type="password" class="form-control custom-input-profile"
                  placeholder="Mínimo 8 caracteres">
              </div>
              <!-- Contenedor del campo de confirmación de contraseña -->
              <div class="col-md-6">
                <!-- Etiqueta -->
                <label class="form-label small fw-bold text-muted">CONFIRMAR CONTRASEÑA</label>
                <!-- Campo de confirmación de contraseña, se llena con el valor de la propiedad confirmarClave -->
                <input v-model="confirmarClave" type="password" class="form-control custom-input-profile">
              </div>
              <!-- Contenedor del mensaje de recomendación -->
              <div class="col-12 mt-4">
                <!-- Alerta de información -->
                <div class="alert alert-info border-0 shadow-sm rounded-3">
                  <!-- Contenedor del texto -->
                  <div class="d-flex align-items-center">
                    <!-- Icono -->
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                      <!-- Título --> 
                      <h6 class="mb-0 fw-bold">Recomendación</h6>
                      <!-- Texto -->  
                      <small>Tu contraseña debe ser difícil de adivinar y no debes compartirla con nadie.</small>
                    </div>
                  </div>
                </div>
              </div>
                <!-- Botón de guardar --> 
              <div class="col-12 text-end">
                <!-- Botón, se usa el evento click para llamar al método actualizarCredenciales, se usa v-bind:disabled="cargando" para deshabilitar el botón si está cargando -->
                <button @click="actualizarCredenciales" class="btn btn-blue px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-key me-2"></i> Cambiar Contraseña
                </button>
              </div>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Familias -->
          <div class="tab-pane fade" id="familias" role="tabpanel">
            <!-- Contenedor del cuerpo -->
            <div class="info-section mb-4">
              <!-- Título --> 
              <h5 class="text-blue fw-bold border-bottom pb-2">Núcleo Familiar Registrado</h5>
              <!-- Texto -->
              <p class="text-muted small">A continuación se listan los familiares vinculados a su cuenta en el sistema
                académico.</p>
            </div>
              <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
            <div v-if="cargandoFamilia" class="text-center py-5">
              <!-- Indicador de carga -->
              <div class="spinner-border text-gold" role="status"></div>
              <!-- Texto -->
              <p class="mt-2 text-muted">Cargando familiares...</p>
            </div>
              <!-- Contenedor de mensaje de error, se muestra cuando no hay familiares -->
            <div v-else-if="familiares.length === 0" class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
              <!-- Contenedor del texto -->
              <div class="d-flex align-items-center">
                <!-- Icono -->
                <i class="fas fa-exclamation-circle fa-3x me-3 text-warning"></i>
                <!-- Contenedor del texto -->
                <div>
                  <!-- Título -->
                  <h6 class="fw-bold mb-1">Usted no posee familia asignada</h6>
                  <!-- Texto -->
                  <p class="mb-0 small">Debe dirigirse a la institución para registrar a su grupo familiar y completar
                    su expediente.</p>
                </div>
              </div>
            </div>
            <!-- Contenedor de los datos de la familia, se muestra cuando hay familiares -->
            <div v-else class="row g-3">
              <!-- Contenedor de cada familia, se usa v-for para recorrer el array de familiares y agregar datos a la página -->
              <div v-for="familiar in familiares" :key="familiar.id_persona" class="col-md-6">
                <!-- Contenedor del componente -->
                <div class="card border shadow-sm rounded-4 h-100 hvr-light">
                  <!-- Contenedor del cuerpo -->
                  <div class="card-body">
                    <!-- Contenedor de la información -->
                    <div class="d-flex align-items-center">
                      <!-- Contenedor del avatar/imagen de la familia, se usa el método getPhotoUrl para obtener la URL de la imagen -->
                      <img :src="familiar.foto ? 'data:image/jpeg;base64,' + familiar.foto : getPhotoUrl(null)"
                        class="rounded-circle border border-2 border-gold shadow-sm"
                        style="width: 70px; height: 70px; object-fit: cover;">
                        <!-- Contenedor de la información -->
                      <div class="ms-3">
                        <!-- Nombre y apellidos -->
                        <h6 class="mb-0 fw-bold text-blue">{{ familiar.nombres }} {{ familiar.apellidos }}</h6>
                        <!-- Parentesco -->
                        <span class="badge bg-gold text-blue small mb-1">{{ familiar.parentesco }}</span>
                        <!-- Cédula -->
                        <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i> {{ familiar.cedula }}</p>
                        <!-- Teléfono, si no existe, se muestra "Sinteléfono" -->
                        <p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i>
                          {{ familiar.telefono || 'Sinteléfono' }}</p>
                      </div>
                      <!-- Contenedor de botones -->
                      <div class="mt-3 border-top pt-3 text-end">
                        <!-- Botón, se usa el evento click para llamar al método verCalificaciones, se usa v-bind:disabled="cargando" para deshabilitar el botón si está cargando -->
                        <button @click="verCalificaciones(familiar)"
                          class="btn btn-sm btn-gold text-blue fw-bold rounded-pill px-3 shadow-sm">
                          <i class="fas fa-chart-bar me-1"></i> Ver Calificaciones
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Representantes -->
          <div class="tab-pane fade" id="estufamilias" role="tabpanel">
            <!-- Contenedor del cuerpo -->
            <div class="info-section mb-4">
              <!-- Título -->
              <h5 class="text-blue fw-bold border-bottom pb-2">Representante(s) registrado(s)</h5>
              <!-- Texto -->
              <p class="text-muted small">A continuación se muestra la información de los representantes registrados en
                el sistema.</p>
            </div>
              <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
            <div v-if="cargandoEstFamiliares" class="text-center py-5">
              <!-- Indicador de carga -->
              <div class="spinner-border text-gold" role="status"></div>
              <!-- Texto -->
              <p class="mt-2 text-muted">Cargando representantes...</p>
            </div>
              <!-- Contenedor de mensaje de error, se muestra cuando no hay representantes -->
            <div v-else-if="estfamiliares.length === 0" class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
              <!-- Contenedor del texto -->
              <div class="d-flex align-items-center">
                <!-- Icono -->
                <i class="fas fa-exclamation-circle fa-3x me-3 text-warning"></i>
                <!-- Contenedor del texto -->
                <div>
                  <!-- Título -->
                  <h6 class="fw-bold mb-1">No posee representante registrado</h6>
                  <!-- Texto -->
                  <p class="mb-0 small">Su representante debe registrarse en el sistema para poder realizar el
                    seguimiento de su estado de estudiante.</p>
                </div>
              </div>
            </div>
            <!-- Contenedor de los datos de la familia, se muestra cuando hay familiares -->
            <div v-else class="row g-3">
              <!-- Contenedor de cada familia, se usa v-for para recorrer el array de familiares y agregar datos a la página -->
              <div v-for="familiar in estfamiliares" :key="familiar.id_persona" class="col-md-6">
                <!-- Contenedor del componente -->
                <div class="card border shadow-sm rounded-4 h-100 hvr-light">
                  <!-- Contenedor del cuerpo -->
                  <div class="card-body">
                    <!-- Contenedor de la información -->
                    <div class="d-flex align-items-center">
                      <!-- Contenedor del avatar/imagen de la familia, se usa el método getPhotoUrl para obtener la URL de la imagen -->
                      <img :src="familiar.foto ? 'data:image/jpeg;base64,' + familiar.foto : getPhotoUrl(null)"
                        class="rounded-circle border border-2 border-gold shadow-sm"
                        style="width: 70px; height: 70px; object-fit: cover;">
                        <!-- Contenedor de la información -->
                      <div class="ms-3">
                        <!-- Nombre y apellidos -->
                        <h6 class="mb-0 fw-bold text-blue">{{ familiar.nombres }} {{ familiar.apellidos }}</h6>
                        <!-- Parentesco -->
                        <span class="badge bg-gold text-blue small mb-1">{{ familiar.parentesco }}</span>
                        <!-- Cédula -->
                        <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i> {{ familiar.cedula }}</p>
                        <!-- Teléfono, si no existe, se muestra "Sinteléfono" -->
                        <p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i>
                          {{ familiar.telefono || 'Sinteléfono' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Mis tutorias -->
          <div class="tab-pane fade" id="mis-tutorias" role="tabpanel">
            <!-- Contenedor del cuerpo -->
            <div class="info-section mb-4">
              <!-- Título -->
              <h5 class="text-blue fw-bold border-bottom pb-2">Cursos bajo mi Tutoría</h5>
              <!-- Texto -->
              <p class="text-muted small">Como docente tutor, usted es responsable del seguimiento integral de estos
                paralelos.</p>
            </div>
              <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
            <div v-if="cargaDocente.tutorias.length === 0" class="alert alert-light border shadow-sm rounded-4">
              <!-- Icono con texto-->
              <i class="fas fa-info-circle me-2"></i> Usted no tiene cursos asignados como tutor en este periodo.
            </div>
              <!-- Contenedor de los cursos, se muestra cuando hay cursos -->
            <div class="row g-3">
                <!-- Contenedor de cada curso, se usa v-for para recorrer el array de cursos y agregar datos a la página -->
              <div v-for="curso in cargaDocente.tutorias" :key="curso.id_curso" class="col-md-6">
                <!-- Contenedor del componente -->
                <div class="card border-start border-gold border-4 shadow-sm rounded-3">
                  <!-- Contenedor del cuerpo -->
                  <div class="card-body">
                      <!-- Título -->
                    <h6 class="fw-bold text-blue mb-1">
                      {{ curso.nivel.nombre }} "{{ curso.paralelo }}"
                    </h6>
                      <!-- Texto -->
                    <p class="mb-0 small text-muted text-uppercase fw-bold">
                      {{ curso.especialidad.nombre }}
                    </p>
                      <!-- Contenedor de la información -->
                    <div class="mt-2">
                      <span class="badge bg-gold-soft text-blue">Periodo: {{ curso.periodo.nombre }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Mis asignaturas -->
          <div class="tab-pane fade" id="mis-asignaturas" role="tabpanel">
            <!-- Contenedor del cuerpo -->
            <div class="info-section mb-4">
              <!-- Título -->
              <h5 class="text-blue fw-bold border-bottom pb-2">Asignaturas que Imparto</h5>
              <!-- Texto -->
              <p class="text-muted small">Listado de materias asignadas y los cursos correspondientes.</p>
            </div>
              <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
            <div v-if="cargaDocente.asignaturas.length === 0" class="alert alert-light border shadow-sm rounded-4">
              <!-- Icono con texto--> 
              <i class="fas fa-info-circle me-2"></i> No se encontraron asignaturas asignadas a su perfil.
            </div>
              <!-- Contenedor de la tabla, animación Fade Up -->
            <div class="table-responsive">
                <!-- Contenedor de la tabla --> 
              <table class="table table-hover align-middle border rounded-3 overflow-hidden">
                <!-- Contenedor de la cabecera -->
                <thead class="table-light text-blue">
                  <!-- Contenedor de la cabecera -->  
                  <tr>
                    <th class="small fw-bold">ASIGNATURA</th>
                    <th class="small fw-bold">CURSO / PARALELO</th>
                    <th class="small fw-bold text-center">H. SEMANALES</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Contenedor de los datos de la tabla, se usa v-for para recorrer el array de asignaturas y agregar datos a la tabla -->
                  <tr v-for="item in cargaDocente.asignaturas" :key="item.id_curso_asignatura">
                    <!-- Contenedor del cuerpo -->
                    <td>
                      <!-- Contenedor de la información -->
                      <div class="d-flex align-items-center">
                        <!-- Contenedor del icono -->
                        <div class="icon-box bg-blue-soft text-blue me-2">
                            <!-- Icono -->
                          <i class="fas fa-book-open"></i>
                        </div>
                        <!-- Nombre de la asignatura -->
                        <span class="fw-bold">{{ item.asignatura.nombre }}</span>
                      </div>
                    </td>
                    <td>
                      <!-- Nombre del nivel y paralelo -->
                      <span class="text-muted">{{ item.curso.nivel.nombre }} "{{ item.curso.paralelo }}"</span>
                      <br>
                      <!-- Nombre de la especialidad -->
                      <small class="text-gold fw-bold">{{ item.curso.especialidad.nombre }}</small>
                    </td>
                      <!-- Contenedor de la información -->
                    <td class="text-center">
                      <span class="badge rounded-pill bg-light text-dark border">{{ item.horas_semanales }} horas</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Contenedor del contenido, se muestra si se da click en Horarios clases -->
          <div class="tab-pane fade" id="horario-clases" role="tabpanel">
              <!-- Contenedor del cuerpo -->  
            <div class="info-section mb-4">
              <!-- Título --> 
              <h5 class="text-blue fw-bold border-bottom pb-2">Horario Semanal de Clases</h5>
              <!-- Texto -->  
              <p class="text-muted small">Visualice su planificación semanal. Los horarios están sujetos a cambios por
                parte de coordinación académica.</p>
            </div>
              <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
            <div v-if="cargandoHorario" class="text-center py-5">
              <!-- Indicador de carga -->
              <div class="spinner-border text-gold" role="status"></div>
              <!-- Texto -->
              <p class="mt-2 text-muted">Generando cronograma...</p>
            </div>
              <!-- Contenedor de mensaje de error, se muestra cuando no hay horarios -->
            <div v-else-if="horario.length === 0" class="alert alert-light border shadow-sm rounded-4 text-center">
              <!-- Icono con texto-->
              <i class="fas fa-calendar-times fa-2x mb-2 text-muted"></i>
              <!-- Texto -->
              <p class="mb-0">No se han registrado horas de clase para su usuario todavía.</p>
            </div>
              <!-- Contenedor de la tabla, animación Fade Up -->  
            <div v-else class="table-responsive shadow-sm rounded-4">
                <!-- Contenedor de la tabla -->   
              <table class="table table-bordered align-middle mb-0 text-center custom-table-schedule">
                <!-- Contenedor de la cabecera -->
                <thead class="bg-blue text-white">
                  <!-- Contenedor de la cabecera -->  
                  <tr>
                    <th class="py-3">Hora</th>
                    <th class="py-3">Lunes</th>
                    <th class="py-3">Martes</th>
                    <th class="py-3">Miércoles</th>
                    <th class="py-3">Jueves</th>
                    <th class="py-3">Viernes</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Contenedor de los datos de la tabla, se usa v-for para recorrer el array de horarios y agregar datos a la tabla -->
                  <tr v-for="(fila, index) in horario" :key="index">
                    <!-- Contenedor del cuerpo , rango de filas para diseñar el horario -->
                    <td class="fw-bold text-blue bg-light" style="width: 15%;">
                      {{ fila.rango }}
                    </td>
                      <!-- Contenedor de los datos de la fila, se usa v-for para recorrer los días y agregar datos a la fila -->
                    <td v-for="dia in ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']" :key="dia"
                      class="p-0 position-relative" style="width: 17%; height: 80px;">
                      <!-- Contenedor del cuerpo, se usa v-if para mostrar el contenido de la fila para cada día -->
                      <div v-if="fila[dia]" class="p-2 h-100 d-flex flex-column justify-content-center">
                        <!-- Nombre de la asignatura -->
                        <div class="fw-bold text-blue mb-1" style="font-size: 0.85rem; line-height: 1.2;">
                          {{ fila[dia].asignatura }}
                        </div>
                        <!-- Nombre del curso -->
                        <div class="text-muted mb-1" style="font-size: 0.75rem;">
                            <!-- Icono mas informacion-->
                          <i class="fas fa-chalkboard text-gold me-1"></i> {{ fila[dia].curso }}
                        </div>
                        <!-- Nombre de la especialidad -->
                        <div class="text-uppercase fw-bold text-blue" style="font-size: 0.65rem; opacity: 0.8;">
                          {{ fila[dia].especialidad }}
                        </div>
                      </div>
                      <!-- Contenedor del cuerpo, si no hay datos se muestra un fondo blanco -->
                      <div v-else class="h-100 bg-white"></div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Contenedor del modal de reporte de calificaciones -->
  <div class="modal fade" id="modalCalificaciones" tabindex="-1" aria-hidden="true">
    <!-- Contenedor del cuerpo -->
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <!-- Contenedor del cuerpo -->
      <div class="modal-content border-0 shadow-lg rounded-4">
        <!-- Contenedor del encabezado -->
        <div class="modal-header bg-blue text-white rounded-top-4">
          <!-- Título -->
          <h5 class="modal-title fw-bold">
            <!-- Icono -->
            <i class="fas fa-user-graduate me-2"></i> Reporte de Calificaciones
          </h5>
          <!-- Botón de cerrar el modal -->
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <!-- Contenedor del cuerpo -->
        <div class="modal-body p-4" style="background-color: #f8f9fa;">
          <!-- Contenedor carga de datos, se muestra mientras se está cargando los datos -->
          <div v-if="cargandoCalificaciones" class="text-center py-5">
            <!-- Indicador de carga -->
            <div class="spinner-border text-gold" role="status"></div>
            <!-- Texto -->
            <p class="mt-2 text-muted">Consultando registro académico...</p>
          </div>
            <!-- Contenedor de datos, se muestra cuando hay datos -->
          <div v-else-if="datosAcademicos">
            <!-- Contenedor del componente -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-gold border-5">
              <!-- Contenedor del cuerpo -->
              <div class="card-body">
                  <!-- Contenedor de la información -->
                <div class="row align-items-center">
                  <!-- Contenedor del texto -->
                  <div class="col-md-8">
                      <!-- Información del curso -->
                    <h5 class="fw-bold text-blue mb-1">
                      {{ datosAcademicos.curso.nivel }} "{{ datosAcademicos.curso.paralelo }}"
                    </h5>
                      <!-- Información de la especialidad -->
                    <p class="text-muted mb-0 small">
                      <span v-if="datosAcademicos.curso.especialidad">{{ datosAcademicos.curso.especialidad }} |</span>
                      Periodo Lectivo: <span class="fw-bold">{{ datosAcademicos.curso.periodo }}</span>
                    </p>
                  </div>
                  <!-- Contenedor del botón -->
                  <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-gold text-blue fs-6 px-3 py-2">
                      {{ estudianteSeleccionado?.nombres }} {{ estudianteSeleccionado?.apellidos }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <!-- Contenedor de la información de las notas del estudiante -->
            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4" role="alert">
              <!-- Título -->
              <h6 class="fw-bold mb-2"><i class="fas fa-calculator me-2"></i> ¿Cómo se calculan las notas?</h6>
              <!-- Contenedor de la información -->
              <ul class="mb-0 small">
                <!-- Contenedor de la información -->
                <li><strong>Parciales (P1, P2, P3):</strong> Se promedian 4 insumos: Tareas, A. Individuales, A.
                  Grupales y
                  Lecciones.</li>
                <li><strong>Promedio Anual:</strong> Requiere mínimo 7/10 para aprobación directa.</li>
                <li><strong>Supletorio:</strong> Se habilita si el anual está entre 5 y 6.99. Aprueba con 7.</li>
                <li><strong>Remedial:</strong> Se habilita si el anual es < 5 o reprobó supletorio.</li>
                <li><strong>Gracia:</strong> Se habilita si reprobó remedial en una sola asignatura.</li>
                <li><strong>Nota Final:</strong> Si aprueba en recuperación, la nota final será siempre 7.00.</li>
                <li><strong>Restricciones:</strong> El sistema no permitirá ingresar valores menores a 0 ni mayores a
                  10.</li>
              </ul>
            </div>
            <!-- Contenedor de la tabla de notas -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Contenedor de la tabla -->
              <div class="table-responsive">
                <!-- Contenedor de la tabla -->
                <table class="table table-hover table-bordered align-middle mb-0 text-center text-nowrap">
                  <!-- Contenedor de la cabecera -->
                  <thead class="bg-blue text-white">
                    <!-- Contenedor de la cabecera -->
                    <tr>
                      <!-- Contenedor de la cabecera de la asignatura -->
                      <th rowspan="2" class="text-start ps-4 align-middle">Asignatura</th>
                      <!-- Contenedor de la cabecera de la quimestre 1 -->
                      <th colspan="4" class="text-center">Quimestre 1</th>
                      <!-- Contenedor de la cabecera de la quimestre 2, se muestra si hay notas de quimestre 2 -->
                      <th v-if="mostrarColumnas.q2" colspan="4" class="text-center border-start border-light">Quimestre
                        2</th>
                        <!-- Contenedor de la cabecera de la promedio anual, se muestra si hay notas de quimestre 2 -->
                      <th v-if="mostrarColumnas.q2" rowspan="2"
                        class="align-middle bg-secondary bg-opacity-25 border-start text-white border-light">Prom. Anual
                      </th>
                      <!-- Contenedor de la cabecera de la supletorio, se muestra si hay notas de supletorio -->
                      <th v-if="mostrarColumnas.supletorio" rowspan="2" class="align-middle bg-warning text-dark">
                        Supletorio</th>
                        <!-- Contenedor de la cabecera de la remedial, se muestra si hay notas de remedial -->
                      <th v-if="mostrarColumnas.remedial" rowspan="2" class="align-middle bg-info text-dark">Remedial
                      </th>
                      <!-- Contenedor de la cabecera de la gracia, se muestra si hay notas de gracia -->  
                      <th v-if="mostrarColumnas.gracia" rowspan="2" class="align-middle bg-primary text-white">Gracia
                      </th>
                      <!-- Contenedor de la cabecera de la nota final, se muestra si hay notas de quimestre 2 --> 
                      <th v-if="mostrarColumnas.q2" rowspan="2" class="align-middle bg-gold text-blue">Nota Final</th>
                      <!-- Contenedor de la cabecera de la estado, se muestra si hay notas de quimestre 2 -->
                      <th v-if="mostrarColumnas.q2" rowspan="2" class="align-middle">Estado</th>
                    </tr>
                    <!-- Contenedor de la cabecera de la promedio parcial -->   
                    <tr class="bg-blue-light text-white" style="background-color: #2a3d8f;">
                      <th class="small fw-normal">P1</th>
                      <th class="small fw-normal">P2</th>
                      <th class="small fw-normal">P3</th>
                      <th class="small fw-bold">Prom</th>
                      <!-- Contenedor de la cabecera de la promedio parcial, se muestra si hay notas de quimestre 2 --> 
                      <th v-if="mostrarColumnas.q2" class="small fw-normal border-start border-light">P1</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-normal">P2</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-normal">P3</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-bold">Prom</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    <!-- Contenedor de los datos de la tabla, se usa v-for para recorrer el array de calificaciones y agregar datos a la tabla -->
                    <tr v-for="cal in datosAcademicos.calificaciones" :key="cal.asignatura">
                      <td class="text-start ps-4 fw-bold text-blue">{{ cal.asignatura }}</td>

                      <td>{{ cal.q1_p1 || '-' }}</td>
                      <td>{{ cal.q1_p2 || '-' }}</td>
                      <td>{{ cal.q1_p3 || '-' }}</td>
                      <td class="fw-bold bg-light">{{ cal.q1_promedio || '-' }}</td>

                      <td v-if="mostrarColumnas.q2" class="border-start">{{ cal.q2_p1 || '-' }}</td>
                      <td v-if="mostrarColumnas.q2">{{ cal.q2_p2 || '-' }}</td>
                      <td v-if="mostrarColumnas.q2">{{ cal.q2_p3 || '-' }}</td>
                      <td v-if="mostrarColumnas.q2" class="fw-bold bg-light">{{ cal.q2_promedio || '-' }}</td>

                      <td v-if="mostrarColumnas.q2" class="fw-bold bg-light border-start">{{ cal.promedio_anual || '-'
                      }}</td>
                      <td v-if="mostrarColumnas.supletorio">{{ cal.nota_supletorio || '-' }}</td>
                      <td v-if="mostrarColumnas.remedial">{{ cal.nota_remedial || '-' }}</td>
                      <td v-if="mostrarColumnas.gracia">{{ cal.nota_gracia || '-' }}</td>

                      <td v-if="mostrarColumnas.q2" class="fw-bold fs-6"
                        :class="Number(cal.nota_final_definitiva) < 7 ? 'text-danger' : 'text-success'">
                        {{ cal.nota_final_definitiva || '-' }}
                      </td>
                      <td v-if="mostrarColumnas.q2">
                        <span v-if="cal.estado_asignatura" class="badge" :class="badgeEstado(cal.estado_asignatura)">
                          {{ cal.estado_asignatura }}
                        </span>
                        <span v-else>-</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- Contenedor de mensaje de error, se muestra cuando no hay datos -->
          <div v-else class="alert alert-warning border-0 shadow-sm rounded-4">
            No se encontró información académica para este estudiante en el periodo actual.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * perfil es un componente en el que se encuentra toda la lógica de la aplicación
 * Se muestra la información de usuario logueado, se verifica si es docente, estudiante, respresentante, administrador, etc.
 * A más de eso se valida que el usuario loguea tenga familiares registrados, si es docente se muestra la información de sus asignaturas y cursos, si es estudiante se muestra su horario y calificaciones.
 * Si es representante se muestra la información de sus representados y sus calificaciones.
 * Se importa API para hacer las peticiones al backend, se importa mostraralertas para mostrar mensajes de alerta y 
 * se importa getMe para obtener la información del usuario logueado.
 * Se importa bootstrap para usar los modales de bootstrap.
 * 
 */
import API from "@/assets/js/axios" // Importa la instancia de Axios para hacer peticiones HTTP
import { mostraralertas } from "@/assets/js/funciones/functions";// Importa la función para mostrar alertas
import { getMe } from "@/assets/js/auth";// Importa la función para obtener la información del usuario logueado
import * as bootstrap from 'bootstrap'; // Importa Bootstrap para usar los modales de Bootstrap
/**
 * Exporta el componente Vue con su configuración, datos, métodos y ciclo de vida
 * Usamos data para definir las variables reactivas del componente
 * Usamos computed para definir propiedades computadas que dependen de otras variables
 * Usamos mounted para ejecutar código cuando el componente se monta en el DOM
 * Usamos methods para definir funciones que se pueden llamar desde el template o desde otros métodos
 */
export default {
  /**
   * Data: Define las variables reactivas del componente, que se pueden usar en el template y en los métodos
   * @returns {Object} Objeto con las variables reactivas
   */
  data() {
    /**
     * Return: Devuelve un objeto con las variables reactivas del componente
     * baseUrl: URL base para las peticiones al backend
     * Persona: Objeto con la información de la persona logueada
     * Usuario: Objeto con la información del usuario logueado
     * familiares: Array con los familiares del usuario logueado  
     * estfamiliares: Array con los familiares del estudiante logueado
     * nuevaClave: String con la nueva clave del usuario logueado
     * confirmarClave: String con la confirmación de la nueva clave del usuario logueado
     * cargando: Boolean que indica si se está cargando información del usuario logueado
     * refreshKey: Número que se usa para actualizar la imagen del usuario logueado
     * idpersona: Número que identifica al usuario logueado
     * idusuario: Número que identifica al usuario logueado
     * idrole: Número que identifica al rol del usuario logueado
     * cargandoFamilia: Boolean que indica si se está cargando información del usuario logueado
     * cargandoEstFamiliares: Boolean que indica si se está cargando los familiares del estudiante logueado
     * cargaDocente: Objeto con la información del docente logueado
     * cargandoCargaDocente: Boolean que indica si se está cargando la información académica del docente logueado
     * horario: Array con los horarios del docente logueado
     * cargandoHorario: Boolean que indica si se está cargando los horarios del docente logueado
     * estudianteSeleccionado: Objeto con la información del estudiante seleccionado
     * datosAcademicos: Objeto con la información académica del estudiante seleccionado
     * cargandoCalificaciones: Boolean que indica si se está cargando la información académica del estudiante seleccionado
     * modalCalificaciones: Objeto con la instancia del modal de reporte de calificaciones
     * 
     */
    return {
      baseUrl: "/sistma",
      Persona: {},
      Usuario: {},
      familiares: {},
      estfamiliares: {},
      nuevaClave: "",
      confirmarClave: "",
      cargando: false,
      refreshKey: Date.now(),
      idpersona: 0,
      idusuario: 0,
      idrole: 0,
      cargandoFamilia: false,
      cargandoEstFamiliares: false,
      cargaDocente: {
        tutorias: [],
        asignaturas: []
      },
      cargandoCargaDocente: false,
      horario: [],
      cargandoHorario: false,
      estudianteSeleccionado: null,
      datosAcademicos: null,
      cargandoCalificaciones: false,
      modalCalificaciones: null,
    }
  },
  /**
   * Computed: Define propiedades computadas que dependen de otras variables y se actualizan automáticamente cuando cambian esas variables
   * mostrarColumnas: Retorna true/false por cada columna dependiendo si hay datos en alguna asignatura
   * @returns {Object} Objeto con las propiedades computadas
   */
  computed: {
    /**
     * MostrarColumnas: Es una propiedad computada que retorna un objeto con las propiedades q2, supletorio, remedial y gracia, que indican si se deben mostrar esas columnas en la tabla de calificaciones.
     * @returns {Object} Objeto con las propiedades computadas
     */
    mostrarColumnas() {
      // Retorna true/false por cada columna dependiendo si hay datos en alguna asignatura
      const cal = this.datosAcademicos?.calificaciones || [];

      return {
        // Se muestra Q2 si algún parcial o el promedio de Q2 es mayor a 0
        q2: cal.some(c =>
          (c.q2_p1 !== null && c.q2_p1 !== undefined && Number(c.q2_p1) > 0) ||
          (c.q2_p2 !== null && c.q2_p2 !== undefined && Number(c.q2_p2) > 0) ||
          (c.q2_p3 !== null && c.q2_p3 !== undefined && Number(c.q2_p3) > 0) ||
          (c.q2_promedio !== null && c.q2_promedio !== undefined && Number(c.q2_promedio) > 0)
        ),

        // Aplicamos la misma lógica para los exámenes de recuperación para evitar que se muestren si traen "0.00" por defecto de la BD
        supletorio: cal.some(c => c.nota_supletorio !== null && c.nota_supletorio !== undefined && Number(c.nota_supletorio) > 0),
        remedial: cal.some(c => c.nota_remedial !== null && c.nota_remedial !== undefined && Number(c.nota_remedial) > 0),
        gracia: cal.some(c => c.nota_gracia !== null && c.nota_gracia !== undefined && Number(c.nota_gracia) > 0),
      };
    }
  },
  /**
   * Mounted: Es un hook del ciclo de vida de Vue que se ejecuta cuando el componente se monta en el DOM. Se usa para inicializar datos y hacer peticiones al backend.
   * Se obtiene la información del usuario logueado con getMe(), se asignan los valores de idpersona y idusuario, se obtienen las informaciones de la persona y del usuario logueado, y se inicializa el estado cargando.
   * Se utiliza async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend. 
   * El promise.all() permite ejecutar varias promesas en paralelo y esperar a que todas se resuelvan antes de continuar con la ejecución del código.
   * @returns {Promise<void>} Retorna una promesa que se resuelve cuando se hayan realizado todas las peticiones.
   */
  async mounted() {
    this.cargando = true;// Inicializar el estado cargando
    const me = await getMe();// Obtener la información del usuario logueado
    this.idpersona = me.id_persona;// Asignar el valor de id_persona a la propiedad idpersona
    this.idusuario = me.id_usuario;// Asignar el valor de id_usuario a la propiedad idusuario
    await Promise.all([this.getPersona(), this.getUsuario()]);// Obtener la información de persona y usuario logueado
    this.cargando = false;// Limpiar el estado cargando
  },
  /**
   * Methods: Define los métodos que se pueden llamar desde el template o desde otros métodos
   * Los método utilizados en este componente son:
   * verCalificaciones: Método para mostrar la información de las calificaciones del estudiante seleccionado
   * badgeEstado: Método para obtener el color de la bandera de estado de la calificación del estudiante seleccionado
   * getPersona: Método para obtener la información de la persona logueada
   * getFamiliares: Método para obtener la información de los familiares del usuario logueado y actualizar la variable familiares
   * getEstFamiliares: Método para obtener la información de los familiares del estudiante logueado y actualizar la variable estfamiliares
   * getHorarioDocente: Método para obtener la información del horario del docente logueado y actualizar la variable horario
   * getCargaDocente: Método para obtener la información académica del docente logueado y actualizar la variable cargaDocente
   * getUsuario: Método para obtener la información del usuario logueado y actualizar la variable Usuario
   * getPhotoUrl: Método para obtener la URL de la imagen del usuario logueado
   * onFileSelected: Método para actualizar la imagen del usuario logueado
   * actualizarDatosPersonales: Método para actualizar la información personal del usuario logueado y mostrar un mensaje de alerta  
   * actualizarCredenciales: Método para actualizar la contraseña del usuario logueado y mostrar un mensaje de alerta 
   */
  methods: {
    /**
     * verCalificaciones: Método para mostrar la información de las calificaciones del estudiante seleccionado
     * Este método se utiliza para mostrar la información de las calificaciones del estudiante seleccionado en la página de perfil del estudiante.
     * Se utiliza la propiedad estudianteSeleccionado para obtener la información de la calificaciones del estudiante seleccionado. 
     * Si la información de la calificaciones del estudiante seleccionado no está cargada, se inicializa el estado cargandoCalificaciones y se invoca la función cargarDatosAcademicos para cargar la información de la calificaciones del estudiante seleccionado. 
     * Si la información de la calificaciones del estudiante seleccionado ya está cargada, se muestra la información de la calificaciones del estudiante seleccionado en la página de perfil del estudiante.  
     * Se recibe como parámetro familiar, que es el objeto del estudiante seleccionado.
     * @param familiar 
     */
    async verCalificaciones(familiar) {
      this.estudianteSeleccionado = familiar;// Asignar el valor de familiar a la propiedad estudianteSeleccionado
      this.datosAcademicos = null;// Limpiar la propiedad datosAcademicos 
      this.cargandoCalificaciones = true;// Inicializar el estado cargandoCalificaciones   
      //Si no existe la instancia del modal de reporte de calificaciones, se crea una nueva instancia
      if (!this.modalCalificaciones) {
        this.modalCalificaciones = new bootstrap.Modal(document.getElementById('modalCalificaciones'));// Crear instancia del modal de reporte de calificaciones
      }
      this.modalCalificaciones.show();// Mostrar el modal de reporte de calificaciones  

      try {
        const res = await API.get(`${this.baseUrl}/calificaciones-actuales/${familiar.id_persona}`);// Llamada a la API para obtener la información de las calificaciones del estudiante seleccionado
        this.datosAcademicos = res.data;// Asignar el valor de res.data a la propiedad datosAcademicos
      } catch (e) {
        console.error(e);// Si hay un error, mostrar un mensaje de error
        //Si el error es 404, ignoramos el mensaje de error ya que ya se muestra el mensaje en el modal de reporte de calificaciones
        if (e.response && e.response.status === 404) {
          // Ignoramos el toast si es 404 porque ya mostramos el mensaje en el modal
        } else {
          mostraralertas("Error al conectar con el servidor", "error");// Mostrar un mensaje de error
        }
      } finally {
        this.cargandoCalificaciones = false;// Limpiar el estado cargandoCalificaciones
      }
    },
    /**
     * badgeEstado: Método para obtener el color de la bandera de estado de la calificación del estudiante seleccionado
     * Este método se utiliza para obtener el color de la bandera de estado de la calificación del estudiante seleccionado en la página de perfil del estudiante.
     * Se utiliza la propiedad datosAcademicos para obtener la información de la calificación del estudiante seleccionado.  
     * Si la información de la calificación del estudiante seleccionado no está cargada, se inicializa el estado cargandoCalificaciones y se invoca la función cargarDatosAcademicos para cargar la información de la calificación del estudiante seleccionado. 
     * Si la información de la calificación del estudiante seleccionado ya está cargada, se devuelve el color de la bandera de estado de la calificación del estudiante seleccionado en la página de perfil del estudiante.  
     * Se recibe como parámetro estado, que es el estado de la calificación del estudiante seleccionado.
     * @param estado 
     */
    badgeEstado(estado) {
      //Si no existe el estado, devuelve el color de la bandera de estado de la calificación del estudiante seleccionado en la página de perfil del estudiante
      if (!estado) return 'bg-secondary';
      //Se utiliza un switch case para devolver el color de la bandera de estado de la calificación del estudiante seleccionado en la página de perfil del estudiante
      switch (estado.toLowerCase()) {
        case 'aprobado': return 'bg-success';// Si el estado es aprobado, devuelve el color verde
        case 'supletorio': return 'bg-warning text-dark';// Si el estado es supletorio, devuelve el color amarillo oscuro y negro
        case 'remedial': return 'bg-info text-dark';// Si el estado es remedial, devuelve el color azul oscuro y negro
        case 'gracia': return 'bg-primary';// Si el estado es gracia, devuelve el color azul
        case 'reprobado': return 'bg-danger';// Si el estado es reprobado, devuelve el color rojo 
        default: return 'bg-secondary';// Si el estado es desconocido, devuelve el color gris
      }
    },
    /**
     * getPersona: Método para obtener la información de la persona logueada
     * Este método se utiliza para obtener la información de la persona logueada en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información de la persona logueada. 
     * Si la información de la persona logueada no está cargada, se inicializa el estado cargando y se invoca la función cargarDatosPersonales para cargar la información de la persona logueada. 
     * Si la información de la persona logueada ya está cargada, se muestra la información de la persona logueada en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend. 
     */
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);// Llamada a la API para obtener la información de la persona logueada
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { 
        //Si hay un error, mostrar un mensaje de error
        console.error(err); 
      }
    },
    /**
     * getFamiliares: Método para obtener la información de los familiares del usuario logueado y actualizar la variable familiares
     * Este método se utiliza para obtener la información de los familiares del usuario logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información de los familiares del usuario logueado. 
     * Si la información de los familiares del usuario logueado no está cargada, se inicializa el estado cargandoFamilia y se invoca la función cargarDatosPersonales para cargar la información de los familiares del usuario logueado. 
     * Si la información de los familiares del usuario logueado ya está cargada, se muestra la información de los familiares del usuario logueado en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async getFamiliares() {
      if (this.familiares.length > 0) return; // Evita recargar si ya hay datos

      try {
        this.cargandoFamilia = true;// Inicializar el estado cargandoFamilia
        const res = await API.get(`${this.baseUrl}/familiares-de/${this.idpersona}`);// Llamada a la API para obtener la información de los familiares del usuario logueado
        this.familiares = res.data.data;// Asignar el valor de res.data.data a la propiedad familiares
      } catch (e) {
        //Si hay un error, mostrar un mensaje de error
        console.error("Error al traer familiares:", e);//motrar error en consola
        mostraralertas("No se pudo obtener la información de familia", "error");//mostrar un mensaje de error
      } finally {
        //Limpiar el estado cargandoFamilia
        this.cargandoFamilia = false;
      }
    },
    /**
     * getEstFamiliares: Método para obtener la información de los familiares del estudiante logueado y actualizar la variable estfamiliares
     * Este método se utiliza para obtener la información de los familiares del estudiante logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información de los familiares del estudiante logueado. 
     * Si la información de los familiares del estudiante logueado no está cargada, se inicializa el estado cargandoEstFamiliares y se invoca la función cargarDatosPersonales para cargar la información de los familiares del estudiante logueado. 
     * Si la información de los familiares del estudiante logueado ya está cargada, se muestra la información de los familiares del estudiante logueado en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async getEstFamiliares() {
      if (this.estfamiliares.length > 0) return; // Evita recargar si ya hay datos

      try {
        this.cargandoEstFamiliares = true;// Inicializar el estado cargandoEstFamiliares
        const res = await API.get(`${this.baseUrl}/familiares-est-de/${this.idpersona}`);// Llamada a la API para obtener la información de los familiares del estudiante logueado
        this.estfamiliares = res.data.data;// Asignar el valor de res.data.data a la propiedad estfamiliares
      } catch (e) {
        //Si hay un error, mostrar un mensaje de error
        console.error("Error al traer familiares:", e);//motrar error en consola
        mostraralertas("No se pudo obtener la información de familia", "error");//mostrar un mensaje de error
      } finally {
        //Limpiar el estado cargandoEstFamiliares
        this.cargandoEstFamiliares = false;
      }
    },
    /**
     * getHorarioDocente: Método para obtener la información del horario del docente logueado y actualizar la variable horario
     * Este método se utiliza para obtener la información del horario del docente logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información del horario del docente logueado. 
     * Si la información del horario del docente logueado no está cargada, se inicializa el estado cargandoHorario y se invoca la función cargarDatosPersonales para cargar la información del horario del docente logueado. 
     * Si la información del horario del docente logueado ya está cargada, se muestra la información del horario del docente logueado en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async getHorarioDocente() {
      try {
        this.cargandoHorario = true;// Inicializar el estado cargandoHorario
        const res = await API.get(`${this.baseUrl}/horarios_docente/${this.idpersona}`);// Llamada a la API para obtener la información del horario del docente logueado
        const datosBrutos = res.data;// Obtener los datos brutos de la respuesta

        // Agrupamos por rango de hora para crear filas únicas
        const grupos = {};
        //asignar los datos brutos a grupos, se utiliza un bucle forEach para recorrer cada elemento del arreglo datosBrutos
        datosBrutos.forEach(item => {
          const rango = `${item.inicio} - ${item.fin}`;// Obtener el rango de hora del elemento
          //Si no existe el rango en grupos, se crea un objeto con las propiedades rango y los días de la semana inicializados en null
          if (!grupos[rango]) {
            //Crear un objeto con las propiedades rango y los días de la semana inicializados en null
            grupos[rango] = {
              rango: rango,
              Lunes: null,
              Martes: null,
              Miércoles: null,
              Jueves: null,
              Viernes: null
            };
          }
          // Asignamos la materia al día correspondiente dentro de ese rango
          grupos[rango][item.dia] = item;
        });

        // Convertimos el objeto a un array ordenado por hora
        this.horario = Object.values(grupos).sort((a, b) => a.rango.localeCompare(b.rango));

      } catch (err) {
        //Si hay un error, mostrar un mensaje de error
        mostraralertas("Error al cargar horario", "error");
      } finally {
        //Limpiar el estado cargandoHorario
        this.cargandoHorario = false;
      }
    },
    /**
     * getCargaDocente: Método para obtener la información académica del docente logueado y actualizar la variable cargaDocente
     * Este método se utiliza para obtener la información académica del docente logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información académica del docente logueado. 
     * Si la información académica del docente logueado no está cargada, se inicializa el estado cargandoCargaDocente y se invoca la función cargarDatosPersonales para cargar la información académica del docente logueado. 
     * Si la información académica del docente logueado ya está cargada, se muestra la información académica del docente logueado en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async getCargaDocente() {
      // Evitamos peticiones repetidas si ya cargó
      if (this.cargaDocente.tutorias.length > 0 || this.cargaDocente.asignaturas.length > 0) return;

      try {
        this.cargandoCargaDocente = true;// Inicializar el estado cargandoCargaDocente
        const res = await API.get(`${this.baseUrl}/docente/carga-academica/${this.idpersona}`);// Llamada a la API para obtener la información académica del docente logueado

        this.cargaDocente.tutorias = res.data.tutorias;// Asignar el valor de res.data.tutorias a la propiedad cargaDocente.tutorias
        this.cargaDocente.asignaturas = res.data.asignaturas;// Asignar el valor de res.data.asignaturas a la propiedad cargaDocente.asignaturas
      } catch (err) {
        //Si hay un error, mostrar un mensaje de error
        console.error("Error al obtener carga docente:", err);//motrar error en consola
        mostraralertas("No se pudo cargar la información académica.", "error");//mostrar un mensaje de error
      } finally {
        //Limpiar el estado cargandoCargaDocente
        this.cargandoCargaDocente = false;
      }
    },
    /**
     * getUsuario: Método para obtener la información del usuario logueado y actualizar la variable Usuario
     * Este método se utiliza para obtener la información del usuario logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idusuario para obtener la información del usuario logueado.
     * Si la información del usuario logueado no está cargada, se inicializa el estado cargandoUsuario y se invoca la función cargarDatosPersonales para cargar la información del usuario logueado. 
     * Si la información del usuario logueado ya está cargada, se muestra la información del usuario logueado en la página de perfil del estudiante.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async getUsuario() {
      try {
        const res = await API.get(`${this.baseUrl}/usuarios/${this.idusuario}`);// Llamada a la API para obtener la información del usuario logueado
        this.Usuario = res.data.data;// Asignar el valor de res.data.data a la propiedad Usuario
      } catch (err) { 
        //Si hay un error, mostrar un mensaje de error
        console.error(err); 
      }
    },
    /**
     * getPhotoUrl: Método para obtener la URL de la imagen del usuario logueado
     * Este método se utiliza para obtener la URL de la imagen del usuario logueado en la página de perfil del estudiante.
     * Si la información de la imagen del usuario logueado no está cargada, se devuelve una URL por defecto de un avatar genérico.
     * Si la información de la imagen del usuario logueado ya está cargada, se devuelve la URL de la imagen del usuario logueado.
     * Se recibe como parámetro ci, que es el código de identificación del usuario logueado.
     * @param ci 
     */
    getPhotoUrl(ci) {
      //Si no existe la imagen del usuario logueado, devuelve una URL por defecto de un avatar genérico
      if (!ci) return "https://ui-avatars.com/api/?name=User&background=1D2A68&color=fff";
      //Retorna la URL de la imagen del usuario logueado, agregando un parámetro de refresco para evitar el cacheo de la imagen
      return `${API.defaults.baseURL}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
    },
    /**
     * onFileSelected: Método para actualizar la imagen del usuario logueado
     * Este método se utiliza para actualizar la imagen del usuario logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad Persona para obtener la información del usuario logueado.
     * Este método abrirá un cuadro de diálogo emergente para seleccionar una imagen del usuario logueado.
     * Una vez que se selecciona una imagen, se cargará la imagen en la página de perfil del estudiante y se actualizará la información del usuario logueado.
     * Este método recibe como parámetro event, que es el evento de selección de archivo.
     * @param event 
     */
    async onFileSelected(event) {
      const file = event.target.files[0];// Obtener el archivo seleccionado
      //Si no existe el archivo seleccionado, se devuelve
      if (!file) return;

      const reader = new FileReader();// Crear un objeto FileReader
      //Crear un objeto FileReader, se utiliza para leer el contenido del archivo seleccionado
      reader.onload = async (e) => {
        const base64String = e.target.result.split(',')[1];// Obtener la cadena base64 del contenido del archivo seleccionado
        try {
          this.cargando = true;// Inicializar el estado cargando
          const params = { ...this.Persona, foto: base64String };// Crear un objeto con las propiedades de Persona y foto
          const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, params);// Llamada a la API para actualizar la información del usuario logueado
          //Si la respuesta es exitosa, mostrar un mensaje de alerta y actualizar la clave de refresco
          if (res.data.mensaje) {
            mostraralertas("Foto actualizada", "success");// Mostrar un mensaje de alerta
            this.refreshKey = Date.now();// Actualizar la clave de refresco
          }
        } catch (e) { 
          //Si hay un error, mostrar un mensaje de error
          mostraralertas("Error al subir foto", "error"); 
        }
        finally { 
          //Limpiar el estado cargando
          this.cargando = false; 
        }
      };
      reader.readAsDataURL(file);// Leer el contenido del archivo seleccionado como una cadena base64
    },
    /**
     * actualizarDatosPersonales: Método para actualizar la información personal del usuario logueado y mostrar un mensaje de alerta
     * Este método se utiliza para actualizar la información personal del usuario logueado en la página de perfil del estudiante.
     * Se utiliza la propiedad idpersona para obtener la información personal del usuario logueado.
     * Se pasan las propiedades almacenadas en la propiedad Persona a la API para actualizar la información personal del usuario logueado.
     * Si la respuesta es exitosa, se muestra un mensaje de alerta y se actualiza la clave de refresco.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async actualizarDatosPersonales() {
      try {
        this.cargando = true;// Inicializar el estado cargando
        const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, this.Persona);// Llamada a la API para actualizar la información personal del usuario logueado
        mostraralertas(res.data.mensaje, "success");// Mostrar un mensaje de alerta
      } catch (e) { 
        //Si hay un error, mostrar un mensaje de error
        mostraralertas("Error al actualizar", "error"); 
      }
      finally { 
        //Limpiar el estado cargando
        this.cargando = false; 
      }
    },
    /**
     * actualizarCredenciales: Método para actualizar la contraseña del usuario logueado y mostrar un mensaje de alerta
     * Este método se utiliza para actualizar la contraseña del usuario logueado en la página de perfil del estudiante.
     * Primero se compara las contraseñas del usuario logueado y se confirman que sean iguales.
     * Si las contraseñas son iguales, se validan que la nueva contraseña sea de al menos 8 caracteres.
     * Se utiliza la propiedad idusuario para decirle a la API que se quiere actualizar la contraseña del usuario logueado.
     * Se pasan las propiedades almacenadas en la propiedad Usuario a la API para actualizar la contraseña del usuario logueado.
     * Si la respuesta es exitosa, se muestra un mensaje de alerta y se actualiza la clave de refresco.
     * Este método no recibe parámetros.
     * Se usa async en este método para evitar que se bloquee el ciclo de vida del componente mientras se realizan las peticiones al backend.
     */
    async actualizarCredenciales() {
      //Si las contraseñas no coinciden, se devuelve un mensaje de alerta de advertencia
      if (this.nuevaClave !== this.confirmarClave) return mostraralertas("Contraseñas no coinciden", "warning");
      //Si la nueva contraseña no es de al menos 8 caracteres, se devuelve un mensaje de alerta de advertencia
      if (this.nuevaClave.length < 8) return mostraralertas("Muy corta", "warning");

      try {
        this.cargando = true;// Inicializar el estado cargando
        const params = { ...this.Usuario, clave: this.nuevaClave };// Crear un objeto con las propiedades de Usuario y clave
        const res = await API.put(`${this.baseUrl}/usuarios/${this.idusuario}`, params);// Llamada a la API para actualizar la contraseña del usuario logueado
        mostraralertas("Contraseña actualizada con éxito", "success");// Mostrar un mensaje de alerta
        this.nuevaClave = ""; this.confirmarClave = "";// Limpiar las variables nuevaClave y confirmarClave
      } catch (e) { 
        //Si hay un error, mostrar un mensaje de error
        mostraralertas("Error al cambiar clave", "error"); 
      }
      finally { 
        //Limpiar el estado cargando
        this.cargando = false; 
      }
    }
  }
}
</script>

<style scoped>
.text-blue {
  color: #1D2A68;
}

.bg-gold-soft {
  background-color: rgba(244, 179, 36, 0.2);
}

/* Diseño del Header */
.profile-header-bg {
  height: 120px;
  background: linear-gradient(90deg, #1D2A68 0%, #151e4b 100%);
}

.profile-content {
  margin-top: -60px;
}

.profile-avatar-container {
  position: relative;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  border: 6px solid #fff;
  background-color: #fff;
}

.profile-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-edit-badge {
  position: absolute;
  bottom: 5px;
  right: 5px;
  width: 35px;
  height: 35px;
  background-color: #F4B324;
  color: #1D2A68;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 3px solid #fff;
  transition: 0.3s;
}

.avatar-edit-badge:hover {
  transform: scale(1.1);
}

/* Estilo Nav Pills Institucional */
.custom-nav-pills .nav-link {
  color: #6c757d;
  font-weight: bold;
  border-radius: 10px;
  padding: 10px 20px;
  margin-right: 10px;
}

.custom-nav-pills .nav-link.active {
  background-color: #1D2A68;
  color: #F4B324;
}

/* Inputs */
.custom-input-profile {
  padding: 10px 15px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
}

.custom-input-profile:focus {
  border-color: #F4B324;
  box-shadow: 0 0 0 0.25rem rgba(244, 179, 36, 0.1);
}

.btn-gold {
  background-color: #F4B324;
  color: #1D2A68;
  border: none;
}

.btn-blue {
  background-color: #1D2A68;
  color: #fff;
  border: none;
}

.bg-blue-soft {
  background-color: rgba(29, 42, 104, 0.1);
}

.icon-box {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.table thead th {
  border-top: none;
  background-color: #f8f9fa;
  padding: 15px;
}

.border-gold {
  border-color: #F4B324 !important;
}

.bg-blue {
  background-color: #1D2A68 !important;
}

.bg-gold-soft {
  background-color: rgba(244, 179, 36, 0.15) !important;
  border: 1px solid rgba(244, 179, 36, 0.3);
}

.table-responsive {
  border: 1px solid #dee2e6;
}

/* Efecto hover para las celdas del horario */
.table td:hover {
  background-color: rgba(29, 42, 104, 0.05);
  transition: 0.3s;
}

custom-table-schedule {
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid #dee2e6;
  background-color: #fff;
}

.custom-table-schedule thead th {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.custom-table-schedule tbody td {
  border: 1px solid #edf0f5;
  transition: all 0.2s;
}

/* Hover suave para las celdas con contenido */
.custom-table-schedule tbody td:hover {
  background-color: #f8faff;
}

/* Badge pequeño para la especialidad */
.specialty-tag {
  font-size: 0.6rem;
  background-color: rgba(29, 42, 104, 0.05);
  color: #1D2A68;
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
}
</style>
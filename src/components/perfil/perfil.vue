<template>
  <div class="container mt-5 pb-5">
    <div class="card shadow-lg border-0 overflow-hidden mb-4 rounded-4">
      <div class="profile-header-bg"></div>
      <div class="card-body pt-0 px-4">
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end profile-content">
          <div class="profile-avatar-container shadow-sm mb-3 mb-md-0">
            <img :src="getPhotoUrl(Persona.id_persona)" class="profile-avatar" alt="Foto de perfil">
            <label for="fileInput" class="avatar-edit-badge" title="Cambiar foto">
              <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="fileInput" @change="onFileSelected" hidden accept="image/*">
          </div>

          <div class="ms-md-4 text-center text-md-start mb-3">
            <h2 class="fw-bold text-blue mb-0">{{ Persona.nombres }} {{ Persona.apellidos }}</h2>
            <p class="text-muted mb-0">
              <span class="badge bg-gold-soft text-blue me-2">ID: {{ Persona.cedula }}</span>
              <span class="small fw-bold text-uppercase"><i class="fas fa-id-badge me-1"></i> Perfil de Usuario</span>
              {{ Usuario.nombre_rol }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-header bg-white border-0 p-0">
        <ul class="nav nav-pills custom-nav-pills px-3 pt-3" id="perfilTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personales" type="button">
              <i class="fas fa-user-edit me-2"></i>Datos Personales
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#usuario" type="button">
              <i class="fas fa-shield-alt me-2"></i>Seguridad de Cuenta
            </button>
          </li>
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol !== 'Estudiante'">
            <button @click="getFamiliares" class="nav-link" data-bs-toggle="tab" data-bs-target="#familias"
              type="button">
              <i class="fas fa-users me-2"></i>Familias
            </button>
          </li>
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Estudiante'">
            <button @click="getEstFamiliares" class="nav-link" data-bs-toggle="tab" data-bs-target="#estufamilias"
              type="button">
              <i class="fas fa-users me-2"></i>Mi(s) Representante
            </button>
          </li>
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <button @click="getCargaDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#mis-tutorias"
              type="button">
              <i class="fas fa-chalkboard-teacher me-2"></i>Mis Tutorías
            </button>
          </li>
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <button @click="getCargaDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#mis-asignaturas"
              type="button">
              <i class="fas fa-book me-2"></i>Asignaturas Asignadas
            </button>
          </li>
          <li class="nav-item" role="presentation" v-if="Usuario.nombre_rol === 'Docente'">
            <button @click="getHorarioDocente" class="nav-link" data-bs-toggle="tab" data-bs-target="#horario-clases"
              type="button">
              <i class="fas fa-calendar-alt me-2"></i>Mi Horario
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-4">
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="personales" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Información Básica</h5>
              <p class="text-muted small">Desde aquí puedes gestionar tu información de contacto. Para cambios en
                nombres o cédula, contacta a secretaría.</p>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <input v-model="Persona.correo" type="email" class="form-control custom-input-profile"
                  placeholder="correo@ejemplo.com">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                <input v-model="Persona.telefono" type="text" class="form-control custom-input-profile"
                  placeholder="09xxxxxxxx">
              </div>
              <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">DIRECCIÓN DE DOMICILIO</label>
                <input v-model="Persona.direccion" type="text" class="form-control custom-input-profile">
              </div>
              <div class="col-12 text-end mt-4">
                <button @click="actualizarDatosPersonales" class="btn btn-gold px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-save me-2"></i> {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="usuario" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Gestión de Acceso</h5>
              <p class="text-muted small">Mantén tu cuenta segura cambiando tu contraseña periódicamente. El nombre de
                usuario no puede ser modificado.</p>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">NOMBRE DE USUARIO</label>
                <input v-model="Usuario.username" type="text" class="form-control bg-light text-muted" readonly>
              </div>
              <div class="col-md-6"></div>

              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">NUEVA CONTRASEÑA</label>
                <input v-model="nuevaClave" type="password" class="form-control custom-input-profile"
                  placeholder="Mínimo 8 caracteres">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">CONFIRMAR CONTRASEÑA</label>
                <input v-model="confirmarClave" type="password" class="form-control custom-input-profile">
              </div>

              <div class="col-12 mt-4">
                <div class="alert alert-info border-0 shadow-sm rounded-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                      <h6 class="mb-0 fw-bold">Recomendación</h6>
                      <small>Tu contraseña debe ser difícil de adivinar y no debes compartirla con nadie.</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 text-end">
                <button @click="actualizarCredenciales" class="btn btn-blue px-4 fw-bold shadow-sm"
                  :disabled="cargando">
                  <i class="fas fa-key me-2"></i> Cambiar Contraseña
                </button>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="familias" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Núcleo Familiar Registrado</h5>
              <p class="text-muted small">A continuación se listan los familiares vinculados a su cuenta en el sistema
                académico.</p>
            </div>

            <div v-if="cargandoFamilia" class="text-center py-5">
              <div class="spinner-border text-gold" role="status"></div>
              <p class="mt-2 text-muted">Cargando familiares...</p>
            </div>

            <div v-else-if="familiares.length === 0" class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
              <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-3x me-3 text-warning"></i>
                <div>
                  <h6 class="fw-bold mb-1">Usted no posee familia asignada</h6>
                  <p class="mb-0 small">Debe dirigirse a la institución para registrar a su grupo familiar y completar
                    su expediente.</p>
                </div>
              </div>
            </div>

            <div v-else class="row g-3">
              <div v-for="familiar in familiares" :key="familiar.id_persona" class="col-md-6">
                <div class="card border shadow-sm rounded-4 h-100 hvr-light">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <img :src="familiar.foto ? 'data:image/jpeg;base64,' + familiar.foto : getPhotoUrl(null)"
                        class="rounded-circle border border-2 border-gold shadow-sm"
                        style="width: 70px; height: 70px; object-fit: cover;">
                      <div class="ms-3">
                        <h6 class="mb-0 fw-bold text-blue">{{ familiar.nombres }} {{ familiar.apellidos }}</h6>
                        <span class="badge bg-gold text-blue small mb-1">{{ familiar.parentesco }}</span>
                        <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i> {{ familiar.cedula }}</p>
                        <p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i>
                          {{ familiar.telefono || 'Sinteléfono' }}</p>
                      </div>
                      <div class="mt-3 border-top pt-3 text-end">
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
          <div class="tab-pane fade" id="estufamilias" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Representante(s) registrado(s)</h5>
              <p class="text-muted small">A continuación se muestra la información de los representantes registrados en
                el sistema.</p>
            </div>
            <div v-if="cargandoEstFamiliares" class="text-center py-5">
              <div class="spinner-border text-gold" role="status"></div>
              <p class="mt-2 text-muted">Cargando representantes...</p>
            </div>
            <div v-else-if="estfamiliares.length === 0" class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
              <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-3x me-3 text-warning"></i>
                <div>
                  <h6 class="fw-bold mb-1">No posee representante registrado</h6>
                  <p class="mb-0 small">Su representante debe registrarse en el sistema para poder realizar el
                    seguimiento de su estado de estudiante.</p>
                </div>
              </div>
            </div>
            <div v-else class="row g-3">
              <div v-for="familiar in estfamiliares" :key="familiar.id_persona" class="col-md-6">
                <div class="card border shadow-sm rounded-4 h-100 hvr-light">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <img :src="familiar.foto ? 'data:image/jpeg;base64,' + familiar.foto : getPhotoUrl(null)"
                        class="rounded-circle border border-2 border-gold shadow-sm"
                        style="width: 70px; height: 70px; object-fit: cover;">
                      <div class="ms-3">
                        <h6 class="mb-0 fw-bold text-blue">{{ familiar.nombres }} {{ familiar.apellidos }}</h6>
                        <span class="badge bg-gold text-blue small mb-1">{{ familiar.parentesco }}</span>
                        <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i> {{ familiar.cedula }}</p>
                        <p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i>
                          {{ familiar.telefono || 'Sinteléfono' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="mis-tutorias" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Cursos bajo mi Tutoría</h5>
              <p class="text-muted small">Como docente tutor, usted es responsable del seguimiento integral de estos
                paralelos.</p>
            </div>

            <div v-if="cargaDocente.tutorias.length === 0" class="alert alert-light border shadow-sm rounded-4">
              <i class="fas fa-info-circle me-2"></i> Usted no tiene cursos asignados como tutor en este periodo.
            </div>

            <div class="row g-3">
              <div v-for="curso in cargaDocente.tutorias" :key="curso.id_curso" class="col-md-6">
                <div class="card border-start border-gold border-4 shadow-sm rounded-3">
                  <div class="card-body">
                    <h6 class="fw-bold text-blue mb-1">
                      {{ curso.nivel.nombre }} "{{ curso.paralelo }}"
                    </h6>
                    <p class="mb-0 small text-muted text-uppercase fw-bold">
                      {{ curso.especialidad.nombre }}
                    </p>
                    <div class="mt-2">
                      <span class="badge bg-gold-soft text-blue">Periodo: {{ curso.periodo.nombre }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="mis-asignaturas" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Asignaturas que Imparto</h5>
              <p class="text-muted small">Listado de materias asignadas y los cursos correspondientes.</p>
            </div>

            <div v-if="cargaDocente.asignaturas.length === 0" class="alert alert-light border shadow-sm rounded-4">
              <i class="fas fa-info-circle me-2"></i> No se encontraron asignaturas asignadas a su perfil.
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle border rounded-3 overflow-hidden">
                <thead class="table-light text-blue">
                  <tr>
                    <th class="small fw-bold">ASIGNATURA</th>
                    <th class="small fw-bold">CURSO / PARALELO</th>
                    <th class="small fw-bold text-center">H. SEMANALES</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in cargaDocente.asignaturas" :key="item.id_curso_asignatura">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="icon-box bg-blue-soft text-blue me-2">
                          <i class="fas fa-book-open"></i>
                        </div>
                        <span class="fw-bold">{{ item.asignatura.nombre }}</span>
                      </div>
                    </td>
                    <td>
                      <span class="text-muted">{{ item.curso.nivel.nombre }} "{{ item.curso.paralelo }}"</span>
                      <br>
                      <small class="text-gold fw-bold">{{ item.curso.especialidad.nombre }}</small>
                    </td>
                    <td class="text-center">
                      <span class="badge rounded-pill bg-light text-dark border">{{ item.horas_semanales }} horas</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="horario-clases" role="tabpanel">
            <div class="info-section mb-4">
              <h5 class="text-blue fw-bold border-bottom pb-2">Horario Semanal de Clases</h5>
              <p class="text-muted small">Visualice su planificación semanal. Los horarios están sujetos a cambios por
                parte de coordinación académica.</p>
            </div>

            <div v-if="cargandoHorario" class="text-center py-5">
              <div class="spinner-border text-gold" role="status"></div>
              <p class="mt-2 text-muted">Generando cronograma...</p>
            </div>

            <div v-else-if="horario.length === 0" class="alert alert-light border shadow-sm rounded-4 text-center">
              <i class="fas fa-calendar-times fa-2x mb-2 text-muted"></i>
              <p class="mb-0">No se han registrado horas de clase para su usuario todavía.</p>
            </div>

            <div v-else class="table-responsive shadow-sm rounded-4">
              <table class="table table-bordered align-middle mb-0 text-center custom-table-schedule">
                <thead class="bg-blue text-white">
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
                  <tr v-for="(fila, index) in horario" :key="index">
                    <td class="fw-bold text-blue bg-light" style="width: 15%;">
                      {{ fila.rango }}
                    </td>

                    <td v-for="dia in ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']" :key="dia"
                      class="p-0 position-relative" style="width: 17%; height: 80px;">

                      <div v-if="fila[dia]" class="p-2 h-100 d-flex flex-column justify-content-center">
                        <div class="fw-bold text-blue mb-1" style="font-size: 0.85rem; line-height: 1.2;">
                          {{ fila[dia].asignatura }}
                        </div>
                        <div class="text-muted mb-1" style="font-size: 0.75rem;">
                          <i class="fas fa-chalkboard text-gold me-1"></i> {{ fila[dia].curso }}
                        </div>
                        <div class="text-uppercase fw-bold text-blue" style="font-size: 0.65rem; opacity: 0.8;">
                          {{ fila[dia].especialidad }}
                        </div>
                      </div>

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
  <div class="modal fade" id="modalCalificaciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header bg-blue text-white rounded-top-4">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-user-graduate me-2"></i> Reporte de Calificaciones
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4" style="background-color: #f8f9fa;">
          <div v-if="cargandoCalificaciones" class="text-center py-5">
            <div class="spinner-border text-gold" role="status"></div>
            <p class="mt-2 text-muted">Consultando registro académico...</p>
          </div>

          <div v-else-if="datosAcademicos">
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-gold border-5">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h5 class="fw-bold text-blue mb-1">
                      {{ datosAcademicos.curso.nivel }} "{{ datosAcademicos.curso.paralelo }}"
                    </h5>
                    <p class="text-muted mb-0 small">
                      <span v-if="datosAcademicos.curso.especialidad">{{ datosAcademicos.curso.especialidad }} |</span>
                      Periodo Lectivo: <span class="fw-bold">{{ datosAcademicos.curso.periodo }}</span>
                    </p>
                  </div>
                  <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-gold text-blue fs-6 px-3 py-2">
                      {{ estudianteSeleccionado?.nombres }} {{ estudianteSeleccionado?.apellidos }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4" role="alert">
              <h6 class="fw-bold mb-2"><i class="fas fa-calculator me-2"></i> ¿Cómo se calculan las notas?</h6>
              <ul class="mb-0 small">
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

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
              <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 text-center text-nowrap">

                  <thead class="bg-blue text-white">
                    <tr>
                      <th rowspan="2" class="text-start ps-4 align-middle">Asignatura</th>
                      <th colspan="4" class="text-center">Quimestre 1</th>
                      <th v-if="mostrarColumnas.q2" colspan="4" class="text-center border-start border-light">Quimestre
                        2</th>
                      <th v-if="mostrarColumnas.q2" rowspan="2"
                        class="align-middle bg-secondary bg-opacity-25 border-start text-white border-light">Prom. Anual
                      </th>
                      <th v-if="mostrarColumnas.supletorio" rowspan="2" class="align-middle bg-warning text-dark">
                        Supletorio</th>
                      <th v-if="mostrarColumnas.remedial" rowspan="2" class="align-middle bg-info text-dark">Remedial
                      </th>
                      <th v-if="mostrarColumnas.gracia" rowspan="2" class="align-middle bg-primary text-white">Gracia
                      </th>
                      <th v-if="mostrarColumnas.q2" rowspan="2" class="align-middle bg-gold text-blue">Nota Final</th>
                      <th v-if="mostrarColumnas.q2" rowspan="2" class="align-middle">Estado</th>
                    </tr>
                    <tr class="bg-blue-light text-white" style="background-color: #2a3d8f;">
                      <th class="small fw-normal">P1</th>
                      <th class="small fw-normal">P2</th>
                      <th class="small fw-normal">P3</th>
                      <th class="small fw-bold">Prom</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-normal border-start border-light">P1</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-normal">P2</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-normal">P3</th>
                      <th v-if="mostrarColumnas.q2" class="small fw-bold">Prom</th>
                    </tr>
                  </thead>

                  <tbody>
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

          <div v-else class="alert alert-warning border-0 shadow-sm rounded-4">
            No se encontró información académica para este estudiante en el periodo actual.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import API from "@/assets/js/axios"
import { mostraralertas } from "@/assets/js/funciones/functions";
import { getMe } from "@/assets/js/auth";
import * as bootstrap from 'bootstrap';

export default {
  data() {
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
  computed: {
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
  async mounted() {
    this.cargando = true;
    const me = await getMe();
    this.idpersona = me.id_persona;
    this.idusuario = me.id_usuario;
    await Promise.all([this.getPersona(), this.getUsuario()]);
    this.cargando = false;
  },
  methods: {
    async verCalificaciones(familiar) {
      this.estudianteSeleccionado = familiar;
      this.datosAcademicos = null;
      this.cargandoCalificaciones = true;

      // Inicializar y mostrar modal de Bootstrap
      if (!this.modalCalificaciones) {
        this.modalCalificaciones = new bootstrap.Modal(document.getElementById('modalCalificaciones'));
      }
      this.modalCalificaciones.show();

      try {
        // Ajusta la URL según cómo la hayas registrado en Laravel (api.php)
        const res = await API.get(`${this.baseUrl}/calificaciones-actuales/${familiar.id_persona}`);
        this.datosAcademicos = res.data;
      } catch (e) {
        console.error(e);
        if (e.response && e.response.status === 404) {
          // Ignoramos el toast si es 404 porque ya mostramos el mensaje en el modal
        } else {
          mostraralertas("Error al conectar con el servidor", "error");
        }
      } finally {
        this.cargandoCalificaciones = false;
      }
    },

    badgeEstado(estado) {
      if (!estado) return 'bg-secondary';
      switch (estado.toLowerCase()) {
        case 'aprobado': return 'bg-success';
        case 'supletorio': return 'bg-warning text-dark';
        case 'remedial': return 'bg-info text-dark';
        case 'gracia': return 'bg-primary';
        case 'reprobado': return 'bg-danger';
        default: return 'bg-secondary';
      }
    },
    async getPersona() {
      try {
        const res = await API.get(`${this.baseUrl}/personas/${this.idpersona}`);
        // Como el show retorna paginación en tu Backend, tomamos el primer item
        this.Persona = res.data.data[0] || res.data.data;
      } catch (err) { console.error(err); }
    },
    async getFamiliares() {
      if (this.familiares.length > 0) return; // Evita recargar si ya hay datos

      try {
        this.cargandoFamilia = true;
        const res = await API.get(`${this.baseUrl}/familiares-de/${this.idpersona}`);
        this.familiares = res.data.data;
      } catch (e) {
        console.error("Error al traer familiares:", e);
        mostraralertas("No se pudo obtener la información de familia", "error");
      } finally {
        this.cargandoFamilia = false;
      }
    },
    async getEstFamiliares() {
      if (this.estfamiliares.length > 0) return; // Evita recargar si ya hay datos

      try {
        this.cargandoEstFamiliares = true;
        const res = await API.get(`${this.baseUrl}/familiares-est-de/${this.idpersona}`);
        this.estfamiliares = res.data.data;
      } catch (e) {
        console.error("Error al traer familiares:", e);
        mostraralertas("No se pudo obtener la información de familia", "error");
      } finally {
        this.cargandoEstFamiliares = false;
      }
    },
    async getHorarioDocente() {
      try {
        this.cargandoHorario = true;
        const res = await API.get(`${this.baseUrl}/horarios_docente/${this.idpersona}`);
        const datosBrutos = res.data;

        // Agrupamos por rango de hora para crear filas únicas
        const grupos = {};

        datosBrutos.forEach(item => {
          const rango = `${item.inicio} - ${item.fin}`;
          if (!grupos[rango]) {
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
        mostraralertas("Error al cargar horario", "error");
      } finally {
        this.cargandoHorario = false;
      }
    },
    async getCargaDocente() {
      // Evitamos peticiones repetidas si ya cargó
      if (this.cargaDocente.tutorias.length > 0 || this.cargaDocente.asignaturas.length > 0) return;

      try {
        this.cargandoCargaDocente = true;
        const res = await API.get(`${this.baseUrl}/docente/carga-academica/${this.idpersona}`);

        this.cargaDocente.tutorias = res.data.tutorias;
        this.cargaDocente.asignaturas = res.data.asignaturas;
      } catch (err) {
        console.error("Error al obtener carga docente:", err);
        mostraralertas("No se pudo cargar la información académica.", "error");
      } finally {
        this.cargandoCargaDocente = false;
      }
    },
    async getUsuario() {
      try {
        const res = await API.get(`${this.baseUrl}/usuarios/${this.idusuario}`);
        this.Usuario = res.data.data;
      } catch (err) { console.error(err); }
    },
    getPhotoUrl(ci) {
      if (!ci) return "https://ui-avatars.com/api/?name=User&background=1D2A68&color=fff";
      // Tu endpoint de imagen
      return `${API.defaults.baseURL}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
    },
    async onFileSelected(event) {
      const file = event.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = async (e) => {
        const base64String = e.target.result.split(',')[1];
        try {
          this.cargando = true;
          const params = { ...this.Persona, foto: base64String };
          const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, params);
          if (res.data.mensaje) {
            mostraralertas("Foto actualizada", "success");
            this.refreshKey = Date.now();
          }
        } catch (e) { mostraralertas("Error al subir foto", "error"); }
        finally { this.cargando = false; }
      };
      reader.readAsDataURL(file);
    },
    async actualizarDatosPersonales() {
      try {
        this.cargando = true;
        const res = await API.put(`${this.baseUrl}/personas/${this.idpersona}`, this.Persona);
        mostraralertas(res.data.mensaje, "success");
      } catch (e) { mostraralertas("Error al actualizar", "error"); }
      finally { this.cargando = false; }
    },
    async actualizarCredenciales() {
      if (this.nuevaClave !== this.confirmarClave) return mostraralertas("Contraseñas no coinciden", "warning");
      if (this.nuevaClave.length < 8) return mostraralertas("Muy corta", "warning");

      try {
        this.cargando = true;
        const params = { ...this.Usuario, clave: this.nuevaClave };
        const res = await API.put(`${this.baseUrl}/usuarios/${this.idusuario}`, params);
        mostraralertas("Contraseña actualizada con éxito", "success");
        this.nuevaClave = ""; this.confirmarClave = "";
      } catch (e) { mostraralertas("Error al cambiar clave", "error"); }
      finally { this.cargando = false; }
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
<template>
    <div class="container-fluid py-4">
        <header
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm mb-4 custom-header"
            style="border-left: 6px solid #F4B324;">

            <div class="mb-3 mb-md-0 d-flex align-items-center">
                <div class="header-icon shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3"
                    style="background-color: #1D2A68; color: #F4B324; width: 55px; height: 55px;">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: #1D2A68; font-family: 'Fraunces', serif;">
                        Gestión de Personas
                    </h2>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">
                        Administración de personas registradas en el sistema.
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="stat-badge d-flex align-items-center px-3 py-2 rounded-pill border"
                    style="background-color: rgba(29, 42, 104, 0.05); border-color: rgba(29, 42, 104, 0.2) !important; color: #1D2A68;">
                    <i class="fas fa-users me-2" style="color: #F4B324;"></i>
                    <span class="fw-medium">
                        Total: <span v-if="totalPersonas > 0">{{ totalPersonas }}</span><span v-else>0</span>
                    </span>
                </div>

                <button
                    class="btn btn-lg shadow-sm rounded-pill d-flex align-items-center interactive-btn px-4 border-0"
                    style="background-color: #1D2A68; color: white;" data-bs-toggle="modal"
                    data-bs-target="#modalUsuario" @click="limpiar">
                    <i class="fas fa-plus-circle me-2" style="color: #F4B324;"></i>
                    <span class="fw-bold fs-6">Nuevo Registro</span>
                </button>
            </div>
        </header>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" v-model="busqueda" @input="busqueda = busqueda.replace(/[^0-9]/g, '')"
                        class="form-control border-0 shadow-none"
                        placeholder="Buscar por cédula de la persona... (Solo números)">
                </div>
                <div class="form-text text-muted ms-2 mt-2">
                    <i class="fas fa-info-circle me-1"></i> Escribe la cédula de una persona para buscar en la base de
                    datos.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #1D2A68 !important;">
                        <tr>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Id</th>
                            <th class="ps-4 py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Persona</th>
                            <th class="py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Teléfono</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Sexo</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Estado</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Creación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Modificación</th>
                            <th class="text-center py-3"
                                style="background-color: #1D2A68 !important; color: white !important; border-bottom: none;">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in objetoList" :key="user.id_persona">
                            <td class="ps-4 fw-bold text-secondary">{{ user.id_persona }}</td>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm"
                                        style="width: 45px; height: 45px; flex-shrink: 0; border: 2px solid #F4B324;">
                                        <img :src="getPhotoUrl(user.id_persona)" @error="handleImageError" alt="Foto"
                                            class="w-100 h-100" style="object-fit: cover;" />
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold mb-1">
                                            <i class="far fa-id-card me-1" style="color: #1D2A68;"></i>{{ user.cedula }}
                                        </div>
                                        <div class="fw-bold text-dark" v-if="user.id_persona === idpersonalog">Yo</div>
                                        <div class="fw-bold text-dark" v-else>{{ user.nombres }} {{ user.apellidos }}</div>
                                        <div class="text-muted small" v-if="user.fecha_nacimiento">
                                            Edad: {{ calcularEdad(user.fecha_nacimiento) }} años
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ user.telefono }}</td>
                            <td class="text-center">
                                <span v-if="user.sexo === 'M' || user.sexo === 'Masculino'" title="Masculino">
                                    <i class="fas fa-mars fs-4" style="color: #1D2A68;"></i>
                                </span>
                                <span v-else-if="user.sexo === 'F' || user.sexo === 'Femenino'" title="Femenino">
                                    <i class="fas fa-venus fs-4" style="color: #F4B324;"></i>
                                </span>
                                <span v-else title="Otro">
                                    <i class="fas fa-genderless fs-4 text-secondary"></i> {{ user.sexo }}
                                </span>
                            </td>

                            <td class="text-center" v-if="user.estado == 1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success px-3">Activo</span>
                            </td>
                            <td class="text-center" v-else>
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger px-3">Inactivo</span>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-calendar-plus me-1" style="color: #1D2A68;"></i> {{ user.created_at
                                    }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border fw-normal px-2 py-1">
                                    <i class="far fa-edit me-1" style="color: #F4B324;"></i> {{ user.updated_at }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                                        data-bs-target="#modalDetalle" @click="cargarDetalles(user)"
                                        title="Ver información completa" style="color: #1D2A68;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" v-if="user.id_persona !== idpersonalog"
                                        data-bs-target="#modalEditUsuario" @click="cargarDatosEdicion(user)"
                                        title="Editar detalles de esta persona" style="color: #F4B324;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger"
                                        @click="eliminar(user.id_persona, user.nombres + ' ' + user.apellidos)"
                                        v-if="user.estado == 1 && user.id_persona !== idpersonalog" title="Inhabilitar esta persona">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-success"
                                        @click="habilitar(user.id_persona, user.nombres + ' ' + user.apellidos)" v-if="user.id_persona !== idpersonalog && user.estado == 0"
                                        title="Habilitar esta persona nuevamente">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="objetoList.length === 0 && !cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 mb-3 d-block" style="color: #F4B324;"></i>
                                No se encontraron personas. ¡Haz clic en "Nuevo Registro" para empezar!
                            </td>
                        </tr>

                        <tr v-if="cargando">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fs-2 mb-2 d-block" style="color: #1D2A68;"></i>
                                Cargando información...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3"
                v-if="lastPage > 1">
                <span class="text-muted small">
                    Página <strong style="color: #1D2A68;">{{ currentPage }}</strong> de <strong
                        style="color: #1D2A68;">{{ lastPage }}</strong>
                </span>

                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                            <button class="page-link shadow-none" style="color: #1D2A68; border-color: #dee2e6;"
                                @click="cambiarPagina(currentPage - 1)" :disabled="currentPage <= 1">
                                Anterior
                            </button>
                        </li>

                        <li class="page-item" v-for="page in paginasMostradas" :key="page"
                            :class="{ active: page === currentPage }">
                            <button class="page-link shadow-none" :style="page === currentPage
                                ? 'background-color: #F4B324 !important; border-color: #F4B324 !important; color: #1D2A68 !important; font-weight: bold;'
                                : 'color: #1D2A68; border-color: #dee2e6;'" @click="cambiarPagina(page)">
                                {{ page }}
                            </button>
                        </li>

                        <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
                            <button class="page-link shadow-none" style="color: #1D2A68; border-color: #dee2e6;"
                                @click="cambiarPagina(currentPage + 1)" :disabled="currentPage >= lastPage">
                                Siguiente
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-user-plus me-2" style="color: #F4B324;"></i>Registrar Nueva Persona
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalCrear"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(29, 42, 104, 0.05); border-left: 4px solid #F4B324 !important;"
                            role="alert">
                            <i class="fas fa-lightbulb fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small text-dark">
                                <strong style="color: #1D2A68;">Guía de Registro:</strong><br>
                                Completa los datos personales. Asegúrate de ingresar números válidos para cédula y
                                teléfono. Puedes agregar una foto de perfil seleccionándola desde tu dispositivo.
                            </div>
                        </div>

                        <form @submit.prevent="guardarData">
                            <div class="row">
                                <div class="col-md-3 text-center border-end mb-3">
                                    <h6 class="text-muted mb-3">Foto de Perfil</h6>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <div class="rounded-circle shadow-sm border overflow-hidden"
                                            style="width: 150px; height: 150px; background-color: #f8f9fa; border-color: rgba(29, 42, 104, 0.2) !important;">
                                            <img v-if="objetoData.previewFoto" :src="objetoData.previewFoto"
                                                class="w-100 h-100" style="object-fit: cover;" alt="Vista previa">
                                            <i v-else
                                                class="fas fa-user d-flex align-items-center justify-content-center h-100"
                                                style="font-size: 5rem; color: #1D2A68; opacity: 0.2;"></i>
                                        </div>
                                    </div>
                                    <input type="file" class="d-none" id="fotoCrear" accept="image/*"
                                        @change="handleFileUpload($event, 'crear')">
                                    <label for="fotoCrear" class="btn btn-sm w-100 rounded-pill shadow-sm fw-medium"
                                        style="border: 1px solid #1D2A68; color: #1D2A68; background-color: transparent;">
                                        <i class="fas fa-camera me-1" style="color: #F4B324;"></i> Seleccionar Foto
                                    </label>
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.cedula" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.cedula }" id="crearCedula"
                                                    placeholder="Cédula"
                                                    @input="objetoData.cedula = objetoData.cedula.replace(/[^0-9]/g, '')"
                                                    maxlength="10">
                                                <label for="crearCedula">Cédula</label>
                                                <div class="invalid-feedback">Ingrese una cédula válida.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.nombres" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.nombres }" id="crearNombres"
                                                    placeholder="Nombres">
                                                <label for="crearNombres">Nombres</label>
                                                <div class="invalid-feedback">Ingrese los nombres.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.apellidos" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.apellidos }" id="crearApellidos"
                                                    placeholder="Apellidos">
                                                <label for="crearApellidos">Apellidos</label>
                                                <div class="invalid-feedback">Ingrese los apellidos.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.fecha_nacimiento" type="date"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': errorsData.fecha_nacimiento }"
                                                    id="crearFecha">
                                                <label for="crearFecha">Fecha de Nacimiento</label>
                                                <div class="invalid-feedback">Seleccione una fecha.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.telefono" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.telefono }" id="crearTelefono"
                                                    placeholder="Teléfono"
                                                    @input="objetoData.telefono = objetoData.telefono.replace(/[^0-9]/g, '')"
                                                    maxlength="10">
                                                <label for="crearTelefono">Teléfono</label>
                                                <div class="invalid-feedback">Ingrese un teléfono válido.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select v-model="objetoData.sexo" class="form-select"
                                                    :class="{ 'is-invalid': errorsData.sexo }" id="crearSexo">
                                                    <option value="" disabled selected>Seleccione</option>
                                                    <option value="Masculino">Masculino</option>
                                                    <option value="Femenino">Femenino</option>
                                                </select>
                                                <label for="crearSexo">Sexo</label>
                                                <div class="invalid-feedback">Seleccione un sexo.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.correo" type="email" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.correo }" id="crearCorreo"
                                                    placeholder="Correo Electrónico">
                                                <label for="crearCorreo">Correo Electrónico</label>
                                                <div class="invalid-feedback">Ingrese un correo válido.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoData.direccion" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsData.direccion }" id="crearDireccion"
                                                    placeholder="Dirección">
                                                <label for="crearDireccion">Dirección</label>
                                                <div class="invalid-feedback">Ingrese una dirección.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" v-if="objetoData.crear_usuario">
                                            <label class="form-label fw-bold text-muted small">Rol del Sistema</label>
                                            <select class="form-select" v-model="objetoData.id_rol">
                                                <option value="" disabled>Seleccione un rol...</option>
                                                <option v-for="rol in rolesDisponibles" :key="rol.id_rol"
                                                    :value="rol.id_rol">
                                                    {{ rol.nombre }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div
                                    class="form-check form-switch p-3 border rounded-3 bg-light d-flex align-items-center shadow-sm">
                                    <input class="form-check-input fs-3 ms-0 me-3 mt-0 cursor-pointer" type="checkbox"
                                        role="switch" id="crearUsuarioSwitch" v-model="objetoData.crear_usuario"
                                        @change="verificarCreacionUsuario" style="cursor: pointer;">
                                    <label class="form-check-label flex-grow-1" for="crearUsuarioSwitch"
                                        style="cursor: pointer;">
                                        <span class="fw-bold text-dark d-block mb-1">
                                            <i class="fas fa-user-shield me-2" style="color: #F4B324;"></i>¿Deseas crear
                                            de una
                                            vez el usuario para esta persona?
                                        </span>
                                        <span class="text-muted small mb-0 d-block">Recuerda que al seleccionar esta
                                            opción, el <strong style="color: #1D2A68;">nombre de usuario</strong> y la
                                            <strong style="color: #1D2A68;">clave por
                                                defecto</strong> serán el número de cédula.
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <hr class="my-4">
                            <button type="submit" class="btn w-100 py-2 shadow-sm rounded-3 fw-bold border-0"
                                style="background-color: #1D2A68; color: white;">
                                <i class="fas fa-save me-2" style="color: #F4B324;"></i>Guardar Persona
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditUsuario" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-user-edit me-2" style="color: #F4B324;"></i>Editar Persona
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseModalEditar"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert border-0 d-flex align-items-center p-3 mb-4 rounded-3"
                            style="background-color: rgba(29, 42, 104, 0.05); border-left: 4px solid #F4B324 !important;"
                            role="alert">
                            <i class="fas fa-info-circle fs-4 me-3" style="color: #F4B324;"></i>
                            <div class="small text-dark">
                                <strong style="color: #1D2A68;">Actualización de datos:</strong><br>
                                Modifica la información personal. Puedes cambiar la foto haciendo clic en el botón
                                debajo de la vista previa.<br>
                                <span class="text-muted">Nota: Para editar la cédula debes ser
                                    superadministrador.</span>
                            </div>
                        </div>

                        <form @submit.prevent="editarData">
                            <div class="row">
                                <div class="col-md-3 text-center border-end mb-3">
                                    <h6 class="text-muted mb-3">Foto de Perfil</h6>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <div class="rounded-circle shadow-sm border overflow-hidden"
                                            style="width: 150px; height: 150px; background-color: #f8f9fa; border-color: rgba(29, 42, 104, 0.2) !important;">
                                            <img v-if="objetoEdit.previewFoto" :src="objetoEdit.previewFoto"
                                                class="w-100 h-100" style="object-fit: cover;" @error="handleImageError"
                                                alt="Vista previa">
                                            <i v-else
                                                class="fas fa-user d-flex align-items-center justify-content-center h-100"
                                                style="font-size: 5rem; color: #1D2A68; opacity: 0.2;"></i>
                                        </div>
                                    </div>
                                    <input type="file" class="d-none" id="fotoEditar" accept="image/*"
                                        @change="handleFileUpload($event, 'editar')">
                                    <label for="fotoEditar" class="btn btn-sm w-100 rounded-pill shadow-sm fw-medium"
                                        style="border: 1px solid #1D2A68; color: #1D2A68; background-color: transparent;">
                                        <i class="fas fa-camera me-1" style="color: #F4B324;"></i> Cambiar Foto
                                    </label>
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.cedula" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.cedula }" id="editCedula"
                                                    placeholder="Cédula"
                                                    @input="objetoEdit.cedula = objetoEdit.cedula.replace(/[^0-9]/g, '')"
                                                    maxlength="10" disabled>
                                                <label for="editCedula">Cédula</label>
                                                <div class="invalid-feedback">Ingrese una cédula válida.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.nombres" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.nombres }" id="editNombres"
                                                    placeholder="Nombres">
                                                <label for="editNombres">Nombres</label>
                                                <div class="invalid-feedback">Ingrese los nombres.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.apellidos" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.apellidos }" id="editApellidos"
                                                    placeholder="Apellidos">
                                                <label for="editApellidos">Apellidos</label>
                                                <div class="invalid-feedback">Ingrese los apellidos.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.fecha_nacimiento" type="date"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.fecha_nacimiento }"
                                                    id="editFecha">
                                                <label for="editFecha">Fecha de Nacimiento</label>
                                                <div class="invalid-feedback">Seleccione una fecha.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.telefono" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.telefono }" id="editTelefono"
                                                    placeholder="Teléfono"
                                                    @input="objetoEdit.telefono = objetoEdit.telefono.replace(/[^0-9]/g, '')"
                                                    maxlength="10">
                                                <label for="editTelefono">Teléfono</label>
                                                <div class="invalid-feedback">Ingrese un teléfono válido.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select v-model="objetoEdit.sexo" class="form-select"
                                                    :class="{ 'is-invalid': errorsEdit.sexo }" id="editSexo">
                                                    <option value="" disabled selected>Seleccione</option>
                                                    <option value="Masculino">Masculino</option>
                                                    <option value="Femenino">Femenino</option>
                                                </select>
                                                <label for="editSexo">Sexo</label>
                                                <div class="invalid-feedback">Seleccione un sexo.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.correo" type="email" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.correo }" id="editCorreo"
                                                    placeholder="Correo Electrónico">
                                                <label for="editCorreo">Correo Electrónico</label>
                                                <div class="invalid-feedback">Ingrese un correo válido.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="objetoEdit.direccion" type="text" class="form-control"
                                                    :class="{ 'is-invalid': errorsEdit.direccion }" id="editDireccion"
                                                    placeholder="Dirección">
                                                <label for="editDireccion">Dirección</label>
                                                <div class="invalid-feedback">Ingrese una dirección.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <select v-model="objetoEdit.estado" class="form-select"
                                                    :class="{ 'is-invalid': errorsEdit.estado }" id="editEstado"
                                                    style="border-color: rgba(29, 42, 104, 0.3);">
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                                <label for="editEstado">Estado en el Sistema</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <button type="submit" class="btn w-100 py-2 shadow-sm rounded-3 fw-bold border-0"
                                style="background-color: #1D2A68; color: white;">
                                <i class="fas fa-sync-alt me-2" style="color: #F4B324;"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold" style="color: #1D2A68;">
                            <i class="fas fa-id-badge me-2" style="color: #F4B324;"></i>Perfil del Usuario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-4">
                            <div class="rounded-circle shadow-sm border mx-auto overflow-hidden"
                                style="width: 160px; height: 160px; border-color: rgba(29, 42, 104, 0.2) !important;">
                                <img :src="personaSeleccionada.previewFoto" @error="handleImageError" alt="Foto Persona"
                                    class="w-100 h-100" style="object-fit: cover;">
                            </div>
                        </div>
                        <h4 class="fw-bold mb-1" style="color: #1D2A68;">
                            {{ personaSeleccionada.nombres }} {{ personaSeleccionada.apellidos }}
                        </h4>
                        <p class="text-muted mb-4">
                            <i class="far fa-id-card me-1" style="color: #F4B324;"></i> {{ personaSeleccionada.cedula }}
                        </p>

                        <div class="row text-start g-3">
                            <div class="col-6">
                                <small class="fw-semibold d-block" style="color: rgba(29, 42, 104, 0.7);">Fecha
                                    Nacimiento</small>
                                <span class="fw-medium">
                                    <i class="far fa-calendar-alt me-1" style="color: #F4B324;"></i> {{
                                    personaSeleccionada.fecha_nacimiento }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="fw-semibold d-block" style="color: rgba(29, 42, 104, 0.7);">Sexo</small>
                                <span class="fw-medium">
                                    <i v-if="personaSeleccionada.sexo === 'M' || personaSeleccionada.sexo === 'Masculino'"
                                        class="fas fa-mars me-1" style="color: #1D2A68;"></i>
                                    <i v-else-if="personaSeleccionada.sexo === 'F' || personaSeleccionada.sexo === 'Femenino'"
                                        class="fas fa-venus me-1" style="color: #1D2A68;"></i>
                                    <i v-else class="fas fa-genderless me-1" style="color: #1D2A68;"></i>
                                    {{ personaSeleccionada.sexo }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="fw-semibold d-block"
                                    style="color: rgba(29, 42, 104, 0.7);">Teléfono</small>
                                <span class="fw-medium">
                                    <i class="fas fa-phone-alt me-1" style="color: #F4B324;"></i> {{
                                    personaSeleccionada.telefono }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="fw-semibold d-block" style="color: rgba(29, 42, 104, 0.7);">Estado</small>
                                <span v-if="personaSeleccionada.estado == 1" class="badge bg-success">Activo</span>
                                <span v-else class="badge bg-danger">Inactivo</span>
                            </div>
                            <div class="col-12">
                                <small class="fw-semibold d-block" style="color: rgba(29, 42, 104, 0.7);">Correo
                                    Electrónico</small>
                                <span class="fw-medium">
                                    <i class="far fa-envelope me-1" style="color: #F4B324;"></i> {{
                                    personaSeleccionada.correo }}
                                </span>
                            </div>
                            <div class="col-12">
                                <small class="fw-semibold d-block"
                                    style="color: rgba(29, 42, 104, 0.7);">Dirección</small>
                                <span class="fw-medium">
                                    <i class="fas fa-map-marker-alt me-1" style="color: #F4B324;"></i> {{
                                    personaSeleccionada.direccion }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn w-100 rounded-pill fw-bold border-0 shadow-sm"
                            style="background-color: #1D2A68; color: white;" data-bs-dismiss="modal">
                            Cerrar Detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import API from "@/assets/js/axios"
import { confimar, confimarhabi, mostraralertas2 } from "@/assets/js/funciones/functions";
import { getMe } from "@/assets/js/auth";

export default {
    data() {
        return {
            baseUrl: "/sistma", // Corregido de /sistma a /sistema
            totalPersonas: 0,
            personaSeleccionada: {}, // Para el modal de detalles
            rolesDisponibles: [],
            objetoRolesList: [],
            objetoData: {
                cedula: "",
                nombres: "",
                apellidos: "",
                fecha_nacimiento: "",
                direccion: "",
                telefono: "",
                correo: "",
                sexo: "",
                foto: "",
                previewFoto: "",
                estado: 1,
                crear_usuario: false,
                id_rol: "",
            },
            errorsData: {
                cedula: false,
                nombres: false,
                apellidos: false,
                fecha_nacimiento: false,
                direccion: false,
                telefono: false,
                correo: false,
                sexo: false,
                foto: false
            },
            objetoEdit: {
                id_persona: 0,
                cedula: "",
                nombres: "",
                apellidos: "",
                fecha_nacimiento: "",
                direccion: "",
                telefono: "",
                correo: "",
                sexo: "",
                foto: "",
                previewFoto: "",
                estado: "",
            },
            errorsEdit: {
                cedula: false,
                nombres: false,
                apellidos: false,
                fecha_nacimiento: false,
                direccion: false,
                telefono: false,
                correo: false,
                sexo: false,
                foto: false,
                estado: false
            },
            busqueda: '',
            timeoutBusqueda: null,
            objetoList: [],
            cargando: false,
            currentPage: 1,
            lastPage: 1,
            refreshKey: Date.now(),
            idpersonalog: 0,
        }
    },
    computed: {
        paginasMostradas() {
            let pages = [];
            let start = Math.max(1, this.currentPage - 2);
            let end = Math.min(this.lastPage, start + 4);

            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    watch: {
        busqueda(newVal) {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.currentPage = 1;
                this.getData();
            }, 500);
        }
    },
    async mounted() {
        const me = await getMe();
        this.idpersonalog = me.id_persona;
        await this.getData();
    },
    methods: {
        async verificarCreacionUsuario() {
            // Si el switch se acaba de encender
            if (this.objetoData.crear_usuario) {

                const fechaNacimiento = this.objetoData.fecha_nacimiento;

                // Validación extra: verificar si la fecha ya fue ingresada antes de activar el switch
                if (!fechaNacimiento) {
                    mostraralertas2("Por favor, ingrese la fecha de nacimiento primero para poder asignar roles.", "warning");
                    // Desmarcamos el switch automáticamente porque falta la fecha
                    this.objetoData.crear_usuario = false;
                    return;
                }

                try {
                    // Llamamos a los métodos que solicitaste
                    await this.GetObjetoList();
                    this.filtrarRolesPorEdad(fechaNacimiento);
                } catch (error) {
                    console.error("Error al obtener la lista de roles:", error);
                    mostraralertas2("Hubo un error al cargar los roles disponibles.", "error");
                    this.objetoData.crear_usuario = false;
                }
            } else {
                // Si el switch se apagó, simplemente limpiamos los roles disponibles
                this.rolesDisponibles = [];
                this.objetoData.id_rol = "";
            }
        },
        filtrarRolesPorEdad(fechaNacimiento) {
            const edad = this.calcularEdad(fechaNacimiento);

            // Suponiendo que el rol de estudiante tiene la palabra "estudiante" en su nombre
            if (edad > 20) {
                // Si es mayor a 20, mostramos todos los roles que NO sean estudiante
                this.rolesDisponibles = this.objetoRolesList.filter(
                    rol => !rol.nombre.toLowerCase().includes('estudiante')
                );
            } else {
                // Si tiene 20 o menos, mostramos SOLO el rol de estudiante
                this.rolesDisponibles = this.objetoRolesList.filter(
                    rol => rol.nombre.toLowerCase().includes('estudiante')
                );
            }
        },
        async GetObjetoList() {
            try {
                const response = await API.get(`${this.baseUrl}/roleshabilitados`); // Usa tu endpoint Roleshabilitados
                this.objetoRolesList = response.data?.data || [];
            } catch (error) {
                console.error("❌ Error al obtener roles:", error);
            }
        },
        calcularEdad(fechaNacimiento) {
            if (!fechaNacimiento) return 0;
            const hoy = new Date();
            const nac = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - nac.getFullYear();
            const m = hoy.getMonth() - nac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) {
                edad--;
            }
            return edad;
        },
        // Carga la foto y genera el base64 para vista previa y envío
        handleFileUpload(event, action) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                // Guarda la imagen en base64
                if (action === 'crear') {
                    this.objetoData.previewFoto = e.target.result;
                    this.objetoData.foto = e.target.result.split(',')[1]; // Solo la cadena base64 para el backend si lo requiere
                } else {
                    this.objetoEdit.previewFoto = e.target.result;
                    this.objetoEdit.foto = e.target.result.split(',')[1];
                }
            };
            reader.readAsDataURL(file);
        },

        cargarDetalles(user) {
            this.personaSeleccionada = { ...user };
            this.personaSeleccionada.previewFoto = this.getPhotoUrl(user.id_persona);
        },

        cargarDatosEdicion(user) {
            this.errorsEdit = { cedula: false, nombres: false, apellidos: false, fecha_nacimiento: false, direccion: false, telefono: false, correo: false, sexo: false, foto: false, estado: false };
            this.objetoEdit = {
                id_persona: user.id_persona,
                cedula: user.cedula,
                nombres: user.nombres,
                apellidos: user.apellidos,
                fecha_nacimiento: user.fecha_nacimiento,
                direccion: user.direccion,
                telefono: user.telefono,
                correo: user.correo,
                sexo: user.sexo,
                foto: "", // se mantiene vacía si no se sube una nueva
                previewFoto: this.getPhotoUrl(user.id_persona), // Carga la URL actual para previsualizar
                estado: user.estado,
            };
        },

        getPhotoUrl(ci) {
            if (!ci) return "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
            const baseURL2 = API.defaults.baseURL;
            return `${baseURL2}/sistma/imagenpersona/${ci}?v=${this.refreshKey}`;
        },

        handleImageError(event) {
            event.target.src = "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png";
        },

        cambiarPagina(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.getData();
            }
        },

        async getData() {
            this.cargando = true;
            try {
                const response = await API.get(`${this.baseUrl}/personas`, {
                    params: {
                        page: this.currentPage,
                        search_query: this.busqueda
                    }
                });

                const data = response.data?.data || [];
                const pagination = response.data?.pagination || {};

                this.currentPage = pagination.current_page || 1;
                this.lastPage = pagination.last_page || 1;
                this.totalPersonas = pagination.total || 0;
                this.objetoList = data;


            } catch (error) {
                console.warn("⚠️ Error al obtener datos:", error?.response?.data || error);
                this.objetoList = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.cargando = false;
            }
        },

        validarFormularioCrear() {
            this.errorsData.cedula = !this.objetoData.cedula || this.objetoData.cedula.trim() === "";
            this.errorsData.nombres = !this.objetoData.nombres || this.objetoData.nombres.trim() === "";
            this.errorsData.apellidos = !this.objetoData.apellidos || this.objetoData.apellidos.trim() === "";
            this.errorsData.fecha_nacimiento = !this.objetoData.fecha_nacimiento || this.objetoData.fecha_nacimiento.trim() === "";
            this.errorsData.direccion = !this.objetoData.direccion || this.objetoData.direccion.trim() === "";
            this.errorsData.telefono = !this.objetoData.telefono || this.objetoData.telefono.trim() === "";
            this.errorsData.correo = !this.objetoData.correo || this.objetoData.correo.trim() === "";
            this.errorsData.sexo = !this.objetoData.sexo || this.objetoData.sexo.trim() === "";

            return !Object.values(this.errorsData).some(val => val === true);
        },

        async guardarData() {
            if (!this.validarFormularioCrear()) {
                return;
            }

            try {
                const response = await API.post(`${this.baseUrl}/personas`, this.objetoData);
                if (response) {
                    console.log(response);
                    const idpersona = response.data.data.id_persona;
                    if (this.objetoData.crear_usuario) {

                        const response2 = await API.post(`${this.baseUrl}/usuarios/store`, {
                            id_persona: idpersona,
                            id_rol: this.objetoData.id_rol,
                            username: this.objetoData.cedula
                        });
                        if (response2) {
                            mostraralertas2("Persona y usuario creados exitosamente", "success");
                            this.refreshKey = Date.now(); // Fuerza refresco de imágenes
                            await this.getData();
                            this.limpiar();
                            document.getElementById('btnCloseModalCrear').click();
                        } else {
                            mostraralertas2("Se recibió una respuesta inesperada del servidor.", "error");
                        }
                    } else {
                        mostraralertas2("Persona creada exitosamente", "success");
                        this.refreshKey = Date.now(); // Fuerza refresco de imágenes
                        await this.getData();
                        this.limpiar();
                        document.getElementById('btnCloseModalCrear').click();
                    }
                } else {
                    mostraralertas2("Se recibió una respuesta inesperada del servidor.", "error");
                }
            } catch (error) {
                console.error("❌ Error al crear persona:", error?.response?.data || error);
                mostraralertas2("Error al crear persona. Por favor, inténtelo de nuevo.", "error");
            }
        },

        validarFormularioEditar() {
            this.errorsEdit.cedula = !this.objetoEdit.cedula || String(this.objetoEdit.cedula).trim() === "";
            this.errorsEdit.nombres = !this.objetoEdit.nombres || this.objetoEdit.nombres.trim() === "";
            this.errorsEdit.apellidos = !this.objetoEdit.apellidos || this.objetoEdit.apellidos.trim() === "";
            this.errorsEdit.fecha_nacimiento = !this.objetoEdit.fecha_nacimiento || String(this.objetoEdit.fecha_nacimiento).trim() === "";
            this.errorsEdit.direccion = !this.objetoEdit.direccion || this.objetoEdit.direccion.trim() === "";
            this.errorsEdit.telefono = !this.objetoEdit.telefono || String(this.objetoEdit.telefono).trim() === "";
            this.errorsEdit.correo = !this.objetoEdit.correo || this.objetoEdit.correo.trim() === "";
            this.errorsEdit.sexo = !this.objetoEdit.sexo || this.objetoEdit.sexo.trim() === "";

            return !this.errorsEdit.cedula && !this.errorsEdit.nombres && !this.errorsEdit.apellidos && !this.errorsEdit.fecha_nacimiento && !this.errorsEdit.direccion && !this.errorsEdit.telefono && !this.errorsEdit.correo && !this.errorsEdit.sexo;
        },

        async editarData() {
            if (!this.validarFormularioEditar()) {
                return;
            }

            try {
                const response = await API.put(`${this.baseUrl}/personas/${this.objetoEdit.id_persona}`, this.objetoEdit);
                if (response) {
                    mostraralertas2("Persona actualizada exitosamente", "success");
                    this.refreshKey = Date.now(); // Fuerza refresco de imágenes
                    await this.getData();
                    this.limpiar();
                    document.getElementById('btnCloseModalEditar').click();
                } else {
                    mostraralertas2("Se recibió una respuesta inesperada.", "error");
                }
            } catch (error) {
                console.error("❌ Error al actualizar persona:", error?.response?.data || error);
                mostraralertas2("Error al actualizar persona. Por favor, inténtelo de nuevo.", "error");
            }
        },

        limpiar() {
            this.objetoData = { cedula: "", nombres: "", apellidos: "", fecha_nacimiento: "", direccion: "", telefono: "", correo: "", sexo: "", foto: "", previewFoto: "", estado: 1, crear_usuario: false };
            this.objetoEdit = { id_persona: 0, cedula: "", nombres: "", apellidos: "", fecha_nacimiento: "", direccion: "", telefono: "", correo: "", sexo: "", foto: "", previewFoto: "", estado: "" };
            this.errorsData = { cedula: false, nombres: false, apellidos: false, fecha_nacimiento: false, direccion: false, telefono: false, correo: false, sexo: false, foto: false };
            this.errorsEdit = { cedula: false, nombres: false, apellidos: false, fecha_nacimiento: false, direccion: false, telefono: false, correo: false, sexo: false, foto: false, estado: false };
            this.personaSeleccionada = {};

            // Limpia los inputs file
            if (document.getElementById('fotoCrear')) document.getElementById('fotoCrear').value = "";
            if (document.getElementById('fotoEditar')) document.getElementById('fotoEditar').value = "";
        },

        async eliminar(id, nombre) {
            try {
                await confimar(
                    `${this.baseUrl}/ihabilitar_persona/`,
                    id,
                    'Inhabilitar registro',
                    '¿Realmente desea inhabilitar a ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al inhabilitar:", error);
            }
        },

        async habilitar(id, nombre) {
            try {
                await confimarhabi(
                    `${this.baseUrl}/habilitar_persona/`,
                    id,
                    'Habilitar registro',
                    '¿Desea habilitar a ' + nombre + '?',
                    this.objetoList
                );
                setTimeout(() => { this.getData(); }, 1000);
            } catch (error) {
                console.error("Error al habilitar:", error);
            }
        }
    }
}
</script>

<style scoped>
/* Transiciones suaves y retoques extra */
.avatar-sm img {
    transition: transform 0.3s ease;
}

table tbody tr:hover .avatar-sm img {
    transform: scale(1.1);
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}

.modal-content {
    overflow: hidden;
}

.form-control:focus,
.form-select:focus {
    box-shadow: none;
    border-color: var(--bs-primary);
}

/* Contenedor principal del header con un borde lateral sutil */
.custom-header {
    border-left: 5px solid #198754;
    /* Cambia al color de tu var(--green-800) si lo prefieres */
    transition: all 0.3s ease;
}

.custom-header:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
}

/* Animación del ícono principal cuando pasas el mouse por el header */
.header-icon {
    width: 55px;
    height: 55px;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.custom-header:hover .header-icon {
    transform: rotate(-10deg) scale(1.1);
}

/* Efecto hover interactivo para el contador (Badge) */
.stat-badge {
    transition: all 0.3s ease;
    cursor: default;
}

.stat-badge:hover {
    transform: translateY(-2px);
    background-color: #198754 !important;
    /* Verde success de Bootstrap */
    color: white !important;
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

/* Efecto hover para el botón principal */
.interactive-btn {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    /* Opcional: puedes ponerle un gradiente en lugar de un color plano */
    /* background: linear-gradient(135deg, #198754, #20c997); */
    /* border: none; */
}

.interactive-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 12px rgba(25, 135, 84, 0.25) !important;
}

.interactive-btn:active {
    transform: translateY(1px);
}
</style>
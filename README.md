# 🎓 Frontmilenio

> **Plataforma de Gestión Académica Integral**

Una aplicación web moderna desarrollada con **Vue.js** para la gestión completa de instituciones educativas, incluyendo administración de cursos, estudiantes, matrículas, calificaciones y perfiles de usuario.

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Instalación](#-instalación)
- [Ejecución](#-ejecución)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Scripts Disponibles](#-scripts-disponibles)
- [Configuración](#-configuración)

---

## ✨ Características

- ✅ **Autenticación y Login** - Sistema seguro de inicio de sesión
- 📚 **Gestión de Cursos** - Administración de cursos y asignaturas
- 👥 **Gestión de Estudiantes** - Control de estudiantes y sus datos
- 📝 **Control de Matrículas** - Registro y seguimiento de matrículas
- 📊 **Sistema de Calificaciones** - Registro y visualización de notas
- 👨‍🏫 **Panel de Tutores** - Gestión de conducta y desempeño
- 👤 **Perfil de Usuario** - Información personal y préstamos
- 📈 **Reportes Académicos** - Históricos y análisis de rendimiento
- ⚙️ **Gestión Administrativa** - Usuarios, roles, periodos lectivos y más

---

## 🛠️ Tecnologías

| Tecnología | Versión | Descripción |
|-----------|---------|------------|
| **Vue.js** | 3.x | Framework progresivo para interfaces |
| **Vue Router** | Latest | Enrutamiento de la aplicación |
| **Vuex** | Latest | Gestión de estado centralizado |
| **Axios** | Latest | Cliente HTTP para peticiones API |
| **Babel** | Latest | Compilador de JavaScript |

---

## 📦 Instalación

### Requisitos Previos

- **Node.js** (v14 o superior)
- **npm** (v6 o superior) o **yarn**

### Pasos de Instalación

1. **Clonar o descargar el proyecto:**
   ```bash
   git clone <repository-url>
   cd frontmilenio
   ```

2. **Instalar dependencias:**
   ```bash
   npm install
   ```
   o con yarn:
   ```bash
   yarn install
   ```

---

## 🚀 Ejecución

### Modo Desarrollo

Compila y carga la aplicación con hot-reload:

```bash
npm run serve
```

La aplicación se abrirá en: `http://localhost:8080`

### Modo Producción

Compilar y minificar para producción:

```bash
npm run build
```

---

## 📁 Estructura del Proyecto

```
frontmilenio/
├── public/                          # Archivos estáticos
│   └── index.html                   # Archivo HTML principal
│
├── src/                             # Código fuente principal
│   ├── App.vue                      # Componente raíz de la aplicación
│   ├── main.js                      # Punto de entrada de Vue.js
│   │
│   ├── assets/                      # Recursos de la aplicación
│   │   ├── img/                     # Imágenes (logos, iconos, etc.)
│   │   └── js/                      # Scripts auxiliares
│   │       ├── auth.js              # Autenticación y token
│   │       ├── axios.js             # Configuración de cliente HTTP
│   │       ├── login.js             # Lógica de login
│   │       └── funciones/
│   │           ├── functions.js     # Funciones generales
│   │           └── loginfunction.js # Funciones de login
│   │
│   ├── components/                  # Componentes reutilizables
│   │   ├── home.vue                 # Componente de inicio
│   │   ├── Login.vue                # Componente de login
│   │   │
│   │   ├── cursos/                  # Módulo de Cursos
│   │   │   └── familiar_cursos/
│   │   │       └── familiarcursos.vue   # Cursos para representantes
│   │   │
│   │   ├── gestion_docente/         # Módulo de Gestión Docente
│   │   │   ├── estudiantes/
│   │   │   │   └── misestudiantes.vue   # Lista de estudiantes del docente
│   │   │   ├── notas/
│   │   │   │   └── registro_calificaciones/   # Registro de notas
│   │   │   └── tutor/
│   │   │       ├── calificaciones_alumnos.vue # Ver calificaciones
│   │   │       ├── calificar_conducta.vue     # Calificar conducta
│   │   │       └── tutor_estudiante.vue       # Información del estudiante
│   │   │
│   │   ├── mantenimiento/           # Módulo de Mantenimiento
│   │   │   ├── asignaturas/
│   │   │   │   └── Asignaturas.vue      # CRUD de asignaturas
│   │   │   ├── cronograma_matriculas/
│   │   │   │   └── Cronograma_matriculas.vue  # Cronograma académico
│   │   │   ├── curso_asignaturas/
│   │   │   │   └── Cursos_Asignaturas.vue     # Relación cursos-asignaturas
│   │   │   ├── cursos/
│   │   │   │   └── Cursos.vue          # CRUD de cursos
│   │   │   ├── especialidades/
│   │   │   │   └── Especialidades.vue  # CRUD de especialidades
│   │   │   ├── familias/
│   │   │   │   └── Familias.vue        # CRUD de familias
│   │   │   ├── horarios_clases/
│   │   │   │   └── Horarios_clases.vue # Gestión de horarios
│   │   │   ├── niveles_academicos/
│   │   │   │   └── Niveles_academicos.vue # Niveles educativos
│   │   │   ├── periodos_lectivos/
│   │   │   │   └── Periodos_lectivos.vue   # Periodos escolares
│   │   │   ├── personas/
│   │   │   │   └── Personas.vue        # CRUD de personas
│   │   │   ├── roles/
│   │   │   │   └── Roles.vue           # CRUD de roles
│   │   │   └── usuarios/
│   │   │       └── Usuarios.vue        # CRUD de usuarios
│   │   │
│   │   ├── matricula/                # Módulo de Matrículas
│   │   │   ├── matricula.vue             # Panel principal
│   │   │   ├── lista_matricula/
│   │   │   │   └── lista_matricula.vue   # Listado de matrículas
│   │   │   ├── mis_matriculas/
│   │   │   │   └── mis_matriculas.vue    # Mis matrículas personales
│   │   │   └── representante/
│   │   │       └── historial_matriculas_representado.vue  # Historial
│   │   │
│   │   ├── Notas/                   # Módulo de Notas
│   │   │   ├── control_subida_notas/
│   │   │   │   └── control_subida_notas.vue   # Control de notas cargadas
│   │   │   ├── historico_notas/
│   │   │   │   └── lista_notas.vue            # Histórico de notas
│   │   │   ├── mis_notas/
│   │   │   │   ├── mis_notas.vue              # Mis notas actuales
│   │   │   │   └── mis_notas_historico.vue    # Historial de mis notas
│   │   │   └── representante/
│   │   │       └── notas_historico_representado.vue  # Notas del representado
│   │   │
│   │   └── perfil/
│   │       └── perfil.vue           # Perfil de usuario
│   │
│   ├── router/                      # Configuración de rutas
│   │   └── index.js                 # Definición de todas las rutas
│   │
│   ├── store/                       # Gestión de estado (Vuex)
│   │   ├── index.js                 # Estado global principal
│   │   └── custom.js                # Módulos personalizados
│   │
│   └── views/                       # Vistas principales
│       ├── HomeView.vue             # Vista de inicio
│       ├── LoginView.vue            # Vista de login
│       ├── Cursos/
│       │   └── FamiliarCursos/      # Vistas de cursos para familias
│       ├── Gestion_docente/         # Vistas de gestión docente
│       │   ├── estudiantes/
│       │   ├── notas/
│       │   └── tutor/
│       ├── Mantenimiento/           # Vistas de configuración
│       │   ├── Asignaturas/
│       │   ├── Cronograma_Matriculas/
│       │   ├── Cursos/
│       │   ├── Cursos_Asignaturas/
│       │   ├── Especialidades/
│       │   ├── Familias/
│       │   ├── Horarios_clases/
│       │   ├── Nivel_Academico/
│       │   ├── Periodos_lectivos/
│       │   ├── Personas/
│       │   ├── Roles/
│       │   └── Usuarios/
│       ├── Matricula/               # Vistas de matrículas
│       │   ├── PanelmatriculaView.vue
│       │   ├── Historial_Matricula/
│       │   ├── Mis_Matriculas/
│       │   └── Representante/
│       ├── Notas/                   # Vistas de notas
│       │   ├── Control_Subida_Notas/
│       │   ├── Mis_Notas/
│       │   └── Representante/
│       └── Perfil/
│           └── PerfilView.vue       # Vista de perfil
│
├── babel.config.js                  # Configuración de Babel
├── jsconfig.json                    # Configuración de JavaScript
├── vue.config.js                    # Configuración de Vue
├── package.json                     # Dependencias del proyecto
└── README.md                        # Este archivo

```

---

## 📝 Descripción Detallada de Módulos

### 🔐 **Autenticación (Login)**
- **Archivos:** `Login.vue`, `LoginView.vue`, `auth.js`, `login.js`, `loginfunction.js`
- **Descripción:** Sistema de autenticación y gestión de sesiones
- **Funcionalidad:** Validación de credenciales, generación de tokens, manejo de sesiones

### 📚 **Cursos**
- **Archivos:** `familiarcursos.vue`
- **Descripción:** Gestión de cursos desde perspectiva de estudiantes y representantes
- **Funcionalidad:** Visualización de cursos inscritos, información del curso

### 👥 **Gestión Docente**
- **Archivos:** `gestion_docente/*`
- **Descripción:** Panel de control para docentes
- **Funcionalidad:** 
  - Ver estudiantes asignados
  - Registrar calificaciones
  - Calificar conducta
  - Acceso a información del estudiante

### ⚙️ **Mantenimiento**
- **Archivos:** `mantenimiento/*`
- **Descripción:** Módulo administrativo para configuración del sistema
- **Funcionalidad:**
  - CRUD de asignaturas, cursos, especialidades
  - Gestión de horarios y periodos lectivos
  - Administración de usuarios y roles
  - Configuración de personas y familias

### 📋 **Matrículas**
- **Archivos:** `matricula/*`
- **Descripción:** Gestión del proceso de matrícula
- **Funcionalidad:**
  - Registro de nuevas matrículas
  - Visualización de matrículas actuales
  - Historial de matrículas
  - Portales para representantes

### 📊 **Notas**
- **Archivos:** `Notas/*`
- **Descripción:** Sistema de calificaciones académicas
- **Funcionalidad:**
  - Carga y control de notas
  - Visualización de notas por período
  - Historial académico
  - Acceso para estudiantes y representantes

### 👤 **Perfil**
- **Archivos:** `perfil.vue`
- **Descripción:** Gestión del perfil de usuario
- **Funcionalidad:** Visualización y edición de datos personales

---

## 📜 Scripts Disponibles

```bash
# Instalar dependencias
npm install

# Ejecutar en modo desarrollo con hot-reload
npm run serve

# Compilar y minificar para producción
npm run build

# Ejecutar linter
npm run lint
```

---

## ⚙️ Configuración

### Archivos de Configuración

- **`babel.config.js`** - Configuración del transpilador Babel
- **`jsconfig.json`** - Configuración de rutas y módulos de JavaScript
- **`vue.config.js`** - Configuración personalizada de V![Logo Frontmilenio](src/assets/img/mile.png)![Logo Frontmilenio](src/assets/img/mile.png)ue CLI
- **`package.json`** - Dependencias y scripts del proyecto

### Variables de Entorno

Crea un archivo `.env.local` en la raíz del proyecto para configurar:

```
VUE_APP_API_URL=http://localhost:3000/api
VUE_APP_ENVIRONMENT=development
```

---

## 🔗 Referencias

- [Documentación de Vue.js](https://vuejs.org/)
- [Documentación de Vue Router](https://router.vuejs.org/)
- [Documentación de Vuex](https://vuex.vuejs.org/)
- [Documentación de Vue CLI](https://cli.vuejs.org/)

---

## 📄 Licencia

Este proyecto es de propiedad de **Frontmilenio**.

---

**Última actualización:** Junio 2024

# BackMilenio - Sistema de Gestión Académica

<p align="center">
  <strong>Plataforma integral de gestión educativa basada en Laravel</strong>
</p>

---

## 📋 Descripción General

**BackMilenio** es un sistema backend de gestión académica diseñado específicamente para instituciones educativas. Proporciona una solución completa para el control y seguimiento de estudiantes, cursos, asignaturas, calificaciones, asistencia, conducta y todo el proceso de matriculación y administración académica.

Construido sobre el framework **Laravel**, ofrece una API robusta y escalable con arquitectura MVC, autenticación segura mediante JWT, y todas las herramientas necesarias para la gestión integral de una institución educativa.

### Tecnologías Principales

- **Framework**: Laravel (PHP moderno y elegante)
- **Autenticación**: JWT (JSON Web Tokens) - tymon/jwt-auth
- **Base de Datos**: SQLite/MySQL/PostgreSQL (agnóstica)
- **Frontend Build**: Vite.js
- **Testing**: PHPUnit
- **Control de Versiones**: Git

---

## 📁 Estructura del Proyecto

### 1. **Directorio Raíz - Archivos de Configuración**

#### `artisan`
Herramienta CLI (Command Line Interface) de Laravel que proporciona comandos para:
- Crear controladores, modelos y migraciones
- Ejecutar migraciones de base de datos
- Gestionar caché, sesiones y colas
- Ejecutar servidores de desarrollo
- Ejecutar tareas programadas y trabajos de fondo

#### `composer.json`
Archivo de configuración de Composer (gestor de dependencias PHP) que define:
- Dependencias del proyecto (Laravel, JWT, etc.)
- Scripts personalizados para ejecutar comandos
- Información del proyecto (nombre, descripción, autor)
- Requisitos mínimos de PHP y extensiones

#### `package.json`
Archivo de configuración de Node.js/npm que especifica:
- Dependencias JavaScript/Node (Vite, Vue, etc.)
- Scripts para desarrollo (`npm run dev`) y producción (`npm run build`)
- Versión de Node requerida
- Dependencias de desarrollo (Vite, PostCSS, Tailwind CSS, etc.)

#### `phpunit.xml`
Configuración de PHPUnit para pruebas unitarias e integración:
- Definición de grupos de pruebas
- Configuración de base de datos de testing
- Exclusión de directorios
- Configuración de cobertura de código

#### `vite.config.js`
Configuración de Vite (empaquetador de módulos moderno):
- Definición de entrada de archivos (CSS, JavaScript)
- Rutas de salida de construcción
- Configuración de servidor de desarrollo
- Transformaciones de activos

---

### 2. **Directorio `/app` - Lógica de Aplicación**

Contiene toda la lógica de negocio y las clases principales de la aplicación.

#### `/app/Console`

**`Kernel.php`** - Núcleo de la consola
- Registra comandos artisan personalizados
- Define tareas programadas (Cron Jobs)
- Configura la planificación de trabajos en segundo plano

#### `/app/Exceptions`

**`Handler.php`** - Manejador global de excepciones
- Captura y procesa todas las excepciones de la aplicación
- Define reportes de errores personalizados
- Renderiza respuestas de error en JSON para APIs
- Maneja errores 404, validación, autenticación, etc.

#### `/app/Http`

**`Kernel.php`** - Núcleo HTTP
- Registra middleware global de la aplicación
- Define grupos de rutas y sus middleware asociados
- Configura middleware de autenticación, CORS, rate limiting

**`/app/Http/Controllers`** - Controladores
- Procesa las solicitudes HTTP entrantes
- Interactúa con modelos para recuperar/manipular datos
- Retorna respuestas JSON/vistas
- Implementa la lógica de negocio de cada endpoint

**`/app/Http/Middleware`** - Middleware
- Procesa solicitudes antes/después de llegar a controladores
- Ejemplos: autenticación, CORS, rate limiting, validación de permisos
- Actúa como filtros de seguridad y validación

#### `/app/Models` - Modelos Eloquent

Cada modelo representa una tabla en la base de datos y define sus relaciones:

- **`Asignaturas.php`** - Representa las materias/asignaturas del currículo
- **`Asistencia.php`** - Registro de asistencia de estudiantes a clases
- **`Calificaciones.php`** - Notas y calificaciones de estudiantes por asignatura
- **`Conducta.php`** - Registro de conducta y comportamiento estudiantil
- **`Control_subida_notas.php`** - Control y auditoría de carga de calificaciones
- **`Cronograma_matriculas.php`** - Calendario de períodos de matriculación
- **`Curso_Asignaturas.php`** - Relación muchos-a-muchos entre cursos y asignaturas
- **`Cursos.php`** - Información de cursos/grados académicos (1ero, 2do, 3ro, etc.)
- **`Especialidades.php`** - Especialidades o ramas académicas (Técnica, Académica, etc.)
- **`Familia.php`** - Información de grupos familiares/padres de estudiantes
- **`Horarios_clases.php`** - Horarios de clases por asignatura y curso
- **`Matriculas.php`** - Registro de matriculación de estudiantes en cursos
- **`Niveles_academicos.php`** - Niveles del sistema educativo (Primaria, Secundaria, etc.)
- **`Periodos_lectivos.php`** - Períodos académicos (bimestres, trimestres, semestres)
- **`Personas.php`** - Información demográfica y personal de personas (estudiantes, docentes)
- **`Rol.php`** - Definición de roles en el sistema (Administrador, Docente, Estudiante, etc.)
- **`User.php`** - Cuentas de usuario con autenticación y relaciones con roles

#### `/app/Providers` - Service Providers

Registradores de servicios que bootstrap la aplicación:

- **`AppServiceProvider.php`** - Servicios generales de la aplicación
- **`AuthServiceProvider.php`** - Autorización, políticas y gates
- **`BroadcastServiceProvider.php`** - Configuración de eventos en tiempo real (Broadcasting)
- **`EventServiceProvider.php`** - Registro de eventos y listeners
- **`RouteServiceProvider.php`** - Carga de rutas y model binding

---

### 3. **Directorio `/bootstrap` - Inicialización de la Aplicación**

#### `/bootstrap/app.php`
- Primer archivo que ejecuta Laravel
- Crea la instancia de la aplicación
- Registra configuraciones iniciales
- Vincula contenedor de servicios

#### `/bootstrap/cache`
- **`packages.php`** - Caché de paquetes Composer detectados
- **`services.php`** - Caché de service providers compilados
- Mejora el rendimiento en producción

---

### 4. **Directorio `/config` - Configuración de la Aplicación**

Archivos de configuración que definen comportamientos de la aplicación:

- **`app.php`** - Configuración general (nombre, timezone, providers)
- **`auth.php`** - Configuración de autenticación y guards
- **`broadcasting.php`** - Configuración de eventos en tiempo real
- **`cache.php`** - Drivers y configuración de caché (Redis, Memcached, file)
- **`cors.php`** - Configuración CORS para APIs (origen permitido, métodos, headers)
- **`database.php`** - Configuración de conexiones a base de datos
- **`filesystems.php`** - Configuración de almacenamiento de archivos (local, S3)
- **`hashing.php`** - Algoritmo de hash para contraseñas (bcrypt, argon2)
- **`jwt.php`** - Configuración de autenticación JWT (claves, algoritmo, TTL)
- **`logging.php`** - Configuración de logs (canal, nivel, formato)
- **`mail.php`** - Configuración de envío de correos
- **`queue.php`** - Configuración de colas de trabajos
- **`sanctum.php`** - Configuración de API tokens Sanctum
- **`services.php`** - Credenciales de servicios externos
- **`session.php`** - Configuración de sesiones
- **`view.php`** - Rutas de vistas y caché

---

### 5. **Directorio `/database` - Base de Datos**

#### `/database/factories`
- Generadores de datos ficticia para testing
- Define cómo crear registros de prueba realistas

#### `/database/migrations`
- Scripts versionados que crean/modifican la estructura de la BD
- Permite versionado y reversión de cambios de esquema
- Se ejecutan con `php artisan migrate`

#### `/database/seeders`

**`DatabaseSeeder.php`** - Sembrador principal
- Llena la base de datos con datos iniciales
- Puede llamar a otros seeders
- Útil para ambiente de desarrollo y testing

---

### 6. **Directorio `/public` - Punto de Acceso Público**

#### `index.php`
- Punto de entrada único de la aplicación (Front Controller)
- Carga el autoloader de Composer
- Bootstrap la aplicación Laravel
- Procesa todas las solicitudes HTTP

#### `robots.txt`
- Instrucciones para crawlers de motores de búsqueda
- Define qué directorios indexar o no

---

### 7. **Directorio `/resources` - Activos y Vistas**

#### `/resources/css`
- **`app.css`** - Hoja de estilos principal compilada
- Contiene estilos CSS globales de la aplicación

#### `/resources/js`
- **`app.js`** - Archivo JavaScript principal (punto de entrada)
- **`bootstrap.js`** - Inicialización de librerías JavaScript y configuraciones

#### `/resources/views`
- **`welcome.blade.php`** - Vista de bienvenida predeterminada
- Archivos `.blade.php` usan el motor de plantillas Blade de Laravel
- Las vistas son plantillas que retornan en respuestas HTTP

---

### 8. **Directorio `/routes` - Definición de Rutas**

Define los endpoints y mapeo a controladores:

- **`api.php`** - Rutas de API REST (JSON responses)
  - Rutas para todas las entidades académicas
  - Controladas por middleware de autenticación JWT
  - Prefijo `/api`

- **`web.php`** - Rutas web (HTML responses)
  - Rutas para vistas (Blade templates)
  - Protegidas por sesiones

- **`channels.php`** - Canales de broadcasting en tiempo real
  - Define autorización para eventos reales

- **`console.php`** - Comandos artisan personalizados
  - Define tareas ejecutables desde CLI

---

### 9. **Directorio `/storage` - Almacenamiento**

#### `/storage/app`
- Archivos subidos por usuarios
- **`/storage/app/public`** - Archivos públicamente accesibles (avatar, documentos)

#### `/storage/framework`
- **`/cache`** - Caché de aplicación
- **`/sessions`** - Datos de sesiones (si se usa file driver)
- **`/testing`** - Archivos temporales de testing
- **`/views`** - Vistas compiladas en caché

#### `/storage/logs`
- **Archivos de log** - Registro de errores, actividades y auditoría
- Organizados por fecha y nivel de severidad

---

### 10. **Directorio `/tests` - Pruebas Automáticas**

#### `CreatesApplication.php`
- Trait que crea la aplicación para testing
- Resetea la base de datos entre pruebas

#### `TestCase.php`
- Clase base para todas las pruebas
- Proporciona métodos helper para assertions

#### `/tests/Feature`
- **`ExampleTest.php`** - Pruebas de funcionalidad end-to-end
- Pruebas de endpoints, flujos completos, integración

#### `/tests/Unit`
- **`ExampleTest.php`** - Pruebas unitarias aisladas
- Pruebas de métodos individuales, modelos, lógica

---

### 11. **Directorio `/vendor` - Dependencias Externas**

Contiene todas las librerías PHP instaladas vía Composer:

- **`autoload.php`** - Cargador automático de clases PSR-4
- **`laravel/`** - Framework Laravel y sus paquetes
- **`tymon/jwt-auth`** - Autenticación JWT
- **`symfony/`** - Componentes Symfony (console, http, etc.)
- **`guzzlehttp/`** - Cliente HTTP
- **`monolog/`** - Librería de logging
- **`doctrine/`** - ORM/ODM
- **`phpunit/`** - Framework de testing
- Cientos más de paquetes...

**Nota**: No se debe editar directamente, se regenera con `composer install`

---

## 🔧 Configuración e Instalación

### Requisitos
- PHP >= 8.1
- Composer
- Node.js >= 14
- SQLite, MySQL o PostgreSQL

### Instalación

```bash
# 1. Clonar repositorio
git clone <repo-url>
cd backmilenio

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JavaScript
npm install

# 4. Copiar archivo de entorno
cp .env.example .env

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Generar claves JWT
php artisan jwt:secret

# 7. Ejecutar migraciones
php artisan migrate

# 8. Ejecutar seeders (opcional)
php artisan db:seed

# 9. Compilar activos
npm run dev
```

---

## 🚀 Ejecución

### Desarrollo
```bash
# Terminal 1 - Servidor PHP
php artisan serve

# Terminal 2 - Compilador Vite
npm run dev
```

### Producción
```bash
# Compilar activos
npm run build

# Optimizar aplicación
php artisan optimize
```

---

## 🧪 Testing

```bash
# Ejecutar todas las pruebas
php artisan test

# Pruebas específicas
php artisan test tests/Feature/ExampleTest.php

# Con cobertura de código
php artisan test --coverage
```

---

## 📚 Arquitectura y Patrones

### MVC Pattern
- **Models** (`/app/Models`) - Lógica de datos
- **Views** (`/resources/views`) - Presentación
- **Controllers** (`/app/Http/Controllers`) - Lógica de negocio

### REST API
- Endpoints JSON documentados
- Versioning de API
- HTTP status codes apropiados

### Autenticación
- JWT (JSON Web Tokens) para API stateless
- Sessiones para web tradicional

### Autorización
- Roles basados en acceso (RBAC)
- Políticas y Gates para autorización granular

---

## 📖 Modelos de Datos Principales

### Entidades Académicas
- **Personas**: Base de toda la información de actores en el sistema
- **User**: Cuentas de usuario con autenticación
- **Rol**: Control de permisos y acceso
- **Familia**: Estructura de relaciones familiares

### Gestión Académica
- **Cursos**: Grados/niveles educativos
- **Asignaturas**: Materias del currículo
- **Especialidades**: Ramas académicas
- **Niveles_academicos**: Estratificación del sistema educativo
- **Periodos_lectivos**: Estructura temporal de períodos académicos

### Seguimiento Estudiantil
- **Matriculas**: Inscripción de estudiantes en cursos
- **Asistencia**: Control de presencia
- **Calificaciones**: Notas y evaluaciones
- **Conducta**: Registro comportamental
- **Control_subida_notas**: Auditoría de cambios en calificaciones

### Programación Académica
- **Horarios_clases**: Horarios de impartición
- **Curso_Asignaturas**: Asignaturas asignadas a cada curso
- **Cronograma_matriculas**: Fechas de períodos de matriculación

---

## 🔒 Seguridad

- Autenticación JWT con tokens con expiración
- Hash de contraseñas con Bcrypt/Argon2
- Protección CSRF en formularios
- Validación y sanitización de entradas
- Rate limiting en APIs
- CORS configurado restrictivamente

---

## 📝 Licencia

Este proyecto está bajo licencia MIT. Véase el archivo LICENSE para más detalles.

---

## 👥 Contribuciones

Las contribuciones son bienvenidas. Para cambios significativos, abre primero un issue para discutir los cambios propuestos.

---

**Última actualización**: 2026-06-21

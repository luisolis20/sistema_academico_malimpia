/**
 * En este archivo se definen las rutas de la aplicación
 * Se importa createRouter y createWebHistory de vue-router
 * Se importa el objeto router de vue-router
 * Se definen las rutas de la aplicación a la cual se mostrará la vista correspondiente
 */
import { createRouter, createWebHistory } from 'vue-router'// Importa createRouter y createWebHistory de vue-router
import HomeView from '../views/HomeView.vue'// Importa la vista HomeView
import LoginView from '../views/LoginView.vue'// Importa la vista LoginView
import UsuarioView from '@/views/Mantenimiento/Usuarios/UsuarioView.vue'// Importa la vista UsuarioView
import RolesView from '@/views/Mantenimiento/Roles/RolesView.vue'// Importa la vista RolesView
import PersonasView from '@/views/Mantenimiento/Personas/PersonasView.vue'// Importa la vista PersonasView
import FamiliasView from '@/views/Mantenimiento/Familias/FamiliasView.vue'// Importa la vista FamiliasView
import Niveles_Academicos from '@/views/Mantenimiento/Nivel_Academico/Nivel_AcademicoView.vue'// Importa la vista Niveles_Academicos
import EspecialidadesView from '@/views/Mantenimiento/Especialidades/EspecialidadesView.vue'// Importa la vista EspecialidadesView
import AsignaturasView from '@/views/Mantenimiento/Asignaturas/AsignaturasView.vue'// Importa la vista AsignaturasView
import Periodos_lectivosView from '@/views/Mantenimiento/Periodos_lectivos/Periodos_lectivosView.vue'// Importa la vista Periodos_lectivosView
import CursosView from '@/views/Mantenimiento/Cursos/CursosView.vue'// Importa la vista CursosView
import Cursos_AsignaturasView from '@/views/Mantenimiento/Cursos_Asignaturas/Cursos_AsignaturasView.vue'// Importa la vista Cursos_AsignaturasView
import Horarios_clasesView from '@/views/Mantenimiento/Horarios_clases/Horarios_clasesView.vue'// Importa la vista Horarios_clasesView
import Cronograma_MatriculasView from '@/views/Mantenimiento/Cronograma_Matriculas/Cronograma_MatriculasView.vue'// Importa la vista Cronograma_MatriculasView
import PerfilView from '@/views/Perfil/PerfilView.vue'// Importa la vista PerfilView
import MatriculaView from '@/views/Matricula/PanelmatriculaView.vue'// Importa la vista MatriculaView
import CursosFamiliarView from '@/views/Cursos/FamiliarCursos/CursosFamiliarView.vue'// Importa la vista CursosFamiliarView
import MisestudiantesView from '@/views/Gestion_docente/estudiantes/MisestudiantesView.vue'// Importa la vista MisestudiantesView
import Tutor_estudianteView from '@/views/Gestion_docente/tutor/Tutor_estudianteView.vue'// Importa la vista Tutor_estudianteView
import Historial_matriculaView from '@/views/Matricula/Historial_Matricula/Historial_matriculaView.vue'// Importa la vista Historial_matriculaView
import Control_Subida_NotasView from '@/views/Notas/Control_Subida_Notas/Control_Subida_NotasView.vue'// Importa la vista Control_Subida_NotasView
import RegistroCalificacionesView from '@/views/Gestion_docente/notas/registro_calificaciones/RegistroCalificacionesView.vue'// Importa la vista RegistroCalificacionesView
import HistoricoNotasView from '@/views/Gestion_docente/notas/historico_notas/HistoricoNotasView.vue'// Importa la vista HistoricoNotasView
import Tutor_Notas_estudianteView from '@/views/Gestion_docente/tutor/Tutor_Notas_estudianteView.vue'// Importa la vista Tutor_Notas_estudianteView
import Mis_NotasView from '@/views/Notas/Mis_Notas/Mis_NotasView.vue'// Importa la vista Mis_NotasView
import Mis_Notas_HistorialView from '@/views/Notas/Mis_Notas/Mis_Notas_HistorialView.vue'// Importa la vista Mis_Notas_HistorialView
import Notas_Historial_RepresentadoView from '@/views/Notas/Representante/Notas_Historial_RepresentadoView.vue'// Importa la vista Notas_Historial_RepresentadoView
import Mis_Historial_matriculaView from '@/views/Matricula/Mis_Matriculas/Mis_Historial_matriculaView.vue'// Importa la vista Mis_Historial_matriculaView
import Historial_matricula_RepreseView from '@/views/Matricula/Representante/Historial_matricula_RepreseView.vue'// Importa la vista Historial_matricula_RepreseView
import Tutor_calificar_conductaView from '@/views/Gestion_docente/tutor/Tutor_calificar_conductaView.vue'// Importa la vista Tutor_calificar_conductaView

/**
 * Constante de rutas para la aplicación
 * Se definen las rutas de la aplicación a la cual se mostrará la vista correspondiente
 * Se usa path para definir la ruta, name para definir el nombre de la ruta y component para definir la vista correspondiente
 */
const routes = [
  /**
   * Ruta para la vista de inicio de sesión
   * Esta usará la ruta /login
   */
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  /**
   * Ruta para la vista de inicio
   * Esta usará la ruta /principal
   */
  {
    path: '/principal',
    name: 'home',
    component: HomeView
  },
  /**
   * Ruta para la vista de panel de usuarios
   * Esta usará la ruta /panel-usuario
   */
  {
    path: '/panel-usuario',
    name: 'panelusuario',
    component: UsuarioView

  },
  /**
   * Ruta para la vista de roles
   * Esta usará la ruta /roles
   */
  {
    path: '/roles',
    name: 'roles',
    component: RolesView
  },
  /**
   * Ruta para la vista de personas
   * Esta usará la ruta /personas
   */
  {
    path: '/personas',
    name: 'personas',
    component: PersonasView
  },
  /**
   * Ruta para la vista de familias
   * Esta usará la ruta /familias
   */
  {
    path: '/familias',
    name: 'familias',
    component: FamiliasView
  },
  /**
   * Ruta para la vista de niveles academicos
   * Esta usará la ruta /niveles-academicos 
   */
  {
    path: '/niveles-academicos',
    name: 'niveles-academicos',
    component: Niveles_Academicos
  },
  /**
   * Ruta para la vista de especialidades
   * Esta usará la ruta /especialidades
   */
  {
    path: '/especialidades',
    name: 'especialidades',
    component: EspecialidadesView 
  },
  /**
   * Ruta para la vista de asignaturas
   * Esta usará la ruta /asignaturas
   */
  {
    path: '/asignaturas',
    name: 'asignaturas',
    component: AsignaturasView
  },
  /**
   * Ruta para la vista de periodos lectivos
   * Esta usará la ruta /periodos-lectivos  
   */
  {
    path: '/periodos-lectivos',
    name: 'periodos-lectivos',
    component: Periodos_lectivosView
  },
  /**
   * Ruta para la vista de cursos
   * Esta usará la ruta /cursos
   */
  {
    path: '/cursos',
    name: 'cursos',
    component: CursosView
  },
  /**
   * Ruta para la vista de cursos asignaturas
   * Esta usará la ruta /cursos-asignaturas 
   */
  {
    path: '/cursos-asignaturas',
    name: 'cursos-asignaturas',
    component: Cursos_AsignaturasView
  },
  /**
   * Ruta para la vista de horarios clases
   * Esta usará la ruta /horarios-clases
   */
  {
    path: '/horarios-clases',
    name: 'horarios-clases',
    component: Horarios_clasesView
  },
  /**
   * Ruta para la vista de cronograma matriculas
   * Esta usará la ruta /cronograma-matriculas  
   */
  {
    path: '/cronograma-matriculas',
    name: 'cronograma-matriculas',
    component: Cronograma_MatriculasView
  },
  /**
   * Ruta para la vista de perfil 
   * Esta usará la ruta /perfil 
   */
  {
    path: '/perfil',
    name: 'perfil',
    component: PerfilView
  },
  /**
   * Ruta para la vista de matricula
   * Esta usará la ruta /matricula  
   */
  {
    path: '/matricula',
    name: 'matricula',
    component: MatriculaView
  },
  /**
   * Ruta para la vista de cursos familiar  
   * Esta usará la ruta /cursos-familiar  
   */
  {
    path: '/cursos-familiar',
    name: 'cursosfamiliar',
    component: CursosFamiliarView
  },
  /**
   * Ruta para la vista de gestión de estudiantes  
   * Esta usará la ruta /gestion-docente/mis-estudiantes  
   */
  {
    path: '/gestion-docente/mis-estudiantes',
    name: 'misestudiantes',
    component: MisestudiantesView
  },
  /**
   * Ruta para la vista de gestión de tutores  
   * Esta usará la ruta /gestion-docente/tutor/estudiantes  
   */
  {
    path: '/gestion-docente/tutor/estudiantes',
    name: 'tutorestidiantes',
    component: Tutor_estudianteView
  },
  /**
   * Ruta para la vista de historial de matricula  
   * Esta usará la ruta /matricula/historial-matricula  
   */
  {
    path: '/matricula/historial-matricula',
    name: 'historialmatricula',
    component: Historial_matriculaView
  },
  /**
   * Ruta para la vista de control de subida de notas  
   * Esta usará la ruta /control-subida-notas   
   */
  {
    path: '/control-subida-notas',
    name: 'control-subida-notas',
    component: Control_Subida_NotasView
  },
  /**
   * Ruta para la vista de registro de calificaciones  
   * Esta usará la ruta /gestion-docente/notas/registro-calificaciones  
   */
  {
    path: '/gestion-docente/notas/registro-calificaciones',
    name: 'registro-calificaciones',
    component: RegistroCalificacionesView
  },
  /**
   * Ruta para la vista de historico de notas  
   * Esta usará la ruta /gestion-administrativa/notas/historico-notas 
   */
  {
    path: '/gestion-administrativa/notas/historico-notas',
    name: 'historico-notas',
    component: HistoricoNotasView
  },
  /**
   * Ruta para la vista de reporte de asistencia  
   * Esta usará la ruta /gestion-docente/tutor/reporte-asistencia/consolidado
   */
  {
    path: '/gestion-docente/tutor/reporte-asistencia/consolidado',
    name: 'tutor-notas-estudiante',
    component: Tutor_Notas_estudianteView
  },
  /**
   * Ruta para la vista de mis notas  
   * Esta usará la ruta /notas/mis-notas    
   */
  {
    path: '/notas/mis-notas',
    name: 'mis-notas',
    component: Mis_NotasView
  },
  /**
   * 
   * Ruta para la vista de mis notas historico  
   * Esta usará la ruta /notas/mis-notas-historico
   */
  {
    path: '/notas/mis-notas-historico',
    name: 'mis-notas-historico',
    component: Mis_Notas_HistorialView
  },
  /**
   * Ruta para la vista de notas historial representado  
   * Esta usará la ruta /notas/representante/historial-notas
   */
  {
    path: '/notas/representante/historial-notas',
    name: 'notas-historial-representado',
    component: Notas_Historial_RepresentadoView
  },
  /**
   * Ruta para la vista de mis historial de matricula  
   * Esta usará la ruta /matricula/mis-historial-matricula  
   */
  {
    path: '/matricula/mis-historial-matricula',
    name: 'mis-historial-matricula',
    component: Mis_Historial_matriculaView
  },
  /**
   * Ruta para la vista de historial de matricula representado  
   * Esta usará la ruta /matricula/representante/historial-matricula
   */
  {
    path: '/matricula/representante/historial-matricula',
    name: 'historial-matricula-representado',
    component: Historial_matricula_RepreseView
  },
  /**
   * Ruta para la vista de gestión de tutores  
   * Esta usará la ruta /gestion-docente/tutor/calificar-conducta
   */
  {
    path: '/gestion-docente/tutor/calificar-conducta',
    name: 'tutor-calificar-conducta',
    component: Tutor_calificar_conductaView
  },
]
/**
 * Configuración de la ruta de la aplicación
 * Se crea un objeto router con la configuración de la ruta de la aplicación
 * Se usa createRouter para crear el objeto router
 * Se usa createWebHistory para crear el objeto history
 * Se usa process.env.BASE_URL para obtener la URL de la aplicación 
 */
const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})
/**
 * Exporta la ruta de la aplicación
 */
export default router
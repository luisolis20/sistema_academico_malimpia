import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import EstudianteView from '../views/EstudianteView.vue'
import EstudianteNew from '@/views/EstudianteNew.vue'
import Estudianteedit from '@/views/Estudianteedit.vue' // <-- Asegúrate que la E sea mayúscula
import DocenteView from '@/views/DocenteView.vue'
import Panelestudiante from '@/views/Panelestudiante.vue'
import Paneladmin from '@/views/Paneladmin.vue'
import Panelmatricula from '@/views/Panelmatricula.vue'
import CursoView from '@/views/Curso.View.vue'
import NotasView from '@/views/Notas.View.vue'
import AsignacionView from '@/views/AsignacionView.vue'
import PeriodoView from '@/views/PeriodoView.vue'
import UsuarioView from '@/views/Mantenimiento/Usuarios/UsuarioView.vue'
import RolesView from '@/views/Mantenimiento/Roles/RolesView.vue'
import PersonasView from '@/views/Mantenimiento/Personas/PersonasView.vue'
import FamiliasView from '@/views/Mantenimiento/Familias/FamiliasView.vue'
import Niveles_Academicos from '@/views/Mantenimiento/Nivel_Academico/Nivel_AcademicoView.vue'
import EspecialidadesView from '@/views/Mantenimiento/Especialidades/EspecialidadesView.vue'
import AsignaturasView from '@/views/Mantenimiento/Asignaturas/AsignaturasView.vue'
import Periodos_lectivosView from '@/views/Mantenimiento/Periodos_lectivos/Periodos_lectivosView.vue'
import CursosView from '@/views/Mantenimiento/Cursos/CursosView.vue'
import Cursos_AsignaturasView from '@/views/Mantenimiento/Cursos_Asignaturas/Cursos_AsignaturasView.vue'
import Horarios_clasesView from '@/views/Mantenimiento/Horarios_clases/Horarios_clasesView.vue'
import Cronograma_MatriculasView from '@/views/Mantenimiento/Cronograma_Matriculas/Cronograma_MatriculasView.vue'
import PerfilView from '@/views/Perfil/PerfilView.vue'
import MatriculaView from '@/views/Matricula/PanelmatriculaView.vue'
import CursosFamiliarView from '@/views/Cursos/FamiliarCursos/CursosFamiliarView.vue'
import MisestudiantesView from '@/views/Gestion_docente/estudiantes/MisestudiantesView.vue'
import Tutor_estudianteView from '@/views/Gestion_docente/tutor/Tutor_estudianteView.vue'
import Historial_matriculaView from '@/views/Matricula/Historial_Matricula/Historial_matriculaView.vue'
import Control_Subida_NotasView from '@/views/Notas/Control_Subida_Notas/Control_Subida_NotasView.vue'
import RegistroCalificacionesView from '@/views/Gestion_docente/notas/registro_calificaciones/RegistroCalificacionesView.vue'
import HistoricoNotasView from '@/views/Gestion_docente/notas/historico_notas/HistoricoNotasView.vue'
import Tutor_Notas_estudianteView from '@/views/Gestion_docente/tutor/Tutor_Notas_estudianteView.vue'
import Mis_NotasView from '@/views/Notas/Mis_Notas/Mis_NotasView.vue'
import Mis_Notas_HistorialView from '@/views/Notas/Mis_Notas/Mis_Notas_HistorialView.vue'
import Notas_Historial_RepresentadoView from '@/views/Notas/Representante/Notas_Historial_RepresentadoView.vue'
import Mis_Historial_matriculaView from '@/views/Matricula/Mis_Matriculas/Mis_Historial_matriculaView.vue'
import Historial_matricula_RepreseView from '@/views/Matricula/Representante/Historial_matricula_RepreseView.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  {
    path: '/principal',
    name: 'home',
    component: HomeView
  },
  {
    path: '/createE',
    name: 'create',
    component: EstudianteNew
  },
  {
    path: '/editE/:id',
    name: 'edit',
    component: Estudianteedit
  },
  {
    path: '/viewE/:id',
    name: 'viewE',
    component: EstudianteView
  },

  {
    path: '/panel-docente',
    name: 'docente',
    component: DocenteView
  },
  {
    path: '/panel-estudiante',
    name: 'panelestudiante',
    component: Panelestudiante
  },

  {
    path: '/panel-admin',
    name: 'paneladmin',
    component: Paneladmin
  },

  {
    path: '/panel-matricula',
    name: 'panelmatricula',
    component: Panelmatricula
  },

  {
    path: '/panel-curso',
    name: 'panelcurso',
    component: CursoView
  },

  {
    path: '/panel-notas',
    name: 'panelnotas',
    component: NotasView
  },

  {
    path: '/panel-asignacion',
    name: 'panelasignacion',
    component: AsignacionView
  },

  {
    path: '/panel-periodo',
    name: 'panelperiodo',
    component: PeriodoView
  },

  {
    path: '/panel-usuario',
    name: 'panelusuario',
    component: UsuarioView

  },
  {
    path: '/roles',
    name: 'roles',
    component: RolesView
  },
  {
    path: '/personas',
    name: 'personas',
    component: PersonasView
  },
  {
    path: '/familias',
    name: 'familias',
    component: FamiliasView
  },
  {
    path: '/niveles-academicos',
    name: 'niveles-academicos',
    component: Niveles_Academicos
  },
  {
    path: '/especialidades',
    name: 'especialidades',
    component: EspecialidadesView 
  },
  {
    path: '/asignaturas',
    name: 'asignaturas',
    component: AsignaturasView
  },
  {
    path: '/periodos-lectivos',
    name: 'periodos-lectivos',
    component: Periodos_lectivosView
  },
  {
    path: '/cursos',
    name: 'cursos',
    component: CursosView
  },
  {
    path: '/cursos-asignaturas',
    name: 'cursos-asignaturas',
    component: Cursos_AsignaturasView
  },
  {
    path: '/horarios-clases',
    name: 'horarios-clases',
    component: Horarios_clasesView
  },
  {
    path: '/cronograma-matriculas',
    name: 'cronograma-matriculas',
    component: Cronograma_MatriculasView
  },
  {
    path: '/perfil',
    name: 'perfil',
    component: PerfilView
  },
  {
    path: '/matricula',
    name: 'matricula',
    component: MatriculaView
  },
  {
    path: '/cursos-familiar',
    name: 'cursosfamiliar',
    component: CursosFamiliarView
  },
  {
    path: '/gestion-docente/mis-estudiantes',
    name: 'misestudiantes',
    component: MisestudiantesView
  },
  {
    path: '/gestion-docente/tutor/estudiantes',
    name: 'tutorestidiantes',
    component: Tutor_estudianteView
  },
  {
    path: '/matricula/historial-matricula',
    name: 'historialmatricula',
    component: Historial_matriculaView
  },
  {
    path: '/control-subida-notas',
    name: 'control-subida-notas',
    component: Control_Subida_NotasView
  },
  {
    path: '/gestion-docente/notas/registro-calificaciones',
    name: 'registro-calificaciones',
    component: RegistroCalificacionesView
  },
  {
    path: '/gestion-administrativa/notas/historico-notas',
    name: 'historico-notas',
    component: HistoricoNotasView
  },
  {
    path: '/gestion-docente/tutor/reporte-asistencia/consolidado',
    name: 'tutor-notas-estudiante',
    component: Tutor_Notas_estudianteView
  },
  {
    path: '/notas/mis-notas',
    name: 'mis-notas',
    component: Mis_NotasView
  },
  {
    path: '/notas/mis-notas-historico',
    name: 'mis-notas-historico',
    component: Mis_Notas_HistorialView
  },
  {
    path: '/notas/representante/historial-notas',
    name: 'notas-historial-representado',
    component: Notas_Historial_RepresentadoView
  },
  {
    path: '/matricula/mis-historial-matricula',
    name: 'mis-historial-matricula',
    component: Mis_Historial_matriculaView
  },
  {
    path: '/matricula/representante/historial-matricula',
    name: 'historial-matricula-representado',
    component: Historial_matricula_RepreseView
  },
]
const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
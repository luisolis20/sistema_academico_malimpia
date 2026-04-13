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


const routes = [
  {
    path: '/',          // Ruta raíz
    redirect: '/login'  // Si entran a la raíz, mándalos al login automáticamente
  },
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
]
const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
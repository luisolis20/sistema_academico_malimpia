/**
 * Este archivo es el punto de entrada principal de tu aplicación
 * Aquí se define la configuración de Vuex
 * Vuex es una librería de estado para Vue.js
 * Aquí se definen los diferentes estados y sus métodos de acción
 * Vuex se encarga de manejar el estado de la aplicación
 *
 */
import { createStore } from 'vuex'// Importa la función createStore de Vuex
/**
 * Exporta la configuración de Vuex
 * Usamos state para definir los estados que se usarán en el componente
 * Usamos getters para definir los métodos de acción que se usarán en el componente
 * Usamos mutations para definir los métodos de acción que se usarán en el componente
 * Usamos actions para definir los métodos de acción que se usarán en el componente
 * Usamos modules para definir los módulos que se usarán en el componente
 */
export default createStore({
  /**
   * State es un objeto que contiene los estados que se usarán en el componente
   */
  state: {
    role: localStorage.getItem('Rol_sitma') || null,// Obtener el rol del usuario guardado en localStorage del navegador
    correo: localStorage.getItem('correo_sitma') || null,// Obtener el correo del usuario guardado en localStorage del navegador
    idusu: localStorage.getItem('id_sitma') || null,// Obtener el id del usuario guardado en localStorage del navegador
    name: localStorage.getItem('name_sitma') || null,// Obtener el nombre del usuario guardado en localStorage del navegador
    token: localStorage.getItem('token_sitma') || null,// Obtener el token del usuario guardado en localStorage del navegador 
    token_type: localStorage.getItem('token_type_sitma') || null,// Obtener el tipo de token del usuario guardado en localStorage del navegador
  },
  /**
   * Getters son métodos que se ejecutan cuando se accede a un propiedad
   */
  getters: {
    getIdusu: state => state.idusu,// Obtener el id del usuario
    isAuthenticated: state => !!state.token,// Comprobar si el usuario está autenticado
    getFullToken: state => `${state.token_type} ${state.token}`,// Obtener el token completo
  },
  /**
   * Mutations son métodos que se ejecutan cuando se cambia un estado
   */
  mutations: {
    /**
     * setRol_sitma es un método de acción que se ejecuta cuando se cambia el rol del usuario
     * @param {*} state: estado del componente
     * @param {*} nuevoRol: nuevo rol del usuario
     */
    setRol_sitma(state, nuevoRol) {
      state.role = nuevoRol;// Asignar el nuevo rol al estado
      localStorage.setItem('Rol_sitma', nuevoRol);// Guardar el nuevo rol en localStorage del navegador
    },
    /**
     * setCorreo_sitma es un método de acción que se ejecuta cuando se cambia el correo del usuario
     * @param {*} state: estado del componente
     * @param {*} nuevocorreo: nuevo correo del usuario
     */ 
    setcorreo_sitma(state, nuevocorreo) {
      state.correo = nuevocorreo;// Asignar el nuevo correo al estado
      localStorage.setItem('correo_sitma', nuevocorreo);// Guardar el nuevo correo en localStorage del navegador
    },
    /**
     * setid_sitma es un método de acción que se ejecuta cuando se cambia el id del usuario
     * @param {*} state: estado del componente
     * @param {*} nuevoid: nuevo id del usuario
     */
    setid_sitma(state, nuevoid) {
      state.idusu = nuevoid;// Asignar el nuevo id al estado
      localStorage.setItem('id_sitma', nuevoid);// Guardar el nuevo id en localStorage del navegador
    },
    /**
     * setname_sitma es un método de acción que se ejecuta cuando se cambia el nombre del usuario
     * @param {*} state: estado del componente
     * @param {*} nuevoname: nuevo nombre del usuario
     */ 
    setname_sitma(state, nuevoname) {
      state.name = nuevoname;// Asignar el nuevo nombre al estado
      localStorage.setItem('name_sitma', nuevoname);// Guardar el nuevo nombre en localStorage del navegador
    },
    /**
     * setToken_sitma es un método de acción que se ejecuta cuando se cambia el token del usuario
     * @param {*} state: estado del componente
     * @param {*} token: nuevo token del usuario
     */
    setToken_sitma(state, token) {
      state.token = token;// Asignar el nuevo token al estado
      localStorage.setItem('token_sitma', token);// Guardar el nuevo token en localStorage del navegador
    },
    /**
     * setTokenType_sitma es un método de acción que se ejecuta cuando se cambia el tipo de token del usuario
     * @param {*} state: estado del componente
     * @param {*} type: nuevo tipo de token del usuario
     */
    setTokenType_sitma(state, type) {
      state.token_type = type;// Asignar el nuevo tipo de token al estado
      localStorage.setItem('token_type_sitma', type);// Guardar el nuevo tipo de token en localStorage del navegador
    },
    /**
     * logout_sitma es un método de acción que se ejecuta cuando se cierra la sesión del usuario
     * @param {*} state: estado del componente
     */
    logout_sitma(state) {
      // Limpia el state y localStorage al cerrar sesión
      state.role = null;
      state.correo = null;
      state.idusu = null;
      state.name = null;
      state.token = null;
      state.token_type = null;
        
      localStorage.removeItem('Rol_sitma');
      localStorage.removeItem('correo_sitma');
      localStorage.removeItem('id_sitma');
      localStorage.removeItem('name_sitma');
      localStorage.removeItem('token_sitma');
      localStorage.removeItem('token_type_sitma');
      localStorage.removeItem('user_sitma');
    },
  },
  actions: {},
  modules: {}
})

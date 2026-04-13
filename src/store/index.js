import { createStore } from 'vuex'

export default createStore({
  state: {
    role: localStorage.getItem('Rol_sitma') || null,
    correo: localStorage.getItem('correo_sitma') || null,
    idusu: localStorage.getItem('id_sitma') || null,
    name: localStorage.getItem('name_sitma') || null,
    token: localStorage.getItem('token_sitma') || null,
    token_type: localStorage.getItem('token_type_sitma') || null,
  },
  getters: {
    getIdusu: state => state.idusu,
    isAuthenticated: state => !!state.token,
    getFullToken: state => `${state.token_type} ${state.token}`,
  },
  mutations: {
    setRol_sitma(state, nuevoRol) {
      state.role = nuevoRol;
      localStorage.setItem('Rol_sitma', nuevoRol);
    },
    setcorreo_sitma(state, nuevocorreo) {
      state.correo = nuevocorreo;
      localStorage.setItem('correo_sitma', nuevocorreo);
    },
    setid_sitma(state, nuevoid) {
      state.idusu = nuevoid;
      localStorage.setItem('id_sitma', nuevoid);
    },
    setname_sitma(state, nuevoname) {
      state.name = nuevoname;
      localStorage.setItem('name_sitma', nuevoname);
    },
    setToken_sitma(state, token) {
      state.token = token;
      localStorage.setItem('token_sitma', token);
    },
    setTokenType_sitma(state, type) {
      state.token_type = type;
      localStorage.setItem('token_type_sitma', type);
    },
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

import { mostraralertas } from "@/assets/js/funciones/functions";
import { enviarsolilogin } from "@/assets/js/funciones/loginfunction";
import store from "@/store";
import { getMe } from '@/assets/js/auth';
export default {
  data() {
    return {
      correolo: "",
      clave2: "",
      url2: `${__API_SITMA__}/sistma/login`,
      loading: false,
      error: null
    };
  },
  methods: {
    async login(event) {
      event.preventDefault();
      this.loading = true;
      this.error = null;
      try {
        var parametros = {
          correo: this.correolo.trim(),
          contrasena: this.clave2.trim(),
        };

        const response = await enviarsolilogin('POST', parametros, this.url2, 'Logueado');
        //console.log("Respuesta del login:", response);
        if (response.error) {
          mostraralertas(response.mensaje, 'warning');
        } else if (response) {
          //  getMe() justo después de guardar el token
          const usuario = await getMe(); // Esto obtiene los datos del usuario autenticado desde /auth/me
          //console.log("Usuario autenticado:", usuario);

          // Redirección según el rol
          const role = response.Rol;
          const tok = response.token;
          //console.log(response.id);
          //console.log(response);
          if (role === 'Administrador') {
            mostraralertas('LE DAMOS LA BIENVENIDA ADMIN ' + (response.Nombre || ''), 'success');
            this.$router.push('/panel-admin');
          } else if (role === 'Estudiante') {
            mostraralertas('LE DAMOS LA BIENVENIDA ESTUDIANTE ' + (response.Nombre || ''), 'success');
            this.$router.push('/panel-estudiante');

          } else if (role === 'Docente') {
            mostraralertas('LE DAMOS LA BIENVENIDA DOCENTE ' + (response.Nombre || ''), 'success');
            this.$router.push('/panel-docente');
          }
          
        }
      } catch (error) {
        console.error("Error en login:", error);
         this.error = "Credenciales incorrectas. Inténtalo de nuevo.";
        if (error.response?.data?.mensaje) {
          mostraralertas(error.response.data.mensaje, 'warning');
        } else {
          mostraralertas('No se pudo conectar con el servidor o error inesperado.', 'error');
        }
      }finally {
        this.loading = false;
      }
    },
  },
};

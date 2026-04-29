import { mostraralertas } from "@/assets/js/funciones/functions";
import { enviarsolilogin } from "@/assets/js/funciones/loginfunction";
import store from "@/store";
import { getMe } from "@/assets/js/auth";
export default {
  data() {
    return {
      correolo: "",
      clave2: "",
      url2: `${__API_SITMA__}/sistma/login`,
      loading: false,
      error: null,
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

        const response = await enviarsolilogin(
          "POST",
          parametros,
          this.url2,
          "Logueado",
        );

        if (response.error) {
          mostraralertas(response.mensaje, 'warning');
        } else if (response){
          await getMe();
          const role = response.Rol;
          const tok = response.token;
          if (role === "Administrador") {
            mostraralertas(
              "BIENVENIDO ADMIN " + (response.Nombre || ""),
              "success",
            );
            this.$router.push("/principal");
          } else if (role === "Representante") {
            mostraralertas(
              "BIENVENIDO REPRESENTANTE " + (response.Nombre || ""),
              "success",
            );
            this.$router.push("/principal");
          } else if (role === "Estudiante") {
            mostraralertas(
              "BIENVENIDO ESTUDIANTE " + (response.Nombre || ""),
              "success",
            );
            this.$router.push("/principal");
          } else if (role === "Docente") {
            mostraralertas(
              "BIENVENIDO DOCENTE " + (response.Nombre || ""),
              "success",
            );
            this.$router.push("/principal");
          }
        }
        

      } catch (error) {
        console.error("Error en login:", error);
        this.error = "Credenciales incorrectas. Inténtalo de nuevo.";
        if (error.response?.data?.mensaje) {
          mostraralertas(error.response.data.mensaje, "warning");
        } else {
          mostraralertas(
            "No se pudo conectar con el servidor o error inesperado.",
            "error",
          );
        }
      } finally {
        this.loading = false;
      }
    },
  },
};

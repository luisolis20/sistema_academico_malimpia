//Funcion para el login del usuario
import axios from "axios";
import store from "@/store";
export async function enviarsolilogin(method, parametros, url, mensaje) {
  try {
    const response = await axios({
      method: method,
      url: url,
      data: parametros,
    });
    if (response.data && response.data.token) {
      store.commit("setToken_sitma", response.data.token);
      store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
      if (response.data.rol === "Administrador") {
        store.commit("setRol_sitma", response.data.rol);
        store.commit("setcorreo_sitma", response.data.correo);
        store.commit("setname_sitma", response.data.nombre + " " + response.data.apellidos);
        store.commit("setid_sitma", response.data.id_usuario);
        return {
          token: response.data.token,
          Rol: response.data.rol,
          Nombre: response.data.nombre + " " + response.data.apellidos,
          correo: response.data.correo,
          token_type: response.data.token_type,
        };
      }
      else if (response.data.rol === "Representante") {
        store.commit("setRol_sitma", response.data.rol);
        store.commit("setcorreo_sitma", response.data.correo);
        store.commit("setname_sitma", response.data.nombre + " " + response.data.apellidos);
        store.commit("setid_sitma", response.data.id_usuario);
        return {
          token: response.data.token,
          Rol: response.data.rol,
          Nombre: response.data.nombre + " " + response.data.apellidos,
          correo: response.data.correo,
          token_type: response.data.token_type,
        };
      }
      else if (response.data.Rol === "Estudiante") {
        store.commit("setRol_sitma", response.data.rol);
        store.commit("setcorreo_sitma", response.data.correo);
        store.commit("setname_sitma", response.data.nombre + " " + response.data.apellidos);
        store.commit("setid_sitma", response.data.id_usuario);
        return {
          token: response.data.token,
          Rol: response.data.rol,
          Nombre: response.data.nombre + " " + response.data.apellidos,
          correo: response.data.correo,
          token_type: response.data.token_type,
        };
      } else if (response.data.Rol === "Docente") {
        store.commit("setRol_sitma", response.data.rol);
        store.commit("setcorreo_sitma", response.data.correo);
        store.commit("setname_sitma", response.data.nombre_persona + " " + response.data.apellidos);
        store.commit("setid_sitma", response.data.id_usuario);
        return {
          token: response.data.token,
          Rol: response.data.rol,
          Nombre: response.data.nombre_persona + " " + response.data.apellidos,
          correo: response.data.correo,
          token_type: response.data.token_type,
        };
      }
    } else {
      console.error("Respuesta inesperada:", response);
      return {
        error: response.data.mensaje,
        clave: response.data.clave,
        mensaje: response.data.mensaje,
      };

    }

  } catch (error) {
    console.error("Error:", error.response.data);
    throw error;
  }
}

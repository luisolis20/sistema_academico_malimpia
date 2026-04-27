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
    // Si la API devuelve un campo "error" explícito aunque el status sea 200
    if (response.data && response.data.error) {
      return {
        error: true,
        mensaje: response.data.mensaje || "Error en las credenciales",
      };
    }
    if (response.data && response.data.token) {
      if (response.data.rol === "Administrador") {
        store.commit("setRol_sitma", response.data.rol);
        store.commit("setcorreo_sitma", response.data.correo);
        store.commit("setname_sitma", response.data.nombre + " " + response.data.apellidos);
        store.commit("setid_sitma", response.data.id_usuario);
        store.commit("setToken_sitma", response.data.token);
        store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
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
        store.commit("setToken_sitma", response.data.token);
        store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
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
        store.commit("setToken_sitma", response.data.token);
        store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
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
        store.commit("setToken_sitma", response.data.token);
        store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
        return {
          token: response.data.token,
          Rol: response.data.rol,
          Nombre: response.data.nombre_persona + " " + response.data.apellidos,
          correo: response.data.correo,
          token_type: response.data.token_type,
        };
      }
    }
    return { error: true, mensaje: "Credenciales incorrectas" };
  } catch (error) {
    console.error("Error:", error.response.data);
    throw error;
  }
}

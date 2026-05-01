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
    if (response.status === 200) {
      store.commit("setToken_sitma", response.data.token);
      store.commit("setTokenType_sitma", response.data.token_type || "Bearer");
      store.commit("setRol_sitma", response.data.rol);
      store.commit("setcorreo_sitma", response.data.correo);
      store.commit(
        "setname_sitma",
        response.data.nombre + " " + response.data.apellidos,
      );
      store.commit("setid_sitma", response.data.id_persona);
      return {
        token: response.data.token,
        Rol: response.data.rol,
        Nombre: response.data.nombre + " " + response.data.apellidos,
        correo: response.data.correo,
        token_type: response.data.token_type,
      };
    } 
    else if (response.error){
      return {
        error: response.error,
        mensaje: response.data.mensaje,
      };
    }
  } catch (error) {
    console.error("Error:", error.response.data);
    throw error;
  }
}

/**
 * Este archivo custom.js es un archivo de configuración personalizado para tu aplicación
 * Aquí se definen las propiedades y los métodos que se usarán en el componente
 * 
 */
import store from "@/store";// Importa de store para acceder a los estados y métodos de acción
/**
 * Exporta la configuración personalizada
 * Usamos computed para definir las propiedades que se usarán en el componente
 */
export default {
    computed: {
        /**
         * Metodo que devuelve el rol del usuario
         * @returns 
         */
        rolUsuario() {
            return store.state.role;// Retorna el rol del usuario
        },
        /**
         * Metodo que devuelve el email del usuario
         * @returns 
         */
        emailUsuario() {
            return store.state.correo;// Retorna el email del usuario
        },
        /**
         * Metodo que devuelve el id del usuario
         * @returns 
         */
        idUsuario() {
            return store.state.idusu;//Retorna el id del usuario
        },
        /**
         * Metodo que devuelve el nombre del usuario
         * @returns 
         */
        nombreUsuario() {
            return store.state.name;// Retorna el nombre del usuario
        },
        /**
         * Metodo para mostrar navbar en base al rol del usuario, en este caso solo mostrará el navbar si el rol es Administrador
         * @returns 
         */
        showNavbarAmin() {
            var rut;// Rut es una variable que almacena el nombre de la ruta actual
            rut = this.$route.name;// Obtener el nombre de la ruta actual
            return (this.rolUsuario === "Administrador"// Si el rol es Administrador
            );
        },
        /**
         * Metodo para mostrar navbar en base al rol del usuario, en este caso solo mostrará el navbar si el rol es Secretaria
         * @returns 
         */
        showNavbarDocente() {
            var rut;// Rut es una variable que almacena el nombre de la ruta actual
            rut = this.$route.name;// Obtener el nombre de la ruta actual
            return (this.rolUsuario === "Docente"// Si el rol es Docente
            );
        },
        /**
         * Metodo para mostrar navbar en base al rol del usuario, en este caso solo mostrará el navbar si el rol es Estudiante
         * @returns 
         */
        showNavbarEstudiante() {
            var rut;// Rut es una variable que almacena el nombre de la ruta actual
            rut = this.$route.name;// Obtener el nombre de la ruta actual
            return (this.rolUsuario === "Estudiante"// Si el rol es Estudiante
            );
        }

    }
}
import store from "@/store";
export default {
    computed: {
        rolUsuario() {
            //console.log(store);
            return store.state.role;
        },
        emailUsuario() {
            //console.log(store);
            return store.state.correo;
        },
        idUsuario() {
            //console.log(store);
            return store.state.idusu;
        },
        nombreUsuario() {
            //console.log(store);
            return store.state.name;
        },
        showNavbarAmin() {

            var rut;
            rut = this.$route.name;
            return (this.rolUsuario === "Administrador"
            );
        },
        showNavbarDocente() {

            var rut;
            rut = this.$route.name;
            return (this.rolUsuario === "Docente"
            );
        },
        showNavbarEstudiante() {

            var rut;
            rut = this.$route.name;
            return (this.rolUsuario === "Estudiante"
            );
        }

    }
}
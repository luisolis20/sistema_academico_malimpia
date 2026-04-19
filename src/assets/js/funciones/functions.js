import Swal from 'sweetalert2';
import API from '@/assets/js/axios';

export function mostraralertas(titulo,icono,foco=''){
    if(foco!=''){
        document.getElementById(foco).focus();
    }
    Swal.fire({
        title:titulo,
        icon:icono,
        customClass:{confirmButton:'btn btn-secondary', popup:'animated zoonIn'},
        buttonsStyling:false
    });
}
export function mostraralertas2(titulo,icono){
    
    Swal.fire({
        title:titulo,
        icon:icono,
        customClass:{confirmButton:'btn btn-secondary', popup:'animated zoonIn'},
        buttonsStyling:false
    });
}
export function confimardesasignar(urlconslash, id, titulo, mensaje, actualizarTabla) {
    var url = urlconslash + id;   // 👈 Se construye la URL con el ID

    const swalwithboostrapbutton = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success me-3',
            cancelButton: 'btn btn-danger'
        },
    });

    return swalwithboostrapbutton.fire({
        title: titulo,
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Desasignar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then((res) => {
        if (res.isConfirmed) {
            return API.delete(url)   // 👈 Ya NO mandamos { data: { id } }
                .then((response) => {
                    mostraralertas(response.data.mensaje ?? 'Desasignado con éxito', 'success');
                    if (typeof objetoListFiltrados === "function") {
                        objetoListFiltrados(); // 🔄 refrescar tabla
                    }
                    return response.data;
                })
                .catch(() => {
                    mostraralertas('Error al desasignar', 'error');
                    throw new Error('Error al desasignar');
                });
        } else {
            mostraralertas('Operación cancelada', 'info');
            return null;
        }
    });
}
export function confimarhabi(urlconslash, id, titulo, mensaje, actualizarTabla) {
    var url = urlconslash + id;   // 👈 Se construye la URL con el ID

    const swalwithboostrapbutton = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success me-3',
            cancelButton: 'btn btn-danger'
        },
    });

    return swalwithboostrapbutton.fire({
        title: titulo,
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Habilitar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then((res) => {
        if (res.isConfirmed) {
            return API.delete(url)   // 👈 Ya NO mandamos { data: { id } }
                .then((response) => {
                    mostraralertas(response.data.mensaje ?? 'Habilitado con éxito', 'success');
                    if (typeof objetoListFiltrados === "function") {
                        objetoListFiltrados(); // 🔄 refrescar tabla
                    }
                    return response.data;
                })
                .catch(() => {
                    mostraralertas('Error al habilitar', 'error');
                    throw new Error('Error al habilitar');
                });
        } else {
            mostraralertas('Operación cancelada', 'info');
            return null;
        }
    });
}
export function confimar(urlconslash,id,titulo,mensaje){
    var url = urlconslash + id;   // 👈 Se construye la URL con el ID

    const swalwithboostrapbutton = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success me-3',
            cancelButton: 'btn btn-danger'
        },
    });

    return swalwithboostrapbutton.fire({
        title: titulo,
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inhabilitar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then((res) => {
        if (res.isConfirmed) {
            return API.delete(url)   //  Ya NO mandamos { data: { id } }
                .then((response) => {
                    mostraralertas(response.data.mensaje ?? 'Inhabilitado con éxito', 'success');
                    if (typeof objetoListFiltrados === "function") {
                        objetoListFiltrados(); // 🔄 refrescar tabla
                    }
                    return response.data;
                })
                .catch(() => {
                    mostraralertas('Error al eliminar', 'error');
                    throw new Error('Error al eliminar');
                });
        } else {
            mostraralertas('Operación cancelada', 'info');
            return null;
        }
    });
   
}
export function confimarreseteo(urlconslash, id, cedula, titulo, mensaje) {
    var url = urlconslash + id; // Se construye la URL con el ID
    
    const swalwithboostrapbutton = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success me-3',
            cancelButton: 'btn btn-danger'
        },
    });

    return swalwithboostrapbutton.fire({
        title: titulo,
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Resetear Clave',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then((res) => {
        if (res.isConfirmed) {
            // 👇 AQUÍ EL CAMBIO: Enviamos { nueva_clave: cedula }
            return API.post(url, { nueva_clave: cedula })
                .then((response) => {
                    mostraralertas2(response.data.mensaje ?? 'Clave reseteada con éxito', 'success');
                    return true; // Retornamos true para saber que se completó
                })
                .catch(() => {
                    mostraralertas2('Error al resetear la clave', 'error');
                    throw new Error('Error al resetear la clave');
                });
        } else {
            mostraralertas2('Operación cancelada', 'info');
            return false; // Retornamos false si canceló
        }
    });
}

export function confimar2(urlconslash,id,titulo,mensaje){
    var url = urlconslash+id;
    const swalwithboostrapbutton = Swal.mixin({
        customClass:{confirmButton:'btn btn-success me-3',cancelButton:'btn btn-danger'},
    });
    swalwithboostrapbutton.fire({
        title:titulo,
        text:mensaje,
        icon:'question',
        showCancelButton:true,
        confirmButtonText:'<i class="fa-solid fa-check"></i> Si, Eliminar',
        cancelButtonText:'<i class="fa-solid fa-ban"></i> Cancelar'}).then((res)=>{
        if(res.isConfirmed){
            enviarsolig('PUT',{id:id},url,'Deshabilitado con éxito');
        }else{
            mostraralertas('Operacion cancelada','info');
        }
    });
   
}
export function enviarsoli(metodo,parametros,url,mensaje){
    API({
        method:metodo,
        url:url,
        data:parametros
    }).then(function(res){
        var estado = res.status;
        if(estado==200){
            mostraralertas(mensaje,'success');
            document.getElementById('IniciarSesion').scrollIntoView({ behavior: 'smooth' });
            /*window.setTimeout(function(){
                window.location.href='/'
            },2000);*/
        }else{
            mostraralertas('No se pudo recuperar la respuesta','error');
        }
    }).catch(function(error){
        mostraralertas('Error Al registrar','error');
    });
}
export function enviarsolig(metodo,parametros,url,mensaje){
    return API({
        method:metodo,
        url:url,
        data:parametros
    }).then(function(res){
        var estado = res.status;
        if(estado==200){
            mostraralertas(mensaje,'success');
            return res;   
        }else{
            mostraralertas('No se pudo recuperar la respuesta','error');

        }
    }).catch(function(error){
        if(error.response.status===409){
            mostraralertas(error.response.data.mensaje,'warning');
            
        }else{
            mostraralertas('Servidor no Disponible', 'error');
        } 
    });
}
export function enviarsoligfoot(metodo,parametros,url,mensaje){
    return API({
        method:metodo,
        url:url,
        data:parametros
    }).then(function(res){
        var estado = res.status;
        if(estado==200){
            mostraralertas(mensaje,'success');
            return res;   
        }else{
            mostraralertas('No se pudo recuperar la respuesta','error');

        }
    }).catch(function(error){
        console.log(error);
        mostraralertas('Servidor no Disponible','error');
    });
}
export function enviarsoligqr(metodo,parametros,url){
    return API({
        method:metodo,
        url:url,
        data:parametros
    }).then(function(res){
        var estado = res.status;
        if(estado==200){
            return res;   
        }else{
            console.log('No se pudo recuperar la respuesta','error');

        }
    }).catch(function(error){
        console.log(error);
    });
}
export async function enviarsoliedit(metodo,parametros,url,mensaje){
    try {
        var response = await API({
        method: metodo,
        url: url,
        data: parametros
      });
  
      
      if (response.data) {
        //console.log(mensaje + ': ' + response.data.mensaje);
        mostraralertas(mensaje,'success');
        
        
        
      } else{
        mostraralertas('No se pudo recuperar la respuesta','error');
        return null;
        }
    } catch (error) {
      console.error('Error:', error.response.data);
      mostraralertas('Servidor no Disponible','error');
      throw error;
    }
}
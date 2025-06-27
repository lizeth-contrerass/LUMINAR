document.addEventListener('DOMContentLoaded', function() {
    const form = document.forms['contraCambio'];
    
    function validarFortalezaContraseña(contraseña) {
        const errores = [];
        
        if (contraseña.length < 8) {
            errores.push('Mínimo 8 caracteres');
        }
        if (!/[A-Z]/.test(contraseña)) {
            errores.push('Al menos una mayúscula');
        }
        if (!/[a-z]/.test(contraseña)) {
            errores.push('Al menos una minúscula');
        }
        if (!/[0-9]/.test(contraseña)) {
            errores.push('Al menos un número');
        }
        if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(contraseña)) {
            errores.push('Al menos un carácter especial');
        }
        
        return errores;
    }
    
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const contraseniaN = document.getElementById('contraseniaN').value;
        const contraN = document.getElementById('contraN').value;
        
        if(contraseniaN !== contraN) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas nuevas no coinciden',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        const errores = validarFortalezaContraseña(contraseniaN);
        
        if(errores.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Contraseña no válida',
                html: `La contraseña debe contener:<br><ul>${
                    errores.map(error => `<li>${error}</li>`).join('')
                }</ul>`,
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        form.submit();
    });
    

});
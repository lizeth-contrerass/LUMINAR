//Valida que la contraseña cumpla todos los requerimientos

const formulario = document.getElementById('formulario');
const inputContrasena = document.getElementById('contrasena');

let mensaje = document.createElement('p');
mensaje.textContent = 'La contraseña no cumple todos los requerimientos.';
mensaje.style.color = 'red';
mensaje.style.display = 'none';
mensaje.style.marginTop = '0.2rem';
mensaje.style.marginBottom = '0.3rem';

inputContrasena.insertAdjacentElement('afterend', mensaje);

function validarContrasena(password) {
    const tieneLongitud = password.length >= 8;
    const tieneMayuscula = /[A-Z]/.test(password);
    const tieneMinuscula = /[a-z]/.test(password);
    const tieneNumero = /[0-9]/.test(password);
    const tieneEspecial = /[^A-Za-z0-9]/.test(password);
    return tieneLongitud && tieneMayuscula && tieneMinuscula && tieneNumero && tieneEspecial;
}

formulario.addEventListener('submit', function (e) {
    const password = inputContrasena.value;
    if (!validarContrasena(password)) {
        e.preventDefault();
        inputContrasena.style.borderColor = 'red';
        inputContrasena.style.outlineColor = '#FF8A8A';
        mensaje.style.display = 'block';
    } else {
        mensaje.style.display = 'none';
    }
});


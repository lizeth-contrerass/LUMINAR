<?php
session_start();

// Leer y limpiar los mensajes de sesión
$mensajeError = $_SESSION['registro_error'] ?? '';
$mensajeExito = $_SESSION['registro_exito'] ?? '';
unset($_SESSION['registro_error'], $_SESSION['registro_exito']);
?>

<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Registro como candidato</title>
        <link rel="stylesheet" href="../estilo/registro-inicio.css" type="text/css">
        <link rel="icon" href="../imagenes/icono.png" type="image/png">
    </head>

    <body>

        <header>
            <a href="../index.html"><img src="../imagenes/logo.png" alt="Logo de Luminar"></a>
        </header>

        <main>
            <h1>Regístrate</h1>
            <h2>Crea tu cuenta como candidato</h2>

            <form id="formulario" action="../includes/registrarCandidato.php" method="POST">
                <p>Nombre(s)*</p>
                <input type="text" name="nombre" required>

                <p>Apellidos*</p>
                <input type="text" name="apellidos" required>
                
                <p>Correo electrónico*</p>
                <input type="email" name="correo" required>
                
                <p>Contraseña</p>
                <input id="contrasena" type="password" name="contrasena" required>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula</li>
                    <li>Al menos una letra minúscula</li>
                    <li>Al menos un número</li>
                    <li>Al menos un carácter especial</li>
                </ul>
                
                <button type="submit">Registrarse</button>
            </form>

            <script src="../includes/validarContrasena.js"></script>
        </main>

        <footer>
            <p>¿Ya tienes cuenta? <a href="../loginCandidato.php">Inicia sesión</a></p>
            <p>¿Buscas talento? <a href="./registroEmpresa.php">Regístrate como reclutador</a></p>
        </footer>
        
    </body>

</html>
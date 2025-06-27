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
        <title>Registro</title>
        <link rel="stylesheet" href="../estilo/registroCandidato.css" type="text/css">
    </head>

    <body>

        <header>
            <img src="../imagenes/logo-sin-fondo.png" alt="Logo de Luminar">
        </header>

        <main>
            <?php if ($mensajeError): ?>
                <p style="color:red;"><?php echo htmlspecialchars($mensajeError); ?></p>
            <?php endif; ?>

            <?php if ($mensajeExito): ?>
                <p style="color:green;"><?php echo htmlspecialchars($mensajeExito); ?></p>
            <?php endif; ?>

            <h1>Regístrate</h1>

            <h2>Crea tu cuenta como candidato</h2>

            <form action="../includes/registrarCandidato.php" method="POST">
                <p>Nombre(s)*</p>
                <input type="text" name="nombre" required><br>

                <p>Apellidos*</p>
                <input type="text" name="apellidos" required><br>
                
                <p>Correo electrónico*</p>
                <input type="email" name="correo" required><br>
                
                <p>Contraseña</p>
                <input type="password" name="contrasena" required><br>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula</li>
                    <li>Al menos una letra minúscula</li>
                    <li>Al menos un número</li>
                    <li>Al menos un carácter especial</li>
                </ul>
                
                <button type="submit">Registrarse</button>
            </form>
        </main>

        <footer>
            <p>¿Ya tienes cuenta? <a href="">Inicia sesión</a></p>
            <p>¿Buscas talento? <a href="">Regístrate como reclutador</a></p>
        </footer>
        
    </body>

</html>
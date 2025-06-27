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
        <title>Registro como reclutador</title>
        <link rel="stylesheet" href="../estilo/registro-inicio.css" type="text/css">
    </head>

    <body>

        <header>
            <a href="../"><img src="../imagenes/logo.png" alt="Logo de Luminar"></a>
        </header>

        <main>
            <?php if ($mensajeError): ?>
                <p style="color:red;"><?php echo htmlspecialchars($mensajeError); ?></p>
            <?php endif; ?>

            <?php if ($mensajeExito): ?>
                <p style="color:green;"><?php echo htmlspecialchars($mensajeExito); ?></p>
            <?php endif; ?>

            <h1>Regístrate</h1>

            <h2>Crea tu cuenta como reclutador</h2>

            <form action="../includes/registrarCandidato.php" method="POST">
                <p>Nombre comercial de la empresa*</p>
                <input type="text" name="nombreem" required><br>

                <p>Razón social*</p>
                <input type="text" name="razon" required><br>
                
                <p>RFC*</p>
                <input type="text" name="rfc" required><br>

                <p>Nombre(s)*</p>
                <input type="text" name="nombreper" required><br>

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
            <p>¿Ya tienes cuenta? <a href="../loginCandidato.php">Inicia sesión</a></p>
            <p>¿Buscas trabajo? <a href="./registroCandidato.php">Regístrate como candidato</a></p>
        </footer>
        
    </body>

</html>
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
    <title>Registro de Candidato</title>
</head>
<body>
<h2>Registro de Candidato</h2>

<?php if ($mensajeError): ?>
    <p style="color:red;"><?php echo htmlspecialchars($mensajeError); ?></p>
<?php endif; ?>

<?php if ($mensajeExito): ?>
    <p style="color:green;"><?php echo htmlspecialchars($mensajeExito); ?></p>
<?php endif; ?>

<form action="../includes/registrarCandidato.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="text" name="apellidos" placeholder="Apellidos" required><br>
    <input type="email" name="correo" placeholder="Correo" required><br>
    <input type="password" name="contrasena" placeholder="Contraseña" required><br>
    <button type="submit">Registrarse</button>
</form>
</body>
</html>
<?php
// Mostrar errores (para desarrollo)


// Validar sesión
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../loginCandidato.php");
    exit;
}

// Incluir clase User y reconstruir usuario
require_once './includes/loginCandidato.php'; // Asegúrate de que la ruta sea correcta

$user = new User();
$user->setUser($_SESSION['user']); // $_SESSION['user'] guarda el correo del usuario
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./estilo/homeCandidato.css">
    <link rel="icon" href="../imagenes/icono.png" type="image/png">
    <title>Candidato</title>
</head>
<body>
<header>
    <iframe class="header" src="./bases/Nav-Inicial.html"></iframe>
</header>

<main>
    <p>Bienvenido <?php echo htmlspecialchars($user->getNombre()); ?></p>
    <p>Correo: <?php echo htmlspecialchars($user->getCorreo()); ?></p>

    <button type="button" onclick="location.href='./includes/logout.php'">Cerrar Sesión</button>
</main>

<footer>
    <iframe class="footer" src="./bases/Nav-Final.html"></iframe>
</footer>
</body>
</html>

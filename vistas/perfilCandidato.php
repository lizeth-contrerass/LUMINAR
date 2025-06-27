<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../estilo/perfilCandidato.css" type="text/css">
    <?php
        include 'perfilC.php';
    ?>
</head>
<body>
    <div class="arriba">
        <iframe class="header" src="../bases/nav-I-User.html"></iframe>
    </div>

    <div class="contenedor">
        <h1>Perfil de Candidato</h1>
        <p>
            <strong>Nombre:</strong> <?= htmlspecialchars($usuario['NOMBRE']) ?>
        </p>
        <p>
            <strong>Apellidos:</strong> <?= htmlspecialchars($usuario['APELLIDOS']) ?>
        </p>
        <p>
            <strong>Correo:</strong> <?= htmlspecialchars($usuario['CORREO']) ?>
        </p>
        <p>
            <strong>Contraseña:</strong> <?= $asteriscos ?>
        </p>

        <div class="botones">
            <a href="./modificaContrasenia.html">Cambiar contraseña</a>
        </div>
    </div>

    <div class="abajo">
        <iframe class="footer" src="../bases/nav-F-User.html"></iframe>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Empresa</title>
    <link rel="stylesheet" href="../estilo/perfilCandidato.css" type="text/css">
    <link rel="icon" href="../imagenes/icono.png" type="image/png">
    <?php
        include 'perfilE.php';
    ?>
</head>
<body>
    <div class="arriba">
        <iframe class="header" src="../bases/nav-I-Empresa.html"></iframe>
    </div>

    <div class="contenedor">
        <h1>Perfil de Empresa</h1>

        <p><strong>Nombre de la empresa:</strong> <?= htmlspecialchars($usuario['NOMBRE_EMPRESA']) ?></p>
        <p><strong>Razón social:</strong> <?= htmlspecialchars($usuario['RAZON_SOCIAL']) ?></p>
        <p><strong>RFC:</strong> <?= htmlspecialchars($usuario['RFC']) ?></p>

        <h2>Datos del reclutador</h2>
        <p><strong>Nombre completo:</strong> <?= htmlspecialchars($usuario['NOMBRE'] . ' ' . $usuario['APELLIDOS']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['CORREO']) ?></p>
        <p><strong>Contraseña:</strong> <?= $asteriscos ?></p>

        <div class="botones">
            <a href="./modificaContrasenia.html">Cambiar contraseña</a>
        </div>
    </div>

    <div class="abajo">
        <iframe class="footer" src="../bases/nav-F-User.html"></iframe>
    </div>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../imagenes/Logo.jpg" type="image/x-icon" >
    <link rel="stylesheet" href="./estilo/inicioUSR.css">
    <title>Iniciar Sesión</title>
</head>
<body>
<header>
    <iframe class="header" src="./bases/Nav-Inicial.html"></iframe>
</header>
<div class="contenedor">
<h1>Inicio de sesión</h1>
<form method="POST" action="./loginCandidato.php">

    <label>
        <input type="text" name="usuario" placeholder="Usuario" required>
    </label>
    <label>
        <input type="password" name="contrasena" placeholder="Contrasena" required>
    </label>
    <label>
        <input type="submit" value="Ingresar">
    </label>

</form>
    <?php
    if(isset($errorLogin)){
        echo $errorLogin;
    }
    ?>
</div>
<footer>
    <iframe class="footer" src="./bases/Nav-Final.html"></iframe>
</footer>
</body>
</html>

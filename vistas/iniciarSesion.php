<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Iniciar sesión</title>
        <link rel="stylesheet" href="./estilo/registro-inicio.css" type="text/css">
    </head>
    
    <body>

        <header>
            <a href="./"><img src="./imagenes/logo.png" alt="Logo de Luminar"></a>
        </header>

        <main>
        
            <h1>Inicia sesión</h1>

            <form method="POST" action="./loginCandidato.php">
                <p>Correo electrónico</p>  
                <input type="text" name="usuario" required>
            
                <p>Contraseña</p>
                <input type="password" name="contrasena" required>
                
                <button type="submit">Iniciar sesión</button>
            </form>

            <?php
            if(isset($errorLogin)){
                echo $errorLogin;
            }
            ?>

        </main>

        <footer>
            <p>¿No tienes cuenta?</p>
            <p>Si buscas empleo regístrate <a href="./vistas/registroCandidato.php">aquí</a>.</p>
            <p>Si buscas talento regístrate <a href="./vistas/registroEmpresa.php">aquí</a>.</p>
        </footer>

    </body>

</html>
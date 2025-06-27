<?php
session_start();
require_once '../includes/conexion.php';

$db = new Database();
$conn = $db->connect();

$id_usuario = $_SESSION['id_usuario'] ?? null;
$correo_actual = '';

if ($id_usuario) {
    $stmt = $conn->prepare("SELECT CORREO FROM CANDIDATO WHERE ID = :id");
    $stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $correo_actual = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificación de correo</title>
    <link rel="stylesheet" href="../estilo/modifica.css" type="text/css">
</head>
<body>
    <div class="arriba">
        <iframe class="header" src="../bases/nav-I-User.html"></iframe>
    </div>

    <div class="contenedor">
        <h1>Modificación de correo electrónico</h1>

        <form name="correoCambio" action="../includes/cambioCorreo.php" method="post" enctype="application/x-www-form-urlencoded">
            <div class="inputs">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required value="<?= htmlspecialchars($correo_actual) ?>">
            </div> 
            <div class="inputs">
                <label for="contrasenia">Ingresa tu contraseña para realizar el cambio:</label>
                <input type="password" id="contrasenia" name="contrasenia" required>
            </div>
            <input type="submit" name="modificar" value="Modificar correo">
        </form>
    </div>

    <div class="abajo">
        <iframe class="footer" src="../bases/nav-F-User.html"></iframe>
    </div>
</body>
</html>

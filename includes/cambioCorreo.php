<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Acceso denegado. Inicia sesión.");
}

$correo = $_POST['correo'] ?? '';
$contrasenia = $_POST['contrasenia'] ?? '';

// Validación básica
if (empty($correo) || empty($contrasenia)) {
    die("Faltan campos obligatorios.");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    mostrarAlerta("Correo no válido", "El correo ingresado no tiene un formato válido.", "error", "../vistas/editorCV.php");
    exit;
}

try {
    // Obtener contraseña actual del usuario
    $stmt = $conn->prepare("SELECT CONTRASENA FROM CANDIDATO WHERE ID = :id_usuario");
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($contrasenia, $hash)) {
        mostrarAlerta("Contraseña incorrecta", "La contraseña ingresada no es correcta.", "error", "../vistas/editorCV.php");
        exit;
    }

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM CANDIDATO WHERE CORREO = :correo AND ID != :id_usuario");
    $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $existe = $stmt->fetchColumn();

    if ($existe > 0) {
        mostrarAlerta("Correo en uso", "Ya existe un usuario con ese correo electrónico.", "error", "../vistas/editorCV.php");
        exit;
    }

    // Actualizar correo
    $stmt = $conn->prepare("UPDATE CANDIDATO SET CORREO = :correo WHERE ID = :id_usuario");
    $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    mostrarAlerta("Correo actualizado", "Tu correo ha sido modificado correctamente.", "success", "../vistas/editorCV.php");
    exit;

} catch (PDOException $e) {
    mostrarAlerta("Error", "No se pudo actualizar el correo: " . $e->getMessage(), "error", "../vistas/editorCV.php");
    exit;
}

// Función de alerta con SweetAlert2
function mostrarAlerta($titulo, $mensaje, $icono, $redirigirA) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <script src="sweetalert2.all.min.js"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: '{$icono}',
        title: '{$titulo}',
        text: '{$mensaje}',
        confirmButtonText: 'Aceptar'
    }).then(() => {
        window.location.href = '{$redirigirA}';
    });
</script>
</body>
</html>
HTML;
}
?>

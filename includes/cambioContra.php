<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

// Obtener correo desde la sesión
$correo = $_SESSION['user'] ?? '';
if (!$correo) {
    die("Acceso denegado. Inicia sesión.");
}

// Obtener datos del formulario
$actual = $_POST['contraseniaA'] ?? '';
$nueva = $_POST['contraseniaN'] ?? '';
$confirmacion = $_POST['contraN'] ?? '';

// Validaciones
if (empty($actual) || empty($nueva) || empty($confirmacion)) {
    die("Faltan campos obligatorios.");
}

if ($nueva !== $confirmacion) {
    die("Las contraseñas no coinciden.");
}

try {
    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT CONTRA FROM USUARIO WHERE CORREO = :correo");
    $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
    $stmt->execute();
    $hashActual = $stmt->fetchColumn();

    if (!$hashActual || !password_verify($actual, $hashActual)) {
        mostrarAlerta("Error", "La contraseña actual es incorrecta.", "error", "../vistas/modificaContrasenia.html");
        exit;
    }

    // Generar nuevo hash y actualizar
    $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE USUARIO SET CONTRA = :nueva WHERE CORREO = :correo");
    $stmt->bindValue(':nueva', $nuevoHash, PDO::PARAM_STR);
    $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
    $stmt->execute();

    mostrarAlerta("¡Contraseña actualizada!", "Tu contraseña ha sido cambiada correctamente.", "success", "../includes/logout.php");
    exit;

} catch (PDOException $e) {
    mostrarAlerta("Error", "No se pudo actualizar la contraseña: " . $e->getMessage(), "error", "../vistas/perfilCandidato.php");
    exit;
}

// Función para mostrar SweetAlert2
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


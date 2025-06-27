<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Acceso denegado. Inicia sesión.");
}

// Obtener datos del formulario
$actual = $_POST['contraseniaA'] ?? '';
$nueva = $_POST['contraseniaN'] ?? '';
$confirmacion = $_POST['contraN'] ?? '';

// Validaciones básicas
if (empty($actual) || empty($nueva) || empty($confirmacion)) {
    die("Faltan campos obligatorios.");
}

if ($nueva !== $confirmacion) {
    die("Las contraseñas no coinciden.");
}

try {
    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT CONTRASENA FROM CANDIDATO WHERE ID = :id_usuario");
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $hashActual = $stmt->fetchColumn();

    if (!$hashActual || !password_verify($actual, $hashActual)) {
        mostrarAlerta("Error", "La contraseña actual es incorrecta.", "error", "../vistas/editorCV.php");
        exit;
    }

    // Generar nuevo hash y actualizar
    $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE CANDIDATO SET CONTRASENA = :nueva WHERE ID = :id_usuario");
    $stmt->bindValue(':nueva', $nuevoHash, PDO::PARAM_STR);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    mostrarAlerta("¡Contraseña actualizada!", "Tu contraseña ha sido cambiada correctamente.", "success", "../vistas/editorCV.php");
    exit;

} catch (PDOException $e) {
    mostrarAlerta("Error", "No se pudo actualizar la contraseña: " . $e->getMessage(), "error", "../vistas/editorCV.php");
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
?>

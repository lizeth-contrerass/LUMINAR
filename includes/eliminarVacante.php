<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

function mostrarAlerta($titulo, $mensaje, $icono, $redirigirA) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


$correo = $_SESSION['user'] ?? null;
$id_vacante = $_POST['id_vacante'] ?? null;

if (!$correo || !$id_vacante) {
    mostrarAlerta("Error", "Acceso inválido o vacante no especificada.", "error", "../vistas/VacantesEmpresa.php");
    exit;
}

// Verificar que la vacante le pertenece al reclutador actual
$stmt = $conn->prepare("
    SELECT V.ID 
    FROM VACANTE V
    JOIN USUARIO U ON V.ID_RECLUTADOR = U.ID
    WHERE U.CORREO = :correo AND V.ID = :id_vacante
");
$stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
$stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    mostrarAlerta("Error", "No tienes permiso para eliminar esta vacante.", "error", "../vistas/VacantesEmpresa.php");
    exit;
}

try {
    $conn->beginTransaction();

    $conn->prepare("DELETE FROM VACANTE_TITULO WHERE ID_VACANTE = :id")
        ->execute([':id' => $id_vacante]);

    $conn->prepare("DELETE FROM VACANTE_HABILIDAD WHERE ID_VACANTE = :id")
        ->execute([':id' => $id_vacante]);

    $conn->prepare("DELETE FROM VACANTE WHERE ID = :id")
        ->execute([':id' => $id_vacante]);

    $conn->commit();

    mostrarAlerta("¡Eliminada!", "La vacante fue eliminada correctamente.", "success", "../vistas/VacantesEmpresa.php");

} catch (PDOException $e) {
    $conn->rollBack();
    mostrarAlerta("Error", "No se pudo eliminar la vacante: " . $e->getMessage(), "error", "../vistas/VacantesEmpresa.php");
    exit;
}



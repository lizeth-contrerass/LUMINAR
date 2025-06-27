<?php
session_start();
require_once 'conexion.php';

$correo = $_SESSION['user'] ?? '';
if (!$correo) {
    die("No hay sesión activa. Inicia sesión como reclutador.");
}

$db = new Database();
$conn = $db->connect();

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

try {
    // Buscar ID del reclutador
    $stmt = $conn->prepare("SELECT ID FROM USUARIO WHERE CORREO = :correo AND TIPO_USUARIO = 2");
    $stmt->bindValue(':correo', $correo);
    $stmt->execute();
    $id_reclutador = $stmt->fetchColumn();

    if (!$id_reclutador) {
        mostrarAlerta("Error", "Usuario no válido o no es reclutador.", "error", "../vistas/PerfEmpresa.html");
        exit;
    }

    // Obtener datos del formulario
    $puesto = $_POST['puesto'] ?? '';
    $salario = $_POST['salario'] ?? '';
    $modalidad = $_POST['modalidad'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $duracion = $_POST['duracion'] ?? '';
    $escolaridad = $_POST['escolaridad'] ?? '';
    $habilidades = $_POST['habilidades'] ?? [];
    $titulos = $_POST['titulos'] ?? [];

    if (!$puesto || !$salario || !$modalidad || !$ubicacion || !$duracion || !$escolaridad || !$titulos) {
        mostrarAlerta("Error", "Faltan campos obligatorios.", "error", "../vistas/registroVacante.php");
        exit;
    }

    $conn->beginTransaction();

    // Insertar vacante
    $stmt = $conn->prepare("INSERT INTO VACANTE (ID_RECLUTADOR, NOMBRE, PUESTO, SALARIO, MODALIDAD, UBICACION, DURACION)
                            VALUES (:id_rec, :nombre, :puesto, :salario, :modalidad, :ubicacion, :duracion)");
    $stmt->execute([
        ':id_rec' => $id_reclutador,
        ':nombre' => $puesto,
        ':puesto' => $puesto,
        ':salario' => $salario,
        ':modalidad' => $modalidad,
        ':ubicacion' => $ubicacion,
        ':duracion' => $duracion
    ]);

    $vacante_id = $conn->lastInsertId();

    // Obtener ID de escolaridad
    $stmt = $conn->prepare("SELECT ID FROM ESCOLARIDAD WHERE NIVEL = :nivel");
    $stmt->bindValue(':nivel', $escolaridad);
    $stmt->execute();
    $escolaridad_id = $stmt->fetchColumn();

    if ($escolaridad_id) {
        $stmt = $conn->prepare("INSERT INTO VACANTE_ESCOLARIDAD (ID_VACANTE, ID_ESCOLARIDAD) VALUES (:vac, :esc)");
        $stmt->execute([':vac' => $vacante_id, ':esc' => $escolaridad_id]);
    }

    // Insertar habilidades
    foreach ($habilidades as $nombre) {
        $stmt = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $hab_id = $stmt->fetchColumn();

        if ($hab_id) {
            $stmtInsert = $conn->prepare("INSERT INTO VACANTE_HABILIDAD (ID_VACANTE, ID_HABILIDAD) VALUES (:vac, :hab)");
            $stmtInsert->execute([':vac' => $vacante_id, ':hab' => $hab_id]);
        }
    }

    foreach ($titulos as $id_titulo) {
        $stmtTitulo = $conn->prepare("INSERT INTO VACANTE_TITULO (ID_VACANTE, ID_TITULO) VALUES (:vacante, :titulo)");
        $stmtTitulo->execute([
            ':vacante' => $vacante_id,
            ':titulo' => $id_titulo
        ]);
    }
    $conn->commit();

    mostrarAlerta("¡Vacante registrada!", "La vacante se ha guardado correctamente.", "success", "../vistas/PerfEmpresa.html");

} catch (PDOException $e) {
    $conn->rollBack();
    mostrarAlerta("Error", "Error al registrar vacante: " . $e->getMessage(), "error", "../vistas/VacanteNueva.html");
}



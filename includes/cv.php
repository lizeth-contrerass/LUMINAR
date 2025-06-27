<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

$correo = $_SESSION['user'] ?? '';
if (!$correo) {
    die("No hay sesión iniciada. Inicia sesión primero.");
}

// Obtener ID del usuario a partir del correo
$stmt = $conn->prepare("SELECT ID FROM USUARIO WHERE CORREO = :correo AND TIPO_USUARIO = 1");
$stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
$stmt->execute();
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    die("Usuario no encontrado o no es candidato.");
}

// Datos del formulario
$escolaridad = $_POST['escolaridad'] ?? '';
$softs = $_POST['softS'] ?? [];
$hards = $_POST['hardS'] ?? [];
$titulo_nombre = $_POST['title'] ?? '';

if (empty($escolaridad) || empty($titulo_nombre)) {
    die("Faltan campos obligatorios.");
}

try {
    $conn->beginTransaction();

    // Verificar si ya existe CV
    $stmt = $conn->prepare("SELECT ID FROM CV WHERE ID_CANDIDATO = :id_usuario");
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cv) {
        $cv_id = $cv['ID'];

        $stmt = $conn->prepare("UPDATE CV SET TITULO = :titulo WHERE ID = :cv_id");
        $stmt->bindValue(':titulo', $titulo_nombre);
        $stmt->bindValue(':cv_id', $cv_id);
        $stmt->execute();

        $conn->prepare("DELETE FROM CV_ESCOLARIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
        $conn->prepare("DELETE FROM CV_HABILIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO CV (ID_CANDIDATO, TITULO) VALUES (:id_usuario, :titulo)");
        $stmt->bindValue(':id_usuario', $id_usuario);
        $stmt->bindValue(':titulo', $titulo_nombre);
        $stmt->execute();
        $cv_id = $conn->lastInsertId();
    }

    // Insertar escolaridad
    $stmt = $conn->prepare("SELECT ID FROM ESCOLARIDAD WHERE NIVEL = :nivel");
    $stmt->bindValue(':nivel', $escolaridad);
    $stmt->execute();
    $escolaridad_id = $stmt->fetchColumn();

    if ($escolaridad_id) {
        $stmt = $conn->prepare("INSERT INTO CV_ESCOLARIDAD (ID_CV, ID_ESCOLARIDAD) VALUES (:cv_id, :escolaridad_id)");
        $stmt->bindValue(':cv_id', $cv_id);
        $stmt->bindValue(':escolaridad_id', $escolaridad_id);
        $stmt->execute();
    }

    // Insertar habilidades blandas
    foreach ($softs as $nombre) {
        $stmt = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre AND TIPO = 'BLANDA'");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $hab_id = $stmt->fetchColumn();

        if ($hab_id) {
            $stmtInsert = $conn->prepare("INSERT INTO CV_HABILIDAD (ID_CV, ID_HABILIDAD) VALUES (:cv_id, :hab_id)");
            $stmtInsert->bindValue(':cv_id', $cv_id);
            $stmtInsert->bindValue(':hab_id', $hab_id);
            $stmtInsert->execute();
        }
    }

    // Insertar habilidades duras
    foreach ($hards as $nombre) {
        $stmt = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre AND TIPO = 'DURA'");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $hab_id = $stmt->fetchColumn();

        if ($hab_id) {
            $stmtInsert = $conn->prepare("INSERT INTO CV_HABILIDAD (ID_CV, ID_HABILIDAD) VALUES (:cv_id, :hab_id)");
            $stmtInsert->bindValue(':cv_id', $cv_id);
            $stmtInsert->bindValue(':hab_id', $hab_id);
            $stmtInsert->execute();
        }
    }

    $conn->commit();
} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al guardar CV: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CV Guardado</title>
    <script src="sweetalert2.all.min.js"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: 'success',
        title: 'CV guardado correctamente',
        text: 'Tu currículum ha sido actualizado.',
        confirmButtonText: 'Aceptar'
    }).then(() => {
        window.location.href = '../vistas/editorCV.php';
    });
</script>
</body>
</html>

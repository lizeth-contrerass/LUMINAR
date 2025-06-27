<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("No hay sesión iniciada. Inicia sesión primero.");
}

$escolaridad = $_POST['escolaridad'] ?? '';
$softs = $_POST['softS'] ?? [];
$hards = $_POST['hardS'] ?? [];
$titulo_nombre = $_POST['title'] ?? '';

if (empty($escolaridad) || empty($titulo_nombre)) {
    die("Faltan campos obligatorios.");
}

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT ID FROM CV WHERE ID_CANDIDATO = :id_usuario");
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cv) {
        $cv_id = $cv['ID'];

        $stmt = $conn->prepare("UPDATE CV SET TITULO = :titulo WHERE ID = :cv_id");
        $stmt->bindValue(':titulo', $titulo_nombre, PDO::PARAM_STR);
        $stmt->bindValue(':cv_id', $cv_id, PDO::PARAM_INT);
        $stmt->execute();

        $conn->prepare("DELETE FROM CV_ESCOLARIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
        $conn->prepare("DELETE FROM CV_HABILIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO CV (ID_CANDIDATO, TITULO) VALUES (:id_usuario, :titulo)");
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo_nombre, PDO::PARAM_STR);
        $stmt->execute();
        $cv_id = $conn->lastInsertId();
    }

    $stmt = $conn->prepare("SELECT ID FROM ESCOLARIDAD WHERE NIVEL = :nivel");
    $stmt->bindValue(':nivel', $escolaridad, PDO::PARAM_STR);
    $stmt->execute();
    $escolaridad_id = $stmt->fetchColumn();

    if ($escolaridad_id) {
        $stmt = $conn->prepare("INSERT INTO CV_ESCOLARIDAD (ID_CV, ID_ESCOLARIDAD) VALUES (:cv_id, :escolaridad_id)");
        $stmt->bindValue(':cv_id', $cv_id, PDO::PARAM_INT);
        $stmt->bindValue(':escolaridad_id', $escolaridad_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    foreach ($softs as $nombre) {
        $stmt = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre AND TIPO = 'BLANDA'");
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $hab_id = $stmt->fetchColumn();

        if ($hab_id) {
            $stmtInsert = $conn->prepare("INSERT INTO CV_HABILIDAD (ID_CV, ID_HABILIDAD) VALUES (:cv_id, :hab_id)");
            $stmtInsert->bindValue(':cv_id', $cv_id, PDO::PARAM_INT);
            $stmtInsert->bindValue(':hab_id', $hab_id, PDO::PARAM_INT);
            $stmtInsert->execute();
        }
    }

    foreach ($hards as $nombre) {
        $stmt = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre AND TIPO = 'DURA'");
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $hab_id = $stmt->fetchColumn();

        if ($hab_id) {
            $stmtInsert = $conn->prepare("INSERT INTO CV_HABILIDAD (ID_CV, ID_HABILIDAD) VALUES (:cv_id, :hab_id)");
            $stmtInsert->bindValue(':cv_id', $cv_id, PDO::PARAM_INT);
            $stmtInsert->bindValue(':hab_id', $hab_id, PDO::PARAM_INT);
            $stmtInsert->execute();
        }
    }

    $conn->commit();
} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al guardar CV: " . $e->getMessage());
}

// Mostrar alerta y redirigir con SweetAlert2
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

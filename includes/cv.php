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
    // Iniciar transacción
    $conn->beginTransaction();

    // 1. Verificar si ya existe CV
    $stmt = $conn->prepare("SELECT ID FROM CV WHERE ID_CANDIDATO = :id_usuario");
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cv) {
        $cv_id = $cv['ID'];

        // Actualizar título
        $stmt = $conn->prepare("UPDATE CV SET TITULO = :titulo WHERE ID = :cv_id");
        $stmt->bindValue(':titulo', $titulo_nombre, PDO::PARAM_STR);
        $stmt->bindValue(':cv_id', $cv_id, PDO::PARAM_INT);
        $stmt->execute();

        // Eliminar datos previos
        $conn->prepare("DELETE FROM CV_ESCOLARIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
        $conn->prepare("DELETE FROM CV_HABILIDAD WHERE ID_CV = :cv_id")->execute([':cv_id' => $cv_id]);
    } else {
        // Insertar nuevo CV
        $stmt = $conn->prepare("INSERT INTO CV (ID_CANDIDATO, TITULO) VALUES (:id_usuario, :titulo)");
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo_nombre, PDO::PARAM_STR);
        $stmt->execute();
        $cv_id = $conn->lastInsertId();
    }

    // 2. Insertar escolaridad
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

    // 3. Insertar habilidades blandas
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

    // 4. Insertar habilidades duras
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

    // Confirmar cambios
    $conn->commit();

    echo "<h3>✅ Tu CV ha sido guardado exitosamente.</h3>";
    echo "<a href='../interfaz/cv.php'>← Volver</a>";

} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al guardar CV: " . $e->getMessage());
}

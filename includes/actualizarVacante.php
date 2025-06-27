<?php
session_start();
require_once '../includes/conexion.php';

$db = new Database();
$conn = $db->connect();

$correo = $_SESSION['user'] ?? null;
if (!$correo) die("No hay sesión activa.");

$id_vacante = $_POST['id'] ?? null;
$puesto = $_POST['puesto'] ?? '';
$salario = $_POST['salario'] ?? '';
$modalidad = $_POST['modalidad'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$duracion = $_POST['duracion'] ?? '';
$escolaridad = $_POST['escolaridad'] ?? '';
$habilidades = $_POST['habilidades'] ?? [];
$titulos = $_POST['titulos'] ?? [];

if (!$id_vacante || !$puesto || !$salario || !$modalidad || !$ubicacion || !$duracion || !$escolaridad || empty($titulos)) {
    die("Faltan campos obligatorios.");
}

try {
    $conn->beginTransaction();

    // 1. Actualizar datos principales de la vacante
    $stmt = $conn->prepare("UPDATE VACANTE SET PUESTO = :puesto, SALARIO = :salario, MODALIDAD = :modalidad, UBICACION = :ubicacion, DURACION = :duracion WHERE ID = :id");
    $stmt->execute([
        ':puesto' => $puesto,
        ':salario' => $salario,
        ':modalidad' => $modalidad,
        ':ubicacion' => $ubicacion,
        ':duracion' => $duracion,
        ':id' => $id_vacante
    ]);

    // 2. Actualizar escolaridad
    $stmt = $conn->prepare("DELETE FROM VACANTE_ESCOLARIDAD WHERE ID_VACANTE = :id");
    $stmt->execute([':id' => $id_vacante]);

    $stmt = $conn->prepare("SELECT ID FROM ESCOLARIDAD WHERE NIVEL = :nivel");
    $stmt->execute([':nivel' => $escolaridad]);
    $id_escolaridad = $stmt->fetchColumn();

    if ($id_escolaridad) {
        $stmt = $conn->prepare("INSERT INTO VACANTE_ESCOLARIDAD (ID_VACANTE, ID_ESCOLARIDAD) VALUES (:vac, :esc)");
        $stmt->execute([':vac' => $id_vacante, ':esc' => $id_escolaridad]);
    }

    // 3. Actualizar títulos
    $conn->prepare("DELETE FROM VACANTE_TITULO WHERE ID_VACANTE = :id")->execute([':id' => $id_vacante]);

    $stmt = $conn->prepare("INSERT INTO VACANTE_TITULO (ID_VACANTE, ID_TITULO) VALUES (:vac, :titulo)");
    foreach ($titulos as $titulo_id) {
        $stmt->execute([':vac' => $id_vacante, ':titulo' => $titulo_id]);
    }

    // 4. Actualizar habilidades
    $conn->prepare("DELETE FROM VACANTE_HABILIDAD WHERE ID_VACANTE = :id")->execute([':id' => $id_vacante]);

    $stmtHab = $conn->prepare("SELECT ID FROM HABILIDAD WHERE NOMBRE = :nombre");
    $stmtInsert = $conn->prepare("INSERT INTO VACANTE_HABILIDAD (ID_VACANTE, ID_HABILIDAD) VALUES (:vac, :hab)");

    foreach ($habilidades as $hab) {
        $stmtHab->execute([':nombre' => $hab]);
        $hab_id = $stmtHab->fetchColumn();
        if ($hab_id) {
            $stmtInsert->execute([':vac' => $id_vacante, ':hab' => $hab_id]);
        }
    }

    $conn->commit();
    header("Location: ../vistas/VacantesEmpresa.php");
    exit;
} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al actualizar vacante: " . $e->getMessage());
}



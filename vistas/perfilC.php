<?php
session_start();
require_once 'conexion.php';

$db = new Database();
$conn = $db->connect();

// Verificar sesión
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("No hay sesión activa.");
}

// Obtener datos del candidato
$stmt = $conn->prepare("SELECT NOMBRE, APELLIDOS, CORREO, CONTRA FROM USUARIO WHERE ID = :id AND TIPO = 1");
$stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Candidato no encontrado.");
}

// Convertir contraseña en asteriscos
$asteriscos = str_repeat('●', strlen($usuario['CONTRA']));
?>
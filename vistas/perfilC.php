<?php
session_start();
require_once '../includes/conexion.php';

$db = new Database();
$conn = $db->connect();

// Verificar sesión activa
$correo_usuario = $_SESSION['user'] ?? null;
if (!$correo_usuario) {
    die("No hay sesión activa.");
}

$stmt = $conn->prepare("SELECT NOMBRE, APELLIDOS, CORREO, CONTRA FROM USUARIO WHERE CORREO = :correo AND TIPO_USUARIO = 1");
$stmt->bindValue(':correo', $correo_usuario, PDO::PARAM_STR);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$usuario) {
    die("Candidato no encontrado.");
}

// Enmascarar la contraseña
$asteriscos = str_repeat('●', 10);


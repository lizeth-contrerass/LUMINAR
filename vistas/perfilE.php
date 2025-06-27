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

// Consulta para obtener todos los datos del reclutador
$sql = "
    SELECT 
        U.NOMBRE, 
        U.APELLIDOS, 
        U.CORREO, 
        U.CONTRA, 
        R.NOMBRE_EMPRESA, 
        R.RAZON_SOCIAL, 
        R.RFC
    FROM USUARIO U
    JOIN RECLUTADOR_INFO R ON U.ID = R.ID_USUARIO
    WHERE U.CORREO = :correo AND U.TIPO_USUARIO = 2
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':correo', $correo_usuario, PDO::PARAM_STR);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Reclutador no encontrado.");
}

// Enmascarar la contraseña con puntos
$asteriscos = str_repeat('●', 10);
?>

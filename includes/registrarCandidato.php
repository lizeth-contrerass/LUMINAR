<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre) || empty($apellidos) || empty($correo) || empty($contrasena)) {
        $_SESSION['registro_error'] = "Por favor llena todos los campos.";
        header("Location: ../vistas/registroCandidato.php");
        exit;
    }

    $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        $db = new Database();
        $conn = $db->connect();

        $verificar = $conn->prepare("SELECT ID FROM USUARIO WHERE CORREO = :correo");
        $verificar->bindParam(":correo", $correo);
        $verificar->execute();

        if ($verificar->rowCount() > 0) {
            $_SESSION['registro_error'] = "Este correo ya está registrado.";
            header("Location: ../vistas/registroCandidato.php");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO USUARIO (NOMBRE, APELLIDOS, CORREO, CONTRA, TIPO_USUARIO)
                        VALUES (:nombre, :apellidos, :correo, :contrasena,:tipo)");
        $tipo = 1;
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellidos", $apellidos);
        $stmt->bindParam(":correo", $correo);
        $stmt->bindParam(":contrasena", $passwordHash);
        $stmt->bindParam(":tipo", $tipo);

        if ($stmt->execute()) {
            $_SESSION['registro_exito'] = "Registro exitoso.";
            $errorLogin = "Registro exitoso.";
            header("Location: ../loginCandidato.php");
            exit;
        } else {
            $_SESSION['registro_error'] = "Error al registrar.";
        }

    } catch (PDOException $e) {
        $_SESSION['registro_error'] = "Error en la base de datos." .$e->getMessage();
    }

    header("Location: ../vistas/registroCandidato.php");
    exit;
}

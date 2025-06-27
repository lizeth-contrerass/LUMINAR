<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $empresa = $_POST['empresa'] ?? '';
    $razon_social = $_POST['razon_social'] ?? '';
    $rfc = $_POST['rfc'] ?? '';

    if (empty($nombre) || empty($apellidos) || empty($correo) || empty($contrasena) ||
        empty($empresa) || empty($razon_social) || empty($rfc)) {
        $_SESSION['registro_error'] = "Por favor llena todos los campos.";
        header("Location: ../vistas/registroEmpresa.php");
        exit;
    }

    $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        $db = new Database();
        $conn = $db->connect();

        // Verificar si el correo ya existe
        $verificar = $conn->prepare("SELECT ID FROM USUARIO WHERE CORREO = :correo");
        $verificar->bindParam(":correo", $correo);
        $verificar->execute();

        if ($verificar->rowCount() > 0) {
            $_SESSION['registro_error'] = "Este correo ya está registrado.";
            header("Location: ../vistas/registroEmpresa.php");
            exit;
        }

        // Insertar en USUARIO
        $stmt = $conn->prepare("INSERT INTO USUARIO (NOMBRE, APELLIDOS, CORREO, CONTRA, TIPO_USUARIO)
                                VALUES (:nombre, :apellidos, :correo, :contrasena, :tipo)");
        $tipo = 2;
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellidos", $apellidos);
        $stmt->bindParam(":correo", $correo);
        $stmt->bindParam(":contrasena", $passwordHash);
        $stmt->bindParam(":tipo", $tipo);

        if ($stmt->execute()) {
            $id_usuario = $conn->lastInsertId();

            // Insertar en RECLUTADOR_INFO
            $stmtInfo = $conn->prepare("INSERT INTO RECLUTADOR_INFO (ID_USUARIO, NOMBRE_EMPRESA, RAZON_SOCIAL, RFC)
                                        VALUES (:id_usuario, :empresa, :razon_social, :rfc)");
            $stmtInfo->bindParam(":id_usuario", $id_usuario);
            $stmtInfo->bindParam(":empresa", $empresa);
            $stmtInfo->bindParam(":razon_social", $razon_social);
            $stmtInfo->bindParam(":rfc", $rfc);

            if ($stmtInfo->execute()) {
                $_SESSION['registro_exito'] = "Registro exitoso.";
                header("Location: ../vistas/iniciarSesion.php");
                exit;
            } else {
                $_SESSION['registro_error'] = "Error al guardar datos adicionales.";
            }
        } else {
            $_SESSION['registro_error'] = "Error al registrar el usuario.";
        }

    } catch (PDOException $e) {
        $_SESSION['registro_error'] = "Error en la base de datos: " . $e->getMessage();
    }

    header("Location: ../vistas/registroEmpresa.php");
    exit;
}


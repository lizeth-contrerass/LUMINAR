<?php
require_once "includes/conexion.php";
require_once "includes/Candidato_Session.php"; // puedes renombrarlo a User_Session.php si ahora es general
require_once "includes/loginCandidato.php";    // contiene la clase User extendida desde Database

$userSession = new User_Session();
$db = new Database();
$conn = $db->connect();
$user = new User();

if (isset($_SESSION['user'])) {
    $user->setUser($userSession->getCurrentUser());
    $tipo = $user->getTipo();

    if ($tipo == 1) {
        header("Location: vistas/perfilCandidato.php");
    } elseif ($tipo == 2) {
        header("Location: vistas/perfilEmpresa.php");
    } else {
        // Tipo no reconocido
        header("Location: vistas/error.php");
    }
    exit;
} else if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    $userForm = $_POST['usuario'];
    $passForm = $_POST['contrasena'];

    if ($user->userExists($userForm, $passForm)) {
        $userSession->setCurrentUser($userForm);
        $user->setUser($userForm);

        $tipo = $user->getTipo();

        if ($tipo == 1) {
            header("Location: vistas/perfilCandidato.php");
        } elseif ($tipo == 2) {
            header("Location: vistas/perfilEmpresa.php");
        } else {
            header("Location: vistas/error.php");
        }
        exit;
    } else {
        $_SESSION['login_error'] = "Nombre de usuario y/o contraseña incorrectos.";
        header("Location: vistas/iniciarSesion.php");
        exit;
    }
} else {
    header("Location: vistas/iniciarSesion.php");
    exit;
}

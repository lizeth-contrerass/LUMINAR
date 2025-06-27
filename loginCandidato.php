<?php

require_once "includes/conexion.php";
require_once "includes/Candidato_Session.php";
require_once "includes/loginCandidato.php";

$userSession = new User_Session(); // session_start aquí
$db = new Database();
$conn = $db->connect();
$user = new User();

if(isset($_SESSION['user'])){
    //echo "hay sesion";
    $user->setUser($userSession->getCurrentUser());
    include_once 'vistas/homeCandidato.php';
}else if(isset($_POST['usuario'])&& isset($_POST['contrasena'])){
    //echo "Validacion de login";
    $userForm = $_POST['usuario'];
    $passForm = $_POST['contrasena'];

    if($user->userExists($userForm,$passForm)){
        //echo "Usuario existente";
        $userSession ->setCurrentUser($userForm);
        $user ->setUser($userForm);

        include_once 'vistas/homeCandidato.php';
    }else{
        //echo "Usuario o contraseña incorrecta";
        $errorLogin = "Nombre de usuario y/o password incorrecto";
        include_once 'vistas/iniciarSesion.php';
    }
}else{
    //echo 'Login':
    include_once 'vistas/iniciarSesion.php';
}

?>
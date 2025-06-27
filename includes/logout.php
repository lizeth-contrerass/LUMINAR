<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once '../includes/Candidato_Session.php';
$userSession = new User_Session();
$userSession->logout();

header("location: ../index.html");
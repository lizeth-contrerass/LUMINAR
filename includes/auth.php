<?php
function authenticate(){
    session_start();
    if (!isset($_SESSION['user'])) {
        header("location: ../loginCandidato.php");
        exit;
    }
}
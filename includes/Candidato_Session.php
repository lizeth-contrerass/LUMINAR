<?php
class User_Session{
    public function __construct()
    {
     session_start();
    }

    public function setCurrentUser($user){
        $_SESSION['user'] = $user;
    }

    public function getCurrentUser(){
        return $_SESSION['user'];
    }

    public function logout(){
        session_unset();
        session_destroy();
    }
}
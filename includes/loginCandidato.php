<?php

include_once 'conexion.php';
class User extends Database
{
    private $nombre;
    private $correo;

    private $tipo;

    public function userExists($correo, $pass)
    {
    $query = $this -> connect()-> prepare("SELECT * FROM USUARIO WHERE CORREO = :correo");
    $query->bindParam(":correo", $correo);
    $query->execute();
        if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (password_verify($pass, $row['CONTRA'])) {
            return true;
        } else {
            return false;
        }
    } else {
        echo "Correo no registrado.";
    }
    }

    public function setUser($correo){
        $query = $this->connect()->prepare("SELECT * FROM USUARIO WHERE CORREO = :correo");
        $query->bindParam(":correo", $correo);
        $query->execute();
        foreach($query as $currentUser){
            $this->nombre = $currentUser['NOMBRE'];
            $this->correo = $currentUser['CORREO'];
            $this->tipo = $currentUser['TIPO_USUARIO'];
        }
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getCorreo(){
        return $this->correo;
    }

    public function getTipo(){
        return $this->tipo;
    }
}
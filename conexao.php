<?php

$usuario = 'root';
$senha = '0000';
$database = 'login';
$host = 'localhost';


$mysqli = new mysqli($host, $usuario, $senha, $database);

if($mysqli->error) {
    die("Falha ao conectar ao banco de dados: " . $mysqli->error);
}

?>
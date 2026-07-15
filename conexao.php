<?php

$host = "localhost:3306";
$usuario = "root";
$senha = "0000";
$database = "login";

$mysqli = new mysqli($host, $usuario, $senha, $database);

if ($mysqli->connect_errno) {
    die("Falha na conexao: " . $mysqli->connect_error);
}
?>

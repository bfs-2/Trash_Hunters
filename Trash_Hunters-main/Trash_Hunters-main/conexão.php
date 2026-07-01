<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$database = "login";

$mysqli = new mysqli($host, $usuario, $senha, $database);

if ($mysqli->connect_errno) {
    die("Falha na conexao: " . $mysqli->connect_error);
}
?>

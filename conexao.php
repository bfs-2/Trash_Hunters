<?php

$host = "127.0.0.1";
$usuario = "root";
$senha = "beatriz22";
$database = "login";

$mysqli = new mysqli($host, $usuario, $senha, $database);

if ($mysqli->connect_errno) {
    die("Falha na conexao: " . $mysqli->connect_error);
}
?>

<?php
session_start();
if (!isset($_SESSION['id'])) {  
    session_start();  
    exit();
}


if(!isset($_SESSION['id']) || !isset($_SESSION['nome'])){
  die("Acesso negado. Por favor, faça login para acessar esta página.");
}
?>

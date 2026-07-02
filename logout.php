<?php


if(!isset($_SESSION['id']) || !isset($_SESSION['nome'])){
  session_start();

  session_destroy();

  header("Location: login.php");
}

?>
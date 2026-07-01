<?php
session_start();
include("conexao.php");

$email = $_POST["usuario"];
$senha = $_POST["senha"];

$sql = "SELECT * FROM usuarios WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 1) {

    $usuario = $resultado->fetch_assoc();

    if (password_verify($senha, $usuario["senha"])) {

        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nome"] = $usuario["nome"];

        header("Location: painel.php");
        exit();

    } else {
        echo "Senha incorreta!";
    }

} else {
    echo "E-mail não encontrado!";
}
?>
<?php
   
    // === CAPTURA DE ERRO ===
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $servidor = "127.0.0.1";
    $usuario = "dev_cardapio";
    $senha = "root";
    $dbname = "cardapio";

    $conn = mysqli_connect ($servidor, $usuario, $senha, $dbname);
    
    if (!$conn) {
            die("Erro na conexão: " . mysqli_connect_error());
        }
?>

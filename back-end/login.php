<?php
session_start();

    if (!$senha_valida)
        {
        echo json_encode([
            "success" => false,
            "message" => "Usuário ou senha incorretos!"
             ]);
             exit;
        }
?>
<?php

// === CORS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === CREDENCIAIS ===
$servidor = "127.0.0.1";
$usuario  = "dev_cardapio";
$senha    = "root";
$dbname   = "cardapio";

// === MYSQLI (usado por alguns arquivos) ===
$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);
if (!$conn) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro na conexão mysqli."]);
    exit();
}
mysqli_set_charset($conn, "utf8mb4");

// === PDO (usado pela maioria dos arquivos) ===
try {
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$dbname;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro na conexão PDO."]);
    exit();
}

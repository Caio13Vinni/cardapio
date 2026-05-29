<?php
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    $_POST = $data;
}

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) { $_POST = $data; }

session_start();
include_once("conexao.php");

$cardapioLogin = $_POST['cardapioLogin'] ?? null;

if ($cardapioLogin) {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
   
    if(!empty($email) && !empty($senha)){
        $email_seguro = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = '$email_seguro' LIMIT 1";
        
        $resultado_execucao = mysqli_query($conn, $sql);

        if($resultado_execucao) {
            $row_usuario = mysqli_fetch_assoc($resultado_execucao);

            if($row_usuario && password_verify($senha, $row_usuario['senha'])){
                $_SESSION['id'] = $row_usuario['id'];
                $_SESSION['nome'] = $row_usuario['nome'];
                
                echo json_encode([
                    "success" => true,
                    "message" => "Login realizado com sucesso",
                    "user" => [
                        "id" => $row_usuario['id'],
                        "nome" => $row_usuario['nome']
                    ]
                ]);
                exit();
            } else {
                echo json_encode(["success" => false, "message" => "Usuário ou Senha incorretos!"]);
                exit();
            }
        } else {
            echo json_encode(["success" => false, "message" => "Erro na base de dados."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
        exit();
    }
} else {
    echo json_encode(["success" => false, "message" => "Pedido inválido"]);
    exit();
}

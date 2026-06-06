<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once 'conexao.php';

$json = file_get_contents('php://input');
$dados = json_decode($json, true);

// Resposta padrão
$retorno = ['sucesso' => false, 'mensagem' => ''];

if ($dados) {
    $nome = mysqli_real_escape_string($conn, $dados['nome']);
    $email = mysqli_real_escape_string($conn, $dados['email']);
    $usuario = mysqli_real_escape_string($conn, $dados['usuario']);
    $senha = password_hash($dados['senha'], PASSWORD_DEFAULT);
    
    $result_usuario = "INSERT INTO usuarios (nome, email, usuario, senha) VALUES (
        '$nome',
        '$email',
        '$usuario',
        '$senha'
    )";
    
    $resultado_usuario = mysqli_query($conn, $result_usuario);
    
    if (mysqli_insert_id($conn)) {
        $retorno['sucesso'] = true;
        $retorno['mensagem'] = "Usuário cadastrado com sucesso!";
    } else {
        $retorno['mensagem'] = "Erro ao cadastrar o usuário no banco de dados.";
    }
} else {
    $retorno['mensagem'] = "Nenhum dado recebido.";
}

echo json_encode($retorno);
?>
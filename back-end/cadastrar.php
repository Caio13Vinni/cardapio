<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

include_once 'conexao.php';

$json = file_get_contents('php://input');
$dados = json_decode($json, true);

$retorno = ['sucesso' => false, 'mensagem' => ''];

if ($dados) {
    $erros = [];

    $nome  = trim($dados['nome']  ?? '');
    $email = trim($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    // RN002 - Nome entre 2 e 120 caracteres
    if (mb_strlen($nome) < 2 || mb_strlen($nome) > 120) {
        $erros[] = "O nome deve ter entre 2 e 120 caracteres. (RN002)";
    }

    // Email obrigatório e válido
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }

    // RN008 - Senha entre 8 e 64 caracteres
    if (mb_strlen($senha) < 8 || mb_strlen($senha) > 64) {
        $erros[] = "A senha deve ter entre 8 e 64 caracteres. (RN008)";
    }

    if (!empty($erros)) {
        echo json_encode(['sucesso' => false, 'mensagem' => implode(" ", $erros)]);
        exit;
    }

    $nome_seguro   = mysqli_real_escape_string($conn, $nome);
    $email_seguro  = mysqli_real_escape_string($conn, $email);
    $senha_hash    = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica email duplicado
    $check = mysqli_query($conn, "SELECT id FROM usuarios WHERE email = '$email_seguro' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este e-mail já está cadastrado.']);
        exit;
    }

    $sql = "INSERT INTO usuarios (nome, email, usuario, senha) VALUES ('$nome_seguro', '$email_seguro', '$email_seguro', '$senha_hash')";
    $resultado = mysqli_query($conn, $sql);

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
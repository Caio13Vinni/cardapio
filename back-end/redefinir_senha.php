<?php
require_once 'conexao.php';

$json = file_get_contents('php://input');
$dados = json_decode($json, true);

$retorno = ['sucesso' => false, 'mensagem' => ''];

if ($dados && !empty($dados['token']) && !empty($dados['nova_senha'])) {
    $token = $dados['token'];
    $nova_senha = password_hash($dados['nova_senha'], PASSWORD_DEFAULT);
    $agora = date('Y-m-d H:i:s');

    try {
        // 1. Procura o utilizador pelo token e verifica se ainda está dentro do prazo de validade
        $stmt = $pdo->prepare("SELECT id FROM restaurantes WHERE token_recuperacao = ? AND token_expiracao > ?");
        $stmt->execute([$token, $agora]);
        $restaurante = $stmt->fetch();

        if ($restaurante) {
            // 2. Atualiza a senha e limpa os campos de recuperação por segurança
            $stmtUpdate = $pdo->prepare("UPDATE restaurantes SET senha = ?, token_recuperacao = NULL, token_expiracao = NULL WHERE id = ?");
            $stmtUpdate->execute([$nova_senha, $restaurante['id']]);

            $retorno['sucesso'] = true;
            $retorno['mensagem'] = "Senha redefinida com sucesso!";
        } else {
            $retorno['mensagem'] = "O link de redefinição é inválido ou já expirou.";
        }
    } catch (PDOException $e) {
        $retorno['mensagem'] = "Erro ao atualizar senha: " . $e->getMessage();
    }
} else {
    $retorno['mensagem'] = "Dados incompletos para a redefinição.";
}

echo json_encode($retorno);
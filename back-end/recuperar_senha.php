<?php
require_once 'conexao.php';

$json = file_get_contents('php://input');
$dados = json_decode($json, true);

$retorno = ['sucesso' => false, 'mensagem' => ''];

if ($dados && !empty($dados['email'])) {
    $email = trim($dados['email']);

    try {
        // 1. Verifica se o e-mail existe
        $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $restaurante = $stmt->fetch();

        if ($restaurante) {
            // 2. Cria um token seguro e define expiração para daqui a 1 hora
            $token = bin2hex(random_bytes(32));
            $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // 3. Salva o token no registo do restaurante
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expiracao = ? WHERE id = ?");
            $stmtUpdate->execute([$token, $expiracao, $restaurante['id']]);

            // 4. Monta o link que o utilizador vai clicar no e-mail
            $dominio = $_SERVER['HTTP_HOST'];
            $link_redefinicao = "http://$dominio/redefinir-senha?token=$token";

            // Envio do e-mail (Simulação ativa ou mail() padrão)
            $para = $email;
            $assunto = "Recuperação de Senha - Cardáp.io";
            $mensagem_email = "Olá, " . $restaurante['nome'] . ".\n\nClique no link abaixo para redefinir a sua senha:\n$link_redefinicao\n\nEste link expira em 1 hora.";
            $headers = "From: nao-responda@cardap.io";

            // Se estiveres num servidor online, isto envia o e-mail real. Em localhost ele apenas avançará com sucesso.
            @mail($para, $assunto, $mensagem_email, $headers);

            $retorno['sucesso'] = true;
            $retorno['mensagem'] = "Link de recuperação gerado com sucesso!";
            // Enviamos o link no JSON apenas para facilitar o teu debug em localhost!
            $retorno['debug_link'] = $link_redefinicao; 
        } else {
            $retorno['mensagem'] = "O e-mail introduzido não está cadastrado.";
        }
    } catch (PDOException $e) {
        $retorno['mensagem'] = "Erro interno: " . $e->getMessage();
    }
} else {
    $retorno['mensagem'] = "E-mail não fornecido.";
}

echo json_encode($retorno);
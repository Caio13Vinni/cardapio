<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    if (!isset($_GET['id_restaurante'])) {
        echo json_encode(["erro" => true, "mensagem" => "ID não fornecido."]);
        exit;
    }
    $id = intval($_GET['id_restaurante']);
    try {
        $stmt = $pdo->prepare("SELECT nome, descricao, endereco, bairro, cep, cidade, estado, telefone, email, horarios, instagram, facebook, website FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $perfil = $stmt->fetch();
        echo $perfil ? json_encode($perfil) : json_encode(["erro" => true, "mensagem" => "Usuário não encontrado."]);
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro: " . $e->getMessage()]);
    }
    exit;
}

if ($metodo === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);

    if (!isset($d['id_restaurante'])) {
        echo json_encode(["erro" => true, "mensagem" => "ID obrigatório."]);
        exit;
    }

    $erros = [];
    $nome     = trim($d['nome']      ?? '');
    $email    = trim($d['email']     ?? '');
    $telefone = trim($d['telefone']  ?? '');
    $endereco = trim($d['endereco']  ?? '');
    $horarios = trim($d['horarios']  ?? '');
    $instagram= trim($d['instagram'] ?? '');
    $facebook = trim($d['facebook']  ?? '');
    $website  = trim($d['website']   ?? '');

    if (empty($nome)) $erros[] = "O nome do restaurante é obrigatório. (RN059)";
    elseif (mb_strlen($nome) < 2 || mb_strlen($nome) > 120) $erros[] = "O nome deve ter entre 2 e 120 caracteres. (RN060)";
    if (mb_strlen($telefone) > 20) $erros[] = "O telefone deve ter no máximo 20 caracteres. (RN062)";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "O e-mail deve ter formato válido. (RN063)";
    if (mb_strlen($email) > 150) $erros[] = "O e-mail deve ter no máximo 150 caracteres. (RN064)";
    if (mb_strlen($endereco) > 255) $erros[] = "O endereço deve ter no máximo 255 caracteres. (RN065)";
    if (mb_strlen($horarios) > 255) $erros[] = "O horário deve ter no máximo 255 caracteres. (RN066)";
    if (mb_strlen($instagram) > 100) $erros[] = "O Instagram deve ter no máximo 100 caracteres. (RN067)";
    if (mb_strlen($facebook) > 100) $erros[] = "O Facebook deve ter no máximo 100 caracteres. (RN068)";
    if (mb_strlen($website) > 255) $erros[] = "O Website deve ter no máximo 255 caracteres. (RN069)";

    if (!empty($erros)) {
        echo json_encode(["erro" => true, "mensagem" => implode(" ", $erros)]);
        exit;
    }

    $id = intval($d['id_restaurante']);
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, descricao=?, endereco=?, bairro=?, cep=?, cidade=?, estado=?, telefone=?, email=?, horarios=?, instagram=?, facebook=?, website=? WHERE id=?");
        $stmt->execute([
            $nome, trim($d['descricao'] ?? ''), $endereco,
            trim($d['bairro'] ?? ''), trim($d['cep'] ?? ''),
            trim($d['cidade'] ?? ''), trim($d['estado'] ?? ''),
            $telefone, $email, $horarios, $instagram, $facebook, $website, $id
        ]);
        echo json_encode(["sucesso" => true, "mensagem" => "Informações atualizadas!"]);
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro: " . $e->getMessage()]);
    }
    exit;
}
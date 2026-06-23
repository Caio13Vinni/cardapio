<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. CARREGAR INFORMAÇÕES DO PERFIL (GET)
if ($metodo === 'GET') {
    if (!isset($_GET['id_restaurante'])) {
        echo json_encode(["erro" => true, "mensagem" => "ID do restaurante não fornecido."]);
        exit;
    }

    $id = intval($_GET['id_restaurante']);

    try {
        $stmt = $pdo->prepare("SELECT nome, descricao, endereco, bairro, cep, cidade, estado, telefone, email, horarios, instagram, facebook, website FROM restaurantes WHERE id = ?");
        $stmt->execute([$id]);
        $perfil = $stmt->fetch();

        if ($perfil) {
            echo json_encode($perfil);
        } else {
            echo json_encode(["erro" => true, "mensagem" => "Estabelecimento não encontrado."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro ao buscar perfil: " . $e->getMessage()]);
    }
    exit;
}

// 2. SALVAR/ATUALIZAR INFORMAÇÕES (POST)
if ($metodo === 'POST') {
    $dadosRecebidos = json_decode(file_get_contents('php://input'), true);

    if (!isset($dadosRecebidos['id_restaurante'])) {
        echo json_encode(["erro" => true, "mensagem" => "ID do restaurante é obrigatório."]);
        exit;
    }

    $id = intval($dadosRecebidos['id_restaurante']);
    $nome = trim($dadosRecebidos['nome'] ?? '');
    $descricao = trim($dadosRecebidos['descricao'] ?? '');
    $endereco = trim($dadosRecebidos['endereco'] ?? '');
    $bairro = trim($dadosRecebidos['bairro'] ?? '');
    $cep = trim($dadosRecebidos['cep'] ?? '');
    $cidade = trim($dadosRecebidos['cidade'] ?? '');
    $estado = trim($dadosRecebidos['estado'] ?? '');
    $telefone = trim($dadosRecebidos['telefone'] ?? '');
    $email = trim($dadosRecebidos['email'] ?? '');
    $horarios = trim($dadosRecebidos['horarios'] ?? '');
    $instagram = trim($dadosRecebidos['instagram'] ?? '');
    $facebook = trim($dadosRecebidos['facebook'] ?? '');
    $website = trim($dadosRecebidos['website'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE restaurantes SET nome = ?, descricao = ?, endereco = ?, bairro = ?, cep = ?, cidade = ?, estado = ?, telefone = ?, email = ?, horarios = ?, instagram = ?, facebook = ?, website = ? WHERE id = ?");
        $stmt->execute([$nome, $descricao, $endereco, $bairro, $cep, $cidade, $estado, $telefone, $email, $horarios, $instagram, $facebook, $website, $id]);

        echo json_encode(["sucesso" => true, "mensagem" => "Informações atualizadas com sucesso!"]);

    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro ao salvar alterações: " . $e->getMessage()]);
    }
    exit;
}

echo json_encode(["erro" => true, "mensagem" => "Método não suportado."]);
<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    if (!isset($_GET['id_restaurante'])) {/var/www/html/cardapio/
    // RN060 - Nome entre 2 e 120 caracteres
    elseif (mb_strlen($nome) < 2 || mb_strlen($nome) > 120) {
        $erros[] = "O nome deve ter entre 2 e 120 caracteres.";
    }

    // RN062 - Telefone máx 20
    $telefone = trim($d['telefone'] ?? '');
    if (mb_strlen($telefone) > 20) {
        $erros[] = "O telefone deve ter no máximo 20 caracteres.";
    }

    // RN063 - Email formato válido
    $email = trim($d['email'] ?? '');
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O e-mail de contato deve ter formato válido. ";
    }

    // RN064 - Email máx 150
    if (mb_strlen($email) > 150) {
        $erros[] = "O e-mail deve ter no máximo 150 caracteres. ";
    }

    // RN065 - Endereço máx 255
    $endereco = trim($d['endereco'] ?? '');
    if (mb_strlen($endereco) > 255) {
        $erros[] = "O endereço deve ter no máximo 255 caracteres. ";
    }

    // RN066 - Horários máx 255
    $horarios = trim($d['horarios'] ?? '');
    if (mb_strlen($horarios) > 255) {/var/www/html/cardapio/airro=?, cep=?, cidade=?, estado=?, telefone=?, email=?, horarios=?, instagram=?, facebook=?, website=? WHERE id=?");
        $stmt->execute([
            $nome,
            trim($d['descricao'] ?? ''),
            $endereco,
            trim($d['bairro'] ?? ''),
            trim($d['cep'] ?? ''),
            trim($d['cidade'] ?? ''),
            trim($d['estado'] ?? ''),
            $telefone,
            $email,
            $horarios,
            $instagram,
            $facebook,
            $website,
            $id
        ]);
        echo json_encode(["sucesso" => true, "mensagem" => "Informações atualizadas!"]);
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro: " . $e->getMessage()]);
    }
    exit;
}

echo json_encode(["erro" => true, "mensagem" => "Método inválido."]);

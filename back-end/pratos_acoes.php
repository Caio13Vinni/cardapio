<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once("conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];

// LISTAR PRATOS DE UM RESTAURANTE (Baseado no id_restaurante)
if ($metodo === 'GET') {
    $id_restaurante = isset($_GET['id_restaurante']) ? intval($_GET['id_restaurante']) : 0;

    if ($id_restaurante === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID do restaurante não fornecido."]);
        exit;
    }

    // Faz um JOIN com a tabela de categorias para garantir que buscamos apenas os pratos deste restaurante
    $query = "SELECT p.*, c.nome as categoria_nome FROM produto p 
              INNER JOIN categoria c ON p.id_categoria = c.id 
              WHERE c.id_restaurante = $id_restaurante ORDER BY c.nome, p.nome ASC";
              
    $resultado = mysqli_query($conn, $query);
    
    $produtos = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $produtos[] = [
            "id" => $linha['id'],
            "nome" => $linha['nome'],
            "descricao" => $linha['descricao'],
            "preco" => floatval($linha['preco']),
            "imagem" => $linha['imagem'],
            "ativo" => (bool)$linha['ativo'],
            "id_categoria" => $linha['id_categoria'],
            "categoria_nome" => $linha['categoria_nome']
        ];
    }

    echo json_encode(["sucesso" => true, "produtos" => $produtos]);
}

// SALVAR OU EDITAR PRATO
if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!empty($dados['id_categoria']) && !empty($dados['nome']) && isset($dados['preco'])) {
        $id_categoria = intval($dados['id_categoria']);
        $nome = mysqli_real_escape_string($conn, $dados['nome']);
        $descricao = mysqli_real_escape_string($conn, $dados['descricao'] ?? '');
        $preco = floatval($dados['preco']);
        $ativo = isset($dados['ativo']) ? intval($dados['ativo']) : 1;
        $imagem = mysqli_real_escape_string($conn, $dados['imagem'] ?? '');

        if (!empty($dados['id'])) {
            $id = intval($dados['id']);
            $query = "UPDATE produto SET nome='$nome', descricao='$descricao', preco=$preco, ativo=$ativo, imagem='$imagem' WHERE id=$id";
        } else {
            $query = "INSERT INTO produto (nome, descricao, preco, imagem, ativo, id_categoria) VALUES ('$nome', '$descricao', $preco, '$imagem', $ativo, $id_categoria)";
        }

        if (mysqli_query($conn, $query)) {
            echo json_encode(["sucesso" => true, "mensagem" => "Prato salvo com sucesso!"]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao processar banco de dados."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Dados incompletos."]);
    }
}

// DELETAR PRATO
if ($metodo === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID inválido."]);
        exit;
    }

    $query = "DELETE FROM produto WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["sucesso" => true, "mensagem" => "Prato removido com sucesso!"]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao remover prato."]);
    }
}
?>
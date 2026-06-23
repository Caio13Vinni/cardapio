<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. LISTAR CATEGORIAS (GET)
if ($metodo === 'GET') {
    if (!isset($_GET['id_restaurante'])) {
        echo json_encode(["erro" => true, "mensagem" => "ID do restaurante não fornecido."]);
        exit;
    }

    $id_restaurante = intval($_GET['id_restaurante']);

    try {
        $stmt = $pdo->prepare("SELECT id, nome, descricao, ativo FROM categorias WHERE id_restaurante = ? ORDER BY nome ASC");
        $stmt->execute([$id_restaurante]);
        $categorias = $stmt->fetchAll();

        // Garante a tipagem correta para o JavaScript ler sem errar
        $resultado = array_map(function($item) {
            return [
                "id" => $item['id'],
                "nome" => $item['nome'],
                "descricao" => $item['descricao'] ?? '',
                "ativo" => (int)$item['ativo']
            ];
        }, $categorias);

        // Retorna o ARRAY PURO, pois o Vue faz: "if (Array.isArray(data))"
        echo json_encode($resultado);

    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro ao buscar categorias: " . $e->getMessage()]);
    }
    exit;
}

// 2. SALVAR OU EDITAR CATEGORIA (POST)
if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (empty($dados['id_restaurante']) || empty(trim($dados['nome'] ?? ''))) {
        echo json_encode([
            "sucesso" => false, 
            "success" => false, 
            "mensagem" => "O nome da categoria e o ID do restaurante são obrigatórios."
        ]);
        exit;
    }

    $id_restaurante = intval($dados['id_restaurante']);
    $nome = trim($dados['nome']);
    $descricao = trim($dados['descricao'] ?? '');
    $ativo = isset($dados['ativo']) ? intval($dados['ativo']) : 1;

    try {
        if (!empty($dados['id'])) {
            // É uma EDIÇÃO (UPDATE)
            $id = intval($dados['id']);
            $stmt = $pdo->prepare("UPDATE categorias SET nome = ?, descricao = ?, ativo = ? WHERE id = ? AND id_restaurante = ?");
            $stmt->execute([$nome, $descricao, $ativo, $id, $id_restaurante]);
            $mensagem = "Categoria atualizada com sucesso!";
        } else {
            // É uma CRIAÇÃO (INSERT)
            $stmt = $pdo->prepare("INSERT INTO categorias (id_restaurante, nome, descricao, ativo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_restaurante, $nome, $descricao, $ativo]);
            $mensagem = "Categoria criada com sucesso!";
        }

        echo json_encode([
            "sucesso" => true, 
            "success" => true, 
            "mensagem" => $mensagem
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "sucesso" => false, 
            "success" => false, 
            "mensagem" => "Erro no banco: " . $e->getMessage()
        ]);
    }
    exit;
}

// 3. DELETAR CATEGORIA (DELETE)
if ($metodo === 'DELETE') {
    // No Vue, às vezes o ID vem na URL (?id=5) e às vezes no corpo do JSON. Cobrimos os dois:
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id === 0) {
        $dados = json_decode(file_get_contents("php://input"), true);
        $id = isset($dados['id']) ? intval($dados['id']) : 0;
    }

    if ($id === 0) {
        echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "ID da categoria não informado para exclusão."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["sucesso" => true, "success" => true, "mensagem" => "Categoria excluída com sucesso!"]);

    } catch (PDOException $e) {
        echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "Erro ao excluir: " . $e->getMessage()]);
    }
    exit;
}

echo json_encode(["erro" => true, "mensagem" => "Método não suportado."]);
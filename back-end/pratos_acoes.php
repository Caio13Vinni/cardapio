<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. LISTAR PRATOS OU BUSCAR UM PRATO ESPECÍFICO (GET)
if ($metodo === 'GET') {
    $id_restaurante = isset($_GET['id_restaurante']) ? intval($_GET['id_restaurante']) : 0;
    $id_prato = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id_restaurante === 0) {
        echo json_encode(["erro" => true, "mensagem" => "ID do restaurante não fornecido."]);
        exit;
    }

    try {
        // SE PASSOU O ID DO PRATO, DEVOLVE SÓ ELE (Usado na tela EditarPrato.vue)
        if ($id_prato > 0) {
            $stmt = $pdo->prepare("
                SELECT p.*, c.nome AS categoria 
                FROM pratos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                WHERE p.id = ? AND p.id_restaurante = ?
            ");
            $stmt->execute([$id_prato, $id_restaurante]);
            $prato = $stmt->fetch();

            echo json_encode($prato ? $prato : ["erro" => true, "mensagem" => "Prato não encontrado"]);
            exit;
        }

        // SE NÃO PASSOU ID, DEVOLVE A LISTA GERAL DO RESTAURANTE (Usado na tela Pratos.vue)
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome AS categoria 
            FROM pratos p 
            LEFT JOIN categorias c ON p.id_categoria = c.id 
            WHERE p.id_restaurante = ? 
            ORDER BY c.nome ASC, p.ordem ASC, p.nome ASC
        ");
        $stmt->execute([$id_restaurante]);
        $pratos = $stmt->fetchAll();

        // Formata os dados exatamente como as variáveis do front-end esperam
        $resultado = array_map(function($p) {
            return [
                "id" => $p['id'],
                "nome" => $p['nome'],
                "descricao" => $p['descricao'] ?? '',
                "preco" => floatval($p['preco']),
                "imagem" => $p['imagem'] ?? '',
                "ativo" => (int)$p['ativo'],
                "destaque" => (int)$p['destaque'],
                "ordem" => (int)$p['ordem'],
                "observacoes" => $p['observacoes'] ?? '',
                "id_categoria" => $p['id_categoria'],
                "categoria" => $p['categoria'] ?? 'Sem categoria'
            ];
        }, $pratos);

        // Devolve o ARRAY PURO para o .map() do Vue não quebrar
        echo json_encode($resultado);

    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro ao buscar pratos: " . $e->getMessage()]);
    }
    exit;
}

// 2. SALVAR OU EDITAR PRATO COM FOTO (POST)
if ($metodo === 'POST') {
    // Como aceita FormData (foto) ou JSON raw, capturamos de ambas as fontes:
    $dados = $_POST;
    if (empty($dados)) {
        $dados = json_decode(file_get_contents("php://input"), true) ?? [];
    }

    if (empty($dados['id_restaurante']) || empty(trim($dados['nome'] ?? ''))) {
        echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "Nome do prato e restaurante são obrigatórios."]);
        exit;
    }

    $id_restaurante = intval($dados['id_restaurante']);
    $nome = trim($dados['nome']);
    $descricao = trim($dados['descricao'] ?? '');
    
    // Converte o preço digitado com vírgula (29,90) para o padrão do MySQL (29.90)
    $preco_str = str_replace(',', '.', trim($dados['preco'] ?? '0'));
    $preco = floatval($preco_str);

    $ativo = isset($dados['ativo']) ? intval($dados['ativo']) : 1;
    $destaque = isset($dados['destaque']) ? intval($dados['destaque']) : 0;
    $ordem = isset($dados['ordem']) ? intval($dados['ordem']) : 1;
    $observacoes = trim($dados['observacoes'] ?? '');

    // =========================================================================
    // TRADUTOR DE CATEGORIA (Resolve a diferença entre NovoPrato e EditarPrato)
    // =========================================================================
    $id_categoria = intval($dados['id_categoria'] ?? 0);

    if ($id_categoria === 0 && !empty($dados['categoria'])) {
        $nome_cat_str = trim($dados['categoria']);
        $stmtCat = $pdo->prepare("SELECT id FROM categorias WHERE nome = ? AND id_restaurante = ?");
        $stmtCat->execute([$nome_cat_str, $id_restaurante]);
        $cat_db = $stmtCat->fetch();
        if ($cat_db) {
            $id_categoria = $cat_db['id'];
        }
    }

    // Se mesmo assim não achou a categoria, atrela a uma categoria genérica de segurança
    if ($id_categoria === 0) {
        $stmtGetFirst = $pdo->prepare("SELECT id FROM categorias WHERE id_restaurante = ? LIMIT 1");
        $stmtGetFirst->execute([$id_restaurante]);
        $first = $stmtGetFirst->fetch();
        $id_categoria = $first ? $first['id'] : 1; 
    }

    // =========================================================================
    // SISTEMA DE UPLOAD DE FOTO DO PRATO
    // =========================================================================
    $caminho_imagem = null;
    $diretorio_uploads = 'uploads/pratos/';
    if (!is_dir($diretorio_uploads)) {
        mkdir($diretorio_uploads, 0755, true);
    }

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        // Gera um nome criptografado único para a foto do prato não ser subscrita
        $nome_foto = "prato_" . $id_restaurante . "_" . time() . "_" . rand(100,999) . "." . $ext;
        $destino = $diretorio_uploads . $nome_foto;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $caminho_imagem = $destino;
        }
    }

    try {
        if (!empty($dados['id'])) {
            // É UMA EDIÇÃO (UPDATE)
            $id_prato = intval($dados['id']);
            
            if ($caminho_imagem) {
                // Se mandou foto nova, atualiza a foto também
                $stmt = $pdo->prepare("UPDATE pratos SET id_categoria = ?, nome = ?, preco = ?, descricao = ?, imagem = ?, ativo = ?, destaque = ?, ordem = ?, observacoes = ? WHERE id = ? AND id_restaurante = ?");
                $stmt->execute([$id_categoria, $nome, $preco, $descricao, $caminho_imagem, $ativo, $destaque, $ordem, $observacoes, $id_prato, $id_restaurante]);
            } else {
                // Se não mexeu na foto, atualiza só os textos
                $stmt = $pdo->prepare("UPDATE pratos SET id_categoria = ?, nome = ?, preco = ?, descricao = ?, ativo = ?, destaque = ?, ordem = ?, observacoes = ? WHERE id = ? AND id_restaurante = ?");
                $stmt->execute([$id_categoria, $nome, $preco, $descricao, $ativo, $destaque, $ordem, $observacoes, $id_prato, $id_restaurante]);
            }
            $msg = "Prato atualizado com sucesso!";
        } else {
            // É UM NOVO PRATO (INSERT)
            $stmt = $pdo->prepare("INSERT INTO pratos (id_restaurante, id_categoria, nome, preco, descricao, imagem, ativo, destaque, ordem, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_restaurante, $id_categoria, $nome, $preco, $descricao, $caminho_imagem, $ativo, $destaque, $ordem, $observacoes]);
            $msg = "Prato cadastrado com sucesso!";
        }

        echo json_encode(["sucesso" => true, "success" => true, "mensagem" => $msg]);

    } catch (PDOException $e) {
        echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "Erro no banco: " . $e->getMessage()]);
    }
    exit;
}

// 3. EXCLUIR PRATO (DELETE)
if ($metodo === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id === 0) {
        $dados = json_decode(file_get_contents("php://input"), true);
        $id = isset($dados['id']) ? intval($dados['id']) : 0;
    }

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM pratos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["sucesso" => true, "success" => true, "mensagem" => "Prato removido do cardápio!"]);
        } catch (PDOException $e) {
            echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "Erro ao deletar: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["sucesso" => false, "success" => false, "mensagem" => "ID do prato não informado."]);
    }
    exit;
}

echo json_encode(["erro" => true, "mensagem" => "Método inválido."]);
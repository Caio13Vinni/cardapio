<?php
require_once 'conexao.php'; // Já configura os headers de CORS e Content-Type JSON

$id_restaurante = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_restaurante === 0) {
    echo json_encode(["sucesso" => false, "mensagem" => "Link de cardápio inválido ou não informado."]);
    exit;
}

try {
    // =========================================================================
    // 1. BUSCA O ESTABELECIMENTO E A APARÊNCIA (Em uma única tacada via JOIN)
    // =========================================================================
    $stmtRest = $pdo->prepare("
        SELECT 
            r.nome, r.descricao, r.endereco, r.bairro, r.cidade, r.estado, r.telefone, r.horarios, r.instagram, r.facebook, r.website,
            a.cor_primaria, a.cor_secundaria, a.logo, a.banner
        FROM restaurantes r
        LEFT JOIN aparencia a ON r.id = a.id_restaurante
        WHERE r.id = ?
    ");
    $stmtRest->execute([$id_restaurante]);
    $restaurante = $stmtRest->fetch();

    if (!$restaurante) {
        echo json_encode(["sucesso" => false, "mensagem" => "Estabelecimento não encontrado."]);
        exit;
    }

    // Monta o objeto visual com segurança de fallbacks
    $aparencia = [
        "cor_primaria" => $restaurante['cor_primaria'] ?? '#ef2b2d',
        "cor_secundaria" => $restaurante['cor_secundaria'] ?? '#1a1a1a',
        "logo" => $restaurante['logo'] ?? '',
        "banner" => $restaurante['banner'] ?? ''
    ];

    // =========================================================================
    // 2. BUSCA TODAS AS CATEGORIAS ATIVAS
    // =========================================================================
    $stmtCat = $pdo->prepare("SELECT id, nome FROM categorias WHERE id_restaurante = ? AND ativo = 1 ORDER BY nome ASC");
    $stmtCat->execute([$id_restaurante]);
    $categorias_db = $stmtCat->fetchAll();

    // =========================================================================
    // 3. BUSCA TODOS OS PRATOS ATIVOS DESTE RESTAURANTE 
    // =========================================================================
    $stmtPratos = $pdo->prepare("
        SELECT id, id_categoria, nome, descricao, preco, imagem 
        FROM pratos 
        WHERE id_restaurante = ? AND ativo = 1 
        ORDER BY ordem ASC, nome ASC
    ");
    $stmtPratos->execute([$id_restaurante]);
    $todos_pratos = $stmtPratos->fetchAll();

    // =========================================================================
    // 4. COSTURA DE ALTA PERFORMANCE (Agrupa os pratos por ID da categoria)
    // =========================================================================
    $mapa_pratos = [];
    foreach ($todos_pratos as $prato) {
        $id_cat = $prato['id_categoria'];
        if (!isset($mapa_pratos[$id_cat])) {
            $mapa_pratos[$id_cat] = [];
        }

        $mapa_pratos[$id_cat][] = [
            "id" => $prato['id'],
            "nome" => $prato['nome'],
            "descricao" => $prato['descricao'] ?? '',
            "preco" => floatval($prato['preco']),
            "imagem" => $prato['imagem'] ?? ''
        ];
    }

    // Monta a árvore final apenas com categorias que possuem itens
    $menu_estruturado = [];
    foreach ($categorias_db as $cat) {
        $id_cat = $cat['id'];
        if (isset($mapa_pratos[$id_cat]) && count($mapa_pratos[$id_cat]) > 0) {
            $menu_estruturado[] = [
                "categoria" => $cat['nome'],
                "itens" => $mapa_pratos[$id_cat]
            ];
        }
    }

    // =========================================================================
    // RETORNO PÚBLICO COMPLETO
    // =========================================================================
    echo json_encode([
        "sucesso" => true,
        "estabelecimento" => $restaurante['nome'],
        // Injetamos o perfil completo aqui dentro para o CardapioCliente.vue não precisar de uma 2ª requisição
        "perfil" => [
            "descricao" => $restaurante['descricao'] ?? '',
            "endereco" => $restaurante['endereco'] ?? '',
            "bairro" => $restaurante['bairro'] ?? '',
            "cidade" => $restaurante['cidade'] ?? '',
            "estado" => $restaurante['estado'] ?? '',
            "telefone" => $restaurante['telefone'] ?? '',
            "horarios" => $restaurante['horarios'] ?? '',
            "instagram" => $restaurante['instagram'] ?? '',
            "facebook" => $restaurante['facebook'] ?? '',
            "website" => $restaurante['website'] ?? ''
        ],
        "visual" => $aparencia,
        "cardapio" => $menu_estruturado
    ]);

} catch (PDOException $e) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro de processamento: " . $e->getMessage()]);
}
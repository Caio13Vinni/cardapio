<?php
require_once 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo !== 'GET') {
    echo json_encode(["erro" => true, "mensagem" => "Método não suportado."]);
    exit;
}

if (!isset($_GET['id_restaurante'])) {
    echo json_encode(["sucesso" => false, "erro" => true, "mensagem" => "ID do restaurante não fornecido."]);
    exit;
}

$id_restaurante = intval($_GET['id_restaurante']);

try {
    // =========================================================================
    // 1. CONTADORES (Categorias, Pratos Totais, Ativos, Inativos)
    // =========================================================================
    $stmtCategorias = $pdo->prepare("SELECT COUNT(*) AS total FROM categorias WHERE id_restaurante = ?");
    $stmtCategorias->execute([$id_restaurante]);
    $totalCategorias = (int) $stmtCategorias->fetch()['total'];

    $stmtPratosTotais = $pdo->prepare("SELECT COUNT(*) AS total FROM pratos WHERE id_restaurante = ?");
    $stmtPratosTotais->execute([$id_restaurante]);
    $totalPratos = (int) $stmtPratosTotais->fetch()['total'];

    $stmtPratosAtivos = $pdo->prepare("SELECT COUNT(*) AS total FROM pratos WHERE id_restaurante = ? AND ativo = 1");
    $stmtPratosAtivos->execute([$id_restaurante]);
    $pratosAtivos = (int) $stmtPratosAtivos->fetch()['total'];

    $pratosInativos = $totalPratos - $pratosAtivos;

    // =========================================================================
    // 2. ÚLTIMOS PRATOS EDITADOS/CRIADOS
    // Obs: a tabela "pratos" não tem coluna de data, então usamos o ID
    // (maior ID = mais recente) como aproximação de "mais recente".
    // =========================================================================
    $stmtRecentes = $pdo->prepare("
        SELECT p.id, p.nome, c.nome AS categoria
        FROM pratos p
        LEFT JOIN categorias c ON p.id_categoria = c.id
        WHERE p.id_restaurante = ?
        ORDER BY p.id DESC
        LIMIT 5
    ");
    $stmtRecentes->execute([$id_restaurante]);
    $recentesDb = $stmtRecentes->fetchAll();

    $recentDishes = array_map(function ($p) {
        return [
            "id" => $p['id'],
            "name" => $p['nome'],
            "category" => $p['categoria'] ?? 'Sem categoria',
            "time" => "Recente"
        ];
    }, $recentesDb);

    // =========================================================================
    // 3. CATEGORIAS COM CONTAGEM DE PRATOS E STATUS
    // =========================================================================
    $stmtCats = $pdo->prepare("
        SELECT 
            c.id, 
            c.nome, 
            c.ativo,
            COUNT(p.id) AS total_pratos
        FROM categorias c
        LEFT JOIN pratos p ON p.id_categoria = c.id
        WHERE c.id_restaurante = ?
        GROUP BY c.id, c.nome, c.ativo
        ORDER BY c.nome ASC
    ");
    $stmtCats->execute([$id_restaurante]);
    $categoriasDb = $stmtCats->fetchAll();

    $categories = array_map(function ($c) {
        return [
            "id" => $c['id'],
            "name" => $c['nome'],
            "count" => (int) $c['total_pratos'],
            "status" => ((int) $c['ativo'] === 1) ? "Ativo" : "Inativo"
        ];
    }, $categoriasDb);

    // =========================================================================
    // RETORNO FINAL
    // =========================================================================
    echo json_encode([
        "sucesso" => true,
        "success" => true,
        "stats" => [
            "categorias" => $totalCategorias,
            "pratosTotais" => $totalPratos,
            "pratosAtivos" => $pratosAtivos,
            "pratosInativos" => $pratosInativos
        ],
        "recentDishes" => $recentDishes,
        "categories" => $categories
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "sucesso" => false,
        "success" => false,
        "mensagem" => "Erro ao carregar dados do dashboard: " . $e->getMessage()
    ]);
}

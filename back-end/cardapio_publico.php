<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once("conexao.php");

$id_restaurante = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_restaurante === 0) {
    echo json_encode(["sucesso" => false, "mensagem" => "Cardápio não encontrado."]);
    exit;
}

// 1. Busca os dados de exibição do restaurante
$query_rest = "SELECT nome, email FROM restaurante WHERE id = $id_restaurante";
$res_rest = mysqli_query($conn, $query_rest);
$restaurante = mysqli_fetch_assoc($res_rest);

if (!$restaurante) {
    echo json_encode(["sucesso" => false, "mensagem" => "Estabelecimento inválido."]);
    exit;
}

// 2. Busca o tema visual (Aparência)
$query_vis = "SELECT cor_primaria, cor_secundaria, logo FROM aparencia WHERE id_restaurante = $id_restaurante";
$res_vis = mysqli_query($conn, $query_vis);
$aparencia = mysqli_fetch_assoc($res_vis) ?: ["cor_primaria" => "#3b82f6", "cor_secundaria" => "#1e3a8a", "logo" => ""];

// 3. Busca apenas as Categorias ATIVAS (ativo = 1)
$query_cat = "SELECT id, nome FROM categoria WHERE id_restaurante = $id_restaurante AND ativo = 1 ORDER BY nome ASC";
$res_cat = mysqli_query($conn, $query_cat);

$menu_estruturado = [];

while ($cat = mysqli_fetch_assoc($res_cat)) {
    $categoria_id = $cat['id'];
    
    // Busca apenas os Produtos ATIVOS (ativo = 1) desta categoria
    $query_prod = "SELECT id, nome, descricao, preco, imagem FROM produto WHERE id_categoria = $categoria_id AND ativo = 1 ORDER BY nome ASC";
    $res_prod = mysqli_query($conn, $query_prod);
    
    $produtos = [];
    while ($prod = mysqli_fetch_assoc($res_prod)) {
        $produtos[] = [
            "id" => $prod['id'],
            "nome" => $prod['nome'],
            "descricao" => $prod['descricao'],
            "preco" => floatval($prod['preco']),
            "imagem" => $prod['imagem']
        ];
    }
    
    // Só exibe a categoria se ela contiver produtos cadastrados e ativos
    if (count($produtos) > 0) {
        $menu_estruturado[] = [
            "categoria" => $cat['nome'],
            "itens" => $produtos
        ];
    }
}

// Retorno Completo do Ecossistema Público
echo json_encode([
    "sucesso" => true,
    "estabelecimento" => $restaurante['nome'],
    "visual" => $aparencia,
    "cardapio" => $menu_estruturado
]);
?>
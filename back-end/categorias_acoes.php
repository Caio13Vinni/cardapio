<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once("conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];

// LISTAR CATEGORIAS
if ($metodo === 'GET') {
    $id_restaurante = isset($_GET['id_restaurante']) ? intval($_GET['id_restaurante']) : 0;

    if ($id_restaurante === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID do restaurante não fornecido."]);
        exit;
    }

    $query = "SELECT * FROM categoria WHERE id_restaurante = $id_restaurante ORDER BY nome ASC";
    $resultado = mysqli_query($conn, $query);
    
    $categorias = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $categorias[] = [
            "id" => $linha['id'],
            "nome" => $linha['nome'],
            "ativo" => (bool)$linha['ativo']
        ];
    }

    echo json_encode(["sucesso" => true, "categorias" => $categorias]);
}

// SALVAR OU EDITAR CATEGORIA
if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!empty($dados['id_restaurante']) && !empty($dados['nome'])) {
        $id_restaurante = intval($dados['id_restaurante']);
        $nome = mysqli_real_escape_string($conn, $dados['nome']);
        $ativo = isset($dados['ativo']) ? intval($dados['ativo']) : 1;

        // Se receber ID, atualiza (Update). Se não, insere novo (Create).
        if (!empty($dados['id'])) {
            $id = intval($dados['id']);
            $query = "UPDATE categoria SET nome = '$nome', ativo = $ativo WHERE id = $id AND id_restaurante = $id_restaurante";
        } else {
            $query = "INSERT INTO categoria (nome, ativo, id_restaurante) VALUES ('$nome', $ativo, $id_restaurante)";
        }

        if (mysqli_query($conn, $query)) {
            echo json_encode(["sucesso" => true, "mensagem" => "Categoria salva com sucesso!"]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao processar no banco de dados."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Dados incompletos."]);
    }
}

// DELETAR CATEGORIA
if ($metodo === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID inválido para exclusão."]);
        exit;
    }

    $query = "DELETE FROM categoria WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["sucesso" => true, "mensagem" => "Categoria excluída com sucesso!"]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao excluir categoria."]);
    }
}
?>
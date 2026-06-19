<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once("conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];

// CARREGAR DADOS DO PERFIL
if ($metodo === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID inválido."]);
        exit;
    }

    $query = "SELECT id, nome, email FROM restaurante WHERE id = $id";
    $resultado = mysqli_query($conn, $query);

    if ($linha = mysqli_fetch_assoc($resultado)) {
        echo json_encode(["sucesso" => true, "restaurante" => $linha]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Restaurante não encontrado."]);
    }
}

// ATUALIZAR DADOS DO PERFIL
if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!empty($dados['id']) && !empty($dados['nome']) && !empty($dados['email'])) {
        $id = intval($dados['id']);
        $nome = mysqli_real_escape_string($conn, $dados['nome']);
        $email = mysqli_real_escape_string($conn, $dados['email']);

        $query = "UPDATE restaurante SET nome = '$nome', email = '$email' WHERE id = $id";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["sucesso" => true, "mensagem" => "Perfil atualizado com sucesso!"]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar dados."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Por favor, preencha os campos obrigatórios."]);
    }
}
?>
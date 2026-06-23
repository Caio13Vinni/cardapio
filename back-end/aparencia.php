<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once("conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];

// OBTER APARÊNCIA ATUAL
if ($metodo === 'GET') {
    $id_restaurante = isset($_GET['id_restaurante']) ? intval($_GET['id_restaurante']) : 0;

    $query = "SELECT * FROM aparencia WHERE id_restaurante = $id_restaurante";
    $resultado = mysqli_query($conn, $query);

    if ($linha = mysqli_fetch_assoc($resultado)) {
        echo json_encode(["sucesso" => true, "aparencia" => $linha]);
    } else {
        // Se não houver configuração, retorna cores padrão para não quebrar o Vue
        echo json_encode([
            "sucesso" => true, 
            "aparencia" => [
                "cor_primaria" => "#3b82f6", 
                "cor_secundaria" => "#1e3a8a", 
                "logo" => ""
            ]
        ]);
    }
}

// SALVAR/ATUALIZAR APARÊNCIA
if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!empty($dados['id_restaurante'])) {
        $id_restaurante = intval($dados['id_restaurante']);
        $cor_primaria = mysqli_real_escape_string($conn, $dados['cor_primaria'] ?? '#3b82f6');
        $cor_secundaria = mysqli_real_escape_string($conn, $dados['cor_secundaria'] ?? '#1e3a8a');
        $logo = mysqli_real_escape_string($conn, $dados['logo'] ?? '');

        // Verifica se já existe um registro de aparência para este restaurante
        $check = mysqli_query($conn, "SELECT id FROM aparencia WHERE id_restaurante = $id_restaurante");

        if (mysqli_num_rows($check) > 0) {
            $query = "UPDATE aparencia SET cor_primaria='$cor_primaria', cor_secundaria='$cor_secundaria', logo='$logo' WHERE id_restaurante=$id_restaurante";
        } else {
            $query = "INSERT INTO aparencia (cor_primaria, cor_secundaria, logo, id_restaurante) VALUES ('$cor_primaria', '$cor_secundaria', '$logo', $id_restaurante)";
        }

        if (mysqli_query($conn, $query)) {
            echo json_encode(["sucesso" => true, "mensagem" => "Aparência salva com sucesso!"]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao salvar design."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Restaurante não identificado."]);
    }
}
?>
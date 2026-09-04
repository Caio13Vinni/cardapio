<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

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
        echo json_encode([
            "sucesso" => true,
            "aparencia" => [
                "cor_primaria"   => "#ef2b2d",
                "cor_secundaria" => "#1a1a1a",
                "logo"           => "",
                "banner"         => ""
            ]
        ]);
    }
    exit;
}

// SALVAR/ATUALIZAR APARÊNCIA (recebe FormData com possível upload)
if ($metodo === 'POST') {
    // FormData vem em $_POST, JSON viria no php://input
    // Tenta pegar id_restaurante de ambos os lugares
    if (!empty($_POST['id_restaurante'])) {
        $dados = $_POST;
    } else {
        $dados = json_decode(file_get_contents("php://input"), true) ?? [];
    }

    $id_restaurante = intval($dados['id_restaurante'] ?? 0);

    if ($id_restaurante === 0) {
        echo json_encode(["sucesso" => false, "mensagem" => "Restaurante não identificado."]);
        exit;
    }

    $cor_primaria   = mysqli_real_escape_string($conn, $dados['cor_primaria']   ?? '#ef2b2d');
    $cor_secundaria = mysqli_real_escape_string($conn, $dados['cor_secundaria'] ?? '#1a1a1a');

    // RN049 - Validação de cores
    if (strtolower($cor_primaria) === strtolower($cor_secundaria)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'As cores primária e secundária não podem ser iguais.']);
        exit;
    }

    // Verifica se as cores são iguais
    if (strtolower($cor_primaria) === strtolower($cor_secundaria)) {
        echo json_encode(["sucesso" => false, "mensagem" => "As cores primária e secundária não podem ser iguais."]);
        exit;
    }

    // === UPLOAD DE LOGO ===
    $logo = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $dir = 'uploads/aparencia/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext      = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $filename = "logo_{$id_restaurante}_" . time() . ".$ext";
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $filename)) {
            $logo = $dir . $filename;
        }
    }

    // === UPLOAD DE BANNER ===
    $banner = null;
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $dir = 'uploads/aparencia/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext      = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
        $filename = "banner_{$id_restaurante}_" . time() . ".$ext";
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $dir . $filename)) {
            $banner = $dir . $filename;
        }
    }

    // Verifica se já existe registro de aparência
    $check = mysqli_query($conn, "SELECT id, logo, banner FROM aparencia WHERE id_restaurante = $id_restaurante");

    if (mysqli_num_rows($check) > 0) {
        $atual = mysqli_fetch_assoc($check);
        // Mantém logo/banner antigo se não enviou novo
        $logo_final   = $logo   ?? mysqli_real_escape_string($conn, $atual['logo']   ?? '');
        $banner_final = $banner ?? mysqli_real_escape_string($conn, $atual['banner'] ?? '');

        $query = "UPDATE aparencia SET cor_primaria='$cor_primaria', cor_secundaria='$cor_secundaria', logo='$logo_final', banner='$banner_final' WHERE id_restaurante=$id_restaurante";
    } else {
        $logo_final   = $logo   ?? '';
        $banner_final = $banner ?? '';
        $query = "INSERT INTO aparencia (cor_primaria, cor_secundaria, logo, banner, id_restaurante) VALUES ('$cor_primaria', '$cor_secundaria', '$logo_final', '$banner_final', $id_restaurante)";
    }

    if (mysqli_query($conn, $query)) {
        echo json_encode(["sucesso" => true, "mensagem" => "Aparência salva com sucesso!"]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao salvar: " . mysqli_error($conn)]);
    }
    exit;
}

echo json_encode(["sucesso" => false, "mensagem" => "Método inválido."]);

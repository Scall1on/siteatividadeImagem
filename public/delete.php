<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../src/ImageService.php';

use App\ImageService;

header('Content-Type: application/json');

// Verifica se é uma requisição DELETE ou POST
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Obtém o filename
$filename = null;
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $filename = $data['filename'] ?? null;
} else {
    $filename = $_POST['filename'] ?? null;
}

if (!$filename) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nome do arquivo não fornecido']);
    exit;
}

$imageService = new ImageService(__DIR__ . '/uploads/');
$result = $imageService->deleteImage($filename);

if ($result['success']) {
    $_SESSION['success'] = 'Imagem deletada com sucesso!';
    http_response_code(200);
    echo json_encode($result);
} else {
    $_SESSION['error'] = $result['error'];
    http_response_code(400);
    echo json_encode($result);
}

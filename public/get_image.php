<?php
date_default_timezone_set('America/Sao_Paulo');
require_once '../src/ImageService.php';

use App\ImageService;

header('Content-Type: application/json');

// Verifica se é uma requisição GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Verifica se o filename foi fornecido
if (!isset($_GET['filename'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nome do arquivo não fornecido']);
    exit;
}

$imageService = new ImageService(__DIR__ . '/uploads/');
$imageInfo = $imageService->getImageInfo($_GET['filename']);

if ($imageInfo) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'image' => $imageInfo
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Imagem não encontrada'
    ]);
}

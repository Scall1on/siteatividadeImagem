<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../src/ImageService.php';

use App\ImageService;

header('Content-Type: application/json');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Verifica se um arquivo foi enviado
if (!isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo foi enviado']);
    exit;
}

// Determina a opção de nome escolhida
$nameOption = isset($_POST['nameOption']) ? $_POST['nameOption'] : 'auto';
$customName = isset($_POST['customFileName']) ? trim($_POST['customFileName']) : '';

$imageService = new ImageService(__DIR__ . '/uploads/');
$result = $imageService->uploadImage($_FILES['image'], $nameOption, $customName);

if ($result['success']) {
    $_SESSION['success'] = 'Imagem enviada com sucesso!';
    http_response_code(200);
    echo json_encode($result);
} else {
    $_SESSION['error'] = implode(', ', $result['errors']);
    http_response_code(400);
    echo json_encode($result);
}

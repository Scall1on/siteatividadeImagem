<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../src/ImageService.php';

use App\ImageService;

$imageService = new ImageService(__DIR__ . '/uploads/');
$images = $imageService->listImages();

// Busca mensagens da sessão
$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria de Imagens</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Galeria de Imagens</h1>
        
        <!-- Mensagens -->
        <?php if ($successMessage): ?>
            <div class="message success" id="success-message">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="message error" id="error-message">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Grid de Imagens -->
        <div class="gallery-grid">
            <!-- Botão de adicionar -->
            <a href="upload.php" class="add-button" id="add-image-btn">
                <span class="plus-icon">+</span>
                <span class="add-text">Adicionar Imagem</span>
            </a>

            <!-- Imagens existentes -->
            <?php foreach ($images as $image): ?>
                <div class="image-card" data-filename="<?= htmlspecialchars($image['filename']) ?>">
                    <img src="<?= htmlspecialchars($image['url']) ?>" 
                         alt="<?= htmlspecialchars($image['filename']) ?>"
                         loading="lazy">
                    <div class="image-overlay">
                        <button class="btn-view" onclick="viewImage('<?= htmlspecialchars($image['filename']) ?>')">
                            Ver Detalhes
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($images)): ?>
            <div class="empty-state">
                <p>Nenhuma imagem foi enviada ainda.</p>
                <p>Clique no botão + para adicionar sua primeira imagem!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de detalhes da imagem -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

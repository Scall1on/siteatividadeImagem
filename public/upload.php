<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Imagem</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="upload-container">
            <h1>Upload de Imagem</h1>
            
            <form id="uploadForm" action="upload_process.php" method="POST" enctype="multipart/form-data">
                <input type="file" 
                       name="image" 
                       id="imageInput" 
                       accept="image/*" 
                       required
                       style="position: absolute; opacity: 0; pointer-events: none;">
                       
                <div class="upload-area" id="uploadArea">
                    <div class="upload-text" id="uploadText">
                        <span class="upload-icon">📁</span>
                        <p>Clique para selecionar uma imagem</p>
                        <p class="upload-hint">ou arraste e solte aqui</p>
                        <p class="upload-info">Formatos: JPEG, PNG, GIF, WebP</p>
                        <p class="upload-info" style="color: #e74c3c; font-weight: bold;">Tamanho máximo: 10MB</p>
                    </div>
                </div>

                <div id="preview" class="preview"></div>
                
                <div id="renameSection" style="display: none; margin: 20px 0;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #4a90e2;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 1.1rem; margin-bottom: 15px;">
                            <input type="radio" name="nameOption" value="original" id="keepOriginalName" checked style="width: 20px; height: 20px;">
                            <span>Manter nome original do arquivo</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 1.1rem; margin-bottom: 10px;">
                            <input type="radio" name="nameOption" value="custom" id="customName" style="width: 20px; height: 20px;">
                            <span>Definir nome personalizado</span>
                        </label>
                        
                        <div id="customNameInput" style="display: none; margin-left: 30px; margin-top: 10px;">
                            <input type="text" 
                                   name="customFileName" 
                                   id="customFileNameInput" 
                                   placeholder="Digite o nome do arquivo (sem extensão)"
                                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 1rem;">
                            <p style="margin-top: 5px; color: #666; font-size: 0.85rem;">
                                A extensão será mantida automaticamente
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="changeImageBtn" style="display: none;">
                        Escolher Outra Imagem
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        Enviar Imagem
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>

                <div id="uploadProgress" class="upload-progress" style="display: none;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <p id="progressText">Enviando...</p>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

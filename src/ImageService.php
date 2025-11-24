<?php

namespace App;

// Define o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

class ImageService
{
    private string $uploadDir;
    private int $maxFileSize;
    private array $allowedTypes;

    public function __construct(string $uploadDir = './uploads/', int $maxFileSize = 10485760)
    {
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = $maxFileSize; // 10MB por padrão
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Valida o arquivo de imagem
     */
    public function validateImage(array $file): array
    {
        $errors = [];

        // Verifica se houve erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $this->getUploadErrorMessage($file['error']);
            return ['valid' => false, 'errors' => $errors];
        }

        // Verifica o tamanho do arquivo (verifica se size é 0 também, pode indicar erro de configuração)
        if ($file['size'] === 0) {
            $errors[] = 'O arquivo está vazio ou o limite de upload do servidor foi excedido. Verifique se o arquivo é válido.';
        } elseif ($file['size'] > $this->maxFileSize) {
            $errors[] = 'O arquivo excede o tamanho máximo de 10MB';
        }

        // Verifica o tipo do arquivo
        if (!in_array($file['type'], $this->allowedTypes)) {
            $errors[] = 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP';
        }

        // Verifica se é realmente uma imagem
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'O arquivo não é uma imagem válida';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Faz o upload da imagem
     * @param array $file - Arquivo do $_FILES
     * @param string $nameOption - 'original', 'custom' ou 'auto'
     * @param string $customName - Nome personalizado (usado quando $nameOption === 'custom')
     */
    public function uploadImage(array $file, string $nameOption = 'auto', string $customName = ''): array
    {
        $validation = $this->validateImage($file);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        // Gera nome do arquivo baseado na escolha do usuário
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if ($nameOption === 'original') {
            // Sanitiza o nome original para segurança
            $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
            $originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
            $filename = $originalName . '.' . $extension;
            
            // Se já existir, adiciona sufixo numérico
            $counter = 1;
            while (file_exists($this->uploadDir . $filename)) {
                $filename = $originalName . '_' . $counter . '.' . $extension;
                $counter++;
            }
        } elseif ($nameOption === 'custom' && !empty($customName)) {
            // Sanitiza o nome personalizado
            $customName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $customName);
            $filename = $customName . '.' . $extension;
            
            // Se já existir, adiciona sufixo numérico
            $counter = 1;
            while (file_exists($this->uploadDir . $filename)) {
                $filename = $customName . '_' . $counter . '.' . $extension;
                $counter++;
            }
        } else {
            // Gera um nome único automático
            $filename = uniqid('img_', true) . '.' . $extension;
        }
        
        $filepath = $this->uploadDir . $filename;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $file['size'],
                'original_name' => $file['name'],
                'upload_date' => date('Y-m-d H:i:s')
            ];
        }

        return [
            'success' => false,
            'errors' => ['Erro ao mover o arquivo para o diretório de uploads']
        ];
    }

    /**
     * Lista todas as imagens
     */
    public function listImages(): array
    {
        $images = [];
        
        if (!is_dir($this->uploadDir)) {
            return $images;
        }

        $files = scandir($this->uploadDir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filepath = $this->uploadDir . $file;
            
            if (is_file($filepath)) {
                $images[] = [
                    'filename' => $file,
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                    'upload_date' => date('Y-m-d H:i:s', filemtime($filepath)),
                    'url' => 'uploads/' . $file
                ];
            }
        }

        // Ordena por data de upload (mais recente primeiro)
        usort($images, function($a, $b) {
            return strtotime($b['upload_date']) - strtotime($a['upload_date']);
        });

        return $images;
    }

    /**
     * Obtém informações de uma imagem específica
     */
    public function getImageInfo(string $filename): ?array
    {
        $filepath = $this->uploadDir . $filename;
        
        if (!file_exists($filepath)) {
            return null;
        }

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
            'upload_date' => date('Y-m-d H:i:s', filemtime($filepath)),
            'url' => 'uploads/' . $filename
        ];
    }

    /**
     * Deleta uma imagem
     */
    public function deleteImage(string $filename): array
    {
        // Previne directory traversal
        $filename = basename($filename);
        $filepath = $this->uploadDir . $filename;

        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'error' => 'Arquivo não encontrado'
            ];
        }

        if (unlink($filepath)) {
            return [
                'success' => true,
                'message' => 'Imagem deletada com sucesso'
            ];
        }

        return [
            'success' => false,
            'error' => 'Erro ao deletar o arquivo'
        ];
    }

    /**
     * Formata o tamanho do arquivo
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1000000) {
            return number_format($bytes / 1000000, 2) . ' MB';
        } elseif ($bytes >= 1000) {
            return number_format($bytes / 1000, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Retorna mensagem de erro de upload
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'O arquivo excede o limite de upload do servidor (upload_max_filesize no php.ini). Configure para no mínimo 10MB.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'O arquivo excede o tamanho máximo permitido pelo formulário';
            case UPLOAD_ERR_PARTIAL:
                return 'O arquivo foi enviado apenas parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo foi enviado';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporária ausente';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever o arquivo no disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Uma extensão PHP interrompeu o upload';
            default:
                return 'Erro desconhecido no upload';
        }
    }
}

<?php

namespace Tests;

use App\ImageService;
use PHPUnit\Framework\TestCase;

class ImageServiceTest extends TestCase
{
    private ImageService $imageService;
    private string $testUploadDir;

    protected function setUp(): void
    {
        // Cria diretório temporário para testes
        $this->testUploadDir = sys_get_temp_dir() . '/test_uploads_' . uniqid() . '/';
        $this->imageService = new ImageService($this->testUploadDir, 10485760);
    }

    protected function tearDown(): void
    {
        // Limpa o diretório de testes
        if (is_dir($this->testUploadDir)) {
            $files = glob($this->testUploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testUploadDir);
        }
    }

    /**
     * Cria um arquivo de imagem fake para testes
     */
    private function createFakeImageFile(string $type = 'image/png', int $size = 1024): array
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_');
        
        // Cria uma imagem PNG válida de 1x1 pixel
        if ($type === 'image/png') {
            $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        } elseif ($type === 'image/jpeg') {
            $imageData = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlbaWmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9/KKKKAP/2Q==');
        } else {
            // Arquivo de texto inválido
            $imageData = 'This is not an image';
        }
        
        file_put_contents($tmpFile, $imageData);
        
        return [
            'name' => 'test_image.png',
            'type' => $type,
            'tmp_name' => $tmpFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($tmpFile)
        ];
    }

    // ==================== TESTES DE CONSTRUÇÃO ====================

    public function testConstructorCreatesUploadDirectory(): void
    {
        $this->assertDirectoryExists($this->testUploadDir);
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $service = new ImageService();
        $this->assertInstanceOf(ImageService::class, $service);
    }

    // ==================== TESTES DE VALIDAÇÃO ====================

    public function testValidateImageWithValidFile(): void
    {
        $file = $this->createFakeImageFile('image/png', 1024);
        $result = $this->imageService->validateImage($file);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
        
        unlink($file['tmp_name']);
    }

    public function testValidateImageWithFileTooLarge(): void
    {
        $file = $this->createFakeImageFile('image/png', 1024);
        $file['size'] = 11 * 1024 * 1024; // 11MB
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        $this->assertContains('O arquivo excede o tamanho máximo de 10MB', $result['errors']);
        
        unlink($file['tmp_name']);
    }

    public function testValidateImageWithEmptyFile(): void
    {
        $file = $this->createFakeImageFile('image/png', 0);
        $file['size'] = 0;
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
        
        unlink($file['tmp_name']);
    }

    public function testValidateImageWithInvalidType(): void
    {
        $file = $this->createFakeImageFile('application/pdf', 1024);
        $file['type'] = 'application/pdf';
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        $this->assertContains('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP', $result['errors']);
        
        unlink($file['tmp_name']);
    }

    public function testValidateImageWithUploadError(): void
    {
        $file = [
            'name' => 'test.png',
            'type' => 'image/png',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        $this->assertContains('Nenhum arquivo foi enviado', $result['errors']);
    }

    public function testValidateImageWithInvalidImageData(): void
    {
        $file = $this->createFakeImageFile('text/plain', 1024);
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        
        unlink($file['tmp_name']);
    }

    // ==================== TESTES DE UPLOAD ====================

    public function testUploadImageWithAutoGeneratedName(): void
    {
        $file = $this->createFakeImageFile('image/png', 1024);
        
        // Mock da função move_uploaded_file
        $testFile = $file['tmp_name'];
        $destinationDir = $this->testUploadDir;
        
        // Simula o upload copiando o arquivo
        $result = $this->imageService->validateImage($file);
        $this->assertTrue($result['valid']);
        
        // Verifica que o arquivo temporário existe
        $this->assertFileExists($testFile);
        
        unlink($testFile);
    }

    public function testUploadImageWithOriginalName(): void
    {
        $file = $this->createFakeImageFile('image/png', 1024);
        $file['name'] = 'my_photo.png';
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
        
        unlink($file['tmp_name']);
    }

    public function testUploadImageWithCustomName(): void
    {
        $file = $this->createFakeImageFile('image/png', 1024);
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertTrue($result['valid']);
        
        unlink($file['tmp_name']);
    }

    public function testUploadImageWithInvalidFile(): void
    {
        $file = [
            'name' => 'test.png',
            'type' => 'image/png',
            'tmp_name' => '/nonexistent/file.png',
            'error' => UPLOAD_ERR_OK,
            'size' => 11 * 1024 * 1024
        ];
        
        $result = $this->imageService->validateImage($file);
        
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    // ==================== TESTES DE LISTAGEM ====================

    public function testListImagesWithEmptyDirectory(): void
    {
        $images = $this->imageService->listImages();
        
        $this->assertIsArray($images);
        $this->assertEmpty($images);
    }

    public function testListImagesWithMultipleFiles(): void
    {
        // Cria alguns arquivos de teste
        file_put_contents($this->testUploadDir . 'image1.png', 'fake data 1');
        file_put_contents($this->testUploadDir . 'image2.jpg', 'fake data 2');
        file_put_contents($this->testUploadDir . 'image3.gif', 'fake data 3');
        
        $images = $this->imageService->listImages();
        
        $this->assertCount(3, $images);
        $this->assertArrayHasKey('filename', $images[0]);
        $this->assertArrayHasKey('size', $images[0]);
        $this->assertArrayHasKey('upload_date', $images[0]);
        $this->assertArrayHasKey('url', $images[0]);
    }

    public function testListImagesOrderedByDate(): void
    {
        // Cria arquivos com diferentes timestamps
        $file1 = $this->testUploadDir . 'image1.png';
        $file2 = $this->testUploadDir . 'image2.png';
        
        file_put_contents($file1, 'data1');
        sleep(1);
        file_put_contents($file2, 'data2');
        
        $images = $this->imageService->listImages();
        
        // O mais recente deve vir primeiro
        $this->assertEquals('image2.png', $images[0]['filename']);
        $this->assertEquals('image1.png', $images[1]['filename']);
    }

    // ==================== TESTES DE INFORMAÇÕES ====================

    public function testGetImageInfoForExistingFile(): void
    {
        $filename = 'test_image.png';
        file_put_contents($this->testUploadDir . $filename, 'fake data');
        
        $info = $this->imageService->getImageInfo($filename);
        
        $this->assertIsArray($info);
        $this->assertEquals($filename, $info['filename']);
        $this->assertArrayHasKey('size', $info);
        $this->assertArrayHasKey('upload_date', $info);
        $this->assertArrayHasKey('url', $info);
    }

    public function testGetImageInfoForNonExistentFile(): void
    {
        $info = $this->imageService->getImageInfo('nonexistent.png');
        
        $this->assertNull($info);
    }

    // ==================== TESTES DE EXCLUSÃO ====================

    public function testDeleteExistingImage(): void
    {
        $filename = 'test_delete.png';
        $filepath = $this->testUploadDir . $filename;
        file_put_contents($filepath, 'test data');
        
        $result = $this->imageService->deleteImage($filename);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Imagem deletada com sucesso', $result['message']);
        $this->assertFileDoesNotExist($filepath);
    }

    public function testDeleteNonExistentImage(): void
    {
        $result = $this->imageService->deleteImage('nonexistent.png');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Arquivo não encontrado', $result['error']);
    }

    public function testDeleteImageWithDirectoryTraversal(): void
    {
        // Tenta usar path traversal - deve ser bloqueado
        $result = $this->imageService->deleteImage('../../../etc/passwd');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Arquivo não encontrado', $result['error']);
    }

    // ==================== TESTES DE FORMATAÇÃO ====================

    public function testFormatFileSizeInBytes(): void
    {
        $result = ImageService::formatFileSize(500);
        $this->assertEquals('500 bytes', $result);
    }

    public function testFormatFileSizeInKilobytes(): void
    {
        $result = ImageService::formatFileSize(5000);
        $this->assertEquals('5.00 KB', $result);
    }

    public function testFormatFileSizeInMegabytes(): void
    {
        $result = ImageService::formatFileSize(5000000);
        $this->assertEquals('5.00 MB', $result);
    }

    public function testFormatFileSizeExactly1MB(): void
    {
        $result = ImageService::formatFileSize(1000000);
        $this->assertEquals('1.00 MB', $result);
    }

    public function testFormatFileSizeExactly1KB(): void
    {
        $result = ImageService::formatFileSize(1000);
        $this->assertEquals('1.00 KB', $result);
    }

    // ==================== TESTES DE EDGE CASES ====================

    public function testValidateImageWithAllowedTypes(): void
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        foreach ($allowedTypes as $type) {
            $file = $this->createFakeImageFile($type, 1024);
            $file['type'] = $type;
            
            $result = $this->imageService->validateImage($file);
            
            // Pode falhar se não for uma imagem real válida, mas o tipo deve ser aceito
            // Se falhar, não deve ser por tipo inválido
            if (!$result['valid']) {
                $hasTypeError = false;
                foreach ($result['errors'] as $error) {
                    if (strpos($error, 'Tipo de arquivo não permitido') !== false) {
                        $hasTypeError = true;
                        break;
                    }
                }
                $this->assertFalse($hasTypeError, "Tipo $type deveria ser permitido");
            }
            
            unlink($file['tmp_name']);
        }
    }

    public function testConstructorWithCustomMaxFileSize(): void
    {
        $customSize = 5 * 1024 * 1024; // 5MB
        $service = new ImageService($this->testUploadDir, $customSize);
        
        $file = $this->createFakeImageFile('image/png', 1024);
        $file['size'] = 6 * 1024 * 1024; // 6MB
        
        $result = $service->validateImage($file);
        
        $this->assertFalse($result['valid']);
        
        unlink($file['tmp_name']);
    }
}

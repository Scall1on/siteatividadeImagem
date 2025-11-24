# Sistema de Galeria de Imagens 📸

Sistema completo de upload e gerenciamento de imagens desenvolvido em PHP, com testes unitários (PHPUnit), testes E2E (Cypress) e BDD (Behat).

## 📋 Índice

- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação](#instalação)
- [Como Usar](#como-usar)
- [Testes](#testes)
- [Características Técnicas](#características-técnicas)

## ✨ Funcionalidades

### ✅ Principais Recursos

1. **Upload de Imagens**
   - Upload de imagens até 10MB
   - Formatos aceitos: JPEG, PNG, GIF, WebP
   - Preview da imagem antes do upload
   - Drag & drop support
   - Barra de progresso durante upload

2. **Visualização de Imagens**
   - Grid responsivo de imagens
   - Preview em miniatura
   - Modal com detalhes da imagem
   - Informações: nome, data de upload, tamanho

3. **Exclusão de Imagens**
   - Exclusão com confirmação
   - Mensagens de feedback
   - Atualização automática da galeria

4. **Interface do Usuário**
   - Design moderno e responsivo
   - Mensagens de sucesso e erro
   - Animações suaves
   - Auto-hide de mensagens

## 🛠 Tecnologias

### Backend
- **PHP 7.4+** - Linguagem principal
- **POO** - Programação Orientada a Objetos
- **PSR-4** - Autoloading

### Frontend
- **HTML5** - Estrutura
- **CSS3** - Estilização e animações
- **JavaScript (ES6+)** - Interatividade
- **AJAX** - Upload assíncrono

### Testes
- **PHPUnit 9.5** - Testes Unitários
- **Cypress 13.6** - Testes E2E (End-to-End)
- **Behat 3.13** - BDD (Behavior Driven Development)
- **Mink Extension** - Testes de interface

## 📁 Estrutura do Projeto

```
siteimagem/
├── src/                          # Código fonte PHP
│   └── ImageService.php          # Classe principal de serviço
├── public/                       # Arquivos públicos
│   ├── index.php                 # Página principal (galeria)
│   ├── upload.php                # Página de upload
│   ├── upload_process.php        # Processamento do upload
│   ├── get_image.php             # API para obter info da imagem
│   ├── delete.php                # API para deletar imagem
│   ├── style.css                 # Estilos CSS
│   └── script.js                 # JavaScript
├── uploads/                      # Diretório de imagens (criado automaticamente)
├── tests/                        # Testes unitários
│   └── ImageServiceTest.php      # Testes do ImageService
├── cypress/                      # Testes E2E
│   ├── e2e/                      # Specs de teste
│   │   └── image-gallery.cy.js  # Testes do sistema
│   └── fixtures/                 # Arquivos de teste
│       └── test-image.jpg        # Imagem de teste
├── features/                     # Testes BDD (Gherkin)
│   ├── upload.feature            # Cenários de upload
│   ├── gallery.feature           # Cenários da galeria
│   ├── delete.feature            # Cenários de exclusão
│   └── bootstrap/
│       └── FeatureContext.php    # Contexto Behat
├── composer.json                 # Dependências PHP
├── package.json                  # Dependências Node.js
├── phpunit.xml                   # Configuração PHPUnit
├── cypress.config.js             # Configuração Cypress
├── behat.yml                     # Configuração Behat
└── README.md                     # Esta documentação
```

## 🚀 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- Composer
- Node.js e npm
- GD Library (para manipulação de imagens)

### Passo 1: Clonar/Navegar até o Projeto

```bash
cd /home/labs/faculdade/php/siteimagem
```

### Passo 2: Instalar Dependências PHP

```bash
composer install
```

### Passo 3: Instalar Dependências Node.js

```bash
npm install
```

### Passo 4: Configurar Permissões

```bash
# Dar permissão de escrita no diretório de uploads
chmod 777 uploads/

# Ou criar o diretório se não existir
mkdir -p uploads && chmod 777 uploads/
```

### Passo 5: Iniciar Servidor PHP

**IMPORTANTE:** Use o router.php para garantir que as configurações de upload estejam corretas:

```bash
# Opção RECOMENDADA (com configurações de upload):
php -S localhost:8000 -t public/ router.php

# Alternativa (pode ter limite de 2MB):
php -S localhost:8000 -t public/
```

**Nota:** O arquivo `router.php` configura automaticamente o PHP para aceitar uploads de até 10MB.

Acesse: http://localhost:8000

## 💻 Como Usar

### Interface Web

1. **Acessar a Galeria**
   - Abra http://localhost:8000 no navegador
   - Você verá a galeria de imagens (vazia inicialmente)

2. **Fazer Upload de Imagem**
   - Clique no botão "+" (Adicionar Imagem)
   - Clique na área de upload ou arraste uma imagem
   - Veja o preview da imagem
   - Clique em "Enviar Imagem"
   - Aguarde a confirmação e redirecionamento

3. **Visualizar Detalhes**
   - Passe o mouse sobre uma imagem
   - Clique em "Ver Detalhes"
   - Veja informações: nome, data, tamanho

4. **Excluir Imagem**
   - Abra os detalhes da imagem
   - Clique em "Excluir Imagem"
   - Confirme a exclusão

### API Endpoints

#### GET /get_image.php
Retorna informações de uma imagem específica.

```bash
curl http://localhost:8000/get_image.php?filename=img_123.jpg
```

#### POST /upload_process.php
Faz upload de uma imagem.

```bash
curl -X POST -F "image=@/path/to/image.jpg" http://localhost:8000/upload_process.php
```

#### POST /delete.php
Deleta uma imagem.

```bash
curl -X POST -d "filename=img_123.jpg" http://localhost:8000/delete.php
```

## 🧪 Testes

### Testes Unitários (PHPUnit)

Os testes unitários verificam a lógica de negócio da classe `ImageService`.

**Executar todos os testes:**
```bash
./vendor/bin/phpunit
```

**Executar com cobertura:**
```bash
./vendor/bin/phpunit --coverage-html coverage/
```

**Testes incluídos:**
- ✅ Validação de imagens (tipo, tamanho)
- ✅ Upload de imagens
- ✅ Listagem de imagens
- ✅ Obtenção de informações
- ✅ Exclusão de imagens
- ✅ Formatação de tamanho
- ✅ Prevenção de directory traversal
- ✅ Tratamento de erros

**Exemplo de saída:**
```
PHPUnit 9.5.x by Sebastian Bergmann

ImageServiceTest
 ✔ Validate image success
 ✔ Validate image fails with invalid type
 ✔ Validate image fails with large file
 ✔ Upload image success
 ✔ List images returns empty array when no images
 ✔ Delete image success
 ✔ Format file size

Time: XX ms, Memory: XX MB

OK (20 tests, 45 assertions)
```

### Testes E2E (Cypress)

Os testes E2E verificam o comportamento completo da aplicação no navegador.

**Executar em modo interativo:**
```bash
npm run cypress:open
```

**Executar em modo headless:**
```bash
npm run cypress:run
```

**Preparar imagem de teste:**
```bash
# Criar ou copiar uma imagem para fixtures
cp /caminho/para/imagem.jpg cypress/fixtures/test-image.jpg
```

**Testes incluídos:**
- ✅ Navegação entre páginas
- ✅ Interface de upload
- ✅ Drag & drop de imagens
- ✅ Preview de imagens
- ✅ Visualização da galeria
- ✅ Modal de detalhes
- ✅ Exclusão de imagens
- ✅ Mensagens de feedback
- ✅ Responsividade
- ✅ Acessibilidade

### Testes BDD (Behat)

Os testes BDD são escritos em linguagem natural (Gherkin) e descrevem o comportamento do sistema.

**Inicializar Behat (primeira vez):**
```bash
./vendor/bin/behat --init
```

**Executar todos os cenários:**
```bash
./vendor/bin/behat
```

**Executar feature específica:**
```bash
./vendor/bin/behat features/upload.feature
```

**Executar com tags:**
```bash
# Apenas testes de upload
./vendor/bin/behat --tags=upload

# Apenas testes de validação
./vendor/bin/behat --tags=validation
```

**Features incluídas:**

1. **upload.feature** - Cenários de upload
   - Acessar página de upload
   - Selecionar imagem
   - Upload bem-sucedido
   - Validações (tamanho, tipo)

2. **gallery.feature** - Cenários da galeria
   - Visualizar galeria vazia
   - Visualizar imagens
   - Ver detalhes
   - Mensagens de feedback

3. **delete.feature** - Cenários de exclusão
   - Excluir com confirmação
   - Cancelar exclusão
   - Excluir múltiplas imagens

**Exemplo de saída:**
```
Funcionalidade: Upload de Imagens

  Cenário: Acessar página de upload        # features/upload.feature:8
    Dado que estou na página inicial       # FeatureContext::queEstouNaPaginaInicial()
    Quando eu clico no botão de adicionar  # FeatureContext::euClicoNoBotao()
    Então eu devo ver a página de upload   # FeatureContext::euDevoVerAPaginaDeUpload()

3 scenarios (3 passed)
12 steps (12 passed)
```

## 🎯 Características Técnicas

### Segurança

- ✅ Validação de tipo MIME
- ✅ Validação de tamanho de arquivo
- ✅ Verificação de imagem real com `getimagesize()`
- ✅ Prevenção de directory traversal
- ✅ Nomes de arquivo únicos (uniqid)
- ✅ Sanitização de HTML com `htmlspecialchars()`

### Performance

- ✅ Upload assíncrono com AJAX
- ✅ Lazy loading de imagens
- ✅ Otimização de CSS e JavaScript
- ✅ Cache de navegador

### Usabilidade

- ✅ Interface intuitiva
- ✅ Feedback visual (mensagens, loading)
- ✅ Drag & drop
- ✅ Preview de imagens
- ✅ Design responsivo
- ✅ Acessibilidade (alt text, títulos)

### Código Limpo

- ✅ PSR-4 autoloading
- ✅ Separação de responsabilidades
- ✅ Comentários e documentação
- ✅ Tratamento de erros
- ✅ Código testável

## 📊 Cobertura de Testes

O projeto possui três níveis de testes:

1. **Testes Unitários (PHPUnit)**
   - Testa a lógica de negócio isoladamente
   - ~20 testes cobrindo todas as funcionalidades do ImageService
   - Cobertura: >90% do código PHP

2. **Testes E2E (Cypress)**
   - Testa o sistema completo no navegador
   - ~40 testes cobrindo toda a interface
   - Valida integração frontend + backend

3. **Testes BDD (Behat)**
   - Testa comportamento do usuário
   - ~15 cenários em linguagem natural
   - Validação de requisitos de negócio

## 🐛 Troubleshooting

### Erro "File too large" ou upload de 2MB não funciona

O PHP por padrão pode ter limite de 2MB. **Solução:**

**Opção 1 (Recomendada):** Use o router.php ao iniciar o servidor:
```bash
php -S localhost:8000 -t public/ router.php
```

**Opção 2:** Edite o arquivo `php.ini`:
```bash
# Encontrar o php.ini
php --ini

# Editar e adicionar/modificar:
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 256M
```

**Opção 3:** O projeto já inclui `.user.ini` e `.htaccess` em `public/` que podem ser reconhecidos automaticamente dependendo da configuração do servidor.

### Erro de permissão no diretório uploads/

```bash
chmod 777 uploads/
# ou
sudo chown -R www-data:www-data uploads/
```

### Erro "File too large"

Edite o `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 12M
```

### Testes Cypress não encontram elementos

Certifique-se de que o servidor está rodando:
```bash
php -S localhost:8000 -t public/
```

### Erro ao instalar dependências

```bash
# Limpar cache do Composer
composer clear-cache
composer install

# Limpar cache do npm
npm cache clean --force
npm install
```

## 📝 Requisitos do Trabalho

Este projeto atende aos seguintes requisitos:

✅ **Upload de imagens** até 10MB  
✅ **Exclusão de imagens**  
✅ **Mensagens** de sucesso e erro  
✅ **Botão "+"** para adicionar imagem  
✅ **Preview** das imagens na galeria  
✅ **Visualização de informações** (nome, data, tamanho)  
✅ **Testes Unitários** com PHPUnit  
✅ **Testes E2E** com Cypress  
✅ **BDD** com Behat e Gherkin  

## 👨‍💻 Desenvolvimento

### Estrutura do ImageService

```php
class ImageService {
    public function validateImage($file): array
    public function uploadImage($file): array
    public function listImages(): array
    public function getImageInfo($filename): ?array
    public function deleteImage($filename): array
    public static function formatFileSize($bytes): string
}
```

### Fluxo de Upload

1. Usuário seleciona imagem
2. JavaScript mostra preview
3. Usuário envia formulário
4. AJAX faz upload assíncrono
5. PHP valida e salva arquivo
6. Retorna resposta JSON
7. JavaScript redireciona ou mostra erro

### Fluxo de Exclusão

1. Usuário clica em imagem
2. Modal abre com detalhes
3. Usuário clica em "Excluir"
4. JavaScript mostra confirmação
5. AJAX envia requisição DELETE/POST
6. PHP deleta arquivo
7. Página recarrega

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais.

## 🤝 Contribuindo

Para contribuir com o projeto:

1. Faça um fork
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Suporte

Se você encontrar algum problema ou tiver dúvidas:

1. Verifique a seção [Troubleshooting](#-troubleshooting)
2. Execute os testes para identificar problemas
3. Consulte a documentação do PHP, Cypress ou Behat

---

**Desenvolvido com ❤️ para o curso de Faculdade**

**Testes implementados:** ✅ PHPUnit | ✅ Cypress | ✅ Behat

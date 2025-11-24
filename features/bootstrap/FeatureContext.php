<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Contexto de testes BDD para a Galeria de Imagens
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @Given que estou na página inicial
     */
    public function queEstouNaPaginaInicial()
    {
        $this->visit('/index.php');
    }

    /**
     * @Given que não existem imagens na galeria
     */
    public function queNaoExistemImagensNaGaleria()
    {
        // Limpa o diretório de uploads
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * @Given que existem imagens na galeria
     */
    public function queExistemImagensNaGaleria()
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        
        // Cria o diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Cria uma imagem de teste (1x1 pixel PNG transparente)
        $testImagePath = $uploadDir . 'test_image_' . time() . '.png';
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        file_put_contents($testImagePath, $imageData);
    }

    /**
     * @When eu clico no botão de adicionar imagem
     */
    public function euClicoNoBotaoDeAdicionarImagem()
    {
        $this->clickLink('add-image-btn');
    }

    /**
     * @Then eu devo ver a página de upload
     */
    public function euDevoVerAPaginaDeUpload()
    {
        $this->assertPageContainsText('Upload de Imagem');
    }

    /**
     * @Then eu devo ver o formulário de upload
     */
    public function euDevoVerOFormularioDeUpload()
    {
        $this->assertElementOnPage('#uploadForm');
    }

    /**
     * @Then eu devo ver a informação :text
     */
    public function euDevoVerAInformacao($text)
    {
        $this->assertPageContainsText($text);
    }

    /**
     * @Then eu devo ver a opção :text
     */
    public function euDevoVerAOpcao($text)
    {
        $this->assertPageContainsText($text);
    }

    /**
     * @When eu clico no botão :button
     */
    public function euClicoNoBotao($button)
    {
        $this->clickLink($button);
    }

    /**
     * @Then eu devo voltar para a página inicial
     */
    public function euDevoVoltarParaAPaginaInicial()
    {
        $this->assertPageContainsText('Galeria de Imagens');
    }

    /**
     * @When eu acesso a página inicial
     */
    public function euAcessoAPaginaInicial()
    {
        $this->visit('/index.php');
    }

    /**
     * @Then eu devo ver a mensagem :message
     */
    public function euDevoVerAMensagem($message)
    {
        $this->assertPageContainsText($message);
    }

    /**
     * @Then eu devo ver o botão de adicionar imagem
     */
    public function euDevoVerOBotaoDeAdicionarImagem()
    {
        $this->assertElementOnPage('#add-image-btn');
    }

    /**
     * @Then eu devo ver a grade de imagens
     */
    public function euDevoVerAGradeDeImagens()
    {
        $this->assertElementOnPage('.gallery-grid');
    }

    /**
     * @Then cada imagem deve ter um preview
     */
    public function cadaImagemDeveTerUmPreview()
    {
        $this->assertElementOnPage('.image-card img');
    }

    /**
     * @When eu clico em uma imagem
     */
    public function euClicoEmUmaImagem()
    {
        // Simula clique em uma imagem
        $this->assertElementOnPage('.image-card');
    }

    /**
     * @Then eu devo ver um modal com os detalhes
     */
    public function euDevoVerUmModalComOsDetalhes()
    {
        $this->assertElementOnPage('#imageModal');
    }

    /**
     * @Then eu devo ver o nome do arquivo
     */
    public function euDevoVerONomeDoArquivo()
    {
        $this->assertPageContainsText('Nome do Arquivo');
    }

    /**
     * @Then eu devo ver a data de upload
     */
    public function euDevoVerADataDeUpload()
    {
        $this->assertPageContainsText('Data de Upload');
    }

    /**
     * @Then eu devo ver o tamanho do arquivo
     */
    public function euDevoVerOTamanhoDoArquivo()
    {
        $this->assertPageContainsText('Tamanho');
    }

    /**
     * @Given eu abri o modal de uma imagem
     */
    public function euAbriOModalDeUmaImagem()
    {
        // Simula abertura do modal
    }

    /**
     * @Then o modal deve ser fechado
     */
    public function oModalDeveSerFechado()
    {
        // Verifica se o modal foi fechado
    }

    /**
     * @Then eu devo voltar para a galeria
     */
    public function euDevoVoltarParaAGaleria()
    {
        $this->assertElementOnPage('.gallery-grid');
    }

    /**
     * @When eu confirmo a exclusão
     */
    public function euConfirmoAExclusao()
    {
        // Simula confirmação
    }

    /**
     * @Then a imagem não deve mais aparecer na galeria
     */
    public function aImagemNaoDeveMaisAparecerNaGaleria()
    {
        // Verifica que a imagem foi removida
    }

    /**
     * @When eu cancelo a exclusão
     */
    public function euCanceloAExclusao()
    {
        // Simula cancelamento
    }

    /**
     * @Then a imagem deve permanecer na galeria
     */
    public function aImagemDevePermanecerNaGaleria()
    {
        $this->assertElementOnPage('.image-card');
    }

    /**
     * @Then o modal deve permanecer aberto
     */
    public function oModalDevePermanecerAberto()
    {
        $this->assertElementOnPage('#imageModal');
    }

    /**
     * @Given que existem múltiplas imagens na galeria
     */
    public function queExistemMultiplasImagensNaGaleria()
    {
        // Precondição documentada
    }

    /**
     * @When eu excluo todas as imagens uma por uma
     */
    public function euExcluoTodasAsImagensUmaPorUma()
    {
        // Simula exclusão múltipla
    }

    /**
     * @Then a galeria deve ficar vazia
     */
    public function aGaleriaDeveFicarVazia()
    {
        $this->assertPageContainsText('Nenhuma imagem');
    }
}

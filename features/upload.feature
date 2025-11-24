# language: pt
Funcionalidade: Upload de Imagens
  Como um usuário do sistema
  Eu quero fazer upload de imagens
  Para que eu possa armazená-las na galeria

  Contexto:
    Dado que estou na página inicial

  Cenário: Acessar página de upload
    Quando eu clico no botão de adicionar imagem
    Então eu devo ver a página de upload
    E eu devo ver o formulário de upload

  Cenário: Visualizar informações sobre upload
    Quando eu clico no botão de adicionar imagem
    Então eu devo ver a informação "Formatos: JPEG, PNG, GIF, WebP"
    E eu devo ver a informação "Tamanho máximo: 10MB"

  Cenário: Opções de nome de arquivo
    Quando eu clico no botão de adicionar imagem
    Então eu devo ver a opção "Manter nome original do arquivo"
    E eu devo ver a opção "Nome personalizado"

  Cenário: Cancelar upload
    Quando eu clico no botão de adicionar imagem
    E eu clico no botão "Cancelar"
    Então eu devo voltar para a página inicial

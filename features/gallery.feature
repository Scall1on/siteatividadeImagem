# language: pt
Funcionalidade: Visualização da Galeria
  Como um usuário do sistema
  Eu quero visualizar minhas imagens
  Para que eu possa gerenciá-las

  Contexto:
    Dado que estou na página inicial

  Cenário: Galeria vazia
    Dado que não existem imagens na galeria
    Quando eu acesso a página inicial
    Então eu devo ver a mensagem "Nenhuma imagem foi enviada ainda."
    E eu devo ver o botão de adicionar imagem

  Cenário: Galeria com imagens
    Dado que existem imagens na galeria
    Quando eu acesso a página inicial
    Então eu devo ver a grade de imagens
    E cada imagem deve ter um preview

  # Nota: Modais e detalhes de imagem requerem JavaScript.
  # Testes de interação JavaScript são cobertos pelo Cypress.

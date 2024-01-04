# Gentileza alterar a branch para dev.


# Consulta.php - Script de Atualização de Incidentes RightNow

## Descrição

Este script PHP, criado por Caique Renan Alves em 08/12/2023, tem a finalidade de atualizar incidentes no sistema RightNow com informações relacionadas aos Correios, como código de postagem, data de postagem, status e código de rastreio.

## Requisitos

- Ambiente com acesso à API externa para consulta.
- Credenciais de autenticação do agente RightNow.

## Instalação

1. Faça o login no console instalado em seu computador.
2. Acesse Configurações > Configurações do Site > Gerenciador de Arquivos.
3. Altere o tipo de arquivo para "Scripts Personalizados".
4. Todos os arquivos estarão disponíveis; o arquivo mencionado é `consulta.php`.

## Uso

O script é projetado para ser chamado por meio de uma requisição HTTP contendo dados JSON, especialmente o ID do incidente. Exemplo de requisição:

```json
{
  "id": 114249,
  "codigo_de_postagem": "ABC123",
  "data_postagem_correios": "2023-12-08",
  "status_correios": "Entregue",
  "codigo_de_rastreio_correios": "XYZ789"
}

```
Fluxo de Execução
Autenticação e Inicialização:

Configuração da localização e autenticação do agente.
Inicialização da API de Conexão do RightNow.
Entrada de Dados:

Verificação se o método HTTP é POST.
Obtenção dos dados JSON da requisição.
Atualização do Incidente no RightNow:

Obtenção do incidente com base no ID fornecido.
Atribuição dos valores aos campos personalizados do incidente.
Salvar as alterações no incidente.
Saída de Dados:

Impressão de informações relevantes ou mensagens de erro.
Tratamento de Erros
Em caso de falha na execução ou exceção, mensagens de erro detalhadas serão impressas.

Avisos
A autenticação e autorização são fundamentais para o acesso e manipulação de dados no RightNow e na API externa.

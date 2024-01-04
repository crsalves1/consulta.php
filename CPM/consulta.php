<?php

/**
 * Arquivo: consulta.php
 * Autor: Caique Renan Alves
 * Data de Criação: 08/12/2023
 * Descrição: Script responsável por obter o código de postagem, data de postagem e o status.
 */

// Configuração da localização e autenticação do agente
setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');
if (!defined('DOCROOT')) {
    $docroot = get_cfg_var('doc_root');
    define('DOCROOT', $docroot);
}
require_once(DOCROOT . '/include/services/AgentAuthenticator.phph');

setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');

initConnectAPI('caique.alves', 'Tr@balh0');
use RightNow\Connect\v1_3 as RNCPHP;

// Verifique se o método HTTP é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed"); // Método não permitido 
    echo json_encode(['error' => 'Método não permitido. Use POST.']);
    return;
}

// Obtenção dos dados JSON da requisição
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$idNum = intval($id);
$codigo_de_postagem = $data['codigo_de_postagem'];
$data_postagem_correios = $data['data_postagem_correios'];
$status_correios = $data['status_correios'];
$codigo_de_rastreio_correios = $data['codigo_de_rastreio_correios'];

try {

    //echo json_encode(["Teste".$id]);
    
    // Obtenção do incidente com base no ID fornecido
    $incident = RNCPHP\Incident::fetch($idNum);
    
    
    // Atribuição dos valores aos campos personalizados do incidente
    $incident->CustomFields->c->codigo_de_postagem = $codigo_de_postagem;
    $incident->CustomFields->c->data_postagem_correios = $data_postagem_correios;
    $incident->CustomFields->c->status_correios = $status_correios;
    $incident->CustomFields->c->codigo_de_rastreio_correios = $codigo_de_rastreio_correios;

    // Salvar as alterações no incidente
    if ($incident->save() === false) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['error' => 'Falha ao atualizar o incidente.']);
        return;
    }

    echo json_encode(['success' => 'Incidente atualizado com sucesso!']);

} catch (Exception $e) {
    // Tratamento de exceções
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => $e->getMessage()]);
}

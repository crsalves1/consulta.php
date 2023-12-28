<?php

/**
 * Criado por: Caique Renan Alves
 * Data:       08/12/2023
 * Hora:       17:24
 * Descrição:  Script responsável pelo envio de Tickets a API Zammad
 */
setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');
if (!defined('DOCROOT')) {
    $docroot = get_cfg_var('doc_root');
    define('DOCROOT', $docroot);
}
require_once(DOCROOT . '/include/services/AgentAuthenticator.phph');

setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');

initConnectAPI('caique.alves', 'Tr@balh0');
use RightNow\Connect\v1_3 as RNCPHP;
    $data = json_decode(file_get_contents('php://input'),true);
    $id = $data['id'];

    try {
        

        echo'Função enviarIncidenteTicketAPI foi chamada com o ID: ' . $id;
        // Acessar o objeto Contact com base no ID fornecido
        $incident = RNCPHP\Incident::fetch($id);
        
        $ID = $incident->ID;
        $pedido = $incident->CustomFields->c->pedido;
        $sku = $incident->c->Sku;
        //$valor = $incident->c->Valor;
        $codigoDePostagem = $incident->CustomFields->c->codigo_de_postagem;
        $fila = $incident->Queue->ID;
        //Acessar as informacoes de cada um dos clientes.
        $cpf = $incident->PrimaryContact->CustomFields->c->cpf;
        $nome = $incident->PrimaryContact->Name->First;
        $sobreNome = $incident->PrimaryContact->Name->Last;
        $ddd = $incident->PrimaryContact->c->ddd;
        $telefone = $incident->PrimaryContact->Phones->RawNumber;
        $endereco = $incident->PrimaryContact->CustomFields->c->nome_rua;
        $numeroCasa = $incident->PrimaryContact->CustomFields->c->numero_casa;
        $bairro = $incident->PrimaryContact->CustomFields->c->bairro;
        $cep = $incident->PrimaryContact->CustomFields->c->cep;
        $cepNumerico = str_replace('-', '', $cep);
        $idZammad = $incident->CustomFields->c->id_zammad;
        $bandeira = $incident->CustomFields->c->bandeira;
        $email = ($bandeira === 1) ? 'gruposaclb@lebiscuit.com.br' : 'bbackofficesac@casaevideo.com.br';



        // Verificar o código de postagem
        if (empty($codigoDePostagem) || $codigoDePostagem === null) {
            // Criar o array de $dados
            $dados = array(
                "title" => $ID,
                "group_id" => 1,
                "customer_id" => 12,
                "processo" => "P004",
                'p004_idrightnow' => $ID,
                'p004_pedido' => $pedido,
                'p004_sku' => $sku,
                'p004_valor' => $valor,
                'codigo_de_postagem' => $codigoDePostagem,
                'p004_cpf' => $cpf,
                'p004_nome' => $nome,
                'p004_sobrenome' => $sobreNome,
                'p004_email'=> $email,
                'p004_ddd' => $ddd,
                'p004_telefone' => $telefone,
                'p004_endereco' => $endereco,
                'p004_numero' => $numeroCasa,
                'p004_bairro'=>$bairro,
                'p004_cep' => $cepNumerico,
                'bandeira'=> $bandeira,
                "article" => array(
                    "subject" => "Teste RIGHTNOW",
                    "body" => "Ticket criado a partir do acionamento automático do Rightnow",
                    "type" => "note",
                    "internal" => false
                )
            );



        }

        var_dump($dado);


        //Mudando para formato Json
        $json = json_encode($dados);

        $jsonError = json_last_error();
        if ($jsonError !== JSON_ERROR_NONE) {
            throw new Exception('Erro na codificação JSON: ' . json_last_error_msg());
        }

        load_curl();
        $curl = curl_init();
        echo'Iniciando requisição cURL para a API Zammad...';

        // Verificar se a inicialização foi bem-sucedida
        if ($curl === false) {
            throw new Exception('Erro ao iniciar a sessão cURL.');
            return; // ou outro tratamento apropriado
        }


        // Adicionar configurações de captura de erros cURL
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Adicionar esta linha antes de configurar as opções da requisição cURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // Definir as opções da requisição cURL
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://lebiscuit.iukelzon.com.br/api/v1/tickets',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer mFLWALrXthfnfVd6FLhT7TRZk05qr7KOBaRJH2hmdwflLN-euVgCrqt0bNDrhLqP'
        ),
        ));

        // Executar a requisição cURL e armazenar a resposta
        $response = curl_exec($curl);
        echo 'Requisição cURL concluída. Resultado:', $response;

        // Verificar se a execução foi bem-sucedida
        if ($response === false) {
            echo 'Erro na execução da requisição cURL: ' . curl_error($curl);
            return; // ou outro tratamento apropriado
        }

        // Exibir a resposta do servidor para análise
        echo 'Resposta do servidor:', $response;

        // Decodificar a resposta JSON
        $result = json_decode($response, true);

        // Verificar se houve erro na decodificação JSON
        if ($result === null) {
            echo 'Erro na decodificação JSON: ' . json_last_error_msg();
            return;
        }

        // Exibir detalhes do erro, se disponíveis
        if (isset($result['errors'])) {
            echo 'Erros encontrados:';
            print_r($result['errors']);
            return;
        }
        $idResponse = $result['id'];

        echo'ID do Response: ' . $idResponse;
        $incident->CustomFields->c->id_zammad = $idResponse;
        $incident->save();




        // Verificar erros na codificação JSON
        $jsonError = json_last_error();
        if ($jsonError !== JSON_ERROR_NONE) {
            throw new Exception('Erro na codificação JSON: ' . json_last_error_msg());
            return; // ou outro tratamento apropriado
        }

        // Verificar o código de resposta HTTP
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 200 && $httpCode < 300) {
            throw new Exception('Integração bem-sucedida. Código de Resposta: ' . $httpCode);
        } else {
            throw new Exception('Erro na integração. Código de Resposta: ' . $httpCode);
        }
        
        // Fechar a sessão cURL
        curl_close($curl);

    }
    catch (Exception $e) {
        print_r($e->getMessage());
    }


?>

// Carrega a extensão para o objeto "Incident" na versão "1.0"
ORACLE_SERVICE_CLOUD.extension_loader.load("Incident", "1.0").then(function(extensionProvider) {
    extensionProvider.registerWorkspaceExtension(function(workspaceRecord) {
        extensionProvider.registerUserInterfaceExtension(function(UserInterfaceContext) {

            /**
             * Obtém o token de sessão atual e chama a função de retorno de chamada.
             * @param {function} callback - Função de retorno de chamada para processar o token de sessão.
             */
            function getSession(callback) {
                extensionProvider.getGlobalContext().then(function(globalContext) {
                    globalContext.getSessionToken().then(function(sessionToken) {
                        callback(sessionToken);
                    });
                });
            }

            /**
             * Exibe uma mensagem de erro ao usuário.
             * @param {string} msg - A mensagem de erro a ser exibida.
             */
            function fn_Err(msg) {
                alert(msg);
            }

            /**
             * Envia uma solicitação para o servidor PHP com o ID do incidente e executa um Refresh após a conclusão.
             */
            function fn_envioCorreios() {
                workspaceRecord.getFieldValues(['Incident.IID','Incident.c$id_zammad']).then(function(FieldDetails) {
                    // Obtém o ID do incidente do campo "Incident.IID"
                    var incidentId = FieldDetails.getField("Incident.IID").getValue();
                    var idZammad = FieldDetails.getField("Incident.c$id_zammad").getValue();
                    // Constrói a URL do servidor PHP
                    var caminho = window.location.origin + '/cgi-bin/casaevideorn.cfg/php/custom/incident.php';
                    console.log(idZammad);
                    if(idZammad !== null){
                        fn_Err('O id do bot é: ' + idZammad + ' Por favor aguarde');
                        getSession((token) => {
                            var http = new XMLHttpRequest();
    
                            // Configura o tratamento de eventos quando o estado da solicitação muda
                            http.onreadystatechange = function() {
                                console.log('Ready state:', http.readyState);
                                console.log('Status:', http.status);
    
                                // Quando a solicitação está concluída (readyState == 4)
                                if (this.readyState == 4) {
                                    console.log('Response:', this.responseText);
                                // Aguarda 1 segundo e, em seguida, executa o comando Refresh no workspaceRecord
                                setTimeout(function() {
                                    workspaceRecord.executeEditorCommand('Refresh');
                                    workspaceRecord.executeEditorCommand('Save');
                                }, 1000);
                                            
                                }
                            };
    
                            // Configura a solicitação AJAX para o servidor PHP
                            http.open('POST', window.location.origin + '/cgi-bin/casaevideorn.cfg/php/custom/consulta.php', true);
                            http.setRequestHeader('OSvC-CREST-Application-Context', '1');
                            http.setRequestHeader('Content-type', 'application/json');
                            http.setRequestHeader('Auth', token);
    
                            // Envia a solicitação com o ID do incidente no corpo
                            http.send(JSON.stringify({
                                "id": incidentId
                            }));
                            });
                    }else{
                        // Obtém o token de sessão
                        getSession((token) => {
                        var http = new XMLHttpRequest();

                        // Configura o tratamento de eventos quando o estado da solicitação muda
                        http.onreadystatechange = function() {
                            console.log('Ready state:', http.readyState);
                            console.log('Status:', http.status);

                            // Quando a solicitação está concluída (readyState == 4)
                            if (this.readyState == 4) {
                                console.log('Response:', this.responseText);
                            // Aguarda 1 segundo e, em seguida, executa o comando Refresh no workspaceRecord
                            setTimeout(function() {
                                workspaceRecord.executeEditorCommand('Refresh');
                                workspaceRecord.executeEditorCommand('Save');
                            }, 1000);
                                        
                            }
                        };
                        
                        // Configura a solicitação AJAX para o servidor PHP
                        http.open('POST', caminho, true);
                        http.setRequestHeader('OSvC-CREST-Application-Context', '1');
                        http.setRequestHeader('Content-type', 'application/json');
                        http.setRequestHeader('Auth', token);

                        // Envia a solicitação com o ID do incidente no corpo
                        http.send(JSON.stringify({
                            "id": incidentId
                        }));
                        });
                    }
                });
            }

            // Adiciona um ouvinte de eventos nomeado "envioCorreios" para a função fn_envioCorreios
            workspaceRecord.addNamedEventListener('envioCorreios', fn_envioCorreios);
        });
    });
});

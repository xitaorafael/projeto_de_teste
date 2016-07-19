<?php

    namespace stalker\application\ponto\controllers;

    use stalker\application\index\controllers\Helpers_Vital;
    use stalker\library\Fw as Fw;
    use stalker\application\ponto\models as models;
    use stalker\application\index\models as models_app;

    /**
     * Gerar relatorio de ponto.
     * 
     * @author Fernando Dias <rodox17@gmail.com>
     * @package APP
     * @subpackage Controllers
     */
    class Relatorio extends Fw\Controller {

        /**
         * Carrega os filtros de
         * usuarios e meses.
         */
        function indexAction() {

            /**
             * Conecta ao BD.
             * 
             * @var Fw\DB
             */
            $db = new Fw\DB();

            /**
             * Lista de usuarios.
             * 
             * @var Array
             */
            $this->view->usuarios = $db->
                    select("usu.id, usu.nome, usu.numero_ctps, numero_pis, usu.data_admissao, he.nome as 'horario',usu.id_horarios_expediente")->
                    from('usuarios usu')->
                    left('horarios_expediente he', 'he.id = usu.id')->
                    order("nome")->
                    fetchAll();

            /**
             * Lista de meses.
             * 
             * @var Array
             */
            $this->view->mes_vital = Fw\Vital::getMes();

            /**
             * URL do formulario.
             * 
             * @var String
             */
            $this->view->action = Helpers_Vital::getUrl("Relatorio", "tabela", null, "ponto");
        }

        /**
         * Gera o relatorio
         * em excel ou HTML.
         */
        function tabelaAction() {

            /**
             * Inicia conexao com o BD.
             * 
             * @var Fw\DB
             */
            $db = new Fw\DB();

            /**
             * Conecta ao Ponto do modulo.
             * 
             * @var models\Ponto
             */
            $Ponto = new models\Ponto();

            /**
             * Conecta ao usuario do
             * app principal.
             * 
             * @var models_app\Usuarios
             */
            $Usuario = new models_app\Usuarios();

            /**
             * ID do usuario a ser baixado.
             * 
             * @var Integer
             */
            $id_usuario = $_POST['id_usuarios'];
            
            /**
             * Retorna todas as informações do usuario selecionado
             * 
             * @var String
             */
            $this->view->usuario = $Usuario->dadosUsuarioEmpresa($id_usuario);

            /**
             * busca mes selecionado na pagina index
             * @var String
             */
            $mes = $_POST['mes'];

            /**
             * busca ano selecionado na pagina index
             * @var String
             */
            $ano = $_POST['ano'];



            $dia = 01 ;
            /**
             * Converte dia da semana do BD para Linguagem PT-BR
             * 
             * @var Array
             */
            $horas['dias_semana'] = array(
                "Monday" => "Segunda-feira",
                "Tuesday" => "Terça-feira",
                "Wednesday" => "Quarta-feira",
                "Thursday" => "Quinta-feira",
                "Friday" => "Sexta-feira",
                "Saturday" => "Sábado",
                "Sunday" => "Domingo"
            );

            /**
             * Converte dia da semana para Linguagem PT-BR
             * 
             * @var Array
             */
            $horas['semana_numero'] = array(
                "1" => "Segunda-feira",
                "2" => "Terça-feira",
                "3" => "Quarta-feira",
                "4" => "Quinta-feira",
                "5" => "Sexta-feira",
                "6" => "Sabado",
                "0" => "Domingo"
            );
            /**
             * Informa quando é array com folga ou falta
             * 
             * @var Array
             */
            $horas['folga'] = array(
                "6" => "Folga",
                "0" => "Folga",
                "1" => "Falta",
                "2" => "Falta",
                "3" => "Falta",
                "4" => "Falta",
                "5" => "Falta",
            );

            /**
             * Informa quando é array com folga ou Banco
             * 
             * @var Array
             */
            $horas['banco'] = array(
                "6" => "Folga",
                "0" => "Folga",
                "1" => "Banco",
                "2" => "Banco",
                "3" => "Banco",
                "4" => "Banco",
                "5" => "Banco",
            );
            /**
             * Informa o cargo do funcionário
             */
            $horas['cargo'] = array(
                "1" => "Desenhista",
                "2" => "Técnico",
                "3" => "Analista Projetos",
                "4" => "Auxiliar Administrativo",
                "5" => "Estágio",
                "6" => "Técnico em Telecomunicações",
                "7" => "Instalador",
                "8" => "Diretor"
            );
            /**
             * Array com a tabela de horários de trabalho
             * 
             * @var Array
             * 
             */
            $horas['horarios'] = array(
                "1" => "08:30 12:00 13:00 18:00",
                "2" => "08:00 12:00 13:00 17:30",
                "3" => "07:30 12:00 13:00 17:00",
                "4" => "08:30 12:00 13:00 17:30",
                "5" => "08:00 12:00 - APRENDIZ",
                "6" => "09:00 12:00 13:00 18:00",
                "7" => "08:30 12:00 13:30 18:00",
                "8" => "08:00 12:15",
                "9" => "09:00 12:00 13:00 16:00",
                "10" => "08:30 14:30"
            );
            $this->view->horario = $Usuario->dadosHorario($id_usuario);

            /**
             * Divide campo horarios por array 
             * example:  array[0] 08:30
             * @var Array
             */
            $horario_exp = explode(" ", $horas['horarios']{$this->view->horario['id_horarios_expediente']});
 
            /**
             * Seleciona todos os dias
             * que o usuario bateu ponto.
             * 
             * @var Array
             */
            $pontos = $db->
                    select()->
                    from("get_ponto")->
                    where("id_usuarios=$id_usuario AND month(data) = month('$ano-$mes-$dia');")->
                    fetchAll();

            
            /**
             * Se dia inicial for contado do mes selecionado
             * começa contagem só depois da data registrada
             * 
             * Example: inicio dia 07 -> array_dia[7] i++
             * @var Array
             */
                $mes_inicio = $pontos[0]['data_banco_horas'];
                $mes_inicio = str_replace("$ano-", "", $mes_inicio);
            $dias_total_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

            for ($k = 0; $k <= $dias_total_mes; $k++) {
                $mes_inicio = str_replace("-0$k", "", $mes_inicio);
            }
                
            if (date("m-Y", strtotime($pontos[0]['data_banco_horas'])) == date("m-Y", strtotime("$ano-$mes-$dia"))) {

                // primeiro dia = 7
                $dia_inicio = $pontos[0]['dia_data_banco_horas'];
            /**
             * Senão começa do dia 1
             */   
            } else {

                $dia_inicio = 1;
                
            }
            /**
             * Se dia fim for contado do mes selecionado
             * contagem vai até a data registrada
             * 
             * Example: fim dia 07 -> array_dia > 0 && < 7
             * @var Array
             */
            if (date("m-Y", strtotime($pontos[0]['data_banco_horas_fim'])) == date("m-Y", strtotime("$ano-$mes-$dia"))) {

                //dia_fim = coluna dia_data_banco_horas_fim
                $dia_fim = $pontos[0]['dia_data_banco_horas_fim'];
            /**
             * Senão continua contabilização normalmente
             */    
            } else {
                //formata $b_date para o formato data ano mes dia
                $b_date = "$ano-$mes-$dia";
                
                //busca ano,mes e ultimo dia do mes
                date("Y-m-t", strtotime($b_date));

                //retorna só ultimo dia do mes
                $dia_fim = (date("t", strtotime($b_date)));
            }

            /**
             * Seleciona no BD o BSALDO do mês anterior.
             *
             * @var Time
             * @example -48:51
             */
            $saldo_mes = $Ponto->saldoGeral($id_usuario, $mes, $ano);

            /**
             * retorna apenas coluna saldo
             */
            $saldo_mes = $saldo_mes['saldo'];
            //$saldo_mes = "-10:19";

            /**
             * Seleciona as Horas compensadas ou Dias.
             *
             * @var Time
             * @example justificatava -> 01:00 = cred +01:00
             */
            $creditos_compensados = $Ponto->buscaGeral($id_usuario, $mes, $ano);

            /**
             * Sempre que ouver credito compensado no dia, gerar um credito ao saldo_diaria
             */
            foreach ($creditos_compensados as $data) {

                /** Se dia ouver apenas 1 caracter
                 * 
                 * @example 1/2/3
                 * @example se torna 01/02/03
                 * @var Time
                 */
                $dia = strlen($data['dia']) == 1 ? "0{$data['dia']}" : $data['dia'];
               
                /**
                 * Busca os segundos da coluna horas_compensado
                 * 
                 * @var Time
                 */
                $seconds = $data['horas_compensado'];
                
                /**
                 * transforma horas em segundo
                 * 
                 * @var Time
                 */
                $hours = floor($seconds / 3600);
                
                /**
                 * transforma minutos em segundo
                 * 
                 * @var Time
                 */
                $mins = floor($seconds / 60 % 60);
               
                /**
                 * identifica variavel secs como segundos
                 * 
                 * @var Time
                 */
                $secs = floor($seconds % 60);

                /**
                 * Formata variaveis hours mins secs para o formato padr]ao de horas
                 * 
                 * @var Time
                 */
                $data['horas_compensado'] = $hours.":".$mins.":".$secs;
                /**
                 * Insirido as variaveis dia/mes/ano dentro da compensacao
                 * 
                 * @var String
                 * @example 01/11/1994
                 */
                $compensacao["$dia/$mes/$ano"] = $data;

                /**
                 * Formata data fim que retornava 01-01-2016 em somente 01 -> dia
                 */
                $data_fim = $data['data_fim'];
                
                //retira mes
                $data_fim = str_replace($mes, "", $data_fim);
                
                //retira ano
                $data_fim = str_replace($ano, "", $data_fim);
                
                //retira hifens
                $data_fim = str_replace("-0-", "", $data_fim);
               
                /**
                 * Se coluna dia_compensado retornar 1
                 * 
                 */
                if ($data['dia_compensado'] == 1) {

                    /**
                    * Retorna registro como Feriado
                    */
                   $ponto_final[$data['dia']]['feriado'] = 1;

                   /**
                    * caso retorna numero 2
                    */
               } elseif ($data['dia_compensado'] == 2) {
                   
                   /**
                    * Retorna registro como Ferias
                    */
                   $ponto_final[$data['dia']]['ferias'] = 2;
               }

               if ($data['banco'] == 1) {

                   /**
                    * Retorna registro como Banco
                    * 
                    * @var string
                    */
                   $ponto_final[$data['dia']]['banco'] = 1;
               }
               
               /**
                * se id_ponto_movimento for = 1 identifica que no dia houve compensacao
                * 
                * @var string
                */
               if ($data['id_ponto_movimento'] == 1) {

                /**
                * dia recebe informação de compensacao
                * 
                * @var string
                */
                   $ponto_final[$data['dia']]['id_ponto_movimento'] = 1;
               }
               
                /**
                * dia recebe informação de banco
                * 
                * @var string
                */
  
               if ($data['forcado'] == 1) {

                /**
                 * Retorna registro como Banco
                 */
                $ponto_final[$data['dia']]['forcado'] = $data['nome'];
                }
            }

            /**
             * Varre a lista de pontos.
             */
            foreach ($pontos as $p) {

                /** Se dia ouver apenas 1 caracter
                 * 
                 * @var Time
                 * @example 1/2/3
                 * @example se torna 01/02/03
                 */
                $dia_bd = strlen($p['dia']) == 1 ? "0{$p['dia']}" : $p['dia'];

                /**
                 * Se houver retorno de horas compensadas no mes continue calculo
                 */
                if ($compensacao["$dia_bd/$mes/$ano"]['dia_compensado']) {

                    continue;
                }

                /**
                 * Retorna total de horas trabalhadas no dia;
                 * 
                 * @var Time
                 * @example $ponto_final = $this->view->ponto ['final'],['saldo'],['total'],['total_minutos3'].
                 */
                $ponto_final[$p['dia']]['horas_trabalhadas'] = Helpers_Vital::somaHoras(array($p['total_minutos1'], $p['total_minutos2'], $p['total_minutos3'])); // 03:23

                //horas_trabalhadas_clone = soma minutos 1,2,3 sem compensacao dos 10 minutos
                $ponto_final[$p['dia']]['horas_trabalhadas_clone'] = $ponto_final[$p['dia']]['horas_trabalhadas'];

                //horas_trabalhadas_clone = soma de todos os dias trabalhados
                $ponto_final['normal_total_mes'] = Helpers_Vital::somaHoras(array($ponto_final['normal_total_mes'], $ponto_final[$p['dia']]['horas_trabalhadas_clone']));

                /**
                 * Quando houver compensação no dia ser adicionado;
                 * 
                 * @var Time
                 * @example $ponto_final[$p['dia']]['horas_trabalhadas'] = 07:30 + $cd['horas_compensado'] = 01:00
                 * @example $ponto_final[$p['dia']]['horas_trabalhadas'] = 08:30.
                 */

                if ($compensacao["$dia_bd/$mes/$ano"]) {

                    /**
                     * Busca o dia no banco de dados e mes e ano de acordo com o selecionado na pagina index
                     * 
                     * @var Time
                     * @example 01/02/2016
                     */
                    $cd = $compensacao["$dia_bd/$mes/$ano"];
                    $fim_data = $cd['data_fim'];

                    $mes_final = $mes;
                    $mes_final = strlen($mes_final) == 1 ? "0".$mes_final : $mes_final;
                    $fim_data = str_replace($ano, "", $fim_data);
                    $fim_data = str_replace("-$mes_final-", "", $fim_data);

                    /**
                     * Soma o total de horas trabalhadas no dia com a compensação do dia
                     * 
                     * @var Time
                     * @example $cd = dia 1 = 07:30 horas trabalhadas + 01:00 de compensação
                     */
                    $ponto_final[$p['dia']]['diferenca_compensacao'] = Helpers_Vital::diminuiHoras(array($p['entrou1_hora'], $horario_exp[0]));

                   /**
                    * carga_do_dia == total de horas que tem que trabalhar
                    * 
                    * @var time
                    */
                    $ponto_final[$p['dia']]['carga_do_dia'] = $p['total_horas_diarias'];
                  
                    /**
                    * formata carga_do_dia de 08:30:00 para 08:30
                    * 
                    * @var time
                    */
                    $ponto_final[$p['dia']]['carga_do_dia'] = str_replace("0:00", "0",  $ponto_final[$p['dia']]['carga_do_dia']);
                  
                    /**
                    * se houver apenas 1 string no dia insere um 0 na frente
                    * 
                    * @var time
                    */
                    $cd['dia'] = strlen($cd['dia']) == 1 ? "0".$cd['dia'] : $cd['dia'];

                    /**
                     * Se compensacao for maior de um dia
                     * 
                     * @var time
                     */
                    if($cd['dia'] !== $fim_data){

                        /**
                         * valor no horas compensado se torna 0
                         * 
                         */
                        $cd['horas_compensado'] = "00:00";

                        /**
                         * E horas trabalhadas recebe valor de carga diaria do dia
                         */
                        $ponto_final[$p['dia']]['horas_trabalhadas'] = $ponto_final[$p['dia']]['carga_do_dia'];

                        /**
                         * E no primeiro dia da compensacao se trabalhou só subtrai a diferença
                         */
                        $ponto_final[$p['dia']]['horas_trabalhadas'] = Helpers_Vital::diminuiHoras(array($ponto_final[$p['dia']]['horas_trabalhadas'], $ponto_final[$p['dia']]['diferenca_compensacao']));

                    }
                   
                        /**
                         * soma horas trabalhadas com valor compensacao se for so um dia
                         */
                    $ponto_final[$p['dia']]['horas_trabalhadas'] = Helpers_Vital::somaHoras(array($cd['horas_compensado'], $ponto_final[$p['dia']]['horas_trabalhadas']));

                    /**
                     * saldo de hroas trabalhadas sem a compensação dos 10 min
                     * @var time
                     */
                    $ponto_final[$p['dia']]['horas_trabalhadas_clone'] = $ponto_final[$p['dia']]['horas_trabalhadas'];
                    /**
                     * compensado = true
                     * 
                     * @var Time
                     * @example se conpensação for !== null retorne valor
                     */
                    $ponto_final[$p['dia']]['compensado'] = 1;
                    /**
                     * Apresenta motivo no relatório
                     * 
                     * @var Time
                     * @example motivo = ginicologista
                     */
                    $ponto_final[$p['dia']]['motivo'] = $cd['motivo'];

                    /**
                     * Se conta ocmo banco
                     *
                     * @var Time
                     * @example motivo = ginicologista
                     */
                    $ponto_final[$p['dia']]['id_ponto_movimento'] = $cd['id_ponto_movimento'];

                    $ponto_final[$data['dia']]['banco'] = $cd['banco'];

                }

                /**
                 * Carga diaria é o total necessário a trabalhar por dia
                 * 
                 * @var Time
                 * @example ['carga_diaria']  = 08:30
                 */
                $ponto_final[$p['dia']]['carga_diaria'] = $p['total_horas_diarias'];

                /**
                 * soma a carga diaria de todos os dias uteis do mes
                 * 
                 * @var Time
                 * @example ['carga_diaria']  = 08:30
                 */
                $ponto_final['carga_total_mes'] = Helpers_Vital::somaHoras(array($ponto_final['carga_total_mes'], $ponto_final[$p['dia']]['carga_diaria']));


                /**
                 * entra na condição da compensação dos 10 minutos
                 * 
                 * @var Time
                 * @example ['carga_diaria']  = 08:30
                 */
                $ponto_final[$p['dia']]['compe_menor'] = Helpers_Vital::diminuiHoras(array("00:10", $ponto_final[$p['dia']]['carga_diaria']));

                  /**
                 * Retira sinal do compe_menor
                 * @var String
                 */
                $ponto_final[$p['dia']]['compe_menor'] = str_replace("+", "", $ponto_final[$p['dia']]['compe_menor']);

                /**
                 * Cria variavel compe_maior para acrescentar 10 minutos total da carga_diaria
                 * @var String
                 */
                $ponto_final[$p['dia']]['compe_maior'] = Helpers_Vital::somaHoras(array($ponto_final[$p['dia']]['carga_diaria'], "00:10"));

                /**
                 * Retira sinal do compe_maior
                 * @var String
                 */
                $ponto_final[$p['dia']]['compe_maior'] = str_replace("+", "", $ponto_final[$p['dia']]['compe_maior']);

                /**
                 * Se for maior que 8:40 e menor que 8:21 será considerado como 8:30
                 * @var String
                 */
                if ($ponto_final[$p['dia']]['horas_trabalhadas'] <= $ponto_final[$p['dia']]['compe_maior'] && $ponto_final[$p['dia']]['horas_trabalhadas'] >= $ponto_final[$p['dia']]['compe_menor']) {

                    /**
                     * horas trabalhadas = 0;
                     * @var String
                     */
                    $ponto_final[$p['dia']]['horas_trabalhadas'] = 0;
                }
                
                /**
                 * Subtrai o cargo horário diário pelas horas trabalhadas
                 * 
                 * @var Time
                 * @example 08:30 - 07:49
                 */
                $ponto_final[$p['dia']]['saldo_diaria'] = Helpers_Vital::diminuiHoras(array($ponto_final[$p['dia']]['carga_diaria'],$ponto_final[$p['dia']]['horas_trabalhadas'])); //-05-07

                
                /**
                 * subtrai o total que devia trabalhar por quanto trabalhou
                 * 
                 * @var Time
                 * @example 08:30 - 07:49
                 */
                $ponto_final[$p['dia']]['saldo_diaria_mes'] = Helpers_Vital::diminuiHoras(array($ponto_final[$p['dia']]['carga_diaria'],$ponto_final[$p['dia']]['horas_trabalhadas'])); //-05-07

                /**
                 * retira o sinal de todos resultados do saldo_diaria
                 * 
                 * @var Time
                 * @example -8:00 = 08:00// +08:00 = 08:00
                 */
                $sinal_saldo_diaria = Helpers_Vital::extraiSinal($ponto_final[$p['dia']]['saldo_diaria']);

                /**
                 * retira o sinal de todos resultados do $sinal_saldo_diaria_mes
                 * 
                 * @var Time
                 * @example -8:00 = 08:00// +08:00 = 08:00
                 */
                $sinal_saldo_diaria_mes = Helpers_Vital::extraiSinal($ponto_final[$p['dia']]['saldo_diaria_mes']);


                /**
                 * retira o sinal de todos resultados do $sinal_saldo_mes
                 * 
                 * @var Time
                 * @example -8:00 = 08:00// +08:00 = 08:00
                 */
                $sinal_saldo_mes = Helpers_Vital::extraiSinal($saldo_mes);
                /**
                 * Gerara array com todos os dias do mes 
                 */
                $ponto_final[$p['dia']]["dados"] = $p;

                /**
                 * retira o sinal de todos resultados do saldo_diaria
                 * 
                 * @var Time
                 * @example -05:00 = Debito // +05:00 = Credito
                 */
                if ($sinal_saldo_mes == "-") {

                /**
                 * Se sinal retornar "-" o valor será gerado como debito diario
                 * 
                 * @var Time
                 * @example debito = -05:00
                 */
               
                    /**
                     * recebe valor do mes anterior
                     */
                    $ponto_final['saldo_mes'] = $saldo_mes;
              
                        /**
                     * se houver debito no dia 1, soma o mes anterior com o debito
                     */
                    if($ponto_final[1]['debito'] !== null){
                        
                    /**
                     * debito + saldo
                     */
                        $ponto_final[1]['debito'] = Helpers_Vital::somaHoras(array($ponto_final[1]['saldo_diaria'],$ponto_final['saldo_mes']));
            
                    }
                    /**
                     * se houver credito no dia 1, soma o mes anterior com o credito
                     */
                    if($ponto_final[1]['credito'] !== null){
                        
                    /**
                     * credito + saldo
                     */
                        
                        $ponto_final[1]['debito'] = $ponto_final['saldo_mes'];
                        
                    }
                    
                    /**
                     * senão houver credito nem debito como condição se refere ao saldo_mes negativo
                     * debito recebe o valor
                     */
                    if($ponto_final[1]['debito'] == null && $ponto_final[1]['credito'] == null){
                        
                        
                    /**
                     * debito == saldo-mes
                     */
                        $ponto_final[1]['debito'] = $saldo_mes;
                        
                    }

                /**
                 * Senao
                 */
                }else{
                    
                    /**
                     * p_saldo_mes = saldo mes anterior
                     */
                    $ponto_final['saldo_mes'] = $saldo_mes;
              
                    /**
                     * se dia 1 tiver debito soma saldo anterior com ele
                     */
                    if($ponto_final[1]['debito'] !== null){
                        
                    /**
                     * debito + saldo
                     */
                        $ponto_final[1]['debito'] = Helpers_Vital::somaHoras(array($ponto_final[1]['saldo_diaria'],$ponto_final['saldo_mes']));
            
                    }
                    
                    /**
                     * se dia 1 tiver credito diminui debito saldo anterior com ele
                     */
                    if($ponto_final[1]['credito'] !== null){
                        
                    /**
                     * credito - saldo
                     */
                        $ponto_final[1]['credito'] = Helpers_Vital::somaHoras(array($ponto_final['saldo_mes'],$ponto_final[1]['saldo_diaria']));
                    } 
                   
                    /**
                     * se não tiver credito nem debito
                     */
                    if($ponto_final[1]['debito'] == null && $ponto_final[1]['credito'] == null){
                    
                    /**
                     * credito recebe o saldo
                     */
                        $ponto_final[1]['credito'] = $saldo_mes;
                    }
                }
            

                if ($sinal_saldo_diaria == "-") {

                    /**
                     * Se sinal retornar "-" o valor será gerado como debito diario
                     * 
                     * @var Time
                     * @example debito = -05:00
                     */
                    $ponto_final[$p['dia']]['debito'] = $ponto_final[$p['dia']]['saldo_diaria'];
                    /**
                     * Se sinal retornar "-" o valor será gerado como debito_final
                     * 
                     * @var Time
                     * @example debito = -05:00
                     */
                    $ponto_final[$p['dia']]['debito_final'] = $ponto_final[$p['dia']]['saldo_diaria'];

                    /**
                     * Senao
                     */
                } else {

                    /**
                     * Se sinal retornar "+" o valor será gerado como credito diario
                     * 
                     * @var Time
                     * @example credito = +05:00
                     */
                    $ponto_final[$p['dia']]['credito'] = $ponto_final[$p['dia']]['saldo_diaria'];
               
                    /**
                     * Se sinal retornar "+" o valor será gerado como credito_final
                     * 
                     * @var Time
                     * @example credito = +05:00
                     */
                    $ponto_final[$p['dia']]['credito_final'] = $ponto_final[$p['dia']]['saldo_diaria'];
                }
           
                if ($sinal_saldo_diaria_mes == "-") {

                    /**
                     * Se sinal retornar "-" o valor será gerado como debito diario
                     * 
                     * @var Time
                     * @example debito = -05:00
                     */
                    $ponto_final[$p['dia']]['debito_mes'] = $ponto_final[$p['dia']]['saldo_diaria_mes'];

                /**
                 * Senao
                 */
                } else {

                    /**
                     * Se sinal retornar "+" o valor será gerado como credito diario
                     * 
                     * @var Time
                     * @example credito = +05:00
                     */
                    $ponto_final[$p['dia']]['credito_mes'] = $ponto_final[$p['dia']]['saldo_diaria_mes'];
                }
                /**
                 * normais = horas trabalhadas
                 * 
                 * @var Time
                 * @example $ponto_final[$p['dia']]['normais'] = entrou1 + entrou2 + entrou3
                 */
                $ponto_final[$p['dia']]['normais'] = $ponto_final[$p['dia']]['saldo_diaria'];

                /**
                 * se saldo diaria conter caracter retira o sinal
                 * 
                 * @var String
                 * @example saldo_diaria -> -06:00 = 06:00
                 */
                $saldo_diaria_aux = str_replace("-", "", $ponto_final[$p['dia']]['saldo_diaria']);

                /**
                 * se saldo total conter caracter retira o sinal
                 * 
                 * @var String
                 * @example saldo_total -> -06:00 = 06:00
                 */
                $saldo_aux = str_replace("-", "", $ponto_final[$p['dia']]['saldo']);

                /**
                 * se saldo diaria conter caracter retira o sinal
                 * 
                 * @var String
                 * @example saldo_diaria -> +06:00 = 06:00
                 */
                $saldo_diaria_aux = str_replace("+", "", $saldo_diaria_aux);

                /**
                 * se saldo total conter caracter retira o sinal
                 * 
                 * @var String
                 * @example saldo_total -> +06:00 = 06:00
                 */
                $saldo_aux = str_replace("+", "", $saldo_aux);

                /**
                 * se saldo diaria do dia 1 e dia 2 tiverem negativo some caso esteje diferente subtraia
                 * 
                 * @var Time
                 * @example saldo_total -> -05:00 + -05:00
                 */

                if (($sinal_saldo_diaria == "-" && $sinal_saldo == "-") || ($sinal_saldo_diaria == "+" && $sinal_saldo == "+")) {

                    /**
                     * soma valores
                     * 
                     * @var Time
                     * @example btotal -> -05:00 + -05:00
                     */
                    $ponto_final[$p['dia']]['btotal'] = "-" . Helpers_Vital::somaHoras(array($saldo_diaria_aux, $saldo_aux));
                    /**
                     * Senao
                     */
                } else {
                    /**
                     * subtrai valores
                     * 
                     * @var Time
                     * @example btotal -> -05:00 - +05:00
                     */
                    $ponto_final[$p['dia']]['btotal'] = Helpers_Vital::diminuiHoras(array($saldo_diaria_aux, $ponto_final[$p['dia']]['btotal']));
                }

                /**
                 * Retorna ultimo saldo de mes
                 * 
                 * @var Time
                 */
                $ultimo_dia = $p['dia'];

                /**
                 * Soma todos os créditos do mês
                 * 
                 * @var Time
                 */
                $ponto_final['bcred'] = Helpers_Vital::somaHoras(array($ponto_final['bcred'], $ponto_final[$p['dia']]['credito']));
               
                /**
                 * Soma todos os créditos do mês
                 * 
                 * @var Time
                 */
                $ponto_final['bcred_mes'] = Helpers_Vital::somaHoras(array($ponto_final['bcred_mes'], $ponto_final[$p['dia']]['credito_mes']));
               
                /**
                 * Soma todos os debitos do mês
                 * 
                 * @var Time
                 */
                $ponto_final['bdeb'] = Helpers_Vital::somaHoras(array($ponto_final['bdeb'], $ponto_final[$p['dia']]['debito']));
                /**
                 * Soma todos os debitos do mês
                 * 
                 * @var Time
                 */
                $ponto_final['bdeb_mes'] = Helpers_Vital::somaHoras(array($ponto_final['bdeb_mes'], $ponto_final[$p['dia']]['debito_mes']));
            
                /**
                * Soma todas os horarios trabalhados do mês
                * 
                * @var Time
                */
                $ponto_final['total_normais'] = Helpers_Vital::somaHoras(array($ponto_final['total_normais'], $ponto_final[$p['dia']]['horas_trabalhadas']));

               /**
                * Subtrai todos debitos e creditos do mês
                * 
                * @var Time
                */
                $ponto_final['btotal'] = Helpers_Vital::diminuiHoras(array($ponto_final['bdeb'], $ponto_final['bcred']));

                /**
                 * soma todos os btotal do mês
                 * 
                 * @var Time
                 */
                $ponto_final['bsaldo'] = $ponto_final[$p['dia']]['btotal'];

                /**
                 * ultimo saldo do mês
                 * 
                 * @var Time
                 */
                $ultimo_saldo_diario = $ponto_final[$p['dia']]['btotal'];
            }

            /**
             * Cria array com todos os dias do mês
             * 
             * @var array
             * @example Fevereiro de 2016 retorna 29 Array
             */
            $dias_total_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

            /**
             * mes fevereiro de 2016, $i = 1 até 29
             * 
             * @var String
             */

            for ($i = 1; $i <= $dias_total_mes; $i++) {

                /**
                 * Se retornar entrou1_hora
                 */
                if ($ponto_final[$i]['dados']['entrou1_hora'] && !$ponto_final[$i]['feriado'] && !$ponto_final[$i]['ferias']) {

                    /**
                     * debito_entrou = debito diario
                     * 
                     * @var Time
                     */

                    $ponto_final[$i]['debito_entrou'] = $ponto_final[$i]['debito'];

                    /**
                     * se debito entrou conter o sinal "-" troque por ""
                     * 
                     * @var Time
                     */
                    $ponto_final[$i]['debito_entrou'] = str_replace("-", "", $ponto_final[$i]['debito_entrou']); //05:00

                    /**
                     * se não retornar nada no dia_debito insira a seguinte informacao
                     * 
                     * @var Time
                     */
                    if ($ponto_final[$i]['debito_entrou'] == null) {// = NULL;

                    /**
                     * Se nao retornar nada debito será 00:00
                     * 
                     * @var Time
                     */
                        $ponto_final[$i]['debito_entrou'] = '00:00'; //debito = 00:00
                    }

                    /**
                     * Credito_entrou = credito diario
                     * 
                     * @var Time
                     */
                    $ponto_final[$i]['credito_entrou'] = $ponto_final[$i]['credito']; //+05:00

                    /**
                     * se credito entrou conter o sinal "+" troque por ""
                     * 
                     * @var String
                     */
                    $ponto_final[$i]['credito_entrou'] = str_replace("+", "", $ponto_final[$i]['credito']); //05:00

                    /**
                     * se credito for nulo
                     * 
                     * @var String
                     */
                    if ($ponto_final[$i]['credito_entrou'] == null) { //= NULL;

                        /**
                         * se credito for nulo então sera 0
                         * 
                         * @var String
                         */
                        $ponto_final[$i]['credito_entrou'] = '00:00'; //credito = 00:00
                    }

                    /**
                     * soma do credito do mes
                     * 
                     * @var Time
                     * @example resultado_credito = soma de todos os creditos do mes
                     */
                    $ponto_final['resultado_credito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['credito_entrou'],$ponto_final['resultado_credito'])); // resultado = credito -> 05:00 ou 00:00

                
                    /**
                     * soma do debito do mes
                     * 
                     * @var Time
                     * @example resultado_debito = soma de todos os debitos do mes
                     */
                    $ponto_final['resultado_debito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['debito_entrou'], $ponto_final['resultado_debito'])); // resultado = debito -> 05:00 ou 00:00

                    /**
                     * saldo geral
                     * 
                     * @var Time
                     * @example resultado_debito + resultado_credito
                     */
                    $ponto_final[$i]['saldo'] = Helpers_Vital::diminuiHoras(array($ponto_final['resultado_debito'],$ponto_final['resultado_credito']));
                }

                /**
                 * Se não retornou entrou1_hora e for diferente de feriado,ferias e sabado ou domingo
                 */

                if (!($ponto_final[$i]['dados']['entrou1_hora']) && !$ponto_final[$i]['feriado'] && !$ponto_final[$i]['ferias'] && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 6) && !$ponto_final[$i]['dados']['compensado'] && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 0)) {

                    /**
                     * debito_entrou sera = carga diaria
                     * 
                     * @var Time
                     * @example $ponto_final[$i]['debito_entrou'] = -08:30
                     */

                    $ponto_final[$i]['debito_entrou'] = "-".$ponto_final[$p['dia']]['carga_diaria']; // debito = -08:30
                   
                    $ponto_final[$i]['carga_falta'] = "-".$ponto_final[$p['dia']]['carga_diaria']; 
                    $ponto_final[$i]['carga_falta'] = str_replace("0:00", "0", $ponto_final[$i]['carga_falta']);
                    $ponto_final[$i]['saldo_diaria'] =  $ponto_final[$i]['carga_falta']; // debito = -08:30

                    /**
                     * se dia que a pessoa entrou foi no meio do mes
                     */
                   
                    
                    
                    //HOUVE ALTERAÇÃO AQUI ------------------------------------------------- < POR >
                        if ($dia_inicio > $i && $mes == $mes_inicio) {

                        /**
                         * não contabilizar falta nos dias anteriores
                         */
                           $ponto_final[$i]['debito_do_mes'] = "00:00";
                           $ponto_final[$i]['debito_entrou'] = "00:00";
                    }
                    
                    
                    /**
                     * se houver compensação do dia todo
                     */
                    if ($dia_inicio < $ponto_final[$i] && $cd['id_ponto_movimento']) {

                        /**
                         * debito recebo 0
                         */
                        $ponto_final[$i]['debito_entrou'] = "00:00";
            
                    }           
                        /**
                         * mesma condiçao so que pra estagio que n trabalha nas quartas tbm
                         */
                    if($this->view->usuario['id_horarios_expediente'] == 5 && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 3)){

                        $ponto_final[$i]['debito_entrou'] = "00:00"; // debito = -08:30


                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu1 = falta
                         */
                        $ponto_final[$i]['dados']['label2'] = "CIEE";

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example entrou2 = falta
                         */
                        $ponto_final[$i]['dados']['label3'] = "CIEE";

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu2 = falta
                         */
                        $ponto_final[$i]['dados']['label4'] = "CIEE";

                    }

                         
                    foreach($creditos_compensados as $data){

                        $data_fim = $data['data_fim'];
                        $data_fim = str_replace($mes, "", $data_fim);
                        $data_fim = str_replace($ano, "", $data_fim);
                        $data_fim = str_replace("-0-", "", $data_fim);

                    if($i == $data["dia"] && $i <= $data_fim){
                            
                            $ponto_final[$i]['debito_entrou'] = "00:00";
                            $ponto_final[$i]['saldo_diaria'] = "00:00";
                            $ponto_final[$i]['dados']['entrou1_hora'] = "Atestado";
                            $ponto_final[$i]['dados']['entrou2_hora'] = "Atestado";
                            $ponto_final[$i]['dados']['saiu1_hora'] = "Atestado";
                            $ponto_final[$i]['dados']['saiu2_hora'] = "Atestado";
                        }
                    } 

                    /**
                     * credito_entrou sera = 0
                     * 
                     * @var Time
                     * @example $ponto_final[$i]['credito_entrou'] = 00:00
                     */
                    $ponto_final[$i]['credito_entrou'] = '00:00'; // credito = 00:00

                    /**
                     * soma entre resultado_credito e credito entrou
                     * 
                     * @var Time
                     * @example $ponto_final['resultado_credito'] = 00:00
                     */
                    $ponto_final['resultado_credito'] = Helpers_Vital::somaHoras(array($ponto_final['resultado_credito'], $ponto_final[$i]['credito_entrou'])); // 05:00 + -08:30

                    /**
                     * soma entre resultado_debito e debito entrou
                     * 
                     * @var Time
                     * @example $ponto_final['resultado_debito'] = -08:30
                     */
                    $ponto_final['resultado_debito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['debito_entrou'], $ponto_final['resultado_debito'])); //05:00 - -08:30

                    /**
                     * $ponto_final[$i]['saldo'] = subtração entre resultado_debito e resultado_credito
                     * 
                     * @var Time
                     * @example  $ponto_final[$i]['saldo'] = -05:40
                     */
                    $ponto_final[$i]['saldo'] = Helpers_Vital::diminuiHoras(array($ponto_final['resultado_debito'], $ponto_final['resultado_credito'])); // -03:30 - -13:30

                    /**
                     * $ponto_final[$i]['debito'] retorna 08:30:00 retirar os ultimos :00
                     * 
                     * @var Time
                     * @example  $ponto_final[$i]['debito'] = 08:30
                     */
                    $ponto_final[$i]['debito'] = str_replace('0:00', "0", $ponto_final[$p['dia']]['carga_diaria']); // 08:30:00 -> 08:30
                    /**
                     * $ponto_final[$i]['debito'] retorna 08:30:00 retirar os ultimos :00
                     * 
                     * @var Time
                     * @example  $ponto_final[$i]['debito'] = 08:30
                     */
                    $ponto_final[$i]['debito'] = "-" . $ponto_final[$i]['debito']; // debito = -08:30

                    /**
                     * $ponto_final[$i]['saldo_diaria'] retorna 08:30:00 retirar os ultimos :00
                     * 
                     * @var Time
                     * @example  $ponto_final[$i]['saldo_diaria'] = 08:30
                     */

                    foreach($creditos_compensados as $data){

                        $data_fim = $data['data_fim'];
                        $data_fim = str_replace($mes, "", $data_fim);
                        $data_fim = str_replace($ano, "", $data_fim);
                        $data_fim = str_replace("-0-", "", $data_fim);

                        if($i >= $data["dia"] && $i <= $data_fim){


                            $ponto_final[$i]['saldo_diaria'] = "";


                            if(!$data_fim){
                                $ponto_final[$i]['saldo_diaria'] = str_replace('0:00', "0", $ponto_final[$p['dia']]['carga_diaria']); // 08:30:00 -> 08:30
                                $ponto_final[$i]['saldo_diaria'] = "-" . $ponto_final[$i]['saldo_diaria']; //saldo = -08:30
                            }
                        }
                    }
                    if($this->view->usuario['id_horarios_expediente'] == 5 && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 3)){

                        $ponto_final[$i]['saldo_diaria'] = ""; // 08:30:00 -> 08:30

                    }
                }

                /**
                 * Caso não tenho entrada e não seja ferias ou feriado.
                 */
                if (!($ponto_final[$i]['dados']['entrou1_hora']) && !$ponto_final[$i]['ferias'] && !$ponto_final[$i]['feriado']) {

                    if ($dia_inicio < $ponto_final[$i]) {

                        $ponto_final[$i]['debito'] = "";
                    }
                    
                    if((date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 0 && $i == 1)){
                            
                        if($sinal_saldo_mes == "-"){
                            $ponto_final[$i]['debito']        = $saldo_mes;
                                
                            $ponto_final[$i]['debito']        =  str_replace(":00", "",$ponto_final[$i]['debito']);
                
                            $ponto_final['resultado_debito']  = Helpers_Vital::somaHoras(array($ponto_final[$i]['debito'], $ponto_final['resultado_debito'])); //05:00 - -08:30
                        }else{
                            $ponto_final[$i]['credito']       = $saldo_mes;
                                 
                            $ponto_final[$i]['credito']       =  str_replace(":00", "",$ponto_final[$i]['credito']);
                
                            $ponto_final['resultado_credito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['credito'], $ponto_final['resultado_credito'])); //05:00 - -08:30
                               
                        }
                    }
                    if((date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 6 && $i == 1)){

                        if($sinal_saldo_mes == "-"){
                            $ponto_final[$i]['debito']        = $saldo_mes;

                            $ponto_final[$i]['debito']        =  str_replace(":00", "",$ponto_final[$i]['debito']);

                            $ponto_final['resultado_debito']  = Helpers_Vital::somaHoras(array($ponto_final[$i]['debito'], $ponto_final['resultado_debito'])); //05:00 - -08:30
                       
                        }else{
                            $ponto_final[$i]['credito']       = $saldo_mes;

                            $ponto_final[$i]['credito']       =  str_replace(":00", "",$ponto_final[$i]['credito']);

                            $ponto_final['resultado_credito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['credito'], $ponto_final['resultado_credito'])); //05:00 - -08:30
                               
                        }
                    }
                    
                    if($this->view->usuario['id_horarios_expediente'] == 5 && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 3 && $i == 1)){

                        if($sinal_saldo_mes == "-"){
                            $ponto_final[$i]['debito']        = $saldo_mes;
                                
                            $ponto_final[$i]['debito']        =  str_replace(":00", "",$ponto_final[$i]['debito']);
                
                            $ponto_final['resultado_debito']  = Helpers_Vital::somaHoras(array($ponto_final[$i]['debito'], $ponto_final['resultado_debito'])); //05:00 - -08:30
                        }else{
                            $ponto_final[$i]['credito']       = $saldo_mes;
                                 
                            $ponto_final[$i]['credito']       =  str_replace(":00", "",$ponto_final[$i]['credito']);
                
                            $ponto_final['resultado_credito'] = Helpers_Vital::somaHoras(array($ponto_final[$i]['credito'], $ponto_final['resultado_credito'])); //05:00 - -08:30
                               
                        } 
                    }
   
                    /**
                     * retorna dia/mes/ano 
                     * 
                     * @var DataTime
                     */
                    $dias_da_semana = date("w", mktime(0, 0, 0, $mes, $i, $ano));
                   
                    if (!($ponto_final[$i]['dados']['entrou1_hora'])&&  !$ponto_final[$i]['feriado'] && !$ponto_final[$i]['ferias']) {

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example entrou1 = falta
                         */
                        $ponto_final[$i]['dados']['label'] = $horas['folga'][$dias_da_semana];

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu1 = falta
                         */
                        $ponto_final[$i]['dados']['label2'] = $horas['folga'][$dias_da_semana];

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example entrou2 = falta
                         */
                        $ponto_final[$i]['dados']['label3'] = $horas['folga'][$dias_da_semana];

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu2 = falta
                         */
                        $ponto_final[$i]['dados']['label4'] = $horas['folga'][$dias_da_semana];
                    }
            
                }
            
                $l_falta = "Falta";
                      
                if (!($ponto_final[$i]['dados']['entrou1_hora']) && ($ponto_final[$i]['id_ponto_movimento'] == 1) && $i > $cd['dia'] && $i < $data_fim && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 6) && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 0)) {
                                            
                    $ponto_final[$i]['dados']['label'] = "Atestado";
                    
                    $ponto_final[$i]['dados']['label2'] = "Atestado";

                    $ponto_final[$i]['dados']['label3'] = "Atestado";

                    $ponto_final[$i]['dados']['label4'] = "Atestado";
                            
       

                    if ($i == 8 && $mes == 2) {

                        $ponto_final[$i]['dados']['label'] = "Banco";

                        $ponto_final[$i]['dados']['label2'] = "Banco";

                        $ponto_final[$i]['dados']['label3'] = "Banco";

                        $ponto_final[$i]['dados']['label4'] = "Banco";
                    }

                    if($this->view->usuario['id_horarios_expediente'] == 5 && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) == 3)){

                        $ponto_final[$i]['dados']['label'] = "CIEE";

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu1 = falta
                         */
                        $ponto_final[$i]['dados']['label2'] = "CIEE";

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example entrou2 = falta
                         */
                        $ponto_final[$i]['dados']['label3'] = "CIEE";

                        /**
                         * retorna falta
                         * 
                         * @var String
                         * @example saiu2 = falta
                         */
                        $ponto_final[$i]['dados']['label4'] = "CIEE";

                    }



                    if ($i == 18 && $mes == 4) {

                        $ponto_final[$i]['dados']['label'] = "Folga";

                        $ponto_final[$i]['dados']['label2'] = "Folga";

                        $ponto_final[$i]['dados']['label3'] = "Folga";

                        $ponto_final[$i]['dados']['label4'] = "Folga";
                    }
                
                }

                /**
                 * se houve entrada 1
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['entrou1_hora'])) {

                    /**
                     * se houve entrada 1
                     * 
                     * @var Time
                     * @example entrou1 = 08:30
                     */
                    $ponto_final[$i]['dados']['label'] = $ponto_final[$i]['dados']['entrou1_hora'];

                    /**
                     * se houve entrada 2
                     * 
                     * @var Time
                     * @example saiu1 = 12:00
                     */
                    $ponto_final[$i]['dados']['label2'] = $ponto_final[$i]['dados']['entrou2_hora'];

                    /**
                     * se houve saiu 1
                     * 
                     * @var Time
                     * @example entrou2 = 13:00
                     */
                    $ponto_final[$i]['dados']['label3'] = $ponto_final[$i]['dados']['saiu1_hora'];

                    /**
                     * se houve saiu 2
                     * 
                     * @var Time
                     * @example saiu2 = 17:30
                     */
                    $ponto_final[$i]['dados']['label4'] = $ponto_final[$i]['dados']['saiu2_hora'];
                }
           

                /**
                 * se houve entrada1_manual
                 * 
                 * @var String
                 */
                if (($ponto_final[$i]['dados']['entrou1_manual'])) {

                    /**
                     * se houve entrada1_manual
                     * 
                     * @var Time
                     * @example entrou1 = *08:30
                     */
                    $ponto_final[$i]['dados']['label'] = "*" . $ponto_final[$i]['dados']['entrou1_hora'];
                }
                /**
                 * se houve entrada2_manual
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['entrou2_manual'])) {

                    /**
                     * se houve entrada2_manual
                     * 
                     * @var Time
                     * @example entrou1 = 08:30//saiu1 = 12:00//entrou2 = *13:00//saiu2 = 17:30//
                     */
                    $ponto_final[$i]['dados']['label2'] = "*" . $ponto_final[$i]['dados']['entrou2_hora'];
                }

                /**
                 * se houve saiu1_manual
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['saiu1_manual'])) {

                    /**
                     * se houve saiu1_manual
                     * 
                     * @var Time
                     * @example entrou1 = 08:30//saiu1 = *12:00//entrou2 = 13:00//saiu2 = 17:30//
                     */
                    $ponto_final[$i]['dados']['label3'] = "*" . $ponto_final[$i]['dados']['saiu1_hora'];
                }

                /**
                 * se houve saiu2_manual
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['saiu2_manual'])) {

                    /**
                     * se houve saiu2_manual
                     * 
                     * @var Time
                     * @example entrou1 = 08:30//saiu1 = 12:00//entrou2 = 13:00//saiu2 = *17:30//
                     */
                    $ponto_final[$i]['dados']['label4'] = "*" . $ponto_final[$i]['dados']['saiu2_hora'];
                }

                /**
                 * se houve entrada3_manual
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['entrou3_manual'])) {

                    /**
                     * se houve entrada2_manual
                     * 
                     * @var Time
                     * @example entrou1 = 08:30//saiu1 = 12:00//entrou2 = 13:00//saiu2 = 17:30//entrou3 = *18:00//saiu3 = 19:00//
                     */
                    $ponto_final[$i]['dados']['entrou3_hora'] = "*" . $ponto_final[$i]['dados']['entrou3_hora'];
                }

                /**
                 * se houve saiu3_manual
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['dados']['saiu3_manual'])) {

                    /**
                     * se houve entrada2_manual
                     * 
                     * @var Time
                     * @example entrou1 = 08:30//saiu1 = 12:00//entrou2 = 13:00//saiu2 = 17:30//entrou3 = 18:00//saiu3 = *19:00//
                     */
                    $ponto_final[$i]['dados']['saiu3_hora'] = "*" . $ponto_final[$i]['dados']['saiu3_hora'];
                }

                /**
                 * se houve feriado no dia
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['feriado']) && !$ponto_final[$i]['banco']) {

                    /**
                     * se houve Ferias
                     * 
                     * @var Time
                     * @example entrou1 = Ferias
                     */
                    $ponto_final[$i]['dados']['label'] = "Ferias";

                    /**
                     * se houve Ferias
                     *
                     * @var Time
                     * @example saiu1 = Ferias
                     */
                    $ponto_final[$i]['dados']['label2'] = "Ferias";

                    /**
                     * se houve Ferias
                     *
                     * @var Time
                     * @example entrou2 = Ferias
                     */
                    $ponto_final[$i]['dados']['label3'] = "Ferias";

                    /**
                     * se houve Ferias
                     *
                     * @var Time
                     * @example saiu2 = Ferias
                     */
                    $ponto_final[$i]['dados']['label4'] = "Ferias";
                }
 
                /**
                 * se houve ferias no dia
                 * 
                 * @var Time
                 */
                if (($ponto_final[$i]['ferias']) && !$ponto_final[$i]['banco']) {
                 
                    if($ponto_final[$i]['forcado']){
                     
                        $l_feriado = $ponto_final[$i]['forcado'];
                        
                    }else{
                           
                        $l_feriado = "Feriado";
                    }

                    if ($i == 9 && $mes == 2) {

                        $l_feriado = "Folga";
                    }

                    /**
                     * se houve feriado
                     * 
                     * @var Time
                     * @example entrou1 = feriado
                     */
                    $ponto_final[$i]['dados']['label'] = $l_feriado;

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example saiu1 = feriado
                     */
                    $ponto_final[$i]['dados']['label2'] = $l_feriado;

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example entrou2 = feriado
                        */
                    $ponto_final[$i]['dados']['label3'] = $l_feriado;

                   /**
                    * se houve feriado
                    *
                    * @var Time
                    * @example saiu2 = feriado
                    */
                    $ponto_final[$i]['dados']['label4'] = $l_feriado;
                }

                
                if ($i >= $cd['dia'] && $i <= $data_fim && $ponto_final[$i]['dados']['label'] == "Falta" && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 6) && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) != 0)) {

                   $ponto_final[$i]['saldo_diaria'] =  "-".$ponto_final[2]['carga_diaria'];
                   $ponto_final[$i]['saldo_diaria'] = str_replace("0:00", "0", $ponto_final[$i]['saldo_diaria']);
                   $ponto_final[$i]['debito_final'] =  $ponto_final[$i]['saldo_diaria'];

                }
                if (($ponto_final[$i]['id_ponto_movimento'] == 1) && !$ponto_final[$i]['dados']['label'] && (date("w", mktime(0, 0, 0, $mes, $i, $ano)) !== 6)) {

                    $ponto_final[$i]['dados']['label'] = "Atestado";
                    $ponto_final[$i]['saldo_diaria'] = "";
                    $ponto_final[$i]['motivo'] = $data['motivo'];
                }
                if (($ponto_final[$i]['id_ponto_movimento'] == 1) && !$ponto_final[$i]['dados']['label2']) {

                    $ponto_final[$i]['dados']['label2'] = "Atestado";
                    $ponto_final[$i]['saldo_diaria'] = "";
                    $ponto_final[$i]['motivo'] = $data['motivo'];
                }
                if (($ponto_final[$i]['id_ponto_movimento'] == 3) && !$ponto_final[$i]['dados']['label3']) {

                    $ponto_final[$i]['dados']['label3'] = "Atestado";
                    $ponto_final[$i]['saldo_diaria'] = "";
                    $ponto_final[$i]['motivo'] = $data['motivo'];
                }
                if (($ponto_final[$i]['id_ponto_movimento'] == 1) && !$ponto_final[$i]['dados']['label4']) {

                    $ponto_final[$i]['dados']['label4'] = "Atestado";
                    $ponto_final[$i]['saldo_diaria'] = "";
                    $ponto_final[$i]['motivo'] = $data['motivo'];
                }
                if (($ponto_final[$i]['ferias']) && $ponto_final[$i]['banco']) {

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example entrou1 = feriado
                     */
                    $ponto_final[$i]['dados']['label'] = "Folga";

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example saiu1 = feriado
                     */
                    $ponto_final[$i]['dados']['label2'] = "Folga";

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example entrou2 = feriado
                     */
                    $ponto_final[$i]['dados']['label3'] = "Folga";

                    /**
                     * se houve feriado
                     *
                     * @var Time
                     * @example saiu2 = feriado
                     */
                    $ponto_final[$i]['dados']['label4'] = "Folga";
                }
                /**
                 * bsaldo = subtração de bdeb e bcred
                 * 
                 * @var Time
                 * @example bsaldo = debito - credito.
                 */
                $ponto_final['bsaldo'] = Helpers_Vital::diminuiHoras(array($ponto_final['bdeb'], $ponto_final['bcred']));

                /**
                 * se no dia tiver motivo
                 * 
                 * @var Time
                 * @example Gini.
                 */
                if ($cd['id_ponto_movimento'] == 1) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label'] = "¨" . $ponto_final[$cd['dia']]['dados']['entrou1_hora'];
                }

                if ($cd['id_ponto_movimento'] == 2) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label3'] = "¨" . $ponto_final[$cd['dia']]['dados']['saiu1_hora'];
                }

                if ($cd['id_ponto_movimento'] == 3) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label2'] = "¨" . $ponto_final[$cd['dia']]['dados']['entrou2_hora'];
                }

                if ($cd['id_ponto_movimento'] == 4) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label4'] = "¨" . $ponto_final[$cd['dia']]['dados']['saiu2_hora'];
                }

                if ($cd['id_ponto_movimento'] == 5) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label'] = "¨" . $ponto_final[$cd['dia']]['dados']['entrou3_hora'];
                }

                if ($cd['id_ponto_movimento'] == 6) {

                    /**
                     * entrou1 recebe "¨"
                     *
                     * @var Time
                     * @example  credito = 01:00 então ¨01:00 .
                     */
                    $ponto_final[$cd['dia']]['dados']['label'] = "¨" . $ponto_final[$cd['dia']]['dados']['saiu3_hora'];
                }
                /**
                 * btotal = bdeb + bcred
                 * @var Time
                 * @example  01:00 + -02:00.
                 */
                $ponto_final['btotal'] = Helpers_Vital::somaHoras(array($ponto_final['bdeb'], $ponto_final[$i]['bcred']));

                /**
                 * bdeb = bdeb + debito
                 * @var Time
                 * @example  -01:00 + -02:00.
                 */
                $ponto_final['bdeb'] = Helpers_Vital::somaHoras(array($ponto_final['bdeb'], $ponto_final[$i]['debito']));
                /**
                 * soma total dos débitos
                 * @var Time
                 * @example  -01:00 + -02:00.
                 */
                $ponto_final['debito_total'] = Helpers_Vital::somaHoras(array($ponto_final['debito_total'], $ponto_final[$i]['debito']));
                /**
                 * soma total do saldo mensal
                 * @var Time
                 */
                $ponto_final['mes_total'] = Helpers_Vital::diminuiHoras(array($ponto_final['resultado_debito'], $ponto_final['resultado_credito']));
                        
                $ponto_final[$i]['saldo'] = Helpers_Vital::diminuiHoras(array($ponto_final['resultado_debito'], $ponto_final['resultado_credito'])); // -03:30 - -13:30
                $ponto_final['smes'] = $saldo_mes; // -03:30 - -13:30
                $ponto_final['smes'] = str_replace(":00", "", $ponto_final['smes']); // -03:30 - -13:30

                $saldo_total_mes = Helpers_Vital::extraiSinal($ponto_final[$i]["saldo_diaria"]);
            
                if($saldo_total_mes == "-"){
                
                    $ponto_final[$i]['debito_do_mes'] = $ponto_final[$i]["saldo_diaria"];
                    
            
                
                }else{
                
                    $ponto_final[$i]['credito_do_mes'] = $ponto_final[$i]["saldo_diaria"];

                }
                if($dia_inicio > $i && $mes == $mes_inicio){
                    
                    $ponto_final[$i]['debito_do_mes'] = "00:00";

                }
    
                $ponto_final['resultado_credito_mes'] = Helpers_Vital::somaHoras(array($ponto_final['resultado_credito_mes'], $ponto_final[$i]['credito_do_mes'])); // -03:30 - -13:30

                $ponto_final['resultado_debito_mes'] = Helpers_Vital::somaHoras(array($ponto_final['resultado_debito_mes'], $ponto_final[$i]['debito_do_mes'])); // -03:30 - -13:30

                $ponto_final['btotal_mes'] = Helpers_Vital::diminuiHoras(array($ponto_final['resultado_debito_mes'],  $ponto_final['resultado_credito_mes'])); // -03:30 - -13:30
           
                /**
                * bsaldo = soma de todos os saldos
                * @var Time
                */
                $ponto_final['bsaldo'] = $ponto_final[$i]['saldo'];
            }
            if ($ponto_final['bsaldo'] == null) {
                $ponto_final['bsaldo'] = $ponto_final[31]['saldo'];
            }
            if ($ponto_final['bsaldo'] == null) {
                $ponto_final['bsaldo'] = $ponto_final[30]['saldo'];
            }
            if ($ponto_final['bsaldo'] == null) {
                $ponto_final['bsaldo'] = $ponto_final[29]['saldo'];
            }
            if($this->view->usuario['id_horarios_expediente']== 5 && $mes ==6){
                
                $ponto_final['bsaldo'] = $ponto_final[9]['saldo'];
                
                $ponto_final['mes_total'] = $ponto_final['bsaldo'];
            }


            /**
             * retorna dia/mes/ano
             * @var Time
             */
            $dias_da_semana = date("w", mktime(0, 0, 0, $mes, $i, $ano));

            /**
             * Pega total de dias no mes.
             * 
             * @var Integer
             */
            $this->view->dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

            /**
             * Lista do ponto.
             * 
             * @var Array
             * @example $ponto_final = $this->view->ponto ['final'],['saldo'],['total'],['total_minutos3'].
             */
            $this->view->ponto = $ponto_final;

            /**
             * Saldo geral do mes.
             * 
             * @var Time
             */
            $this->view->saldo = $Ponto->saldoGeral($id_usuario, $mes, $ano);
            /**
             * array das horas mostrando as datas traduzidas
             * @example array 0 = Domingo 1 = Segunda-feira .
             */
            $this->view->horas = $horas;
            /**
             * Horário de trabalho.
             * @var Time
             */

            /**
             * Dados do usuario selecionado.
             * 
             * @var Array
             */
            $this->view->usuario = $Usuario->dadosUsuarioEmpresa($id_usuario);

            $this->view->dia_inicio = $dia_inicio;

            $this->view->dia_fim = $dia_fim;
        }

    }
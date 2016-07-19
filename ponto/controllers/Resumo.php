<?php

namespace stalker\application\ponto\controllers;

use stalker\library\Fw as Fw;
use stalker\application\ponto\models as models_ponto;
use stalker\application\index\models as models;

/**
 * Resumo do ponto.
 * 
 * @author Fernando Dias <rodox17@gmail.com>
 * @package APP
 * @subpackage Controllers
 */
class Resumo extends Fw\Controller {
	
    function indexAction(){
        
       // $this->setNoLayout();
        
        $id       = $_GET['id'];
        
        
                $this->view->mes_vital = Fw\Vital::getMes();
        $this->view->usuarios = models\Usuarios::fetchAll();
        
        if(!$id){
            
            $this->view->msg = "Selecione um colaborador";
            
            die;
            
        }
        
        /**
        * Solicitação do banco de dados.
        *
        * @name $db
        * @var Fw\DB
        */
       $Ponto     = new models_ponto\Ponto();

       /**
        * Todos usuarios que
        * batem ponto.
        * 
        * @var Array
        */
       $us        = models\Usuarios::fetchByID("id = $id");

       /**
        * retorna array dos meses e passa para mês escrito.
        *
        * @name $Ponto->mes
        * @var $this->view->mes
        * @example 1 -> janeiro; 2 -> fevereiro;
        */
              
       $mes       = array(

            "1"  => "JANEIRO",
            "2"  => "FEVEREIRO",
            "3"  => "MARÇO",
            "4"  => "ABRIL",
            "5"  => "MAIO",
            "6"  => "JUNHO",
            "7"  => "JULHO",
            "8"  => "AGOSTO",
            "9"  => "SETEMBRO",
            "10" => "OUTUBRO",
            "11" => "NOVEMBRO",
            "12" => "DEZEMBRO"

       );

       /**
        * Se retornar dia da semana do banco de dados converta para portugues.
        *
        * @name $Ponto->semana
        * @var $this->view->semana
        * @example Monday -> Seg; tuerday -> Ter;
        */
       $semana     = array(

            "Monday"    => "Seg",
            "Tuesday"   => "Ter",
            "Wednesday" => "Qua",
            "Thursday"  => "Qui",
            "Friday"    => "Sex",
            "Saturday"  => "Sáb",
            "Sunday"    => "Dom"

       );

        /**
         * Busca total de horas trabalhadas.
         *
         * @name $Ponto->historico
         * @var $this->view->historico
         * @example entrou1 = 08:00,saiu1 = 12:00,
         */
        $historico = $Ponto->historico($us['id'], $_REQUEST['mes'], date("Y"));

        /**
         * Busca total de horas trabalhadas.
         *
         * @name $Ponto->saldo
         * @var $this->view->saldo
         * @example final = -48:51
         */
      //  $saldo     = $Ponto->saldoGeral($us['id'], date("m") + 1, date("Y"));
        
        /**
         * Creditos do usuario.
         * 
         * @var Array
         */
        $creditos  = $Ponto->buscaGeral($us['id'], date("m"), date("Y"));

        $this->view->historico = $historico;
        $this->view->saldo     = $saldo;
        
        foreach($creditos as $c){

                /**
                 * Se encontrar o dia de hoje.
                 */
                if($historico[$i]['dia'] == $c['dia'] && $c['horas_compensado']){

                    /**
                     * Arruma a data.
                     * 
                     * @var String Time
                     */
                    $historico[$i]['final'] = helper\Helpers_Vital::somaHoras(array($historico[$i]['final'], $c['horas_compensado']));

                    /**
                     * Se valor for negativo.
                     */
                    if(strstr($historico[$i]['restante'], "-") === false){

                        /**
                         * Arruma a data.
                         *
                         * @var String Time
                         */
                        $historico[$i]['restante'] = helper\Helpers_Vital::somaHoras(array($c['horas_compensado'],$historico[$i]['restante']));

                    }else{

                            /**
                             * Arruma a data.
                             *
                             * @var String Time
                             */
                            $historico[$i]['restante'] = helper\Helpers_Vital::diminuiHoras(array($c['horas_compensado'],$historico[$i]['restante']));

                    }

                }

            }

    }
	
}
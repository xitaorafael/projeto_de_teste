<?php

namespace stalker\application\modules\ponto\controllers;

use stalker\library\Fw as Fw;
use stalker\application\modules\ponto\models as models;
use stalker\application\controllers\Helpers_Vital;

/**
 * JSON.
 * 
 * @author Fernando Dias <rodox17@gmail.com>
 * @package APP
 * @subpackage Controllers
 */
class Registro extends Fw\Controller {
	
	public $id_usuario;
	
	public function init(){
	
		//$this->setNoRender();
	
		/*header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");*/
	
	}
	
	public function baterPontoAction(){
		
		$this->setNoLayout();
		
		/**
		 * Solicitação do banco de dados.
		 *
		 * @name $db
		 * @var Fw\DB
		 */
		$db               = new Fw\DB();
		$Ponto            = new models\Ponto();
		
		$this->id_usuario = $_GET['id'];
		
		if(!$this->id_usuario){
			
			$this->view->mensagem = "Digital n&atilde;o encontrada.";
			
			exit;
			
		}
		
		$pt = $db->
				select()->
				from("get_ponto")->
				find("id_usuarios={$this->id_usuario} AND DATE(data) = CURDATE()");
		
		$ultima_minuto = explode(":", Helpers_Vital::diminuiHoras(array(date("H:i"), $pt['ultima_hora'])));

		if($pt['saiu3_data']){
			
			$this->view->mensagem = "Ponto fechado.";
			
		}else{
			
			$data_atual = date("Y-m-d H:i");
			
			for($i = 1; $i <= 3;  $i++){
				
				if($pt["entrou{$i}_data"]){
					
					if(!$pt["saiu{$i}_data"]){
					
						$dados["saiu{$i}"] = $data_atual;
						
						$i = 10;
						
					}
					
				}else{
					
					$dados["entrou{$i}"] = $data_atual;
					
					$i = 10;
					
				}
				
			}
			
			$dados['id_usuarios'] = $this->id_usuario;
			$dados['data']        = date("Y-m-d");
			
			if($pt){
				
				$db->atualizar($dados, "ponto", "id={$pt['id']}");
				
			}else{
				
				$db->salvar($dados, "ponto");
				
			}
			
			//$html_email = $this->partial("emailClt","ponto");
			
			//if(!Fw\Vital::enviarEmail($pt['email'], "Ponto Validado na data ".date("d/m/Y H:i"), $html_email)){
				
				//$this->view->mensagem = "ERRO. E-mail n&atilde;o enviado.";
				
			//}else{

			if(($ultima_minuto[1] < 10 && $pt['ultima_hora']) && $pt['dia'] == date("d")){
				
				$this->view->mensagem = "Ponto registrado({$ultima_minuto[1]}).";
				
			}else{
			
				$this->view->mensagem = "Ponto registrado.";
				
			}
			
		}
		
		//$this->view->saldo     = $Ponto->saldoGeral($this->id_usuario, date("m") - 1, date("Y"));
		$this->view->historico = $Ponto->historico($this->id_usuario);
		$this->view->historico = $this->view->historico[0];
		
	}
	
	public function emailCLTAction(){
		
		/**
		 * Solicitação do banco de dados.
		 *
		 * @name $db
		 * @var Fw\DB
		 */
		$Ponto                 = new models\Ponto();
		
		$this->view->historico = $Ponto->historico($this->id_usuario);
		//$this->view->saldo     = $Ponto->saldoGeral($this->id_usuario);
		
	}
	
}
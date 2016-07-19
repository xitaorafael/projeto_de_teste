<?php


namespace stalker\application\modules\ponto\controllers;

use stalker\library\Fw as Fw;
//error_reporting(E_ALL);ini_set('display_errors', 1);

/**
 * JSON.
 * 
 * @author Fernando Dias <rodox17@gmail.com>
 * @package APP
 * @subpackage Controllers
 */
class Cadastro extends Fw\Controller {
	
	/**
	 * Desabilita os templates
	 * e arruma o header da pagina
	 * para JSON.
	 */
	public function init(){
	
		$this->setNoRender();
	
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
	
	}
	
	/**
	 * Lista informações no BD de acordo com página
	 * selecionada.
	 *
	 * @method Fw/DB
	 * @access public
	 */
	public function atualizarTemplateAction(){
	
		/**
		 * Solicitação do banco de dados.
		 *
		 * @name $db
		 * @var Fw\DB
		 */
		$db       = new Fw\DB();
		$cache    = new Fw\Cache();
	
		$id       = $_GET['id'];
		$template = $_GET['template'];
	
		$db->atualizar(array("template" => $template), 'usuarios', "id=$id");
		
		$cache->clean("ponto_biometria");
		
		echo 1;
	
	}
	
	/**
	 * Lista informações no BD de acordo com página
	 * selecionada.
	 *
	 * @method Fw/DB
	 * @access public
	 */
	public function listarUsuariosAction(){
	
		/**
		 * Solicitação do banco de dados.
		 *
		 * @name $db
		 * @var Fw\DB
		 */
		$db   = new Fw\DB();
		
		$json =	$db->
				select()->
				from("usuarios")->
//				where('template IS NULL AND data_demissao IS NULL AND id_tipos_contrato_usuarios = 1')->
				where('bate_ponto IS NOT NULL AND template IS NULL')->
				order('nome')->
				fetchAll();
	
		foreach ($json as $j){
				
			$us[] = $j['id']."-".$j['nome'];
				
		}
			
		echo json_encode(array("usuarios"=>$us));
	
	}
	
	/**
	 * Lista informações no BD de acordo com página
	 * selecionada.
	 *
	 * @method Fw/DB
	 * @access public
	 */
	public function listarUsuariosTemplateAction(){
	
		/**
		 * Solicitação do banco de dados.
		 *
		 * @name $db
		 * @var Fw\DB
		 */
		$db    = new Fw\DB();
		$cache = new Fw\Cache();
		
		$us    = $cache->read("ponto_biometria");
		
		if(!$us){
	
			$json =	$db->
					select()->
					from("usuarios")->
					where('template IS NOT NULL')->
					fetchAll();
		
			foreach ($json as $j){
					
				$us[] = $j['id']."-".$j['template'];
					
			}
			
			$cache->save("ponto_biometria", $us);
			
		}
	
		echo json_encode(array("usuarios"=>$us));
	
	}
	
}
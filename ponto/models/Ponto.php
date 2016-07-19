<?php

namespace stalker\application\ponto\models;

use stalker\library\Fw as Fw;

class Ponto{
	
	public $db;
	
	public function __construct(){
		
		$this->db = new Fw\DB();
		
	}
	/**
	 * Busca total hora do banco de dados de cada usuario
	 * @param $id_usuario
	 */
	/**public function historico($id_usuario,$mes,$ano){
		
		return $this->db->
				select("*, @tempo := DATE_FORMAT(ADDTIME(total_minutos1, total_minutos2), '%h:%i') as 'total_horas_dia', if(total_minutos3 <> 0, ADDTIME(total_minutos3, @tempo),@tempo) as 'final' ")->
				from("get_ponto")->
				find("id_usuarios = $id_usuario AND DATE(data) = CURDATE()");
		
	}
	*/
	public function historico($id_usuario, $mes = null){
		
		$where_mes = $mes ? " AND mes = $mes" : null;
		
		return $this->db->
				select("*, @tempo := DATE_FORMAT(ADDTIME(total_minutos1, total_minutos2), '%h:%i') as 'total_horas_dia', DATE_FORMAT(ADDTIME(ifnull(total_minutos3,'00:00'),@tempo),'%H:%i') as 'final',TIME_FORMAT(TIMEDIFF(@tempo,total_horas_diarias), '%H:%i') as 'restante' ")->
				from("get_ponto")->
				where("id_usuarios = $id_usuario $where_mes")->
				order("data desc")->
				fetchAll();
		
	}
	
	/**
	 * busca saldo total do mês anterior, total de horas trabalhadas e carga horário total.
	 * 
	 * @param $id_usuario
	 * @param $mes
	 * @param $ano
	 */
	public function saldoGeral($id_usuario,$mes,$ano){
		
		$saldo = $this->db->procedure("pesquisar_ponto_saldo($id_usuario, $mes, $ano)");
		
		return $saldo[0];
		
	}
	/**
	 * Busca horas compensado, motivo para falta e dia_compensado(ferias, feriados)
	 * 
	 * @param $id_usuario
	 * @param $mes
	 * @param $ano
	 */
	public function buscaGeral($id_usuario,$mes,$ano){
		//die("pesquisar_ponto_creditos($id_usuario, $mes, $ano)");
		$saldo = $this->db->procedure("pesquisar_ponto_creditos($id_usuario, $mes, $ano)");
		
		return $saldo;
	}
	
}
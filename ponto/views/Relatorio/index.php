<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>

	<base href='<?php echo BASE; ?>' />
	
	<meta name="robots" content="noindex,nofollow" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<style>
	
	html,body{
		
		margin:0;
		padding: 0;
		
		background: url(public/images/wp2.jpg) repeat-x;
	}
	
	<?php 
	
	$mes = $_POST['mes'];
	$ano = $_POST['ano'];
	$horario_exp = explode(" ", $this->view->horas['horarios']{$this->view->horario['id_horarios_expediente']});
	
	?>
	
	#arrow-left {
		position: absolute;
		right: 60px;
	   	width: 0px;
	    height: 0px;
	    margin-top: 20px;
	    border-top: 10px solid transparent;
	    border-bottom: 10px solid transparent;
	    border-right: 10px solid #5d5d5d;
	}
	
	</style>
	
</head>

<body>

<div style='padding: 10px;'>

	<h2>Relatório do Cartão Ponto</h2>
	
	<hr />
	
	<br />
	
	<form method='post' target='_blank' action="<?php echo $this->view->action; ?>">
	
		<select name='id_usuarios' style="padding: 10px;">
		
			<option value=''>Selecione Colaborador</option>
	
			<?php foreach($this->view->usuarios as $u): ?>
			
			<option <?php echo $u['id']==$_POST['id_usuarios']?"selected='selected'":null; ?> value='<?php echo $u['id']; ?>'><?php echo $u['nome']; ?></option>
			
			<?php endforeach; ?>
		
		</select>
		
		&nbsp;
		&nbsp;
	
		<select name='mes' style="padding: 10px;">
		
			<option value=''>Selecione Mês</option>
	
			<?php foreach($this->view->mes_vital as $d => $m): ?>
		
			<option <?php echo ($this->view->dados['mes'] && $d==$this->view->dados['mes'])||(!$this->view->dados['mes'] &&  $d==date("m"))?"selected='selected'":null; ?> value='<?php echo $d; ?>'><?php echo $m; ?></option>
			
			<?php endforeach; ?>
		
		</select>
		Ano: 
		<input type='text' style="background:#f0f0f0;"value='<?php echo date("Y"); ?>' name='ano' />
		
		&nbsp;
		&nbsp;
		
		<div id='arrow-left'></div>
		
		<div style='position: absolute; right: 0;
		border-top-left-radius: 10px;
		border-bottom-left-radius: 10px;
		background: #5d5d5d;padding: 10px;'>
		
			<input type='image' class='tip' title='Exportar Lista para Excel' 
			height='35px' style="margin-top: 3px;"
			src='<?php echo BASE; ?>public/images/icones/printer.png' />
			
			
		</div>
		
		<div style='position: absolute; bottom: 0;
		margin-left: 550px;
		border-top-left-radius: 10px;
		border-bottom-left-radius: 10px;
'>		
			<input type='image' class='tip' title='Exportar Lista para Excel' 
			height='140px' style="margin-top: 3px;"
			src='<?php echo BASE; ?>public/images/icon.png' />
			
			
		</div>
	
	</form>
	
</div>
</body>
</html>


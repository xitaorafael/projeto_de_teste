<?php header("Access-Control-Allow-Origin: *"); header("Content-Type: text/html; charset=UTF-8"); ?>

<style>

body{

	font-family: arial;
}

.box{

	background-color: red; 
	border: 2px solid #680000;
	color:white;position: absolute;width: 200px;
	top: 10px;left: 10px;padding:10px;
	text-align: center;
	border-radius: 10px;
	padding-left: 20px;
	background-image: url(http://192.168.50.12/stalker3/public/images/icones/error.png);
	background-repeat: no-repeat;
	background-position: 10px;

}

</style>

<?php if($this->view->mensagem): ?>

<div class='box'><?php echo $this->view->mensagem; ?></div>

<?php endif; ?>

<?php if($this->view->historico): ?>

<div style='text-align: center;margin-top: 50px;margin-bottom: 50px;
border-bottom: 1px dashed gray;padding-bottom: 30px;'>

	<img height="200" width='150' style="border: 1px solid black;"
	src='<?php echo BASE; ?>data/uploads/<?php echo $this->view->historico['arquivo']; ?>' />
	
	<h2><?php echo $this->view->historico['nome']; ?></h2>
	
</div>

<div style='text-align: center;
padding-bottom: 40px;'>

	<table border='1' align='center' 
	style="width: 600px;text-align: center;font-size: 20px;">
	
		<thead>
	
			<tr style="background: #EAEAEA;">
			
				<th>Entrou</th>
				<th>Saiu</th>
				<th>Entrou</th>
				<th>Saiu</th>
				
				<?php if($this->view->historico['entrou3_hora']): ?>
				
				<th>Entrou</th>
				<th>Saiu</th>
				
				<?php endif; ?>
				
			</tr>
			
		</thead>
		
		<tbody>
		
			<tr style="background: #EAEAEA;">
			
				<td style="text-align: center;"><?php echo $this->view->historico['entrou1_hora']; ?></td>
				<td style="text-align: center;"><?php echo $this->view->historico['saiu1_hora']; ?></td>
				<td style="text-align: center;"><?php echo $this->view->historico['entrou2_hora']; ?></td>
				<td style="text-align: center;"><?php echo $this->view->historico['saiu2_hora']; ?></td>
				
				<?php if($this->view->historico['entrou3_hora']): ?>
				
				<td style="text-align: center;"><?php echo $this->view->historico['entrou3_hora']; ?></td>
				<td style="text-align: center;"><?php echo $this->view->historico['saiu3_hora']; ?></td>
				
				<?php endif;?>
				
			</tr>
		
		</tbody>
	
	</table>
	
</div>

<?php if($this->view->historico['saiu1_hora']): ?>

<div style='padding-top: 10px;text-align: center;border-top: 1px dashed gray;'>

	Total de horas dia:<br />
	
	<strong style="font-size: 25px;"><?php echo $this->view->historico['final']; ?></strong><br /><br />
	
	Saldo Geral:<br />
	
	<strong style="font-size: 25px;">
	
		<?php if($this->view->saldo['saldo'] >= 0): ?>
		
			<font style="color: green;">
		
				<?php echo $this->view->saldo['saldo']; ?>
				
			</font>
		
		<?php else: ?>
		
			<font style="color: red;">
		
				<?php echo $this->view->saldo['saldo']; ?>
				
			</font>
		
		<?php endif; ?>
		
	</strong>
	
</div>

<?php endif; ?>

<?php endif; ?>
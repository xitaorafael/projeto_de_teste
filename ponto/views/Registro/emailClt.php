<div style='text-align: center;
padding-bottom: 50px;'>

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

<div style='padding-top: 20px;text-align: center;border-top: 1px dashed gray;'>

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
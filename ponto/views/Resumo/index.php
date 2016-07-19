<div style='text-align: center;padding-bottom: 50px;font-family: arial;'>
    
    <form action="<?php echo BASE;?>index.php/ponto/Resumo/index/">
        
        Selecione seu Nome<br />
    
        <select name='id'>

            <option value=''>Selecione</option>

            <?php foreach($this->view->usuarios as $u): ?>

            <option <?php echo $_GET['id'] == $u['id'] ? "selected='selected'" : null; ?> value='<?php echo $u['id']; ?>'><?php echo $u['nome']; ?></option>

            <?php endforeach; ?>

        </select>
        
        	&nbsp;
		
                &nbsp;

		<select name='mes'>
		
			<option value=''>Selecione Mes</option>
	
			<?php foreach($this->view->mes_vital as $d => $mes): ?>
		
			<option <?php echo ($this->view->dados['mes'] && $d==$this->view->dados['mes'])||(!$this->view->dados['mes'] &&  $d==date("m"))?"selected='selected'":null; ?> value='<?php echo $d; ?>'><?php echo $mes; ?></option>
			
			<?php endforeach; ?>
		
		</select>
        
        <input type='submit' value='Gerar' />
        
    </form>
    
    <?php if(!$this->view->msg): ?>
    
    <hr style="border: 0;border-top: 1px dashed gray;" />

    <h2>
        
        <strong>RESUMO DIÁRIA</strong>
    
    </h2>

    <table border='1' align='center' style='width: 600px;text-align: center;font-size: 20px;'>

        <thead>

            <tr style='background: #EAEAEA;'>
                
                <th>Dia</th>
                <th>Entrou</th>
                <th>Saiu</th>
                <th>Entrou</th>
                <th>Saiu</th>
                <th>Entrou</th>
                <th>Saiu</th>

            </tr>

    </thead>

    <tbody>
        
        <?php foreach($this->view->historico as $historico): ?>

            <?php 
            
            /**
             * Zebra.
             * 
             * @var String Cor
             */
            $cor    = $count++ % 2 ? '#eaeaea' : 'white';

            ?>

            <tr style='background: <?php echo $cor; ?>'>

                <td style='text-align: center;font-family:monospace;'><?php echo $historico['dia']?></td>
                <td style='text-align: center;'><?php echo $historico['entrou1_hora']?></td>
                <td style='text-align: center;'><?php echo $historico['saiu1_hora']?></td>
                <td style='text-align: center;'><?php echo $historico['entrou2_hora']?></td>
                <td style='text-align: center;'><?php echo $historico['saiu2_hora']?></td>
                <td style='text-align: center;'><?php echo $historico['entrou3_hora']?></td>
                <td style='text-align: center;'><?php echo $historico['saiu3_hora']?></td>

            </tr>

          <?php endforeach; ?>

        </tbody>

    </table>

</div>

<div style='padding-top: 20px;text-align: center;border-top: 1px dashed gray;'>

    <br />

    <strong style='font-size: 25px;'>

        <?php if(strstr($this->view->saldo['saldo'], "-") === false): ?>

        <font style='color: green;'>

            <?php echo $this->view->saldo['saldo']; ?>

        </font>

        <?php else: ?>

        <font style='color: red;'>

            <?php echo $this->view->saldo['saldo']; ?>

        </font>

        <?php endif; ?>

    </strong>

</div>

<?php endif; ?>
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

            }

            <?php
            $mes = $_POST['mes'];
            $ano = $_POST['ano'];
            $horario_exp = explode(" ", $this->view->horas['horarios']{$this->view->horario['id_horarios_expediente']});
            ?>

        </style>

    </head>

    <body>

        <img height='60' style="float: left;" 
             src='<?php echo $this->view->usuario['logo']; ?>'/>

        <div style="height: 60px;font-size: 12px;padding-left: 10px;
             border-left: 1px solid gray;margin-left: 100px;">

            <h2 style="margin: 0;padding: 0;padding-top: 5px;">CARTÃO PONTO</h2>
            DE <?php
            $b_date = "$ano-$mes-1";

            date("t", strtotime($b_date));

            $dia_final = (date("t", strtotime($b_date)));

            echo date(strlen($i) == 1 ? "t/0$mes/$ano" : "01/0$mes/$ano");
            ?> ATÉ <?php echo date(strlen($i) == 1 ? "0t/0$mes/$ano" : "$dia_final/0$mes/$ano");
            ?><br />
            EMITIDO EM <?php echo date("d/m/Y H:i:s"); ?>

        </div>

        <div style='padding: 10px;padding-top: 0;'>

            <table style="float: left;width: 49%;height:150px;font-size: 10px;
                   margin-right: 1%;" cellspacing='0' cellpadding='0'>

                <tr>

                    <td>Empresa</td>
                    <td><strong><?php echo $this->view->usuario['nome_empresa']; ?></strong></td>

                </tr>

                <tr>

                    <td style="border-top: 1px solid;border-bottom: 1px solid;">CNPJ</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;"><?php echo $this->view->usuario['cnpj_br']; ?></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">Inscrição Est.</td>
                    <td style="border-bottom: 1px solid;"><?php echo $this->view->usuario['ie']; ?></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">Nome</td>
                    <td style="border-bottom: 1px solid;"><strong><?php echo $this->view->usuario['nome']; ?></strong></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">N° PIS/PASEP </td>
                    <td style="border-bottom: 1px solid;"><?php echo $this->view->usuario['numero_pis']; ?></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">CTPS</td>
                    <td style="border-bottom: 1px solid;"><?php echo $this->view->usuario['numero_ctps']; ?> Admissão: <?php echo $this->view->usuario['data_admissao_br']; ?></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">Função</td>
                    <td style="border-bottom: 1px solid;"><?php echo $this->view->horas['cargo']{$this->view->horario['id_cargos']}; ?></td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">Departamento</td>
                    <td style="border-bottom: 1px solid;"><?php echo $this->view->usuario['nome_grupo']; ?></td>

                </tr>

            </table>

            <table cellspacing='0' cellpadding='0' 
                   style="width: 49%;height:150px;font-size: 10px;">

                <tr>

                    <td colspan='7'><strong>Horário de Trabalho</strong></td>

                </tr>

                <tr>

                    <td style="border-top: 1px solid;border-bottom: 1px solid;"></td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">ENT 1</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">SAI 1</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">ENT 2</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">SAI 2</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">ENT 3</td>
                    <td style="border-top: 1px solid;border-bottom: 1px solid;">ENT 3</td>

                </tr>

                <tr>

                    <td>SEG</td>
                    <td><?php echo $horario_exp[0]; ?></td>
                    <td><?php echo $horario_exp[1]; ?></td>
                    <td><?php echo $horario_exp[2]; ?></td>
                    <td><?php echo $horario_exp[3]; ?></td>
                    <td>--:--</td>
                    <td>--:--</td>

                </tr>

                <tr>

                    <td>TER</td>
                    <td><?php echo $horario_exp[0]; ?></td>
                    <td><?php echo $horario_exp[1]; ?></td>
                    <td><?php echo $horario_exp[2]; ?></td>
                    <td><?php echo $horario_exp[3]; ?></td>
                    <td>--:--</td>
                    <td>--:--</td>

                </tr>

                <tr>

                    <td>QUA</td>
                    <td><?php echo $horario_exp[0]; ?></td>
                    <td><?php echo $horario_exp[1]; ?></td>
                    <td><?php echo $horario_exp[2]; ?></td>
                    <td><?php echo $horario_exp[3]; ?></td>
                    <td>--:--</td>
                    <td>--:--</td>

                </tr>

                <tr>

                    <td>QUI</td>
                    <td><?php echo $horario_exp[0]; ?></td>
                    <td><?php echo $horario_exp[1]; ?></td>
                    <td><?php echo $horario_exp[2]; ?></td>
                    <td><?php echo $horario_exp[3]; ?></td>
                    <td>--:--</td>
                    <td>--:--</td>

                </tr>

                <tr>

                    <td>SEX</td>
                    <td><?php echo $horario_exp[0]; ?></td>
                    <td><?php echo $horario_exp[1]; ?></td>
                    <td><?php echo $horario_exp[2]; ?></td>
                    <td><?php echo $horario_exp[3]; ?></td>
                    <td>--:--</td>
                    <td>--:--</td>

                </tr>

                <tr>

                    <td>SAB</td>
                    <td>Folga</td>
                    <td>Folga</td>
                    <td>Folga</td>
                    <td>Folga</td>
                    <td>Folga</td>
                    <td>Folga</td>

                </tr>

                <tr>

                    <td style="border-bottom: 1px solid;">DOM</td>
                    <td style="border-bottom: 1px solid;">Folga</td>
                    <td style="border-bottom: 1px solid;">Folga</td>
                    <td style="border-bottom: 1px solid;">Folga</td>
                    <td style="border-bottom: 1px solid;">Folga</td>
                    <td style="border-bottom: 1px solid;">Folga</td>
                    <td style="border-bottom: 1px solid;">Folga</td>

                </tr>

            </table>

            <table cellspacing='0' cellpadding='0' width='100%' 
                   style="margin-top: 10px;font-size: 10px;font-family: monospace;">

                <thead>

                    <tr>

                        <th align="left">DIA</th>
                        <th>ENT. 1</th>
                        <th>SAI. 1</th>
                        <th>ENT. 2</th>
                        <th>SAI. 2</th>
                        <th>ENT. 3</th>
                        <th>SAI. 3</th>
                        <th>CARGA</th>
                        <th>NORMAIS</th>
                        <th>BCRED.</th>
                        <th>BDEB.</th>
                        <th>BTOTAL</th>
                        <th>BSALDO</th>
                        <th>MOTIVO</th>
                        <th>S.MÊS</th>

                    </tr>

                    <tr style="font-weight: normal;">

                        <th style="border-bottom: 1px solid;" colspan="7" align="left">TOTAIS</th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['carga_total_mes']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['normal_total_mes']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['bcred_mes']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;">-<?php echo $this->view->ponto['resultado_debito_mes']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['btotal_mes']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['bsaldo']; ?></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"></th>
                        <th style="border-bottom: 1px solid;font-weight: normal;"><?php echo $this->view->ponto['smes']; ?></th>

                    </tr>

                    <tr>

                        <th style="border-bottom: 1px solid;" colspan="14" align="left">&nbsp;</th>

                    </tr>

                </thead>

                <?php for ($i = $this->view->dia_inicio; $i <= $this->view->dias_mes; $i++): ?>

                    <tbody>

                        <tr style="text-align: center;<?php echo $this->view->ponto[$i]['feriado'] ? "color: green;" : null; ?><?php echo $this->view->ponto[$i]['compensado'] ? "color: red;" : null; ?><?php echo $this->view->ponto[$i]['ferias'] ? "color: blue;" : null; ?>">

                            <td style="border-bottom: 1px solid;text-align: left;">

                                <?php
                                $dias_da_semana = date("w", mktime(0, 0, 0, $mes, $i, $ano));
                                echo date(strlen($i) == 1 ? "0$i/0$mes/$ano" : "$i/0$mes/$ano") . " - {$this->view->horas['dias_semana'][$this->view->ponto[$i]['dados']['dia_semana']]}";
                                if (empty($this->view->ponto[$i]['dados']['dia_semana'])) {
                                    echo $this->view->horas['semana_numero']{$dias_da_semana};
                                };
                                ?>

                            </td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['label']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['label3']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['label2']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['label4']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['entrou3_hora']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['dados']['saiu3_hora']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['carga_diaria']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['horas_trabalhadas_clone']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['credito_final']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]['debito_final']; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]["saldo_diaria"]; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]["saldo"]; ?></td>
                            <td style="border-bottom: 1px solid;"><?php echo $this->view->ponto[$i]["motivo"]; ?></td>
                        </tr>
                        <?php
                        If ($i == $this->view->dia_fim) {
                            $i = 100;
                        }
                        ?>
<?php endfor; ?>

                </tbody>

            </table>

            <div style='width: 49%;text-align: center;float:left;
                 font-size: 13px;margin-top: 5px;margin-right: 2%;'>

<?php echo "( * ) - Batida lançada manualmente"; ?>

            </div>

            <div style='width: 49%;text-align: center;float:left;
                 font-size: 13px;margin-top: 5px;'>

<?php echo "( ¨ ) - Abono Parcial"; ?>

            </div>

            <div style='width: 49%;text-align: center;float:left;
                 border-top: 1px solid;margin-top: 40px;margin-right: 2%;'>

<?php echo $this->view->usuario['nome']; ?>

            </div>

            <div style='width: 49%;text-align: center;float:left;
                 border-top: 1px solid;margin-top: 40px;'>

                DIRETOR/GERENTE

            </div>

        </div>

    </body>

    <script>window.print();</script>
</html>
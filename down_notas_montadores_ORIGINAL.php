<?php
//Conexão ao Banco de dados local
include "php/config.php";

$mont = $_POST['mont'];
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$ativo = 0;
$not = $_POST['not'];
if($not == 2){
	$cond = "o.status = '$not'";
	$cond_data = "d.data_saida_montador >= '$data_inicio' AND d.data_saida_montador <= '$data_fim'";
	$texto = "MONTAGENS EM ATENDIMENTO";
}
else{
	$cond = "o.status = '1' OR o.status = '3' OR o.status = '5' OR o.status = '7' OR o.status = '8' OR o.status = '9'";
	$cond_data = "d.data_entrega_montador >= '$data_inicio' AND d.data_entrega_montador <= '$data_fim'";
	$texto = "MONTAGENS CONCLU&Iacute;DAS";
}

		$SQL = "SELECT m.nome, c.prioridade, c.tipo, c.*, pr.preco_montador, d.*, o.* FROM montadores m, clientes c, datas d, ordem_montagem o, produtos pd, precos pr WHERE c.cod_cliente = pd.cod_produto AND pr.id_preco = pd.id_preco AND c.n_montagem = o.n_montagem AND d.n_montagens = o.n_montagem AND ($cond_data) AND (o.id_montador = m.id_montadores AND m.id_montadores = '$mont' AND ($cond)) AND c.ativo='$ativo' ORDER BY o.n_montagem ASC";
		
		//echo $mont;
		//echo "<br>";
		//echo $SQL;
		//echo "<br>";
		//exit();
		$executa = mysql_query($SQL)or die(mysql_error());
$ini = explode("-",$data_inicio);
$arquivo_inicio = $ini[2]."_".$ini[1]."_".$ini[0];
$fin = explode("-",$data_fim);
$arquivo_final = $fin[2]."_".$fin[1]."_".$fin[0];

			$data_inicio = new DateTime($data_inicio);  
			$data_inicio = $data_inicio->format('d/m/Y');
			
			$data_fim = new DateTime($data_fim);  
			$data_fim = $data_fim->format('d/m/Y');


header("Content-type: application/msexcel");

// Como será gravado o arquivo
header("Content-Disposition: attachment; filename=relatorio_pg_montadores_de_".$arquivo_inicio."_ate_".$arquivo_final.".xls");
		// montando a tabela
		echo "<table border='1' width='100%' cellspacing='1'>
				<tr>
					<td colspan='5' align='center'><b><h3>$texto DO DIA $data_inicio &Aacute; $data_fim</h3></b></td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>				
				<tr>
				  <td align='center' width='100' bgcolor='#F2F2F2'><b>Nota</b></td>
				  <td align='center' width='100' bgcolor='#F2F2F2'><b>Qdt. Produto</b></td>
				  <td align='center' width='300' bgcolor='#F2F2F2'><b>Produto</b></td>";
			if($not == 2){
				 echo" <td align='center' width='100' bgcolor='#F2F2F2'><b>Visto Montador</b></td>
				  <td align='center' width='100' bgcolor='#F2F2F2'><b>Visto Ger&ecirc;ncia</b></td>
			   </tr>";
			}
			else{
				 echo" <td align='center' width='100' bgcolor='#F2F2F2'><b>Valor Unit&aacute;rio</b></td>
				  <td align='center' width='100' bgcolor='#F2F2F2'><b>Total</b></td>
			   </tr>";
			}
		$total=0;
		$total_geral =0;
		while ($rs = mysql_fetch_array($executa)){
		            $nome = $rs[nome];
			if($rs[status] == 1){$status = "<img src='images/tools.png' border='0' />";}
			elseif($rs[status] == 2){$status = "<img src='images/ampulheta.gif' border='0' align='absbottom' />";}
			elseif($rs[status] == 3){$status = "<img src='images/tick.png' border='0' />";}
			elseif($rs[status] == 4){$status = "<img src='images/ico_excluir.jpg' border='0' />";}
			elseif($rs[status] == 5){$status = "<img src='images/justice.png' border='0' />";}
			elseif($rs[status] == 6){$status = "<img src='images/ausente.png' border='0' width='24' />";}

			if($rs[data_final] != '0000-00-00'){
				$data_final = new DateTime($rs[data_final]);  
				$data_final = $data_final->format('d/m/Y');
			}
			else{
				$data_final = 'NÃO DEFINIDO';
			}
			if($rs[data_entrega_montador] != '0000-00-00'){
				$data_entrega_montador = new DateTime($rs[data_entrega_montador]);  
				$data_entrega_montador = $data_entrega_montador->format('d/m/Y');
			}
			else{
				$data_entrega_montador = 'NÃO DEFINIDO';
			}
			
			$prioridade = $rs["prioridade"];
			$tipo		= $rs["status"];
/*			
	if($prioridade == 0){
		if($modelo == 0){$tipo = 3;}
		if($modelo == 2){$tipo = 7;}
		if($modelo == 3){$tipo = 8;}
		if($modelo == 1){$tipo = 9;}
	}
	elseif($prioridade == 2){
		if($modelo == 0){$tipo = 5;}
		if($modelo == 2){$tipo = 7;}
		if($modelo == 3){$tipo = 8;}
		if($tipo == 9){$modelo = 1;}
	}
	elseif($prioridade == 4){
		if($tipo == 3){$modelo = 0;}
		if($tipo == 7){$modelo = 2;}
		if($tipo == 8){$modelo = 3;}
		if($tipo == 9){$modelo = 1;}
	}
*/
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total = (7.50*$rs['qtde_cliente']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total = (9.00*$rs['qtde_cliente']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total = (($rs['preco_montador']/2)*$rs['qtde_cliente']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total = (7.50*$rs['qtde_cliente']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total = (9.00*$rs['qtde_cliente']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total = (($rs['preco_montador']/2)*$rs['qtde_cliente']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total = (5.00*$rs['qtde_cliente']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total = ($rs['preco_montador']*$rs['qtde_cliente']);
				}

			}
			
			$select_preco2 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente2]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco2."<br>";
			$query_preco2 = mysql_query($select_preco2);
			$p = mysql_fetch_array($query_preco2);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total2 = (7.50*$rs['qtde_cliente2']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total2 = (9.00*$rs['qtde_cliente2']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total2 = (($p['preco_montador']/2)*$rs['qtde_cliente2']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total2 = (7.50*$rs['qtde_cliente2']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total2 = (9.00*$rs['qtde_cliente2']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total2 = (($p['preco_montador']/2)*$rs['qtde_cliente2']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total2 = (($p['preco_montador']/2)*$rs['qtde_cliente2']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total2 = (5.00*$rs['qtde_cliente2']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total2 = ($p['preco_montador']*$rs['qtde_cliente2']);
				}

			}
			
			$select_preco3 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente3]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco3."<br>";
			$query_preco3 = mysql_query($select_preco3);
			$q = mysql_fetch_array($query_preco3);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total3 = (7.50*$rs['qtde_cliente3']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total3 = (9.00*$rs['qtde_cliente3']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total3 = (($q['preco_montador']/2)*$rs['qtde_cliente3']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total3 = (7.50*$rs['qtde_cliente3']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total3 = (9.00*$rs['qtde_cliente3']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total3 = (($q['preco_montador']/2)*$rs['qtde_cliente3']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total3 = (($q['preco_montador']/2)*$rs['qtde_cliente3']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total3 = (5.00*$rs['qtde_cliente3']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total3 = ($q['preco_montador']*$rs['qtde_cliente3']);
				}

			}
			
			$select_preco4 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente4]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco4."<br>";
			$query_preco4 = mysql_query($select_preco4);
			$r = mysql_fetch_array($query_preco4);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total4 = (7.50*$rs['qtde_cliente4']);
				}

				// montagem tecnica
				elseif($tipo == 8){
					$total4 = (9.00*$rs['qtde_cliente4']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total4 = (($r['preco_montador']/2)*$rs['qtde_cliente4']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total4 = (7.50*$rs['qtde_cliente4']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total4 = (9.00*$rs['qtde_cliente4']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total4 = (($r['preco_montador']/2)*$rs['qtde_cliente4']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total4 = (($r['preco_montador']/2)*$rs['qtde_cliente4']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total4 = (5.00*$rs['qtde_cliente4']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total4 = ($r['preco_montador']*$rs['qtde_cliente4']);
				}

			}
			
			$select_preco5 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente5]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco5."<br>";
			$query_preco5 = mysql_query($select_preco5);
			$s = mysql_fetch_array($query_preco5);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total5 = (7.50*$rs['qtde_cliente5']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total5 = (9.00*$rs['qtde_cliente5']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total5 = (($s['preco_montador']/2)*$rs['qtde_cliente5']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total5 = (7.50*$rs['qtde_cliente5']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total5 = (9.00*$rs['qtde_cliente5']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total5 = (($s['preco_montador']/2)*$rs['qtde_cliente5']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total5 = (($s['preco_montador']/2)*$rs['qtde_cliente5']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total5 = (5.00*$rs['qtde_cliente5']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total5 = ($s['preco_montador']*$rs['qtde_cliente5']);
				}

			}

			$select_preco6 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente6]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco6."<br>";
			$query_preco6 = mysql_query($select_preco6);
			$t = mysql_fetch_array($query_preco6);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total6 = (7.50*$rs['qtde_cliente6']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total6 = (9.00*$rs['qtde_cliente6']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total6 = (($t['preco_montador']/2)*$rs['qtde_cliente6']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total6 = (7.50*$rs['qtde_cliente6']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total6 = (9.00*$rs['qtde_cliente6']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total6 = (($t['preco_montador']/2)*$rs['qtde_cliente6']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total6 = (($t['preco_montador']/2)*$rs['qtde_cliente6']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total6 = (5.00*$rs['qtde_cliente6']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total6 = ($t['preco_montador']*$rs['qtde_cliente6']);
				}

			}
			$select_preco7 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente7]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco7."<br>";
			$query_preco7 = mysql_query($select_preco7);
			$u = mysql_fetch_array($query_preco7);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total7 = (7.50*$rs['qtde_cliente7']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total7 = (9.00*$rs['qtde_cliente7']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total7 = (($u['preco_montador']/2)*$rs['qtde_cliente7']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total7 = (7.50*$rs['qtde_cliente7']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total7 = (9.00*$rs['qtde_cliente7']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total7 = (($u['preco_montador']/2)*$rs['qtde_cliente7']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total7 = (5.00*$rs['qtde_cliente7']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total7 = ($u['preco_montador']*$rs['qtde_cliente7']);
				}

			}
			$select_preco8 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente8]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco8."<br>";
			$query_preco8 = mysql_query($select_preco8);
			$v = mysql_fetch_array($query_preco8);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total8 = (7.50*$rs['qtde_cliente8']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total8 = (9.00*$rs['qtde_cliente8']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total8 = (($v['preco_montador']/2)*$rs['qtde_cliente8']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total8 = (7.50*$rs['qtde_cliente8']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total8 = (9.00*$rs['qtde_cliente8']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total8 = (($v['preco_montador']/2)*$rs['qtde_cliente8']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total8 = (5.00*$rs['qtde_cliente8']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total8 = ($v['preco_montador']*$rs['qtde_cliente8']);
				}

			}
			$select_preco9 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente9]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco9."<br>";
			$query_preco9 = mysql_query($select_preco9);
			$x = mysql_fetch_array($query_preco9);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total9 = (7.50*$rs['qtde_cliente9']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total9 = (9.00*$rs['qtde_cliente9']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total9 = (($x['preco_montador']/2)*$rs['qtde_cliente9']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total9 = (7.50*$rs['qtde_cliente9']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total9 = (9.00*$rs['qtde_cliente9']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total9 = (($x['preco_montador']/2)*$rs['qtde_cliente9']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total9 = (5.00*$rs['qtde_cliente9']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total9 = ($x['preco_montador']*$rs['qtde_cliente9']);
				}

			}
			$select_preco10 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente10]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco10."<br>";
			$query_preco10 = mysql_query($select_preco10);
			$z = mysql_fetch_array($query_preco10);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total10 = (7.50*$rs['qtde_cliente10']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total10 = (9.00*$rs['qtde_cliente10']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total10 = (($z['preco_montador']/2)*$rs['qtde_cliente10']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total10 = (7.50*$rs['qtde_cliente10']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total10 = (9.00*$rs['qtde_cliente10']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total10 = (($z['preco_montador']/2)*$rs['qtde_cliente10']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total10 = (5.00*$rs['qtde_cliente10']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total10 = ($z['preco_montador']*$rs['qtde_cliente10']);
				}

			}
			$select_preco11 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente11]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco11."<br>";
			$query_preco11 = mysql_query($select_preco11);
			$a = mysql_fetch_array($query_preco11);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total11 = (7.50*$rs['qtde_cliente11']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total11 = (9.00*$rs['qtde_cliente11']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total11 = (($a['preco_montador']/2)*$rs['qtde_cliente11']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total11 = (7.50*$rs['qtde_cliente11']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total11 = (9.00*$rs['qtde_cliente11']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total11 = (($a['preco_montador']/2)*$rs['qtde_cliente11']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total11 = (5.00*$rs['qtde_cliente11']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total11 = ($a['preco_montador']*$rs['qtde_cliente11']);
				}

			}
			$select_preco12 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente12]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco12."<br>";
			$query_preco12 = mysql_query($select_preco12);
			$b = mysql_fetch_array($query_preco12);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total12 = (7.50*$rs['qtde_cliente12']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total12 = (9.00*$rs['qtde_cliente12']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total12 = (($b['preco_montador']/2)*$rs['qtde_cliente12']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total12 = (7.50*$rs['qtde_cliente12']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total12 = (9.00*$rs['qtde_cliente12']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total12 = (($b['preco_montador']/2)*$rs['qtde_cliente12']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total12 = (5.00*$rs['qtde_cliente12']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total12 = ($b['preco_montador']*$rs['qtde_cliente12']);
				}

			}
			$select_preco13 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente13]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco13."<br>";
			$query_preco13 = mysql_query($select_preco13);
			$c = mysql_fetch_array($query_preco13);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total13 = (7.50*$rs['qtde_cliente13']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total13 = (9.00*$rs['qtde_cliente13']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total13 = (($c['preco_montador']/2)*$rs['qtde_cliente13']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total13 = (7.50*$rs['qtde_cliente13']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total13 = (9.00*$rs['qtde_cliente13']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total13 = (($c['preco_montador']/2)*$rs['qtde_cliente13']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total13 = (5.00*$rs['qtde_cliente13']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total13 = ($c['preco_montador']*$rs['qtde_cliente13']);
				}

			}
			$select_preco14 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente14]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco14."<br>";
			$query_preco14 = mysql_query($select_preco14);
			$d = mysql_fetch_array($query_preco14);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total14 = (7.50*$rs['qtde_cliente14']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total14 = (9.00*$rs['qtde_cliente14']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total14 = (($d['preco_montador']/2)*$rs['qtde_cliente14']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total14 = (7.50*$rs['qtde_cliente14']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total14 = (9.00*$rs['qtde_cliente14']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total14 = (($d['preco_montador']/2)*$rs['qtde_cliente14']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);

				}
				// montagem justica
				elseif($tipo == 5){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total14 = (5.00*$rs['qtde_cliente14']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total14 = ($d['preco_montador']*$rs['qtde_cliente14']);
				}

			}
			$select_preco15 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente15]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco15."<br>";
			$query_preco15 = mysql_query($select_preco15);
			$e = mysql_fetch_array($query_preco15);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total15 = (7.50*$rs['qtde_cliente15']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total15 = (9.00*$rs['qtde_cliente15']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total15 = (($e['preco_montador']/2)*$rs['qtde_cliente15']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total15 = (7.50*$rs['qtde_cliente15']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total15 = (9.00*$rs['qtde_cliente15']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total15 = (($e['preco_montador']/2)*$rs['qtde_cliente15']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total15 = (5.00*$rs['qtde_cliente15']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total15 = ($e['preco_montador']*$rs['qtde_cliente15']);
				}

			}
			$select_preco16 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente16]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco16."<br>";
			$query_preco16 = mysql_query($select_preco16);
			$f = mysql_fetch_array($query_preco16);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total16 = (7.50*$rs['qtde_cliente16']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total16 = (9.00*$rs['qtde_cliente16']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total16 = (($f['preco_montador']/2)*$rs['qtde_cliente16']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total16 = (7.50*$rs['qtde_cliente16']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total16 = (9.00*$rs['qtde_cliente16']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total16 = (($f['preco_montador']/2)*$rs['qtde_cliente16']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total16 = (5.00*$rs['qtde_cliente16']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total16 = ($f['preco_montador']*$rs['qtde_cliente16']);
				}

			}
			$select_preco17 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente17]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco17."<br>";
			$query_preco17 = mysql_query($select_preco17);
			$g = mysql_fetch_array($query_preco17);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total17 = (7.50*$rs['qtde_cliente17']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total17 = (9.00*$rs['qtde_cliente17']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total17 = (($g['preco_montador']/2)*$rs['qtde_cliente17']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total17 = (7.50*$rs['qtde_cliente17']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total17 = (9.00*$rs['qtde_cliente17']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total17 = (($g['preco_montador']/2)*$rs['qtde_cliente17']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total17 = (5.00*$rs['qtde_cliente17']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total17 = ($g['preco_montador']*$rs['qtde_cliente17']);
				}

			}
			$select_preco18 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente18]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco18."<br>";
			$query_preco18 = mysql_query($select_preco18);
			$h = mysql_fetch_array($query_preco18);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total18 = (7.50*$rs['qtde_cliente18']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total18 = (9.00*$rs['qtde_cliente18']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total18 = (($h['preco_montador']/2)*$rs['qtde_cliente18']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total18 = (7.50*$rs['qtde_cliente18']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total18 = (9.00*$rs['qtde_cliente18']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total18 = (($h['preco_montador']/2)*$rs['qtde_cliente18']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total18 = (5.00*$rs['qtde_cliente18']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total18 = ($h['preco_montador']*$rs['qtde_cliente18']);
				}

			}
			$select_preco19 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente19]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco19."<br>";
			$query_preco19 = mysql_query($select_preco19);
			$i = mysql_fetch_array($query_preco19);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total19 = (7.50*$rs['qtde_cliente19']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total19 = (9.00*$rs['qtde_cliente19']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total19 = (($i['preco_montador']/2)*$rs['qtde_cliente19']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total19 = (7.50*$rs['qtde_cliente19']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total19 = (9.00*$rs['qtde_cliente19']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total19 = (($i['preco_montador']/2)*$rs['qtde_cliente19']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total19 = (5.00*$rs['qtde_cliente19']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total19 = ($i['preco_montador']*$rs['qtde_cliente19']);
				}

			}
			$select_preco20 = "SELECT p.preco_montador FROM precos p, produtos pd WHERE pd.cod_produto = '$rs[cod_cliente20]' AND p.id_preco = pd.id_preco ";
			//echo $select_preco20."<br>";
			$query_preco20 = mysql_query($select_preco20);
			$j = mysql_fetch_array($query_preco20);
			
			if($prioridade == 0){
				// montagem normal
				if($tipo == 3){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total20 = (7.50*$rs['qtde_cliente20']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total20 = (9.00*$rs['qtde_cliente20']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total20 = (($j['preco_montador']/2)*$rs['qtde_cliente20']);
				}

			}
			elseif($prioridade == 2){
				// juridico normal
				if($tipo == 3){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// juridico justica
				elseif($tipo == 5){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// juridico revisao
				elseif($tipo == 7){
					$total20 = (7.50*$rs['qtde_cliente20']);
				}
				// juridico tecnica
				elseif($tipo == 8){
					$total20 = (9.00*$rs['qtde_cliente20']);
				}
				// juridico desmontagem
				elseif($tipo == 9){
					$total20 = (($j['preco_montador']/2)*$rs['qtde_cliente20']);
				}

			}
			elseif($prioridade == 4){
				// montagem normal
				if($tipo == 3){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// montagem justica
				elseif($tipo == 5){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// montagem revisao
				elseif($tipo == 7){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}
				// montagem tecnica
				elseif($tipo == 8){
					$total20 = (5.00*$rs['qtde_cliente20']);
				}
				// montagem desmontagem
				elseif($tipo == 9){
					$total20 = ($j['preco_montador']*$rs['qtde_cliente20']);
				}

			}

			$total_geral += $total+$total2+$total3+$total4+$total5+$total6+$total7+$total8+$total9+$total10+$total11+$total12+$total13+$total14+$total15+$total16+$total17+$total18+$total19+$total20;
			
			$total = number_format($total,2,',','');
			$total2 = number_format($total2,2,',','');
			$total3 = number_format($total3,2,',','');
			$total4 = number_format($total4,2,',','');
			$total5 = number_format($total5,2,',','');
			$total6 = number_format($total6,2,',','');
			$total7 = number_format($total7,2,',','');
			$total8 = number_format($total8,2,',','');
			$total9 = number_format($total9,2,',','');
			$total10 = number_format($total10,2,',','');
			$total11 = number_format($total11,2,',','');
			$total12 = number_format($total12,2,',','');
			$total13 = number_format($total13,2,',','');
			$total14 = number_format($total14,2,',','');
			$total15 = number_format($total15,2,',','');
			$total16 = number_format($total16,2,',','');
			$total17 = number_format($total17,2,',','');
			$total18 = number_format($total18,2,',','');
			$total19 = number_format($total19,2,',','');
			$total20 = number_format($total20,2,',','');
			
			if($prioridade == 0){
				if($tipo == 3){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 5){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 7){
					$preco_montador = number_format(7.50,2,',','');
					$preco_montador2 = number_format(7.50,2,',','');
					$preco_montador3 = number_format(7.50,2,',','');
					$preco_montador4 = number_format(7.50,2,',','');
					$preco_montador5 = number_format(7.50,2,',','');
					$preco_montador6 = number_format(7.50,2,',','');
					$preco_montador7 = number_format(7.50,2,',','');
					$preco_montador8 = number_format(7.50,2,',','');
					$preco_montador9 = number_format(7.50,2,',','');
					$preco_montador10 = number_format(7.50,2,',','');
					$preco_montador11 = number_format(7.50,2,',','');
					$preco_montador12 = number_format(7.50,2,',','');
					$preco_montador13 = number_format(7.50,2,',','');
					$preco_montador14 = number_format(7.50,2,',','');
					$preco_montador15 = number_format(7.50,2,',','');
					$preco_montador16 = number_format(7.50,2,',','');
					$preco_montador17 = number_format(7.50,2,',','');
					$preco_montador18 = number_format(7.50,2,',','');
					$preco_montador19 = number_format(7.50,2,',','');
					$preco_montador20 = number_format(7.50,2,',','');	
				}
				elseif($tipo == 8){
					$preco_montador = number_format(9.00,2,',','');
					$preco_montador2 = number_format(9.00,2,',','');
					$preco_montador3 = number_format(9.00,2,',','');
					$preco_montador4 = number_format(9.00,2,',','');
					$preco_montador5 = number_format(9.00,2,',','');
					$preco_montador6 = number_format(9.00,2,',','');
					$preco_montador7 = number_format(9.00,2,',','');
					$preco_montador8 = number_format(9.00,2,',','');
					$preco_montador9 = number_format(9.00,2,',','');
					$preco_montador10 = number_format(9.00,2,',','');
					$preco_montador11 = number_format(9.00,2,',','');
					$preco_montador12 = number_format(9.00,2,',','');
					$preco_montador13 = number_format(9.00,2,',','');
					$preco_montador14 = number_format(9.00,2,',','');
					$preco_montador15 = number_format(9.00,2,',','');
					$preco_montador16 = number_format(9.00,2,',','');
					$preco_montador17 = number_format(9.00,2,',','');
					$preco_montador18 = number_format(9.00,2,',','');
					$preco_montador19 = number_format(9.00,2,',','');
					$preco_montador20 = number_format(9.00,2,',','');	
				}
				elseif($tipo == 9){
					$preco_montador = number_format(($rs['preco_montador']/2),2,',','');
					$preco_montador2 = number_format(($p['preco_montador']/2),2,',','');
					$preco_montador3 = number_format(($q['preco_montador']/2),2,',','');
					$preco_montador4 = number_format(($r['preco_montador']/2),2,',','');
					$preco_montador5 = number_format(($s['preco_montador']/2),2,',','');
					$preco_montador6 = number_format(($t['preco_montador']/2),2,',','');
					$preco_montador7 = number_format(($u['preco_montador']/2),2,',','');
					$preco_montador8 = number_format(($v['preco_montador']/2),2,',','');
					$preco_montador9 = number_format(($x['preco_montador']/2),2,',','');
					$preco_montador10 = number_format(($z['preco_montador']/2),2,',','');
					$preco_montador11 = number_format(($a['preco_montador']/2),2,',','');
					$preco_montador12 = number_format(($b['preco_montador']/2),2,',','');
					$preco_montador13 = number_format(($c['preco_montador']/2),2,',','');
					$preco_montador14 = number_format(($d['preco_montador']/2),2,',','');
					$preco_montador15 = number_format(($e['preco_montador']/2),2,',','');
					$preco_montador16 = number_format(($f['preco_montador']/2),2,',','');
					$preco_montador17 = number_format(($g['preco_montador']/2),2,',','');
					$preco_montador18 = number_format(($h['preco_montador']/2),2,',','');
					$preco_montador19 = number_format(($i['preco_montador']/2),2,',','');
					$preco_montador20 = number_format(($j['preco_montador']/2),2,',','');	
				}
			}
			elseif($prioridade == 2){
				if($tipo == 3){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 5){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 7){
					$preco_montador = number_format(7.50,2,',','');
					$preco_montador2 = number_format(7.50,2,',','');
					$preco_montador3 = number_format(7.50,2,',','');
					$preco_montador4 = number_format(7.50,2,',','');
					$preco_montador5 = number_format(7.50,2,',','');
					$preco_montador6 = number_format(7.50,2,',','');
					$preco_montador7 = number_format(7.50,2,',','');
					$preco_montador8 = number_format(7.50,2,',','');
					$preco_montador9 = number_format(7.50,2,',','');
					$preco_montador10 = number_format(7.50,2,',','');
					$preco_montador11 = number_format(7.50,2,',','');
					$preco_montador12 = number_format(7.50,2,',','');
					$preco_montador13 = number_format(7.50,2,',','');
					$preco_montador14 = number_format(7.50,2,',','');
					$preco_montador15 = number_format(7.50,2,',','');
					$preco_montador16 = number_format(7.50,2,',','');
					$preco_montador17 = number_format(7.50,2,',','');
					$preco_montador18 = number_format(7.50,2,',','');
					$preco_montador19 = number_format(7.50,2,',','');
					$preco_montador20 = number_format(7.50,2,',','');	
				}
				elseif($tipo == 8){
					$preco_montador = number_format(9.00,2,',','');
					$preco_montador2 = number_format(9.00,2,',','');
					$preco_montador3 = number_format(9.00,2,',','');
					$preco_montador4 = number_format(9.00,2,',','');
					$preco_montador5 = number_format(9.00,2,',','');
					$preco_montador6 = number_format(9.00,2,',','');
					$preco_montador7 = number_format(9.00,2,',','');
					$preco_montador8 = number_format(9.00,2,',','');
					$preco_montador9 = number_format(9.00,2,',','');
					$preco_montador10 = number_format(9.00,2,',','');
					$preco_montador11 = number_format(9.00,2,',','');
					$preco_montador12 = number_format(9.00,2,',','');
					$preco_montador13 = number_format(9.00,2,',','');
					$preco_montador14 = number_format(9.00,2,',','');
					$preco_montador15 = number_format(9.00,2,',','');
					$preco_montador16 = number_format(9.00,2,',','');
					$preco_montador17 = number_format(9.00,2,',','');
					$preco_montador18 = number_format(9.00,2,',','');
					$preco_montador19 = number_format(9.00,2,',','');
					$preco_montador20 = number_format(9.00,2,',','');						
				}
				elseif($tipo == 9){
					$preco_montador = number_format(($rs['preco_montador']/2),2,',','');
					$preco_montador2 = number_format(($p['preco_montador']/2),2,',','');
					$preco_montador3 = number_format(($q['preco_montador']/2),2,',','');
					$preco_montador4 = number_format(($r['preco_montador']/2),2,',','');
					$preco_montador5 = number_format(($s['preco_montador']/2),2,',','');
					$preco_montador6 = number_format(($t['preco_montador']/2),2,',','');
					$preco_montador7 = number_format(($u['preco_montador']/2),2,',','');
					$preco_montador8 = number_format(($v['preco_montador']/2),2,',','');
					$preco_montador9 = number_format(($x['preco_montador']/2),2,',','');
					$preco_montador10 = number_format(($z['preco_montador']/2),2,',','');
					$preco_montador11 = number_format(($a['preco_montador']/2),2,',','');
					$preco_montador12 = number_format(($b['preco_montador']/2),2,',','');
					$preco_montador13 = number_format(($c['preco_montador']/2),2,',','');
					$preco_montador14 = number_format(($d['preco_montador']/2),2,',','');
					$preco_montador15 = number_format(($e['preco_montador']/2),2,',','');
					$preco_montador16 = number_format(($f['preco_montador']/2),2,',','');
					$preco_montador17 = number_format(($g['preco_montador']/2),2,',','');
					$preco_montador18 = number_format(($h['preco_montador']/2),2,',','');
					$preco_montador19 = number_format(($i['preco_montador']/2),2,',','');
					$preco_montador20 = number_format(($j['preco_montador']/2),2,',','');						
				}
			}
			elseif($prioridade == 4){
				if($tipo == 3){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 5){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
				elseif($tipo == 7){
					$preco_montador = number_format(7.50,2,',','');
					$preco_montador2 = number_format(7.50,2,',','');
					$preco_montador3 = number_format(7.50,2,',','');
					$preco_montador4 = number_format(7.50,2,',','');
					$preco_montador5 = number_format(7.50,2,',','');
					$preco_montador6 = number_format(7.50,2,',','');
					$preco_montador7 = number_format(7.50,2,',','');
					$preco_montador8 = number_format(7.50,2,',','');
					$preco_montador9 = number_format(7.50,2,',','');
					$preco_montador10 = number_format(7.50,2,',','');
					$preco_montador11 = number_format(7.50,2,',','');
					$preco_montador12 = number_format(7.50,2,',','');
					$preco_montador13 = number_format(7.50,2,',','');
					$preco_montador14 = number_format(7.50,2,',','');
					$preco_montador15 = number_format(7.50,2,',','');
					$preco_montador16 = number_format(7.50,2,',','');
					$preco_montador17 = number_format(7.50,2,',','');
					$preco_montador18 = number_format(7.50,2,',','');
					$preco_montador19 = number_format(7.50,2,',','');
					$preco_montador20 = number_format(7.50,2,',','');	
				}
				elseif($tipo == 8){
					$preco_montador = number_format(5.00,2,',','');
					$preco_montador2 = number_format(5.00,2,',','');
					$preco_montador3 = number_format(5.00,2,',','');
					$preco_montador4 = number_format(5.00,2,',','');
					$preco_montador5 = number_format(5.00,2,',','');
					$preco_montador6 = number_format(5.00,2,',','');
					$preco_montador7 = number_format(5.00,2,',','');
					$preco_montador8 = number_format(5.00,2,',','');
					$preco_montador9 = number_format(5.00,2,',','');
					$preco_montador10 = number_format(5.00,2,',','');
					$preco_montador11 = number_format(5.00,2,',','');
					$preco_montador12 = number_format(5.00,2,',','');
					$preco_montador13 = number_format(5.00,2,',','');
					$preco_montador14 = number_format(5.00,2,',','');
					$preco_montador15 = number_format(5.00,2,',','');
					$preco_montador16 = number_format(5.00,2,',','');
					$preco_montador17 = number_format(5.00,2,',','');
					$preco_montador18 = number_format(5.00,2,',','');
					$preco_montador19 = number_format(5.00,2,',','');
					$preco_montador20 = number_format(5.00,2,',','');						
				}
				elseif($tipo == 9){
					$preco_montador = number_format($rs['preco_montador'],2,',','');
					$preco_montador2 = number_format($p['preco_montador'],2,',','');
					$preco_montador3 = number_format($q['preco_montador'],2,',','');
					$preco_montador4 = number_format($r['preco_montador'],2,',','');
					$preco_montador5 = number_format($s['preco_montador'],2,',','');
					$preco_montador6 = number_format($t['preco_montador'],2,',','');
					$preco_montador7 = number_format($u['preco_montador'],2,',','');
					$preco_montador8 = number_format($v['preco_montador'],2,',','');
					$preco_montador9 = number_format($x['preco_montador'],2,',','');
					$preco_montador10 = number_format($z['preco_montador'],2,',','');
					$preco_montador11 = number_format($a['preco_montador'],2,',','');
					$preco_montador12 = number_format($b['preco_montador'],2,',','');
					$preco_montador13 = number_format($c['preco_montador'],2,',','');
					$preco_montador14 = number_format($d['preco_montador'],2,',','');
					$preco_montador15 = number_format($e['preco_montador'],2,',','');
					$preco_montador16 = number_format($f['preco_montador'],2,',','');
					$preco_montador17 = number_format($g['preco_montador'],2,',','');
					$preco_montador18 = number_format($h['preco_montador'],2,',','');
					$preco_montador19 = number_format($i['preco_montador'],2,',','');
					$preco_montador20 = number_format($j['preco_montador'],2,',','');	
				}
			}				
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total</td>";				
			}
			echo "</tr>";
			if(strlen($rs['cod_cliente2'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente2]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente2]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador2</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total2</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente3'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente3]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente3]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador3</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total3</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente4'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente4]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente4]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador4</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total4</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente5'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente5]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente5]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador5</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total5</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente6'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente6]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente6]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador6</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total6</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente7'])>0){
			$ii++;			
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente7]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente7]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador7</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total7</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente8'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente8]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente8]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador8</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total8</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente9'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente9]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente9]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador9</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total9</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente10'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente10]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente10]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador10</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total10</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente11'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente11]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente11]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador11</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total11</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente12'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente12]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente12]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador12</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total12</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente13'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente13]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente13]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador13</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total3</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente14'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente14]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente14]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador14</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total14</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente15'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente15]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente15]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador15</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total15</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente16'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente16]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente16]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador16</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total16</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente17'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente17]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente17]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador17</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total17</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente18'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente18]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente18]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador18</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total18</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente19'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente19]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente19]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador19</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total19</td>";				
			}
			echo "</tr>";
			}
			if(strlen($rs['cod_cliente20'])>0){
			$ii++;
			echo "<tr>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[n_montagem]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[qtde_cliente20]</td>";
				echo "<td align='center' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>$rs[produto_cliente20]</td>";
			if($not == 2){
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'></td>";				
			}
			else{
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $preco_montador20</td>";								
				echo "<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' bgcolor='#F2F2F2'>R$ $total20</td>";				
			}
			echo "</tr>";
			}
			$ii++;
		}
			$total_geral = number_format($total_geral,2,',','');
	if($not == 2){
		  echo "<tr>
		  			<td align='right' colspan='4' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' ><strong></strong></td>
		  			<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'  align='center'><strong></strong></td>
		  		</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
			</table>
			<table width='100%'>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>Data: _____/_____/__________</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>Ass: _______________________________________________________________<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>$nome</b></td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
		</table>";
	}
	else{
		  echo "<tr>
		  			<td align='right' colspan='4' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;' ><strong>TOTAL BRUTO</strong></td>
		  			<td align='right' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'  align='center'><strong>R$ $total_geral</strong></td>
		  		</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
			</table>
			<table width='100%'>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>Data: _____/_____/__________</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>Ass: _______________________________________________________________<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>$nome</b></td>
				</tr>
				<tr>
					<td align='left' colspan='5' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif;'>&nbsp;</td>
				</tr>
		</table>";
	}
?>
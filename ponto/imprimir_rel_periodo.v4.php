<?

// Includes
include_once "valida_login.php";
include_once "config.php";
include_once "classes/classefuncionario.php";
include_once "classes/classeEmp.php";
include_once "funcoes_ponto.php";

$ESTILO="<span style=\"font-family:verdana; font-size:12px;\"><b>";
$ESTILO_FIM="</b> </span>";
$ESTILO2="<span style=\"font-family:verdana; font-size:12px; color:";
#.$cor.";\">Falta</span> - ";
$ESTILO_FIM2="</span> - ";

// Cria objeto
$objFuncionario = new Funcionario;
$objEmp = new Emp;

// Declara vari�veis
$erro = null;
$totHora = null;


//$idFuncionario = $_POST['idFuncionario'];
$txtDia = $_POST['txtDia'];
$txtMes = $_POST['txtMes'];
$txtAno = $_POST['txtAno'];
$txtDia2 = $_POST['txtDia2'];
$txtMes2 = $_POST['txtMes2'];
$txtAno2 = $_POST['txtAno2'];
#print "txtDia: $txtDia, txtMes: $txtMes, txtAno: $txtAno, txtDia2: $txtDia2,
#txtMes2: $txtMes2, txtAno2: $txtAno2<br>";
$MES_FALTAS = "\nFaltas calculadas em $now_date_Y relativas ao periodo $txtDia-$txtMes-$txtAno";
$MES_FALTAS .= " at&eacute; $txtDia2-$txtMes2-$txtAno2:\n";
$MES_FALTAS .= str_repeat  ("-",strlen($MES_FALTAS)) . "\n";

$ASSUNTO="Ponto: Faltas calculadas em $now_date_Y relativas ao mes $txtMes/$txtAno";
##$sender="fatura@wb.com.br";
##$destinatario="fatura@wb.com.br";
$assunto=$ASSUNTO;

function somaHora($arrHoras){
  $arrSai = null; 
  for($w=0; $w<sizeof($arrHoras); $w++){
	if($w % 2 == 0){
		$arrEnt[] = $arrHoras[$w];
	}else{
		$arrSai[] = $arrHoras[$w];
	}
  }

  for($h=0; $h<sizeof($arrEnt); $h++){
	$resEnt = strtotime($arrEnt[$h]);
	$resSai	= strtotime($arrSai[$h]);
	$result	= ($resEnt - $resSai) / 3600;
	$arrResult[] = $result; 
  }
  $arrHes = null;
  for($z=0; $z<sizeof($arrResult); $z++){
	$arrHes = $arrHes +  $arrResult[$z];	
  }
  echo "Horas trabalhadas: ".ceil($arrHes)/-1;
}


/*
$query	= "SELECT DISTINCT(date_format(po_data, \"%Y-%m-%d\")) 
	FROM ponto WHERE date_format(po_data, \"%Y-%m\") = '2006-01'";
$result	= mysql_query($query);
while($arr = mysql_fetch_array($result))
	echo $arr[0]."<br>";

exit();
*/

?>
<html>
<head>
		<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">

</head>
<body topmargin="2" leftmargin="0">


<center><img src="img/logo.jpg"></center><br>

<table align="center" border="1" width="800px" cellpadding="0" cellspacing="0" style="border: 1px solid #000000;">
 <tr>
 	<td colspan="2" class="textoLogin" align="left">&nbsp;&nbsp;Periodo: <?=$txtDia?> / <?=$txtMes?> / <?=$txtAno?></td>
 </tr>
 <tr>
 	<td colspan="2" class="admDados" height="25" align="center">Cart�o de ponto</td>
 </tr>
 <tr>
 	<td colspan="2" class="textoLogin" height="25" align="left">
 	<?
 		$arrDadosd = $objEmp->getEmpForm();
 		$varDadosd = explode("|",$arrDadosd[0]);
 		echo $varDadosd[1]."<br>";
 		echo $varDadosd[2]."<br>";
 		echo "CNPJ ".$varDadosd[3]."<br>";
 	?>
 	</td>
 </tr>
</table>

<table align="center" border="1" width="800px" cellpadding="0" cellspacing="0" style="border: 1px solid #000000;">	
<?
$arrDadosId = $objFuncionario->getFuncionarioFormT2();	
#Retorna todos os funcionarios em um array.
#print_r($arrDadosId);
#95|Horário Padrão (9:00 às 18:00)|PEDRO ALVES
#FILHO|ADMIN|44071038|1990709-6 ifp|68868910730|2010-03-17|ADMIN|4
for($t=0; $t<sizeof($arrDadosId); $t++)  #Percorre todos os funcionarios...
{
 $varDados = explode("|",$arrDadosId[$t]);
 //print "varDados: $varDados<br>";
 //echo $varDados[0]."<br>";

 //$arrDados = $objFuncionario->getFuncionarioForm($idFuncionario);
 //$varDados = explode("|",$arrDados[0]);

 $dtAdmFunc = explode("-",$varDados[7]);	#Data Admissao func.
 $dtAdmFunc = $dtAdmFunc[2]."/".$dtAdmFunc[1]."/".$dtAdmFunc[0]; 

 ?>
 <tr>
 <td>
 <?
 //echo "<td class=\"textoLogin\" height=\"25\" align=\"left\">|<i>".$varDados[2]."</i> </td>";
 ##### Imprime nome do funcion. $varDados[2]
 echo "<span style=\"font-family:verdana; font-size:12px;\"><b>".$varDados[2]."</b></span><br><br>";
 ##if(true)	#$txtDia == 00)  #Imprime relatorio de todos os dias de todo mundo
	            #21-3-2010: Vamos comecar no dia 25 e prosseguir ate o
		    #dia 24 do mes seguinte.
 #{
	$nr_faltas=0;
	##print "txtDia: $txtDia<br>";
	#tabela ponto: 25    4     2006-01-06 12:55:36 Friday     201.36.98.18
	#            po_id fu_id 	po_data        po_semana   po_ip
 	$query0	= "SELECT DISTINCT(date_format(po_data, \"%d-%m-%Y\")),po_semana 
		FROM ponto WHERE date_format(po_data, \"%Y-%m\") 
		>= '$txtAno-$txtMes-$txtDia'
		AND date_format(po_data, \"%Y-%m\") <= '$txtAno2-$txtMes2-$txtDia2'
		";
 	$query2	= "SELECT DISTINCT po_data,po_semana 
		FROM ponto WHERE po_data
		>= '$txtAno-$txtMes-$txtDia'
		AND po_data <= '$txtAno2-$txtMes2-$txtDia2'
		";
 	$query	= "SELECT DISTINCT(date_format(po_data, \"%d-%m-%Y\")),po_semana 
		FROM ponto WHERE date_format(po_data, \"%Y-%m-%d\") 
		>= '$txtAno-$txtMes-$txtDia'
		AND date_format(po_data, \"%Y-%m-%d\") <= '$txtAno2-$txtMes2-$txtDia2'
		";
		#order by po_data
		#";	#Nao e' conveniente 'order by po_data' pois permite
			#fraude: bater um ponto depois e alterar a data e hora.

	##print "query: $query<br>";
 	$result	= mysql_query($query);
 	while($arr = mysql_fetch_array($result))
 	{
		$feriado=false; $fim_de_semana=false;
		$dia_tratado=$arr[0];
		#if($ultimo_dia + 1 < $dia_tratado)
		#{
		#Pulou um dia ou mais. Imprima as faltas de marcacao de ponto.
		#}
		#print "Dia do ano: $arr[0] <br>";
		if(isset($FERIADOS_DIAS[$dia_tratado]))
		{ 
		 #print "$dia_tratado: Feriado";
		 $feriado=true;
		}
 		switch($arr[1]){
 			case "Monday":
 				$semana = $arr[0]."(<i>Seg</i>)&nbsp;&nbsp;";
 				break;
 			case "Tuesday":
 				$semana = $arr[0]."(<i>Ter</i>)&nbsp;&nbsp;";
 				break;
 			case "Wednesday":
 				$semana = $arr[0]."(<i>Qua</i>)&nbsp;&nbsp;";
 				break;
 			case "Thursday":
 				$semana = $arr[0]."(<i>Qui</i>)&nbsp;&nbsp;";
 				break;
 			case "Friday":
 				$semana = $arr[0]."(<i>Sex</i>)&nbsp;&nbsp;";
 				break;
 			case "Saturday":
 				$semana = $arr[0]."(<i>Sab</i>)&nbsp;&nbsp;";
				$fim_de_semana=true;
 				break;
 			case "Sunday":
 				$semana = $arr[0]."(<i>Dom</i>)&nbsp;&nbsp;";
				$fim_de_semana=true;
 				break;
 			}

 			//echo "<td class=\"textoLogin\">--$semana</td>";
			#Imprime data e dia da semana: $semana
 			echo "<span style=\"font-family:verdana; font-size:12px;\"><b>$semana</b>  </span>";
 											
 			$sSql = mysql_query("SELECT DATE_FORMAT(po_data, \"%H:%i:%s\") 
 				FROM ponto 
 				 WHERE fu_id = '$varDados[0]' 
 				 AND date_format(po_data, \"%d-%m-%Y\") = '$arr[0]'");
 								 
 			$varAux = 0; $trabalhou=false; 
 			while($arrHora = mysql_fetch_array($sSql))							
 			{
 			   $cor = ($varAux % 2 == 0) ? "#0000FF" : "#FF0000" ;
 			   echo "<span style=\"font-family:verdana; font-size:12px; color:".$cor.";\">".$arrHora[0]."</span> - ";
 			   $varAux++;
			   $trabalhou=true;
			   #print " -- ";
 			}
			if(!$trabalhou and $feriado)
			{ print "$ESTILO $FERIADOS_DIAS[$dia_tratado] $ESTILO_FIM"; }
			else if(!$trabalhou and $fim_de_semana)
			{ print "$ESTILO Fim de semana $ESTILO_FIM"; }
			else if(! $trabalhou )
			{
			  #print "Falta"; 
 			  ##echo "<span style=\"font-family:verdana; font-size:12px; color:".$cor.";\">Falta</span> - ";
			  print "$ESTILO2 $cor;\"> Falta $ESTILO_FIM2";
			  $nr_faltas++; ##print "Nr de faltas: $nr_faltas";
			}
 			echo "<br>";
			$ultimo_dia=$arr[0];
			#print "ultimo dia: $ultimo_dia";
 	}	#Fim while
	print "<center>$ESTILO Nr de faltas: $nr_faltas $ESTILO_FIM <center>";
	$MES_FALTAS .= "$varDados[2]: Nr de faltas: $nr_faltas\n";
 ##}	#if txtDia...
 			
 	//echo "<br><br>";
 //echo "</td>";
}	#fim for ...
?>

</td>
</tr>
<?
/* $arrDados = $objFuncionario->getMeses($txtMes,$txtAno);
$sqlTemp = mysql_query("SELECT DISTINCT(date_format(po_data,\"%Y-%m-%d\")) as dtPonto,po_semana 
	FROM ponto 
	WHERE fu_id = $varDados[0]
	AND date_format(po_data,\"%d\") = $txtDia
	AND date_format(po_data,\"%m\") = $txtMes");
 */
//$arrNewMeses = getNewMeses($dia,$mes,$ano)						
 						
//while($arrRowsD = mysql_fetch_array($sqlTemp))
//$arrDados[] = $arrRowsD[0]."|".$arrRowsD[1];
 	
 	
 /*
 for($i=0; $i<sizeof($arrDados); $i++)
 {
 	$varData = explode("|",$arrDados[$i]);
 	$bg = ($i % 2 == 0) ? '#EEEEEE' : '#FFFFFF' ;
 */
?>

</table>
<?php
 #print $MES_FALTAS;
 $body=$MES_FALTAS;
 $body .= "\nLogin: " . $_SESSION['login'] . ":\nArq: " . __FILE__ . "\n";
 #print $body;
 #send_mailx($sender,$destinatario,$assunto,$body)
 $body_html=ereg_replace("\n","<br>",$body);
 print $body_html;
 send_mailx_html($sender,$destinatario,$assunto,$body_html);
?>

<?
//}
?>
<br>
<input type="button" onclick='javascript:self.print()' value="Imprimir">

</body>
</html>

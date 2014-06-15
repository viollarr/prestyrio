<?

//----- Includes -----//
include_once "config.php";
include_once "ips_allow.php";
include_once "funcoes_ponto.php";

//----- Declara variáveis -----//
$msg = "";
$HORA=date("H");
$MIN=date("i");
$agora=mktime($HORA,$MIN);
$WDAY=date("w");	#0 (for Sunday) through 6 (for Saturday)
$agora_date_time=date("Y-m-d G:i:s");
$arcondicionado = "<blink> Por favor, LIGUE O AR CONDICIONADO E ESVAZIE O BALDE! Obrigado!</blink>";
$horarios_batidas=array();
$ANO=date("Y") ;$MES=date("m");  $DIA=date("d");
#print "$ANO - $MES - $DIA ";

if(false)	#$WDAY > 0 and $WDAY < 6)
{
  if(
   ( $agora > $PERMITE_ENTRADA['inicio'] and 
     $agora < $PERMITE_ENTRADA['fim'] 
   )
   or
   $agora > $PERMITE_ENTRADA['ini_livre']
  ) 
  {
   /********
   print "$HORA: $MIN => agora: $agora " . $PERMITE_ENTRADA['inicio'] . " - "
	. $PERMITE_ENTRADA['fim'] . " - " . $PERMITE_ENTRADA['ini_livre']
	. "<br>";
   print "OK!";
   ********/

  }
  else 
  {
    print "Entrada fora de hora!"; 
    exit(); 
  }
}
$nmCracha  = $_POST['x_subname']; $nmCracha=trim($nmCracha);
if(strlen($nmCracha) < 5)$nmCracha=$_GET['x_subname'];
$nmCracha=trim($nmCracha);
#print "Cracha: " . $_POST['cracha'] . " -- " . $_POST['x_subname'] .  " Len: ". strlen($_POST['x_subname']);
#print "<br>nmCracha : $nmCracha<br>" . "_POST[cracha]" . $_POST['cracha'] . " -- " . $_GET['cracha'] . "<br>";
$arrDados=array();

if( (isset($_POST['cracha']) or isset($_GET['cracha']) ) and strlen($nmCracha) > 1 )
{
	##$nmCracha  = $_POST['x_subname'];
	#Pedro 19-9-2007
	#print "nmCracha: $nmCracha ...";
	if(strlen($nmCracha) < 3)exit();
	$query_ponto= "SELECT * FROM funcionario WHERE fu_num_cracha = '$nmCracha'";
	$sql       = mysql_query($query_ponto);
	$arrDados  = mysql_fetch_array($sql);
	$numCracha = mysql_num_rows($sql);
	
	if($numCracha == 0)
	{
		$msg = "<br><span style='font-family: verdana; 
		font-size: 12px; color: #FF0000;'><b>$nmCracha: Cracha Nao Existe !</b>
		</span>";
	}
	else if($numCracha > 1)
	{
		$msg = "<br><span style='font-family: verdana; 
		font-size: 12px; color: #FF0000;'><b>Crachá Duplicado !</b>
		</span>";
	}
	else
	{
		/*
		$hrHora = date("H:m:s");
		// Cadastra ponto
		$dtAual = mysql_query("SELECT * FROM data_ponto ORDER BY dapo_id DESC LIMIT 1"); // Pega data
		$arrData = mysql_fetch_array($dtAual); 
		*/
		$nmSemana = date("l");
		// Insere dados na tabela ponto
		
		$ip = $_SERVER['REMOTE_ADDR'];
		##print_r($arrDados);
                $fu_ho_id=$arrDados ['fu_ho_id'];
                ##print "fu_ho_id: $fu_ho_id"; #exit();
                $query_ho 	= "SELECT * FROM horario where ho_id='$fu_ho_id' ";
                $result_ho = mysql_query($query_ho);
                if(mysql_num_rows($result_ho) == 0)
                {
                 print "Não existe horario cadastrado. O ponto não foi aceito."; exit();
                }
                $result_ho = mysql_fetch_array($result_ho);	#Horario em $result_ho['ho_initol_depois'] - Atraso max. Verificar que seja a primeira batida: entrada.
                #print_r($result_ho); exit();
                $x = str_pad($DIA,2,"0",STR_PAD_LEFT);
                $txtAno=$ANO ;$txtMes=$MES;           
                $idFuncionario=$arrDados['fu_id'];
                $sqlx = "SELECT DISTINCT TIME(`po_data`) AS `hora` FROM `ponto` WHERE `fu_id` = $idFuncionario AND DATE_FORMAT(`po_data`,\"%Y-%m-%d\") = '$txtAno-$txtMes-$x' ORDER BY `po_data` ASC";
                $resultadox= mysql_query($sqlx);
                $nr_batidas_do_dia = mysql_num_rows($resultadox);
                #print "nr_batidas_do_dia: $nr_batidas_do_dia"; exit();
                if($nr_batidas_do_dia < 1)	#E' a primeira batiada = entrada.
                {
                 #Testa o horario
                 list($ho_initol_depois_h,$ho_initol_depois_m,$ho_initol_depois_s)=explode(":",$result_ho['ho_initol_depois']);
                 ##print "$result_ho[ho_initol_depois] -> $ho_initol_depois_h,$ho_initol_depois_m"; exit();
                 $ho_initol_depois = mktime($ho_initol_depois_h,$ho_initol_depois_m);
                 if($agora > $ho_initol_depois)
                 {
                   print "Horario fora do limite. O ponto não é aceito."; 
                   $host_ip = $_SERVER['REMOTE_ADDR'];
                   $date_order_Y="d/m/Y H:i:s";
                   $now_date=date("$date_order_Y");
                   
                   $sender="info@wb.com.br"; $destinatario="info_ponto@wb.com.br";
                   $assunto="Ponto INVALIDO - Ponto de $arrDados[fu_id]:$arrDados[fu_nome] em " . "$now_date hs.";
                   $body="$now_date: $assundo\nFuncao: Marcação de ponto.\nMaquina: $host_ip\n";
                   ####$body .="ip_remote: $ip_remote - $ip_remote_nome\n";
                   $body .="nmCracha: $nmCracha, fu_id: $arrDados[fu_id], $arrDados[fu_nome]\n";
                   $body .= "\n\n1. Arq: " . __FILE__ . " : linha nr "  . __LINE__ . "\n\n";

                   send_mailx($sender,$destinatario,$assunto,$body);
                   exit();
                 }                 
                }
                                
		#mysql now(): 2010-03-17 13:42:28
		##$queryP	= "INSERT INTO ponto(fu_id,po_data,po_semana,po_ip) 
		##	VALUES (".$arrDados['fu_id'].",now(),'$nmSemana','$ip')";
		$queryP	= "INSERT INTO ponto(fu_id,po_data,po_semana,po_ip) 
			VALUES (".$arrDados['fu_id'].",'$agora_date_time','$nmSemana','$ip')";
		##print "queryP: $queryP";
		##$sqlPonto = mysql_query($queryP);
		if(!$sqlPonto= mysql_query($queryP))
		{
			echo "Erro .. $queryP<br>" . mysql_error(); 
			exit();
		}


        	$varHora = date("H:i:s");
		$varData = date("d.m.Y");

        	$hrHora = date("H");

		if($hrHora >= 00 and $hrHora < 06)
			$saudacao = "...";
		else if($hrHora >= 06 and $hrHora <= 12)
        	{ $saudacao = "Bom dia!"; }
		else if($hrHora >= 13 and  $hrHora <= 18)
        	{ $saudacao = "Boa tarde!"; }
		else 	#Depois das 18  horas.
		{
		 $saudacao = "Boa noite!" . $arcondicionado; 
		}
		####$saudacao=$hr;
		if($WDAY == 0 or $WDAY == 6)
		{ $saudacao .= $arcondicionado; }

		##print "WDAY: $WDAY, hora: $hrHora ";
		#print_r($arrDados); 
		# "$nmCracha / $numCracha  /query_ponto: $query_ponto - query: $queryP " .
		$msg0 = "<br><span style='font-family: verdana;
		 font-size: 12px; color: #0000FF;'>";
		$msg0="<p class=now>";
	        ##$msg=$msg0 . $arrDados['fu_nome_cracha'] . " <br> Data: $varData <br> Hora: $varHora hs. " . "<br>" . "$saudacao"; #  . "</span>";	#. $query_ponto ;
	        $msg=$msg0 . $arrDados['fu_nome_cracha'] . " <br> Data: $varData <br> Hora: $varHora hs. " . "<br>" . "$saudacao"; #  . "</span>";	#. $query_ponto ;
	}
}
##else print "?????";

?>
<html>
<head>
<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function teste()
    {
	    document.getElementById("spn").innerHTML = '';
	}


	function setfocus() //Função que vai setar o focu dentro do form.
	 {
	  document.frm_info.x_subname.focus();
	  return true;
	 }
	
	function max(txarea)
	{
		tam = txarea.value.length;
		str="";
		str=str+tam;
	  
		Digitado.innerHTML = str;
	
		aux = txarea.value;
		txarea.value = aux.substring(0,total);
		Digitado.innerHTML = total;
		
	}
	
	function testavalor()
	{
		var tt ;
		tt = document.frm_info;
		if(tt.x_subname.value.length >= 9)
		 {
		  document.frm_info.submit();
		 }
	}
	

<!--
function DateTime()
{
var s = new Date();

var semana=new Array(7);
semana[0] = "Domingo";
semana[1] = "Segunda-feira";
semana[2] = "Terça-feira";
semana[3] = "Quarta-feira";
semana[4] = "Quinta-feira";
semana[5] = "Sexta-feira";
semana[6] = "Sábado";

var Ho, Mi, Se, Dia, Mes;

Ho = s.getHours();
Mi = s.getMinutes();
Se = s.getUTCSeconds();

Mes = s.getMonth(); 

for(var x = 0; x <= 9; x++)
{
    if(Mes == x)
	{
	    Mes = "0"+Mes;    
	}

    if(Ho == x)
	{
	    Ho = "0"+Ho;    
	}

	if(Mi == x)
	{
	    Mi = "0"+Mi;    
	}

	if(Se == x)
	{
	    Se = "0"+Se;    
	}
}

var varHoras = Ho+":"+Mi+":"+Se;
var varData  = s.getDate()+"."+Mes+"."+s.getFullYear();

showDateTime = s.toLocaleString()

//mostra a data e hora na barra de status
window.status=showDateTime


//mostra a data e hora no título
document.title = "Data: " + varData + "         Semana: " + 
	semana[s.getDay()]+"         Hora:" + varHoras ;

//mostra a data e hora no documento
//document.getElementById("show").innerHTML="<font face='arial' size='2'>"+showDateTime+"</font>"
document.getElementById("show").innerHTML="<font face='verdana' size='3'> <b>Data:</b> "
 +varData+" &nbsp;&nbsp;&nbsp;  <b>Dia:</b> "
 +semana[s.getDay()]
 +" &nbsp;&nbsp;&nbsp; <b>Hora:</b> "
 + varHoras + " </font>";


setTimeout("DateTime();", 1000);

setTimeout("teste();", 5000);


}
//-->

</script>
</head>
<body leftmargin="0" topmargin="0" autocomplete="off"
  onload="document.frm_info.x_subname2.focus();" >
<!--     onload="document.frm_info.x_subname2.focus(); DateTime();" -->
<?php
$detona_se_ip_nao_autorizado=true;
$func_watch=true;
login_logout($detona_se_ip_nao_autorizado,$func_watch);		#Detona se ip nao autorizado, watch

?>

<table border="0" width="100%" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td height="120px" background="img/topo.jpg">&nbsp;</td>
	</tr>

	<tr>
		<td align="center">
			<br />
			    <div id="show" align="center"></div>
			<br />
		</td>
	</tr>	
	<tr>
		<td align="center">
		<form action="index.php" method="post" name="frm_info">
		<input type="hidden" name="cracha" value="1">Crach&aacute;:
		<input type="password" name="x_subname" size="20" maxlength="20"
		 OnKeyUp="max(this)"   OnKeyPress="testavalor()" id="x_subname2" 
		 style="border: 1px solid #FFFFFF;background-color: #CCCCCC;">
		</form>
		<?php print "Data: $agora_date_time"; ?>
		<br>O ponto <b>TEM</b> que ser batido na entrada e no final do trabalho.
		<br>Caso n&atilde;o seja batido o ponto na entrada ou na saida ser&aacute; considerado <b>FALTA</b>.
		</td>
	</tr>
</table>

<center>
<span id="spn"><?php
##print $msg;	#Imprimindo mais adiante
?></span>
</center>
<?php 
#De funcionario/imprimir_relatorio3.php
#Verificar qual o horario: entrada/saida intervalo almoco ou jantar  /volta almoco-jantar / saida trabalho

if( (isset($_POST['cracha']) or isset($_GET['cracha']) ) and strlen($nmCracha) > 1 )
{
##print_r($arrDados);
#Array ( [0] => 95 [fu_id] => 95 [1] => 1 [fu_active] => 1 [2] => HorÃ¡rio PadrÃ£o (9:00 Ã s 18:00) [fu_horario] => HorÃ¡rio PadrÃ£o (9:00 Ã s 18:00) 
#[3] => PEDRO ALVES FILHO [fu_nome] => PEDRO ALVES FILHO [4] => ADMIN [fu_funcao] => ADMIN [5] => 44071038 [fu_matricula] => 44071038 
#[6] => 1990709-6 ifp [fu_rg] => 1990709-6 ifp [7] => 68868910730 [fu_ctps] => 68868910730 [8] => 2010-03-17 [fu_admissao] => 2010-03-17 
#[9] => 0000-00-00 [fu_exclusao] => 0000-00-00 [10] => ADMIN [fu_setor] => ADMIN [11] => 44071038 [fu_num_cracha] => 44071038 
#[12] => PEDRO ALVES FILHO [fu_nome_cracha] => PEDRO ALVES FILHO [13] => pedro1_1.jpg [fu_foto_cracha] => pedro1_1.jpg [14] => 4 [cr_id] => 4 ) 
$idFuncionario=$arrDados['fu_id'];
$txtAno=$ANO ;$txtMes=$MES;  $x=$DIA;

/* */
$x = str_pad($x,2,"0",STR_PAD_LEFT);
$sql = "SELECT DISTINCT TIME(`po_data`) AS `hora` FROM `ponto` WHERE `fu_id` = $idFuncionario AND DATE_FORMAT(`po_data`,\"%Y-%m-%d\") = '$txtAno-$txtMes-$x' ORDER BY `po_data` ASC";
$resultado= mysql_query($sql);
$nr_batidas_do_dia = mysql_num_rows($resultado);
#print "query: $sql<br>nr_batidas_do_dia: $nr_batidas_do_dia<br>";
/* */
while($result1 = mysql_fetch_array($resultado))
{
#print_r($result1); print "<br>";
#print $result1['hora']; print "<br>";
$horarios_batidas[]=$result1['hora']; 
}
#print_r($horarios_batidas);
print $msg; print "<br><br><u>Hist&oacute;rico do Dia</u><br>";
##print "<p class=now>Data: $DIA/$MES/$ANO <br>";
print "Entrada: $horarios_batidas[0]<br>";
print "Saida intervalo: $horarios_batidas[1]<br>";
print "Volta intervalo: $horarios_batidas[2]<br>";
if(!isset($horarios_batidas[3]) or strlen($horarios_batidas[3]) < 1)
print "Saida: <blink>??????</blink>";
else print "Saida: $horarios_batidas[3]<br>";
if($nr_batidas_do_dia >4)
{
print "<br><blink>Aten&ccedil;&atilde;o: Foram batidas <b>$nr_batidas_do_dia</b> vezes este ponto hoje!</blink><br>";
}
print "</p>";

}
print <<<EOF
<meta HTTP-EQUIV="REFRESH" CONTENT="10;URL=/">
EOF;

##print "Redirecionando para: $_SERVER[PHP_SELF] ";

?>

</body>
</html>

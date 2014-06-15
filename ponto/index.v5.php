<?

//----- Includes -----//
include_once "config.php";
include_once "ips_allow.php";

//----- Declara variáveis -----//
$msg = "";
##print "cracha:  " . $_POST['cracha']  . " " . $_POST['x_subname'];
if(isset($_POST['cracha']))
{
	$nmCracha  = $_POST['x_subname'];
	#Pedro 19-9-2007
	$nmCracha=trim($nmCracha);

	if(strlen($nmCracha) < 3)exit();
	$query_ponto= "SELECT * FROM funcionario WHERE fu_num_cracha = '$nmCracha'";
	$sql       = mysql_query($query_ponto);
	$arrDados  = mysql_fetch_array($sql);
	$numCracha = mysql_num_rows($sql);
	
	if($numCracha == 0)
	{
		$msg = "<br><span style='font-family: verdana; 
		font-size: 12px; color: #FF0000;'><b>$nmCracha: Crachá Não Existe !</b>
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
		
		$queryP	= "INSERT INTO ponto(fu_id,po_data,po_semana,po_ip) 
			VALUES (".$arrDados['fu_id'].",now(),'$nmSemana','$ip')";
		$sqlPonto = mysql_query($queryP);
		if(!$sqlPonto)
		{
			echo "erro..$queryP<br>".mysql_error();
		}


        	$varHora = date("H:i:s");
		$varData = date("d.m.Y");

        	$hrHora = date("H");

		if($hrHora >= 00 || $hrHora < 06)
			$hr = "Ralando em pião... vai para casa dormir...";
		else if($hrHora >= 06 || $hrHora <= 12)
        		$hr = "Bom dia";
		else if($hrHora > 13 || $hrHora <= 18)
        	    $hr = "Boa tarde";
		else
		    $hr = "Boa noite";

		#print_r($arrDados); 
		# "$nmCracha / $numCracha  /query_ponto: $query_ponto - query: $queryP " .
		$msg = "<br><span style='font-family: verdana;
		 font-size: 12px; color: #0000FF;'> 1. Funcionário: " . 
		 $arrDados['fu_nome_cracha'] .
		 " <br> Data: $varData <br> Hora: $varHora  </span>"
		  ;	#. $query_ponto ;
	}
}

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
document.getElementById("show").innerHTML="<font face='verdana' size='3'> <b>Data:</b> "+varData+" &nbsp;&nbsp;&nbsp;  <b>Semana:</b> "+semana[s.getDay()]+" &nbsp;&nbsp;&nbsp; <b>Hora:</b> "+varHoras+" </font>";


setTimeout("DateTime();", 1000);

setTimeout("teste();", 5000);


}
//-->

</script>
</head>
<body leftmargin="0" topmargin="0" autocomplete="off"  onload="document.frm_info.x_subname2.focus(); DateTime();">
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
		<input type="hidden" name="cracha" value="1">
		<input type="password" name="x_subname" size="20" maxlength="20"
		 OnKeyUp="max(this)"   OnKeyPress="testavalor()" id="x_subname2" 
		 style="border: 1px solid #FFFFFF;">
		</form>
		</td>
	</tr>
</table>

<center>
<span id="spn"><?php
print $msg;
?></span>
</center>

</body>
</html>

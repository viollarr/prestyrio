<?php

/*********************************************************************************
							Enviar foto para área multimédia
							Paulo Oliveira:02/10/2007
************************************************************************************/

include_once ("../php/funcoes.php");

$conexao = mysql_pconnect("$HOST","$MYSQL_USER","$MYSQL_PASS");
$db = mysql_select_db($DATABASE,$conexao) or die ("Erro #123");
$varUpload_normal=false;
$varUpload_thumb=false;
#$agenda=utf8_encode($agenda);
$agenda=addslashes($agenda);

$inserir  = "INSERT INTO texto_agenda (_texto) VALUES
	('$agenda')";
$sql = mysql_query ($inserir,$conexao);	# or die ("Erro #1234");


if(mysql_error($conexao))
{
 print "\nMysql error:" . mysql_errno($conexao)
 . " : "  . mysql_error($conexao) . "<br>";
 print("<CENTER>Saindo.</CENTER>");
 exit();
}
else 
{
  print "Insercao de<br>
---------------------<br>
 $agenda
<br>
------------------<br>
 no banco de dados realizada.";
}

?>

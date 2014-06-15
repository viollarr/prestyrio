<?php


/*********************************************************************************
							Inserir_camarim.php
************************************************************************************/


include_once ("../php/funcoes.php");

#$conexao = mysql_pconnect("$HOST","$MYSQL_USER","$MYSQL_PASS");
#$cnx_id = mysql_select_db($DATABASE,$conexao) or die ("Erro #123");
$varUpload_normal=false;
$varUpload_thumb=false;
$area_normal="../areas/mm_foto";
$area_thumb="../areas/mm_thumb";
$width_thumb="55";

#mysql_connect('wbhost.wb.com.br','wbhost_alcione','z3x2c1');
#mysql_select_db('wbhost_alcione');

$noticia = $_POST ["noticia"];

$noticia=trim($noticia);
if(strlen($noticia) > 5)
{
 $noticia = date("d/m/Y H:m") . ":" . $noticia;
 $insere= "INSERT INTO texto_camarim (_texto) VALUE ('$noticia')";
 mysql_query ($insere, $cnx_id) or die ("Erro ao inserir dados.");
 echo "Noticia -$noticia- inserida com sucesso!";
}
else
{
 echo "Noticia -$noticia- vazia!";
}	


?>

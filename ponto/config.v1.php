<?
$dbHost	= "localhost";
$dbUser = "wbhost_wbhost";
$dbPass	= "2q3w4e";
$dbName	= "wbhost_ponto";
$TABLE_ID_usuarios="adm_id";
$TABLE_usuarios="adm";
$TABLE_usuarios_login="adm_login";
$TABLE_usuarios_senha="adm_senha";

$dbConexao = mysql_connect($dbHost,$dbUser,$dbPass);
if(!$dbConexao)
{
	echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"><b>Erro</b> na conexão com o banco...</span><br>".mysql_error();
	exit;
}

$dbSelect = mysql_select_db($dbName);
if(!$dbSelect)
{
	echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"><b>Erro</b> na seleção do banco...</span><br>".mysql_error();
	exit;
}

$foto_width="100";
$foto_height="120";
$foto_dim=$foto_width . "x" . $foto_height;
############### Pedro em 2-1-2008:
$date_order="d/m/y";
$date_order_Y="d/m/Y";
$now_date=date("$date_order");
$now_date_Y=date("$date_order_Y");
$words_date=explode("/",$now_date);
$words_date_Y=explode("/",$now_date_Y);
$DIA=$words_date[0];
$MES=$words_date[1];
$ANO=$words_date_Y[2];
$ANO_2=$ANO-2;
$ANO_1=$ANO-1;
$ANOS_CRT=array($ANO-2,$ANO-1,$ANO,$ANO+1,$ANO+2,"");
$PERMITE_ENTRADA="";

?>

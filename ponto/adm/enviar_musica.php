<?php

/*********************************************************************************
							Enviar foto para área multimédia
							Paulo Oliveira:02/10/2007
************************************************************************************/

//Conexão do banco de dados
#include ("http://www.wbhost.com.br/projetos/alcione/php/funcoes.php");
include_once ("../php/funcoes.php");
#print "Mysql: $HOST $MYSQL_USER $MYSQL_PASS";
#print_r($_FILES);
#Array ( [foto] => Array ( [name] => xavier2.jpg [type] => image/jpeg
#[tmp_name] => /tmp/phpDtJlci [error] => 0 [size] => 36217 ) ) Files:
#xavier2.jpg, /tmp/phpDtJlci

$conexao = mysql_pconnect("$HOST","$MYSQL_USER","$MYSQL_PASS");
$db = mysql_select_db($DATABASE,$conexao) or die ("Erro #123");
$varUpload_normal=false;
$varUpload_thumb=false;
$area_normal="../areas/mm_foto";
$area_thumb="../areas/mm_thumb";
$area_musica="../areas/mm_musica";


#$foto = $_POST ['foto'];
#$tipo = $_POST ['tipo'];

#print_r($_FILES); exit();

$nmFoto         = $_FILES['foto']['name'];
$tmpFoto        = $_FILES['foto']['tmp_name'];
$nmthumb         = $_FILES['thumb']['name'];
$tmpthumb        = $_FILES['thumb']['tmp_name'];
$nmmusica        = $_FILES['musica']['name'];
$tmpmusica       = $_FILES['musica']['tmp_name'];

#$nmFoto         = $_POST['foto']['name'];
#print "tmpFoto: $tmpFoto<br>"; exit();
#$tmpFoto        = $_POST['foto']['tmp_name'];
#$foto=$nmFoto;  # $tipo=$_FILES['foto']['tipo'];

#$varUpload_normal = move_uploaded_file($tmpFoto,"$area_normal/$nmFoto");
#$varUpload_thumb = move_uploaded_file($tmpthumb,"$area_thumb/$nmthumb");

#$nmmusica2=ereg_replace("[^0-9a-zA-Z]",'',$nome_musica);
$nmmusica2=ereg_replace("[^0-9a-zA-Z\.]",'',$nmmusica);
$varUpload_normal = move_uploaded_file($tmpmusica,"$area_musica/$nmmusica2");

if($varUpload_normal)
{
 print "Musica de $tmpmusica para $area_musica/$nmmusica2 ($nome_musica) : OK!<br>";
}
else
{
 print "Musica de $tmpmusica para $area_musica/$nmmusica2 ($nome_musica) : NOT OK!<br>";
 exit();
}


#| id | _texto  | arquivo
$inserir  = "INSERT INTO texto_musicas ( _texto,arquivo) VALUES
	('$nome_musica','$nmmusica2')";
$sql = mysql_query ($inserir,$conexao);	# or die ("Erro #1234");
$last_id=mysql_insert_id($conexao);

if(mysql_error($conexao))
{
 print "\nMysql error:" . mysql_errno($conexao)
 . " : "  . mysql_error($conexao) . "<br>";
 print("<CENTER>Saindo.</CENTER>");
 exit();
}
else
{
  print "Inser&ccedil;&atilde;o de $nome_musica/$nmmusica no banco de dados realizada 
	id: $last_id.";
}
?>

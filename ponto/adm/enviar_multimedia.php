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
$width_thumb="55";

#$foto = $_POST ['foto'];
#$tipo = $_POST ['tipo'];
#print_r($_FILES); exit();
$nmFoto         = $_FILES['foto']['name'];
$tmpFoto        = $_FILES['foto']['tmp_name'];
$nmthumb         = $_FILES['thumb']['name'];
$tmpthumb        = $_FILES['thumb']['tmp_name'];

#$nmFoto         = $_POST['foto']['name'];
#print "tmpFoto: $tmpFoto<br>"; exit();
#$tmpFoto        = $_POST['foto']['tmp_name'];
$foto=$nmFoto;  # $tipo=$_FILES['foto']['tipo'];

#print "Files: $nmFoto, $tmpFoto, $foto, $tipo "; exit();
/*
if($imagem == "normal")
{
  $area="../areas/mm_foto";
  $varUpload = move_uploaded_file($tmpFoto,"../areas/mm_foto/$nmFoto"); 
}
else
{
  $area="../areas/mm_thumb";
  $varUpload = move_uploaded_file($tmpFoto,"../areas/mm_thumb/$nmFoto"); 
}
*/


$varUpload_normal = move_uploaded_file($tmpFoto,"$area_normal/$nmFoto");

$varUpload_thumb = move_uploaded_file($tmpthumb,"$area_thumb/$nmthumb");

if($varUpload_normal)
{
 print "Foto de $tmpFoto para $area_normal/$nmFoto : OK!<br>";
}
else
{
 print "Foto de $tmpFoto para $area_normal/$nmFoto : NOT OK!<br>";
}
if($varUpload_thumb)
{
 print "Foto de $tmpthumb para $area_thumb/$nmthumb : OK!<br>";
}
else
{
 print "Foto de $tmpthumb para $area_thumb/$nmthumb : NOT OK!<br>";
}
#
#| _id | _tipo    | _nome        | _thumb   | _foto  |
#+-----+----------+--------------+----------+--------+
#|   1 | pessoais | Foto Legal   | 1_p.jpg  | 1.jpg  |
#|   2 | pessoais | Foto Maneira | 2_p.jpg  | 2.jpg  |
#
##if($imagem != "normal")exit();

if($varUpload_normal)
{
 $inserir  = "INSERT INTO fotos_multimidia (_foto, _tipo,_nome,_thumb) VALUES
	('$foto','$tipo','$foto_nome','$nmthumb')";
 $sql = mysql_query ($inserir,$conexao);	# or die ("Erro #1234");
}

if($varUpload_thumb)
{
 $tamanho=`imgsize -r $area_thumb/$nmthumb`;
 $tamanho=rtrim($tamanho); $tam=explode(" ",$tamanho); 
 #print " $tamanho ==" . $tam[0] . " x " . $tam[1]. "<br>";
 #print "Tamanho de $area_thumb/$nmthumb: $tamanho  -- imagem: $tam[0] x $tam[1]<br>";
 if($tam[0] > 55)
 {
  system("convert $area_thumb/$nmthumb  -geometry $width_thumb $area_thumb/$nmthumb");
 }
}
else
if($varUpload_normal)
{
  system("convert $area_normal/$nmFoto  -geometry $width_thumb $area_thumb/$nmthumb");
}


if(mysql_error($conexao))
{
 print "\nMysql error:" . mysql_errno($conexao)
 . " : "  . mysql_error($conexao) . "<br>";
 print("<CENTER>Saindo.</CENTER>");
 exit();
}
else if($varUpload_normal)
{
  print "Inserção de $foto_nome no banco de dados realizada.";
}

?>

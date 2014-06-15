<?

//----------------------------//
//----- Conecta ao banco -----//
//----------------------------//

$cn = mysql_connect("localhost", "wbhost_wbhost", "123456");
if(!$cn)
{
    echo "ERRO AO CONECTAR BANCO !<br />".mysql_error();
	exit();
}

$bd = mysql_select_db("wbhost_ponto");
if(!$bd)
{
    echo "ERRO AO SELECIONAR BANCO !<br />".mysql_error();
	exit();
}
//----------------------------//
//----- Conecta ao banco -----//
//----------------------------//


// Pega dados do banco
$query = "
          SELECT *
          FROM cracha
          ORDER BY cr_tipo
         ";
$result = mysql_query($query);

echo "<center>";
// Grava no arquivo
while($arrRows = mysql_fetch_array($result)) 
{	

	echo "<img src=\"cracha/img_cracha/" . $arrRows['cr_frente'] . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<img src=\"cracha/img_cracha/" . $arrRows['cr_verso'] . "\">";
	echo "<hr width=\"100%\">";
    
}
echo "</center>";

?>
<?
//----------------------------//
//----- Conecta ao banco -----//
//----------------------------//

$cn = mysql_connect("localhost", "prestyri_ponto", "gabrielph");
if(!$cn)
{
    echo "ERRO AO CONECTAR BANCO !<br />".mysql_error();
	exit();
}

$bd = mysql_select_db("prestyri_prestyri");
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
          FROM funcionario
          ORDER BY fu_nome
         ";
$result = mysql_query($query);


// Nome do arquivo a ser gravado
$nmArquivo = "arquivo.txt";

// Zera o arq, deixa ele limpo para escrever
$abrir = fopen($nmArquivo, "w");
$escrever = fwrite($abrir, "");
fclose($abrir);


// Abra arquivo
$abrirArq = fopen ($nmArquivo, "a");


if(!$abrirArq)
{
    echo "Erro abrindo arquivo (" . $arquivo . ")";
    exit;
}

fwrite($abrirArq, "\"fu_tipo\"; \"fu_nome_cracha\"; \"fu_funcao\"; \"fu_nome\"; \"fu_rg\"; \"fu_ctps\"; \"fu_admissao\"; \"fu_matricula\"; \"fu_num_cracha\" \r\n\r\n");

// Grava no arquivo
while($arrRows = mysql_fetch_array($result)) 
{
	//$arrRows['fu_id']."|".$arrRows['fu_funcao']."|".$arrRows['fu_matricula']."|".$arrRows['fu_rg']."|".$arrRows['fu_ctps']."|".$arrRows['fu_admissao']."|".$arrRows['fu_setor']."|".$arrRows['fu_num_cracha']."|".$arrRows['fu_nome_cracha']."|".$arrRows['fu_foto_cracha']."|".$arrRows['cr_id'];
    if ($arrRows['cr_id'] == 3)
	    $tipo = "CIEE";
	if ($arrRows['cr_id'] == 4)
	    $tipo = "WB";
	if ($arrRows['cr_id'] == 5)
	    $tipo = "Quality";

    $arrD = explode("-", $arrRows['fu_admissao']);
    $arrD = $arrD[2]."-".$arrD[1]."-".$arrD[0]; 

    $arrDados = "\"" . $tipo . "\"; \"" . $arrRows['fu_nome_cracha'] . "\"; \"" . $arrRows['fu_funcao'] . "\"; \"" . $arrRows['fu_nome'] . "\"; \"" . $arrRows['fu_rg'] . "\"; \"" . $arrRows['fu_ctps'] . "\"; \"" . $arrD . "\"; \"" . $arrRows['fu_matricula'] . "\"; \"" . $arrRows['fu_num_cracha'] . "\" \r\n\r\n"; 
    $gravaArq = fwrite($abrirArq, $arrDados);
}


// Valida gravação de arquivo
if(!$gravaArq)
{
    echo "Erro escrevendo no arquivo ($filename)";
    exit;
}
else
{
	fclose($abrirArq);
}

/*
// Copia e renomeia arquivo(foto)
if($gravaArq)
{
   $arqOrigem  = "funcionario/fotos/";
   $arqDestino = "dados/";

   $query = "
             SELECT fu_matricula, fu_foto_cracha
             FROM funcionario
             ORDER BY fu_nome
            ";
   $result = mysql_query($query); 
   
   while($arrRows = mysql_fetch_array($result))
   {
       $nmFoto      = $arrRows['fu_foto_cracha'];
       $nrMatricula = $arrRows['fu_matricula'];
       if($nmFoto != "")
	   {
       if(file_exists($arqOrigem.$nmFoto))
	   {
	       $getCopy = copy($arqOrigem.$nmFoto, $arqDestino.$nmFoto);
           if($getCopy)
	       {
	           $getRename = rename($arqDestino.$nmFoto, $arqDestino.$nrMatricula.".jpg");
	       }
		   else
			   echo "->".$arqOrigem.$nmFoto."<-";
	   }
	   }
   }

}
*/
echo "<a href=\"arquivo.txt\"> Para baixar os dados dos funcionário, CLICK AQUI </a> <br /><br />";
/*
$aux = 1;
foreach (glob("dados/*.jpg") as $filename => $valor) {
	$br = ($aux % 7 == 0) ? "\n<br>" : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
    echo "<img src=\"$valor\" width=\"100\" height=\"100\" /> $br";
$aux++;
}
*/
//http://www.touring.com.br/ponto/dados.php
?>

<html>
<head>
    <title> DAdos </title>

<script type="text/javascript">
    function tt()
	{
	     document.getElementById("teste")style.display = '';
	}
</script>

</head>
<body onload="javascript:tt():">

<div id="teste" style="display:none;">
carregando...
</div>

</body>
</html>
<?
// Includes
##include_once "config.php";
include_once "funcoes_ponto.php";
include_once "classes/classeAdm.php";
include_once "classes/classeEmp.php";


// Cria objeto
$objAdm = new Adm;
$objEmp = new Emp;


if(isset($_POST['cadAdm']))
{
	$txtLogin	= $_POST['txtLogin'];
	$txtSenha 	= $_POST['txtSenha'];
	$txtNome 	= $_POST['txtNome'];
	$txtSetor 	= $_POST['txtSetor'];
	$txtFuncao 	= $_POST['txtFuncao'];

	$objAdm->setDados($txtLogin,$txtSenha,$txtNome,$txtSetor,$txtFuncao);
	$varTemp = $objAdm->cadAdm();
	if($varTemp)
	{
		echo "<h2>Administrador cadastrato com sucesso...</h2><a href=\"valida_instalacao.php\">Continuar >></a>";
		exit;
	}
	else
	{
		echo "Erro ao cadastrar administrador...".mysql_error();
		exit;
	}
}


// Empresa -----------------//
if(isset($_POST['cadEmp']))
{
	$txtNome	= $_POST['txtNome'];
	$txtEnd 	= $_POST['txtEnd'];
	$txtCgc 	= $_POST['txtCgc'];

	$objEmp->setDados($txtNome,$txtEnd,$txtCgc);
	$varTemp = $objEmp->cadEmp();
	if($varTemp)
	{
		echo "<h2>Empresa cadastrata com sucesso !</h2><a href=\"index.php\">[ Iniciar ponto ]</a>";
		exit;
	}
	else
	{
		echo "Erro ao cadastrar empresa...".mysql_error();
		exit;
	}
}





// Inicio da pagina --------------//
global $dbConexao;
conecta_bd();
$query	= "SELECT * FROM $TABLE_adm";
$result = mysql_query($query, $dbConexao);
if( mysql_error($dbConexao) )
{
   #print("*** 1. $query_log ***<br>");
   #print "\nMysql error:" . mysql_errno($dbConexao)
   #. " : "  . mysql_error($dbConexao) . "<br>";
   ##exit;
   if(mysql_errno($dbConexao) == "1146")
   { 
    print "A tabela n&atilde;o existe. Criando TODAS as tabela";
    include_once "db/config_tables.php";
    ##aprint_r($TABELAS);
    foreach($TABELAS as $TABELA_NOME)
    {
     ##print "Tabela: ${$TABELA_NOME}<br>"; #exit();
     if(strlen(trim(${$TABELA_NOME}) ) < 2)continue;
     $result2 = mysql_query(${$TABELA_NOME},$dbConexao);
     if(mysql_error($dbConexao))
     {
      print "\n<br>Mysql error em ${$TABELA_NOME}:" . mysql_errno($dbConexao) . " : "  . mysql_error($dbConexao) . "<br>"; exit();
     }
    }
    #exit();
    $TABELAS_CRIADAS=true;
   }
}
$result = mysql_query($query, $dbConexao);
if(mysql_num_rows($result) == 0)
{
?>
	<html>
	<head>
		<title>..:: Ponto ::..</title>
		
	</head>
	<body topmargin="0" leftmargin="0">
	<table border="0" width="476" align="left">
		<form name="frmAdm" action="valida_instalacao.php" method="post">
		<input type="hidden" name="cadAdm" value="1">
		<tr>
			<td colspan="2"><h2>Instala��o...</h2></td>
		</tr>
		<tr>
			<td colspan="2">* Antes de iniciar o ponto, voc� deve cadastrar um administrador.</td>
		</tr>
		<tr>
			<td width="57">Login: </td>
			<td width="403"><input type="text" name="txtLogin" size="25"></td>
		</tr>
		<tr>
			<td>Senha: </td>
			<td><input type="password" name="txtSenha" size="25"></td>
		</tr>
		<tr>
			<td>Nome: </td>
			<td><input type="text" name="txtNome" size="25"></td>
		</tr>
		<tr>
			<td>Setor: </td>
			<td><input type="text" name="txtSetor" size="25"></td>
		</tr>
		<tr>
			<td>Fun��o: </td>
			<td><input type="text" name="txtFuncao" size="25"></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="Cadastrar"></td>
		</tr>
		</form>
	</table>
		
	</body>
	</html>	
<?
	exit;
}



// Cadastra empresa ----------------------------------//

$query2	= "SELECT * FROM empresa";
$result2 = mysql_query($query2);
if(mysql_num_rows($result2) == 0)
{
?>
	<html>
	<head>
		<title>..:: Ponto ::..</title>
		
	</head>
	<body topmargin="0" leftmargin="0">
	<table border="0" width="476" align="left">
		<form name="frmEmp" action="valida_instalacao.php" method="post">
		<input type="hidden" name="cadEmp" value="1">
		<tr>
			<td colspan="2"><h2>Instala��o...</h2></td>
		</tr>
		<tr>
			<td colspan="2">* Antes de iniciar o ponto, voc� deve cadastrar a empresa.</td>
		</tr>
		<tr>
			<td width="57">Nome: </td>
			<td width="403"><input type="text" name="txtNome" size="25"></td>
		</tr>
		<tr>
			<td>Endere�o: </td>
			<td><input type="text" name="txtEnd" size="25"></td>
		</tr>
		<tr>
			<td>CGC: </td>
			<td><input type="text" name="txtCgc" size="25"></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="Cadastrar"></td>
		</tr>
		</form>
	</table>
		
	</body>
	</html>	
<?
	exit;
}
                      

?>
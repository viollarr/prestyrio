<?
// Includes
##include_once "config.php";
include_once "funcoes_ponto.php";
conecta_bd();

include_once "valida_instalacao.php";
include_once "classes/classeLogin.php";
include_once "ips_allow.php";

#conecta_bd();

// Cria objeto
$objLogin = new Login;

// Define variáveis
$erro = null;

// Pega compo hidden e verifica login
if(isset($_POST['hidLogar']))
{
	$varLogin = $_POST['txtLogin'];
	$varSenha = $_POST['txtSenha'];

	$varDados  = $objLogin->excLogin($varLogin,$varSenha);
	if($varDados)
	{
		/*
		// Pega data atual para gerenciar ponto
		$dtData = date("Y-m-d");
		$seData	= date("l");

		$dtAual = mysql_query("SELECT * FROM data_ponto ORDER BY dapo_id DESC LIMIT 1");
		$arrDado = mysql_fetch_array($dtAual); 
		if($dtData != $arrDado['dapo_data']){
			$sql = mysql_query("INSERT INTO data_ponto(dapo_data,dapo_semana) VALUES('$dtData','$seData')");
		}
		*/
		##$arrTemp = explode("|",$varDados);
		##print "varDados: $varDados"; exit();
		#print_r($varDados); #exit();
		$arrTemp = $varDados;
		if($arrTemp[0] == 3){
			session_start();
			$_SESSION['ponto']['registered']=time()+$ESQ_SESSION_TIMEOUT;
			$_SESSION['funcao']	= $arrTemp[3];
			$_SESSION['login'] 	= $arrTemp[1];
			$_SESSION['id']  	= $arrTemp[0];
			header("Location: gera_relatorio2.php");
			exit;
		}
				
		session_start();
		$_SESSION['ponto']['registered']=time()+$ESQ_SESSION_TIMEOUT;
		$_SESSION['id']		= $arrTemp['adm_id'];
		$_SESSION['adm_id']	= $arrTemp['adm_id'];
		$_SESSION['login'] 	= $arrTemp['adm_login'];
		$_SESSION['adm_login'] 	= $arrTemp['adm_login'];
		$_SESSION['funcao']	= $arrTemp['adm_funcao'];
		$_SESSION['adm_funcao']	= $arrTemp['adm_funcao'];
		$_SESSION['adm_grupo']  = $arrTemp['adm_grupo'];
		$_SESSION['adm_nome']   = $arrTemp['adm_nome'];
		#print_r($_SESSION); exit();
		#print $_SESSION['ponto']['registered'];
				
		header("Location: admindex.php");
	}else{
		$erro = "Erro ao Logar !";
	}
}

?>
<html>
<head>
		<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">

</head>
<?php

$detona_se_ip_nao_autorizado=true;
$func_watch=true;
login_logout($detona_se_ip_nao_autorizado,$func_watch); 
        #Detona se ip nao autorizado

?>
<body leftmargin="0" topmargin="0">

<table border="0" align="left" cellpadding="0" cellspacing="0">
	<tr>
		<td><img src="img/topo.jpg"></td>
	</tr>
	<form name="frmLogin" action="admin.php" method="post">
	<input type="hidden" name="hidLogar" value="1">
	<tr bgcolor="#EEEEEE">
		<td class="textoLogin">&nbsp;&nbsp;
		Login : <input type="text" name="txtLogin" size="15" class="formLogin">&nbsp;&nbsp;
		Senha : <input type="password" name="txtSenha" size="15" class="formLogin">&nbsp;&nbsp;
		<input type="submit" value="Entrar" class="textoLogin">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span style="color:#FF0000;"><b> <?=$erro?> </b>
		</td>
	</tr>
	</form>
</table>

<?php
#print "Ponto " . $_SESSION['ponto']['registered'];				
?>

</body>
</html>

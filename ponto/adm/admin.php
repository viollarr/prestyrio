<?
// Includes
include_once "config.php";
include_once "valida_instalacao.php";
include_once "classes/classeLogin.php";
include_once "ips_allow.php";

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
		$arrTemp = explode("|",$varDados);
		
		if($arrTemp[0] == 3){
			session_start();
			$_SESSION['funcao']	= $arrTemp[3];
			$_SESSION['login'] 	= $arrTemp[1];
			$_SESSION['id'] = $arrTemp[0];
			header("Location: gera_relatorio2.php");
			exit;
		}
				
		session_start();
		$_SESSION['id']		= $arrTemp[0];
		$_SESSION['login'] 	= $arrTemp[1];
		$_SESSION['funcao']	= $arrTemp[3];
						
				
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

</body>
</html>
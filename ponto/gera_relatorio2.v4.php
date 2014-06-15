<?
session_start();
if(!isset($_SESSION['login']) || !isset($_SESSION['id'])){
header("Location: http://".$_SERVER['HTTP_HOST']."/ponto/admin.php");
}

// Includes
//include_once "valida_login.php";
include_once "config.php";
include_once "classes/classefuncionario.php";
##include_once "adm/funcoes.php";
include_once "funcoes_ponto.php";

// Cria objeto
$objFuncionario = new Funcionario;

// Declara variáveis
$erro = null;
$mostrar = null;
$falta = null;

/*
// Pega id do Funcionario da pagina 'funcionarioIndex.php' e preenche form
$idFuncionario = $_GET['idFuncionario'];
if(!is_numeric($idFuncionario)){
	header("Location: funcionarioIndex.php");
}
*/

?>
<html>
<head>
		<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">

</head>
<body leftmargin="0" topmargin="0">

<table border="0" width="770px" align="left" cellpadding="0" cellspacing="0">
<!-- inclusão do topo -->
<? include_once "topo.php"; ?>
	<!--Inicio - Tabela  Menu  do Administrativo -->
	<tr bgcolor="#EEEEEE">
		<td class="textoLogin">
			<table border="0" width="770px" align="center" cellpadding="0" cellspacing="0">
				<tr align="center">
					<td width="104" class="linkMenu"><img src="img/funcionario.gif" border="0"><br>Funcionário</td>
					<td width="666" align="left" valign="bottom">
				  		<a href="admindex.php" class="linkSair"><b><< Voltar</b></a>
					</td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td colspan="2" align="center" class="linkSair"><a class="linkSair" href="imprimir_relatorio_dia.php">[ Relatório diario ] </a></td>
				</tr>
				<tr>
					<td colspan="2">
					  <table  border="0" width="770px" align="center" cellpadding="2" cellspacing="2">
                        <form name="frmAjustar" action="imprimir_relatorio2.php" method="post">
                          <input type="hidden" name="ajustarPonto" value="1">
                          <!--
                          <input type="hidden" name="idFuncionario" value="< ?=//$idFuncionario; ?>">
                          -->
                          <tr class="admDados">
                            <td height="25" colspan="2" align="left"> :: Gera relatório</td>
                            <td width="368" height="25" colspan="2" align="center" style="font-family: verdana; font-size:10px; color:#FFFFFF;">
								&nbsp;&nbsp;Dia: 
								<select name="txtDia" class="textoLogin">
									<option value="00"> - </option>
									<option value="01"> 1 </option>
									<option value="02"> 2 </option>
									<option value="03"> 3 </option>
									<option value="04"> 4 </option>
									<option value="05"> 5 </option>
									<option value="06"> 6 </option>
									<option value="07"> 7 </option>
									<option value="08"> 8 </option>
									<option value="09"> 9 </option>
									<option value="10"> 10 </option>
									<option value="11"> 11 </option>
									<option value="12"> 12 </option>
									<option value="13"> 13 </option>
									<option value="14"> 14 </option>
									<option value="15"> 15 </option>
									<option value="16"> 16 </option>
									<option value="17"> 17 </option>
									<option value="18"> 18 </option>
									<option value="19"> 19 </option>
									<option value="20"> 20 </option>
									<option value="21"> 21 </option>
									<option value="22"> 22 </option>
									<option value="23"> 23 </option>
									<option value="24"> 24 </option>
									<option value="25"> 25 </option>
									<option value="26"> 26 </option>
									<option value="27"> 27 </option>
									<option value="28"> 28 </option>
									<option value="29"> 29 </option>
									<option value="30"> 30 </option>
									<option value="31"> 31 </option>
								</select>
								<?php
								include_once ("include/inc_mes.php");
								/**********
								&nbsp;&nbsp;Mês: 
								<select name="txtMes" class="textoLogin">
									<option value="01"> Janeiro </option>
									<option value="02"> Fevereiro </option>
									<option value="03"> Março </option>
									<option value="04"> Abril </option>
									<option value="05"> Maio </option>
									<option value="06"> Junho </option>
									<option value="07"> Julho </option>
									<option value="08"> Agosto </option>
									<option value="09"> Setembro </option>
									<option value="10"> Outubro </option>
									<option value="11"> Novembro </option>
									<option value="12"> Dezembro</option>
								</select>
								********/
								?>

								<?php
								include("include/inc_ano.php");
								/******
								print <<<EOF
								&nbsp;&nbsp;Ano:
								<select name="txtAno" class="textoLogin">
									<option value="$ANO_2">$ANO_2</option>
									<option value="$ANO_1">$ANO_1</option>
									<option value="$ANO" selected>$ANO</option>
								</select>
								********/
								print <<<EOF
								&nbsp;&nbsp;<input type="submit" value="ok" class="formLogin">
EOF;
								?>
							</td>
                          </tr>
                        </form>
			        </table>
					<?
						if($erro){
							echo "Sua consulta não retornou nenhum dado !";
							exit;
						}
					?>
				</td>
				</tr>
		  </table>
		</td>
	</tr>
</table>

</body>
</html>
<?php
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

				<!--tr bgcolor="#FFFFFF">
					<td colspan="2" align="center" class="linkSair"><a class="linkSair" href="imprimir_relatorio_dia.php">[ Relatório diario ] </a></td>
				</tr-->

				<tr>
					<td colspan="2">
					  <table  border="0" width="770px" align="center" cellpadding="2" cellspacing="2">
                        <form name="frmAjustar" action="imprimir_rel_periodo.php" method="post">
                          <input type="hidden" name="ajustarPonto" value="1">
                          <!--
                          <input type="hidden" name="idFuncionario" value="< ?=//$idFuncionario; ?>">
                          -->
                          <tr class="admDados">
                            <td height="25" colspan="2" align="left"> :: Gera relatório</td>
                            <td width="368" height="25" colspan="2" align="center" style="font-family: verdana; font-size:10px; color:#FFFFFF;">

					<?php
					print "Desde: ";
					include_once ("include/inc_dia.php");
					include_once ("include/inc_mes.php");
					include("include/inc_ano.php");
					##print_r($now_array);
					print " At&eacute;&nbsp;&nbsp;";
					show_sel_list("txtDia2",$DIAS_MES,$now_array['mday'],1,"textoLogin");
					print "&nbsp;&nbsp;";
					show_sel_list("txtMes2",$MESES_ANO,$now_array['mon'],1,"textoLogin");
					print "&nbsp;&nbsp;";
print $ANO_FINAL;
#print <<<EOF
#	<select name="txtAno2" class="textoLogin">
#	<!-- option value="$ANO_2">$ANO_2</option>
#	<option value="$ANO_1">$ANO_1</option-->
#	<option value="$ANO" selected>$ANO</option>
#	</select>
#EOF;

					print <<<EOF
&nbsp;&nbsp;
<input type="submit" value="ok" class="formLogin">
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
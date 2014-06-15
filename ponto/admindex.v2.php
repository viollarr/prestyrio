<?

// Includes
include_once "valida_login.php";
include_once "config.php";
include_once "funcoes_ponto.php";
conecta_bd();


?>
<html>
<head>
		<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	function getPonto()
	{
		window.open('index.php','po','fullscreen=1');
		void(0);
	}
</script>
</head>
<body leftmargin="0" topmargin="0">

<table border="0" width="870" align="left" cellpadding="0" cellspacing="0">
<!-- inclusão do topo -->
<? include_once "topo.php"; ?>
	<!--Inicio - Tabela  Menu  do Administrativo -->
	<tr bgcolor="#EEEEEE">
		<td class="textoLogin">
			<table border="0" width="870" align="center" cellpadding="0" cellspacing="0">
				<tr align="center">
					<td>
						<a href="javascript:getPonto();" class="linkMenu">
							<img src="img/ponto.jpg" border="0"><br>Iniciar ponto
						</a>
					</td>
					<td>
						<a href="funcionario/funcionarioindex.php" class="linkMenu">
							<img src="img/funcionario.gif" border="0"><br>Funcionário
						</a>
					</td>
					<td>
						<a href="horario/horarioIndex.php" class="linkMenu">
							<img src="img/horario.gif" border="0"><br>Horário
						</a>
					</td>
					<td>
						<a href="gera_relatorio2.php" class="linkMenu">
							<img src="img/relatorio.gif" border="0"><br>Gerar relatório
						</a>
					</td>
					<td>
						<a href="relatorio_periodo.php" class="linkMenu">
							<img src="img/relatorio.gif" border="0">
							<br>Gerar relatório por periodo
						</a>
					</td>
					<td>
						<a href="empresa/empresa.php" class="linkMenu">
							<img src="img/empresa.jpg" border="0"><br>Empresa
						</a>
					</td>
					<td>
						<a href="justificativa/justificativa.php" class="linkMenu">
							<img src="img/just.gif" border="0"><br>Justificativa
						</a>
					</td>

					<td>
						<a href="http://<?=$_SERVER['HTTP_HOST']?>/ponto/sair.php" class="linkSair">
							<img src="img/sair.gif" border="0"><br>Sair
						</a>
					</td>
				</tr>
		  </table>
		</td>
	</tr>
	<tr bgcolor="#EEEEEE">
		<td class="textoLogin">
			<table border="0" width="870" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left">
						<a href="cracha/cracha.php" class="linkMenu">
							<img src="img/empresa.jpg" border="0"><br>Crachá
						</a>
					</td>
				</tr>
		  </table>
		</td>
	</tr>
	<?php
	if($_SESSION['adm_grupo'] == 'superadm')
	{
	 print <<<EOF
	 <tr><td>
	 <a href="adm/">Administra&ccedil;&atilde;o do site/cria&ccedil;&atilde;o de usu&aacute;rios, ...</a>
	 </td></tr>
EOF;
	}
	?>
	
	<!--Fim - Tabela  Menu  -->
	<tr>
		<td colspan="6"><br>
		<?
		// Verifica número de horario ----------//
			$query 	= "SELECT * FROM horario";
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
				echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"> <b>*</b> Você deve cadastrar no mínimo um <b>horário</b>.</span><br>";
		
		// ----------- //
		
		// Verifica número de funcionário ----------//
			$query 	= "SELECT * FROM funcionario";
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
				echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"> <b>*</b> Você deve cadastrar no mínimo um <b>funcionário</b>.</span>";

		
		?>		
		</td>
	</tr>
</table>
<?php
#print "Ponto " . $_SESSION['ponto']['registered'];
?>

</body>
</html>

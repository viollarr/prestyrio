<?

// Includes
include_once "valida_login.php";
include_once "config.php";
include_once "classes/classefuncionario.php";
include_once "classes/classeEmp.php";


// Cria objeto
$objFuncionario = new Funcionario;
$objEmp = new Emp;

// Declara variáveis
$erro = null;
$totHora = null;

//$idFuncionario = $_POST['idFuncionario'];
$txtMes = date("m");
$txtAno = date("Y");

function somaHora($arrHoras){
	for($w=0; $w<sizeof($arrHoras); $w++){
		if($w % 2 == 0){
			$arrEnt[] = $arrHoras[$w];
		}else{
			$arrSai[] = $arrHoras[$w];
		}
	}
	
	for($h=0; $h<sizeof($arrEnt); $h++){
		$resEnt = strtotime($arrEnt[$h]);
		$resSai	= strtotime($arrSai[$h]);
		$result	= ($resEnt - $resSai) / 3600;
		$arrResult[] = $result; 
	}
	$arrHes = null;
	for($z=0; $z<sizeof($arrResult); $z++){
		$arrHes = $arrHes +  $arrResult[$z];	
	}
	echo "Horas trabalhadas: ".ceil($arrHes)/-1;
}


?>
<html>
<head>
		<title>..:: Ponto ::..</title>
<link href="estilo.css" rel="stylesheet" type="text/css">

</head>
<body topmargin="2" leftmargin="0">


<center><img src="img/logo.jpg"></center><br>


<table align="center" border="1" width="800px" cellpadding="0" cellspacing="0" style="border: 1px solid #000000;">
	<tr>
		<td class="textoLogin" align="left">Relatório diário: <?=date("d-m-Y");?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Périodo: <?=$txtMes?> / <?=$txtAno?></td>
	</tr>
	<tr>
		<td class="admDados" height="25" align="center">Cartão de ponto</td>
	</tr>
	<tr>
		<td class="textoLogin" height="25" align="left">
		<?
			$arrDadosd = $objEmp->getEmpForm();
			$varDadosd = explode("|",$arrDadosd[0]);
			echo "<b>".$varDadosd[1]."</b><br>";
			echo "<b>".$varDadosd[2]."</b><br>";
			echo "<b>C.G.C ".$varDadosd[3]."</b><br>";
		?>
		</td>
	</tr>
	
<?
$arrDadosId = $objFuncionario->getFuncionarioFormT();
for($t=0; $t<sizeof($arrDadosId); $t++)
{
	$varDados = explode("|",$arrDadosId[$t]);
	//echo $varDados[0]."<br>";
?>	
	<tr>
		<td class="textoLogin" height="25" align="left">
		<?
			//$arrDados = $objFuncionario->getFuncionarioForm($idFuncionario);
			//$varDados = explode("|",$arrDados[0]);
			echo "<i>".$varDados[2]."</i> <b>|</b>";
			
			
			$arrTeee = null;
			$dtData	 = date("Y-m-d");
				$arrHora = $objFuncionario->getDias($dtData,$varDados[0]);
				if($arrHora == ''){
					echo "Faltou !";
				}else{									
					for($j=0; $j<sizeof($arrHora); $j++){
						$varHora = explode("|",$arrHora[$j]);
						$varH2 = explode(" ",$varHora[1]);				
						echo "  <b>".$varH2[1]."</b>&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;";
						
						$arrTeee[] = $varHora[2];
					}
					//echo "$arrTeee[1]";
					//somaHora($arrTeee);
				
	/*
				// Verifica se existe justificativa
				$sqlJ = mysql_query("SELECT * FROM justificativa WHERE dapo_id = '$idData'");
				$numJust = mysql_num_rows($sqlJ);
				$arrJust = mysql_fetch_array($sqlJ);
				if($numJust > 0){
					$sqlJ2 = mysql_query("SELECT * FROM evento WHERE ev_id = '".$arrJust['ev_id']."'");
					$arrJust2 = mysql_fetch_array($sqlJ2);
						echo " -> ".$arrJust2['ev_desc'];
				}
	*/
			}
			
		?>
		</td>
	</tr>
<?
	}
?>

	<tr>
		<td class="textoLogin" align="left">
		</td>
	</tr>
	<?
		//}
	?>
</table>
<br>
<center><input type="button" onclick='javascript:self.print()' value="Imprimir"></center>

</body>
</html>

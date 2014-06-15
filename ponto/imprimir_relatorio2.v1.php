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
$txtDia = $_POST['txtDia'];
$txtMes = $_POST['txtMes'];
$txtAno = $_POST['txtAno'];

function somaHora($arrHoras){
$arrSai = null;
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


/*
$query	= "SELECT DISTINCT(date_format(po_data, \"%Y-%m-%d\")) FROM ponto WHERE date_format(po_data, \"%Y-%m\") = '2006-01'";
$result	= mysql_query($query);
while($arr = mysql_fetch_array($result))
	echo $arr[0]."<br>";

exit();
*/

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
		<td colspan="2" class="textoLogin" align="left">&nbsp;&nbsp;Periodo: <?=$txtDia?> / <?=$txtMes?> / <?=$txtAno?></td>
	</tr>
	<tr>
		<td colspan="2" class="admDados" height="25" align="center">Cartão de ponto</td>
	</tr>
	<tr>
		<td colspan="2" class="textoLogin" height="25" align="left">
		<?
			$arrDadosd = $objEmp->getEmpForm();
			$varDadosd = explode("|",$arrDadosd[0]);
			echo $varDadosd[1]."<br>";
			echo $varDadosd[2]."<br>";
			echo "C.G.C ".$varDadosd[3]."<br>";
		?>
		</td>
	</tr>
</table>

<table align="center" border="1" width="800px" cellpadding="0" cellspacing="0" style="border: 1px solid #000000;">	
<?
$arrDadosId = $objFuncionario->getFuncionarioFormT();
for($t=0; $t<sizeof($arrDadosId); $t++)
{
	$varDados = explode("|",$arrDadosId[$t]);
	//echo $varDados[0]."<br>";

//$arrDados = $objFuncionario->getFuncionarioForm($idFuncionario);
//$varDados = explode("|",$arrDados[0]);

$dtAdmFunc = explode("-",$varDados[7]);
$dtAdmFunc = $dtAdmFunc[2]."/".$dtAdmFunc[1]."/".$dtAdmFunc[0]; 

?>
	<tr>
	<td>
	<?
		//echo "<td class=\"textoLogin\" height=\"25\" align=\"left\">|<i>".$varDados[2]."</i> </td>";
		echo "<span style=\"font-family:verdana; font-size:12px;\"><b>".$varDados[2]."</b></span><br><br>";
		if($txtDia == 00)
		{
			$query	= "SELECT DISTINCT(date_format(po_data, \"%d-%m-%Y\")),po_semana FROM ponto WHERE date_format(po_data, \"%Y-%m\") = '$txtAno-$txtMes'";
			$result	= mysql_query($query);
			while($arr = mysql_fetch_array($result))
			{
				switch($arr[1]){
					case "Monday":
						$semana = $arr[0]."(<i>Seg</i>)&nbsp;&nbsp;";
						break;
					case "Tuesday":
						$semana = $arr[0]."(<i>Ter</i>)&nbsp;&nbsp;";
						break;
					case "Wednesday":
						$semana = $arr[0]."(<i>Qua</i>)&nbsp;&nbsp;";
						break;
					case "Thursday":
						$semana = $arr[0]."(<i>Qui</i>)&nbsp;&nbsp;";
						break;
					case "Friday":
						$semana = $arr[0]."(<i>Sex</i>)&nbsp;&nbsp;";
						break;
					case "Saturday":
						$semana = $arr[0]."(<i>Sab</i>)&nbsp;&nbsp;";
						break;
					case "Sunday":
						$semana = $arr[0]."(<i>Dom</i>)&nbsp;&nbsp;";
						break;
					}

						//echo "<td class=\"textoLogin\">--$semana</td>";
						echo "<span style=\"font-family:verdana; font-size:12px;\"><b>$semana</b>  </span>";
													
						$sSql = mysql_query("SELECT DATE_FORMAT(po_data, \"%H:%i:%s\") 
											 FROM ponto 
											 WHERE fu_id = '$varDados[0]' 
											 AND date_format(po_data, \"%d-%m-%Y\") = '$arr[0]'");
											 
							$varAux = 0;
							while($arrHora = mysql_fetch_array($sSql))							
							{
							   $cor = ($varAux % 2 == 0) ? "#0000FF" : "#FF0000" ;
								echo "<span style=\"font-family:verdana; font-size:12px; color:".$cor.";\">".$arrHora[0]."</span> - ";
						       $varAux++;
							}
							echo "<br>";
					}
				}
				else
				{
					$query	= "SELECT DISTINCT(date_format(po_data, \"%d-%m-%Y\")),po_semana FROM ponto WHERE date_format(po_data, \"%Y-%m-%d\") = '$txtAno-$txtMes-$txtDia'";
					$result	= mysql_query($query);
					while($arr = mysql_fetch_array($result))
					{
						switch($arr[1]){
							case "Monday":
								$semana = $arr[0]."(<i>Seg</i>)&nbsp;&nbsp;";
								break;
							case "Tuesday":
								$semana = $arr[0]."(<i>Ter</i>)&nbsp;&nbsp;";
								break;
							case "Wednesday":
								$semana = $arr[0]."(<i>Qua</i>)&nbsp;&nbsp;";
								break;
							case "Thursday":
								$semana = $arr[0]."(<i>Qui</i>)&nbsp;&nbsp;";
								break;
							case "Friday":
								$semana = $arr[0]."(<i>Sex</i>)&nbsp;&nbsp;";
								break;
							case "Saturday":
								$semana = $arr[0]."(<i>Sab</i>)&nbsp;&nbsp;";
								break;
							case "Sunday":
								$semana = $arr[0]."(<i>Dom</i>)&nbsp;&nbsp;";
								break;
							}

						//echo "<td class=\"textoLogin\">--$semana";
						echo "<span style=\"font-family:verdana; font-size:12px;\"><b>$semana</b>  </span>";
        					
							$sSql = mysql_query("SELECT DATE_FORMAT(po_data, \"%H:%i:%s\") 
											 FROM ponto 
											 WHERE fu_id = '$varDados[0]' 
											 AND date_format(po_data, \"%d-%m-%Y\") = '$arr[0]'");
											 
							$varAux = 0;
							while($arrHora = mysql_fetch_array($sSql))							
							{
							   $cor = ($varAux % 2 == 0) ? "#0000FF" : "#FF0000" ;
								echo "<span style=\"font-family:verdana; font-size:12px; color:".$cor.";\">".$arrHora[0]."</span> - ";
						       $varAux++;
							}
								echo "<br>";
							}
						}
						
						//echo "<br><br>";
				//echo "</td>";
			}
		?>

		</td>
	</tr>
	<?
		/* $arrDados = $objFuncionario->getMeses($txtMes,$txtAno);
		$sqlTemp = mysql_query("SELECT DISTINCT(date_format(po_data,\"%Y-%m-%d\")) as dtPonto,po_semana 
								FROM ponto 
								WHERE fu_id = $varDados[0]
								AND date_format(po_data,\"%d\") = $txtDia
								AND date_format(po_data,\"%m\") = $txtMes"); */
		//$arrNewMeses = getNewMeses($dia,$mes,$ano)						
								
		//while($arrRowsD = mysql_fetch_array($sqlTemp))
			//$arrDados[] = $arrRowsD[0]."|".$arrRowsD[1];
			
			
		/*
		for($i=0; $i<sizeof($arrDados); $i++)
		{
			$varData = explode("|",$arrDados[$i]);
			$bg = ($i % 2 == 0) ? '#EEEEEE' : '#FFFFFF' ;
		*/
	?>

</table>

<?
	//}
?>
<br>
<input type="button" onclick='javascript:self.print()' value="Imprimir">

</body>
</html>

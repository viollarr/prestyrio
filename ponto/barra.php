<?

function barcode($CodBarras)
// ************************************************
// Função para gerar e imprimir o código de barras
// ************************************************
 {

  $fino = 1;
  $largo = 5;
  $altura = 60;
  $p = "funcionario/boleto/imagens/preto.jpg";
  $b = "funcionario/boleto/imagens/branco.jpg";

$Bar[0] = "00110";
$Bar[1] = "10001";
$Bar[2] = "01001";
$Bar[3] = "11000";
$Bar[4] = "00101";
$Bar[5] = "10100";
$Bar[6] = "01100";
$Bar[7] = "00011";
$Bar[8] = "10010";
$Bar[9] = "01010";


// inicio código de barras

echo "<img src=\"$p\" width=\"$fino\" height=\"$altura\" border=\"0\">";
echo "<img src=\"$b\" width=\"$fino\" height=\"$altura\" border=\"0\">";
echo "<img src=\"$p\" width=\"$fino\" height=\"$altura\" border=\"0\">";
echo "<img src=\"$b\" width=\"$fino\" height=\"$altura\" border=\"0\">";

// meio do código de barras

for ($a = 0; $a < strlen($CodBarras); $a++){ 

    $Preto  = $CodBarras[$a]; 
    $CodPreto  = $Bar[$Preto]; 

    $a = $a+1; // Sabemos que o Branco é um depois do Preto... 
    $Branco = $CodBarras[$a]; 
    $CodBranco = $Bar[$Branco]; 


    // Encontrado o CodPreto e o CodBranco vamos fazer outro looping dentro do nosso 
    for ($y = 0; $y < 5; $y++) { // O for vai pegar os binários 

        if ($CodPreto[$y] == '0')
         { // Se o binario for preto e fino ecoa 
           echo "<img src=\"$p\" width=\"$fino\" height=\"$altura\" border=\"0\">";
         } 

        if ($CodPreto[$y] == '1')
         { // Se o binario for preto e grosso ecoa 
           echo "<img src=\"$p\" width=\"$largo\" height=\"$altura\" border=\"0\">";
         } 

        if ($CodBranco[$y] == '0') 
         { // Se o binario for branco e fino ecoa 
           echo "<img src=\"$b\" width=\"$fino\" height=\"$altura\" border=\"0\">";
         } 

        if($CodBranco[$y] == '1')
         { // Se o binario for branco e grosso ecoa 
           echo "<img src=\"$b\" width=\"$largo\" height=\"$altura\" border=\"0\">";
         } 
 
   } 

} // Fechamos nosso looping maior 

// fim código de barras

echo "<img src=\"$p\" width=\"$largo\" height=\"$altura\" border=\"0\">";
echo "<img src=\"$b\" width=\"$fino\" height=\"$altura\" border=\"0\">";
echo "<img src=\"$p\" width=\"$fino\" height=\"$altura\" border=\"0\">";

}


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
          FROM funcionario
          ORDER BY fu_num_cracha
         ";
$result = mysql_query($query);

$x = 0;
// Grava no arquivo
echo "<table align=\"center\" width=\"90%\" cellpadding=\"5\" cellspacing=\"5\" border=\"0\">";
echo "<tr>";
while($arrRows = mysql_fetch_array($result)) 
{	
    

	if ($x % 4 == 0)
	{
	    echo "</tr><tr><td align=\"center\">".$arrRows['fu_nome_cracha']."<br>( ".$arrRows['fu_num_cracha']." )<br><br>";
        barcode($arrRows['fu_num_cracha']);
		echo "</td>";
	}
	else
	{
	    echo "<td align=\"center\">".$arrRows['fu_nome_cracha']."<br>( ".$arrRows['fu_num_cracha']." )<br><br>";
        barcode($arrRows['fu_num_cracha']);
		echo "</td>";
	}
	

    $x++;
}
echo "</table>";
?>
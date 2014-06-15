<?php 

//session_start();

include "conecta_mysql.inc"; 
 
//echo 'loginok = '.$_POST['login'].'<br>';
//echo "loginok = ".$HTTP_POST_VARS['loginok']."<br>"; 

//mysql_select_db($mysql_id, 'site1');
#usuarios  | _id | _login | _senha |

//echo "login = " . $_POST['login'] . "<br>";
//echo "senha = " . $_POST['senha'] . "<br>";
//echo "crypto_senha = " . md5($_POST['senha']) . "<br>";
$sql = "SELECT * FROM `usuarios` WHERE login = '" . $_POST['login']. "' 
	AND senha = '" . $_POST['senha'] . "' ";
#	AND crypto_senha = '" . md5($_POST['senha']) . "'";

$resultado = mysql_query($sql, $mysql_id);
if(mysql_error($mysql_id))
{
    print("*** 1. $sql ***<br>");
    print "\nMysql error:" . mysql_errno($mysql_id)
            . " : "  . mysql_error($mysql_id) . "<br>";
    exit();
}

$errorbd=mysql_error($mysql_id);
//echo $errobd;
//printf("resultado: %s<br>\n", $resultado);
//printf("Id: %s<br>\n", mysql_result($resultado,0,"id"));

//$linha = mysql_fetch_row($resultado);
//echo $linha["id"]."<br>";

//echo 'registros achados = '.mysql_num_rows($resultado).'<br>';
$num = mysql_num_rows($resultado);
	if ($num == 1) 
	{
	
		while ($linha = mysql_fetch_object($resultado)) 
		{
			//echo "postsenha: ".md5($_POST['senha']). "<br>";
			//echo "bdsenha: ".md5($linha->senha). "<br>";
		
			if (md5($_POST['senha']) == md5($linha->senha)) 
			{
			//echo "variaveis sao iguais <br>";
			//echo 'logado com sucesso!!! <br>';
			session_start();
			$_SESSION['perfil'] = $linha->perfil;
			$_SESSION['login'] = $_POST['login'];
			$_SESSION['senha'] = $_POST['senha'];
				if (!isset($_SESSION['logado'])) 
				{
					$_SESSION['logado'] = "true";
				}
			header ("Location: index.php");
			}
		}
	
	}	else { echo "Login invalido.<br><br>"; 
				echo "<input type=submit name='Voltar' value='Voltar para pagina de login' OnClick='javascript:history.back(-1);'>";
					
	}

?>
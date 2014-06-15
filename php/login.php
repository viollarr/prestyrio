<?php
include "config.php";

// obtém os valores digitados
$login	= $_POST["login"];
$senha		= $_POST["senha"];

// acesso ao banco de dados
$resultado	= mysql_query("SELECT * FROM usuarios WHERE login='$login'");
$linhas		= mysql_num_rows($resultado);
$a = mysql_fetch_array($resultado);
if ($linhas==0)  // testa se a consulta retornou algum registro
{
	echo("<script>
			alert(\"Usuário N&Atilde;O encontrado\");
			window.location = '../index.php';
         </script>");
}
else
{
    if ($senha != mysql_result($resultado, 0, "senha")) // confere senha
	{
		echo("<script>
				alert(\"A senha está incorreta\");
				window.location = '../index.php';
			  </script>");
	}
	else   // usuário e senha corretos. Vamos criar os cookies
    {
        //setcookie("nome_usuario", $username);
        //setcookie("senha_usuario", $senha);
		//---- Cria sessão, depois do login...
		session_start();
		#$_SESSION['login_adm']	= $login;		
		$_SESSION['login']			= $login;
		$_SESSION['tipo']			= $a['tipo'];
		$_SESSION['id_usuario']		= $a['id'];
		$_SESSION['id_montador']	= $a['id_montador'];
        // direciona para a página inicial dos usuários cadastrados
		if ($a['tipo'] != 5){
        	echo("<script>window.location = '../adm_usuarios.php';</script>");  
		}
		else{
        	echo("<script>window.location = '../adm_montador.php';</script>");  
		}
    }
}
//mysql_close($con);
?>
<?php 

include "valida_sessao.php";

?>
<html>
<head>
<title>Inserir Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
@import url("../css/estilo.css");
</style>
</head>
<body>

<a href="javascript:history.back();">Voltar</a><br><br>

<?php 

if (!isset($inseriu)) { 

?>

<form method=POST action="<?php echo $PHP_SELF; ?>">
<input type=hidden name="inseriu" value="true">
<input type=hidden name="perfil" value="2">
<table id="todoform">    
<tbody><tr>       
<th colspan="2">Inserir Usuário</th>    
</tr>    
<tr>       
<td><label>*Login</label>
</td>
<td><input name="login" type="text" size="33" maxlength="100"></td>    
</tr>    
<tr>       
<td><label>*Senha</label> </td>
<td><input name="senha" type="password" size="33" maxlength="100"></td>    
</tr>    

<tr>       
<td><label>*Email</label> </td>
<td><input name="email" type="text" size="33" maxlength="100"></td>    
</tr>    


<tr> 
<td><input name="submit" type="submit" value="Enviar" class="botao"> 
</td>      
<td>* Campos de preenchimento obrigat&oacute;rio</td>    
</tr>  
</tbody></table>
</form>

<?php } else {
#| id | inicio              | ip_inicio | email | login  | senha  |
#crypto_senha                     | perfil | active |

echo "Dados inseridos: <br>";
echo "Login: ".$_POST['login'].'<br>';
echo "Senha: ".$_POST['senha'].'<br>';
//echo $_POST['perfil'].'<br>';

$sql_insere = "INSERT INTO usuarios 
(inicio,login,senha,crypto_senha,email,perfil)
VALUES 
(NOW(),
'". $_POST['login'] . "',
'" . $_POST['senha'] . "',
'" . md5($_POST['senha']) . "',
'". $_POST['email'] . "',
'" . $_POST['perfil'] . "'
)";
# 
$resultado = mysql_query($sql_insere, $mysql_id);
$errorbd=mysql_error($mysql_id);
#echo $errobd;
if($errorbd)
{
    print("*** 1. $sql_insere ***<br>");
    print "\nMysql error:" . mysql_errno($mysql_id)
            . " : "  . mysql_error($mysql_id) . "<br>";
    exit();
}

if (!isset($errobd)) 
 {
  echo "Dados alterados com sucesso!<br><br>";
  echo "<a href='index.php'>Voltar a página principal</a>";
 }
}
?>

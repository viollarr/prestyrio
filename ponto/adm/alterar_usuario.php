<?php 
session_start(); 
include "valida_sessao.php";
?>
<html>
<head>
<style type="text/css">
@import url("../css/estilo.css");
</style>
</head>
<title>Alterar Usuário</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<body>
<a href="javascript:history.back();">Voltar</a><br><br>
<?php 
if (!isset($alterou)) { 
?>
<form method=POST action="<?php echo $PHP_SELF; ?>">
<input type=hidden name="alterou" value="true"><table id="todoform">    
<tbody><tr>       
<th colspan="2">Alterar Usuário</th>    
</tr>    
<tr>       
<td><label>Login</label>
</td>
<td><input name="login" type="text" size="33" maxlength="100" value="<?php if (isset($_SESSION['login'])) { echo $_SESSION['login']; } ?>"></td>    
</tr>    
<tr>       
<td><label>*senha</label> </td>
<td><input name="senha" type="text" size="33" maxlength="100" value="<?php if (isset($_SESSION['senha'])) { echo $_SESSION['senha']; } ?>"></td>    
</tr>    
<tr> 
<td><input name="submit" type="submit" value="Enviar" class="botao"> 
</td>      
<td>* Campos de preenchimento obrigat&oacute;rio</td>    
</tr>  
</tbody></table>
</form>

<?php } else {

$sql = "UPDATE usuarios SET 
login='" . $_POST['login'] . "',
senha='" . $_POST['senha'] . "',
crypto_senha='" . md5($_POST['senha']) . "'
 WHERE login='" . $_SESSION['login'] . "' AND senha='" . $_SESSION['senha'] . "'";

$resultado = mysql_query($sql, $mysql_id);

$errorbd=mysql_error($mysql_id);
#echo $errobd;
if($errorbd)
{
    print("*** 1. $sql_insere ***<br>");
    print "\nMysql error:" . mysql_errno($mysql_id)
            . " : "  . mysql_error($mysql_id) . "<br>";
    exit();
}
	
if (!isset($errobd)) { echo "Dados alterados com sucesso!<br><br>";
 echo "<a href='index.php'>Voltar a página principal</a>";
}


}

?>
</body>
</html>

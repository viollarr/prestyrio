<?php session_start(); 

include "valida_sessao.php";

//echo $alterou;

?>

<html>
<head>
<style type="text/css">

@import url("../css/estilo.css");

</style>
</head>
<title>Alterar Usuários - Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<body>

<a href="javascript:history.back();">Voltar</a><br><br>

<?php 

if (isset($excluiu)) {

//echo "excluiu = true";

	$sql_del = "DELETE FROM usuarios WHERE id='$id'"; 
	
	$resultado_del = mysql_query($sql_del, $mysql_id);

	//$sql2 = "DELETE FROM menu WHERE id='$id'"; 
	
	//$resultado2 = mysql_query($sql2, $mysql_id);

		
	$errorbd=mysql_error();
//	echo $errobd;
	
		if (!isset($errobd)) { echo '<br>Dados excluidos com sucesso!<br><br>
		<a href="index.php">Voltar para pagina principal</a>
		';}

}

if (!isset($alterou) AND !isset($update)) { 

$sql = "SELECT * FROM usuarios";
$resultado = mysql_query($sql, $mysql_id);
//echo 'registros achados = '.mysql_num_rows($resultado).'<br>';
$num = mysql_num_rows($resultado);
if ($num > 0) {

?> 

<table id="todoform"> <tbody>

<?php

while ($linha = mysql_fetch_object($resultado)) {

?>

<tr>
<td>Login: <?php echo $linha->login; ?> </td>
<td>Email: <?php echo $linha->email; ?> </td>
<td><a href="<?php echo $PHP_SELF.'?alterou=true&id='.$linha->id; ?>">Alterar</a></td>
<td><a href="<?php echo $PHP_SELF.'?excluiu=true&id='.$linha->id; ?>">Excluir</a></td>
</tr>

<?php
}}
?>
</tbody>
</table>

<?php } else { 

if (!isset($update)) {
$sql_altera = "SELECT * FROM usuarios WHERE id=".$id;
//echo $id;
$resultado = mysql_query($sql_altera, $mysql_id); 
$num = mysql_num_rows($resultado);
	if ($num > 0) {
		while ($linha = mysql_fetch_object($resultado)) {
		
		?>

<form method=POST action="<?php echo $PHP_SELF; ?>">
<input type=hidden name="update" value="true">
<input type=hidden name="id" value="<?php echo $linha->id; ?>">
<table id="todoform">    
<tbody><tr>       
<th colspan="2">Formul&aacute;rio de contato</th>    
</tr>    

<tr>       
<td><label>Login</label>
</td>
<td><input name="login" type="text" size="33" maxlength="100" value="<?php if (isset($linha->login)) { echo $linha->login; } ?>"></td>    
</tr>    

<tr>       
<td><label>Senha</label> </td>
<td><input name="senha" type="password" size="33" maxlength="100" value="<?php if (isset($linha->senha)) { echo $linha->senha; } ?>"></td>
</tr>    

<tr>       
<td><label>Login</label>
</td>
<td><input name="email" type="text" size="33" maxlength="100" value="<?php if (isset($linha->login)) { echo $linha->email; } ?>"></td>    
</tr>    


<tr> 
<td><input name="submit" type="submit" value="Enviar" class="botao"> 
</td>      
<td>* Campos de preenchimento obrigat&oacute;rio</td>    
</tr>  
</tbody></table>
</form>		

<?php
		}
		}
	}



}

if (isset($update)) {

	$sql = "UPDATE usuarios SET 
	login='" . $_POST['login'] . "',
	senha='" . $_POST['senha'] . "',
	crypto_senha='" . md5($_POST['senha']) . "',
	email='" . $_POST['email'] . "'
	 WHERE id='" . $_POST['id'] . "'";
	
	$resultado = mysql_query($sql, $mysql_id);

	$errorbd=mysql_error();
	echo $errobd;
	
		if (!isset($errobd)) { echo "Dados alterados com sucesso!<br><br>";
		echo "<a href='index.php'>Voltar a página principal</a>";
		}
	
						}
	
	
?>

</body>
</html>
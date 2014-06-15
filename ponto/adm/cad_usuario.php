<?php
//include_once "php/valida_sessao.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ALCIONE</title>
	<link rel="stylesheet" href="css/estilo.css" type="text/css" />

</head>


<body>
<? include_once "inc_topo.php"; ?>
<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200px" align="center" valign="top" bgcolor="#0097F0"><? include_once "inc_menu.php"; ?></td>
    <td width="578" valign="top">
	<form action="php/inc_cad_usuario.php" method="post" name="frm" id="frm">
	<input type="hidden" value="<?=$_GET[ip];?>" name="ip" />
	<input type="hidden" value="<?=$_GET[data];?>" name="data" />
	<input type="hidden" value="<?=$_GET[hora];?>" name="hora" />
        <table width="350" border="0" align="center">
      <tr>
        <td colspan="2" align="center">Cadastrar usuários</td>
      </tr>
	  <tr>
	    <td align="left">Nome:</td>
	    <td align="left"><input name="adm_nome" type="text" id="adm_nome" size="30" /></td>
	  </tr>
	  <tr>
	    <td align="left">E-mail:</td>
	    <td align="left"><input name="email" type="text" id="email" size="30" /></td>
	  </tr>
	  <tr>
        <td width="120" align="left">Login: </td>
        <td align="left">
          <input name="login" type="text" id="login" size="30" maxlength="10" />
        </td>
	  </tr>
	  <tr>
	    <td align="left">Senha:</td>
	    <td align="left"><input name="senha" type="password" maxlength="8" id="senha" size="30" /></td>
	  </tr>
	  <tr>
	    <td align="center" colspan="2"><input type="submit" name="Submit" value="Entrar" /></td>
	  </tr>
	  <tr>
	    <td align="center" colspan="2">&nbsp;</td>
	  </tr>
    </table>
	</form>
	</td>
  </tr>
</table>
</body>
</html>
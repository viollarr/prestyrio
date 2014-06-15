<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>WB INTERNET</title>
<script type="text/javascript" src="js/funcoes.js"></script>
<script type="text/javascript" src="js/fckeditor.js"></script>
<link rel="stylesheet" href="css/estilo.css" type="text/css" />
</head>


<body>
<? 
 #include_once "inc_topo.php"; 
?>
<table width="778" border="0" align="center" cellpadding="0" cellspacing="0" class="tbl_geral">
  <tr>
    <td valign="top">
	<table width="570" border="0" align="center">
	  <tr>
        <td colspan="2" align="center">
		  <a href="cad_clientes.php"></a> </td>
      </tr>
	  <tr>
        <td>
	<form action="php/inc_lembrar_senha.php" method="post" name="frm" id="frm">
	  <table width="350" border="0" align="center">
      <tr>
        <td colspan="2" align="center">Lembrar senha ! </td>
      </tr>
	  <tr>
	    <td width="120" align="left">E-mail:</td>
	    <td align="left"><input name="email" type="text" id="email" size="30" /></td>
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
	</td>
  </tr>
</table>
</body>
</html>

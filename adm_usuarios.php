<?php

include_once "php/valida_sessao.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">



<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>NAEC - PRESTY-RIO</title>

	<link rel="stylesheet" href="css/estilo.css" type="text/css" />



</head>

<body>

<?php //include_once "inc_topo.php"; ?>

<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>

    <td width="200px" align="center" valign="top" class="menu"><?php include_once "inc_menu.php"; ?></td>

    <td width="578" valign="top">

	<table width="570" border="0" align="center">

	  <tr>

        <td colspan="2" align="center" bgcolor="#FFFFFF">

	<a href="cad_usuario.php">

	<img src="images/ico_cadastrar.jpg" alt="" width="54" height="38" border="0" />

	</a>

	<?php

        include "limite.php";

    ?>

    </td>

      </tr>

      <tr>

        <td valign="top"><?php include_once "php/inc_adm_usuario.php"; ?></td>

      </tr>

    </table>

	</td>

  </tr>

</table>

<?php include_once "inc_rodape.php"; ?>

</body>

</html>
<?php
include_once "php/valida_sessao.php";
include ("php/config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>NAEC - PRESTY-RIO</title>
	<script type="text/javascript" src="js/funcoes.js"></script>
	<link rel="stylesheet" href="css/estilo.css" type="text/css" />
</head>
<body>
<?php //include_once "inc_topo.php"; ?>
<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200px" align="center" valign="top" class="menu"><? include_once "inc_menu.php"; ?></td>
    <td width="578" valign="top">
	<form action="adm_atrasados.php" method="post" enctype="multipart/form-data" name="frm" id="frm" target="_self">
	<table width="570" border="0" align="center">
	  <tr>
		<td class="titulo">Escolha o Montador </td>
	  </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td><ul><li>Selecione o montador para gerar o relatório de notas atrasadas:</li></ul></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
      	<td>
            <select name="mont">
          <option value="" selected="selected">---Escolha um Montador---</option>
          <?php
			$select_all = "SELECT * FROM montadores WHERE ativo_m = '1' ORDER BY nome ASC";
			$query_all  = mysql_query($select_all);
				while($b_all = mysql_fetch_array($query_all)){
					echo '<option value="'.$b_all[id_montadores].'">'.$b_all[nome].'</option>';
				}
		?>
        </select></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
      	<td colspan="2" align="center" bgcolor="#FFFFFF"><input name="OK" type="submit"  value="OK"/></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	</form>
	</td>
  </tr>
</table>
<? include_once "inc_rodape.php"; ?>
</body>
</html>
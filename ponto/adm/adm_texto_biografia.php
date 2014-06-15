<?php
include_once "php/valida_sessao.php";
include("js/fckeditor.php") ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alcione</title>
<script type="text/javascript" src="js/funcoes.js"></script>
<script type="text/javascript" src="js/fckeditor.js"></script>
<link rel="stylesheet" href="css/estilo.css" type="text/css" />
</head>


<body>
<?php include_once "inc_topo.php"; ?>
<table width="778" border="0" align="center" cellpadding="0" cellspacing="0" class="tbl_geral">
  <tr>
    <td width="200px" align="center" valign="top" class="back_menu"><? include_once "inc_menu.php"; ?></td>
    <td width="578" valign="top">
	<!-- form action="php/inc_cad_servico.php" method="post"
		 enctype="application/x-www-form-urlencoded"
		 name="frm" id="frm"-->
	<form action="php/inc_texto_biografia.php" method="post" 
		enctype="application/x-www-form-urlencoded" 
		name="frm" id="frm">
	<table width="570" border="0" align="center">
	  <tr>
        <td colspan="2" align="center" bgcolor="#FFFFFF">
		<input type="image" src="images/ico_salvar.jpg" alt="Salvar" title="Salvar" />
		</td>
      </tr>
      <tr>
        <td>
		<?php
		
		include "php/config.php";
		
		$oFCKeditor = new FCKeditor('descricao', 530, 350);
		$oFCKeditor->Width  = '100%' ;
		$oFCKeditor->Height = '400' ;
		$oFCKeditor->BasePath = 'js/';
		#$oFCKeditor->ToolbarSet = 'metalfortec' ;
                #$oFCKeditor->ToolbarSet = 'Default' ;
		$oFCKeditor->ToolbarSet = 'wbinternet1' ;

		#mysql> #select _id,_texto from texto_biografia;
		#_id | texto_mc
		#$y = mysql_query("SELECT * FROM camarim_imprensa  ORDER BY _id DESC LIMIT 1");
		$y = mysql_query("SELECT * FROM texto_biografia  ORDER BY _id DESC LIMIT 1");

		while($x = mysql_fetch_array($y))
		{
		$id	= $x["_id"];
		$descricao	= $x["_texto"];
		##print "Descricao: $descricao<br>";
		$oFCKeditor->Value = "$descricao";
		}
		$oFCKeditor->Create();
		?>
		<input type="hidden" name="tabela" value="texto_biografia">
		<input type="hidden" name="campo" value="_texto">
		<input type="hidden" name="rotina" value="adm_texto_biografia.php">

		</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	</form>
	</td>
  </tr>
</table>
</body>
</html>

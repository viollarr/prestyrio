<tr>
	<td><img  width=870 src="../img/topo.jpg"></td>
</tr>
<?php
print <<<EOF
<tr bgcolor="#EEEEEE">
  <td class="textoLogin">&nbsp;&nbsp;
      Administrador: $_SESSION[login] ($_SESSION[funcao])&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="/$USUARIO/sair.php" class="linkSair">[sair]</a>
  </td>
</tr>
EOF;
?>
                                


<tr>
	<td>&nbsp;</td>
</tr>

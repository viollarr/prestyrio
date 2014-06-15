<?


if (isset($_POST['editar_usuario']))

{
include"config.php";
$id = $_POST['id_usuario'];
$ip = $_SERVER["REMOTE_ADDR"];
$data = date("y.m.d");
$hora = date("H:i:s");
$tipo  = $_POST["tipo"];
$email = $_POST["email"];
$login = $_POST["login"];
$senha = $_POST["senha"];

$query	= "
	UPDATE usuarios SET 
	ip = '".$ip."',
	data_hora = '" . $data;	# . "',

	#hora = '".$hora."',
	$query .= " $hora'" . ", 
	tipo  = '".$tipo."',
	email = '".$email."',
	login = '".$login."', 
	senha = '".$senha."' 
	
	WHERE id='".$id."'";

$result	= mysql_query($query);
#print "query: $query <br>"; exit();

Header("Location: ../adm_usuarios.php");

}

?>

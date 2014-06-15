<?
session_start();
session_unset();
session_destroy();
$_SESSION = array();
header("Location: http://".$_SERVER['HTTP_HOST']."/ponto/admin.php");
?>
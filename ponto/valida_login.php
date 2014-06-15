<?
session_start();
if(!isset($_SESSION['login'])){
header("Location: http://".$_SERVER['HTTP_HOST']."/ponto/admin.php");
}
?>
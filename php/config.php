<?php
$TABLE_ID_usuarios="id";
$TABLE_usuarios="usuarios"; 
$host = "localhost"; 
$user = "prestyri_prestyr"; 
$pass = "1861vic03"; 
$db = "prestyri_prestyri";
//$db = "prestyri_teste"; 

$a = @mysql_connect($host,$user,$pass) or die(mysql_error()); 
$b = @mysql_select_db($db,$a) or die("<b>Error connecting database Google.com => Contact your system administrator</b>");

?>
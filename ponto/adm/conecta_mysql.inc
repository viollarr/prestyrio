<?php

#$mysql_id = mysql_connect('mysql.wb.com.br', 'ecco', 'ecco') or
# die ('I cannot connect to the database because: ' . mysql_error());
#mysql_select_db('ecco');


#$db_link=mysql_pconnect("$HOST","$MYSQL_USER","$MYSQL_PASS") or
#mysql_select_db($DATABASE,$db_link))
$HOST="wbhost.wb.com.br";
$MYSQL_USER="wbhost_alcione";
$MYSQL_PASS="z3x2c1";
$DATABASE="wbhost_alcione";

$mysql_id=mysql_connect($HOST,$MYSQL_USER,$MYSQL_PASS);
$cnx_id=$mysql_id;

mysql_select_db($DATABASE);

?>

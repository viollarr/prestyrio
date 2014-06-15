<?php

#print "========="; #exit();

//$date_order="d/m/y";
//include("lang/portugues_br.php");
#$look_comd=1;
#global $id_contabil,$_SESSION,$accesslvl, $provedor_session, $PROVIDER_CRT, $date_order_Y;
global $id_contabil,$accesslvl, $provedor_session, $PROVIDER_CRT, $date_order_Y;
#
function secure($level)
{
	#global $id_contabil,$_SESSION,$accesslvl, $provedor_session, $PROVIDER_CRT, $date_order_Y;
	global $id_contabil,$accesslvl, $provedor_session, $PROVIDER_CRT, $date_order_Y;
	#global $PROVIDER_CRT, $date_order_Y;
	$DATA=date("Y-m-d H:i:s");
	if(!isset($PROVIDER_CRT)  or strlen($PROVIDER_CRT) < 3)
	{ system("echo -n \"PROVIDER_CRT ? NULL\" >> /tmp/secure.txt"); }
	if (! is_dir("/tmp/$PROVIDER_CRT"))
	{
	  mkdir("/tmp/$PROVIDER_CRT", 0700);
	  $MSG="0. $DATA -- Criando dir. /tmp/$PROVIDER_CRT\n";
	  system("echo -n \"$MSG\" >> /tmp/secure.txt");
	}
	session_save_path("/tmp/$PROVIDER_CRT");
	if(!isset($accesslvl) or empty($accesslvl)
		or !isset($_SESSION['accesslvl'])
		or empty($_SESSION['accesslvl']) 
		or !session_is_registered('accesslvl') 
	  )
	{
	  session_register("accesslvl");
	  $MSG="$DATA: Registrando accesslvl = $accesslvl\n";
	  system("echo -n \"$MSG\" >> /tmp/secure.txt");
	  #$accesslvl=$_SESSION['accesslvl'];	# = $level;
	}
	if(!isset($provedor_session) or empty($provedor_session)
		or !isset($_SESSION['provedor_session'])
		or empty($_SESSION['provedor_session'])
		or !session_is_registered('provedor_session')  )
	{
	  session_register("provedor_session");
	  $MSG="$DATA: Registrando provedor_session = $provedor_session\n";
	  system("echo -n \"$MSG\" >> /tmp/secure.txt");
	  #$provedor_session=$_SESSION['provedor_session'];	# = $PROVIDER_CRT;
	}
	#print "accesslvl: $accesslvl, PROVIDER_CRT: $PROVIDER_CRT, provedor_session: $provedor_session<br>";
	#sleep(1);
	if(! preg_match("#$PROVIDER_CRT#i",$provedor_session) )
	{
	  ##$DATA=date("$date_order_Y");
	  $MSG="1. $DATA -- PROVIDER_CRT: $PROVIDER_CRT, $id_contabil\nProvedor: $provedor_session\n";
	  $MSG .= "accesslvl: $accesslvl >= level: $level\n";
	  if(isset($_SESSION) and isset($_SESSION['accesslvl_contabil']) )
	  {
	   $MSG .= "_SESSION[provedor_session] = " . $_SESSION['provedor_session'] . "\n";
	   #print "_SESSION[provedor_session] : " . $_SESSION['provedor_session'];
	   $MSG .= "_SESSION[accesslvl_contabil] = " . $_SESSION['accesslvl_contabil'] . "\n";
	  }
	  system("echo -n \"$MSG\" >> /tmp/secure.txt");
	  return(false);
	}
	#if($accesslvl >= $level)
	if(intval($accesslvl) >= intval($level) )
	{
	  return(true);
	}
	else
	{
	  ##$DATA=date("$date_order_Y");
	  $MSG="2. $DATA -- PROVIDER_CRT: $PROVIDER_CRT, $id_contabil\nProvedor: $provedor_session\n";
	  if(isset($_SESSION))
	  {
	   $MSG .= "accesslvl: $accesslvl < level: $level ***\n";
	   $MSG .= "_SESSION[provedor_session] = " . $_SESSION['provedor_session'] . "\n";
	   $MSG .= "_SESSION[accesslvl_contabil] = " . $_SESSION['accesslvl_contabil'] . "\n";
	  }
	  system("echo -n \"$MSG\" >> /tmp/secure.txt");
	  return(false);
	}
}
#print "========="; exit();
 
function sel($var,$val)
{
	if($var==$val){
		print("selected");
	}
}

//function logevent($action,$clientid,$user,$amt=0,$bal=0)
function logevent($action,$clientid,$user,$amt=0,$bal=0)
{
	#include_once("dbinfo.php");
	include("dbinfo.php");
	global $db_link, $CLIENT_table;
	$when=time();
	//print($bal);
	//??Pedro 
	//$bal=update_balance($clientid);  //????????****
	$query_log="SELECT valormen,valormen2 FROM 
		$CLIENT_table WHERE clientid='$clientid' ";	##AND active='1'";
		//AND (end=0 OR end>$now) ";
		//  AND (end=0 OR end>$now) ";
		// and start<$when";
	$valormensal=mysql_query($query_log,$db_link);
        if( mysql_error($db_link) )
	{
		print("*** 198. $query_log ***<br>");
		print "\nMysql error:" . mysql_errno($db_link)
		. " : "  . mysql_error($db_link) . "<br>";
		exit;
	}
	//$valormensal=mysql_query($query_log,$db_link);
	$row2=mysql_fetch_row($valormensal);
	//$row2=mysql_fetch_array($valormensal);
	$valormensal0=$row2[0]; $valormensal2=$row2[1];
	//$valormensal0="xx$valormensal yy";
	//$valormensal0="$clientid";

	if($amt==0){
		$query_log="INSERT INTO EVENTS 
	       (action,clientid,user,time,balance,valormen) 
		VALUES('$action','$clientid','$user',
		'$when','$bal',	'$valormensal2')";}
	else{
		$query_log="INSERT INTO EVENTS 
	       (action,clientid,user,time,amount,balance,valormen)
	 VALUES('$action','$clientid','$user','$when','$amt',
		'$bal','$valormensal2')";
	}
	if(!mysql_query($query_log,$db_link)){
	 //print "Error ...<br>"; 
	 print "<br><center><h3>199. Error in mysql:<br>
	 $query_log <br>\nMysql error: " . mysql_errno($db_link)
	 . " : "  . mysql_error($db_link);
	 return(1);
	}
	else{return(0);}
}

function update_balance($clientid)
{
	global $PACKAGES_table, $CLIENT_table, $db_link; 
	include("dbinfo.php");	#Se usar include_once o valormen fica errado
	$now=time();
	# CUIDADO COM A PERIODICIDADE : period - anual, trimestral...
	#periodo: 1=mensal, 3=trimestral, 6=semestral = $period_array[]
	#$start_ini=date("$date_order",$spit[start]);
	#$start_vect=explode("/",$start_ini); $start_mes=$start_vect[1];
	#$periodicidade_ori=$start_mes  % $periodo;
	#$MES -- period: 
	$flag_period3=false; $flag_period6=false; $flag_period12=false;
	if($MES % 3 == 0)$flag_period3=true;
	if($MES % 6 == 0)$flag_period6=true;
	if($MES % 12 == 0)$flag_period12=true;
	$query_bal="SELECT sum(balance) FROM $PACKAGES_table WHERE 
	clientid='$clientid' AND active='1' and start<'$now'
	 AND (end='0' OR end>'$now') and suspend='0' 
	 AND period='1'
	";
	if($flag_period3) $query_bal .= " AND period='3' ";
	if($flag_period6) $query_bal .= " AND period='6' ";
	if($flag_period12) $query_bal .= " AND period='12' ";

	//$one=mysql_query($query_bal,$db_link);
	//print "$query_bal, $db_link";
	$one=mysql_query($query_bal,$db_link);
	if(mysql_error($db_link))
	{
		print "<br><center><h3>Error in mysql:<br>
		$query_bal <br>\nMysql error: " . mysql_errno($db_link)
		. " : "  . mysql_error($db_link);
		exit();
	}
	//$one=mysql_query($query_bal,$db_link);
	$r=mysql_fetch_row($one);

	if($r[0] != null)
	{			
		$query_bal="UPDATE $CLIENT_table SET balance='$r[0]' WHERE clientid='$clientid'";
		if(mysql_query($query_bal,$db_link)){return($r[0]);}
		else{return(null);}
	}
	else{
		//return(0.00);
		$query_bal="SELECT valormen,valormen2 FROM $CLIENT_table WHERE clientid='$clientid'
		AND active='1' ";	// and start<$now";
		$one=mysql_query($query_bal,$db_link);
		$r=mysql_fetch_row($one);
		###return($r[0]);
		return($r[1]);		#Retorna 'valormen2'
		//return($one);
	}
}

function zip_number($sZip){ 
    $sZip = ereg_replace("[^0-9]",'',$sZip); 
    #if(strlen($sZip) != 10) return(False);
    $sZip = trim($sZip);
    if(strlen($sZip) < 8) return(False); 
    $sArea = substr($sZip,0,5); 
    $sPrefix = substr($sZip,5,3); 
    //$sNumber = substr($sZip,6,3); 
    //$sZip = "(".$sArea.")".$sPrefix."-".$sNumber; 
    $sZip = $sArea . "-" . $sPrefix;	// . $sNumber; 
    return($sZip); 
} 

function is_zip_number($sZip){ 
    $sZip = ereg_replace("[^0-9]",'',$sZip); 
    $sZip = trim($sZip);
    if(strlen($sZip) < 8) return(0); 
    return(1); 
} 

function is_phone_number($Phone){ 
    $Phone = ereg_replace("[^0-9]",'',$Phone); 
    if(strlen($Phone) < 8) return(0); 
    return(1);
    #$tam=strlen($Phone);
    #$sArea = substr($Phone,$tam-4,4); 
    #$sPrefix = substr($Phone,0,$tam-4); 
    #$Phone = $sArea . "-" . $sPrefix;
    #return($Phone); 
} 

function data6($sdate){ 
    if(strlen($sdate) != 6) return(False); 
    //if(strlen($sdate) < 6) return(False); 
    $sDia = substr($sdate,0,2); 
    $sMes = substr($sdate,2,2); 
    $sAno = substr($sdate,4,2); 

    $sData = $sDia . "/" . $sMes . "/" . $sAno;
    return($sData); 
} 

function data8($sdate){ 
    if(strlen($sdate) != 6) return(False); 
    //if(strlen($sdate) < 6) return(False); 
    $sDia = substr($sdate,0,2); 
    $sMes = substr($sdate,2,2); 
    $sAno = substr($sdate,4,2); 

    $sData = $sDia . "/" . $sMes . "/20" . $sAno;
    return($sData); 
} 

function data10($sdate){ 
    if(strlen($sdate) != 8) return(False); 
    //if(strlen($sdate) < 6) return(False); 
    $sDia = substr($sdate,0,2); 
    $sMes = substr($sdate,2,2); 
    $sAno = substr($sdate,4,4); 

    $sData = $sDia . "/" . $sMes . "/" . $sAno;
    return($sData); 
} 
function data10y($sdate){ 
    if(strlen($sdate) != 8) return(False); 
    $sDia = substr($sdate,6,2); 
    $sMes = substr($sdate,4,2); 
    $sAno = substr($sdate,0,4); 

    $sData = $sDia . "/" . $sMes . "/" . $sAno;
    return($sData); 
} 

function get_clientid($nome,$flag_error)
{
   global $db_link;
   #include_once("dbinfo.php");
   include("dbinfo.php");
   if(! isset($flag_error)) $flag_error=1;
   if(! $nome or strlen($nome) < 3) return(0);
   $nome00=substr($nome,0,20);
   #$nome00=$nome;
 	   $query_cl="SELECT clientid FROM $CLIENT_table WHERE first like
	'$nome00%' ";	//AND active=1";
   $result=mysql_query($query_cl,$db_link);
   if(mysql_error($db_link) or !$result)
   {
    print("*** $query_cl *** $result ***<br>");
    print "\nMysql error:" . mysql_errno($db_link)
            . " : "  . mysql_error($db_link) . "<br>";
            #exit();
    return(0);
   }
   #$row2=mysql_fetch_row($result);
  	   #$row_name=mysql_fetch_array($result,MYSQL_BOTH);
  	   #$clientid_nr=$row2[0];
  	   ##$clientid_nr=$row_name[clientid];
   $nr_clients=0;
           while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $clientid_nr=$clientes[clientid];
    #print $clientes[clientid];
    #print ", ";
   }

  	   #if(! $clientid_nr) { print "Erro! Não encontramos nenhum cliente
   #deste nome / username [ $nome / $username ].<br>";
   #$client_nr=0;
   ##exit(); 
   #}
 	   #if(! $clientid_nr or $nr_clients > 1 or $clientid_nr < 1) { print 
 	   if( (! $clientid_nr or $nr_clients != 1) and $flag_error)
   { 	print 
	"1. Erro! Não encontramos 
	 nenhum cliente deste username [ $username / $nome / 
	 $nr_clients / $clientid_nr ] ou existem vários ($nr_clients).<br>";
	 #exit(); 
	 return(0);
   }
   return($clientid_nr);
}

function set_clientid($invid_nr,$nome,$invnum_nr)
{
  #include_once("dbinfo.php");
  include("dbinfo.php");
  global $db_link;
  if($invid_nr < 1 or  strlen($nome) < 1 or $invnum_nr < 1) 
  return(0);

  #Correct invoice. Insert clientid into invoice:
  $clientid2=get_clientid($nome,0);
  if($clientid2 < 1) return(0);
  #Teste se existe mais de uma invnum com o mesmo nr.
  $query_invcount="SELECT count(invnum) FROM 
	$INVOICES_table WHERE invnum like '%$invnum_nr%'";
	#WHERE active=1";
  $output_inv=mysql_query($query_invcount,$db_link_INVOICES);
  if(mysql_error($db_link_INVOICES) or ! $output_inv)
  {
   print("*** 1. $query_invcount ***<br>");
   print "\nMysql error:" . mysql_errno($db_link_INVOICES)
   . " : "  . mysql_error($db_link_INVOICES) . "<br>";
   #exit();
   return(0);
  }
  $acctcount=mysql_result($output_inv,0);

  $query_cli="UPDATE $INVOICES_table SET
   clientid='$clientid2' WHERE invid='$invid_nr'";
  if(!mysql_query($query_cli,$db_link_INVOICES))
   {
   	print("<br>$L_Invoice #$invnum_nr /
	$L_Client $clientid2 :
   	$L_Not_Updated.<br>" .
	mysql_errno($db_link_INVOICES) .  mysql_error($db_link_INVOICES)
	);
	#exit();
	return(0);
           ####} else  $clientid_nr=$clientid2;
   } else  return($clientid2);
}
###################### valormens ###########################
function calc_clientmens($clientid_nr)
{
 global $PACKAGES_table, $CLIENT_table, $db_link;
 #Calcula mens do clientid
 $change_valormen=false;
 include("dbinfo.php");
 #Se usar include_once ocorrem varios erros. As variaveis declaradas em config.php sao perdidas.
 
 $now=time();
 $SUSPENSO=" AND suspend='0' ";
 $SUSPENSO= " " ;
 $query_calc="SELECT start,cost,period FROM $PACKAGES_table WHERE 
  clientid='$clientid_nr' AND
  active='1' $SUSPENSO AND parentpack='0' AND start<'$now' 
  AND (end='0' OR end>'$now')
  ";
 #1-3-2008 ignorey AND suspend='0'
 #

 #active=1 AND parentpack=0 AND start<$now AND (end=0 OR end>$now)
 $throughput=mysql_query($query_calc,$db_link);
 $custo_tot=0;
 $data=$words_date[1];
 while($spit=mysql_fetch_array($throughput,MYSQL_BOTH))
 {
   $change_valormen=true;
   $start_ini=date("$date_order",$spit[start]);
   $start_vect=explode("/",$start_ini);  $start_mes=$start_vect[1];
   $periodo=$spit[period];
   $periodicidade_ori=$start_mes  % $periodo;
   $periodicidade=$words_date[1] % $periodo;
   ##print "<br>Periodo: $periodo , $data,  $periodicidade ...";
           #$row_valormen = sprintf ("%.2f;",$row_valormen);
           $custo_crt=$spit[cost];
   #print "Somando "; printf ("%.2f , ini: $start_ini ;",$custo_crt);
   if($periodicidade == $periodicidade_ori){
     #print "Somando "; printf ("%.2f , ini: $start_ini ;<br>",$custo_crt);
     #print "<br>";
     $custo_tot +=$custo_crt;
   }
   else {
     #print "Sem somar $custo_crt por que a periodicidade 
     #	foi $periodo == $periodicidade_ori == $start_mes $start_ini .<br>";
   }
 }
 ##$total=$spit[0];
 $total = $custo_tot;
 ##if($change_valormen)$total += 1;
 if($MIN_VALUE2CHARGE < 5) { $MIN_VALUE2CHARGE=5; }
         #if($total > $MIN_VALUE2CHARGE or $change_valormen){
 $total = sprintf ("%.2f",$total);
 if($change_valormen)
 {
  //$query2="UPDATE CLIENT SET valormen='$cost_tot' WHERE clientid='$clientid' ";
  $query2_upd="UPDATE $CLIENT_table SET valormen='$total',valormen2='$total' 
        WHERE clientid='$clientid_nr' ";
  $result2=mysql_query($query2_upd,$db_link);
  //print "Valor da mensalidade para o cliente $clientid  = $total\n<br>";	 
 }
 return($total);
}
############################

function wich_provider($client_nr){ 
    global $PROVEDORES;
#   foreach($myArray as $Key => $Value) {
#      echo "Key:" . $Key;
#      echo "Value:" . $Value;
#    }
foreach($PROVEDORES as $Key => $Value) {
      	  #echo "$client_nr : Key:" . $Key;
      	  #echo "Value:" . $Value[min] . " - " . $Value[max];
  if($client_nr >= $Value[min] and $client_nr <= $Value[max])
	return("(" . $PROVEDORES[$Key][provedor] . ")");
}
    return ("??");
    #return("(" . $PROVEDORES[esquadro][provedor] . ")"); 
} 

//global $serverurl;
//$serverurl="Base url for internal links";
//$serverurl="http://www.momentus.com.br/wbradio/cobranca";
//$serverurl="http://lin.wb.com.br/wbradio/cobranca";
//$serverurl="/contabil/esquadro"; //Foi para config.php
//global $MIN_VALUE2CHARGE, $yeardiscount, salestax;

$yeardiscount=0.1;
$salestax=0.0;		
$MIN_VALUE2CHARGE="5.00";
################################################
#http://www.phpportalen.net/viewtopic.php?t=15779&highlight=character+replace
#Sen ska vi inte glömma att det finns andra tecken som kan skapa problem än
#å, ä och ö. Följande funktion byter ut tecken som í, è och ü mot motsvarande
#"engelska" tecken (i, e, u) och byter till slut ut alla andra konstiga
#tecken (som ., # och !) mot _. 

function NoStrangeLetters($s) 
{ 
   $pattern = array("'é'", "'è'", "'ë'", "'ê'", "'É'", "'È'", "'Ë'", "'Ê'",
"'á'", "'à'", "'ä'", "'â'", "'å'", "'Á'", "'À'", "'Ä'", "'Â'", "'Å'", "'ó'",
"'ò'", "'ö'", "'ô'", "'Ó'", "'Ò'", "'Ö'", "'Ô'", "'í'", "'ì'", "'ï'", "'î'",
"'Í'", "'Ì'", "'Ï'", "'Î'", "'ú'", "'ù'", "'ü'", "'û'", "'Ú'", "'Ù'", "'Ü'",
"'Û'", "'ý'", "'ÿ'", "'Ý'", "'ø'", "'Ø'", "'Æ'", "'ç'", "'Ç'"); 
#'Û', 'ý', 'ÿ', 'Ý', 'ø', 'Ø', '\.', '\.', 'Æ', 'ç','Ç'); 
   $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 
'a', 'a', 'a','a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 
'o', 'o', 'o', 'O', 'O', 'O', 'O','i', 'i', 'i', 'I', 
'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U',
'U', 'y', 'y', 'Y', 'o', 'O', 'A', 'c', 'C'); 
#'U', 'y', 'y', 'Y', 'o', 'O', 'a',  'A',  'A', 'c', 'C'); 
   $s = preg_replace($pattern, $replace, $s);
   #Ver: $addr = strtr($addr, "äåö", "aao");
   $s = strtoupper($s);
   return($s);
   ##return preg_replace('/[^a-z0-9\.]/i', "_", $s); 
} 

function str_replace_special($str) {
   // Ver tb: http://br.php.net/manual/pt_BR/function.str-replace.php
   //esta funçao retira todos os acentos e caracteres especiais, deixando apenas
   // o '_' e as vogais em si. 
   // Pedro Ivo(pedroivo@linuxgoias.net)
   // acentos e caracteres padroes do portugues brasileiro
   $trade = array('á'=>'a','à'=>'a','ã'=>'a',
                 'ä'=>'a','â'=>'a',
                 'Á'=>'A','À'=>'A','Ã'=>'A',
                 'Ä'=>'A','Â'=>'A',
                 'é'=>'e','è'=>'e',
                 'ë'=>'e','ê'=>'e',
                 'É'=>'E','È'=>'E',
                 'Ë'=>'E','Ê'=>'E',
                 'í'=>'i','ì'=>'i',
                 'ï'=>'i','î'=>'i',
                 'Í'=>'I','Ì'=>'I',
                 'Ï'=>'I','Î'=>'I',
                 'ó'=>'o','ò'=>'o','õ'=>'o',
                 'ö'=>'o','ô'=>'o',
                 'Ó'=>'O','Ò'=>'O','Õ'=>'O',
                 'Ö'=>'O','Ô'=>'O',
                 'ú'=>'u','ù'=>'u',
                 'ü'=>'u','û'=>'u',
                 'Ú'=>'U','Ù'=>'U',
                 'Ü'=>'U','Û'=>'U',
                 '$'=>'_','@'=>'_','!'=>'_',
                 '#'=>'_','%'=>'_',
                 '^'=>'_','&'=>'_','*'=>'_',
                 '('=>'_',')'=>'_',
                 '-'=>'_','+'=>'_','='=>'_',
                 '\\'=>'_','|'=>'_',
                 ''=>'_','~'=>'_','/'=>'_',
                 '\"'=>'_','\''=>'_',
                 '<'=>'_','>'=>'_','?'=>'_',
                 ','=>'_','ç'=>'c','Ç'=>'C');
   return(strtr($str,$trade));
} 
#
function replace_acentuada_ansi($input_string)
{
$com_acentos=array(
  "Ã¡","Ã","Ã£","Ãƒ",
  "Ã¢","Ã‚","Ã ","Ã€",
  "Ã©","Ã‰","Ãª","ÃŠ",
  "Ã­","Ã","Ã³","Ã“",
  "Ãµ","Ã•","Ã´","Ã”",
  "Ãº","Ãš","Ã¼","Ãœ",
  "Ã§","Ã‡");
$sem_acentos=array(
  "a","A","a","A",
  "a","A","a","A",
  "e","E","e","E",
  "i","I","o","O",
  "o","O","o","O",
  "u","U","u","U",
  "c","C");
#$input_string = utf8_decode($input_string);
#Uso: $output_string = str_replace($com_acentos,$sem_acentos,$input_string);                                             
$output_string=str_replace($com_acentos,$sem_acentos,$input_string);
#print_r($com_acentos); print_r($sem_acentos); print "$input_string, $output_string";
#return utf8_encode($output_string); 
return($output_string);

}
#
function normalize_br ($string) 
{
$table = array(
 'Å '=>'S', 'Å¡'=>'s', 'Ä'=>'Dj', 'Ä‘'=>'dj', 'Å½'=>'Z', 'Å¾'=>'z', 'ÄŒ'=>'C', 'Ä'=>'c', 'Ä†'=>'C', 'Ä‡'=>'c',
 'Ã€'=>'A', 'Ã'=>'A', 'Ã‚'=>'A', 'Ãƒ'=>'A', 'Ã„'=>'A', 'Ã…'=>'A', 'Ã†'=>'A', 'Ã‡'=>'C', 'Ãˆ'=>'E', 'Ã‰'=>'E',
 'ÃŠ'=>'E', 'Ã‹'=>'E', 'ÃŒ'=>'I', 'Ã'=>'I', 'ÃŽ'=>'I', 'Ã'=>'I', 'Ã‘'=>'N', 'Ã’'=>'O', 'Ã“'=>'O', 'Ã”'=>'O',
 'Ã•'=>'O', 'Ã–'=>'O', 'Ã˜'=>'O', 'Ã™'=>'U', 'Ãš'=>'U', 'Ã›'=>'U', 'Ãœ'=>'U', 'Ã'=>'Y', 'Ãž'=>'B', 'ÃŸ'=>'Ss',
 'Ã '=>'a', 'Ã¡'=>'a', 'Ã¢'=>'a', 'Ã£'=>'a', 'Ã¤'=>'a', 'Ã¥'=>'a', 'Ã¦'=>'a', 'Ã§'=>'c', 'Ã¨'=>'e', 'Ã©'=>'e',
 'Ãª'=>'e', 'Ã«'=>'e', 'Ã¬'=>'i', 'Ã­'=>'i', 'Ã®'=>'i', 'Ã¯'=>'i', 'Ã°'=>'o', 'Ã±'=>'n', 'Ã²'=>'o', 'Ã³'=>'o',
 'Ã´'=>'o', 'Ãµ'=>'o', 'Ã¶'=>'o', 'Ã¸'=>'o', 'Ã¹'=>'u', 'Ãº'=>'u', 'Ã»'=>'u', 'Ã½'=>'y', 'Ã½'=>'y', 'Ã¾'=>'b',
 'Ã¿'=>'y', 'Å”'=>'R', 'Å•'=>'r',
 );
 #$string=utf8_decode($string);
 #$string=iconv("UTF-8", "CP1252", $string);
$txt = strtr($string,
 "\xe1\xc1\xe0\xc0\xe2\xc2\xe4\xc4\xe3\xc3\xe5\xc5".
 "\xaa\xe7\xc7\xe9\xc9\xe8\xc8\xea\xca\xeb\xcb\xed".
 "\xcd\xec\xcc\xee\xce\xef\xcf\xf1\xd1\xf3\xd3\xf2".
 "\xd2\xf4\xd4\xf6\xd6\xf5\xd5\x8\xd8\xba\xf0\xfa".
 "\xda\xf9\xd9\xfb\xdb\xfc\xdc\xfd\xdd\xff\xe6\xc6\xdf",
 "aAaAaAaAaAaAacCeEeEeEeEiIiIiIiInNoOoOoOoOoOoOoouUuUuUuUyYyaAs");
 return($txt);
 #return strtr($string, $table);
 #$string=utf8_encode($string);
}
#
### give the form to add new domain
function give_form($novo_dom)
{
        #include_once("config.php");
        include("config.php");	
        #Se usar include_once ocorrem varios erros. As variaveis declaradas em config.phpsao perdidas.
	global $DNS_MASTER,$NS_RECORD1,$NS_RECORD2,$MX_DEFAULT,$WWW_SERVER,
	  $FTP_SERVER,$FTP_SERVER,$SMTP_SERVER,$POP_SERVER,$cname_record,
	  $username,$password,$username_mail,$password_mail,
	  $pass,$row,$domain_name,$dontsync,
	  $use_spf,$envia_email,$banda_vol, $packid, $email2tecnico;
	global $sistema_op, $groupo_contabil, $revendedor;
	global $sistema_op_pop, $revendedor_pop;

	global $suspenddominio,$suspendd2, $suspendd2_mail, $suspendd2_www;
#	#$suspendd2_index=0;
#	print <<<EOF
#    ==>0. suspenddominio: $suspenddominio, 
#	suspenddominio2: $suspendd2[$suspendd2_index] <==
#EOF;

	$clientid=$row['clientid'];
	global $cli_revenda_dominio;
	#$cli_revenda_dominio=$row['cli_revenda_dominio'];
	$cli_revenda_dominio=get_variavel_client($clientid,'cli_revenda_dominio');
	##print "clientid: $clientid, cli_revenda_dominio: $cli_revenda_dominio";
	#print_r($row); exit();

	$ERRO_DECL="<blink><b>*** ERRO ***</b></blink>";
	#&html_header();
        //$date_order="y/m/d";
	//$then=date("$date_order");
	$SIZE=20; $MAX_TEXT_AREA=5000;
	##print "--->$banda_vol<--<br>";
	if(! $banda_vol or strlen($banda_vol)< 2)
	{ $banda_vol=$banda_vol_DEFAULT; }
	#print "0. --->$banda_vol, $novo_dom<--<br>";
	if(! $novo_dom)
	{
	 ##global $row;
	 #$banda_vol=$banda_vol_DEFAULT;
	 $suspend=$row['suspend'];
	 ##$dontsync=$row['dontsync'];
	 $domain_name=$row['DOMAIN_NAME'];
	 $serial_number=$row['serial_number'];
	 $username=$row['username']; $password=$row['password'];
	 $dis_pass_www="true"; $password_new="";
	 if(strlen($username) < 4)
	 {
	   $inicio=substr($domain_name,0,1);
	   $username=$inicio ."w". $packid;
	   $password=generatePassword(9,4);
	   $password_new=$password;
	   $dis_pass_www="false";
	 }
	 if(strlen($password) < 5)
	 {
	   $password=generatePassword(9,4);
	   $password_new=$password;
	   $dis_pass_www="false";
	 }

	 $ns_record=$row['NS_RECORD'];
	 $mx_record=$row['MX_RECORD'];
         $spf_record=$row['SPF_RECORD'];

	 $master=$row['MASTER_DNS'];
	 $secondary1=$row['DNS_SECONDARY1'];
	 //$mail_test_com=$row[];
	 //$ns_test_com="dns1.wb.com.br";
	 //$ns_test_com2="dns2.wb.com.br";
	 $www=$row['WWW']; $ftp=$row['FTP'];
	 $smtp=$row['SMTP'];  $pop=$row['POP'];
	 $username_mail=$row['username_mail'];
	 $password_mail=$row['password_mail']; 
	 $dis_pass_mail="true"; $password_mail_new="";
	 if(strlen($username_mail) < 4)
	 {
	   $inicio=substr($domain_name,0,1);
	   $username_mail=$inicio ."s". $packid;
	   $password_mail=generatePassword(9,4);
	   $password_mail_new=$password_mail;
	   $dis_pass_mail="false";
	 }
	 if(strlen($password_mail) < 5)
	 {
	   $password_mail=generatePassword(9,4);
	   $password_mail_new=$password_mail;
	   $dis_pass_mail="false";
	 }

	 $cname_record=$row['CNAME_RECORD'];
	 $MSG_INI="Dominio <b>$domain_name</b>";
	 $email2tecnico=$row['email2tecnico'];
	}
	else
	{
	 #$dontsync=$row['dontsync'];
	 #$envia_email=$row['envia_email'];
	 #$banda_vol=$row['banda_vol'];
	 $banda_vol=$banda_vol_DEFAULT;
	 $serial_number = `date  '+%Y%m%d%H'`;
	 $master=$DNS_MASTER;		//"dns1.wb.com.br";
	 $ns_record=$NS_RECORD1;	//"dns1.wb.com.br";
	 $secondary1=$NS_RECORD2;	//$secondary1="dns2.wb.com.br";
	 $mx_record=$MX_DEFAULT0;	//"hp.wb.com.br";
	 //$mail_test_com="hp.wb.com.br";
	 //$ns_test_com="dns1.wb.com.br";
	 //$ns_test_com2="dns2.wb.com.br";
	 #$SERVIDOR_DEFAULT
	 $www=$SERVIDOR_DEFAULT['WWW_SERVER0'];	#$WWW_SERVER0;		//"hp.wb.com.br";
	 $ftp=$SERVIDOR_DEFAULT['FTP_SERVER0']; #$FTP_SERVER0;		//"hp.wb.com.br";
	 $smtp=$SERVIDOR_DEFAULT['SMTP_SERVER0']; #$SMTP_SERVER0;		//"hp.wb.com.br";
	 $pop=$SERVIDOR_DEFAULT['POP_SERVER0']; #$POP_SERVER0;		//"hp.wb.com.br";

	 $cname_record="";
	 $MSG_INI="Em caso de dominio";
	}
	#print "1. --->$banda_vol<--<br>";
	$MSG_REVENDA="";
	if($cli_revenda_dominio)
	{
	#$MSG_REVENDA="Revendor de dominio: ele cadastra o dominio no servidor onde está";
	 $MSG_REVENDA=$cli_revenda_dominio_opcoes[$cli_revenda_dominio]['MSG_SEL'];
	 $MSG_REVENDA2=$cli_revenda_dominio_opcoes[$cli_revenda_dominio]['MSG_FULL'];
	}
	$STATUS=get_domain_status($domain_name);
	print <<<EOF
	<br>

	<table align="center" width="95%">
	<tr align=center>
	<td colspan="2" title="$MSG_REVENDA2"><u>$MSG_INI</u>
: $STATUS
EOF;
	if($cli_revenda_dominio)print " -- $MSG_REVENDA";
	print<<<EOF
	</td>
EOF;

		$selected_suspend=array();
		$selected_suspend[$suspend]="selected";

		$selected_spf=array();
		$selected_spf[$use_spf]="selected";

		$selected_dontsync=array();
		#$selected_dontsync[$dontsync]="selected";
		$selected_dontsync[0]="selected";

		$selected_envia_email=array();
		#$selected_envia_email[$envia_email]="selected";
		$selected_envia_email[0]="selected";

		if(strlen($username) > 3){ $USERNAME="Username"; }
		else { $USERNAME="Digite username"; }

		print <<<EOF
		<tr><td>
		$USERNAME para <br>administração do domínio (www):
EOF;
		#if($www == "wbw.wb.com.br" or $smtp == "wbw.wb.com.br")
		#{
		 print "<br>Se for no plesk é: <b>$domain_name</b>";
		#}
		print <<<EOF
		</td><td>
		<input name="username" value="$username" size="$SIZE">
EOF;
		########## begin admin
		if(true)	##$groupo_contabil == 'admin')
		{
		print <<<EOF
		 Sist. Op. (www): 
EOF;
		##print "sistema_op: $sistema_op";
		#Sistem op. de um 'usuario' final: TEM que coincidir
		# com o sistema operacional de uma revenda
		##show_val_sel_index("sistema_op",$SISTEMA_OP,$sistema_op);
 		show_val_sel_index2nome("sistema_op",$REVENDA_TIPO2,$sistema_op,"$www");
		##show_sel_list("sistema_op",$SISTEMA_OP,$sistema_op,0);

		#Mostre as revendas para escolher
		#mysql> select username from PACKAGES where revenda like
		#'%revenda%linux%';

		##if($sistema_op > 0)	#strlen($sistema_op) > 3)
		##{
		  #seleciona_revendedor($revendedor,$sistema_op,$www,$smtp);
		  seleciona_revendedor("revendedor",$revendedor,$sistema_op,$www);
		  ##print " Revendedor: ";
		  #show_val_sel_index2nome("revenda",$REVENDA_TIPO2,$revenda_pac);
		##}


		}
		######## End admin

		if(strlen($password) > 3)
		{ $SENHA="Digite nova senha de acesso"; }
		else { $SENHA="Digite a senha de acesso"; }
		print <<<EOF
		<TR class="bgcol2susp"> <td>
		Administração de domínio (www):<br>$SENHA
		<br>previamente cadastrada no cpanel
		</td><td>
		<input name="password_new" value="" size="$SIZE"
		disabled="$dis_pass_www">
		<input type="hidden" name="password" value="$password">
EOF;
		#document.form1.password.disabled=false;
		print <<<EOF
<script language="javascript">
function enableField()
{
document.this.form.password_new.disabled=false;
}
</script>
<input type=radio name="senha" value="habilita"
onclick="javascript:document.pacote.password_new.disabled=false">
Click aqui para digitar senha
EOF;
#<a href="javascript:enableField()">Click aqui para mudar senha<a/>
#EOF;
		print "</td></tr>";
		$domain_name_ex="dominio.com.br";
		if($domain_name){
		$domain_name_ex=$domain_name;
		} else { $domain_name_ex="dominio.com.br";}

		$smtp_ip="200.1.1.1"; $www_ip="200.1.1.1";
		if($smtp){
		$smtp_ip= gethostbyname(trim($smtp));;
		} else { $smtp_ip="200.1.1.1"; }
		if($www){
		$www_ip= gethostbyname(trim($www));;
		} else { $www_ip="200.1.1.1"; }

                print <<<EOF
		<TR BGCOLOR="$BACKG_TR2"><TD>Domain Name</TD>
		<TD>
		<INPUT TYPE="text"  NAME="domain_name"
			value="$domain_name"
			 size="$SIZE">
EOF;
		#if($smtp == "bet.wb.com.br")
		#if(preg_match("/bet.wb.com.br|wbhost.wb.com.br|vetor.wb.com.br/",$smtp))
		# mostra acesso ao cpanel e email para receber senha
		req_cpanel2($packid,$smtp,"dominio/www",
			$username,$password,$sistema_op);
		##req_cpanel($packid,$www,"dominio",$username);

		print <<<EOF
		</td></tr>
		<tr><td>
		Suspende domínio:</td><td>
		<select name="suspend">
		<option value="0" $selected_suspend[0]>Não</option>
		<option value="1" $selected_suspend[1]>Sim</option>
		</select>

		Atualiza: 
		<select name="dontsync">
		<option value="0" $selected_dontsync[0] >Sim</option>
		<option value="1" $selected_dontsync[1] >Não</option>
		</select>

		Notifica cliente: 
		<select name="envia_email">
		<option value="0" $selected_envia_email[0] >Não</option>
		<option value="1" $selected_envia_email[1] >Sim</option>
		</select>

		<!--
		Suspend no CPANEL?
		<input type="checkbox" name="suspenddominio"
		value="sim" editable onChange="this.form.submit();">
		    ==> suspenddominio: $suspenddominio <<==
		-->

		</TD>
		</TR>

		<!-- TR BGCOLOR="$BACKG_TR2"><TD>Serial Number</TD><TD>
			$serial_number</TD>
		</TR-->
		<tr><INPUT TYPE="hidden" NAME="serial_number"
			VALUE="$serial_number">
		</tr>

		<!-- TR BGCOLOR="$BACKG_TR2"><TD>DNS Master Server</TD><TD>
			<INPUT TYPE="text" NAME="master"
			VALUE="$master" size="$SIZE"></TD>
		</TR-->
EOF;
		$ERRO_DNS=""; $ERRO_DNS1="";
		$ns_record=trim($ns_record);
		$ip_nsrecord=gethostbyname($ns_record);
		if($ip_nsrecord == $ns_record)$ERRO_DNS="<blink>ERRO</blink>";
		$secondary1=trim($secondary1);
		$ip_nsrecord1=gethostbyname($secondary1);
		if($ip_nsrecord1 == $secondary1)$ERRO_DNS1=$ERRO_DECL;

		print <<<EOF
		<TR BGCOLOR="$BACKG_TR2"><TD>DNS Master Server</TD><TD>
			<INPUT TYPE="text" NAME="ns_record"
			VALUE="$ns_record" size="$SIZE"> $ERRO_DNS
			(Def. $NS_RECORD1)</TD>
		</TR>

		<TR BGCOLOR="$BACKG_TR2"><TD>DNS Secondary Server</TD><TD>
			<INPUT TYPE="text" NAME="secondary1"
			VALUE="$secondary1"  size="$SIZE"> $ERRO_DNS1
			(Def. $NS_RECORD2)</TD></TR>

		<!--		
		<TR BGCOLOR="$BACKG_TR2"><TD>Secondary Server 2</TD><TD>
			<INPUT TYPE="text" NAME="secondary2"
			VALUE="$secondary2"  size="$SIZE"></TD></TR>
		<TR BGCOLOR="$BACKG_TR2"><TD>Secondary Server 3</TD><TD>
			<INPUT TYPE="text" NAME="secondary3"
			VALUE="$secondary3"  size="$SIZE"></TD></TR>
		-->

		<TR class="bgcol2susp"><TD>Mail Exchange (MX)
		<br>Default: $MX_DEFAULT<br>
		<!-- Para aceitar SPAM, use: mxspam.wb.com.br
		<br--> 'A CNAME' nem 'IP' podem aparecer no MX record.
		<br> Colocar um 'nome de maquina' que<br> resolva dentro do dominio 'wb.com.br'.
		</TD><TD>
			<INPUT TYPE="text" NAME="mx_record" 
			VALUE="$mx_record"  size="$SIZE">
			($MX_DEFAULT <!-- /70.85.230.98 -->)
EOF;
                #Teste se ip/mx valido
		$mx_teste=trim($mx_record);
		$ip_mx=gethostbyname($mx_record);
		if($ip_mx == $mx_record){ print "*** ERROR NO MX $ip_mx/$mx_record ***"; }
		#else { print "$ip_mx/$mx_record"; }
		
                print <<<EOF
                </TD></TR>

		<TR BGCOLOR="$BACKG_TR2">
		<TD>Servidores adicionais de e-mail<br> para SPF. Ex:<br>
		a:proxy1.idemp.com.br  a:proxy2.idemp.com.br<br>
		ip4:200.162.64.10
		</TD><TD>
			<INPUT TYPE="text" NAME="spf_record" 
			VALUE="$spf_record"  size="$SIZE"  maxlength="50" >

		Usa SPF: 
		<select name="use_spf">
		<option value="0" $selected_spf[0] >Não</option>
		<option value="1" $selected_spf[1] >Sim</option>
		</select>

		</TD></TR>


		<!--
		<TR BGCOLOR="$BACKG_TR2"><TD>NS Record 1</TD><TD>
			<INPUT TYPE="text" NAME="ns_record" 
			VALUE="$ns_test_com"  size="$SIZE"></TD></TR>
		<TR BGCOLOR="$BACKG_TR2"><TD>NS Record 2</TD><TD>
			<INPUT TYPE="text" NAME="dns2"
			VALUE="$ns_test_com2"  size="$SIZE"></TD></TR>
		-->


		<TR BGCOLOR="$BACKG_TR2"><TD>WWW Record</TD><TD>
			<INPUT TYPE="text" NAME="www" 
			VALUE="$www"  size="$SIZE">
EOF;
		req_cpanel2($packid,$www,"www",$username,$password,$sistema_op);
		req_cpanel_escolha($packid,$www,"www",$username);

		print <<<EOF
			<br>($WWW_SERVER)
EOF;
		##req_cpanel($packid,$www,"www",$username);

		print <<<EOF
		</TD></TR>
		<TR BGCOLOR="$BACKG_TR2"><TD>FTP Record</TD><TD>
			<INPUT TYPE="text" NAME="ftp" 
			VALUE="$ftp"  size="$SIZE"> 
			<br>($FTP_SERVER)</TD></TR>
EOF;
		$smtp=trim($smtp); $ERRO_SMTP="";
		$ip_smtp=gethostbyname($smtp);
		if($ip_smtp == $smtp)$ERRO_SMTP=$ERRO_DECL;

		$pop=trim($pop); $ERRO_POP="";
		$ip_pop=gethostbyname($pop);
		if($ip_pop == $pop)$ERRO_SMTP=$ERRO_DECL;


		#Teste se $smtp != $www
		if($smtp != $www)
		{
		print <<<EOF
		<TR BGCOLOR="$BACKG_TR2"><TD>Username adm. Email</TD><TD>
			<INPUT TYPE="text" NAME="username_mail" 
			VALUE="$username_mail"  size="$SIZE">
		</TD></TR>
		<TR BGCOLOR="$BACKG_TR2"><TD>Senha adm. Email</TD><TD>
			<INPUT TYPE="hidden" NAME="password_mail" 
			VALUE="$password_mail"  size="$SIZE"
			>
			<INPUT TYPE="text" NAME="password_mail_new" 
			VALUE="$password_mail_new"  size="$SIZE"
			disabled="$dis_pass_mail">
EOF;
		print <<<EOF
<script language="javascript">
function enableField_email()
{
document.this.form.password_mail_new.disabled=false;
}
</script>
<input type=radio name="senha_email" value="habilita"
onclick="javascript:document.pacote.password_mail_new.disabled=false">
Click aqui para digitar senha
EOF;

		#Escolha sistema_op_pop e revendedor_pop

		print <<<EOF
		</TD></TR>
EOF;
		}
		print <<<EOF
		<TR BGCOLOR="$BACKG_TR2"><TD>SMTP Record</TD>
		<TD>
			<INPUT TYPE="text" NAME="smtp" 
			VALUE="$smtp"  size="$SIZE"> $ERRO_SMTP

EOF;
		print "Sist. Op. (smtp/pop): ";
		#Sistem op. de um 'usuario' final: TEM que coincidir
		# com o sistema operacional de uma revenda
		##show_val_sel_index("sistema_op_pop",$SISTEMA_OP,$sistema_op_pop);
		show_val_sel_index2nome("sistema_op_pop",$REVENDA_TIPO2,$sistema_op_pop,"$pop");

		###########
		seleciona_revendedor("revendedor_pop",$revendedor_pop,$sistema_op_pop,$smtp);

		##req_cpanel($packid,$smtp,"smtp",$username);
		##req_cpanel_escolha($packid,$smtp,"smtp",$username);

		print <<<EOF
			<br>($SMTP_SERVER)
EOF;

		######req_cpanel($packid,$smtp,"smtp",$username);
		##Escolha sistema_op_pop e revendedor_pop

		print <<<EOF
		</TD></TR>

		<TR BGCOLOR="$BACKG_TR2"><TD>POP Record</TD><TD>
			<INPUT TYPE="text" NAME="pop" 
			VALUE="$pop"  size="$SIZE">  $ERRO_POP
EOF;
		req_cpanel2($packid,$smtp,"smtp",$username_mail,$password_mail,$sistema_op_pop);
		req_cpanel_escolha($packid,$smtp,"smtp",$username_mail);

		print <<<EOF
			<br>($POP_SERVER)</TD></TR>
		<TR BGCOLOR="$BACKG_TR2">
		<td>Volume de tráfego (banda)/dia</td>
		<td>
EOF;

#		print <<<EOF
#		<INPUT TYPE="text" NAME="banda_vol"" 
#		VALUE="$banda_vol"  size="$SIZE">
#EOF;
		##print "--->$banda_vol<--<br>";
		print "<select name='banda_vol'> ";
		while ($type = current($banda_vol_def)) 
		{
		   print "<option";
		   if ($type == $banda_vol)echo " selected ";
		   print "> $type";
		   next($banda_vol_def);
		}
		print " </select>";

		print <<<EOF
		/ dia.</TD></TR>

		<TR BGCOLOR="$BACKG_TR2">
		<td align="center" colspan=2><u>
		Sub domínios, A Record, CNAME Record, etc.</u></td>
		</TR>
		<TR BGCOLOR="$BACKG_TR2">
		<TD  >
		<!-- A e CNAME Record<br> -->
		S&oacute; altere os dados se souber o que est&aacute; fazendo!
		<br>
		<u>Ex</u>.:<br> www2 CNAME  hp.wb.com.br
		<!--
		<br>orion  IN A 200.162.64.10
		-->
		<br>webmail in cname webmail.wb.com.br
                <br>domain in cname $DOMAIN_ADM2
		<br>mysql in cname mysql.wb.com.br
		<br>\$INCLUDE /var/named/wb.com.br.inc

		<br><u>ERRADO</u> (não é IP):<br>
		@  in cname wbhost.com.br
		<!-- (Não é IP) -->
		<br><u>CERTO</u> (é IP):<br>
		@  in a 70.85.230.98
		<!-- -->
		<br><u>Recomendável</u>:<br>
		SMTP: dominio.com.br in a $smtp_ip<br>
		WWW: dominio.com.br in a $www_ip
		<br>Ex.:<br>
		$domain_name_ex. in a $smtp_ip<br>
		webmail.$domain_name_ex. in a $smtp_ip
		<!-- -->
		<br><u>Wild card</u>:<br>
		* in a $smtp_ip
		<br>Só usar se tudo estiver na mesma maquina!
		</TD>
		<TD  >
			<TEXTAREA cols="50" rows="10" NAME="cname_record" 
			value="$cname_record" 
			onkeyup="TrackCount(this,'textcount',$MAX_TEXT_AREA)" 
			onkeypress="LimitText(this,$MAX_TEXT_AREA)"
			>$cname_record</TEXTAREA>
		<br>
		Max. caracteres restantes acima:
		<input type="text" name="textcount" size="4" 
		value="$MAX_TEXT_AREA"> de $MAX_TEXT_AREA.
		</TD></TR>

	</TABLE>
<a href="cpanel/dominios_plesk.txt">
Ex. de cadastramento de dominio no PLESK
</a><br><br>
EOF;

	return(0);

}
############################################
function isIPIn($ip,$net,$mask) {
   $lnet=ip2long($net);
   $lip=ip2long($ip);
   $binnet=str_pad( decbin($lnet),32,"0","STR_PAD_LEFT" );
   $firstpart=substr($binnet,0,$mask);
   $binip=str_pad( decbin($lip),32,"0","STR_PAD_LEFT" );
   $firstip=substr($binip,0,$mask);
   return(strcmp($firstpart,$firstip)==0);
}

function test_ip_allow($myip)
{
   #print "xxxxxxxxx"; return(1);
   global  $FROM_NET, $ALLOWED_IPS2;
   $FROM_NET=""; $FROM_NET_ALLOW=false;
   foreach ($ALLOWED_IPS2 as $k=>$v)
   {
	list($net,$mask)=split("/",$k);
	if(!$net or !$mask){ 
	 print "<br><h3>Erro em config_all: $k, $v</h3><br>"; 
	 exit(); 
	}
	if (isIPIn($myip,$net,$mask)) {
	  $FROM_NET_ALLOW=true;
       	  #echo $n[$k]."<br />\n"; 
	  $FROM_NET .= "$ALLOWED_IPS2[$k] ";
	}
   }
   return($FROM_NET_ALLOW);
}

function login_logout()
{
        #return();
	global $HTTP_VIA,$REMOTE_ADDR,$HTTP_X_FORWARDED_FOR,$host_ip,
	$USER_HOST_IP, $USER_HOST_NAME;
 	#? $host_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	#print "HTTP_VIA = $HTTP_VIA <br>"; #exit();
	if(false)	#$HTTP_VIA) 
	{
	$host=gethostbyaddr($HTTP_X_FORWARDED_FOR);
	$host_ip=$HTTP_X_FORWARDED_FOR;
	#print " host_ip = $HTTP_X_FORWARDED_FOR<br>";
	}
	else {
	#print " host_ip = $REMOTE_ADDR<br>"; exit();
	$host = gethostbyaddr($REMOTE_ADDR);
	$host_ip = $REMOTE_ADDR;
	#print " host_ip = $REMOTE_ADDR<br>"; exit();
	}
	$USER_HOST_NAME=$host;
	$USER_HOST_IP=$host_ip;
	#$host_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	#print "<br>--- $host_ip <br>"; exit();
	#if(false)	#! test_ip_allow($host_ip))	 # $FROM_NET
	if(! test_ip_allow($host_ip))	 # $FROM_NET
	{
	 print "1. IP: $host_ip / $FROM_NET : banned <br>"; 
	 exit();
	}
}

function conexao_nr($login,$begin_timed_pac,$final_timed_pac)
{
  global $db_link;
  $login=trim($login); if(empty($login))return(0);
  ##include_once("dbinfo.php");
  include("dbinfo.php");
  
  $begin_time_acc=date("Y-m-d H:i:s",$begin_timed_pac);
  $final_time_acc=date("Y-m-d H:i:s",$final_timed_pac);
  #2004-11-17 00:06:33 
  $query_minutagem_nr="SELECT count(*) FROM radacct
  where UserName = '$login' 
  AND AcctStartTime >= '$begin_time_acc'  
  AND AcctStopTime < '$final_time_acc'";
  # Nao existe em radacct : AND servcode ='$SERVICE_DIALUP' ";
  #Nr. de conexoes:
  $result_minutagem=mysql_query($query_minutagem_nr,$db_link);
  if($result_minutagem)
  {
	  $pack_minutagem_nr=mysql_fetch_row($result_minutagem);
	  $nr_conexoes=$pack_minutagem_nr[0];
  } else
  {
	  $nr_conexoes=0;
  }
  return($nr_conexoes);
}

function conexao_nr_comd($login,$begin_timed_pac,$final_timed_pac)
{
  global $look_comd;
  if(! $look_comd)return(0);
  $login=trim($login); if(empty($login))return(0);
  include_once("dbinfo_comd.php");
  $begin_time_acc=date("Y-m-d H:i:s",$begin_timed_pac);
  $final_time_acc=date("Y-m-d H:i:s",$final_timed_pac);
  #2004-11-17 00:06:33 
  $query_minutagem_nr="SELECT count(*) FROM radacct
  where UserName = '$login' 
  AND AcctStartTime >= '$begin_time_acc'  
  AND AcctStopTime < '$final_time_acc'";
  # Nao existe em radacct : AND servcode ='$SERVICE_DIALUP' ";
  #Nr. de conexoes:
  $result_minutagem=mysql_query($query_minutagem_nr,$db_link_COMD);
  if($result_minutagem)
  {
	  $pack_minutagem_nr=mysql_fetch_row($result_minutagem);
	  $nr_conexoes=$pack_minutagem_nr[0];
  } else
  {
	  $nr_conexoes=0;
  }
  return($nr_conexoes);
}

function conexao_time($login,$begin_timed_pac,$final_timed_pac)
{
  global $db_link;
  $login=trim($login); if(empty($login))return(0);
  #AcctSessionTime
  #include_once("dbinfo.php");
  include("dbinfo.php");
  
  $begin_time_acc=date("Y-m-d H:i:s",$begin_timed_pac);
  $final_time_acc=date("Y-m-d H:i:s",$final_timed_pac);
  #2004-11-17 00:06:33 
  $query_minutagem_time="SELECT sum(AcctSessionTime) FROM radacct
  where UserName = '$login' 
  AND AcctStartTime >= '$begin_time_acc'  
  AND AcctStopTime < '$final_time_acc'";
  # Nao existe em radacct : AND servcode ='$SERVICE_DIALUP' ";
  #Nr. de conexoes:
  $result_minutagem_time=mysql_query($query_minutagem_time,$db_link);
  if($result_minutagem_time)
  {
	  $pack_minutagem_time=mysql_fetch_row($result_minutagem_time);
	  $conexoes_time=$pack_minutagem_time[0];
  } else
  {
	  $conexoes_time=0;
  }
  return($conexoes_time);
}

function conexao_time_comd($login,$begin_timed_pac,$final_timed_pac)
{
  global $look_comd;
  if(! $look_comd)return(0);
  $login=trim($login); if(empty($login))return(0);

  #AcctSessionTime
  include_once("dbinfo_comd.php");
  $begin_time_acc=date("Y-m-d H:i:s",$begin_timed_pac);
  $final_time_acc=date("Y-m-d H:i:s",$final_timed_pac);
  #2004-11-17 00:06:33 
  $query_minutagem_time="SELECT sum(AcctSessionTime) FROM radacct
  where UserName = '$login' 
  AND AcctStartTime >= '$begin_time_acc'  
  AND AcctStopTime < '$final_time_acc'";
  # Nao existe em radacct : AND servcode ='$SERVICE_DIALUP' ";
  #Nr. de conexoes:
  ##print "$query_minutagem_time , $begin_timed_pac,$final_timed_pac";
  $result_minutagem_time=mysql_query($query_minutagem_time,$db_link_COMD);
  if($result_minutagem_time)
  {
	  $pack_minutagem_time=mysql_fetch_row($result_minutagem_time);
	  $conexoes_time=$pack_minutagem_time[0];
  } else
  {
	  $conexoes_time=0;
  }
  return($conexoes_time);
}

function updt_radreply($username,$attrib,$op,$valor)
{
 #include_once("dbinfo.php");
 include("dbinfo.php");
 
 global $db_link;
 #| id | UserName               | Attribute           | op | Value  |
 #+----+------------------------+---------------------+----+--------+
 #|  2 | pedro@pppoe.wb.com.br  | Configuration-Token | := | 440440 |
 #if($servtype != $SERVICE_PPPOE ) { return; }
 $username=trim($username); $attrib=trim($attrib); $op=trim($op);
 $valor=trim($valor);
 if(empty($username) or empty($attrib) or empty($op) )	###or empty($valor) )
 {
  #print "0. Packages Updated $username , ($attrib) , $op, $valor . <br>";
  return(1);
 }
 $query_radreply="DELETE FROM radreply where UserName='$username'
	and Attribute='$attrib'";
 #if(!mysql_query($query_radreply,$db_link))
 mysql_query($query_radreply,$db_link);
 #print "1. Packages Updated $query_radreply <br>";
 if(mysql_error($db_link))
 {
   print("<CENTER>1. $Package_Changes <b>$not</b> $posted");
   print "<br>\nMysql error:" . mysql_errno($db_link) . " -- ";
   print mysql_error($db_link) . "<br> $query_radreply";
   print "</center>";
   #print "4. Packages  NOT Updated.";
   exit();
 }
 if(empty($valor) )
 {
  #print "0. Packages Updated $username , ($attrib) , $op, $valor . <br>";
  return(1);
 }
 #INSERT INTO radreply VALUES ('','user5@pppoe.wb.com.br','Configuration-Token',':=', '200220');
 #INSERT INTO radreply VALUES ('','user5@pppoe.wb.com.br','Connect-Info',':=', '512');
 $query_radreply="INSERT INTO radreply VALUES 
	('','$username','$attrib','$op', '$valor')";
 #print "query_radreply: $query_radreply <br>";
 mysql_query($query_radreply,$db_link);
 #if(!mysql_query($query_radreply,$db_link))
 #print "1. Packages Updated $query_radreply <br>";
 if(mysql_error($db_link))
 {
   print("<CENTER>1. $Package_Changes <b>$not</b> $posted");
   print "<br>\nMysql error:" . mysql_errno($db_link) . " -- ";
   print mysql_error($db_link) . "<br> $query_radreply";
   print "</center>";
   #print "4. Packages  NOT Updated.";
   exit();
 }
 #print "4. Packages Updated. <br>";
}
##################################
#http://www.finalwebsites.com/snippets.php?id=27
function check_email($mail_address) {
    if(preg_match("/,/",$mail_address)) {
      return(true);
    }
    $pattern = "/^[\w-]+(\.[\w-]+)*@";
    $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
    if (preg_match($pattern, $mail_address)) {
        $parts = explode("@", $mail_address);
        if (checkdnsrr($parts[1], "MX")){
            #echo "The e-mail address is valid.";
            return true;
        } else {
            #echo "The e-mail host is not valid.";
            return false;
        }
    } else {
        #echo "The e-mail address contains invalid charcters.";
        return false;
    }
}
#check_email("INFO@google.co.uk");
#http://www.php.net/manual/en/function.checkdnsrr.php
function validate_email0($email){
   #Verifica syntaxe do username/email (lskfj@email.com, por ex.)
   $exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";
   if(eregi($exp,$email)){
    return(true);
   }
   return(false);
}
######################
function validate_email($email){
   #Verifica syntaxe do email (lskfj@email.com, por ex.) e se existe MX.
   if(preg_match("/,/",$email)) {
      return(true);
   }
   $exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";
   if(eregi($exp,$email)){
     if(checkdnsrr(array_pop(explode("@",$email)),"MX")){
       return true;
     }else{
       return false;
     }
   }else{
     return false;
   }   
}
########################################
function check_ip_def($ip_address,$crt_packid)
{
 $agora=time();
 ## Examina os ips de clients e ve clients suspensos ate 12 meses!
 $meses_suspenso=12;
 $client_suspenso=$meses_suspenso * 30;	#12 meses
 $now_time_3m=time() - 24*3600*$client_suspenso;		#Cliente suspenso há mais de 90 dias
 #print "ip_address: $ip_address <br>";
 if(! test_ip($ip_address)) return(false);	#Se não e' um IP retorne falso.
 #print "ip_address: $ip_address <br>";
 #PACKAGES.servername='$wearehere'
 #and  PACKAGES.start < '$now'
 $query_ip_nr="SELECT count(*) from PACKAGES
        where
        PACKAGES.active='1' and 
	(PACKAGES.suspend = '1' and
	 PACKAGES.suspendedfrom > '0' and PACKAGES.suspendedfrom < '$now_time_3m')
        AND
        (PACKAGES.end='0' OR PACKAGES.end > '$agora') 
	and PACKAGES.ip_nr='$ip_address' and PACKAGES.packid != '$crt_packid'";
 #and  PACKAGES.start < '$agora' 
 $query_ip_nr2="SELECT * from PACKAGES
        where
        PACKAGES.active='1' and
	(PACKAGES.suspend = '1' and
	 PACKAGES.suspendedfrom > '0' and PACKAGES.suspendedfrom < '$now_time_3m')
        AND
        (PACKAGES.end='0' OR PACKAGES.end > '$agora') 
	and PACKAGES.ip_nr='$ip_address' and PACKAGES.packid != '$crt_packid'";
 $query_ip_nr3="SELECT * from PACKAGES where 
	active='1'
	and ip_nr='$ip_address'
	and packid != '$crt_packid'";
 #include("dbinfo.php");
 global $db_link;
 #$result_cli=mysql_query($query_ip_nr,$db_link);
 #$result_cli_nr=mysql_fetch_row($result_cli);

 $result_cli3=mysql_query($query_ip_nr3,$db_link);
 $num_rows = mysql_num_rows($result_cli3);
 #if($result_cli_nr[0] == 0)
 if($num_rows == 0)
 {
   #print "result_cli false : $result_cli <br>"; exit(0);
   return(false);
 }
 #print "result_cli true : $result_cli <br>"; exit(0);
 $result_cli_nr2=mysql_fetch_array($result_cli3);
 return($result_cli_nr2[clientid]);
}

function test_ip($ip_address)
{
 # Retorno se $ip_address é um IP  valido
 if(empty($ip_address))return(false);
 $long = ip2long($ip_address);
 if ($long == -1 || $long === FALSE) {
   return(false);
 }
 return(true);

 #if(! empty($ip_address))
 #{
   $ip_address=ereg_replace(" ",'',$ip_address);
   #preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
   #echo "domain name is: {$matches[0]}\n";
   #$element2=preg_split("/(\d+\.\d+\.\d+\.\d+)/",$ip_address,-1,
   #	PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
   preg_match("/^(\d+\.\d+\.\d+\.\d+)$/",$ip_address,$element2);
   if(isset($element2[0]) and strlen($element2[0]) > 0
	   #isset($element2[2]) and strlen($element2[2]) > 0 and
	   #isset($element2[3]) and strlen($element2[3]) > 0 and
	   #isset($element2[4]) and strlen($element2[4]) > 0 
     )
     {
	#print "element: " . $element2[0] . " elem2: " . $element2[1] . "<br>" ; exit();
	return(true);
	##IP OK!
     }
     else 
     {
	  #print "element: " . $element2[0] . " elem2: " . $element2[1] . "<br>" ; exit();
	  $ip_address .="--IP_ERRADO";
	  return(false);
     }
 #}
 #return(true);
}
##################
function check_username_def($username,$crt_packid)
{
 # Testa se username existe em outro pacote. Se existe, retorne clientid.
 global $db_link;
 $username=trim($username);
 if(strlen($username)<3)return(false);
 $query_ip_nr3="SELECT * from PACKAGES where 
	active='1'
	and username='$username'
	and packid != '$crt_packid'";
 $result_cli3=mysql_query($query_ip_nr3,$db_link);
 $num_rows = mysql_num_rows($result_cli3);
 if($num_rows == 0)
 {
   #print "result_cli false : $result_cli <br>"; exit(0);
   return(false);
 }
 #print "result_cli true : $result_cli <br>"; exit(0);
 $result_cli_nr3=mysql_fetch_array($result_cli3);
 return($result_cli_nr3[clientid]);
}

##################
function get_servicename($servcode){
  #include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link;
  $query_serv="SELECT servicename from SERVICES where serviceid='$servcode'";
  $result=mysql_query($query_serv,$db_link);
  if(mysql_error($db_link) or !$result){
    print("*** $query_cl *** $result ***<br>");
    print "\nMysql error:" . mysql_errno($db_link)
    . " : "  . mysql_error($db_link) . "<br>";
    return("??");
  }
  $row=mysql_fetch_array($result);
  return("$row[servicename]");
}

function get_rede_predio($id_predio)
{
global $linha_edita,$conexao;
$linha_edita=array();
$linha_edita['nome_edificio']=""; $linha_edita['redeauth']="";
$linha_edita['id_node']=""; $linha_edita['rede']=""; $linha_edita['redeip']="";
$linha_edita['redemask']=""; $linha_edita['redegw']=""; $linha_edita['edif_node']=0;
if($id_predio < 1)return(0);

include_once ("includes/inc_conexao.php");
$consulta = "SELECT * from adodbedificio where id_predio = '$id_predio' ";
$resultado = mysql_query($consulta,$conexao);
####$num_rows = mysql_num_rows($resultado);
if(mysql_errno($conexao) or ! $resultado)
{
	print("*** $consulta ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita=mysql_fetch_array($resultado,MYSQL_BOTH);
$linha_edita['id_node']=0;
#print "id_predio: $id_predio, edif_node: $linha_edita[edif_node] <br>"; exit(0);

if(0)		#$linha_edita['edif_node'] > 0)
{
 #print "edif_node=$linha_edita[edif_node] <br>";
 $id_node_ips=$linha_edita['edif_node'];
 #$consulta = "SELECT * from node,node_ips where id_node_ips='$id_node_ips' ";
 $consulta = "SELECT * from node,node_ips where 
 	id_node_ips='$id_node_ips' and id_node=id_node_ips ";
 $resultado = mysql_query($consulta,$conexao);
 ####$num_rows = mysql_num_rows($resultado);
 if(mysql_errno($conexao) or ! $resultado)
 {
	print("*** $consulta ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
 }
 $nome_edificio=$linha_edita['nome_edificio'];
 $redeauth=$linha_edita['redeauth'];
 $linha_edita=array();
 $linha_edita['nome_edificio']=$nome_edificio;
 $linha_edita['redeauth']=$redeauth;
 #
 $linha_edita['id_node']=$id_node_ips;
 #$linha_edita['rede']=get_node_name($id_node_ips);
 ##$linha_edita['rede']=$linha_edita2['node_ip'];
 #
 $linha_edita['rede']=$id_node_ips;
 $linha_edita['redeip']="";
 $linha_edita['redemask']=""; $linha_edita['redegw']="";
 $not_empty = "";
 while($linha_edita2=mysql_fetch_array($resultado,MYSQL_BOTH))
 {
  $linha_edita['rede']=$linha_edita2['node_nome'];
  #print "$linha_edita2[node_ip], $linha_edita2[node_mascara],
  #	$linha_edita2[node_gw]<br>";
  #print "linha_edita_redeip: $linha_edita[redeip] <br>";
  if(!empty($linha_edita['redeip']) ){ $not_empty = ", ";
  }
  $linha_edita['redeip'] .= $not_empty .  $linha_edita2['node_ip'];
  $linha_edita['redemask'] .= $not_empty . "255.255.255." . $linha_edita2['node_mascara'];
  $linha_edita['redegw'] .= $not_empty . $linha_edita2['node_gw'];
 }
}
#print "redeip: $linha_edita[redeip], redemask:$linha_edita[redemask],
#redegw: $linha_edita[redegw]<br>";
return(0);
#return($linha_edita);
#['rede']);
#,$linha_edita['redeip'],
#  $linha_edita['redegw'],$linha_edita['redemask'],$linha_edita['redeauth']);

}
#########

function get_rede_node($id_node)
{
global $linha_edita_node,$destino_cliente, $destino_router, $conexao;
$linha_edita_node=array();
$linha_edita_node['nome_edificio']=""; $linha_edita_node['redeauth']="";
$linha_edita_node['id_node']=""; $linha_edita_node['rede']=""; $linha_edita_node['redeip']="";
$linha_edita_node['redemask']=""; $linha_edita_node['redegw']=""; $linha_edita_node['edif_node']=0;
if($id_node < 1)return(0);

include_once ("includes/inc_conexao.php");
$id_node_ips=$id_node;
$consulta = "SELECT * from node,node_ips where 
	id_node_ips='$id_node_ips' and id_node=id_node_ips and
	( ips_destino='$destino_cliente' or ips_destino='$destino_router') 
	and node_active='1' ";
$resultado = mysql_query($consulta,$conexao);
####$num_rows = mysql_num_rows($resultado);
if(mysql_errno($conexao) or ! $resultado)
{
	print("*** $consulta ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$nome_edificio=$linha_edita_node['nome_edificio'];
$redeauth=$linha_edita_node['redeauth'];
$linha_edita_node=array();
$linha_edita_node['nome_edificio']=$nome_edificio;
$linha_edita_node['redeauth']=$redeauth;
#
$linha_edita_node['id_node']=$id_node_ips;
#$linha_edita_node['rede']=get_node_name($id_node_ips);
##$linha_edita_node['rede']=$linha_edita_node2['node_ip'];
#
$linha_edita_node['rede']=$id_node_ips;
$linha_edita_node['redeip']="";
$linha_edita_node['redemask']=""; $linha_edita_node['redegw']="";
$not_empty0 = ""; $begin_mask0="255.255.255."; $begin_mask="";
while($linha_edita_node2=mysql_fetch_array($resultado,MYSQL_BOTH))
{
 $linha_edita_node['rede']=$linha_edita_node2['node_nome'];
 #print "$linha_edita_node2[node_ip], $linha_edita_node2[node_mascara],
 #	$linha_edita_node2[node_gw]<br>";
 #print "linha_edita_node_redeip: $linha_edita_node[redeip] <br>";
 if(!empty($linha_edita_node['redeip']) ){ $not_empty = ", ";
 } else { $not_empty = ""; }
 if(strlen($linha_edita_node2['node_mascara']) < 4)$begin_mask=$begin_mask0;
 $linha_edita_node['redeip'] .= $not_empty .  $linha_edita_node2['node_ip'];
 $linha_edita_node['redemask'] .= $not_empty . $begin_mask . $linha_edita_node2['node_mascara'];
 $linha_edita_node['redegw'] .= $not_empty . $linha_edita_node2['node_gw'];
}

#print "redeip: $linha_edita_node[redeip] 
#redemask: $linha_edita_node[redemask] redegw: $linha_edita_node[redegw]<br>";
return(0);

}

#########
function get_parent_name($node_nr)
{
global $conexao;
$consulta = "SELECT node_nome from node where id_node = '$node_nr' 
  AND node_active='1' ";
$resultado = mysql_query($consulta,$conexao);
####$num_rows = mysql_num_rows($resultado);
if(mysql_errno($conexao) or ! $resultado)
{
	print("*** $consulta ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita=mysql_fetch_array($resultado,MYSQL_BOTH);
return($linha_edita['node_nome']);

}

#########
function get_id_node_name()	#$node_nr_nome)
{
global $conexao,$node_nr_nome;
include_once ("includes/inc_conexao.php");

$consulta_node = "SELECT id_node,node_nome from node 
  ORDER by node_nome  ";
  #where  node_active='1'
$resultado_node = mysql_query($consulta_node,$conexao);
//echo mysql_errno($conexao).": ".mysql_error($conexao)."<br>";
if(mysql_errno($conexao) )
{
	print("*** $consulta_node ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}

#$node_nr_nome=array();  
$node_nr_nome[0]="--";
while ($linha_edita_node = mysql_fetch_array($resultado_node,MYSQL_BOTH))
{
 #print_r($linha_edita_node);
 $node_nr_nome[$linha_edita_node['id_node']]=$linha_edita_node['node_nome'];
}

return(0);	#$node_nr_nome);

}
########################
function get_id_node_name2($node_type)	#$node_nr_nome)
{
global $conexao,$node_nr_nome;
include_once ("includes/inc_conexao.php");

$consulta_node = "SELECT id_node,node_nome from node 
	where node_type = '$node_type' AND node_active='1'
	ORDER by node_nome ";
$resultado_node = mysql_query($consulta_node,$conexao);
//echo mysql_errno($conexao).": ".mysql_error($conexao)."<br>";
if(mysql_errno($conexao) )
{
	print("*** $consulta_node ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}

#$node_nr_nome=array();  
$node_nr_nome[0]="--";
while ($linha_edita_node = mysql_fetch_array($resultado_node,MYSQL_BOTH))
{
 #print_r($linha_edita_node);
 $node_nr_nome[$linha_edita_node['id_node']]=$linha_edita_node['node_nome'];
}

return(0);	#$node_nr_nome);

}
########################






function get_node_name($node_nr)
{
global $conexao;
#include_once ("includes/inc_conexao.php");
#include_once ("includes/inc_variaveis_conexao.php");

$consulta2 = "SELECT node_nome from node where id_node = '$node_nr' 
 AND node_active='1'";
#print "consulta2: $consulta2, id_node: $node_nr<br>";
$resultado2 = mysql_query($consulta2,$conexao);
####$num_rows = mysql_num_rows($resultado2);
if(mysql_errno($conexao) or ! $resultado2)
{
	print("*** $consulta ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita2=mysql_fetch_array($resultado2,MYSQL_BOTH);
return($linha_edita2['node_nome']);
}
########################
function get_node_name2($node_nr,$campo)
{
global $conexao;
#include_once ("includes/inc_conexao.php");
#include_once ("includes/inc_variaveis_conexao.php");

$consulta2 = "SELECT $campo from node where id_node = '$node_nr' AND node_active='1' ";
#print "consulta2: $consulta2, id_node: $node_nr<br>";
$resultado2 = mysql_query($consulta2,$conexao);
####$num_rows = mysql_num_rows($resultado2);
if(mysql_errno($conexao) or ! $resultado2)
{
	print("*** $consulta2 ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita2=mysql_fetch_array($resultado2,MYSQL_BOTH);
return($linha_edita2[$campo]);
}
########################
# update_estoq_w('node','id_node=$id_node','node_ultima_atua='$DATA_ATUA')
function update_estoq_w($tabela,$where_var,$set_var)
{
##                        where              set_var
global $conexao;
include_once ("includes/inc_conexao.php");
include_once ("includes/inc_variaveis_conexao.php");
$consulta2 = "UPDATE $tabela set $set_var where $where_var limit 1 ";
#print "consulta2: $consulta2"; exit();
$resultado2 = mysql_query($consulta2,$conexao);
if(mysql_errno($conexao) )
{
	print("*** $consulta2 ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
}
########################################
function get_node_ips($node_nr)
{
global $conexao;
#include_once ("includes/inc_conexao.php");
#include_once ("includes/inc_variaveis_conexao.php");
##select id_node_ips,node_ip,node_mascara from node_ips where id_node_ips='6';
$consulta2 = "SELECT id_node_ips,node_ip,node_mascara
	 from node_ips where id_node_ips = '$node_nr'
	 AND ips_destino='1' and  ips_active='1' ";	#ips_destino=1: ips_cliente
$resultado2 = mysql_query($consulta2,$conexao);
#$num_rows = mysql_num_rows($resultado2);
if(mysql_errno($conexao) or ! $resultado2)
{
	print("*** $consulta2 ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$IPS=""; $MASC=""; $IPS_MASC="";
while ($linha2=mysql_fetch_array($resultado2,MYSQL_BOTH))
{
 if(strlen($IPS_MASC)>1)$IPS_MASC .=",<br>";
 #$linha2=mysql_fetch_array($resultado2,MYSQL_BOTH);
 $ips_cur=trim($linha2['node_ip']);
 if(empty($ips_cur))$ips_cur="***node: $node_nr: ERRO***";
 $IPS =$ips_cur;

 $MASC ="255.255.255." . $linha2['node_mascara'];
 $IPS_MASC .="$IPS/$MASC";
}

return($IPS_MASC);

}
########################################
function get_node_gateways($node_nr)
{
global $conexao;
include_once ("includes/inc_conexao.php");
include_once ("includes/inc_variaveis_conexao.php");
##select id_node_ips,node_ip,node_mascara from node_ips where id_node_ips='6';
$consulta3 = "SELECT id_node_ips,node_ip,node_mascara,node_gw,interface_lan,interface_wan
	 from node_ips where id_node_ips = '$node_nr'
	 AND ips_destino='1' and  ips_active='1' ";	#ips_destino=1: ips_cliente
$resultado3 = mysql_query($consulta3,$conexao);
$num_rows = mysql_num_rows($resultado3);
if(mysql_errno($conexao) or ! $resultado3)
{
	print("*** $consulta3 ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	print "Linhas $num_rows , resultado3: "; print_r($resultado3); print "<br>";
	exit();
}
$IPS=""; $MASC=""; $IPS_MASC="";
$REDE_MASC=array("0" => "24", "128" => "25", "192" => "26");
$GATEWAYS_RET=array();
while ($linha2=mysql_fetch_array($resultado3,MYSQL_BOTH))
{
 ##if(strlen($IPS_MASC)>1)$IPS_MASC .=",<br>";
 $ips_cur=trim($linha2['node_gw']);
 if(empty($ips_cur))$ips_cur="***node: $node_nr: ERRO***";
 $IPS =$ips_cur;

 $MASC =$linha2['node_mascara']; $MASC = $REDE_MASC[$MASC];
 ##$GATEWAYS_RET[]="$IPS/$MASC";
 $GATEWAYS_RET["$IPS/$MASC"]['lan']=trim($linha2['interface_lan']);
 $GATEWAYS_RET["$IPS/$MASC"]['wan']=trim($linha2['interface_wan']);
}

return($GATEWAYS_RET);

}

########################################

function ValidateMAC($str)
{
  preg_match("/^([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})[-: ]?([0-9a-fA-F]{0,2})$/", $str, $arr);
  if(strlen($arr[0]) != strlen($str))
   return FALSE;
  return sprintf("%02X:%02X:%02X:%02X:%02X:%02X", hexdec($arr[1]),
	hexdec($arr[2]), hexdec($arr[3]), hexdec($arr[4]), hexdec($arr[5]), hexdec($arr[6]));
}

/* $testStrings = array("0-1-2-3-4-5","00:a0:e0:15:55:2f","89 78 77 87 88 9a",
	"0098:8832:aa33","bc de f3-00 e0 90","00e090-ee33cc",
	"bf:55:6e:7t:55:44", "::5c:12::3c","0123456789ab");
 foreach($testStrings as $str)
 {
   $res = ValidateMAC($str);
   print("$str => $res<br>");
 }
*/
function send_mailx($sender,$destinatario,$assunto,$body)
{
 ##system ("echo \"send_mailx destinatario: $destinatario\" >> /tmp/send_mailx.txt");
 $sendmail = "/usr/sbin/sendmail -t -f $sender";
 $fd = popen($sendmail, "w");
 fputs($fd, "To: $destinatario\r\n");
 fputs($fd, "From: $sender\r\n");
 fputs($fd, "Subject: $assunto\r\n");
 fputs($fd, "X-Mailer: PHP mailer\r\n\r\n");
 fputs($fd, $body);
 pclose($fd);
}

function send_attach($sender,$destinatario,$assunto,$body)
{
#http://www.webcheatsheet.com/PHP/send_email_text_html_attachment.php
#http://www.phpbuilder.com/columns/kartic20000807.php3?page=1
#
//define the receiver of the email
$to = $destinatario;	#'youraddress@example.com';
//define the subject of the email
$subject = $assunto;	#'Test email with attachment';
//create a boundary string. It must be unique
//so we use the MD5 algorithm to generate a random hash
$random_hash = md5(date('r', time()));
//define the headers we want passed. Note that they are separated with \r\n
##$headers = "From: webmaster@example.com\r\nReply-To: webmaster@example.com";

# Em 27-7-2009
####$headers = "From: $sender\r\nReply-To: $sender";
$headers = "From: $sender\r\nReply-To: $sender\r\n";
$headers .= "Return-path: $sender\r\n";	#Sem isto o email fica
            # "Return-path: apache@capri2.wb.com.br e muitos ISP's rejeitam

//add boundary string and mime type specification
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-" 
. $random_hash . "\"";
//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
##$attachment = chunk_split(base64_encode(file_get_contents('attachment.zip')));
$attachment = chunk_split(base64_encode($body));
#Content-Disposition: attachment|inline
#Content-Description: This is an optional header. Free text descriptions 

//define the body of the message.
ob_start(); //Turn on output buffering
print <<<EOF
--PHP-mixed-$random_hash
Content-Type: multipart/alternative; boundary="PHP-alt-$random_hash"

--PHP-alt-$random_hash
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

Estamos enviando a mensagem em anexo.

--PHP-alt-$random_hash
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<h2>Estamos enviando a mensagem em anexo.</h2>
<p>Está no formato HTML.</p>

--PHP-alt-$random_hash

--PHP-mixed-$random_hash
Content-Type: application/html; name="attachment.html"
Content-Transfer-Encoding: base64 
Content-Disposition: attachment

$attachment
--PHP-mixed-$random_hash

EOF;
#Original estava na linha acima:
# Content-Type: application/zip; name="attachment.zip"

//copy current buffer contents into $message variable and delete current output buffer
$message = ob_get_clean();
//send the email
$mail_sent = @mail( $to, $subject, $message, $headers,"-f$sender");
//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
#echo $mail_sent ? "Mail sent" : "Mail failed";
return($mail_sent);

}

function send_mailx_txt_attach
($sender,$destinatario,$assunto,$body_txt,$body_attach,$nome_attach)
{
#http://www.webcheatsheet.com/PHP/send_email_text_html_attachment.php
#http://www.phpbuilder.com/columns/kartic20000807.php3?page=1
#
$CRLF="\r\n";
#$CRLF="\n";
$mensagem_no_formato_html=$body_attach;

//define the receiver of the email
$to = $destinatario;	#'youraddress@example.com';
//define the subject of the email
$subject = $assunto;	#'Test email with attachment';
//create a boundary string. It must be unique
//so we use the MD5 algorithm to generate a random hash
$random_hash = "x" . md5(date('r', time())) . "x";
//define the headers we want passed. Note that they are separated with \r\n
##$headers = "From: webmaster@example.com\r\nReply-To: webmaster@example.com";
$headers = "From: $sender\r\nReply-To: $sender\r\n";
$headers .= "Return-path: $sender\r\n";	#Sem isto o email fica
            # "Return-path: apache@capri2.wb.com.br e muitos ISP's rejeitam

#$headers .= "Connection: close\r\n";
#$headers .= "Cache-Control: no-cache, must-revalidate\r\n";  // HTTP/1.1
#$headers .= "Expires: Sat, 26 Jul 1997 05:00:00 GMT\r\n"; 	    // Date in the past
#Pedro em 23-12-2008
##$headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";


//add boundary string and mime type specification
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed;$CRLF\tboundary=\"PHP-mixed-" 
  . $random_hash . "\"";	## . "\r\n";
# com multipart/alternative - fica inline
#$headers .= "Content-Type: multipart/alternative;  boundary=\"PHP-mixed-" . $random_hash . "\"";
#                Content-Type: 	multipart/alternative; boundary="----=_NextPart_000_EE680_01C927CF.3A961E40"
##$headers .= "\r\n\r\n";

//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
##$attachment = chunk_split(base64_encode(file_get_contents('attachment.zip')));
$attachment = chunk_split(base64_encode($body_attach));
#Content-Disposition: attachment|inline
#Content-Description: This is an optional header. Free text descriptions 

//define the body of the message.
ob_start(); //Turn on output buffering
/**********/
$mess1=<<<EOF
This is a multi-part message in MIME format.

--PHP-mixed-$random_hash
Content-Type: multipart/alternative;
	boundary="PHP-alt-$random_hash"

--PHP-alt-$random_hash
Content-Type: text/plain;
	charset="iso-8859-1"
Content-Transfer-Encoding: quoted-printable

$body_txt


--PHP-alt-$random_hash
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

$mensagem_no_formato_html


--PHP-alt-${random_hash}--

EOF;
print $mess1;

/*********/
##print "\r\n\r\n" . $body_txt . "\n\n";

$mess2=<<<EOF

--PHP-mixed-$random_hash
Content-Type: application/html; name="$nome_attach"
Content-Transfer-Encoding: base64 
Content-Disposition: attachment

$attachment

--PHP-mixed-${random_hash}--

EOF;
print $mess2;
#Original estava na linha acima:
# Content-Type: application/zip; name="attachment.zip"

//copy current buffer contents into $message variable and delete current output buffer
$message = ob_get_clean();
//send the email
#$mail_sent = @mail( $to, $subject, $message, $headers );
$mail_sent = @mail( $to, $subject, $mess1 . $mess2, $headers,"-f$sender" );

//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
#echo $mail_sent ? "Mail sent" : "Mail failed";
return($mail_sent);

}

function send_mailx_confirma($sender,$destinatario,$assunto,$body)
{
 #Confirma recebimento da mensagem
 ##system ("echo \"send_mailx destinatario: $destinatario\" >> /tmp/send_mailx.txt");
 $sendmail = "/usr/sbin/sendmail -t -f $sender";
 $fd = popen($sendmail, "w");
 fputs($fd, "To: $destinatario\r\n");
 fputs($fd, "Disposition-Notification-To: $sender\r\n");
 fputs($fd, "From: $sender\r\n");
 fputs($fd, "Reply-To: $sender\r\n");
 fputs($fd, "Return-path: $sender\r\n");
 fputs($fd, "Subject: $assunto\r\n");
 fputs($fd, "X-Mailer: PHP mailer\r\n\r\n");
 fputs($fd, $body);
 pclose($fd);
}
################
function send_mailx_html($sender,$destinatario,$assunto,$body)
{
 $numargs = func_num_args();
 $disposition=false;
 if($numargs > 4)$disposition=true;
 
/************
#http://www.php.net/manual/en/function.mail.php
# -=-=-=- MIME BOUNDARY
$mime_boundary = "----MSA Shipping----".md5(time());
# -=-=-=- MAIL HEADERS
$to = "dlwijerathna@gmail.com";
//$to = "duminda@dumidesign.com";
//$to = "info@msashipping.com";
$subject = "Information Request from MSA Shipping - Contact Form";
$headers = "From: MSA SHIPPING <webmaster@msashipping.com>\n";
$headers .= "Reply-To: MSA Shipping <webmaster@msashipping.com>\n";
$headers .= "BCC: dumidesign@gmail.com";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
$message .= "--$mime_boundary\n";
$message .= "Content-Type: text/html; charset=UTF-8\n";
$message .= "Content-Transfer-Encoding: 8bit\n\n";

# -=-=-=- FINAL BOUNDARY
$message .= "--$mime_boundary--\n\n";
*******/

#$body=strip_tags($body);
$sendmail = "/usr/sbin/sendmail -t -f $sender";
$fd = popen($sendmail, "w");
fputs($fd, "To: $destinatario\r\n");
if($disposition){
 fputs($fd, "Disposition-Notification-To: $sender\r\n");
}
fputs($fd, "From: $sender\r\n");
fputs($fd, "Reply-To: $sender\r\n");
fputs($fd, "Return-path: $sender\r\n");
fputs($fd, "Subject: $assunto\r\n");
fputs($fd, "X-Mailer: PHP mailer\r\n");
fputs($fd,"Mime-Version: 1.0\r\n");
$marcacao=md5(time());

$CONTENT=<<<EOF
Content-Type: multipart/alternative;
	boundary="= Multipart Boundary $marcacao"\r\n
EOF;
fputs($fd, $CONTENT);
$CONTENT2=<<<EOF


This is a multipart MIME message.

--= Multipart Boundary $marcacao
Content-Type: text/html;
	charset="ISO-8859-1"
Content-Transfer-Encoding: 8bit


EOF;
fputs($fd, $CONTENT2);
fputs($fd, $body);
fputs($fd,"\n--= Multipart Boundary $marcacao--\n");

pclose($fd);

}
#########################

function block_pac($PACKAGES_table,$clientnum,$radcheck_table)
{
   #Entrando em suspensao.
   #suspend: 1, suspend_old: 0
   #suspendservfuturo: 1, suspendservfrom: 1151463600
   #suspendcobfuturo: 1, suspendcobfrom: 1151463600
   #include_once("dbinfo.php");
   include("dbinfo.php");
   
   global $db_link;
   $UNBLOCK_MAX="100";

 
   ######### Muda radreply   -- em common.php -block_pac
   #mysql> select * from radreply where UserName like '%pedro%' limit 1;
   #+-----+-----------------------+--------------+----+-------+
   #| id  | UserName              | Attribute    | op | Value |
   #+-----+-----------------------+--------------+----+-------+
   #| 635 | pedro@pppoe.wb.com.br | Connect-Info | := | 384   |
   #+-----+-----------------------+--------------+----+-------+
   #$query2="UPDATE $radreply_table set Value='10' WHERE 
   # clientid='$clientnum' and ";	### AND UserName='$UserName'";
   #$result2=mysql_query($query2,$db_link);
   #mysql> select * from PACKAGES where clientid='220424' limit 1;
   #mysql> select bandwidth,ulbandwidth,dlbandwidth from PACKAGES where clientid='220424';

   #$SERVICE_ID0=$servcode;
   #if($SERVICES_IP[$SERVICE_ID0])
   #empty($ip_nr)
   #$query_pac="UPDATE $PACKAGES_table set 
   #	bandwidth='10',ulbandwidth='10',
   #	dlbandwidth='10' where clientid='$clientnum'
   #	and active='1' and suspend='0' limit 20";
   #    Talvez seja melho suspender e nao colocar a banda em 10.
   $query_pac="UPDATE $PACKAGES_table set 
	suspend='1' where clientid='$clientnum'
	and active='1' and suspend='0' 
	limit $UNBLOCK_MAX";

   #AND start >= '$begin_timed_pac' AND start <= '$final_timed_pac'";
   #print "2. query: $query2<br>";
   $result_pac=mysql_query($query_pac,$db_link);
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
   }

   #Suspende os dominios
   $query_pac="UPDATE $PACKAGES_table set 
	suspend='1' where clientid='$clientnum'
	and active='1' and suspend='0' 
	and servcode='$SERVICE_DOMAIN'
	limit 20";

   #AND start >= '$begin_timed_pac' AND start <= '$final_timed_pac'";
   #print "2. query: $query2<br>";
   $result_pac=mysql_query($query_pac,$db_link);
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
   }
   # 6-11-2006: Verifica se entre os pacotes suspensos tem equipamentos em comodato
   verify_comodato_block_pac($PACKAGES_table,$clientnum,$radcheck_table);

   #########  Bloqueia / desbloquei contas de acesso do usuario em $radcheck_table
   $query2="SELECT id,Value FROM $radcheck_table WHERE 
    clientid='$clientnum'";	### AND UserName='$UserName'";
   $result2=mysql_query($query2,$db_link);
   #print "2. query: $query2<br>";
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** 99. $query2 ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
   }
   //$new_pass=crypt($new[0]);
   #$out=mysql_fetch_row($result);
   ###$row_pack = mysql_fetch_array($result2);
   #$new_pass="!$out[0]";
   while ($linha=mysql_fetch_array($result2,MYSQL_BOTH))
   {
    #if($suspend){ $active='0';
    #	$new_pass="!$linha[Value]";	#"!$out[0]";
    #}
    #else { $active='1';
    #	$new_pass= ereg_replace ('^\!','',$linha[Value]);
    #	#$new_pass="$out[0]";
    #}
    $new_pass="!$linha[Value]";
    $id=$linha['id'];
    #active='$active'
    $query3="UPDATE $radcheck_table SET Value='$new_pass'
    WHERE  clientid='$clientnum'
    AND id=$id";
    #print "3. clientid=$clientnum, id=" . $id . " Value=" . $linha[Value] . "<br>";
    if(!mysql_query($query3,$db_link))
    {
	print("$L_Password_could_not_be_updated
	$L_Please_contact_our 
	<a href=mailto:$replymail>$L_billing_dept</a>");
	print("*** $query3 ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
    }
   }
   ######### Muda radreply   -- em common.php -block_pac
   #exit();
   ########################### Fim bloqueia/desbloqueia acessos PACKAGES

}
function verify_comodato_block_pac($PACKAGES_table,$clientnum,$radcheck_table)
{
 global $db_link, $db_link_INVOICES, $now_date_Y, $serverurl, $serverurl_http;
 $nome_cli=get_nome($clientnum);
 $ender_cli=get_endereco($clientnum);
 $email_cli=get_email($clientnum);
 $query_pac="SELECT * from $PACKAGES_table WHERE
	(suspend='1' or active='0') AND clientid='$clientnum'
	AND  equipcomodato='1' AND equipcomodato_devol='0'
	";
 $result_pac=mysql_query($query_pac,$db_link);
 #if(!mysql_query($query2,$db_link) )
 if(mysql_errno($db_link))
 {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
 }
 $equip="Em " . $now_date_Y . ":\n";
 $envia_email=false;
 while ($linha=mysql_fetch_array($result_pac,MYSQL_BOTH))
 {
  $equip .="Foi suspenso Cliente/packid com equip. em comodato: $linha[clientid] / $linha[packid]";
  $equip .= "\nNome: $nome_cli\nEndereço: $ender_cli\nEmail: $email_cli\n";
  $envia_email=true;
 }
 if($envia_email)
 {
  $equip .="\n\n *** PROVIDENCIAR NEGOCIAÇÃO DA DÍVIDA OU RETIRADA DO EQUIPAMENTO ***\n\n";
  $equip .="\n<a href=\"$serverurl_http/tools.php?clientid=$clientnum\">Acessa cliente</a>\n\n";
  $sender="info@wb.com.br"; $destinatario="fatura@wb.com.br"; 
  $assunto="Cliente suspenso com Equipamento em comodato";
  #send_mailx($sender,$destinatario,$assunto,$equip);
  $equip_html=ereg_replace("\n","<br>\n",$equip);
  send_mailx_html($sender,$destinatario,$assunto,$equip_html);

 }
}
##################
function unblock_pac($PACKAGES_table,$clientnum,$radcheck_table)
{
  #Saindo da suspensao:
  #suspend: 0, suspend_old: 1
  #suspendservfuturo: 0, suspendservfrom: 0
  #suspendcobfuturo: 0, suspendcobfrom: 0
  ##include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link;
  $UNBLOCK_MAX="100";
   ######### Muda radreply   -- em common.php - unblock_pac
   #mysql> select * from radreply where UserName like '%pedro%' limit 1;
   #+-----+-----------------------+--------------+----+-------+
   #| id  | UserName              | Attribute    | op | Value |
   #+-----+-----------------------+--------------+----+-------+
   #| 635 | pedro@pppoe.wb.com.br | Connect-Info | := | 384   |
   #+-----+-----------------------+--------------+----+-------+
   #$query2="UPDATE $radreply_table set Value='10' WHERE 
   # clientid='$clientnum' and ";	### AND UserName='$UserName'";
   #$result2=mysql_query($query2,$db_link);
   #mysql> select * from PACKAGES where clientid='220424' limit 1;
   #mysql> select bandwidth,ulbandwidth,dlbandwidth from PACKAGES where clientid='220424';

   #$SERVICE_ID0=$servcode;
   #if($SERVICES_IP[$SERVICE_ID0])
   #empty($ip_nr)
   #$query_pac="UPDATE $PACKAGES_table set 
   #	bandwidth='10',ulbandwidth='10',
   #	dlbandwidth='10' where clientid='$clientnum'
   #	and active='1' and suspend='0' limit 20";
   #    Talvez seja melho suspender e nao colocar a banda em 10.
   $query_pac="UPDATE $PACKAGES_table set 
	suspend='0',synced='0',envia_email='0' where clientid='$clientnum'
	and active='1' and suspend='1' 
	limit $UNBLOCK_MAX";

   #AND start >= '$begin_timed_pac' AND start <= '$final_timed_pac'";
   #print "2. query: $query2<br>";
   $result_pac=mysql_query($query_pac,$db_link);
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
   }

   #UN-Suspende os dominios
   $query_pac="UPDATE $PACKAGES_table set 
	suspend='0' where clientid='$clientnum'
	and active='1' and suspend='1' 
	and servcode='$SERVICE_DOMAIN'
	limit 20";

   #AND start >= '$begin_timed_pac' AND start <= '$final_timed_pac'";
   #print "2. query: $query2<br>";
   $result_pac=mysql_query($query_pac,$db_link);
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
   }

   #########  Bloqueia / desbloquei contas de acesso do usuario em $radcheck_table
   $query2="SELECT id,Value FROM $radcheck_table WHERE 
    clientid='$clientnum'";	### AND UserName='$UserName'";
   $result2=mysql_query($query2,$db_link);
   #print "2. query: $query2<br>";
   ####if(!mysql_query($query2,$db_link) )
   if(mysql_errno($db_link))
   {
	print("*** 99. $query2 ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
   }
   //$new_pass=crypt($new[0]);
   #$out=mysql_fetch_row($result);
   ###$row_pack = mysql_fetch_array($result2);
   #$new_pass="!$out[0]";
   while ($linha=mysql_fetch_array($result2,MYSQL_BOTH))
   {
    #if($suspend){ $active='0';
    #	$new_pass="!$linha[Value]";	#"!$out[0]";
    #}
    #else { $active='1';
    #	$new_pass= ereg_replace ('^\!','',$linha[Value]);
    #	#$new_pass="$out[0]";
    #}

    $new_pass= $linha['Value']; 
    #print "1. linha[Value]: $linha[Value], new_pass: $new_pass<br>";
    while (preg_match("/^\!/", $new_pass)) {
      #print "linha[Value]: $linha[Value], new_pass: $new_pass<br>";
      $new_pass= ereg_replace ('^\!','',$new_pass);
    }
    #exit();

    $id=$linha[id];
    #active='$active'
    $query3="UPDATE $radcheck_table SET Value='$new_pass'
    WHERE  clientid='$clientnum'
    AND id=$id";
    #print "3. clientid=$clientnum, id=" . $id . " Value=" . $linha[Value] . "<br>";
    if(!mysql_query($query3,$db_link))
    {
	print("$L_Password_could_not_be_updated
	$L_Please_contact_our 
	<a href=mailto:$replymail>$L_billing_dept</a>");
	print("*** $query3 ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
    }
   }
   ######### Muda radreply   -- em common.php -block_pac

   #exit();
   ########################### Fim bloqueia/desbloqueia acessos PACKAGES

}
##################
function unblock_clientid($clientid,$PACKAGES_table,$radcheck_table)
{
  #Saindo da suspensao:
  #suspend: 0, suspend_old: 1
  #suspendservfuturo: 0, suspendservfrom: 0
  #suspendcobfuturo: 0, suspendcobfrom: 0
  #include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $CLIENT_table, $db_link;

  $clientid=trim($clientid);
  if(strlen($clientid) < 1)die("Clientid: $clientid ???");

  unblock_pac($PACKAGES_table,$clientid,$radcheck_table);

  $query_pac="UPDATE $CLIENT_table set 
	suspend='0',suspendcobfuturo='0' where clientid='$clientid'
	and active='1' and suspend='1' 
	limit 20";
  #print "query_pac: $query_pac"; exit();
  $result_pac=mysql_query($query_pac,$db_link);
  ####if(!mysql_query($query2,$db_link) )
  if(mysql_errno($db_link))
  {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
  }
  ########################### Fim bloqueia/desbloqueia acessos CLIENT

}
#############################
function valida_email($exEmail,$action)
{
/*
#Testa se email é valido e se existe realmente.
#$action=so retorn o resultado sem imprimir (print).
$action=0 so retorna o resultado e nao imprime nada
*/

require("email_validation.php3");

$time_out_email=5;
#Se mais de um email, nao teste. Retorne OK
if(preg_match("/,/",$exEmail)) {
 $email_arr=explode(",",$exEmail);
 #Teste o primeiro email.
 $exEmail=$email_arr[0];
 #print "Testando $exEmail<br>";
 #return(true);
}

#Ver acima: check_email
$pattern = "/^[\w-]+(\.[\w-]+)*@";
$pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
if (! preg_match($pattern, $exEmail)) {
 ##print "Email < $exEmail > errado.<br>";
 return(false);
}

#if(preg_match("/,|\@ism.com.br/",$exEmail)) {
#return(1);
#}

#############

$validator=new email_validation_class;
$validator->timeout=$time_out_email;
	#se strcmp($exEmail,"") > 0 se $exEmail > "" 
global $groupo_contabil;
if( isset($exEmail) && strcmp($exEmail,"") &&
    ( ($groupo_contabil != 'admin' and $groupo_contabil  != 'cobranca') 
       or $action < 1
    )
  )
 {
  if(($result=$validator->ValidateEmailBox($exEmail))<0)
   {
    if($action == 1)
    {
     #echo "<H4><CENTER>It was NOT possible to determine 
     #	if $exEmail is a valid deliverable e-mail box address.
     #	</CENTER></H4>\n";
     #sleep(5);
     return(1);
     #exit();
    }
    else return(1);
   }
  else if($result < 1)
   {
    # $result == 0: email não é válido; == 1: email é válido
    if($action == 1)
    {
     echo "<H4><CENTER><br><br>$exEmail is " .
    	($result ? "" : " not ") .
    	"a valid deliverable e-mail box address.<br> $exEmail " .
    	($result ? "" : " não ") .
    	" é um 	endereço de e-mail correto/válido.
    	</CENTER></H4>\n";
     exit();
    }
    else return($result);
   }
   return($result);
 }
 else
 {
  return(1);
  /*
  $port=(strcmp($port=getenv("SERVER_PORT"),"") ? intval($port) : 80);
  $site="http://".(strcmp($site=getenv("SERVER_NAME"),"") ? $site : "localhost").($port==80 ? "" : ":".$port).$PHP_SELF;
  //echo "<H4>Access this page using a URL like:<br>
  //	$site?exEmail=<TT>your@test.email.here</TT></H4>\n";
 */

 }
 ######################## Fim Testa se email é valido
}
############################
function valida_email_cep_phone2($email,$cep,$phone)
{
 global $erro_endereco; $erro_endereco="";
 #print "email: $mail, cep: $cep, tel.: $phone<br>";
 $resul_email=valida_email($email,0); if(!$resul_email)$erro_endereco = "Email errado.";
 $resul_phone=is_phone_number($phone); if(!$resul_phone)$erro_endereco .= " Tel. errado." ;
 $resul_zip=zip_number($cep); if(!$resul_zip)$erro_endereco .= " CEP errado.";
 $resul=$resul_email and $resul_phone and $resul_zip;
 return($resul);
}
function valida_email_cep_phone($email,$cep,$phone)
{
 global $erro_endereco; $erro_endereco="";
 #print "email: $mail, cep: $cep, tel.: $phone<br>";
 #$resul_email=valida_email($email,0);
 $resul_email=validate_email($email);	#Testa syntaxe e se MX existe!
 if(!$resul_email)$erro_endereco = "Email errado.";
 $resul_phone=is_phone_number($phone); if(!$resul_phone)$erro_endereco .= " Tel. errado." ;
 $resul_zip=zip_number($cep); if(!$resul_zip)$erro_endereco .= " CEP errado.";
 $resul=$resul_email and $resul_phone and $resul_zip;
 return($resul);
}
############################
function get_suspend($clientid){
 #Retorna o bit 'suspendserv' do clientid
 #include_once("dbinfo.php");
 include("dbinfo.php");
 
 global $db_link;
 if(! $clientid) return(0);
 $query_cl="SELECT suspend,suspendserv FROM CLIENT WHERE clientid='$clientid'
	and active='1' limit 1";
 $result=mysql_query($query_cl,$db_link);
 if(!$result){
  print("*** $query_cl *** $result ***<br>");
  print "\nMysql error:" . mysql_errno($db_link)
        . " : "  . mysql_error($db_link) . "<br>";
  return(0);
 }
 $nr_clients=0; $suspensao=0; $suspensao0=0;
 while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $suspensao0=$clientes['suspendserv'];
 }
 ##if($suspensao0){$suspensao="<blink>Cliente Suspenso!</blink>"; }
 ##else { $suspensao=""; }
 #return("Cliente $clientid: $suspensao");
 return("$suspensao0");
}
#
function get_suspend2($clientid){
 #Retorna o bit 'suspend' do clientid como uma string '*** SUSPENSO ***'
 #  out ''
 #include_once("dbinfo.php");
 global $db_link, $db_link_INVOICES;
 if(! $clientid) return(0);
 $query_cl="SELECT suspend,suspendserv,suspendcobfuturo 
	FROM $CLIENT_table WHERE clientid='$clientid'
	and active='1' limit 1";
 $result=mysql_query($query_cl,$db_link);
 if(!$result){return(0); }
 if(mysql_error($db_link) )
 {
  print("ERRO: *** $query_cl *** $result ***<br>");
  print "ERRO: \nMysql error:" . mysql_errno($db_link)
        . " : "  . mysql_error($db_link) . "<br>";
  return(0);
 }
 $nr_clients=0; $suspensao0=0; $suspensao="";
 while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $suspensao0=$clientes['suspend'];
    $suspensao_serv=$clientes['suspendserv'];
    $suspensao_cob=$clientes['suspendcobfuturo'];
 }
 if($nr_clients < 1){ return(0); }
 if($suspensao0){$suspensao="*** SUSPENSO *** "; }
 else { $suspensao="";	##*** NAO SUSPENSO *** "; 
 }
 if($suspensao_serv){$suspensao .=" *** SUSPENSO SERV. *** " ; }
 if($suspensao_cob){$suspensao .=" *** SUSPENSO COB. FUT. *** "; }

 return("$suspensao");
}
###################
function get_suspend_packid($packid){
 #Retorna o bit 'suspend' do packid
 #include_once("dbinfo.php");
 global $db_link, $db_link_INVOICES, $PACKAGES_table;
 if(! $packid) return(99);
 $query_cl="SELECT suspend FROM $PACKAGES_table WHERE packid='$packid'
	and active='1' limit 1";
 $result=mysql_query($query_cl,$db_link);
 if(!$result){return(199); }
 if(mysql_error($db_link) )
 {
  print("ERRO: *** $query_cl *** $result ***<br>");
  print "ERRO: \nMysql error:" . mysql_errno($db_link)
        . " : "  . mysql_error($db_link) . "<br>";
  return(0);
 }
 $nr_clients=0; $suspensao0=0; $suspensao="";
 while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $suspensao0=$clientes['suspend'];
 }
 if($nr_clients < 1){ return(0); }
 #if($suspensao0){$suspensao="*** SUSPENSO ***"; }
 #else { $suspensao="";	##*** NAO SUSPENSO ***"; 
 #	}
 return($suspensao0);
}
###################
function get_plano_pg($clientid){
 #Retorna o byte  'cob_qmes' do clientid
 ##include_once("dbinfo.php");
 include("dbinfo.php");		#Com include_once tenho erro:
 #*** SELECT suspend,suspendserv,cob_qmes FROM  WHERE clientid='232'
 #  and active='1' limit 1 ***<br>
 #Corrigindo com:
 global $db_link, $CLIENT_table;;
 if(! $clientid) return(0);
 $query_cl="SELECT suspend,suspendserv,cob_qmes FROM $CLIENT_table WHERE clientid='$clientid'
	and active='1' limit 1";
 $result=mysql_query($query_cl,$db_link);
 #if(!$result){
 if(mysql_error($db_link) ){
  print("*** get_plano_pg: $query_cl ***<br>");
  print "\nMysql error:" . mysql_errno($db_link)
        . " : "  . mysql_error($db_link) . "<br>";
  return(0);
 }
 $nr_clients=0; $suspensao=0; $suspensao0=0;
 while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $suspensao0=$clientes['cob_qmes'];
 }
 ##if($suspensao0){$suspensao="<blink>Cliente Suspenso!</blink>"; }
 ##else { $suspensao=""; }
 #return("Cliente $clientid: $suspensao");
 return("$suspensao0");
}
##########################
function get_suspendcob($clientid){
 #Retorna o bit 'suspendcobfuturo' do clientid

 #include_once("dbinfo.php");
 global $db_link, $db_link_INVOICES;
 if(! $clientid) return(0);
 $query_cl="SELECT suspendcobfuturo FROM CLIENT WHERE clientid='$clientid'
	and active='1' limit 1";
 $result=mysql_query($query_cl,$db_link);
 ##if(!$result){return(0); }
 if(mysql_error($db_link) )
 {
  print("ERRO: *** $query_cl ***<br>");
  print "ERRO: \nMysql error:" . mysql_errno($db_link)
        . " : "  . mysql_error($db_link) . "<br>";
  return(0);
 }
 $nr_clients=0; $suspensao0=0; $suspensao="";
 while($clientes = mysql_fetch_array($result,MYSQL_BOTH)){
    $nr_clients++;
    $suspensao0=$clientes['suspendcobfuturo'];
 }
 if($nr_clients < 1){ return(0); }

 $suspensao=$suspensao0;
 #if($suspensao0){$suspensao="*** suspendcobfuturo: SIM ***"; }
 #else { $suspensao="*** suspendcobfuturo: NAO ***"; }

 return("$suspensao");
}
###################
function show_radcheck1 ($clientid,$packid,$pac_rad_vistos)
{
  #include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link, $radcheck_table;
  $query="SELECT * FROM $radcheck_table WHERE clientid='$clientid' and active='1' ";
  if($packid > 0)$query .=" and packid_rad = '$packid' ";
  $result=mysql_query($query,$db_link);

  if(mysql_error($db_link) )
  #if(! $result )
  {
	 print("101. $query<br>");
	 print "\nMysql error:" . mysql_errno($db_link)
	 . " : "  . mysql_error($db_link) . "<br>";
	 #exit();
  }

  ##print "<br>\n";
  while($list = mysql_fetch_array($result))
  {
	if(!  $pac_rad_vistos[$list['packid_rad'] ])
	{    
		show_radcheck($list,$clientid);		#,$packid);
	}
  }
}
############## $radcheck_table
function show_radcheck ($list,$clientid)	//#,$packid)
{
  global $groupo_contabil,$id_contabil,$PERMIT_ALTER_RADCHECK,
	$newclientid,$acesso_disc_usado,$nr_usuarios_bloqueados;
  global $db_link, $radcheck_table;
  ###include_once("dbinfo.php");
  include("dbinfo.php");
  $packid_rad=$list['packid_rad'];

  $query2="SELECT Value FROM $radcheck_table WHERE clientid='$clientid' 
	AND UserName='$list[UserName]'";
  $out=0;
  if($result2=mysql_query($query2,$db_link) )
  {
   $out=mysql_fetch_row($result2);
  }
  ################# 
  #function conexao_nr($login,$begin_timed_pac,$final_timed_pac)
  $ultimos_dias=90;
  $begin_timed_pac=time() - 86400*$ultimos_dias;	#comece 90 dias antes de hoje
  $final_timed_pac=time();
  $nr_conexoes=conexao_nr($list[UserName],$begin_timed_pac,$final_timed_pac);
  $nr_conexoes+=conexao_nr_comd($list[UserName],$begin_timed_pac,$final_timed_pac);
  $session_time=0; $tot_session_time = 0;
  if($nr_conexoes > 0)
  {
    #function conexao_time($login,$begin_timed_pac,$final_timed_pac)
    #?$session_time=conexao_time($list_pack[username],$begin_timed_pac,$final_timed_pac);
    $session_time=conexao_time($list[UserName],$begin_timed_pac,$final_timed_pac);
    ##$tot_session_time += $session_time;
    #$session_time=conexao_time_comd($list_pack[username],$begin_timed_pac,$final_timed_pac);
    $session_time +=conexao_time_comd($list[UserName],$begin_timed_pac,$final_timed_pac);
    $tot_session_time += $session_time;
    $acesso_disc_usado++;
  }
  $session_time_min = $session_time / 60;
  $session_time_min = sprintf ("%.2f",$session_time_min);

 #global $L_Acesso_Discado_de_username, ....; Do contrario mantem include e nao include_once
 print <<<EOF
  <u>$L_Acesso_Discado_de_username:</u> 
  <a href="dialup/htdocs/user_accounting.php?login=$list[UserName]"
  target="_blank">$list[UserName]</a> ($nr_conexoes conexões)
  ($session_time_min min) (Ultimos $ultimos_dias dias)
EOF;
  ###################################
  #if($groupo_contabil == "admin" and $id_contabil == "$PERMIT_ALTER_RADCHECK")
  #if($groupo_contabil == "suporte" or $groupo_contabil == "admin" )	# and $id_contabil == "$PERMIT_ALTER_RADCHECK")
  if( preg_match("/admin|adminredew|suporte/i", $groupo_contabil ))
  {
	 print <<<EOF
	<BR>
	<table width="25%">
	<FORM class="cob" name="change_clientid" target="_new"
               action="$serverurl/changepass2crypt.php" 
		method="post">
	<input type="hidden" name="clientid" value="$clientid">
	<input type="hidden" name="first" value="$NAME0">
	<input type="hidden" name="UserName" value="$list[UserName]">
	<input type="hidden" name="packid_rad" value="$list[packid_rad]">
	<input class="cob" type="submit" name="func" value="Novo Clientid"
		size="8" > :
	<input class="cob" type="text" name="newclientid" value="$clientid"  
	size="8"  maxlength="10"> 
EOF;

	########################## packid_rad
  	if($groupo_contabil == "admin" and $id_contabil == "$PERMIT_ALTER_RADCHECK")
	{
	#if($packid_rad < 1)
	#{
	print <<<EOF
	<br><p class="cob">
	Novo Pacote ID : 
	<input class="cob" type="text" name="newpackid_rad" value="$packid_rad"
	size="8"  maxlength="10">
EOF;
        #}
	}
	else
	{
	 print <<<EOF
	<input type="hidden" name="newpackid_rad" value="$packid_rad">
EOF;
	}
	##################

	print <<<EOF
	</FORM>
	</TABLE>
EOF;
  }
  else
  {
   print "<br>";
  }

  ###################################

  print <<<EOF
  <a
href="$serverurl/changepass2crypt.php?func=$Change0&clientid=$clientid&UserName=$list[UserName]&first=$NAME0"
  >
  $L_Troca_a_senha
  </a>
EOF;
  /////////////////////////////////////// Troca senha
  print "<br>\n";
  if(!eregi('^\!',$out[0]))
  {
  print <<<EOF
  <a
href="$serverurl/changepass2crypt.php?func=desativa&clientid=$clientid&UserName=$list[UserName]&first=$NAME0"
  >
  $L_Block_Usuario
  </a>
  <br>
EOF;
  }
  /////////////////////////////////// Block user
  if(eregi('^\!',$out[0]))
  {
  print <<<EOF
  <a
href="$serverurl/changepass2crypt.php?func=reativa&clientid=$clientid&UserName=$list[UserName]&first=$NAME0"
  >
  $L_Unblock_Usuário
  </a>
  <br>
EOF;
  $nr_usuarios_bloqueados++;
  }
  //////////////////////////////Desbloqueia

  if(!eregi('^\!',$out[0]))
  {
  print <<<EOF
  <a
href="$serverurl/changepass2crypt.php?func=testpass&clientid=$clientid&UserName=$list[UserName]&first=$NAME0"
  >
  $L_Testa_a_Senha
  </a>
  <br>
EOF;
  }
  /////////////////////////////////Testa senha
  if(eregi('^\!',$out[0]))
  {
   print <<<EOF
   <a
href="$serverurl/changepass2crypt.php?func=deleta&clientid=$clientid&UserName=$list[UserName]&first=$NAME0"
   >
   $L_Deleta_user
   </a><br>
EOF;
#   <xxbr>
#EOF;
  }
  #print "<br>";
  ///////////////// Mostra Deleta user se ja estiver bloqueado


}
############# 
function show_sel_list($var_select,$var_values_array,$select_item,$array_type)
{
##global $var_select;
#Ex.: show_sel_list("suspend_inadimp",$comentario_novo,0,1);
#print "xxxx$select_item<br>"; 
#array_type=0: a=array("0","1",...)  array_type=1: a=("0"=>"jan", "1"=>"fev",...)
print <<<EOF
<select name="$var_select">
EOF;
##<select name="var_select"  editable onChange="this.form.submit();">
reset($var_values_array);
while (current($var_values_array) !== false) {
     $var_select_crt = key($var_values_array);	#var_select_crt é o indice do array
     $escolha="";
     if($array_type == 0)
     {
	if($var_values_array[$var_select_crt] == $select_item){ $escolha=" selected"; } 
	print <<<EOF

<option value="$var_values_array[$var_select_crt]"$escolha>
	$var_values_array[$var_select_crt] </option>
EOF;

     }
     else
     {
	if($select_item == $var_select_crt){ $escolha=" selected"; } 
	print <<<EOF

<option value="$var_select_crt"$escolha> $var_values_array[$var_select_crt] </option>
EOF;

     }
#     print <<<EOF
#
#<option value="$var_select"$escolha> $var_values_array[$var_select] </option>
#EOF;
##  $var_values_array[$var_select] - $var_select - $select_item</option>

     next($var_values_array);
}
print <<<EOF

</select>
EOF;

}
############# 
function show_sel_list_sub($var_select,$var_values_array,$select_item,$array_type,$sub)
{
##global $var_select;
#Ex.: show_sel_list("suspend_inadimp",$comentario_novo,0,1);
#print "xxxx$select_item<br>"; 
#array_type=0: a=array("0","1",...)  array_type=1: a=("0"=>"jan", "1"=>"fev",...)
print <<<EOF
<select name="$var_select"
EOF;
if($sub)
{
print <<<EOF
editable onChange="this.form.submit();"
EOF;
}
print ">";

##<select name="var_select"  editable onChange="this.form.submit();">
reset($var_values_array);
while (current($var_values_array) !== false) {
     $var_select_crt = key($var_values_array);	#var_select_crt é o indice do array
     $escolha="";
     if($array_type == 0)
     {
	if($var_values_array[$var_select_crt] == $select_item){ $escolha=" selected"; } 
	print <<<EOF

<option value="$var_values_array[$var_select_crt]"$escolha>
	$var_values_array[$var_select_crt] </option>
EOF;

     }
     else
     {
	if($select_item == $var_select_crt){ $escolha=" selected"; } 
	print <<<EOF

<option value="$var_select_crt"$escolha> $var_values_array[$var_select_crt] </option>
EOF;

     }
#     print <<<EOF
#
#<option value="$var_select"$escolha> $var_values_array[$var_select] </option>
#EOF;
##  $var_values_array[$var_select] - $var_select - $select_item</option>

     next($var_values_array);
}
print <<<EOF

</select>
EOF;

}

#######################
function quota_email_uso($clientid)
{
 global $db_link, $PACKAGES_table, $SERVICE_EMAIL;
 $query_quota="SELECT sum(quotasize) FROM 
	$PACKAGES_table WHERE clientid='$clientid' 
	and active='1' and suspend='0' and servcode='$SERVICE_EMAIL' ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 ##print "query_quota: $query_quota <br>";
 ##$row2=mysql_fetch_row($valorq);
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}

function atualiza_email_uso($clientid,$quotasize,$email,$quota_email_reservada,$QUOTA_EMAIL_MAX)
{
	###$quota_email_reservadat=quota_email_uso($clientid); --  so quando cria email
	$quotasize0=$quotasize;
	#$quota_email_reservadat=quota_email_uso($clientid);
	#$quota_email_reservada=$quota_email_reservadat - $quotasize;
	if($quotasize > ($QUOTA_EMAIL_MAX - $quota_email_reservada ) )
	{ 
	  $quotasize=$QUOTA_EMAIL_MAX - $quota_email_reservada;
	  if($quotasize < 0)$quotasize=0;
	  print "<center><br>1. Quota de email especificada ($quotasize0 Mb) 
		excede a max. contratada ($QUOTA_EMAIL_MAX Mb).<br>
		Ajuste para $quotasize Mb<br>.";
	  exit();
	}
	return($quotasize);
	#else {
	#  $quotasize=$quotasize0;
	#}

}

function cria_email_uso($clientid,$quotasize,$email,$password,$QUOTA_EMAIL_MAX)
{
	global $cpanel_vexim,$email_ja_existia;
	$email_ja_existia=true;
	if(!validate_email($email))
	{
	 die("Email < $email > invalido!"); exit();
	}
	$quota_email_reservadat=quota_email_uso($clientid);
	$quotasize_cur=$QUOTA_EMAIL_MAX - $quota_email_reservadat;
	if($quotasize_cur < 0)$quotasize=0;
	if($quotasize_cur < $quotasize)$quotasize=$quotasize_cur;

	$quotasize=atualiza_email_uso($clientid,$quotasize,$email,$quota_email_reservada,$QUOTA_EMAIL_MAX);

	$email=trim($email);
	#Cria email 
	$split = split("@",$email);
	$loginemail = $split[0];
	$dominioemail = $split[1];
	##print "Criando email: $email  ... " . $cpanel_vexim[$dominioemail] . "..."; exit();
	$ATUALIZA_EMAIL=0;
	$ATUALIZA_COMANDO="";
	include_once dirname(__FILE__) . "/cpanel/common_vexim2.php";
	if($cpanel_vexim[$dominioemail] == "vexim")
	{
	  $realname=get_nome($clientid);
	  /*
	  $ATUALIZA_EMAIL_CRIA="cpanel/adminuseraddsubmit2.php?";
	  $ATUALIZA_EMAIL_CRIA .="email=$loginemail&dominio=$dominioemail&senha=$row[password]&quota=$row[quotasize]";
	  $ATUALIZA_EMAIL_CRIA .="&realname=$realname";
	  $ATUALIZA_COMANDO_CRIA = "Cria email";
	  #
	  $ATUALIZA_EMAIL1="cpanel/userchangesubmit2.php?";
	  $ATUALIZA_EMAIL1 .="email=$loginemail&dominio=$dominioemail&senha=$row[password]&quota=$row[quotasize]";
	  $ATUALIZA_EMAIL1 .="&realname=$realname";
	  $ATUALIZA_COMANDO1="Atualiza email";
	  */

	  $resultado=cria_email_vexim2($loginemail,$dominioemail,$clientid,$realname,$quotasize,$password);
	  
	  if($resultado)
	  {
	    #Insucesso
	    $email_ja_existia=true;
	  }
	  else
	  {
	   #print "Email criado com sucesso<br>";
	   $email_ja_existia=false;
	  }
	  
	}
	else if($cpanel_vexim[$dominioemail] == "cpanel")
	{
	  include("config.php");
          #Se usar include_once ocorrem varios erros. As variaveis declaradas em config.phpsao perdidas.
	  
	  include_once dirname(__FILE__) . "/cpanel/common_cpanel.php";
	  ##print "Criando email: $email, $password, $quotasize<br>"; exit();

	  #cpanel_cria_email.php?email=pedro765@vetor.com.br&password=xxcv&quota=71
	  $criaconta = pop3create($email,$password,$quotasize);
	  if ($criaconta == 1) 
	  {
	   #echo "Conta :$email: Criada com Sucesso!!!";
	   $email_ja_existia=false;
	  }
	  if ($criaconta == 2) 
	  {
	   #echo "Conta :$email: já Existente!!!<br>Tente Novamente...";
	   $email_ja_existia=true;
	  }
	  
	}
	return($quotasize);
}
function del_email_uso($email)
{
  global $cpanel_vexim,$email_ja_existia;
  #print "Deletando conta $email<br>"; exit();
  #Delete email 
  $email=trim($email);
  $split = split("@",$email);
  $loginemail = $local_part = $split[0];
  $dominioemail = $dominio0 = $split[1];
  $ATUALIZA_EMAIL=0;
  $ATUALIZA_COMANDO="";
  include_once dirname(__FILE__) . "/cpanel/common_vexim2.php";
  if($cpanel_vexim[$dominioemail] == "vexim")
  {
    #$resultado=cria_email_vexim2($email,$dominio,$clientid,$realname,$quota,$senha);
    ##$resultado=cria_email_vexim2($loginemail,$dominioemail,$clientid,$clientid,$quotasize,$password);
    $resultado=delete_email_vexim2($local_part,$dominio0,$clientid);
    #resultado=0: OK, =1: Erro ao deletar email $local_part@$dominio0 .
    #print "Deletando: $local_part@$dominio0 -- resultado: $resultado<br>";
    return($resultado);
  }
  else if($cpanel_vexim[$dominioemail] == "cpanel")
  {
    include("config.php");
    #Se usar include_once ocorrem varios erros. As variaveis declaradas em config.phpsao perdidas.
    include_once dirname(__FILE__) . "/cpanel/common_cpanel.php";
    #$criaconta = pop3create($email,$password,$quotasize);
    #print "1. Deletando conta $email<br>"; exit();
    $del_conta=pop3delete($email);
    if ($del_conta == 1) 
    {
     #echo "Conta :$email: inexistente";
     $email_ja_existia=false;
     return($del_conta);
    }
    if ($del_conta == 2) 
    {
     #echo "Conta :$email: apagada com sucesso";
     $email_ja_existia=true;
     return(0);
    }
  }
  return(0);	#$del_conta
}

function get_nome($clientid)
{
 global $db_link, $CLIENT_table;
 $query_quota="SELECT first,last FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
	###and active='1' and suspend='0' and servcode='$SERVICE_EMAIL' ";
 ##print "query: $query_quota<br>";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error ($db_link))
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
	#return(0);
 }
 $num_rows = mysql_num_rows($valorq);
 #print "num_rows: $num_rows";
 if($num_rows < 1) return("0");
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0] . $clientesq[1]);

}

function get_email($clientid)
{
 global $db_link, $CLIENT_table;
 $query_quota="SELECT email FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_quota,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}

function get_endereco($clientid)
{
#address_tipo_cob | address             | address_nr_cob | address_compl_cob
#| bairro  | city         | state | zip      | phone     | fax |
#address_tipo_inst | address_inst | address_nr_inst | address_compl_inst |
#bairro_inst | city_inst | state_inst | zip_inst | phone_inst 

 global $db_link, $CLIENT_table;
 $query_end="SELECT address_tipo_cob,address,address_nr_cob,
	address_compl_cob,bairro,phone FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_end,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_end ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq['address_tipo_cob'] . " " . $clientesq['address'] . " ". 
	$clientesq['address_nr_cob'] . " - " .
	$clientesq['address_compl_cob'] . " - " . $clientesq['bairro'] . 
	". Tel: " . $clientesq[5]);

}

function get_active_sus($clientid)
{
 global $db_link, $CLIENT_table;
 $query_act="SELECT count(active) FROM 
	$CLIENT_table WHERE clientid='$clientid' 
	AND active='0' and suspend='1' ";
 $valorq=mysql_query($query_act,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_act ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}
########################################
function insert_debito($clientid,$mes_devido,$mes_a_cobrar,$valor,$mensagem,$descricao)
{
 global $db_link, $CLIENT_table,$PACKAGES_table,$id_contabil,$SERVICE_DEBT;
 $now=getdate();
 $este_mes=$now['mon'];
 ##print "Este mes: $este_mes<br>";
 $year=$now['year'];
 if($mes_a_cobrar < $este_mes)$year++;	#O proximo mes é o prox. ano?!
 #print "$clientid,$mes_devido(este mes:$este_mes),$mes_a_cobrar/$year,$valor,$mensagem<br>"; exit();

#int mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
$ndays_do_mes = date("t", mktime(0, 0, 0, $mes_a_cobrar, 1,$year)); // You can also specify a date 
if($ndays_do_mes > 28)$ndays_do_mes="28";

#insert_debito($clientid,$date0_orig[$invcount],$mes_seguinte,$new_amount[$invcount],$mensagem)
#Insere pacote de debito no clientid, valendo no mes seguinte.
#Chamado de payrecvd.php
#DIA --  Inicio do pacote
$day='1';
$month=$mes_a_cobrar;
//////////////////// end package ///////////////////////////////
#DIA
$endday=$ndays_do_mes;
#MES
$endmon=$mes_a_cobrar;
#ANO
$endyear=$year;
$end=mktime(23,59,59,$endmon,$endday,$endyear);

$period=0;	#$One_Time_Fee
$price=$cost=$valor;
$created_date=time();
$startdate=mktime(0,0,0,$month,$day,$year);
$servcode=$SERVICE_DEBT;
$servtype=$SERVICE_DEBT;
$desserv=$descricao;
$comment=$mensagem;
$discount=0;
#print "SERVICE_DEBT: $SERVICE_DEBT<br>";

$query_ins="INSERT INTO $PACKAGES_table 
(clientid,created_date,
start,price,discount,period,desserv,servtype,
servcode,cost,
renewdate,balance,comment,lastrenew,end,vendedor
) ";

$query_ins.="VALUES('$clientid','$created_date',
'$startdate','$price','$discount',
'$period','$desserv',
'$servtype','$servcode',
'$cost','$renew','$cost','$comment','$startdate','$end','$id_contabil'
)";
#print "Inserindo data: $query_ins.<br>";
$valorq=mysql_query($query_ins,$db_link);
if( mysql_error($db_link) )
{
	print("*** 1. $query_ins ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
}
$last_packid=mysql_insert_id($db_link);
#print "packid inserido: $last_packid<br>";
return($last_packid);

}
########################################
function insert_debito2($db_link_CRT,$clientid,$mes_devido,$mes_a_cobrar,$valor,$mensagem,$descricao)
{
 global $CLIENT_table,$PACKAGES_table,$id_contabil,$SERVICE_DEBT;
 $db_link=$db_link_CRT;
 $now=getdate();
 $este_mes=$now['mon'];
 ##print "Este mes: $este_mes<br>";
 $year=$now['year'];
 if($mes_a_cobrar < $este_mes)$year++;	#O proximo mes é o prox. ano?!
 #print "$clientid,$mes_devido(este mes:$este_mes),$mes_a_cobrar/$year,$valor,$mensagem<br>"; exit();

#int mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
$ndays_do_mes = date("t", mktime(0, 0, 0, $mes_a_cobrar, 1,$year)); // You can also specify a date 
if($ndays_do_mes > 28)$ndays_do_mes="28";

#insert_debito($clientid,$date0_orig[$invcount],$mes_seguinte,$new_amount[$invcount],$mensagem)
#Insere pacote de debito no clientid, valendo no mes seguinte.
#Chamado de payrecvd.php
#DIA --  Inicio do pacote
$day='1';
$month=$mes_a_cobrar;
//////////////////// end package ///////////////////////////////
#DIA
$endday=$ndays_do_mes;
#MES
$endmon=$mes_a_cobrar;
#ANO
$endyear=$year;
$end=mktime(23,59,59,$endmon,$endday,$endyear);

$period=0;	#$One_Time_Fee
$price=$cost=$valor;
$created_date=time();
$startdate=mktime(0,0,0,$month,$day,$year);
$servcode=$SERVICE_DEBT;
$servtype=$SERVICE_DEBT;
$desserv=$descricao;
$comment=$mensagem;
$discount=0;
#print "SERVICE_DEBT: $SERVICE_DEBT<br>";

$query_ins="INSERT INTO $PACKAGES_table 
(clientid,created_date,
start,price,discount,period,desserv,servtype,
servcode,cost,
renewdate,balance,comment,lastrenew,end,vendedor
) ";

$query_ins.="VALUES('$clientid','$created_date',
'$startdate','$price','$discount',
'$period','$desserv',
'$servtype','$servcode',
'$cost','$renew','$cost','$comment','$startdate','$end','$id_contabil'
)";
#print "Inserindo data: $query_ins.<br>";
$valorq=mysql_query($query_ins,$db_link);
if( mysql_error($db_link) )
{
	print("*** 1. $query_ins ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
}
$last_packid=mysql_insert_id($db_link);
#print "packid inserido: $last_packid<br>";
return($last_packid);

}
##############################
function get_ips_username($clientid)
{
   global $db_link, $CLIENT_table,$PACKAGES_table;
   $query_ip="SELECT DISTINCTROW PACKAGES.ip_nr,PACKAGES.equipetiq,
	PACKAGES.equipcomodato,PACKAGES.equipcomodato_devol,PACKAGES.username 
   FROM CLIENT,PACKAGES WHERE 
   CLIENT.active='1' AND
   PACKAGES.active='1' AND CLIENT.clientid=PACKAGES.clientid and
   CLIENT.clientid='$clientid' and 
   (PACKAGES.ip_nr !='' or   PACKAGES.username !='' ) ";
   $result=mysql_query($query_ip,$db_link);
   $IPS="";
   if($result)
   {
    while( $row=mysql_fetch_array($result) )
    {
      if(strlen($IPS)>5){$separa = ","; } else $separa="";
      if(strlen($row[ip_nr]) > 5) $IPS .= "$separa$row[ip_nr]";
      if(strlen($row[username]) > 5) $IPS .= "$separa$row[username] ";

      if(strlen($row[equipetiq]) > 1)
      {
	 if( $row[equipcomodato_devol] == 0)	#Equip. em comodato e nao foi devolvido
	 {
	   $IPS .= "(Etiq: $row[equipetiq])";
	 }
      }
    }
   }
   return($IPS);
}
function get_ips_package($clientid,$packid)
{
   global $db_link, $CLIENT_table,$PACKAGES_table;
   $query_ip="SELECT DISTINCTROW PACKAGES.ip_nr,PACKAGES.equipetiq,
	PACKAGES.equipcomodato,PACKAGES.equipcomodato_devol,PACKAGES.username 
   FROM CLIENT,PACKAGES WHERE 
   CLIENT.active='1' AND
   PACKAGES.active='1' AND CLIENT.clientid=PACKAGES.clientid and
   CLIENT.clientid='$clientid' and 
   (PACKAGES.ip_nr !='' or   PACKAGES.username !='' ) ";
   if(strlen($packid) > 0 and $packid > 0)
   {
    $query_ip .=" AND PACKAGES.packid ='$packid' ";
   }
   $result=mysql_query($query_ip,$db_link);
   $IPS="";
   if($result)
   {
    while( $row=mysql_fetch_array($result) )
    {
      if(strlen($IPS)>5){$separa = ","; } else $separa="";
      if(strlen($row[ip_nr]) > 5) $IPS .= "$separa$row[ip_nr]";
      if(strlen($row[username]) > 5) $IPS .= "$separa$row[username] ";

      #if(strlen($row[equipetiq]) > 1)
      #{
      #	 if( $row[equipcomodato_devol] == 0)	#Equip. em comodato e nao foi devolvido
      #	 {
      #	   $IPS .= "(Etiq: $row[equipetiq])";
      #	 }
      #}
    }
   }
   return($IPS);
}
function get_mascara_package($clientid,$packid)
{
   global $db_link, $CLIENT_table,$PACKAGES_table;
   $query_ip="SELECT DISTINCTROW PACKAGES.ip_nr,PACKAGES.equipetiq,
	PACKAGES.mascara,PACKAGES.ip_gateway,
	PACKAGES.equipcomodato,PACKAGES.equipcomodato_devol,PACKAGES.username 
   FROM CLIENT,PACKAGES WHERE 
   CLIENT.active='1' AND
   PACKAGES.active='1' AND CLIENT.clientid=PACKAGES.clientid and
   CLIENT.clientid='$clientid' and 
   (PACKAGES.ip_nr !='' or   PACKAGES.username !='' ) ";
   if(strlen($packid) > 0 and $packid > 0)
   {
    $query_ip .=" AND PACKAGES.packid ='$packid' ";
   }
   $result=mysql_query($query_ip,$db_link);
   $IPS="";
   if($result)
   {
    while( $row=mysql_fetch_array($result) )
    {
      if(strlen($IPS)>5){$separa = ","; } else $separa="";
      if(strlen($row[ip_nr]) > 5) $IPS .= "$separa$row[ip_nr]";
      if(strlen($row[username]) > 5) $IPS .= "$separa$row[username] ";
      $mascara=$row['mascara'];

      #if(strlen($row[equipetiq]) > 1)
      #{
      # if( $row[equipcomodato_devol] == 0)	#Equip. em comodato e nao foi devolvido
      #	 {
      #	   $IPS .= "(Etiq: $row[equipetiq])";
      #	 }
      #}
    }
   } else { $mascara="??"; }
   return($mascara);
}

function get_gateway_package($clientid,$packid)
{
   global $db_link, $CLIENT_table,$PACKAGES_table;
   $query_ip="SELECT DISTINCTROW PACKAGES.ip_nr,PACKAGES.equipetiq,
	PACKAGES.mascara,PACKAGES.ip_gateway,
	PACKAGES.equipcomodato,PACKAGES.equipcomodato_devol,PACKAGES.username 
   FROM CLIENT,PACKAGES WHERE 
   CLIENT.active='1' AND
   PACKAGES.active='1' AND CLIENT.clientid=PACKAGES.clientid and
   CLIENT.clientid='$clientid' and 
   (PACKAGES.ip_nr !='' or   PACKAGES.username !='' ) ";
   if(strlen($packid) > 0 and $packid > 0)
   {
    $query_ip .=" AND PACKAGES.packid ='$packid' ";
   }
   $result=mysql_query($query_ip,$db_link);
   $IPS="";
   if($result)
   {
    while( $row=mysql_fetch_array($result) )
    {
      if(strlen($IPS)>5){$separa = ","; } else $separa="";
      if(strlen($row[ip_nr]) > 5) $IPS .= "$separa$row[ip_nr]";
      if(strlen($row[username]) > 5) $IPS .= "$separa$row[username] ";
      $ip_gateway=$row['ip_gateway'];

      #if(strlen($row[equipetiq]) > 1)
      #{
      # if( $row[equipcomodato_devol] == 0)	#Equip. em comodato e nao foi devolvido
      #	 {
      #	   $IPS .= "(Etiq: $row[equipetiq])";
      #	 }
      #}
    }
   } else { $ip_gateway="??"; }
   return($ip_gateway);
}


function set_email_status($clientid,$status)
{
  global $db_link, $CLIENT_table,$PACKAGES_table;
  $query_email="UPDATE $CLIENT_table SET erro_email='$status' WHERE clientid='$clientid'";
  if(mysql_query($query_email,$db_link)){return(0);}
  if(mysql_error($db_link) )
  {
	print("*** 1. $query_email ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
  }
}
################
function test_ip_allow2($myip,$ips_predio)
{
   $redes_predio=explode(",",$ips_predio);
   $FROM_NET_ALLOW=false;
   foreach ($redes_predio as $k=>$net)
   {
	$net=trim($net);
	$net_array=explode("-",$net);

	#list($net,$mask)=split("/",$k);
	#if(!$net or !$mask){ 
	# print "<br><h3>Erro em config_all: $k, $v</h3><br>"; 
	# exit(); 
	#}
	#print "k = $k, net = $net, $net_array[0]<br>";
	$mask="24";
	$net_array0=$net_array[0]; $net_array_final=$net_array[1];
	$ip_ini=ip2long($net_array[0]); $ip_final=ip2long($net_array[1]);
	$myip_long=ip2long($myip);
	#if (isIPIn($myip,$net_array0,$mask)) {
	#  $FROM_NET_ALLOW=true;
       	#  #echo $net[$k]."<br>\n"; 
	#  ##$FROM_NET .= "$ALLOWED_IPS2[$k] ";
	#}
	if($myip_long >= $ip_ini and  $myip_long <= $ip_final)$FROM_NET_ALLOW=true;
	#print "myip = $myip, k = $k, net = $net, net_array: $net_array[0]<br>";
	#if($FROM_NET_ALLOW)print "FROM_NET_ALLOW = true<br>";
   }
   return($FROM_NET_ALLOW);
}
#########
function count_email_system($email)
{
#include("dbinfo.php");
global $db_link, $USERS_table;

$query_email="SELECT count(email_user) FROM 
	$USERS_table WHERE email_user = '$email'
		AND active='1' ";
$output_email=mysql_query($query_email,$db_link);
if(! $output_email or mysql_error($db_link) )
  {
   print("*** 1. $query_email ***<br>");
   print "\nMysql error:" . mysql_errno($db_link)
   . " : "  . mysql_error($db_link) . "<br>";
   #exit();
   return(0);
  }
$acctcount=mysql_result($output_email,0);
#print "Nr. de mail do sistem: $acctcount";
return($acctcount);

}
#########
function get_campo_users($name,$campo)
{
#include("dbinfo.php");
global $db_link, $USERS_table;
#campo name ==> $id_contabil
$query_email="SELECT $campo FROM 
	$USERS_table WHERE name = '$name'
		AND active='1' ";
$output_email=mysql_query($query_email,$db_link);
if(! $output_email or mysql_error($db_link) )
  {
   print("*** 1. $query_email ***<br>");
   print "\nMysql error:" . mysql_errno($db_link)
   . " : "  . mysql_error($db_link) . "<br>";
   #exit();
   return(0);
  }
$acctcount=mysql_result($output_email,0);
#print "Nr. de mail do sistem: $acctcount";
return($acctcount);

}
####################
function get_cli_id_node($id_node)
{
 global $db_link, $PACKAGES_table;
 ##include_once("dbinfo.php");
 include("dbinfo.php");
 

 $query_act="SELECT count(active) FROM
	$PACKAGES_table WHERE servername_id_node='$id_node' 
	AND active='1' and suspend='0' ";
 $valorq=mysql_query($query_act,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_act ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}
#
##function req_cpanel($packid,$server,$type,$username,$sistema_operacional)
function req_cpanel2($packid,$server,$type,$username,$password,$sistema_operacional)
{
 global $domain_name, $serverurl, $groupo_contabil, $id_contabil;	#, $suspendd2_index;
 global $REVENDA_TIPO2;
 # Mostra link para acesso ao 'CPANEL/PLESK' e 'email' para receber senha.
 ##include_once("dbinfo.php");
 #print "username: $username , senha: $password / $password_mail .";
 if(empty($username) or empty($password) )
 {
  print " ! Falta ou não foi salvo 'username/password' ! "; #exit();
 }

 /************
 Pelo sistema_op/sistema_op_pop $sistema_operacional, podemos
   identificar se é CPANEL, PLESK, ..., se é revenda, ...
 $REVENDA_TIPO2[$sistema_operacional]['MENS'] .. ['IMG'] .. ['REVENDA']
 $ACESSO="Acessa Dominio";
 if($type == "smtp"){ $ACESSO="Acessa CPANEL/Email"; }
 if($type == "www"){ $ACESSO="Acessa CPANEL"; }
 if($server == "wbw.wb.com.br"){ $ACESSO="Acessa Plesk"; }
 *******/
 $ACESSO="Acessa $type " . $REVENDA_TIPO2[$sistema_operacional]['MENS'];
 $LARG=$REVENDA_TIPO2[$sistema_operacional]['WIDTH'];
 if(!$IMG_LOGO=$REVENDA_TIPO2[$sistema_operacional]['IMG'])
 {
  $IMG_LOGO="anthony-unknown.gif"; $LARG="15";
 }

 #print "21. packid: $packid, server: $server"; exit();
 #if(preg_match("/bet.wb.com.br|wbhost\.|wbhost.wb.com.br|vetor.wb.com.br/",$server))
 if(true)
 {
	print <<<EOF
	<img src="images/bullet1.gif"> <a target="_new"
	href="$serverurl/acessa_pack.php?packid=$packid&tipo=$type"> <img 
	border="0" width="$LARG" title="$ACESSO" 
	src="images/$IMG_LOGO"></a>

EOF;

  if($groupo_contabil == 'admin' and !empty($username))
  {
	print <<<EOF
 <img border="0" src="images/bullet1.gif">
 <a target="_new" title="Envia senha para Hostmaster WB"
href="$serverurl/acessa_pack.php?packid=$packid&func=server&envia=email&tipo=$type">
<img src="$serverurl/images/mail1.gif" border="0"></a>

EOF;
  }

  ##print "12. id_contabil: $id_contabil, type: $type<br>"; #type: dominio/www/pop
  ##if($id_contabil == 'pedro' 
  if( preg_match("/admin|adminredew|suporte/i",$groupo_contabil)
	and !empty($username) 
	and ($type == "www" or $type == "smtp")
    )
  {
	/***********
    Encerra o Usuario $username em $server  (packid: $packid,tipo:$type) <br>
    Ver: cpanel/cpanel_terminate_acc.php <br>
     Suspend o Usuario $username em $server <br>
    Ver: cpanel/suspende_ativa_contas_no_whm.php <br>
    Ver: cpanel/cpanel_login.php<br>
    Ver: cpanel/dom_planet.php
    ***********/

    #function cpanel_suspend_domain2($server,$revenda,$domain,$user,
    #    $acao,$reason)
    global $suspenddominio2,$suspendd2, $suspendd2_www, $suspendd2_smtp;
			#Tem que ter esta declaracao
			#Para term a variavl $suspendd2 'onChange'

    $comandos_cpanel="suspender|reativar|killacct|cria";
    ###if(isset($suspendd2_www) and 
    $suspendd2="";
    if(	preg_match("/$comandos_cpanel/i",$suspendd2_www) )
    {
      $type_crt="www";
      if($type == "www")$suspendd2=$suspendd2_www; 
      $username_c=$username;
      $password_c=$password;
    }
    else if(	####isset($suspendd2_smtp) and 
	preg_match("/$comandos_cpanel/i",$suspendd2_smtp) )
    {
      $type_crt="smtp";
      if($type == "smtp")$suspendd2=$suspendd2_smtp;
      $suspendd2=$suspendd2_smtp; 
      $username_c=$username;
      $password_c=$password;
    }
    /***********
    print "22. domain_name: $domain_name,
	suspendd2_www: $suspendd2_www,  suspendd2_smp: $suspendd2_smtp, 
  	suspendd2: $suspendd2, username_c: $username_c, 
	password_c: $password_c, type: $type, type_crt: $type_crt 
	sistema_operacional: $sistema_operacional<br>";
    ********/

    if(empty($username))
    {
	print " ?Falta username? ";
    }
    else if(
	#preg_match("/suspender|reativar|killacct|cria/i",$suspenddominio)
	#or 
	preg_match("/suspender|reativar|killacct|cria/i",
		$suspendd2)
	and $type == $type_crt
    )
    {
	include_once dirname(__FILE__) . "/cpanel/common_cpanel2.php";
	##print "Suspendendo/terminando/criando dominio $username_c ...";
	global $sistema_op, $sistema_op_pop;

	##$contactemail="webmaster@wb.com.br";
	global $CONTACTEMAIL;
	##$resposta=cpanel_suspend_domain2($server,"revenda%$sistema_op%",
	$resposta=cpanel_suspend_domain2($server,$sistema_operacional,
	$domain_name,
	$username_c,
	$password_c,$CONTACTEMAIL,
        $suspendd2,"Inadimplente");

	#Coloca em comments do $PACKAGES_table
	if(strlen($resposta) > 1)
	{ store_comment_pac($packid,$resposta); }
    }

  }
 }
 #else if($server == "rayon.wb.com.br")
 else if(preg_match("/rayon.wb.com.br|mail100.wb.com.br/i",$server))
 {
	print <<<EOF
	>>> <a target="_new" 
	href="$serverurl/acessa_pack.php?packid=$packid&func=server&tipo=$type">
$ACESSO</a>

EOF;
	if($groupo_contabil != 'admin')
	{
	print <<<EOF
&nbsp;&nbsp;&nbsp;
 >>> <a target="_new"
href="$serverurl/acessa_pack.php?packid=$packid&func=server&envia=email&tipo=$type">
<img src="$serverurl/images/mail1.gif" border="0"></a>
EOF;
	}

 }
 else if($server == "wbw.wb.com.br")	#Plesk
 {
	print <<<EOF
	>>> <a target="_new"
	href="$serverurl/acessa_pack.php?packid=$packid&tipo=$type">
$ACESSO</a>

EOF;
	if($groupo_contabil == 'admin')
	{
	print <<<EOF
&nbsp;&nbsp;&nbsp;
 >>> <a target="_new"
href="$serverurl/acessa_pack.php?packid=$packid&func=server&envia=email&tipo=$type">
<img src="$serverurl/images/mail1.gif" border="0"></a>
EOF;
	}

 }

}	#end req_cpanel
############# 
function req_cpanel_escolha($packid,$server,$type,$username)
{
    global $groupo_contabil, $suspendd2_www, $suspendd2_smtp;
    global $cli_revenda_dominio;
    if( ! preg_match("/admin|adminredew|suporte/i",$groupo_contabil) )return(0);
    #if(! preg_match("#admin#i",$groupo_contabil) )return(0);
    $suspendd2_type="suspendd2";
    if($type == "www"){ $suspendd2_type="suspendd2_www"; }
    else if($type == "smtp"){ $suspendd2_type="suspendd2_smtp"; }
    print <<<EOF
	<img border="0" src="images/bullet1.gif">
	<select name="$suspendd2_type" 
		>
	<option value="--" >* Escolha *</option>
EOF;

    if(preg_match("/admin|webdesig/i",$groupo_contabil) 
	)
    {
	print <<<EOF
	<option value="cria">Cria Dom.</option>
EOF;
    }
    else if(preg_match("/webdesig|adminredew|suporte/i",$groupo_contabil) 
	and ! $cli_revenda_dominio
	)
    {
	print <<<EOF
	<option value="cria">Cria Dom.</option>
EOF;
    }

    if(preg_match("/admin|cobranca/i",$groupo_contabil) )
    {
    print <<<EOF
	<option value="suspender">Suspende</option>
	<option value="reativar">Reativa</option>
	<option value="killacct">Termina</option>

EOF;
    }

    print <<<EOF
	</select>
	Confirma? <input type="checkbox" name="confirma_dominio"
    		value="sim" editable onChange="this.form.submit();">

EOF;

}

############# Vale para os dominio wb.com.br, vetor.com.br, ...
function get_var_cpanel_email($dominio)
{
 global $serverurl,  $PROVIDER_CRT ;
 #print "71. serverurl: $serverurl, PROVIDER_CRT: $PROVIDER_CRT .<br>";
 global $host, $logincpanel, $senhacpanel, $cpanel, $serverurl, $PROVIDER_CRT;
 #include_once("/var/www/html/contabil/$PROVIDER_CRT/dbinfo.php");
 #require("dbinfo.php");	#OU: dbinfo_servers.php ?
 require("dbinfo_servers.php");	#db_link --> db_link_s
 #print "72. serverurl: $serverurl, PROVIDER_CRT: $PROVIDER_CRT .<br>";

 #mysql> select packid,clientid,username,password,DOMAIN_NAME,active,WWW,SMTP
 #from PACKAGES where DOMAIN_NAME='cebresnet.psi.br';

 ##$host=$cpanel[$dominio]['host'];
 ##$logincpanel=$cpanel[$dominio]['login'];
 ##$senhacpanel=$cpanel[$dominio]['senha'];

#mysql> select POP,SMTP,username,password from PACKAGES where
#DOMAIN_NAME='vetor.com.br';
#+-----------------+-----------------+----------+----------+
#| POP             | SMTP            | username | password |
#+-----------------+-----------------+----------+----------+
#| vetor.wb.com.br | vetor.wb.com.br | vetor    | N6H5P19  |
#+-----------------+-----------------+----------+----------+
 global $PACKAGES_table, $SERVICE_DOMAIN;
 #Preciso saber onde está o dominio, para localizar o POP, SMTP...
 $query_cp="SELECT POP,SMTP,username,password,username_mail,password_mail
	from $PACKAGES_table where
	DOMAIN_NAME = '$dominio' AND active='1' AND
	servcode='$SERVICE_DOMAIN' ";
 ##$valorq=mysql_query($query_cp,$db_link);
 $valorq=mysql_query($query_cp,$db_link_s);
 #print "valorq ....<br>";
 if( mysql_error($db_link_s) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_s)
	. " : "  . mysql_error($db_link_s) . "<br>";
	exit;
 }

 $num_rows = mysql_num_rows($valorq);
 if($num_rows != 1 )
 {
  print " Erro: Nr. de entradas ($num_rows) entrada(s) encontrada(s) para o dominio $dominio";
  print "<br>dominio: $dominio<br>query: $query_cp<br>";
  exit();
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 #print_r($clientesq); exit();
 
 $host=$clientesq['SMTP']; $cpanel[$dominio]['host']=$host;
 $logincpanel=$clientesq['username']; $cpanel[$dominio]['login']=$logincpanel;
 $senhacpanel=$clientesq['password']; $cpanel[$dominio]['senha']=$senhacpanel;
 ##print "<br>host: $host, logincpanel: $logincpanel, senhacpanel: $senhacpanel"; exit();
 #return(true);
}
function form_cpf_cnpj($exCnpj_cpf)
{
	$exCnpj_cpf=ereg_replace("[^0-9]",'',$exCnpj_cpf);
	$exCnpj_cpf=trim($exCnpj_cpf);
	##print " 1. exCnpj_cpf: $exCnpj_cpf "; #0161178000179
	if(strlen($exCnpj_cpf) > 13)
	{
	#CNPJ
	##print " 2. exCnpj_cpf: $exCnpj_cpf ";
	preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})$/",$exCnpj_cpf,$element2);
	$exCnpj_cpf="$element2[1]" . ".$element2[2]" . 
	".$element2[3]" . "/$element2[4]" . "-$element2[5]";
	}
	else if(strlen($exCnpj_cpf) > 10)
	{
	#CPF  0161178000179
	##print " 3. exCnpj_cpf: $exCnpj_cpf ";
	preg_match("/([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/",$exCnpj_cpf,$element2);
	$exCnpj_cpf= "$element2[1]" . 
	".$element2[2]" . ".$element2[3]" . "/$element2[4]";

	}
return($exCnpj_cpf);

}
#
function createMailto($strEmail){
  #
  #Obfuscate Mailto PHP Code v1.01
  #http://johnhaller.com/jh/useful_stuff/obfuscate_mailto/code/php/
  #
  $strNewAddress = '';
  for($intCounter = 0; $intCounter < strlen($strEmail); $intCounter++){
    $strNewAddress .= "&#" . ord(substr($strEmail,$intCounter,1)) . ";";
  }
  $arrEmail = explode("&#64;", $strNewAddress);
  $strTag = "<script language="."Javascript" . "type="."text/javascript".">\n";
  $strTag .= "<!--\n";
  $strTag .= "document.write('<a href=\"mai');\n";
  $strTag .= "document.write('lto');\n";
  $strTag .= "document.write(':" . $arrEmail[0] . "');\n";
  $strTag .= "document.write('@');\n";
  $strTag .= "document.write('" . $arrEmail[1] . "\">');\n";
  $strTag .= "document.write('" . $arrEmail[0] . "');\n";
  $strTag .= "document.write('@');\n";
  $strTag .= "document.write('" . $arrEmail[1] . "<\/a>');\n";
  $strTag .= "// -->\n";
  $strTag .= "</script><noscript>" . $arrEmail[0] . " at \n";
  $strTag .= str_replace("&#46;"," dot ",$arrEmail[1]) . "</noscript>";
  return $strTag;
}
#
function get_cnpj_cpf($clientid)
{
 global $db_link, $CLIENT_table;
 $query_val="SELECT cnpj_cpf FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_val,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $cnpj_cpf = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($cnpj_cpf[0]);
}
#
function cnpj_cpf_nr($clientid)
{
 #include("functions/validcpfcnpj.php");
 include_once("functions/validarcpfcnpj.php");
 $cnpj_cpf=get_cnpj_cpf($clientid);
 $cnpj_cpf=form_cpf_cnpj($cnpj_cpf);
 #print "cnpj_cpf: $cnpj_cpf";
 return(validate_cpf_cnpj2($cnpj_cpf));

}
#
function boletas_nao_pagas($clientid,$dias_venc_min)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 global $SITE_BOLETA, $PROVIDER_CRT;

 $nr_entradas=0; $nr_entradas_paid=0; $nr_entradas_nao_paid=0;

 #$dias_venc_min=5;	#Vencimento < 5 dias atras
 if($dias_venc_min < 1)$dias_venc_min=1;
 
 $dias_venc_max=186;	#Vencimento > 180 dias atras
 $venc=time() - 86400 * $dias_venc_min;	#Vencimento < 5 dias atras
 $venc_min=time() - 86400 * $dias_venc_max;  #Vencimento > 180 dias atras
 $query_inv="SELECT *
  FROM $INVOICES_table WHERE clientid='$clientid'
  and paid='0' and cancela='0'
  and  due < '$venc' and due > '$venc_min'
  ORDER BY due";
 #print "INVOICES_table: $INVOICES_table, query: $query_inv<br>";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 $return_pg=""; $invoices_abertos="";
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  #print "due: $pg_open[due] , venc: $pg_open[date0] <br>";
  #$return_pg .= "Vencimento: $pg_open[date0], valor: R\$ $pg_open[amount]<br>\n";
  $invnum=$pg_open['invnum'];
  $return_pg .= "Bol. nr: $pg_open[invnum], venc. original: $pg_open[date0_orig]";
  $return_pg .= ", valor: R\$ $pg_open[amount]<br>\n";
  $return_pg .= "<br>\n&nbsp;&nbsp;&nbsp;Para imprimir a boleta acima, clic ";
  $return_pg.= "no link abaixo:<br>\n";

  $return_pg .= "<a target=\"_new\" href=\"";
  $return_pg .= "$SITE_BOLETA/f.php?";
  $return_pg .= "n=$invnum&p=$PROVIDER_CRT&xemail_rec=fatura2@wb.com.br";
  $return_pg .= "\">";
  #$return_pg .= "\">aqui!</a><br>\n";
  $return_pg .="$SITE_BOLETA/f.php?";
  $return_pg .="n=$invnum&p=$PROVIDER_CRT&xemail_rec=fatura2@wb.com.br";
  $return_pg .= "</a><br>\n";
  $return_pg .= "<br>Ou, corte e cole o endereço acima no seu navegador!<br>\n";

  $invoices_abertos .= $invnum . "\n";
  $nr_entradas_nao_paid++;
 }
 $return_nao_pagam=array(
	"nr_entradas_nao_paid" => "$nr_entradas_nao_paid",
	"mensag" => "$return_pg",
	"invoices" => $invoices_abertos
	);
 #return($return_pg);
 return($return_nao_pagam);
}
#
function boletas_nao_pagas2($clientid,$intervalo_dia1,$intervalo_dia2)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 $nr_entradas=0; $nr_entradas_paid=0; $nr_entradas_nao_paid=0;

 #$dias_venc_min=5;	#Vencimento < 5 dias atras
 #$dias_venc_max=180;	#Vencimento > 180 dias atras
 #$venc=time() - 86400 * $dias_venc_min;	#Vencimento < 5 dias atras
 #$venc_min=time() - 86400 * $dias_venc_max;  #Vencimento > 180 dias atras
 $nr_entradas_nao_paid=0;
 $query_inv="SELECT *
  FROM $INVOICES_table WHERE clientid='$clientid'
  and paid='0' and cancela='0'
  and  due < '$intervalo_dia2' and due > '$intervalo_dia1'
  ORDER BY due";
 #print "INVOICES_table: $INVOICES_table, query: $query_inv<br>";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 $return_pg="";
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  #print "due: $pg_open[due] , venc: $pg_open[date0] <br>";
  #$return_pg .= "Vencimento: $pg_open[date0], valor: R\$ $pg_open[amount]<br>\n";
  $return_pg .= "Bol. nr: $pg_open[invnum], venc. original: $pg_open[date0_orig]";
  $return_pg .= ", valor: R\$ $pg_open[amount]<br>\n";
  $nr_entradas_nao_paid++;
 }
 $return_nao_pagam=array(
	"nr_entradas_nao_paid" => "$nr_entradas_nao_paid", "mensag" => "$return_pg"
	);
 #return($return_pg);
 #return($return_nao_pagam);
 return($nr_entradas_nao_paid);
}
#
function boletas_nao_pagas3($clientid,$intervalo_dia1,$intervalo_dia2)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 $nr_entradas=0; $nr_entradas_paid=0; $nr_entradas_nao_paid=0;

 #$dias_venc_min=5;	#Vencimento < 5 dias atras
 #$dias_venc_max=180;	#Vencimento > 180 dias atras
 #$venc=time() - 86400 * $dias_venc_min;	#Vencimento < 5 dias atras
 #$venc_min=time() - 86400 * $dias_venc_max;  #Vencimento > 180 dias atras
 $nr_entradas_nao_paid=0;
 $query_inv="SELECT *
  FROM $INVOICES_table WHERE clientid='$clientid'
  and paid='0' and cancela='0'
  and  due < '$intervalo_dia2' and due > '$intervalo_dia1'
  ORDER BY due";
 ##print "INVOICES_table: $INVOICES_table, query: $query_inv<br>";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 $return_pg="";
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  #print "due: $pg_open[due] , venc: $pg_open[date0] <br>";
  #$return_pg .= "Vencimento: $pg_open[date0], valor: R\$ $pg_open[amount]<br>\n";
  $return_pg .= "Bol. nr: $pg_open[invnum], venc. original: $pg_open[date0_orig]";
  $return_pg .= ", valor: R\$ $pg_open[amount]<br>\n";
  $nr_entradas_nao_paid++;
 }
 $return_nao_pagam=array(
	"nr_entradas_nao_paid" => "$nr_entradas_nao_paid", "mensag" => "$return_pg"
	);
 return($return_pg);
 #return($return_nao_pagam);
 #return($nr_entradas_nao_paid);
}
#
function boletas_enviadas($clientid,$MESES_BOLETAS_ENVIADAS,$MESES_BOLETAS_ENVIADAS_TOTAL)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 global $date_order_Y;
 #if(!isset($MESES_BOLETAS_ENVIADAS) or $MESES_BOLETAS_ENVIADAS < 6)
 #{
 #  $MESES_BOLETAS_ENVIADAS=7;	#7  
 #}
 $nr_entradas=0; $nr_entradas_paid=0; $nr_entradas_nao_paid=0;
 #print "MESES_BOLETAS_ENVIADAS: $MESES_BOLETAS_ENVIADAS";
 
 $dias_venc_min=0;	#Vencimento < 5 dias atras
 $dias_venc_max=$MESES_BOLETAS_ENVIADAS * 30;	#Vencimento > 180 dias atras
 #print "dias: $dias_venc_max";
 
 $venc=time(); 		#- 86400 * $dias_venc_min;	#Vencimento < 5 dias atras
 $venc_min=time() - 86400 * $dias_venc_max;  #Vencimento > 180 dias atras
 $query_inv="SELECT *
  FROM $INVOICES_table WHERE clientid='$clientid'
  and
  (  (due < '$venc' and due > '$venc_min')
  or (due > '$venc' and paid='1')
  )
  ORDER BY due ";	####date0_orig  ";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 $return_pg=""; $valor_recebido=0;
 #| invid | invnum |date | due | date0_orig: vencim. original | date0: novo venc. | amount_rec
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  #print "due: $pg_open[due] , venc: $pg_open[date0_orig] <br>";
  $return_pg .= "Bol. nr: $pg_open[invnum], venc. original: $pg_open[date0_orig], valor cobrado: R\$ $pg_open[amount]";
  if($pg_open['paid'])
  {
   $return_pg .= ", valor recebido: R\$ $pg_open[amount_rec]";	
   $valor_recebido += $pg_open['amount_rec'];
   
   #, em $pg_open[datepaidre]";
   if($pg_open['amount_rec'] > 0)
   {
     $RECEBIDO_paid=date($date_order_Y,$pg_open['datepaid']); 
     $RECEBIDO=date($date_order_Y,$pg_open['datepaidre']);
   }
   else $RECEBIDO=" - ";
   $return_pg .= ", em $RECEBIDO\n";
   $nr_entradas_paid++;
  }
  else
  {
    $return_pg .= ", valor recebido: R\$ $pg_open[amount_rec]";
    $RECEBIDO=" - ";
    $return_pg .= ", em $RECEBIDO\n";
    $nr_entradas_nao_paid++;
  }
 }
 $return_pagam=array("nr_entradas_paid" => "$nr_entradas_paid", 
	"nr_entradas_nao_paid" => "$nr_entradas_nao_paid", "mensag" => "$return_pg"
	);
 $valor_recebido_s=sprintf ("%.2f",$valor_recebido);
 if($MESES_BOLETAS_ENVIADAS_TOTAL)$return_pg .= "\n *** Total: R\$ $valor_recebido_s\n";
 return($return_pg);
}
#
function mark_email_cobranca_sent($clientid)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 global $date_order_Y, $DIAS_MIN_ENVIAR_EMAIL_COB;
 $agora=time();
 $passado=$agora - $DIAS_MIN_ENVIAR_EMAIL_COB*24*60*60;
 #$query_cli="UPDATE $CLIENT_table set segs_sent_emailc='$agora' WHERE clientid='$clientid'
 # ";
 $query_cli="UPDATE $CLIENT_table set segs_sent_emailc='$agora' WHERE clientid='$clientid'
  and segs_sent_emailc < '$passado'";

 $valorq=mysql_query($query_cli,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cli ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $num_rows=0;
 if($valorq)$num_rows = mysql_affected_rows($db_link);
 return($num_rows);
}
##########
function mark_email_cobranca_sent2($clientid,$dias_min)
{
 #Usado especificamente para enviar cobranca bloqueando.
 # Considera que já mandou um email antes.
 # O novo email e bloqueio ocorre em um tempo menor que
 #  $DIAS_MIN_ENVIAR_EMAIL_COB .
 #  $DIAS_MIN_ENVIAR_EMAIL_COB_E_SUSPENDER < $DIAS_MIN_ENVIAR_EMAIL_COB
 #  $dias_min pode ser =   $DIAS_MIN_ENVIAR_EMAIL_COB_E_SUSPENDER
 #
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 global $date_order_Y, $DIAS_MIN_ENVIAR_EMAIL_COB,
	$DIAS_MIN_ENVIAR_EMAIL_COB_E_SUSPENDER;
 $agora=time();
 ##$passado=$agora - $DIAS_MIN_ENVIAR_EMAIL_COB*24*60*60;
 $passado=$agora - $dias_min*24*60*60;
 #$query_cli="UPDATE $CLIENT_table set segs_sent_emailc='$agora' WHERE clientid='$clientid'
 # ";
 $query_cli="UPDATE $CLIENT_table set segs_sent_emailc='$agora' WHERE clientid='$clientid'
  and segs_sent_emailc < '$passado'";

 $valorq=mysql_query($query_cli,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cli ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $num_rows=0;
 if($valorq)$num_rows = mysql_affected_rows($db_link);
 return($num_rows);
}
##########
function mark_email_sent($clientid,$campo)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
 global $date_order_Y, $DIAS_MIN_ENVIAR_EMAIL_MARKETING, 
	$DIAS_MIN_ENVIAR_EMAIL_COB;
 $agora=time();
 $passado=$agora - $DIAS_MIN_ENVIAR_EMAIL_MARKETING*24*60*60;
 #$query_cli="UPDATE $CLIENT_table set segs_sent_emailc='$agora' WHERE clientid='$clientid'
 # ";
 $query_cli="UPDATE $CLIENT_table set 
  $campo='$agora' WHERE clientid='$clientid'
  and $campo < '$passado'";

 $valorq=mysql_query($query_cli,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cli ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $num_rows=0;
 if($valorq)$num_rows = mysql_affected_rows($db_link);
 return($num_rows);
}
##########
function get_dominios($clientid)
{
 global $db_link, $CLIENT_table, $PACKAGES_table, $SERVICE_DOMAIN,
	$date_order_Y;
 $query_cp="SELECT *
	from $PACKAGES_table where
	active='1' AND
	servcode='$SERVICE_DOMAIN' AND clientid='$clientid' ";
 $valorq=mysql_query($query_cp,$db_link);
 #print "valorq ....<br>";
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $DOMINIOS=""; $nr_entradas_dom=0;
 $num_rows = mysql_num_rows($valorq);
 while($clientesq = mysql_fetch_array($valorq,MYSQL_BOTH) )
 {
   #print $clientesq['DOMAIN_NAME'];
   $created_date=$clientesq['created_date'];
   $created_date= date("$date_order_Y",$created_date);
   $DOMINIOS .= $clientesq['DOMAIN_NAME'] . " desde " .
	$created_date . "\n";
   $nr_entradas_dom++;
 }
 $return_dominios=array(
	"nr_entradas_dom" => "$nr_entradas_dom", "mensag" => "$DOMINIOS"
	);
 return($return_dominios);
}
###############

function send_msg_attach_confirma($sender,$destinatario,$assunto,$body,$attach,
	$fileatttype,$fileattname)
{
 #Confirma recebimento da mensagem
 /**********
 $sendmail = "/usr/sbin/sendmail -t -f $sender";
 $fd = popen($sendmail, "w");
 fputs($fd, "To: $destinatario\r\n");
 fputs($fd, "From: $sender\r\n");
 fputs($fd, "Disposition-Notification-To: $sender\r\n");
 fputs($fd, "Subject: $assunto\r\n");
 fputs($fd, "X-Mailer: PHP mailer\r\n\r\n");
 fputs($fd, $body);
 pclose($fd);
 ***********/

 #$to = "$name <$email>"; 
 $to = "pedro@wb.com.br"; 
 #$from = "John-Smith <john.smith@domain.com>"; 
 $from = "pedro@wb.com.br"; 
 $subject = "Here is your attachment";
 $subject = $assunto;
 #$fileatt = "../folhetos/roubo_dominio.pdf";
 $fileatt = "$attach";
 ##$fileatttype = "application/pdf"; 
 ##$fileattname = "newname.pdf";
 $headers = "From: $from";

 $file = fopen( $fileatt, 'rb' ); 
 $data = fread( $file, filesize( $fileatt ) ); 
 fclose( $file );
 
 $semi_rand = md5( time() ); 
 $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
  
 $headers .= "\nMIME-Version: 1.0\n" . 
             "Content-Type: multipart/mixed;\n" . 
             " boundary=\"{$mime_boundary}\"";
  
 $message = "This is a multi-part message in MIME format.\n\n" . 
         "--{$mime_boundary}\n" . 
         "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . 
         "Content-Transfer-Encoding: 7bit\n\n" .
	 $body . "\n\n";
	 ##.   $message . "\n\n";

###	Content-Transfer-Encoding: quoted-printable
### 	image/jpeg;
###       "Content-Disposition: inline\n\n" .
  
 $data = chunk_split( base64_encode( $data ) );
          
 $message .= "--{$mime_boundary}\n" . 
 "Content-Type: {$fileatttype};\n" . 
 " name=\"{$fileattname}\"\n" . 
 "Content-Disposition: attachment;\n" . 
 " filename=\"{$fileattname}\"\n" . 
 "Content-Transfer-Encoding: base64\n\n" . 
 $data . "\n\n" . 
 "--{$mime_boundary}--\n"; 


#Content-Type: text/plain; charset=ISO-8859-1
#Content-Transfer-Encoding: quoted-printable
#Content-Disposition: inline

 
 #print " $to, $subject, $message, $headers<br>-----------<br>";
 if( mail( $to, $subject, $message, $headers ) )
 {
    #echo "<p>The email was sent.</p>";
    return(true);
 }
 else 
 { 
   #echo "<p>There was an error sending the mail.</p>";
   return(false);
 }

}
################
function get_mensal_pred($id_predio)
{
#global $conexao;
$linha_edita_pred=array();
##include_once ("includes/inc_conexao.php");
include_once ("includes/inc_variaveis_conexao.php");

$consulta_pred = "SELECT * from adodbedificio where id_predio = '$id_predio'
	LIMIT 1 ";
$resultado_pred = mysql_query($consulta_pred,$conexao);
####$num_rows = mysql_num_rows($resultado_pred);
if(mysql_errno($conexao) or ! $resultado_pred)
{
	print("*** $consulta_pred ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita_pred=mysql_fetch_array($resultado_pred,MYSQL_BOTH);
# instalacao | bonus_instal | mensalidade | bonus_mensal
$retorna=$linha_edita_pred['mensalidade'];
#return($retorna);
return($linha_edita_pred);

}
##############
function get_variavel_predio($variavel,$id_predio)
{
global $conexao;
$linha_edita_pred=array();
##include_once ("includes/inc_conexao.php");
include_once ("includes/inc_variaveis_conexao.php");

$consulta_pred = "SELECT $variavel from adodbedificio where id_predio = '$id_predio'
	LIMIT 1 ";
$resultado_pred = mysql_query($consulta_pred,$conexao);
####$num_rows = mysql_num_rows($resultado_pred);
if(mysql_errno($conexao))	# or ! $resultado_pred)
{
	print("*** $consulta_pred ***<br>");
	print "\nMysql error:" . mysql_errno($conexao)
	. " : "  . mysql_error($conexao) . "<br>";
	exit();
}
$linha_edita_pred=mysql_fetch_array($resultado_pred,MYSQL_BOTH);
# instalacao | bonus_instal | mensalidade | bonus_mensal
$retorna=$linha_edita_pred[$variavel];
return($retorna);

}
##############
function block_client_serv($clientid)
{
 # Inicialmente de envia_email_inadimplentes.php
 #  Bloqueia servicos
 #
 global $CLIENT_table,$PACKAGES_table,$radcheck_table,$db_link,$now_date,$id_contabil;
 $agora=time();
 $query="UPDATE $CLIENT_table SET ";
 $query .= " suspendservfuturo='1',suspendserv='1', suspendservfrom='$agora' ";
 $query .= " , suspend_inadimp='1' ";
 $query .=<<<EOF
 , comments=CONCAT(comments,"[$now_date-$id_contabil]..Cliente inadimplente: suspenso automatico..\n")
  where clientid="$clientid" limit 1
EOF;
 #update CLIENT set comments=CONCAT(comments,"\nteste\n") where
 #clientid='5067' limit 1;
 #print "query: $query";
 if(!mysql_query($query,$db_link))
 {
  print("$query<br>");
  print "\nMysql error:" . mysql_errno($db_link)
  . " : "  . mysql_error($db_link) . "<br>";
  exit();
 }
 #print " $PACKAGES_table,$clientid,$radcheck_table ";
 block_pac($PACKAGES_table,$clientid,$radcheck_table);

}
##########block cobranca de clientes.
function block_client_cob($clientid,$quando_cob)
{
 # Inicialmente de envia_email_inadimplentes.php
 #
 global $CLIENT_table,$PACKAGES_table,$radcheck_table,$db_link,$now_date,$id_contabil;
 $agora=time();
 if($quando_cob < $agora)
 {
   die("Erro: bloqueio de cobrança: $quando_cob < agora: $agora");
 }
 $query="UPDATE $CLIENT_table SET ";
 $query .= " suspendcobfuturo='1',  suspendcobfrom='$quando_cob' ";
 $query .=<<<EOF
 , comments=CONCAT(comments,"[$now_date-$id_contabil]..Cliente inadimplente: suspenso de cobranca ..\n")
  where clientid="$clientid" limit 1
EOF;
 #update CLIENT set comments=CONCAT(comments,"\nteste\n") where
 #clientid='5067' limit 1;
 #print "query: $query";
 if(!mysql_query($query,$db_link))
 {
  print("$query<br>");
  print "\nMysql error:" . mysql_errno($db_link)
  . " : "  . mysql_error($db_link) . "<br>";
  exit();
 }
 #print " $PACKAGES_table,$clientid,$radcheck_table ";
 ##block_pac($PACKAGES_table,$clientid,$radcheck_table);

}
##############
function get_email_nr($email)
{
 global $db_link, $CLIENT_table;
 $query_quota="SELECT count(email) FROM 
	$CLIENT_table WHERE email='$email' and active='1' ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}
##################
function get_cnpj_cpf_nr($cnpj_cpf)
{
 global $db_link, $CLIENT_table;
 $cnpj_cpf=ereg_replace("[^0-9]",'',$cnpj_cpf);
 $query_quota="SELECT count(cnpj_cpf) FROM 
	$CLIENT_table WHERE cnpj_cpf='$cnpj_cpf' ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}
function get_cnpj_cpf_nr_max($cnpj_cpf)
{
 global $db_link, $CLIENT_table;
 $cnpj_cpf=ereg_replace("[^0-9]",'',$cnpj_cpf);
 $query_quota="SELECT cnpj_cpf_nr_permit FROM 
	$CLIENT_table WHERE cnpj_cpf='$cnpj_cpf' order by clientid limit 1";
 #print "query_quota: $query_quota";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq['cnpj_cpf_nr_permit']);
}
##################
function compute_mensal_now($clientid)
{
 #Retorna a mensalidade de um cliente somando os pacotes, tendo cuidado com
 # o campo 'period'.
 global $db_link, $db_link_INVOICES, $PACKAGES_table, $date_order, $words_date;
 if(! $clientid) return(0);
 $now=time();
 $MSG_COB="\n";
 #############
 $query="SELECT start,cost,period,clientid,packid 
  FROM $PACKAGES_table WHERE clientid='$clientid' AND
  active='1'
  AND start < '$now' AND (end = '0' OR end > '$now')
  ";
 ###  AND parentpack='0'
 $throughput=mysql_query($query,$db_link);
 #if(! $throughput)
 if(mysql_error($db_link))
 {
	print("*** 2. $query ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
 }

 $custo_tot=0;
 $data=$words_date[1];
 ##print "Mes: $data\n";
 $tem_pacotes=0; 
 while($spit=mysql_fetch_array($throughput,MYSQL_BOTH))
 {
   $DEBUG=false;
   $start_ini=date("$date_order",$spit['start']);
   $start_vect=explode("/",$start_ini);  $start_mes=$start_vect[1];
   $periodo=$spit['period'];
   $periodicidade_ori=$start_mes  % $periodo;
   $periodicidade=$words_date[1] % $periodo;
   $custo_crt=$spit['cost'];
   if($periodicidade == $periodicidade_ori)
   {
    if($DEBUG){ print " servico_tipo=$spit[$servico_tipo] "; }
    $custo_tot +=$custo_crt;
    $tem_pacotes++;
    #Verificar pacotes 'reg. de dominio/SSL' e envia um email para
    # webmaster@wb.com.br 
   }
   else 
   {
     #print "Sem somar $custo_crt por que a periodicidade 
     #	foi $periodo == $periodicidade_ori == $start_mes $start_ini .<br>";
   }
 } #End while 
 ####################
 $retorna=array('0' => "$tem_pacotes", '1' => "$custo_tot");
 #print_r($retorna);
 return($retorna);

}
###################
function get_international_date($data_human)
{
 #  $data_human = 28/01/2008, retorna: 2008/01/28
 $date_h=explode("/",$data_human);
 if(strlen($date_h[0]) < 2)$date_h[0]="0" . $date_h[0];
 if(strlen($date_h[1]) < 2)$date_h[1]="0" . $date_h[1];
 if(strlen($date_h[2]) < 3)$date_h[2]="20" . $date_h[2];
 return($date_h[2] . "/" . $date_h[1] . "/". $date_h[0]);
}
function get_day_of_week($data_human_international)
{
  global $dia_semana;
  $data_arr=explode("/",$data_human_international);
  $day =$data_arr[2];$month=$data_arr[1];$year=$data_arr[0];
  $utime = mktime (1,1,1,$month,$day,$year);
  #$dia_semana=mcal_day_of_week  ($year,$month,$day );
  $dia_semana0=date('w',$utime);   //  0 for Sunday through 6 for Saturday
  #print $data_human_todos[$data_hh] . "(" .
  #$dia_semana[$dia_semana0] . ") - ";
  return($dia_semana[$dia_semana0]);
}
###########
function get_client_attr($clientid)
{
 global $db_link, $CLIENT_table;
 $query_quota="SELECT * FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq);

}
##################
function get_variavel_client($clientid,$variavel)
{
 # Ver function get_variavel_predio($variavel,$id_predio)
 global $db_link, $CLIENT_table;
 $query_quota="SELECT $variavel FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 $retorna=$clientesq [$variavel];
 #print "retorna: $retorna";
 return($retorna);
}
function put_variavel_client($clientid,$campo,$variavel)
{
 # Ver function get_variavel_predio($variavel,$id_predio)
 global $db_link, $CLIENT_table;
 $query_quota="UPDATE 
	$CLIENT_table set $campo='$variavel' WHERE clientid='$clientid' limit 1 ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 #$clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 #$retorna=$clientesq [$variavel];
 #return($retorna);
}
#
function concat_variavel_client($clientid,$campo,$variavel)
{
 # Ver function get_variavel_predio($variavel,$id_predio)
 global $db_link, $CLIENT_table;
 # , comments=CONCAT(comments,"[$now_date-$id_contabil]..Cliente inadimplente: suspenso automati
 $query_quota="UPDATE 
	$CLIENT_table set $campo=CONCAT($campo,'$variavel') 
	WHERE clientid='$clientid' limit 1 ";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error($db_link) )
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
}
##################
function get_credito_attr($cnpj_cpf)
{
 global $db_link_CRED, $CLIENT_table,$CLIENT_CREDITO_table;
 $query_credito="SELECT * FROM 
	$CLIENT_CREDITO_table WHERE cnpj_cpf_cred='$cnpj_cpf' ";
 $valorq=mysql_query($query_credito,$db_link_CRED);
 if(mysql_error($db_link_CRED) )
 {
	print("*** 1. $query_credito ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRED)
	. " : "  . mysql_error($db_link_CRED) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq);
}
##################
function insert_credito_attr($cnpj_cpf,$attributos)
{
 global $PROVIDER_CRT, $db_link_CRED, $CLIENT_table,$CLIENT_CREDITO_table;
 #clientid_cred, cnpj_cpf_cred,data_consulta,
 #id_contabil_cred,cred_ok,empresa_cred, comment_cred
 $clientid_cred=$attributos['clientid_cred'];
 $cnpj_cpf_cred=$attributos['cnpj_cpf_cred']; 
 $nome_cred=$attributos['nome_cred'];
 $data_nasc_cred=$attributos['data_nasc_cred'];
 $data_consulta=$attributos['data_consulta'];
 $id_contabil_cred=$attributos['id_contabil_cred'];
 $cred_ok=$attributos['cred_ok'];
 $empresa_cred=$attributos['empresa_cred'];
 $comment_cred=$attributos['comment_cred'];
 $cod_resposta=$attributos['cod_resposta'];
 $nome_mae=$attributos['nome_mae'];
 $cheque_sem_fundo=$attributos['cheque_sem_fundo'];
 $telefones=$attributos['telefones'];

 #telefone_cpf : 'Telefones ligados ao CPF/CNPJ',
 $telefone_cpf = ereg_replace("[^0-9]",'',$telefones);
 ##print "PROVIDER_CRT: $PROVIDER_CRT"; exit(0);
 $phone=get_campo_cli_prov($PROVIDER_CRT,$clientid_cred,'phone');
 $phone= ereg_replace("[^0-9]",'',$phone);
 $telefone_cpf .= $phone;
 
 $query_credito="INSERT INTO $CLIENT_CREDITO_table
  (clientid_cred, cnpj_cpf_cred,data_consulta,
   id_contabil_cred,cred_ok,empresa_cred, comment_cred,
   cod_resposta,nome_mae,cheque_sem_fundo,data_nasc_cred,
   nome_cred,telefones,telefone_cpf
  )
  VALUES('$clientid_cred', '$cnpj_cpf_cred','$data_consulta',
    '$id_contabil_cred','$cred_ok','$empresa_cred', '$comment_cred',
    '$cod_resposta','$nome_mae','$cheque_sem_fundo','$data_nasc_cred',
    '$nome_cred','$telefones','$telefone_cpf'
    )";
 $valorq=mysql_query($query_credito,$db_link_CRED);
 if(mysql_error($db_link_CRED) )
 {
	print("*** 1. $query_credito ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRED)
	. " : "  . mysql_error($db_link_CRED) . "<br>";
	exit;
 }
}
##################
function update_credito_attr($cnpj_cpf,$attributos)
{
 global $PROVIDER_CRT, $db_link_CRED, $CLIENT_table,$CLIENT_CREDITO_table;
 #clientid_cred, cnpj_cpf_cred,data_consulta,
 #id_contabil_cred,cred_ok,empresa_cred, comment_cred
 $clientid_cred=$attributos['clientid_cred'];
 $cnpj_cpf_cred=$attributos['cnpj_cpf_cred']; 
 $nome_cred=$attributos['nome_cred'];

 $data_nasc_cred=$attributos['data_nasc_cred'];
 $data_consulta=$attributos['data_consulta'];
 $id_contabil_cred=$attributos['id_contabil_cred'];
 $cred_ok=$attributos['cred_ok'];
 $empresa_cred=$attributos['empresa_cred'];
 $comment_cred=$attributos['comment_cred'];
 $cod_resposta=$attributos['cod_resposta'];
 $nome_mae=$attributos['nome_mae'];
 $cheque_sem_fundo=$attributos['cheque_sem_fundo'];
 $telefones=$attributos['telefones'];

 #telefone_cpf : 'Telefones ligados ao CPF/CNPJ',
 $telefone_cpf = ereg_replace("[^0-9]",'',$telefones);
 
 #print "PROVIDER_CRT: $PROVIDER_CRT"; exit(0);
 $phone=get_campo_cli_prov($PROVIDER_CRT,$clientid_cred,'phone');
 $phone= ereg_replace("[^0-9]",'',$phone);
 $telefone_cpf .= $phone;

 $query_credito="UPDATE $CLIENT_CREDITO_table
  set 
  clientid_cred='$clientid_cred',data_consulta='$data_consulta',
   id_contabil_cred='$id_contabil_cred',cred_ok='$cred_ok',
   empresa_cred='$empresa_cred', comment_cred='$comment_cred',
   cod_resposta='$cod_resposta',nome_mae='$nome_mae',
   cheque_sem_fundo='$cheque_sem_fundo',data_nasc_cred='$data_nasc_cred',
   nome_cred='$nome_cred',telefones='$telefones',
   telefone_cpf='$telefone_cpf'
   WHERE cnpj_cpf_cred='$cnpj_cpf_cred' limit 1 ";

 $valorq=mysql_query($query_credito,$db_link_CRED);
 if(mysql_error($db_link_CRED) )
 {
	print("*** 1. $query_credito ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRED)
	. " : "  . mysql_error($db_link_CRED) . "<br>";
	exit;
 }
}
##################
function get_last_variavel_credito($cnpj_cpf,$variavel)
{
 global $db_link_CRED, $CLIENT_table,$CLIENT_CREDITO_table;
 #$query_credito="SELECT max(id_cred) FROM 
 #	$CLIENT_CREDITO_table WHERE cnpj_cpf_cred='$cnpj_cpf' ";
 $query_credito="SELECT $variavel FROM 
	$CLIENT_CREDITO_table WHERE cnpj_cpf_cred='$cnpj_cpf'
	ORDER BY id_cred DESC limit 1 ";

 $valorq=mysql_query($query_credito,$db_link_CRED);
 if(mysql_error($db_link_CRED) )
 {
	print("*** 1. $query_credito ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRED)
	. " : "  . mysql_error($db_link_CRED) . "<br>";
	exit;
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 return($clientesq[0]);

}
function get_cred_put_client($clientid,$cnpj_cpf)
{
 #Get 'comment_cred' de CREDITO_CLIENT e coloque em comments de CLIENT.
 if(!$clientid or !$cnpj_cpf)die("clientid: $clientid, cnpj_cpf: $cnpj_cpf");
 $var_cnpj_cpf=array();
 $var_cnpj_cpf=get_credito_attr($cnpj_cpf);
 $credito_cli=$var_cnpj_cpf['cred_ok'];
 $comment_cred=$var_cnpj_cpf['comment_cred'];
 $comment_cred=stripslashes($comment_cred);
 ##$comment_cred=addslashes($comment_cred);
 $nome_mae=$var_cnpj_cpf['nome_mae'];
 if(strlen($nome_mae)>5)
 {
  $comment_cred .= "\nMae: " . $nome_mae . "\n";
  ##put_variavel_client($clientid,"nome_da_mae",$nome_mae);	
  #Colocar 'nome_da_mae' ao criar cliente ou manualmente
 }
 $cheque_sem_fundo=$var_cnpj_cpf['cheque_sem_fundo'];
 if(strlen($cheque_sem_fundo)>5)$comment_cred .= "\nCheques sem fundo: " .  $cheque_sem_fundo;
 $comment_cred=clean_string($comment_cred) . "\n";
 $comment_cred=addslashes($comment_cred);
 put_variavel_client($clientid,"credito_cli",$credito_cli);
 concat_variavel_client($clientid,"comments",$comment_cred);

}
function clean_string($strin2clean)
{
 $strin2clean=trim($strin2clean);
 $strin2clean=preg_replace ("/\\x0a/","\n",$strin2clean);
 $strin2clean=preg_replace ("/\n\n|\r\r|\r\n|\s+\n/","\n",$strin2clean);
 $strin2clean=preg_replace ("/\n\n/","\n",$strin2clean);
 $strin2clean=preg_replace ("/\t/"," ",$strin2clean);
 $strin2clean=preg_replace ("/\s\s+/"," ",$strin2clean);
 return($strin2clean);
}
######
function get_nr_dominios($clientid)
{
 global $db_link, $SERVICE_DOMAIN;
 $agora=time();
 $query_serv_susp="SELECT DOMAIN_NAME from PACKAGES
        where
	clientid='$clientid' AND
        PACKAGES.active='1' and 
	(PACKAGES.suspend = '1' and
	 PACKAGES.suspendedfrom > '0' and PACKAGES.suspendedfrom < '$agora')
        AND
        (PACKAGES.end='0' OR PACKAGES.end > '$agora') 
	and servcode='$SERVICE_DOMAIN'
	";
 $num_rows_susp=0;
 if($result_cli3=mysql_query($query_serv_susp,$db_link) )
 { $num_rows_susp = mysql_num_rows($result_cli3); }
 #print "$query_serv_a -- :$num_rows_a: <br>"; print_r($result_cli3);
 $query_serv_a="SELECT DOMAIN_NAME from PACKAGES
        where
	clientid='$clientid' AND
        PACKAGES.active='1' and 
        (PACKAGES.end='0' OR PACKAGES.end > '$agora') 
	and servcode='$SERVICE_DOMAIN'
	";
 $num_rows_a=0;
 if($result_cli3=mysql_query($query_serv_a,$db_link) )
 { $num_rows_a = mysql_num_rows($result_cli3); }
 #print "$query_serv_a -- :$num_rows_susp: <br>"; print_r($result_cli3);
 #print "clientid: $clientid =>$num_rows_a/$num_rows_susp";
 return("$num_rows_a/$num_rows_susp");
}
###########
function get_data_int($data)
{
  if(preg_match("#(..)/(..)/(.*)$#",$data,$p_nasc) )
  {
    $data_int="$p_nasc[3]-$p_nasc[2]-$p_nasc[1]";
  }
  return($data_int);
}
##########
function get_clientid_inv($nosso_nr)
{
 #Get ID client do invnum em $INVOICES_table.
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;

 $query_inv="SELECT clientid
  FROM $INVOICES_table WHERE invnum='$nosso_nr'";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 #$return_pg="0"; 
 $nr_inv=0;
 $return_clientid="0";
 while($cli_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  #$return_pg = $cli_open['clientid'];
  $return_clientid = $cli_open['clientid'];
  $nr_inv++;
 }
 #print "clientid: $return_pg";
 #if($nr_inv > 1)$return_pg=-1;
 if($nr_inv > 1)
 {
   #$return_clientid=-1;
   $MSG="Aleta: Mais de uma boleta com o mesmo nr.: $nosso_nr<br>";
   die($MSG);
 }
 #return($return_pg);
 return($return_clientid);
}
######
function get_campo_inv($nosso_nr,$campo)
{
 #Get 'campo' do invnum em $INVOICES_table.
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;

 $query_inv="SELECT $campo
  FROM $INVOICES_table WHERE invnum='$nosso_nr' ";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
 $return_pg=""; $nr_inv=0;
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  $return_pg .= $pg_open[$campo];
  $nr_inv++;
 }
 #print "<br>query_inv: $query_inv , $campo: $return_pg<br>";
 #Retorno UM valor do campo ou 'false' se existirem varias invoices com o mesmo nr.
 if($nr_inv > 1)$return_pg=-1;
 return($return_pg);
}
######
#function set_campo_inv($nosso_nr,$campo,$valor)
function set_campo_inv($invid,$campo,$valor)
{
 #CONCAT 'campo' + 'valor' do invid em $INVOICES_table.
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;

 $query_inv=<<<EOF
UPDATE 
 $INVOICES_table set 
 $campo=CONCAT($campo,"$valor - ")
 WHERE invid='$invid' limit 1
EOF;
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
}
######
function concat_campo_inv($invid,$campo,$valor)
{
 #CONCAT 'campo' + 'valor' do invid em $INVOICES_table.
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table ;

 $query_inv=<<<EOF
UPDATE $INVOICES_table set 
 $campo=CONCAT($campo,"$valor")
 WHERE invid='$invid' limit 1
EOF;
 ##print "query_inv: $query_inv";
 $valorq=mysql_query($query_inv,$db_link_INVOICES);
 if( mysql_error($db_link_INVOICES) )
 {
	print("*** 1. $query_val ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_INVOICES)
	. " : "  . mysql_error($db_link_INVOICES) . "<br>";
	exit;
 }
}
######

function calc_mens_data($clientid,$data_br)
{
   #Calcula a mensal. em uma data
   global $db_link, $PACKAGES_table, 
	$db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
   global $date_order;

   $nova_data_cob_a=explode("/",$data_br);
   $dia_cob=$nova_data_cob_a[0]; $mes_cob=$nova_data_cob_a[1];
   $ano_cob=$nova_data_cob_a[2];
   $data_br_segs=mktime(0,0,0,$mes_cob,$dia_cob,$ano_cob);
   
   #Ver periodicidade aqui
   #periodo: 1=mensal, 3=trimestral, 6=semestral = $period_array[]
   #$periodicidade=$words_date[1] % $periodo;
   #		servcode/servtype
   $servico_tipo="servcode";
   $query="SELECT start,cost,period,$servico_tipo,clientid,packid 
    FROM $PACKAGES_table WHERE clientid='$clientid' AND
    active='1'
    AND start < '$data_br_segs' AND (end = '0' OR end > '$data_br_segs')
    ";
   ###  AND parentpack='0'
   $throughput=mysql_query($query,$db_link);
   if(mysql_error($db_link) )	##! $throughput)
   {
  	print("*** 2. $query ***<br>");
  	print "\nMysql error:" . mysql_errno($db_link)
  	. " : "  . mysql_error($db_link) . "<br>";
  	exit();
   }

   $custo_tot=0;
   $data=$nova_data_cob_a[1];	#$words_date[1];	#Ver periodicidade MES
   $tem_pacotes=false; 
   while($spit=mysql_fetch_array($throughput,MYSQL_BOTH))
   {
     $start_ini=date("$date_order",$spit[start]);
     $start_vect=explode("/",$start_ini);  $start_mes=$start_vect[1];
     $periodo=$spit['period'];
     $periodicidade_ori=$start_mes  % $periodo;
     #$periodicidade=$words_date[1] % $periodo;
     $periodicidade=$nova_data_cob_a[1] % $periodo;
     $custo_crt=$spit['cost'];
     # Calcular a cobranca da periodicidade de tal forma que
     # seja cobrada em 01/11 (1 de nov.) quando o pacote for criado
     # em 10/10 (10 out). ou apos a cobranca do mes anterior.
     if($periodicidade == $periodicidade_ori){
       if($DEBUG){ print " servico_tipo=$spit[$servico_tipo] "; }
       $custo_tot +=$custo_crt;
       #Verificar pacotes 'reg. de dominio/SSL' e envia um email para
       # webmaster@wb.com.br 
       ##print "Clientid: $clientid, custo_tot: $custo_tot\n<br>";
     }
     else {
       #print "Sem somar $custo_crt por que a periodicidade 
       #	foi $periodo == $periodicidade_ori == $start_mes $start_ini .<br>";
     }
     $tem_pacotes=true;
   } #End while 
   if(!$tem_pacotes)
   {
	$query="SELECT clientid,valormen,valormen2 FROM $CLIENT_table WHERE ACTIVE='1' 
	AND suspend='0' AND clientid='$clientid' ";
	##$query_limite_venc ";
	$output_val=mysql_query($query,$db_link);
	#if(!mysql_error)
	if(mysql_errno($db_link) )
	{
		print("*** 1. $query ***<br>");
		print "\nMysql error:" . mysql_errno($db_link)
		. " : "  . mysql_error($db_link) . "<br>";
		exit();
	}

	while($row = mysql_fetch_array($output_val))	# output_val
	{
	 $clientid=$row['clientid'];
	 $valormen=$row['valormen'];
	 $valormen2=$row['valormen2'];
	}

	$custo_tot=$valormen2;	#get $valormen do $CLIENT_table

   }
   ######################
   return($custo_tot);
}
######
function calc_mens_em_periodo($clientid,$data_br)	##,$data_br1,$data_br2)
{
   #Calcula a mensal. media tendo em conta o periodo de cada pacote em uma
   #	data determinada ($data_br).
   global $db_link, $PACKAGES_table, 
	$db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
   global $date_order;

   $nova_data_cob_a=explode("/",$data_br);
   $dia_cob=$nova_data_cob_a[0]; $mes_cob=$nova_data_cob_a[1];
   $ano_cob=$nova_data_cob_a[2];
   $data_br_segs=mktime(0,0,0,$mes_cob,$dia_cob,$ano_cob);
   
   #Ver periodicidade aqui
   #periodo: 1=mensal, 3=trimestral, 6=semestral = $period_array[]
   #$periodicidade=$words_date[1] % $periodo;
   #		servcode/servtype
   $servico_tipo="servcode";
   $query="SELECT start,cost,period,$servico_tipo,clientid,packid 
    FROM $PACKAGES_table WHERE clientid='$clientid' AND
    active='1'
    AND start < '$data_br_segs' AND (end = '0' OR end > '$data_br_segs')
    ";
   ###  AND parentpack='0'
   $throughput=mysql_query($query,$db_link);
   if(mysql_error($db_link) )	##! $throughput)
   {
  	print("*** 2. $query ***<br>");
  	print "\nMysql error:" . mysql_errno($db_link)
  	. " : "  . mysql_error($db_link) . "<br>";
  	exit();
   }

   $custo_tot=0;
   $data=$nova_data_cob_a[1];	#$words_date[1];	#Ver periodicidade MES
   $tem_pacotes=false; 
   while($spit=mysql_fetch_array($throughput,MYSQL_BOTH))
   {
     $start_ini=date("$date_order",$spit[start]);
     $start_vect=explode("/",$start_ini);  $start_mes=$start_vect[1];
     $periodo=$spit['period'];	#Mensal: periodo=1, semestral: periodo=6, ...
     #$periodo=0: periodo unico
     if($periodo == 0)$periodo=1;
     /******
     $periodicidade_ori=$start_mes  % $periodo;
     #$periodicidade=$words_date[1] % $periodo;
     $periodicidade=$nova_data_cob_a[1] % $periodo;
     */
     $custo_crt=$spit['cost'];
     $custo_tot +=$custo_crt/$periodo;
     /******
     # Calcular a cobranca da periodicidade de tal forma que
     # seja cobrada em 01/11 (1 de nov.) quando o pacote for criado
     # em 10/10 (10 out). ou apos a cobranca do mes anterior.
     if($periodicidade == $periodicidade_ori){
       if($DEBUG){ print " servico_tipo=$spit[$servico_tipo] "; }
       $custo_tot +=$custo_crt;
       #Verificar pacotes 'reg. de dominio/SSL' e envia um email para
       # webmaster@wb.com.br 
       ##print "Clientid: $clientid, custo_tot: $custo_tot\n<br>";
     }
     else {
       #print "Sem somar $custo_crt por que a periodicidade 
       #	foi $periodo == $periodicidade_ori == $start_mes $start_ini .<br>";
     }
     ******/

     $tem_pacotes=true;
   } #End while 
   if(!$tem_pacotes)
   {
	$query="SELECT clientid,valormen,valormen2 FROM $CLIENT_table WHERE ACTIVE='1' 
	AND suspend='0' AND clientid='$clientid' ";
	##$query_limite_venc ";
	$output_val=mysql_query($query,$db_link);
	#if(!mysql_error)
	if(mysql_errno($db_link) )
	{
		print("*** 1. $query ***<br>");
		print "\nMysql error:" . mysql_errno($db_link)
		. " : "  . mysql_error($db_link) . "<br>";
		exit();
	}

	while($row = mysql_fetch_array($output_val))	# output_val
	{
	 $clientid=$row['clientid'];
	 $valormen=$row['valormen'];
	 $valormen2=$row['valormen2'];
	}

	$custo_tot=$valormen2;	#get $valormen do $CLIENT_table

   }
   ######################
   return($custo_tot);
}
######
function fatura_cli($clientid,$desde_data_br)	##,$data_br1,$data_br2)
{
   #Calcula a mensal. media tendo em conta o periodo de cada pacote em uma
   #	data determinada ($data_br).
   #select datepaidre,amount_rec from INVOICES where invnum='00101009430923' limit 1;
   global $db_link, $PACKAGES_table, 
	$db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
   global $date_order;

   /******
   $nova_data_cob_a=explode("/",$data_br);
   $dia_cob=$nova_data_cob_a[0]; $mes_cob=$nova_data_cob_a[1];
   $ano_cob=$nova_data_cob_a[2];
   $data_br_segs=mktime(0,0,0,$mes_cob,$dia_cob,$ano_cob);
   *****/
   $data_br_segs=time() - 365*24*60*60;	#Tempo de 1 ano atras
   $query="SELECT  sum(amount_rec) from $INVOICES_table
	 where clientid='$clientid' and datepaidre > '$data_br_segs' 
	 AND paid='1' ";

   $throughput=mysql_query($query,$db_link);
   if(mysql_error($db_link) )	##! $throughput)
   {
  	print("*** 2. $query ***<br>");
  	print "\nMysql error:" . mysql_errno($db_link)
  	. " : "  . mysql_error($db_link) . "<br>";
  	exit();
   }

   $custo_tot=0;
   $data=$nova_data_cob_a[1];	#$words_date[1];	#Ver periodicidade MES
   $tem_pacotes=false; 
   while($spit=mysql_fetch_array($throughput,MYSQL_BOTH))
   {
     $custo_crt=$spit['0'];
     #print "custo_crt: $custo_crt";
   } #End while 
   ######################
   return($custo_crt);
}
########
function get_nr_inv($invnum,$pay_type)
{
   global $db_link, $PACKAGES_table, 
	$db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
   global $date_order;
   $query_inv_count="SELECT count(invnum) from $INVOICES_table 
	where invnum='$invnum' 
	AND pay_type='$pay_type' ";
   $output_inv=mysql_query($query_inv_count,$db_link_INVOICES);
   $invcount=mysql_result($output_inv,0);
   return($invcount);
}
########
function get_next_nr_inv_bb($clientid,$CONVENIO_BB,$ano_cob_Y,$mes_cob_0,$pay_type)
{
   global $db_link, $PACKAGES_table, 
	$db_link_INVOICES, $CLIENT_table, $INVOICES_table ;
   global $date_order;

   $timestamp = mktime (0, 0, 0, $mes_cob_0, 1, $ano_cob_Y);
   $ano_cob_Y_1=$ano_cob_Y % 10;
   $time = getdate($timestamp);
   $yday = $time['yday'];
   $yday = $yday + 1;
   $yday =  substr("000" . $yday,-3);
   $nosso_cc_nr0="$ano_cob_Y_1$yday";
   $nosso_cc_nr0 = substr("000000000000000" . $nosso_cc_nr0,-4); 
   ##print "1. nosso_cc_nr0: $nosso_cc_nr0 - ";
   $clientid_str=substr("000000" .  $clientid,-6);
   $nosso_cc_nr = $nosso_cc_nr0 . $clientid_str;
   $nosso_cc_nr = $CONVENIO_BB . $nosso_cc_nr;
   ##print "1. nosso_cc_nr: $nosso_cc_nr - ";
   $invcount=get_nr_inv($nosso_cc_nr,$pay_type);
   while($invcount > 0)
   {
     $yday++;
     $yday =  substr("000" . $yday,-3);
     $nosso_cc_nr0="$ano_cob_Y_1$yday";
     $nosso_cc_nr0 = substr("000000000000000" . $nosso_cc_nr0,-4);
     $nosso_cc_nr = $CONVENIO_BB . $nosso_cc_nr0 . $clientid_str;
     $invcount=get_nr_inv($nosso_cc_nr,$pay_type);
   }
   return($nosso_cc_nr);
}
########
//calculate years of age (input string: YYYY-MM-DD)
function birthday ($birthday){
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if ($day_diff < 0 || $month_diff < 0)
      $year_diff--;
    return $year_diff;
}
###########
function get_login_senha_cpanel($server,$revenda,$campo)
{
 #$logincpanel=get_login_senha_cpanel($server,$revenda,"username");
 #$senhacpanel=get_login_senha_cpanel($server,$revenda,"password");
 #Ver 	req_cpanel($packid,$smtp,"dominio",$username);
 global $SERVICES_ADMIN_SERV;
 global $PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 include("dbinfo_servers.php");		#Se include_once : da erro.
 ##system ("ls -asl dbinfo.php");
/**********
mysql> select servername,username,password from PACKAGES where
username='redewbn';
+-----------------------+----------+--------------+
| servername            | username | password     |
+-----------------------+----------+--------------+
| wbhost.wb.com.br:2086 | redewbn  | eta2007ferro |
+-----------------------+----------+--------------+
********/

 ##$query_cp="SELECT  servername,username,password 
 $query_cp="SELECT $campo
	from $PACKAGES_table where
	servtype = '$SERVICES_ADMIN_SERV'
	AND servername = '$server:2086' AND revenda like '%$revenda%'
	AND username != 'root' AND username != 'admin'
	AND active='1' AND suspend='0' 
	limit 1 ";
	# Faca desc e escolha a ultima revenda cadastrada.
 #Limit a 1 revendedor: ou o escolhido ou o primeiro.
 #
 $valorq=mysql_query($query_cp,$db_link_s);
 ##print "<br>query_cp: $query_cp, valorq: $valorq ....<br>";
 if( mysql_error($db_link_s) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_s)
	. " : "  . mysql_error($db_link_s) . "<br>";
	exit;
 }

 $num_rows = mysql_num_rows($valorq);
 if($num_rows != 1 )
 {
  ##print " 11. query ($query_cp) Erro: Nr. de entradas ($num_rows) entrada(s) 
  ##	encontrada(s) para $server,$revenda,$campo";
  print "<br>[common.php] linha __LINE__ 11. Erro: Nr. de entradas ($num_rows) entrada(s) 
	encontrada(s) para $server,$revenda,$campo, $query_cp<br>";
  print "<br>Verifique se a revenda está cadastrada!<br>";
  exit();
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 #print_r($clientesq); exit();
 #print "<br>112. $server,$revenda,$campo ==> $clientesq[$campo] ..."; exit();
 return($clientesq[$campo]);

}
############
function get_param_revenda($revenda,$campo)
{
 #$logincpanel=get_login_senha_cpanel($server,$revenda,"username");
 #$senhacpanel=get_login_senha_cpanel($server,$revenda,"password");
 #Ver 	req_cpanel($packid,$smtp,"dominio",$username);
 global $SERVICES_ADMIN_SERV;
 global $PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 #include_once("dbinfo_servers.php");		#Banco de dado dos servidores
 include("dbinfo_servers.php");		#Banco de dado dos servidores

/**********
mysql> SELECT servername,username,password from PACKAGES where  username
like '%redewbn%' AND active='1' AND suspend='0' ;
********/

 ###	AND servername = '$server:2086' AND revenda like '%$revenda%'
 $query_cp="SELECT $campo
	from $PACKAGES_table where
	servtype = '$SERVICES_ADMIN_SERV' 
	AND username = '$revenda'
	AND active='1' AND suspend='0' ";
 $valorq=mysql_query($query_cp,$db_link_s);
 #print "valorq ....<br>";
 if( mysql_error($db_link_s) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_s)
	. " : "  . mysql_error($db_link_s) . "<br>";
	exit;
 }

 #print_r($valorq);
 $num_rows = mysql_num_rows($valorq);
 if($num_rows != 1 )
 {
  print " 1. query ($query_cp) Erro: Nr. de entradas ($num_rows) entrada(s) 
	encontrada(s) para $revenda,$campo";
  exit();
 }
 $clientesq = mysql_fetch_array($valorq,MYSQL_BOTH);
 #print "2. $server,$revenda,$campo ==> $clientesq[$campo]"; exit();
 return($clientesq[$campo]);

}
############

function get_attr_dominio($dominio)
{
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 #include_once("dbinfo.php");
 include("dbinfo.php");
 

 $query_cp="SELECT t1.first,t2.* FROM $CLIENT_table AS t1,$PACKAGES_table AS t2 
  WHERE 
 t1.clientid=t2.clientid AND 
 t2.DOMAIN_NAME = '$dominio' ";
 ##GROUP BY ${CLIENT_table}.clientid";
 ##print "query: $query_cp<br>";
 $result=mysql_query($query_cp,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $nr_result=0;
 while($row = mysql_fetch_array($result,MYSQL_BOTH))
 {
	$nr_result++;
	$ACTIVE="";
	if(!$row[active])$ACTIVE="*** DESATIVADO ***";
	if($row[suspend])$ACTIVE .="*** SUSPENSO ***";
	print <<<EOF
	<a class="pay" target="_blank" href="$serverurl/tools.php?clientid=$row[clientid]"
	>$row[first] | www: $row[WWW] | smtp: $row[SMTP] $ACTIVE</a>
EOF;
 }
 if(!$nr_result)
 {
   #print " *** Cliente não encontrado! *** "; 
   return(false);
 }
 return(true);
}
######################
function show_val_sel($nome_sel,$value_arr,$crt_val)
{
print <<<EOF
\n<select name='$nome_sel'>
EOF;

 foreach ($value_arr as $line_num => $type)
 {
   print "<option";
   if ($type == $crt_val)echo " selected ";
   print "> $type";
   next($value_arr);
 }
 print " </select>\n";
}
#################
function show_val_sel_index($nome_sel,$value_arr,$crt_val)
{
#Mostra um select declarado em um $value_arr.
print <<<EOF
\n<select name='$nome_sel'>
EOF;

 foreach ($value_arr as $line_num => $type)
 {
   print "<option";
   if ($line_num == $crt_val)echo " selected ";
   print " value=\"$line_num\" > $line_num";
   next($value_arr);
 }
 print " </select>\n";
}
#################
function show_val_sel_index2nome($nome_sel,$value_arr,$crt_val,$servidor_crt)
{
#show_val_sel_index2nome("sistema_op",$REVENDA_TIPO2,$sistema_op,"$www");
#Mostra as revendas ($value_arr = $REVENDA_TIPO2) /sistemas op. 
#	e um servidor onde está ($www,$smtp)
#
#print "XXXXXXXXXXXXXXX";
#print_r($value_arr); 
global $SERVIDOR_ATTR;
##print "crt_val: $crt_val ==";
if(!$crt_val)	#$crt_val=$servidor_crt;
{
 #print "?? crt_val = 'revenda'- Tem que pegar no bd ??";
 #mysql> SELECT username,sistema_op,revenda,revendedor,revendedor_pop from PACKAGES where servername like 'conta46.wb.com.br:%' ;
 #$crt_val=$SERVIDOR_ATTR[$servidor_crt]['SIST_OP_REVENDA'];
 
}

##print "-- $nome_sel -- 'crt_val=$crt_val' -- $servidor_crt ---";

print <<<EOF
\n<select name='$nome_sel'>
EOF;

 foreach ($value_arr as $line_num => $type)
 {
   print "<option";
   if ($line_num == $crt_val)echo " selected ";
   print " value=\"$line_num\" > ";	#$line_num";
   print $value_arr[$line_num]['MENS'];
   ###print " : $line_num : $crt_val ";
   #print_r ($value_arr[$line_num]);	#[$line_num];
   next($value_arr);
 }
 print " </select>\n";  ##print_r($value_arr); print "ZZ";
}
###########
function store_comment_pac($packid,$resposta)
{
 #Concat $resposta ao comments de PACKAGES, com data e id_contabil
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 global $id_contabil, $now_date;
 ##include_once("dbinfo.php");
 $query_cp=<<<EOF
UPDATE $PACKAGES_table set 
 comment=CONCAT(comment,"[$now_date-$id_contabil] $resposta\n")
 where packid='$packid' limit 1
EOF;
 $result=mysql_query($query_cp,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }

}
#
##function seleciona_revendedor($revendedor,$sistema_op,$WWW_SERVER,$SMTP_SERVER)
function seleciona_revendedor($campo_revendedor,$revendedor,$sistema_op,$CRT_SERVER)
{
 global $BAN_REVENDA, $SERVIDOR_ATTR;
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 global $REVENDA_TIPO2;
 ##print_r($SERVIDOR_ATTR);
 $WWW_SERVER=$CRT_SERVER;

#Mostre as revendas para escolher. Proibir 'root', 'admin', 'administrator',
#		'administrador'

##$BAN_REVENDA=array("root" => '1',"admin" => '1',
##		"administrator" => '1',"administrador" => '1');

#mysql> select username from PACKAGES where revenda like
#'%revenda%linux%';

 global $SERVIDOR_ATTR;
 #print " ==> sistema_op: $sistema_op <== ";
 #print "revendedor: $revendedor --";
 if(!$revendedor or empty($revendedor) ) 
 {
  ##print "ZZZ";
  $revendedor=$SERVIDOR_ATTR[$CRT_SERVER]['REVENDEDOR'];
  ##print "revendedor: $revendedor ";
 }
 #print " 1. sistema_op: $sistema_op ";
 #if(!$sistema_op)
 #{
 # #$sistema_op=$SERVIDOR_ATTR[$CRT_SERVER]['SIST_OP_REVENDA'];
 # #Pega no banco de dados
 #}

 #print " 2. sistema_op: $sistema_op == CRT_SERVER: $CRT_SERVER, revendedor: '$revendedor' ==";


 /********************* Revendedores  19-5-2008
 #################  get_revendedores($CRT_SERVER) de  common_servers.php
 # $REVENDA_TIPO2[$sistema_operacional]['MENS'] .. ['IMG'] .. ['REVENDA']
 ##include_once("dbinfo.php");
 $query_cp=<<<EOF
SELECT username from $PACKAGES_table where
EOF;
# revenda like '%revenda%$sistema_op%' AND
#EOF;
##if($WWW_SERVER == $SMTP_SERVER)
##{
## $query_cp .=" AND servername like '%$WWW_SERVER%' ";
##}
 $query_cp .="  servername  like '$CRT_SERVER:%' ";
 ##print $query_cp;
 $result=mysql_query($query_cp,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 ################### Revendedores
 ********/
 ##include_once("common_servers.php");
 $result=get_revendedores($CRT_SERVER);

 ##print_r($BAN_REVENDA);
 #print "Revendedores: "; print_r($result); print ".";
 #print "sistema_op: $sistema_op ."; print_r($row);
 print " \n<br>Revendedor: <select name=\"$campo_revendedor\" >\n";
 $revendedor_existe=false;
 #$row=mysql_fetch_array($result,MYSQL_BOTH); 
 while($row = mysql_fetch_array($result,MYSQL_BOTH))
 {
   #print " --XXXXXXXXXXX $row[username] "; #exit();
   if($BAN_REVENDA[$row['username']])continue;	
	#Nao aceita 'root','admin',.. como revenda.
   # Sá aceita sistema op. no status 'revenda'
   ##print " -- ljs ";
   $revenda_bd=$row['revenda'];
   $sistema_op2=$sistema_op; if(strlen($sistema_op) < 1)$sistema_op2=$revenda_bd;
   if(! $REVENDA_TIPO2[$sistema_op2]['REVENDA'])continue;
   $revendedor_existe=true;
   print "<option value=$row[username] ";
   if($revendedor == $row['username'])print " selected ";
   print ">$row[username]";
   #print $REVENDA_TIPO2[$sistema_op]['MENS'] . " - " . 
   #	$REVENDA_TIPO2[$sistema_op]['REVENDA'];
   print "</option>";

 }
 print "</select>\n";
 if(!$revendedor_existe)
 print "O revendedor ($revendedor_existe) de $CRT_SERVER aparece se 
 estiver cadastrado como servidor com  a revenda, login e senha.";
}
########
##function get_entry_from_pac($packid)
function get_pac_packid($packid)
{
 # Retorna uma entrada (array) referente a $packid de $PACKAGES_table;
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 #$query_pac="SELECT * from $PACKAGES_table WHERE
 #	(suspend='1' or active='0') AND clientid='$clientnum'
 #	AND  equipcomodato='1' AND equipcomodato_devol='0'
 #	";

 $query_cp=<<<EOF
SELECT * from $PACKAGES_table where  packid='$packid' 
EOF;
##AND servcode='$SERVICE_DOMAIN'
#  AND suspend='0' or active='1'
#EOF;
 $result=mysql_query($query_cp,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 $row = mysql_fetch_array($result,MYSQL_BOTH);
 return($row);
}
#
function get_pac_clientid_campo($clientid,$campo)
{
 # Retorna uma lista de entradas (array) do $campo de $PACKAGES_table;
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 #$query_pac="SELECT * from $PACKAGES_table WHERE
 #	(suspend='1' or active='0') AND clientid='$clientnum'
 #	AND  equipcomodato='1' AND equipcomodato_devol='0'
 #	";

 $query_cp=<<<EOF
SELECT $campo from $PACKAGES_table where clientid='$clientid' 
 AND suspend='0' AND active='1'
EOF;
##AND servcode='$SERVICE_DOMAIN'
#  AND suspend='0' or active='1'
#EOF;
 ##print "query_cp: $query_cp, db_link: $db_link ";  exit();
 $result=mysql_query($query_cp,$db_link);
 if( mysql_error($db_link) )
 {
	print("*** 1. $query_cp ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
 }
 ##$row = mysql_fetch_array($result,MYSQL_BOTH);
 $resultado="";
 while($row=mysql_fetch_array($result,MYSQL_BOTH))
 {
  if(empty($row[$campo]))continue;
  #print_r($row); #exit();
  if(!empty($resultado))$resultado .= ", ";
  $resultado .= $row[$campo];
 }
 return($resultado);
}
###########
function send_email_dominio($packid)
{
 ######### Send email de dominio para o cliente
 include_once("include/inc_mensagens.php");
 global $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link;
 global $REVENDA_TIPO2, $id_contabil, $id_users;
 #print "<br>Tabelas: $CLIENT_table,$PACKAGES_table, $SERVICE_DOMAIN, $db_link,
 #	$REVENDA_TIPO2, packid: $packid <br>";
 ##$row=get_entry_from_pac($packid);
 $row=get_pac_packid($packid);
 #print_r($row);
 #die();
 $nome_cli=get_nome($row['clientid']);
 $email_cli=get_email($row['clientid']);
 ###print "Cliente: $nome_cli, email: $email_cli, email2tecnico: $row[email2tecnico]"; die();
 /*******
 my $query = "SELECT $optC,SYNCED,WWW,POP,clientid,MX_RECORD,
	username,password,FTP,suspend,dontsync,envia_email,banda_vol,
	revendedor,sistema_op,revendedor_pop,sistema_op_pop,
	email2tecnico,DOMAIN_NAME, username_mail,password_mail,
	revendedor_pop
	from $optT where 
	SYNCED & $opt_Y != '$opt_Y'
        and $optC != '' and active='1' and suspend='0' ";
 *****/
 if(!$row['envia_email'])return(0);
 $domain_name=$row['DOMAIN_NAME'];
 $clientid=$row['clientid'];
 $email2tecnico=$row['email2tecnico'];
 $username=$row['username'];
 $password=$row['password'];
 $username_mail=$row['username_mail'];
 $password_mail=$row['password_mail'];
 $www=$row['WWW'];	# WWW | FTP | SMTP | POP | WEB  |
 $smtp=$row['SMTP'];
 $pop=$row['POP'];
 $ftp=$row['FTP'];
 $sistema_op=$row['sistema_op'];
 $sistema_op_pop=$row['sistema_op_pop'];

 $SUBJECT="Painel de controle para $domain_name";
 $FROM0="info@wb.com.br";
 if(strlen($email2tecnico)>5){ $SEND_MSG_TO=$email2tecnico; }
 else $SEND_MSG_TO=$email_cli;
 ##$SEND_MSG_TO .=",pedro@wbhost.com.br,pedro@wb.com.br";	#"diretoria@wb.com.br";
 ##$SEND_MSG_TO .="\nBCC: pedro@wbhost.com.br,pedro@wb.com.br";	#"diretoria@wb.com.br";
 $SEND_MSG_2WB ="pedro@wbhost.com.br,pedro@wb.com.br";	#"diretoria@wb.com.br";

 $DATA_HOJE=date("d/m/Y H:i");

 $MSG_HEADER_HTML0=<<<EOF
<HTML><HEAD><TITLE>
$SUBJECT
</TITLE>
<META content="text/html; charset=iso-8859-1" http-equiv=Content-Type>
<LINK REL=STYLESHEET HREF="styles/account.css" TYPE="text/css">
</HEAD>
<BODY>

EOF;

 $MSG_HEADER_DATA=<<<EOF


Rio de Janeiro, $DATA_HOJE


EOF;

 ##$REVENDA_TIPO2["MENS","IMG", "PORTA", "WIDTH" , "REVENDA"
 #$BODY=$MSG_HEADER_HTML0;
 $BODY=$MSG_HEADER_DATA;
 $BODY .= $MSG_PARA_QUEM;
 #
 ##$BODY .= $MENSAG =>  $MENSAG_DOM_RAYON 
 ###$BODY .= $estatistica;
 ##$BODY .= $MSG_VEXIM;   Caso: VEXIM
 ##$BODY .= $PLESK;
 ##$BODY .= $CPANEL;
 ##$BODY .= $WEBMAIL_RAYON;
 ##$BODY .= $WEBMAIL_PLESK;
 ##$BODY .= $WEBMAIL_CPANEL;
 $BODY .= $MSG_GERAL;	##VEXIM_WBHOST; 	##VEXIM

 $BODY .= $MSG_TAIL;

 $BODY = ereg_replace("__clientid__","$clientid",$BODY);
 $BODY = ereg_replace("__nome_cli__","$nome_cli",$BODY);
 $BODY = ereg_replace("__domain_name__","$domain_name",$BODY);
 $BODY = ereg_replace("__NOVODOMINIO__","$domain_name",$BODY);
 $BODY = ereg_replace("__POP_DOMAIN__","$pop",$BODY);
 $BODY = ereg_replace("__WWW__","$www",$BODY);

 if(preg_match("/cpanel/i",$REVENDA_TIPO2[$sistema_op_pop]['MENS']) )
 {
  $BODY = ereg_replace("__WEBMAIL__","$WEBMAIL_CPANEL",$BODY);
 }
 else if(preg_match("/plesk/i",$REVENDA_TIPO2[$sistema_op_pop]['MENS']) )
 {
  $BODY = ereg_replace("__WEBMAIL__","$WEBMAIL_PLESK",$BODY);
 }
 else
 {
  $BODY = ereg_replace("__WEBMAIL__","$WEBMAIL_RAYON",$BODY);
 }

	#$WEBMAIL_PLESK, .. $WEBMAIL_RAYON, $CPANEL, $PLESK
	#__PAINEL_DE_CONTROLE__
 ##print "Revenda: " . $REVENDA_TIPO2[$sistema_op]['MENS'] . "<br>";

 if(preg_match("/plesk/i",$REVENDA_TIPO2[$sistema_op]['MENS']) )
 {
  $BODY = ereg_replace("__PAINEL_DE_CONTROLE_WWW__","$PLESK_WWW",$BODY);
  ##  Acesse o site antes da publicação:
  $BODY = ereg_replace("__ANTES_DE_PUBLICAR__","$PLESK_WWW_ANTES_PUBLICAR",$BODY);
  $BODY = ereg_replace("__domain_name__","$domain_name",$BODY);
  $BODY = ereg_replace("__NOVODOMINIO__","$domain_name",$BODY);
  $BODY = ereg_replace("__WWW__","$www",$BODY);
 }
 if(preg_match("/plesk/i",$REVENDA_TIPO2[$sistema_op_pop]['MENS']) )
 {
  $BODY = ereg_replace("__PAINEL_DE_CONTROLE_MAIL__","$PLESK_MAIL",$BODY);
 }
 if(preg_match("/cpanel/i",$REVENDA_TIPO2[$sistema_op]['MENS']) )
 {
  $BODY = ereg_replace("__PAINEL_DE_CONTROLE_WWW__","$CPANEL_WWW",$BODY);
  $BODY = ereg_replace("__ANTES_DE_PUBLICAR__","$CPANEL_WWW_ANTES_PUBLICAR",$BODY);
  $BODY = ereg_replace("__domain_name__","$domain_name",$BODY);
  $BODY = ereg_replace("__NOVODOMINIO__","$domain_name",$BODY);
  $BODY = ereg_replace("__WWW__","$www",$BODY);
 } 
 if(preg_match("/cpanel/i",$REVENDA_TIPO2[$sistema_op_pop]['MENS']) )
 {
  $BODY = ereg_replace("__PAINEL_DE_CONTROLE_MAIL__","$CPANEL_MAIL",$BODY);
 }

 #Se nenhum... apague
 $BODY = ereg_replace("__PAINEL_DE_CONTROLE_WWW__","",$BODY);
 $BODY = ereg_replace("__PAINEL_DE_CONTROLE_MAIL__","",$BODY);
 #

 $BODY = ereg_replace("__username__","$username",$BODY);
 $BODY = ereg_replace("__password__","$password",$BODY);
 $BODY = ereg_replace("__SENHA_DOMINIO__","$password",$BODY);
 $BODY = ereg_replace("__ANTES_DE_PUBLICAR__","",$BODY);
 ##print "username_mail: $username_mail, password_mail: $password_mail";

 if(empty($username_mail))$username_mail=$username;
 if(empty($password_mail))$password_mail=$password;
 $BODY = ereg_replace("__username_mail__","$username_mail",$BODY);
 $BODY = ereg_replace("__password_mail__","$password_mail",$BODY);


 #Dominio na rayon: $MENSAG_DOM_RAYON:
 ###$BODY = ereg_replace("__URL_WEBMAIL__","$url_ebmail",$BODY);

 $BODY = ereg_replace("__domain_name__","$domain_name",$BODY);
 $BODY = ereg_replace("__NOVODOMINIO__","$domain_name",$BODY);
 $id_contabil2=substr($id_contabil,0,3);
 #$id_users=get_id_from_users($id_contabil);
 $BODY .= "\n(a) $id_users:$id_contabil2\n";
 $BODY = ereg_replace("\n\n","\n",$BODY);
 $BODY_HTML=ereg_replace("\n","<br>\n",$BODY);

 #send_mailx_html($FROM0,$SEND_MSG_TO,$SUBJECT,$BODY_HTML);
 send_mailx($FROM0,$SEND_MSG_TO,$SUBJECT,$BODY);
 send_mailx($FROM0,$SEND_MSG_2WB,$SUBJECT,$BODY);

}
####################
function generatePassword($length=9, $strength=4) 
{
  # Gera password
  #http://snippetsnap.com/snippets/5003-PHP-password-generator
  #
  $vowels = 'aeuy';
  $consonants = 'bdghjmnpqrstvz';
  if ($strength & 1) {
  $consonants .= 'BDGHJLMNPQRSTVWXZ';
  }
  if ($strength & 2) {
  $vowels .= "AEUY";
  }
  if ($strength & 4) {
  $consonants .= '23456789';
  }
  if ($strength & 8) {
  $consonants .= '@#$%';
  }
  
  $password = '';
  $alt = time() % 2;
  srand(time());
  for ($i = 0; $i < $length; $i++) 
  {
   if ($alt == 1) 
   {
    $password .= $consonants[(rand() % strlen($consonants))];
    $alt = 0;
   }
   else 
   {
    $password .= $vowels[(rand() % strlen($vowels))];
    $alt = 1;
   }
  }
  return $password;
}
##############
function mysql_insert_array($table, $data, $password_field = "") 
{
 ##http://snippetsnap.com/snippets/556-PHP-Insert-Data-Into-MySQL-Table-Using-An-Array
 ##PHP: Insert Data Into MySQL Table Using An Array
foreach ($data as $field=>$value) {
$fields[] = '' . $field . '';

if ($field == $password_field) {
$values[] = "PASSWORD('" . mysql_real_escape_string($value) . "')";
} else {
$values[] = "'" . mysql_real_escape_string($value) . "'";
}
}
$field_list = join(',', $fields);
$value_list = join(', ', $values);

$query = "INSERT INTO " . $table . " (" . $field_list . ") VALUES (" .
$value_list . ")";

return $query;
}
#
function get_domain_status($dominio)
{
if(preg_match("/\.br/i",$dominio) )
{
 $STATUS=`whois -h registro.br $dominio|grep -i status`;
 if(preg_match("/hold/i",$STATUS))
 $STATUS .= " Se 'on-hold' a FAPESP (REGISTRO.BR) suspendeu(congelou) o dominio.";
}
else
$STATUS=`whois $dominio|grep -i "expires on"`;

return($STATUS);

}
function change_clientid($clientid,$newclientid,$comment_new)
{
  ##include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link, $CLIENT_table, $NOVOS_CLIENTES, $NOVOS_CLIENTES2, $NOVOS_CLIENTES3;

  $clientid=trim($clientid);
  $clientid = ereg_replace("[^0-9]",'',$clientid);
  if(strlen($clientid) < 1)die("Clientid: $clientid ???");
  if(!get_nome($clientid) )die ("O clientid ja existe!<br>");
  if($newclientid < $NOVOS_CLIENTES or $newclientid > $NOVOS_CLIENTES3)
  die ("Os clientes perdidos/em duvida estao na faixa >= $NOVOS_CLIENTES e <= $NOVOS_CLIENTES3 <br>");

  $query_pac="UPDATE $CLIENT_table set 
	clientid='$newclientid',comments=CONCAT(comments,'\n$comment_new')
	where clientid='$clientid'
	and active='1'
	limit 1";
  #print "query_pac: $query_pac"; exit();
  $result_pac=mysql_query($query_pac,$db_link);
  ####if(!mysql_query($query2,$db_link) )
  if(mysql_errno($db_link))
  {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
  }
  change_packid($clientid,$newclientid,$comment_new);
}
#
function change_packid($clientid,$newclientid,$comment_new)
{
  ##include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link, $CLIENT_table, $NOVOS_CLIENTES, $NOVOS_CLIENTES2, $NOVOS_CLIENTES3;
  global $PACKAGES_table;
  $MAX_TRANSFER_PACK=20;
 
  $clientid=trim($clientid);
  $clientid = ereg_replace("[^0-9]",'',$clientid);
  if(strlen($clientid) < 1)die("Clientid: $clientid ???");
  if(get_nome($clientid) )die ("O clientid NAO existe!<br>");
  #if($newclientid < $NOVOS_CLIENTES2)
  if($newclientid < $NOVOS_CLIENTES or $newclientid > $NOVOS_CLIENTES3)
  die ("Os clientes perdidos/em duvida estao na faixa >= $NOVOS_CLIENTES e <= $NOVOS_CLIENTES2 <br>");

  $nr_pacotes=get_nrpack_in_clientid($clientid);
  if($nr_pacotes > $MAX_TRANSFER_PACK)print "*** Nr pacotes do cliente: $nr_pacotes. 
  So estamos transferindo $MAX_TRANSFER_PACK <br>";

  $query_pac="UPDATE $PACKAGES_table set 
	clientid='$newclientid',comments=CONCAT(comments,'\n$comment_new')
	where clientid='$clientid'
	limit $MAX_TRANSFER_PACK";
  #and active='1'
   
  #print "query_pac: $query_pac"; exit();
  $result_pac=mysql_query($query_pac,$db_link);
  ####if(!mysql_query($query2,$db_link) )
  if(mysql_errno($db_link))
  {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
  }
}
#
function get_nrpack_in_clientid($clientid)
{
  ##include_once("dbinfo.php");
  include("dbinfo.php");
  
  global $db_link, $CLIENT_table, $NOVOS_CLIENTES, $NOVOS_CLIENTES2;
  global $PACKAGES_table;
 
  $clientid=trim($clientid);
  $clientid = ereg_replace("[^0-9]",'',$clientid);
  if(strlen($clientid) < 1)die("Clientid: $clientid ???");

  $query_pac="SELECT packid from $PACKAGES_table 
        where clientid='$clientid'";
  $result_pac=mysql_query($query_pac,$db_link);
  if(mysql_errno($db_link))
  {
	print("*** $query_pac ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit();
  }
  $num_rows = mysql_num_rows($result_pac);
  return($num_rows);
}
function get_clientid_inv_bb($nosso_nr)
{
 $clientid=substr($nosso_nr,-6);
 $clientid++; $clientid--;
 return($clientid);
}
#
function insere_cli_cob($clientid,$first,$PAY_TYPE_ESTE,$BANCO)
{
  global $CLIENT_table, $db_link, $NOVOS_CLIENTES;
  $INSERIR_CLIENTE=false;
  $HOJE=time();
  #Nao test de for cliente velox:
  if($clientid > $CLIENT_VELOX_BEGIN and $clientid < $CLIENT_VELOX_END )
  { return(0); }
  
  if($clientid < $NOVOS_CLIENTES)
  {
   print "Verificar Cliente ID $clientid caso nao seja inserido!<br>" . 
   "Arq.: " . __FILE__ . "<br>Linha: " . __LINE__ . "<br>";
   #return(0);
  }
  #if(get_nome($clientid) == "0")
  if(!exist_cli($clientid))
  {
   print "Cliente ID: $clientid - nao existe!<br>";
   if(!$INSERIR_CLIENTE)
   {
    print " ATENCAO: Nao vamos inserir o cliente $clientid no BD." .
    "Arq.: " . __FILE__ . "<br>Linha: " . __LINE__ . "<br>";
   }
   else
   {
     print " ATENCAO:  Vamos inserir o cliente $clientid no BD." .
    "Arq.: " . __FILE__ . "<br>Linha: " . __LINE__ . "<br>";
   }

   #pay_type,created_date,provedor,
   $s_first=addslashes($first);
   $query_cli="INSERT into $CLIENT_table (clientid,first,
   pay_type,created_date,provedor,vendedor,
   comments)
    values ( '$clientid','$s_first',
    '$PAY_TYPE_ESTE','$HOJE','esquadro2','pedro2'
    '')";
   if($INSERIR_CLIENTE)
   {
    $output_cli=mysql_query($query_cli,$db_link);
    if (mysql_errno($db_link))
    {
	#$mysql_nr_error++;
	print "<b>4. Mysql error" .
        mysql_errno($db_link) . mysql_error($db_link) . " $query_cli !<br>";
	#continue;
	   }
    else
    {
	print " OK!\n";
    }
    #sendmail
    $body_clix="clientid: $clientid nao estava cadastrado na cobranca $BANCO\n" .
    "Arq.: " .  __FILE__ . "\nLinha: " . __LINE__ . "\n";

    send_mailx_html($EMAIL_FATURA_SENDER,$EMAIL_CLIENT_X,
    "Faturamento: Cliente cadastrado na cobranca do $BANCO",$body_clix);   
   } 
   else
   {
    $body_clix="clientid: $clientid nao estava cadastrado na cobranca $BANCO\n";
    $body_clix .= " O cliente NAO foi cadastrado!\n" .
    "Arq.: " .  __FILE__ . "\nLinha: " . __LINE__ . "\n";

    send_mailx_html($EMAIL_FATURA_SENDER,$EMAIL_CLIENT_X,
    "Faturamento: Cliente nao cadastrado na cobranca do $BANCO",$body_clix);   
   }
  }	############################## 
  else
  { 
   print "Cliente $clientid ja estava cadastrada no banco de dados!<br>";
  }
}
#
function exist_cli($clientid)
{
 global $db_link, $CLIENT_table;
 $query_quota="SELECT first,last FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
	###and active='1' and suspend='0' and servcode='$SERVICE_EMAIL' ";
 ##print "query: $query_quota<br>";
 $valorq=mysql_query($query_quota,$db_link);
 if(mysql_error ($db_link))
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link)
	. " : "  . mysql_error($db_link) . "<br>";
	exit;
	#return(0);
 }
 $num_rows = mysql_num_rows($valorq);
 #print "num_rows: $num_rows";
 if($num_rows < 1) return(false);
 return(true);
}
#
function cli_velox($clientid)
{
 global $db_link_VELOX, $CLIENT_table;
 $query_quota="SELECT first,last FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
	###and active='1' and suspend='0' and servcode='$SERVICE_EMAIL' ";
 ##print "query: $query_quota<br>";
 $valorq=mysql_query($query_quota,$db_link_VELOX);
 if(mysql_error ($db_link_VELOX))
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_VELOX)
	. " : "  . mysql_error($db_link_VELOX) . "<br>";
	exit;
	#return(0);
 }
 $num_rows = mysql_num_rows($valorq);
 #print "num_rows: $num_rows";
 if($num_rows < 1) return(false);
 return(true);

}
#
function cli_esquadro($clientid)
{
 global $db_link_ESQUADRO, $CLIENT_table;
 $query_quota="SELECT first,last FROM 
	$CLIENT_table WHERE clientid='$clientid' ";
	###and active='1' and suspend='0' and servcode='$SERVICE_EMAIL' ";
 ##print "query: $query_quota<br>";
 $valorq=mysql_query($query_quota,$db_link_ESQUADRO);
 if(mysql_error ($db_link_ESQUADRO))
 {
	print("*** 1. $query_quota ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_ESQUADRO)
	. " : "  . mysql_error($db_link_ESQUADRO) . "<br>";
	exit;
	#return(0);
 }
 $num_rows = mysql_num_rows($valorq);
 #print "num_rows: $num_rows";
 if($num_rows < 1) return(false);
 return(true);
}
#
function get_campo_cli_prov($provedor,$clientid,$campo)
{
 #Get 'campo'  em $CLIENT_table.
 global $db_link, $db_link_ESQUADRO, $db_link_VELOX, $CLIENT_table, $INVOICES_table ;
 if($provedor == "esquadro"){$db_link_CRT=$db_link_ESQUADRO; }
 else if($provedor == "velox"){$db_link_CRT=$db_link_VELOX; }
 else 
 {
  print "Provedor '$provedor' errado:<br>Arq.: " . __FILE__ 
  . "<br>Linha:  " . __LINE__
  . "<br>"; 
  exit(0); 
 }
 $query_cli="SELECT $campo
  FROM $CLIENT_table WHERE clientid='$clientid' ";
 $valorq=mysql_query($query_cli,$db_link_CRT);
 if( mysql_error($db_link_CRT) )
 {
	print("*** 1. $query_cli ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRT)
	. " : "  . mysql_error($db_link_CRT) . "<br>";
	exit;
 }
 $return_pg=""; $nr_inv=0;
 while($pg_open = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  $return_pg .= $pg_open[$campo];
  $nr_inv++;
 }
 #print "<br>query_inv: $query_inv , $campo: $return_pg<br>";
 #Retorno UM valor do campo ou 'false' se existirem varias invoices com o mesmo nr.
 if($nr_inv > 1)$return_pg=-1;
 return($return_pg);
}
######
function get_campo_cli_table($provedor,$campo_var,$campo)
{
 #Get quantidades de 'campo'  em $CLIENT_table com valor like $campo_var
 global $db_link, $db_link_ESQUADRO, $db_link_VELOX, $CLIENT_table, $INVOICES_table ;
 if($provedor == "esquadro"){$db_link_CRT=$db_link_ESQUADRO; }
 else if($provedor == "velox"){$db_link_CRT=$db_link_VELOX; }
 else 
 {
  print "Provedor $provedor errado:<br>" . __FILE__ . "<br>" . __LINE__
  . "<br>"; 
  exit(0); 
 }
 $query_cli="SELECT clientid,$campo
  FROM $CLIENT_table WHERE $campo like '%$campo_var%' ";
 $valorq=mysql_query($query_cli,$db_link_CRT);
 if( mysql_error($db_link_CRT) )
 {
	print("*** 1. $query_cli ***<br>");
	print "\nMysql error:" . mysql_errno($db_link_CRT)
	. " : "  . mysql_error($db_link_CRT) . "<br>";
	exit;
 }
 $return_campo=""; $nr_ret=0;
 while($retornos = mysql_fetch_array($valorq,MYSQL_BOTH))
 {
  $return_campo .= $retornos['clientid'] . ",";
  $nr_ret++;
 }
 #print "<br>query_cli: $query_cli , $campo: $return_campo, nr_ret: $nr_ret<br>";
 #return($return_campo);
 return($nr_ret);
}
######
function insert_bol_perdida($nosso_nr,$ID_BOLETAS_PERDIDAS,$banco_crt,
 $PAY_TYPE_ESTE,$arquivo_ret_orig,$venciment,$val_cob)
{
 global $db_link, $db_link_INVOICES, $CLIENT_table, $INVOICES_table;
 global $id_contabil;
 $clientid=$ID_BOLETAS_PERDIDAS;
 $message="Boleta Perdida - Nao encontrado o cliente ID";
 $val_cob00=$val_cob * 100;
 $date_segs_venc=time();
 
 $query_inv="INSERT into $INVOICES_table 
 (clientid,first,
  amount,amount_x, banco, invnum,
  date0_orig,date0,
  pay_type,message,id_contabil,
  data_arq_ret,nome_arq_ret,due,
  paid
 ) 
 values 
 ( '$clientid','NAO LOCALIZADO',
   '$val_cob','$val_cob00','$banco_crt','$nosso_nr',
   '$venciment','$venciment',
   '$PAY_TYPE_ESTE','$message','$id_contabil',
   '$data_ret_int','$arquivo_ret_orig','$date_segs_venc',
   '1'
 )";
 #date: $data_emissao_cobranca
 #print "query_inv: $query_inv<br>"; exit();
 $output=mysql_query($query_inv,$db_link_INVOICES) ;
 if (mysql_errno($db_link_INVOICES))
 {
	$mysql_nr_error++;
	print "<b>44. Mysql error" .
        mysql_errno($db_link_INVOICES) . mysql_error($db_link_INVOICES) 
        . " $query_inv !<br>";
	exit();
 }
 print " invnum $nosso_nr inserida no ID $clientid<br>";
}

#############################
function  print_situacao_invoice($nosso_nr)
{
 #Retorna: -1=mais de uma boleta com nosso nr.
 #0=boleta em aberto,  1=boleta paga, 2=boleta cancelada.
 $pago=get_campo_inv($nosso_nr,'paid');
 if($pago < 0){ print "Mais de uma boleta com $nosso_nr"; return(-1); }
 if($pago > 0){ print "Bol. pg. "; return(1);} 
 $cancela=get_campo_inv($nosso_nr,'cancela');
 if($cancela < 0){ print "Mais de uma boleta com $nosso_nr"; return(-1); }
 if($cancela > 0)
 {
  #datepaidre=data pagamento recebido
  $datepaidre9=get_campo_inv($nosso_nr,'datepaidre');
  $date_order_br="Y/m/d";
  $now_human=date($date_order_br,$datepaidre9);
  print "Bol. canc. em " .  $now_human . "."; 
  return(2);
 } 
 if($pago == 0 and $cancela == 0)
 { print "Bol. em aberto"; return(0); }
}
#####
function cobra_adiante($clientid,$nosso_nr)
{
 #Da baixa e cobra boleta $nosso_nr adiante. De payrecvd.php linha 342
 if($clientid < 1 or $nosso_nr < 1)
 {
  print "Erro em clientid: $clientid ou nosso_nr: $nosso_nr";
  exit(0);
 }
 #print "clientid: $clientid, nosso_nr: $nosso_nr ";

 global $db_link, $db_link_ESQUADRO, $db_link_VELOX, $db_link_INVOICES, $db_link_VELOX, $CLIENT_table, $INVOICES_table ;
 global $id_contabil, $COBRADO_ADIANTE, $MESES_ANO;
 global $MULTA_POR_ATRASO;
 
 if(cli_esquadro($clientid))
 {
   $PROVEDOR_NOW="esquadro";
   $db_link_CRT=$db_link_ESQUADRO;
 }
 else 
 if(cli_velox($clientid))
 {
  $PROVEDOR_NOW="velox";
  $db_link_CRT=$db_link_VELOX;
 }
 else
 {
  $PROVEDOR_NOW="desconhecido"; print "Provedor desconhecido"; return(0); 
 }

 #print "Prov. $PROVEDOR_NOW . "; 
 $suspenso=get_campo_cli_prov($PROVEDOR_NOW,$clientid,"suspend");
 if($suspenso)
 { 
  print "Cli. sus. => NAO e' cobrado adiante.";  return(0);
 }
 ##return(0);
 #A boleta tem que estar cancela=0 e paid=0;
 $pago=get_campo_inv($nosso_nr,'paid');
 $cancela=get_campo_inv($nosso_nr,'cancela');
 if(!( $pago == 0 and $cancela == 0) )
 {
   print "Bol. pg/canc.";
   return(0);
 }
 #
 $now=time();
 $mes_seguinte=date("m") + 1; if($mes_seguinte > 12)$mes_seguinte=1;
 if(strlen($mes_seguinte)<2){ $mes_seguinte ="0" . $mes_seguinte; }
 $mes_seguinte_nome=$MESES_ANO[$mes_seguinte];

 $date0_orig=get_campo_inv($nosso_nr,'date0_orig');
 $new_amount=get_campo_inv($nosso_nr,'amount');
 $new_amount = $new_amount + $MULTA_POR_ATRASO;
   
 $PAID_CANC="paid='0',cancela='1'";
 $message="\n*$COBRADO_ADIANTE em $mes_seguinte_nome\n";
 #$mensagem=$reason[$invcount] . ", Venc. original: " . $date0_orig[$invcount]
 $mensagem="AUTO-" . $COBRADO_ADIANTE . ", Venc. original: " . $date0_orig
  . ", boleto nr: " . $nosso_nr;
 $mensagem .= ". MULTA POR ATRASO: R\$ $MULTA_POR_ATRASO .";
 $descricao="Venc. original: " . $date0_orig;

 #print "=>$clientid,$date0_orig,$mes_seguinte,$new_amount,$mensagem,$descricao <=";
 $executa=true;
 #return(0);
 #
 global $data_ret_int,$arquivo_ret_orig;
 #body=CONCAT(body,'$id_contabil] AUTO-$COBRADO_ADIANTE .'),
 #mysql> update INVOICES set body=ifnull(CONCAT(body,"kkk"),"xx") where invnum='14842032' limit 1;
 $body_msg="$id_contabil] AUTO-$COBRADO_ADIANTE .";
 $query="UPDATE $INVOICES_table SET 
 $PAID_CANC,datepaid='$now',
 body=IFNULL(CONCAT(body,'$body_msg'),'$body_msg'),
 message=CONCAT(message,'$message'),
 datepaidre='$now',
 amount_rec='0.00',
 data_arq_ret=CONCAT(data_arq_ret,' - $data_ret_int'),
 nome_arq_ret=CONCAT(nome_arq_ret,' - $arquivo_ret_orig')
 WHERE invnum='$nosso_nr' limit 1 "; 
 #print "query: $query "; 
 if(!executa)
 {
  print "query: $query ";
  return(0);
 }
 $result=mysql_query($query,$db_link_INVOICES);
 if(mysql_error($db_link_INVOICES)) 
 {
  print("*** $query ***<br>");
  print "\nMysql error:" . mysql_errno($db_link_INVOICES)
   . " : "  . mysql_error($db_link_INVOICES) . "<br>";
  #exit();
 }
 else if($executa)
 {
  $new_packid=insert_debito2($db_link_CRT,$clientid,$date0_orig,$mes_seguinte,$new_amount,$mensagem,$descricao);
  print "Criado um pacote de nr. $new_packid com a divida. <blink>* VERIFICAR *</blink><br>\n";
 }
 return(0);
}
#########
function banda_pppoe($userid,$clientid,$packid,$ip_nr,$bandwidth,$bandwidthul,$band_garante)
{
 global $db_link, $CONNECT_INFO, $CONNECT_INFO_SUFIX, $CONNECT_INFO_MIKROTIK, $BURST_TIME;
 ##include_once("dbinfo.php");
 include("dbinfo.php");
 
 #$userid=$username;
 #mysql> update radreply set Attribute='Rate-Limit',
 #Value='90k/180k 128k/256k 90k/180k 10000/10000' 
 #where id='11696' limit 1;
 #print "*****"; exit();
 $tx_burst_rate=$bandwidth;	#bandwidth em kbits/s: 256/1024/...
 $rx_burst_rate=intval($bandwidth / 4);	#UPLOAD para o cliente
 ##if($rx_burst_rate > 128)$rx_burst_rate="128";
 if($rx_burst_rate < 64)$rx_burst_rate="64";
 if(!isset($band_garante) or $band_garante < 20)$band_garante="20";
 #print "band_garante: $band_garante,tx_burst_rate: $tx_burst_rate, rx_burst_rate: $rx_burst_rate<br>"; exi();
 $tx_rate=intval($tx_burst_rate * $band_garante / 100);
 $rx_rate=intval($rx_burst_rate * $band_garante / 100);
 if($rx_rate  < 64)$rx_rate="64";
 if($tx_rate  < 64)$tx_rate="64";
 #print "rx_rate: $rx_rate, tx_rate: $tx_rate<br>"; exit();
 #$tx_burst_threshold=intval($tx_burst_rate / 2);
 #$rx_burst_threshold=intval($rx_burst_rate / 2);
 $tx_burst_threshold=$tx_rate;
 $rx_burst_threshold=$rx_rate;

 ##$rx_burst_time=$tx_burst_time="10000"; => $BURST_TIME
 
 $bandwidth2=$bandwidth . $CONNECT_INFO_SUFIX;
 ##$bandwidth2_MIKROTIK=$bandwidth . $CONNECT_INFO_SUFIX_MIKROTIK;
 $bandwidth2_MIKROTIK=$rx_rate . "k/" . $tx_rate . "k " .
 $rx_burst_rate . "k/" . $tx_burst_rate . "k " .
 $rx_burst_threshold . "k/" . $tx_burst_threshold . "k " .
 $BURST_TIME;		#"60000/60000";
 /******/
 #print "$userid: bandwidth2_MIKROTIK: $bandwidth2_MIKROTIK, rx_burst_rate: $rx_burst_rate, bandwidth: $bandwidth<br>"; exit();
 ##print "CONNECT_INFO_SUFIX: $CONNECT_INFO_SUFIX, CONNECT_INFO: $CONNECT_INFO, CONNECT_INFO_MIKROTIK: $CONNECT_INFO_MIKROTIK<br>"; exit();
 updt_radreply($userid,$CONNECT_INFO,":=",
	$bandwidth2);
 updt_radreply($userid,$CONNECT_INFO_MIKROTIK,":=",
	$bandwidth2_MIKROTIK);

 $clientid_packid=$clientid . "_" . $packid;
 updt_radreply($userid,"Configuration-Token",":=",$clientid_packid);
 updt_radreply($userid,"Framed-IP-Address",":=",$ip_nr);
}
##############
?>

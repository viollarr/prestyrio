<?php

include_once "funcoes_ponto.php";
function login_logout($func,$func_watch)
{
	#func_watch: true => detona se ip nao autorizado
        global $HTTP_VIA,$REMOTE_ADDR,$HTTP_X_FORWARDED_FOR,$host_ip,
        $USER_HOST_IP, $USER_HOST_NAME;
        $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
	#Pedro em 26-3-2010
	$nmCracha  = trim($_POST['x_subname']);
	##print "nmCracha: $nmCracha";

        #? $host_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        #print "HTTP_VIA = $HTTP_VIA <br>"; #exit();
	$date_order_Y="d/m/Y H:i:s";
	#$DATA=date("M/d/Y");
	#$HORA=date("H:i:s");

	$now_date=date("$date_order_Y");
	##$DATA=date();

        if($HTTP_VIA) 
        {
        $host=gethostbyaddr($HTTP_X_FORWARDED_FOR);
        $host_ip=$HTTP_X_FORWARDED_FOR;
        #print " host_ip = $HTTP_X_FORWARDED_FOR<br>";
        }
        else 
        {
        #print " host_ip = $REMOTE_ADDR<br>"; exit();
        #global $REMOTE_ADDR; print "REMOTE_ADDR: $REMOTE_ADDR";
        #print "REMOTE_ADDR: $REMOTE_ADDR, $_REMOTE_ADDR, $_SERVER[REMOTE_ADDR]";
        $host = gethostbyaddr($REMOTE_ADDR);
        $host_ip = $REMOTE_ADDR;
        #print " host_ip = $REMOTE_ADDR<br>"; exit();
        }
        $USER_HOST_NAME=$host;
        $USER_HOST_IP=$host_ip;
        #$host_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        #print "<br>--- $host_ip <br>"; exit();
        if(! test_ip_allow($host_ip))    # $FROM_NET
        {
         #print "IP: $host_ip / $FROM_NET : banned <br>";
	 #$ip = $_SERVER['REMOTE_ADDR'];
	 #$arrDados['fu_id'].",now(),'$nmSemana','$ip')";
	 ###$nmCracha  = $_POST['x_subname'];
	 #mysql> select fu_id,fu_nome from funcionario where fu_id='35';
	 $sql       = mysql_query("SELECT * FROM funcionario WHERE fu_num_cracha = '$nmCracha'");
	 $arrDados  = mysql_fetch_array($sql);
	 $numCracha = mysql_num_rows($sql);
	 $ip_remote = $_SERVER['REMOTE_ADDR'];
	 $ip_remote_nome = gethostbyaddr($ip_remote);
	 $fu_id=$arrDados['fu_id']; 
	 if($numCracha < 1)
	 {
	  $fu_nome="$nmCracha/???"; $fu_id="???";
	  $fu_nome10=$fu_nome;
	 }
	 else 
	 {
	  $fu_nome=$arrDados['fu_nome']; 
	  $fu_nome10=substr($fu_nome,0,10);
	 }

	 $sender="webmaster@prestyrio.com.br"; $destinatario="adm@prestyrio.com.br";
	 $assunto="Indevido - Ponto de ${fu_id}:$fu_nome10 em " . "$now_date hs.";
	 $body="$now_date: $assundo\nFuncao: ips_allow.php\nMaquina: $host_ip\n";
	 $body .="ip_remote: $ip_remote - $ip_remote_nome\n";
	 $body .="nmCracha: $nmCracha, fu_id: $fu_id, fu_nome: $fu_nome\n";
	 $body .= "\n\n1. Arq: " . __FILE__ . " : linha nr "  . __LINE__ . "\n\n";
	 send_mailx($sender,$destinatario,$assunto,$body);
	 if($func)exit();	#Detona se IP nao autorizado e $func ==true.
        }
	else if($func_watch and strlen($nmCracha) > 1)	#IP autorizado. Envia email se strlen($nmCracha) > 1
	{
	 ##$nmCracha  = $_POST['x_subname'];
	 #mysql> select fu_id,fu_nome from funcionario where fu_id='35';
	 $sql       = mysql_query("SELECT * FROM funcionario WHERE fu_num_cracha = '$nmCracha'");
	 $arrDados  = mysql_fetch_array($sql);
	 $numCracha = mysql_num_rows($sql);
	 $ip_remote = $_SERVER['REMOTE_ADDR'];
	 $ip_remote_nome = gethostbyaddr($ip_remote);
	 $fu_nome=$arrDados['fu_nome']; 
	 if($numCracha < 1)
	 {
	  $fu_nome="$nmCracha/???";  $fu_id="???";
	  $fu_nome10=$fu_nome;
	 }
	 else 
	 {
	  $fu_nome10=substr($fu_nome,0,10);
	  $fu_id=$arrDados[fu_id];
	 }
	 $sender="webmaster@prestyrio.com.br"; $destinatario="adm@prestyrio.com.br";
	 $assunto="Ponto de " . "${fu_id}:${fu_nome10}" .  " em $now_date hs.";
	 $body="$now_date: $assundo\nFuncao: ips_allow.php\nMaquina: $host_ip\n";
	 $body .="ip_remote: $ip_remote - $ip_remote_nome\n";
	 $body .="nmCracha: $nmCracha, fu_id: $fu_id, fu_nome: $fu_nome\n";
	 $body .= "\n\n2. Arq: " . __FILE__ . " : linha nr "  . __LINE__ . "\n\n";

	 send_mailx($sender,$destinatario,$assunto,$body);
	 #print "Email enviado: <br>numCracha: $numCracha<br>assundo: $assunto <br>body: $body";
	}
}                          


function isIPIn($ip,$net,$mask) {
   $lnet=ip2long($net);
   $lip=ip2long($ip);
   $binnet=str_pad( decbin($lnet),32,"0",STR_PAD_LEFT);
   $firstpart=substr($binnet,0,$mask);
   $binip=str_pad( decbin($lip),32,"0",STR_PAD_LEFT);
   $firstip=substr($binip,0,$mask);
   return(strcmp($firstpart,$firstip)==0);
}


function test_ip_allow($myip)
{
   global  $FROM_NET, $ALLOWED_IPS2;
   $ALLOWED_IPS2 = array (
    '201.36.98.0/24' => "WB1",
    '201.36.96.0/24' => "WB2",
    '201.12.27.0/24' => "WB3",
    '189.77.3.0/24' => "WB4",
    '189.25.143.169/32' => "VELOX_PEDRO",
    '189.122.129.0/24' => "VIRTUAL_PEDRO",
    '187.67.103.0/24' => "VIRTUAL_PEDRO",
    '187.67.100.0/24' => "VIRTUAL_PEDRO",
   );

   $FROM_NET=""; $FROM_NET_ALLOW=false;
   foreach ($ALLOWED_IPS2 as $k=>$v)
   {
        list($net,$mask)=split("/",$k);
        if($net == "0.0.0.0" and $mask == "0")
        {
          $FROM_NET = "$ALLOWED_IPS2[$k]";
          return(true);
        }
        if(!$net or !$mask){
         print "<br><h3>Erro em " . __FILE__ . ": $k, $v</h3><br>";
         exit();
        }
        if (isIPIn($myip,$net,$mask)) {
          $FROM_NET_ALLOW=true;
          #echo $n[$k]."<br />\n";
          $FROM_NET = "$ALLOWED_IPS2[$k] "; return($FROM_NET_ALLOW);
        }
   }
   return($FROM_NET_ALLOW);
}

#function send_mailx($sender,$destinatario,$assunto,$body)
#{
# $sendmail = "/usr/sbin/sendmail -t -f $sender";
# $fd = popen($sendmail, "w");
# fputs($fd, "To: $destinatario\r\n");
# fputs($fd, "From: $sender\r\n");
# fputs($fd, "Subject: $assunto\r\n");
# fputs($fd, "X-Mailer: PHP mailer\r\n\r\n");
# fputs($fd, $body);
# pclose($fd);
#}

?>

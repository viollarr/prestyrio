<?php

/*
mysql> select * from  funcionario where fu_id='58';
+-------+--------------------------------+-----------------------+-----------------+--------------+------------------+--------------+-------------+----------+---------------+-----------------------+----------------+-------+
| fu_id | fu_horario                     | fu_nome               | fu_funcao
| fu_matricula | fu_rg            | fu_ctps      | fu_admissao | fu_setor |
fu_num_cracha | fu_nome_cracha        | fu_foto_cracha | cr_id |
+-------+--------------------------------+-----------------------+-----------------+--------------+------------------+--------------+-------------+----------+---------------+-----------------------+----------------+-------+
|    58 | Hor�io Padr� (9:00 � 18:00) | BRUNO SANTOS DA CUNHA |
SUPORTE TECNICO | 44071001     | 119069219DIC R J | 091591077-23 |
2007-10-01  | SUPORTE  | 44071001      | BRUNO SANTOS DA CUNHA | NULL
| 5     |
+-------+--------------------------------+-----------------------+-----------------+--------------+------------------+--------------+-------------+----------+---------------+-----------------------+----------------+-------+
1 row in set (0.02 sec)
*/


include_once "config.php";

function get_last_num_cracha()
{
 $query="SELECT * from  funcionario ORDER BY fu_id DESC limit 1";
 $sql = mysql_query("$query");
 $arrDados = mysql_fetch_array($sql);
 return($arrDados['fu_matricula']);
}

function Sec2Time($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    ##return (array) $value;
    return $value["hours"] .  ":" . $value["minutes"] . ":" . $value["seconds"];
  }else{
    return (bool) FALSE;
  }
}
#
function calc_dif_horas($hora_entrada,$hora_saida)
{
 $hora_entrada=trim($hora_entrada); $hora_saida=trim($hora_saida);
 if(strlen($hora_entrada) < 1 or strlen($hora_saida) < 1)
 {
  ##print "Erro no ponto: entrada ou saida n&atilde;o foi batido => $hora_entrada --- $hora_saida";
  $horas_calc=array("0" => "0:0:0", "1" => "0");
  return($horas_calc);
 }
 
 ###################
 list($hora_entrada_h,$hora_entrada_m,$hora_entrada_s) = explode(":",$hora_entrada);
 list($hora_saida_h,$hora_saida_m,$hora_saida_s) = explode(":",$hora_saida);
 #if($idFuncionario == "53" and $hora_saida_h > 18)
 #{
 # $hora_saida_h="18";
 # $hora_saida="18:$hora_saida_m:$hora_saida_s";
 #}

 $a = mktime($hora_entrada_h,$hora_entrada_m,$hora_entrada_s,$txtMes,$x,$txtAno);
 #$b = mktime($hora_saida_h+2,$hora_saida_m,$hora_saida_s,$txtMes,$x,$txtAno);
 #print "hora_saida_h: $hora_saida_h, hora_saida_m: $hora_saida_m ";
 $b = mktime($hora_saida_h,$hora_saida_m,$hora_saida_s,$txtMes,$x,$txtAno);
 ###print "a: $a, b: $b<br>";

 $dif=$b - $a;
 #----Se a dif. for maior que 6 horas, desconte uma hora do almoco. Versao antiga.
 #---- if($dif > 6*3600){$dif = $b - $a - 3600; }	#Horas em segs. 3600 = 1 hora de almoco
 # -1 hora de almoco se estiver trabalhando mais de 6 horas.
 ####SE TIROU HORA DO ALMOCO?!!
 ##print "dif: $dif<br>";

 #Errado: $hora_total = date("H:i:s",$dif);
 $hora_total = Sec2Time($dif);

 #if($hora_saida == 0 or $hora_entrada == 0)
 #{
 #  $hora_total="0:0:0"; #print "$hora_saida - $hora_entrada";
 #}
 ##print "hora_total: $hora_total<br>";
 list($hora_total_h,$hora_total_m,$hora_total_s) = explode(":",$hora_total);
 ###print "2. dif: $dif, hora_total: $hora_total<br>";
 $acrescimo_segs= (($hora_total_h * 3600)+($hora_total_m * 60)+($hora_total_s));
 $total_geral_segs =$acrescimo_segs;

 #if($feriado or $fim_de_semana)
 #{
 #  $total_segs_extras_feriado +=$acrescimo_segs;
 #}
 #else 
 #{
 # $total_geral_segs_em_dias_uteis +=$acrescimo_segs;
 #}
 $horas_calc=array("0" => "$hora_total", "1" => "$total_geral_segs");
 return($horas_calc);
 ###############
}
########
function send_mailx($sender,$destinatario,$assunto,$body)
{
 $sendmail = "/usr/sbin/sendmail -t -f $sender";
 $fd = popen($sendmail, "w");
 fputs($fd, "To: $destinatario\r\n");
 fputs($fd, "From: $sender\r\n");
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

#$MESES_ANO / DIAS_MES
#############
function show_sel_list($var_select,$var_values_array,$select_item,$array_type,$style_class)
{
print <<<EOF
<select name="$var_select" class="$style_class">
EOF;
reset($var_values_array);
while (current($var_values_array) !== false) {
     $var_select_crt = key($var_values_array);  #var_select_crt X o indice do array
     $escolha="";
     if($array_type == 0)
     {
        if($var_values_array[$var_select_crt] == $select_item){ $escolha="
selected"; }
        print <<<EOF

<option value="$var_values_array[$var_select_crt]"$escolha>
        $var_values_array[$var_select_crt] </option>
EOF;

     }
     else
     {
        if($select_item == $var_select_crt){ $escolha=" selected"; }
        print <<<EOF

<option value="$var_select_crt"$escolha> $var_values_array[$var_select_crt]
</option>
EOF;

     }
     next($var_values_array);
}
print <<<EOF
</select>
EOF;
}
function get_dia_especial($DIA_I,$DIA_P)
{
 global $FERIADOS_DIAS, $DIAS_MEIO_EXPEDIENTE;
 #$DIA no formato 2011-03-05
 #w: 0 (for Sunday) through 6 (for Saturday)
 #N: 1 (for Monday) through 7 (for Sunday)
 $wday = date("w",strtotime("$DIA_I")); 
 #print "wday=$wday"; exit();
 if($wday == 0 or $wday == 6)return(true);
 ##print "FERIADOS_DIAS: "; print_r($FERIADOS_DIAS); print $DIA_P; exit();
 if(isset($FERIADOS_DIAS[$DIA_P]))
 {
  ##print "Feriado"; exit();
  return(true); 
 }
 if(isset($DIAS_MEIO_EXPEDIENTE[$DIA_P]))
 {
  ##print "Meio expediente"; exit();
  return(true);
 }
 return(false);
}

###########

?>

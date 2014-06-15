<?
$dbHost	= "localhost";
$dbUser = "wbhost_wbhost";
$dbPass	= "2q3w4e";
$dbName	= "wbhost_ponto";
$TABLE_ID_usuarios="adm_id";
$TABLE_usuarios="adm";
$TABLE_usuarios_login="adm_login";
$TABLE_usuarios_senha="adm_senha";

$dbConexao = mysql_connect($dbHost,$dbUser,$dbPass);
if(!$dbConexao)
{
	echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"><b>Erro</b> na conexão com o banco...</span><br>".mysql_error();
	exit;
}

$dbSelect = mysql_select_db($dbName);
if(!$dbSelect)
{
	echo "<span style=\"font-family: verdana; font-size: 12px; color: #FF0000;\"><b>Erro</b> na seleção do banco...</span><br>".mysql_error();
	exit;
}

$foto_width="100";
$foto_height="120";
$foto_dim=$foto_width . "x" . $foto_height;
############### Pedro em 2-1-2008:
$now_array=getdate();
#print_r($now_array); [seconds] => 22 [minutes] => 1
#[hours] => 9 [mday] => 22 [wday] => 1 [mon] => 3 [year] => 2010 [yday] => 80
#[weekday] => Monday [month] => March [0] => 1269259282 ) 
$date_order="d/m/y";
$date_order_Y="d/m/Y";
$now_date=date("$date_order");
$now_date_Y=date("$date_order_Y");
$words_date=explode("/",$now_date);
$words_date_Y=explode("/",$now_date_Y);
$DIA=$words_date[0];
$MES=$words_date[1];
$ANO=$words_date_Y[2];
$ANO_2=$ANO-2;
$ANO_1=$ANO-1;
$ANOS_CRT=array($ANO-2,$ANO-1,$ANO,$ANO+1,$ANO+2,"");
#$DATA=" Rio, " . date("Y-M-d H:m") . " hs";
#mktime ( 0, 0, 0, $fmonth, $fday, $fyear) 
#nt mktime  ([ int $hour  [, int $minute  [, int $second  [, int $month  [,
#int $day  [, int $year  [, int $is_dst  ]]]]]]] )
$inicio=mktime("6","0");
$fim=mktime("9","15");
$ini_livre=mktime("11","0");
#$PERMITE_ENTRADA=array('inicio' => "6:00",'fim' => "9:15",'ini_livre' => "11:00");
$PERMITE_ENTRADA=array('inicio' => $inicio,'fim' => $fim,
	'ini_livre' => $ini_livre);
#Mail 
$sender="fatura@wb.com.br";
$destinatario="pedro@wb.com.br,pedro@wbhost.com.br";

$MESES_ANO= array
(
  "01" => "Janeiro", 	#"1" => "Janeiro",
  "02" => "Fevereiro", 	#"2" => "Fevereiro",
  "03" => "Marco", 	#"3" => "Marco",
  "04" => "Abril", 	#"4" => "Abril",
  "05" => "Maio", 	#"5" => "Maio",
  "06" => "Junho", 	#"6" => "Junho",
  "07" => "Julho", 	#"7" => "Julho",
  "08" => "Agosto", 	#"8" => "Agosto",
  "09" => "Setembro", 	#"9" => "Setembro",
  "10" => "Outubro",
  "11" => "Novembro",
  "12" => "Dezembro",
  ""   => ""
);
$DIAS_MES= array
(
  "01" => "01",
  "02" => "02",
  "03" => "03",
  "04" => "04",
  "05" => "05",
  "06" => "06", 
  "07" => "07",
  "08" => "08",
  "09" => "09",
  "10" => "10",
  "11" => "11",
  "12" => "12",
  "13" => "13",
  "14" => "14",
  "15" => "15",
  "16" => "16", 
  "17" => "17",
  "18" => "18",
  "19" => "19",
  "20" => "20",
  "21" => "21",
  "22" => "22",
  "23" => "23",
  "24" => "24",
  "25" => "25",
  "26" => "26", 
  "27" => "27",
  "28" => "28",
  "29" => "29",
  "30" => "30",
  "31" => "31",
  "" => ""
);

#Sessoes:
session_start();
$ESQ_SESSION_TIMEOUT=60*60;	#1 hora= 60 min x 60 seg
if(!isset($_SESSION['ponto']['registered']) or !isset($_SESSION['ponto']) )
{
 $_SESSION = array();
 unset($_SESSION['ponto']);
 unset($_SESSION['id']);
 unset($_SESSION['login']);
 unset($_SESSION['funcao']);
 unset($_SESSION['adm_id']);
 unset($_SESSION['adm_login']);
 unset($_SESSION['adm_grupo']);
 unset($_SESSION['adm_funcao']);
 unset($_SESSION['ponto']['registered']);
 unset($_SESSION['ponto']);
 #header("Location: http://".$_SERVER['HTTP_HOST']."/ponto/admin.php");
 #exit();
}
else if(isset($_SESSION['ponto']['registered']) and
            (time() - $_SESSION['ponto']['registered']) > 0
       )
{
 $_SESSION = array();
 unset($_SESSION['ponto']);
 unset($_SESSION['id']);
 unset($_SESSION['login']);
 unset($_SESSION['adm_id']);
 unset($_SESSION['adm_login']);
 unset($_SESSION['adm_grupo']);
 unset($_SESSION['adm_funcao']);
 unset($_SESSION['funcao']);
 unset($_SESSION['ponto']['registered']);
 #header("Location: http://".$_SERVER['HTTP_HOST']."/ponto/admin.php");
 #exit();
}
else
{
 $_SESSION['ponto']['registered']=time()+$ESQ_SESSION_TIMEOUT;
}
##print_r($_SESSION);
$dia_semana = array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado');

$FERIADOS_DIAS=array (
'01-01-2008'  => 'Feriado - Ano Novo',
'20-01-2008'  => 'Feriado - Cidade Rio de Janeiro',
'25-12-2008' => 'Feriado - Natal',
'01-05-2008'  => 'Feriado - Dia do Trabalhador',
'01-01-2009'  => 'Feriado - Ano Novo',
'20-01-2009'  => 'Feriado - Cidade Rio de Janeiro',
'01-05-2009'  => 'Feriado - Dia do Trabalhador',
'25-12-2009' => 'Feriado - Natal',
'01-01-2010'  => 'Feriado - Ano Novo', 
'20-01-2010'  => 'Feriado - Cidade Rio de Janeiro',
#Carnaval:
'15-02-2010'  => 'Feriado - Segunda Carnaval', 
'16-02-2010'  => 'Feriado - Ter&ccedil;a Carnaval',
# '17-02-2010'  => '1', Quarta-feira
'02-04-2010'=> 'Feriado - Semana Santa', #Semana Santa
'21-04-2010'=> 'Feriado - Tiradentes',
'23-04-2010'=> 'Feriado - Sao Jorge',
'01-05-2010'  => 'Feriado - Dia do Trabalhador', 
'03-06-2010'  => 'Feriado - Corpus Cristie',
'07-09-2010'=> 'Feriado - Dia da Patria',
'12-10-2010' => 'Feriado',
'02-11-2010' => 'Feriado', 
'15-11-2010' => 'Feriado', 
'25-12-2010' => 'Feriado - Natal',
'01-01-2011'  => 'Feriado - Ano Novo',
'20-01-2011'  => 'Feriado - Sao Sebastiao',
'07-03-2011'  => 'Compensa&ccedil;&atilde;o segunda carnaval',
'08-03-2011'  => 'Ter&ccedil;a carnaval',
);
$FERIADOS_DIAS2=array (
'2008-01-01'  => 'Feriado - Ano Novo',
'2008-01-20'  => 'Feriado - Cidade Rio de Janeiro',
'2008-12-25' => 'Feriado - Natal',
'2008-05-01'  => 'Feriado - Dia do Trabalhador',
'2009-01-01'  => 'Feriado - Ano Novo',
'2009-01-20'  => 'Feriado - Cidade Rio de Janeiro',
'2009-05-01'  => 'Feriado - Dia do Trabalhador',
'2009-12-25' => 'Feriado - Natal',
'2010-01-01'  => 'Feriado - Ano Novo', 
'2010-01-20'  => 'Feriado - Cidade Rio de Janeiro',
#Carnaval:
'2010-02-15'  => 'Feriado - Segunda Carnaval', 
'2010-02-16'  => 'Feriado - Ter&ccedil;a Carnaval',
# '17-02'  => '1', Quarta-feira
'2010-04-02'=> 'Feriado - Semana Santa', #Semana Santa
'2010-04-21'=> 'Feriado - Tiradentes',
'2010-04-23'=> 'Feriado - Sao Jorge',
'2010-05-01'  => 'Feriado - Dia do Trabalhador', 
'2010-06-03'  => 'Feriado - Corpus Cristie',
'2010-09-07'=> 'Feriado - Dia da Patria',
'2010-10-12' => 'Feriado',
'2010-11-02' => 'Feriado', 
'2010-11-15' => 'Feriado', 
'2010-12-25' => 'Feriado - Natal',
'2011-01-01'  => 'Feriado - Ano Novo',
'2011-01-20'  => 'Feriado - Sao Sebastiao',
'2011-03-07'  => 'Compensa&ccedil;&atilde;o segunda carnaval',
'2011-03-08'  => 'Ter&ccedil;a carnaval',
);
$DIAS_MEIO_EXPEDIENTE=array (
'09-03-2011'  => array
  ('INICIO'  => '12:00', 
  'COMENTARIO' => 'Quarta-feira cinzas: Meio expediente'
  ),
);

$DIAS_MEIO_EXPEDIENTE2=array (
'2011-03-09'  => array
  ('INICIO'  => '12:00', 
  'COMENTARIO' => 'Quarta-feira cinzas: Meio expediente'
  ),
);

?>

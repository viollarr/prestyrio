<?php
#include_once('util/sessao.php');
include_once('tbs/tbs_class.php');
#include_once('util/connect_db.php');
include_once('../php/funcoes.php');


//Default value
if (!isset($_GET)) $_GET=&$HTTP_GET_VARS ;
if (isset($_GET['PageNum'])) {
  $PageNum = $_GET['PageNum'] ;
} else {
    $PageNum = 1 ;
}

//Default value
if (isset($_GET['RecCnt'])) {
  $RecCnt = intval($_GET['RecCnt']) ;
} else {
    $RecCnt = -1 ;
}


$PageSize = 200 ;        #Nr. de entradas por pagina.
$navsize=$PageSize + 1;

if(!isset($PageNum)  or ! $PageNum or $PageNum == '') $PageNum=0;
$recordnr=$PageNum * $PageSize;
#print "recordnr: $recordnr<br>"; exit();
#if($recordnr < 1) $recordnr=1;
if($RecCnt < $PageSize )$recordnr=0;
if($PageNum < 1)$PageNum=1;



$TBS = new clsTinyButStrong ;
$TBS->LoadTemplate('html/listar_fotos.html') ;
$query=" select * from fotos_multimidia ";


/*
mysql> select * from fotos_multimidia limit 1;
+-----+----------+------------+---------+-------+
| _id | _tipo    | _nome      | _thumb  | _foto |
+-----+----------+------------+---------+-------+
|   1 | pessoais | Foto Legal | 1_p.jpg | 1.jpg |
+-----+----------+------------+---------+-------+
1 row in set (0.00 sec)
*/

#$TBS->MergeBlock('blk1',$cnx_id,'SELECT `102000_6000` FROM `a102000` WHERE `102000_3000` = 1') ;
$RecCnt = $TBS->MergeBlock('blk',$cnx_id,"$query") ;
#$TBS->MergeNavigationBar('nv','',$PageNum,$RecCnt,$PageSize) ;
mysql_close($cnx_id) ;
$TBS->Show() ;

?> 

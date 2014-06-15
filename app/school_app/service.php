<?
/*******************************************************************|
|**SIMULA��O PORCA DE WEBSERVICE PARA CONSUMO REST/JSON NO ANDROID**|
|*******************************************************************/

$idS = $_POST["idS"];			//pasta da escola
$action = $_POST["action"];		//pasta da a��o - define o retorno
$id = $_POST["id"];				//arquivo do aluno
$key = $_POST["key"];			//chave de seguran�a na app
$op = $_POST["op"];				//Parametro opcional para a��es que demandam mais de um ID (acessar pasta a��o(action)/pasta idUsr(id)/ json da a��o(op))
$op2 = $_POST["op2"];			//Parametro opcional2 para a��es que demandam mais de dois IDs (acessar pasta a��o(action)/pasta idTurma(id)/ pasta idMateria(op)/ json da a��o(op2))

$apiKey = "]}AnDr01)[{";		//chave de seguran�a no webservice 
$returnType = "js";				//tipo de retorno (xml ou js)

if($key == $apiKey){	
	if(!isset($op))
		$file =  "school".$idS."/".$action."/".$id.".".$returnType;
	else if(isset($op) && isset($op2))	
		$file =  "school".$idS."/".$action."/".$id."/".$op."/".$op2.".".$returnType;
	else
		$file =  "school".$idS."/".$action."/".$id."/".$op.".".$returnType;
	
	if(file_exists($file)){
		$fp = fopen($file, 'r');
		$content = fread($fp, filesize($file));
		echo $content;
		fclose($fp);
	}else{
		if(file_exists("school".$idS."/".$action) && !file_exists($file))
			$message = $file;//"Register not found";
		else if(!file_exists("school".$idS."/".$action))
			$message = "Incorrect action";

		echo "{\"message_error\"	: \"".$message."\"}";	
	}
}else if(!isset($idS) || !isset($action) || !isset($id) || !isset($key)){
	$message = "Incorrect parameters, please contact-us in mail: schoolmobile@jtony.net";
	echo "{\"message_error\"	: \"".$message."\"}";
}else{
	$message = "Incorrect API Key please contact-us in mail: schoolmobile@jtony.net";
	echo "{\"message_error\"	: \"".$message."\"}";
}
?>

 
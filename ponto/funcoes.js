// JavaScript Document

function novoColunista(){
	x = window.open('novo_colunista.php','pop','width=300,height=265');	
}

function getCampoFoto(){
	var n;
	n = document.frmNovoColunista;
	if (n.chkFoto.checked == true){
		document.getElementById('idFoto').style.display = '';
	}else{
		document.getElementById('idFoto').style.display = 'none';
	}
}
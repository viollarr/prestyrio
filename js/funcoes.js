// JavaScript Document
/*
Este script foi desenvolvido por Crystian Zini Valduga,
   com a finalidade de ajudar programadores web.
� muito f�cil copiar, por isso respeite quem teve a id�ia
   de faz�-lo. Amanha pode ser voc� no meu lugar.

   Abra�o

   Bom proveito

   Crystian
*/
var campos = new Array();
// Fun��o que adiciona os campos que v�o receber o foco
function addCampos(nome){
campos[campos.length] = nome;
}
// Fun��o que trata o evento do teclado.(Quando se clica no enter)
function enter(evt){
var ret = new Boolean(true);
   var tecla = (navigator.appName == 'Netscape') ? evt.keyCode : window.event.keyCode;
   var nome = (navigator.appName == 'Netscape')?evt.target.name: event.srcElement.name;
   var type = (navigator.appName == 'Netscape')?evt.target.type: event.srcElement.type;
   if(tecla == 13){
       if(type == "button") return true;
       ret = nextCampo(nome);
       return ret;
   }
   return ret;
}
// Fun��o que passa o foco para o pr�ximo campo.
function nextCampo(nome){
for(i=0; i< campos.length; i++){ //>
 if(campos[i]== nome){
           if(i==campos.length-1){
               obj = eval('document.forms[0].'+campos[0]);
               obj.focus();
               break;
           } else {
               obj = eval('document.forms[0].'+campos[i+1]);
               obj.focus();
               break;
           }
       }
   }
   return false;
}

// atribui��o do manipulador ao evento
if(navigator.appName=="Netscape") document.onkeypress = enter;
else document.onkeydown = enter;
///////////////////////////////////////////////////////////////////

function divIn(id,idS){
   var sDiv  = document.getElementById(id);
   var sSpan = document.getElementById(idS);
   
   if(sDiv.style.display == "none"){
		sDiv.style.display = "";
		sSpan.innerHTML = '<img src="images/seta_up.jpg" border="0">';
   }else{
		sDiv.style.display = "none";
		sSpan.innerHTML = '<img src="images/seta_down.jpg" border="0">';
   }
}

function fmtDate(campo, e)
{
    myVal = campo.value;

    if (myVal.length > 2 && !myVal.match(/\//))
    {
       myVal = '';
    }
    else
    {
        if (window.event)
        {
           keycode = window.event.keyCode;
        }
        else if (e)
        {
            keycode = e.which;
        }

        if (keycode < 48 || keycode > 57)
        {
            myVal = myVal.substr(0, (myVal.length - 1));
        }

        if (myVal.length == 2 || myVal.length.length == 5)
        {
            myVal += '/';
        }
    }
    campo.value = myVal;
}
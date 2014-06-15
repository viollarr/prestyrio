// JavaScript Document
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



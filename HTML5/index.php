<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Minha Página em HTML 5</title>
        <script type="text/javascript">
			function success(position) {
				var data_hora = document.getElementById('data_hora').value;
				alert('Data = '+data_hora+' Latitude = '+position.coords.latitude+' Logitude = '+position.coords.longitude)
			
			}
			function error(msg) {
				alert(msg);
			}
			function pegarChekin(){
				//alert('teste');
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(success, error);
				} else {
					error('Seu navegador não suporta Geolocalização!');
				}
			}
        </script>
    </head>
    <body>        
        <section>
            <article>
                <p><span id="status">Por favor aguarde enquanto nós tentamos locar você...</span></p>
            </article>
         </section>
    </body>
</html>

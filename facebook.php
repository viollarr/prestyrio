<html> 
   <head> 
     <title>Client Flow Example</title> 
   </head> 
   <body> 
   <script> 
     function displayUser(user) {
       var userName = document.getElementById('userName');
       var greetingText = document.createTextNode('Greetings, '
         + user.name + '.');
   userName.appendChild(greetingText);
     }

     var appID = "147571762002025";
     if (window.location.hash.length == 0) {
       var path = 'https://www.facebook.com/dialog/oauth?';
   var queryParams = ['client_id=' + appID,
     'redirect_uri=' + window.location,
     'response_type=token'];
   var query = queryParams.join('&');
   var url = path + query;
   window.open(url);
     } else {
       var accessToken = window.location.hash.substring(1);
       var path = "https://graph.facebook.com/me?";
   var queryParams = [accessToken, 'callback=displayUser'];
   var query = queryParams.join('&');
   var url = path + query;

   // use jsonp to call the graph
       var script = document.createElement('script');
       script.src = url;
       document.body.appendChild(script);        
     }
   </script> 
   <p id="userName"></p> 
   </body> 
  </html>
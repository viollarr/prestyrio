<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Untitled Document</title>
    </head>
    <body>
        <audio id="audio" src="mp3/audio.mp3"></audio>
        <div>
          <button onclick="document.getElementById('audio').play()">Reproduzir o áudio</button>
          <button onclick="document.getElementById('audio').pause()">Pausar o áudio</button>
          <button onclick="document.getElementById('audio').volume+=0.1">Aumentar o volume</button>
          <button onclick="document.getElementById('audio').volume-=0.1">Diminuir o volume</button>
        </div>
    </body>
</html>
<?php
require_once 'gmaps.class.php';
$gmaps = new gMaps;

# Carregar de  XML
//$gmaps->loadFromXML("markers.xml");
//$gmaps->getMarkers();exit;

# Adicionar pin/icons 
//$gmaps->addIcon("NOME ICON","IMAGEM.EXT","LARGURA","ALTURA");
$gmaps->addIcon( "pizza", "icons/pizza.png", "45", "45" );
$gmaps->addIcon( "kart", "icons/kart.png", "45", "45" );
$gmaps->addIcon( "green", "icons/green.png", "45", "45" );
$gmaps->addIcon( "imovel", "icons/imovel.png", "45", "45" );

# Adicionar marcador por latitude e longitute
//$gmaps->addMarker("LATITUDE","LONGITUDE","TEXTO HTML","ICON");
//$gmaps->addMarker("LATITUDE","LONGITUDE");
//$gmaps->addMarker("-23.5462057","-46.3022458","<p>Lanchonete João</p><p><img src=http://tinyurl.com/897xozw /></p><p>R$ 4,50</p>","pizza");
$gmaps->addMarker("-22.8722026","-43.3423262","<p>Werther</p>","pizza");

# Adicionar marcador por CEP e Numero
//$gmaps->addMarkerCep("CEP","NUMERO");
//$gmaps->addMarkerCep("08615000","555","<p>Mercado do José</p>","kart");
//$gmaps->addMarkerCep("08665060","110","<p>Imobiliária do Chico</p>","imovel");
//$gmaps->addMarkerCep("08664580","81","<p>Alguma Coisa Verde</p>","green");

# Adicionar marcador por endereco
//$gmaps->addMarkerAddress("RUA, NUMERO, CIDADE, SIGLA UF");
//$gmaps->addMarkerAddress( "Rua da Consolacao, 1200, Sao Paulo, SP","<h1>Boteco Allegro</h1><p><img src=http://tinyurl.com/897xozw /></p>","pizza");
//$gmaps->addMarkerAddress( "Av Paulista, 1000, Sao Paulo, SP","<h1>Hipermercado Allegro</h1>","kart");
//$gmaps->addMarkerAddress( "Av Paulista, 10, Sao Paulo, SP","<h1>Imobiliária Allegro</h1>","imovel");
//$gmaps->addMarkerAddress( "Av Dr Arnaldo, 10, Sao Paulo, SP","<h1>Coisa Verde</h1>","green");
$gmaps->addMarkerAddress( "Rua Pedro Rufino, 1186, Rio de Janeiro, RJ","<h1>HOME</h1>","imovel");


# Retorna array latitude e longitude por endereco
//print_r( $gmaps->getLatLon("Rua Ipes, 890, Suzano, SP") );

# Retorna endereco enviando CEP e num
//echo $gmaps->getAddressCep("08615060","890");

# Retornara latitude e longitude do CEP 08615060 numero 890
//print_r( $gmaps->getLatLon( $gmaps->getAddressCep("08615060","890") ) );

# Retornar todos os markers adicionados em JSon
$gmaps->getMarkers();
?>
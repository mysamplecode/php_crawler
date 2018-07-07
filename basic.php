<?php
// useful headers for this page...
header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); 
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate"); 
header ("Pragma: no-cache");

// The bot should be able to run with PHP version 5.3
// The strings encoding must be UTF-8

// Global variables:
$file_path = $argv[1]; // XML file path
$timeout = $argv[2]; // timeout (if time exceed this timeout, the bot has to stop parsing and save the stores it has found so far)
$start = new DateTime('now'); // when the bot starts
$xml_data  = ''; // content of XML file

// Includes
include_once 'constants.php';
include_once 'htmlDownloader.php'; // to download web page
include_once 'xmlGenerator.php'; // to create the XML file
require_once 'init_array.php';  // array containing the US states
include_once 'functions.php';


// Run
$bot = new parser();
$bot->run();
xmlGenerator::saveXML();


class parser
{
    public  $nombreTienda = 'Food Basics';

    public function run()
    {
        global $array_values_cities;

		// foreach zipcode, lat and lon
        foreach ($array_values_cities as $k => $st) {
            $url = 'http://foodbasics.apsupermarket.com/storelocator?location_lookup='.$st['zip_code'].'&location_lookup_geo='.$st['latitude'].'%2C'.$st['longitude'].'&banner=fb&form_id=storelocator_form';
            $this->crowlUrls($url);
            if(xmlGenerator::timeoutExceeded() == true) { return; }
        }
    }

    function crowlUrls($url) {

        $downloader = new htmlDownloader();
        $html = $downloader->get_html($url);
        functions::write_log($url);
        $market = array();
        $market[PAIS] = 'US';
        $market[NOMBRE_COMERCIO] = $this->nombreTienda;
        $market[URL] = 'http://pathmark.apsupermarket.com/storelocator';

        if (preg_match_all('/<li class=\"store\-number\-(.*?) store\-node\-.*?\">(.*?)<\/li>/is', $html, $marketUrl)) {
            for ($j = 0; $j < count($marketUrl[0]); $j++) {
                $this->crowlMarket($market, $marketUrl[0][$j]);
                if(xmlGenerator::timeoutExceeded() == true) { return; }
            }
        }
    }

    function crowlMarket(&$market, $html){

        $market[NOMBRE_TIENDA] = $this->nombreTienda;

        if (preg_match('/<div class=\\"store-title\\">(.*?)<\\/div>/is', $html, $m)){
            $market[VIA] = trim($m[1]);
        }
        if (preg_match('/<span class=\\"store-city\\">(.*?),/is', $html, $m)){
            $market[LOCALIDAD] = trim($m[1]);
        }
        if (preg_match('/<span class=\\"store-state\\">(.*?)<\\/span>/is', $html, $m)){
            $market[PROVINCIA] = trim($m[1]);
        }
        if (preg_match('/<span class=\\"store-zipcode\\">(.*?)<\\/span>/is', $html, $m)){
            if(functions::validate_zipcode(trim($m[1])))$market[CODIGO_POSTAL]=trim($m[1]);
        }
        if (preg_match('/<div id=\\"lat_homestore-.*?\\" class=\"latitude\">(.*?)<\\/div>/is', $html, $m)){
            $market[LATITUD] = trim($m[1]);
        }
        if (preg_match('/<div id=\\"lat_homestore-.*?\\" class=\"longitude\">(.*?)<\\/div>/is', $html, $m)){
            $market[LONGITUD] = trim($m[1]);
        }

        if (preg_match('/<div class=\\"store-phone\\">(.*?)<\\/div>/is', $html, $m)){
            $market[TELEFONO] = trim($m[1]);
        }
        if (preg_match('/<li class=\"store-number-(.*?) store/is', $html, $m)){
            $market[RDB_STORE] = trim($m[1]);
        }

        if(!empty($market[VIA]) || isset($market[VIA])){
            xmlGenerator::addMarket($market);
        }

        if(xmlGenerator::timeoutExceeded() == true) { return; }
    }

}

?>

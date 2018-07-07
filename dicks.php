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
include_once 'simple_html_dom.php';


// Run
$bot = new parser();
$bot->run();
xmlGenerator::saveXML();


class parser
{
    public  $nombreTienda = 'Dicks Sporting Goods';
    public $counter = 10198;
    public function run()
    {
        global $array_values_cities;
        // foreach zipcode, lat and lon
        foreach ($array_values_cities as $k => $st) {
            $url = "http://storelocator.dickssportinggoods.com/ajax?&xml_request=";
            $url .= urlencode("<request><appkey>AF6B23F0-1FAB-11E1-A284-AAEEA858831C</appkey><formdata id='locatorsearch'><dataview>store_default</dataview><limit>10</limit><atleast>10</atleast><geolocs><geoloc><addressline>{$st['zip_code']}</addressline><longitude>{$st['longitude']}</longitude><latitude>{$st['latitude']}</latitude></geoloc></geolocs><searchradius>25|50|100|250|500|1000</searchradius><stateonly>1</stateonly><nobf>1</nobf></formdata></request>");
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
        $market[URL] = 'http://storelocator.dickssportinggoods.com/';
        $xml =  str_get_html($html);
        foreach($xml->find('poi') as $store) 
        {
            $this->crowlMarket($market, $store);
            if(xmlGenerator::timeoutExceeded() == true) { return; }
        }
    }
    function crowlMarket(&$market, $xml){
        global $xml_data;
        $market[NOMBRE_TIENDA] = $this->nombreTienda;
        $market[VIA]            = trim($xml->find('name',0)->innertext);
        $market[LOCALIDAD]      = trim($xml->find('city',0)->innertext);
        $market[PROVINCIA]      = trim($xml->find('city',0)->innertext);
        $market[CODIGO_POSTAL]  = trim($xml->find('postalcode',0)->innertext);
        $market[LATITUD]        = trim($xml->find('latitude',0)->innertext);
        $market[LONGITUD]       = trim($xml->find('longitude',0)->innertext);
        $market[TELEFONO]       = trim($xml->find('phone',0)->innertext);
        $market[RDB_STORE]      = trim($xml->find('uid',0)->innertext);
        if(!empty($market[VIA]) || isset($market[VIA])){
            xmlGenerator::addMarket($market);
        }
        $this -> counter = $this -> counter + 1 ;
        if(xmlGenerator::timeoutExceeded() == true) { return; }
    }

}
?>

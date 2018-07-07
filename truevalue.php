<?php
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
    public $nombreTienda = "True Value";

    public function run()
    {
        global $array_values_cities;

		// foreach zipcode
        foreach($array_values_cities as $k => $xy){
            $url = 'http://www.truevalue.com/custserv/locate_store_ajax.cmd?setDomain=false&isMyAccount=false&form_state=locateStoreForm&UUID=&latitude=&longitude=&callBackFunc=&radius=1000&cityStateZip='.$xy['zip_code'];
            $this->crowlUrls($url);
            if(xmlGenerator::timeoutExceeded() == true) { return; }
        }

    }

    function crowlUrls($url) {
        $downloader = new htmlDownloader();
        $html = $downloader->get_html($url);

        if (preg_match_all('/var store = new Object\\(\\);(.*?)allStores\\[.*?\\] = store;/is', $html, $m)) {
            for ($i = 0; $i < count($m[0]); $i++) {
                $market = array();
                $this->crowlMarket($market, $m[1][$i]);
                if(xmlGenerator::timeoutExceeded() == true) { return; }
            }
        }
    }

    private function crowlMarket(&$market, $html) {
        $market[NOMBRE_COMERCIO] = $this->nombreTienda;
        $market[PAIS] = 'US';
        $market[URL] = 'http://www.truevalue.com/';
        ///GET INFO

        if (preg_match ('/store.ADDRESS_LINE_1 = "(.*?)";/is', $html, $m)){
            $market[VIA] = $m[1];
        }
        if (preg_match ('/store.ZIP_CODE = "(.*?)";/is', $html, $m)){
            $market[CODIGO_POSTAL] = $m[1];
        }
        if (preg_match ('/store.CITY = "(.*?)";/is', $html, $m)){
            $market[LOCALIDAD] = $m[1];
        }
        if (preg_match ('/store.STATE = "(.*?)";/is', $html, $m)){
            $market[PROVINCIA] = $m[1];
        }
        if (preg_match ('/store.PHONE = "(.*?)";/is', $html, $m)){
            $market[TELEFONO] = $m[1];
        }
        if (preg_match ('/store.STORE_NAME = "(.*?)";/is', $html, $m)){
            $market[NOMBRE_TIENDA] = $m[1];
        }
        if (preg_match ("/store.LATITUDE = '(.*?)';/is", $html, $m)){
            $market[LATITUD] = $m[1];
        }
        if (preg_match ("/store.LONGITUDE = '(.*?)';/is", $html, $m)){
            $market[LONGITUD] = $m[1];
        }
        if (preg_match ('/store.STORE_ID = "(.*?)";/is', $html, $m)){
            $market[RDB_STORE] = $m[1];
        }

        if(!empty($market[VIA]) || isset($market[VIA])){
            xmlGenerator::addMarket($market);
        }

        xmlGenerator::addMarket($market);
    }
}

?>

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
include_once 'htmlDownloader.php';
include_once 'xmlGenerator.php';

// Run
$bot = new parser();
$bot->run();
xmlGenerator::saveXML();


class parser 
{

	// 2 functions to use inside the parser class:
	// public static function addMarket(&$market) 
	//	--> xmlGenerator::addMarket($market)
	// public static function timeoutExceeded()
	//  --> if(xmlGenerator::timeoutExceeded() == true) { /*stop main function*/ }

    public function run()
	{
        ...
    }

    ...
}

?>

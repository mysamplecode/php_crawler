<?php

include_once 'functions.php';
$StoresAdded_IDs = array();

class xmlGenerator
{

    public static function addMarket(&$market)
    {
        global $xml_data;
        global $StoresAdded_IDs;
        if (isset($market[RDB_STORE]))
        {
            if (in_array($market[RDB_STORE], $StoresAdded_IDs))
            {
                return;
            }
            else
            {
                array_push($StoresAdded_IDs, $market[RDB_STORE]);
            }
        }

        if(isset($market[CODIGO_POSTAL])){
            if(functions::validate_zipcode($market[CODIGO_POSTAL])== false) return;
        }
        $xml_data_store = "<store>";
        if (isset($market[NOMBRE_COMERCIO]))
            $xml_data_store .= "<retailer value=\"" . xmlGenerator::xml_entities($market[NOMBRE_COMERCIO]) . "\" />";
        if (isset($market[PAIS]))
            $xml_data_store .= "<country code=\"" . xmlGenerator::xml_entities($market[PAIS]) . "\" />";
        if (isset($market[URL]))
            $xml_data_store .= "<url value=\"" . xmlGenerator::xml_entities($market[URL]) . "\" />";
        if (isset($market[NOMBRE_TIENDA]))
            $xml_data_store .= "<name value=\"" . xmlGenerator::xml_entities($market[NOMBRE_TIENDA]) . "\" />";
        if (isset($market[VIA]))
            $xml_data_store .= "<address value=\"" . xmlGenerator::xml_entities($market[VIA]) . "\" />";
        if (isset($market[CODIGO_POSTAL]))
            $xml_data_store .= "<postcode value=\"" . xmlGenerator::xml_entities($market[CODIGO_POSTAL]) . "\" />";
        if (isset($market[LOCALIDAD]))
            $xml_data_store .= "<town value=\"" . xmlGenerator::xml_entities($market[LOCALIDAD]) . "\" />";
        if (isset($market[PROVINCIA]))
            $xml_data_store .= "<region type=\"state\" value=\"" . xmlGenerator::xml_entities($market[PROVINCIA]) . "\" />";
        if (isset($market[TELEFONO]))
            $xml_data_store .= "<phonenumber value=\"" . xmlGenerator::xml_entities($market[TELEFONO]) . "\" />";
        if (isset($market[LATITUD]) && isset($market[LONGITUD]))
            $xml_data_store .= "<coordinates lat=\"" . $market[LATITUD] . "\" lon=\"" . $market[LONGITUD] . "\" />";
        if (isset($market[CENTRO]))
            $xml_data_store .= "<shoppingcentre value=\"" . xmlGenerator::xml_entities($market[CENTRO]) . "\" />";
        if (isset($market[DESCRIPCION]))
            $xml_data_store .= "<description value=\"" . xmlGenerator::xml_entities($market[DESCRIPCION]) . "\" />";
        if (isset($market[RDB_STORE]))
            $xml_data_store .= "<id value=\"" . xmlGenerator::xml_entities($market[RDB_STORE]) . "\" />";
        if (isset($market[HORARIO]))
        {
            //$xml_data_store .= "<openinghours>";
            for ($i = 1; $i <= 7; $i++)
            {
                if (isset($market[HORARIO][$i]))
                {
                    for ($j = 1; $j <= 3; $j++)
                    {
                        if (isset($market[HORARIO][$i][$j]))
                        {

                            $hora_inicio = $market[HORARIO][$i][$j][HORA_INICIO];
                            if(strlen($hora_inicio)==1){
                                $hora_inicio = "0" . $hora_inicio;
                            }
                            $minuto_inicio = $market[HORARIO][$i][$j][MINUTO_INICIO];
                            if(strlen($minuto_inicio)==1){
                                $minuto_inicio = "0" . $minuto_inicio;
                            }
                            $hora_fin = $market[HORARIO][$i][$j][HORA_FIN];
                            if(strlen($hora_fin)==1){
                                $hora_fin = "0" . $hora_fin;
                            }
                            $minuto_fin = $market[HORARIO][$i][$j][MINUTO_FIN];
                            if(strlen($minuto_fin)==1){
                                $minuto_fin = "0" . $minuto_fin;
                            }


                            $xml_data_store .= "<openinghours weekday=\"" . $i . "\" "
                                . "from=\"" . $hora_inicio . ":" . $minuto_inicio . "\" "
                                . "till=\"" . $hora_fin . ":" . $minuto_fin . "\" "
                                . "/>";
                        }
                    }
                }
            }
            //$xml_data_store .= "</openinghours>";
        }
        $xml_data_store .= "</store>";
        $xml_data .= $xml_data_store;
        echo "\nNew store";
    }

    



    public static function xml_entities($string)
    {
        $result = strtr
        (
            $string,
            array(
                "<" => "&lt;",
                ">" => "&gt;",
                '"' => "&quot;",
                "'" => "&apos;",
                "&" => "&amp;",
            )
        );
        $result = iconv("utf-8", "utf-8//ignore", $result);
        $result = trim($result);
        return $result;
    }

    public static function closeXML()
    {
        global $xml_data;
        $xml_data = ">\n<bot>" . $xml_data;
        $xml_data = "?" . $xml_data;
        $xml_data = "xml version=\"1.0\" encoding=\"UTF-8\"" . $xml_data;
        $xml_data = "?" . $xml_data;
        $xml_data = "<" . $xml_data;
        $xml_data = $xml_data . "</bot>";
    }
    public static function saveXML()
    {
        xmlGenerator::closeXML();
        global $xml_data;
        global $file_path;
        $fp = fopen($file_path, 'w');
        fwrite($fp, $xml_data);
    }


    public static function timeoutExceeded()
    {
        global $timeout;
        global $start;
        $now = new DateTime('now');
        echo "\nTime: " . $now->format('Y-m-d H:i:s');;
        $diff = $now->getTimestamp() - $start->getTimestamp();
        if ($diff > 60 * $timeout)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}



?>
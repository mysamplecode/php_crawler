<?php

class functions{

    public static function validate_zipcode($val){

        if(preg_match("/[0-9]{5}.*?[0-9]{4}|[0-9]{5}|[0-9]{5}-[0-9]{4}/is",$val)) {
            return true;
        }
        return false;

    }
	
	public static function valHourMin($val){
	
		if(preg_match("/[0-9]{2}|[0-9]{1}/is",$val) && is_numeric($val)) {
			return true;
		}
		 return false;
	}

    public static function write_log($cadena,$tipo ='')
    {
        return;
        $arch = fopen("/tiendeo/logs/milog_".date("Y-m-d").".txt", "a+");

        fwrite($arch, "[".date("Y-m-d H:i:s.u")." - $tipo ] ".$cadena."\n");
        fclose($arch);
    }
}

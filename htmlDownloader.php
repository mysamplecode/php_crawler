<?php

class htmlDownloader{
    public function get_html($url, $post = '', $curlParameters = '')
	{
        $ch = curl_init();
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'tiendeoBot');
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($post){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($curlParameters){
            foreach ($curlParameters as $key => $value){
                curl_setopt($ch, $key, $value);
            }
        }

		$html = curl_exec($ch);

		if (curl_errno($ch))
		{
			print curl_error($ch);
		}

		curl_close($ch);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = str_replace("\xc2\xa0",' ',$html);
        return $html;
    }
	
}

?>

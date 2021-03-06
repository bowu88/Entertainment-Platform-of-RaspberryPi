<?php
	//Curl Get Function
	function curl_get($url)
	{
	    $refer = "http://music.163.com/";
	    $header[] = "Cookie: " . "appver=1.5.0.75771;";
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	    curl_setopt($ch, CURLOPT_REFERER, $refer);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}

	//Search Function
	function music_search($word,$type,$offset){
		$url = "http://music.163.com/api/search/pc";
	    $post_data = array(
	        's' => $word,
	        'offset' => $offset,
	        'limit' => '20',
	        'type' => $type,
	    );
	    $referrer = "http://music.163.com/";
	    $URL_Info = parse_url($url);
	    $values = [];
	    $result = '';
	    $request = '';
	    foreach ($post_data as $key => $value) {
	        $values[] = "$key=" . urlencode($value);
	    }
	    $data_string = implode("&", $values);
	    if (!isset($URL_Info["port"])) {
	        $URL_Info["port"] = 80;
	    }
	    $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
	    $request .= "Host: " . $URL_Info["host"] . "\n";
	    $request .= "Referer: $referrer\n";
	    $request .= "Content-type: application/x-www-form-urlencoded\n";
	    $request .= "Content-length: " . strlen($data_string) . "\n";
	    $request .= "Connection: close\n";
	    $request .= "Cookie: " . "appver=1.5.0.75771;\n";
	    $request .= "\n";
	    $request .= $data_string . "\n";
	    $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
	    fputs($fp, $request);
	    $i = 1;
	    while (!feof($fp)) {
	        if ($i >= 15) {
	            $result .= fgets($fp);
	        } else {
	            fgets($fp);
	            $i++;
	        }
	    }
	    fclose($fp);
	    return $result;
	}

	//Music Detail Function
	function music_info($music_id){
	    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
	    return curl_get($url);
	}
	
	//Artist-Album Function
	function artist_album($artist_id, $limit){
	    $url = "http://music.163.com/api/artist/albums/" . $artist_id . "?limit=" . $limit;
	    return curl_get($url);
	}
	
	//Album Detail Function
	function album_info($album_id){
	    $url = "http://music.163.com/api/album/" . $album_id;
	    return curl_get($url);
	}

	//Music Download Function
	function download($music_url,$music_name){
		$content = http_get_data($music_url);
		//Please Replace the route below when you deploy it on your raspberry pi
    	//And use chmod to make it writeable
    	$dir = '/Library/WebServer/Documents/download/';
    	
    	$filename = $music_name.'.mp3';
    	$route = $dir.$filename;
    	//Bond stream to a file
    	$fp = @fopen($route,"a");
    	//Write a file
    	fwrite($fp, $content);
    	return $route;
	}

	//HTTP GET Data function
	function http_get_data($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();
		$return_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		return $return_content;
	}

?>
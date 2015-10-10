<?php

    $__ROOT__ = dirname(__FILE__)."/..";
    
    require_once $__ROOT__."/config.php";

    function getConnexion()
    {
        return new PDO('mysql:host='.__DBHOST__.';dbname='.__DBNAME__, __DBUSER__, __DBPASS__);
    }
    
    function getHash($pw, $user)
    {
        $user_salt = md5($pw.$user);
        
        $combine = $pw.$user_salt.__SITEKEY__;
        
        $hash = hash('sha512', $combine);
    
        return $hash;
    }
	
    function createThumbnail($path, $img)
    {
        $query = "convert -resize 100x144 ".$path.$img." ".$path."thumbs/".$img;
        exec($query, $string);
    }
    
	function apiKeyExist($key)
	{
		$db = getConnexion();
		
		if($db)
		{
			$sql = "SELECT id FROM users where api_key = :api_key";
			
			$stmt = $db->prepare($sql);
			
			if($stmt->execute(array("api_key" => $key)))
			{
				return ($stmt->fetch(PDO::FETCH_ASSOC) != false);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
    
    function getApiKey($pw, $user)
    {
        $hash = getHash($pw, $user);
        
        $api_key = md5($hash);
        
        return strtoupper($api_key);
    }
    
    function insertUrl($db, $url)
    {
		$id = rand(10000,99999);
		$shorturl = base_convert($id, 20, 36);

		$stmt = $db->prepare('INSERT INTO push (url, shorten)'.
                                     'VALUES (:url, :shorten)');

		$stmt->execute(array('url' => $url, 'shorten' => $shorturl));
		
		return $shorturl;
    }
    
    function urlExist($db, $url)
    {
        $stmt = $db->prepare('SELECT url FROM push WHERE url = :url');
        
        if($stmt->execute(array("url" => $url)))
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row)
            {
                return true;
            }
            else
            {
                return false;
            }
        }   
    }
    
    function cleanString($string)
    {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-.]/', '_', $string);
    }
    
    function getMimeType($path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $path);
        
        finfo_close($finfo);
        
        return $mime;
    }
    
    function createShortLink($url)
    {
        $shorturl = "";
    	try
	    {
		    $db = getConnexion();
	    }
	    catch(PDOException $e)
	    {
		    echo($e);
	    }
	
	    if($db)
	    {
	      	if(!urlExist($db, $url))
	        {
	            $shorturl = insertUrl($db, $url);
	            return $_SERVER["SERVER_NAME"]."/".$shorturl;
	        }
	        else
	        {
	            return null;
	        }
	    }
	    else
	    {
		    return null;
	    }
	    
	    return null;
	}
?>

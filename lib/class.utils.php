<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MMWP_Utils
 {	
 
 	public static function getPluginName(){
 		$path = dirname(dirname(__FILE__));
 		return array_pop(explode("/", $path));
 	}
 	
 	public static function getImageUrl($imageName)
 	{
 		$imageUrl = get_option("siteurl")."/wp-content/plugins/".self::getPluginName()."/images/";
 		$filePath = dirname(dirname(__FILE__))."/images/".$imageName.".png";
 		
 		switch ($imageName)
 		{
 			default:
 				if(file_exists($filePath))
 					$imageUrl .= $imageName.".png";
 				break;
 		}
 		return $imageUrl;
 	}
 	
 	public static function constructPageUrl() {
		$pageURL = 'http';
		
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		return $pageURL;
	}
 	 
 }
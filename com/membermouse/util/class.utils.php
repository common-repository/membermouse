<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_Utils
 {	
 	public static $MM_PASSWORD_KEY = "MM234897";
 
 	public static function testMe(){
 		echo "test me!";
 	}
 	
 	public static function isGetParamAllowed($getParam){
 		global $reservedGetParams;
	 	if(!is_admin()){
	 		$key = strtolower($getParam);  
	 		if(isset($reservedGetParams[$key])){
	 			return false;
	 		}
	 	}
	 	return true;
 	}
 	
	public static function isLimeLightInstall(){
		if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE)==MM_Site::$INSTALL_TYPE_LIMELIGHT)
		{
			return true;
		}
		return false;
	}
 
	public static function getGatewayMethodObj($paymentType){
		$paymentMethods = MM_CampaignOptions::getOptionRow("gateway");
		if(count($paymentMethods)>0){
			foreach($paymentMethods as $id=>$row){
				$row->attr = json_decode($row->attr);
			
				if(strtolower($row->attr->hidden_paymentObject) == strtolower(get_class($paymentType))){
					return $row;
				}
			}
		}
		return false;
	}
 	
	public static function getPaymentMethodObj($paymentType, $onlyShownOnReg=false){
		$paymentMethods = MM_CampaignOptions::getOptionRow("payment",$onlyShownOnReg);
		if(count($paymentMethods)>0){
			foreach($paymentMethods as $id=>$row){
				$row->attr = json_decode($row->attr);
			
				if(strtolower($row->attr->hidden_paymentObject) == strtolower(get_class($paymentType))){
					return $row;
				}
			}
		}
		return false;
	}
	
 	public static function convertArrayToObject($arr){
 		if(is_array($arr)){
 			$info = new stdClass();
 			foreach($arr as $k=>$v){
 				$info->$k = $v;
 			}
 			return $info;
 		}
 		return new stdClass();
 	}
 	
 	public static function loadFile($file){
		if(file_exists($file)){
			return file_get_contents($file);
		}
		return "";
	}
	
	public static function getReferrer(){
		if(isset($_SERVER["HTTP_REFERER"])){
			return $_SERVER["HTTP_REFERER"];
		}
		return "";
	}
	
	public static function createOptionsArray($obj, $idLabel, $valueLabel){
		$retArr = array();
		if(is_array($obj)){
			foreach($obj as $row){
				if(isset($row->$idLabel) && isset($row->$valueLabel)){
					$retArr[$row->$idLabel] = $row->$valueLabel;
				}
			}
		}
		return $retArr;
	}
	
	public static function explode($needle, $haystack)
	{
		$arr = explode($needle, $haystack);
		if(is_array($arr)){
			foreach($arr as &$value){
				$value = urldecode($value);
			}
		}
		return $arr;
	}
	
	 public static function isURL($url = null) {
	        if(is_null($url)){
	        	return false;
	        }
	
	        $protocol = '(http://|https://)';
	        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
	
	        $regex = "^". $protocol . // must include the protocol
	                         '(' . $allowed . '{1,63}\.)+'. // 1 or several sub domains with a max of 63 chars
	                         '[a-z]' . '{2,6}'; // followed by a TLD
	        if(eregi($regex, $url)===true){
	        	return true;
	        }
	        else{
	        	return false;
	        }
	}
	
 	public static function appendUrlParam($url, $paramKey, $paramVal, $urlencode=true)
 	{
 		if($urlencode)
 		{
 			$paramVal = urlencode($paramVal);
 		}
 		
 		if(preg_match("/(\?)/", $url))
 		{
 			return $url."&".$paramKey."=".$paramVal;	
 		}
 		return $url."?".$paramKey."=".$paramVal;
 	}
 	
 	public static function chooseRandomAssocOption($options){
 		if(is_array($options)){
 			$key = array_rand($options,1);
 			return $options[$key];
 		}
 		return "";
 	}
 	
 	public static function chooseRandomOption($options){
 		if(is_array($options)){
 			$index = rand(0, count($options)-1);
 			return $options[$index];
 		}
 		return "";
 	}
 	
 	public static function createRandomString($length=7, $onlyAlpha=false, $onlyDigits=false) { 
 		$chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
 		if($onlyAlpha){
 			$chars = "abcdefghijkmnopqrstuvwxyz";
 		}
 		else if($onlyDigits){
 			$start = str_pad("1", $length, "0", STR_PAD_RIGHT);
 			$end = str_pad("9", $length, "9", STR_PAD_RIGHT);
 			return rand($start, $end);
 		}
	    srand((double)microtime()*1000000); 
	    $i = 0; 
	    $pass = '' ; 
	
	    while ($i <= $length) { 
	        $num = rand() % 33; 
	        $tmp = substr($chars, $num, 1); 
	        $pass = $pass . $tmp; 
	        $i++; 
	    } 
	
	    return $pass; 
	} 
 	
 	public static function calculateDaysDiff($startDate, $endDate)
 	{
 		$day = 86400; 
		$start_time = strtotime($startDate);
		$end_time = strtotime($endDate); 
		
		return (round($end_time - $start_time) / $day) + 1;
 	}
 	
 	public static function getFilesFromDir($directory, $recursive = false, $includeDirs = false, $pattern = '/.*/')
	{
		$items = array();
		
		if($handle = opendir($directory)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != '.' && $file != '..') {
					$path = "$directory/$file";
					$path = preg_replace('#//#si', '/', $path);
					if (is_dir($path)) {
						if ($includeDirs) {
							$items[] = $path;
						}
						if ($recursive) {
							$items = array_merge($items, self::getFilesFromDir($path, true, $includeDirs, $pattern));
						}
					}
					else {
						if (preg_match($pattern, $file)) {
							$items[] = $path;
						}
					}
				}
			}
			
			closedir($handle);
		}
		
		sort($items);
		
		return $items;
	}
	
	public static function getClientIPAddress()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	    {
	      $ip = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip = $_SERVER['REMOTE_ADDR'];
	    }
	    
	    return $ip;
	}
	
 	public static function getImageUrl($imageName)
 	{
 		$imageUrl = MM_IMAGES_URL;
 		
 		switch ($imageName)
 		{
 			default:
 				if(file_exists(MM_IMAGES_PATH."/".$imageName.".png"))
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
 	
 	public static function getStatusImage($status) {
	 	if($status == '1') {
	    	return '<img src="'.MM_Utils::getImageUrl("bullet_green").'" title="Active" />';
	    }
	    else if($status == '0') {
	    	return '<img src="'.MM_Utils::getImageUrl("bullet_red").'" title="Inactive" />';
	    }
	    else
	    {
	    	return MM_NO_DATA;
	    }
 	}
 	
	public static function isAdmin($userId=0)
	{
		if($userId>0){
			$capabilities = get_usermeta($userId,"wp_capabilities");
			
			if(isset($capabilities["administrator"]) && $capabilities["administrator"]=="1"){
				return true;
			}
			return false;
		}
		return current_user_can('manage_options');
	}
 	
	public static function encryptPassword($password) 
	{ 
		for($i=0; $i<5; $i++) {
			$password = strrev(base64_encode($password)); 
		}
		 
		return $password;
	} 
	
 	public static function decryptPassword($password) 
	{ 
		for($i=0; $i<5; $i++) {
	   	 	$password = base64_decode(strrev($password));
	  	}
	  	
	  	return $password;
	} 
 }
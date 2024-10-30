<?php
class Utils
{	
	public static function logRequest($message, $request=""){
		if(class_exists("MM_LogApi")){
			$api = new MM_LogApi();
			$api->setIpAddress($_SERVER["REMOTE_ADDR"]);
			$api->setMessage($message);
			$api->setRequest($request);
			$api->commitData();
		}
	}
	
	public static function logEvent($ip, $ref)
	{
		global $wpdb;
		$sql ="insert into ".TABLE_ACCESS_LOG." set 
				ip='{$ip}',	
				referring_url='".mysql_escape_string($ref)."'";
		$wpdb->query($sql);	
	}
	
	public static function isAuthenticated($post, $rest)
	{
		global $wpdb;
		if(!isset($post["apikey"]) || !isset($post["apisecret"])){
			return false;
		}
		else
		{
	        if($post["apikey"] == null || $post["apisecret"] == null || !preg_match("/^[a-zA-Z0-9]+$/",$post["apikey"]))
	        	return false;
	        	
	      	$sql = "select count(id) as total from ".TABLE_ACCESS_KEYS." 
	      					where 
	      						api_key='".mysql_escape_string($post["apikey"])."' AND 
	      						api_secret='".mysql_escape_string($post["apisecret"])."' AND 
	      						status='1' 
	      						
	      			";
	      	LogMe::write("isAuthenticated() : ".$sql);
	      	$row = $wpdb->get_row($sql);
	      	if(is_object($row))
	      		return ($row->total>0);
		}
        return true;
	}
	
}
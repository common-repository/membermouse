<?php
class MM_Url{
	
	private $url ="";
	private $ssl = "";
	
	function __construct($url=null){
		if(isset($_SERVER["HTTP_HOST"])){
			$this->url = (is_null($url))?$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]:$url;
			if(!preg_match("/(http)/", $this->url)){
				if($this->isSSL()){
					$this->url  = "https://".$this->url;
				}
				else{
					$this->url = "http://".$this->url;
				}
			}
			LogMe::write("MM_Url.__construct() : ".$this->url);
 			$this->setSSLUrl();
		}
	}
	
	public function hasSSL(){
		$checked = MM_OptionUtils::getOption("mm-ssl");
		if($checked=='1'){
			return true;
		}
		return false;
	}
	
	public static function sslInstalled(){
		$url = preg_replace("/(http\:\/\/)/", "", MM_OptionUtils::getOption("siteurl"));
		if(preg_match("/(https)/", $url)){
			return true;
		}
		
		$sslCheck = @fsockopen("ssl://".$url."/", 443, $errno, $errstr, 30); 
		if (!$sslCheck) { 
			return false;
		} 
		fclose($sslCheck); 
		return true;
	}
	
	public function forceSSL(){
		LogMe::write("forceSSL() : redirecting to ".$this->ssl);
		wp_redirect($this->ssl);
		exit;
	}
	
	public function forceHTTP(){
		$this->url = preg_replace("/(https)/", "http", $this->url);
		LogMe::write("forceSSL() : redirecting to ".$this->url);
		wp_redirect($this->url);
		exit;
	}
	
 	public function isSSL(){
 		if(isset($_SERVER["HTTPS"])){
		 	if($_SERVER["HTTPS"] == "on" || preg_match("/(https)/", $this->url))
			{
				return true;		
			}
 		}
 		return false;
 	}
 	
 	private function setSSLUrl(){
 		$this->ssl = preg_replace("/(http\:)/", "https:", $this->url);
 	}
 	
 	public function get(){
 		return $this->url;
 	}
 	
 	public function set($url){
 		$this->url = $url;
 		$this->setSSLUrl();
 	}
}
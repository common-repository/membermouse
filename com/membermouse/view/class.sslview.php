<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_SSLView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_CONFIRM_SSL:
					return $this->confirmSSL($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function isValidSSL(){
		return MM_Url::sslInstalled();
	}
	
	private function saveSSL($post){
		if(isset($post["use_ssl"]) && $post["use_ssl"]=="1"){
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL, 1);
		
			if(isset($post["use_ssl_admin"]) && $post["use_ssl_admin"]=='1'){
				MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN, 1);
			}
			else{
				MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN, 0);
			}
		}
		else{
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL, 0);
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN, 0);
		}
		return new MM_Response("SSL settings saved successfully.");
	}
	
	private function confirmSSL($post){
		if(isset($post["use_ssl"]) && $post["use_ssl"]=="1"){
			if(!isset($post["forceSSL"]) && $this->isValidSSL()){
				return $this->saveSSL($post);
			}
			else if(!isset($post["forceSSL"]) && !$this->isValidSSL()){
				return new MM_Response("Invalid SSL Certificate", MM_Response::$ERROR);
			}
			else if(isset($post["forceSSL"])){
				return $this->saveSSL($post);
			}
		}
		return $this->saveSSL($post);
	}
}
?>
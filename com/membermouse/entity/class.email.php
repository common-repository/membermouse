<?php
class MM_Email
{
	public static $BR = "<br>";
	
	private $subject;
	private $body;
	private $toName;
	private $ccAddress = array();
	private $toAddress;
	private $fromName;
	private $fromAddress;
	private $context;
 	
 	public function send()
 	{
 		$req = array();
 		
 		// Simple checking here as we should expect well-formed emails from validation methods.
 		if(empty($this->toAddress)) {
 			return new MM_Response("Not a valid 'to address'.", MM_Response::$ERROR);	
 		}
 		
 		if(empty($this->fromAddress)) {
 			return new MM_Response("Not a valid 'from address' email address.", MM_Response::$ERROR);	
 		}
 		
 		if(empty($this->fromName)) {
 			return new MM_Response("No 'from name' supplied.", MM_Response::$ERROR);	
 		}
 		
 		if(empty($this->body)) {
 			return new MM_Response("Body may not be empty.", MM_Response::$ERROR);	
 		}
 		
 		if(empty($this->subject)) {
 			return new MM_Response("Subject may not be empty.", MM_Response::$ERROR);	
 		}
 		
 		if($this->context instanceof MM_Context) 
 		{
 			$subject = MM_SmartTagEngine::processContent($this->subject, $this->context);
 			$body = MM_SmartTagEngine::processContent($this->body, $this->context);
 		}
 		else 
 		{	
 			$subject = $this->subject;
 			$body = $this->body;
 		}
 		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		if(is_array($this->ccAddress) && count($this->ccAddress)>0){
			foreach($this->ccAddress as $email=>$name){
				$name = (empty($name))?$email:$name;
				$headers .= 'Cc: '.$name.' <'.$email.'>' . "\r\n";
			}	
		}
		
		$headers .= 'From: '.$this->fromName.' <'.$this->fromAddress.'>' . "\r\n";
		
		LogMe::write("MM_Email.send() : ".$headers.$body);
	
		@wp_mail($this->toAddress, $subject, nl2br($body), $headers, array());
		
		return new MM_Response();	
 	}
 
 	public function setSubject($str)
 	{
 		$this->subject = $str;
 	}
 	
 	public function getSubject()
 	{
 		return $this->subject;
 	}
 
 	public function setBody($str)
 	{
 		$this->body = $str;
 	}
 	
 	public function getBody()
 	{
 		return $this->body;
 	}
 
 	public function setToName($str)
 	{
 		$this->toName = $str;
 	}
 	
 	public function getToName()
 	{
 		return $this->toName;
 	}
 
 	public function setToAddress($str)
 	{
 		$this->toAddress = $str;
 	}
 	
 	public function getToAddress()
 	{
 		return $this->toAddress;
 	}
 
 	public function setFromName($str)
 	{
 		$this->fromName = $str;
 	}
 	
 	public function getCC(){
 		return $this->ccAddress;
 	}
 	
 	public function addCC($email, $name=""){
 		$this->ccAddress[$email] = $name;
 	}
 	
 	public function getFromName()
 	{
 		return $this->fromName;
 	}
 
 	public function setFromAddress($str)
 	{
 		$this->fromAddress = $str;
 	}
 	
 	public function getFromAddress()
 	{
 		return $this->fromAddress;
 	}
 	
 	public function setContext(MM_Context $context)
 	{
 		$this->context = $context;
 	}
 	
 	public function getContext()
 	{
 		return $this->context;
 	}
 	
}
?>
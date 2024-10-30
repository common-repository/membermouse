<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Response
{
	public static $SUCCESS = "success";
	public static $ERROR = "error";
	
	public $message = "";
	public $type = "";
	
	public function __construct($msg="", $type="success") 
 	{
 		if(isset($msg))
 		{
 			$this->message = $msg;
 		}
 		
 		if(isset($type)) 
 		{
 			$this->type = $type;
 		}
 	}
}
?>

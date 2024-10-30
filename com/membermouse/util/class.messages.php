<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Messages
{
	public static function addError($str)
	{
		$errors = MM_Session::value(MM_Session::$KEY_ERRORS);
		
		if(!$errors) {
			$errors = array();
		}
		foreach($errors as $error){
			if($error == $str){
				return false;
			}
		}
		$errors[] = $str;
		
		MM_Session::value(MM_Session::$KEY_ERRORS, $errors);
	}
	
	public static function get($key)
	{
		return MM_Session::value($key);
	}
	
	public static function addMessage($str)
	{
		$msgs = MM_Session::value(MM_Session::$KEY_MESSAGES);
		
		if(!$msgs) {
			$msgs = array();
		}
		
		$msgs[] = $str;
		
		MM_Session::value(MM_Session::$KEY_MESSAGES, $msgs);
	}
	
	public static function clear()
	{
		MM_Session::clear(MM_Session::$KEY_ERRORS);
		MM_Session::clear(MM_Session::$KEY_MESSAGES);
	}
}
?>

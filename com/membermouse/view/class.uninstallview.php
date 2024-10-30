<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_UninstallView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_UNINSTALL:
					return $this->uninstall();
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function uninstall()
	{	
		$install = new MM_Install();
		$result = $install->uninstall();
		
		if($result) {
			return new MM_Response("MemberMouse was uninstalled successfully.");
		}
		else {
			return new MM_Response("An error occured during the uninstallation process. Please check the latest MemberMouse log for details.", MM_Response::$ERROR);
		}
	}
}
?>

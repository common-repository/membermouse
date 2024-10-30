<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_DashboardView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->toggleGuide($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function toggleGuide($post)
	{	
		if(isset($post["toggle_value"])) {
			MM_OptionUtils::setOption(MM_OPTION_SHOW_GUIDE, $post["toggle_value"]);
			
			return new MM_Response();
		}
		else {
			return new MM_Response("Could not toggle getting started guide. Invalid toggle value received.", MM_Response::$ERROR);
		}
	}
}
?>

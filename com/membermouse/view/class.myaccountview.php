<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MyAccountView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_CONFIRM_AT_CANCEL:
					return $this->confirmCancel($post);
					
				case self::$MM_JSACTION_DEACTIVATE_ACCESS_TAG:
					$membershipDetails = new MM_MemberDetailsView();
					return $membershipDetails->deactivateAccessTag($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function confirmCancel($post){
		
		return new MM_Response("Are you sure you want to cancel this subscription?");
	}
}
?>
<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_RegistrationSettingsView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE_TERMS:
					return $this->saveTerms($post);
				
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function saveTerms($post) 
	{
		if(isset($post["mm_include_terms_on_reg"]) && isset($post["mm_terms_and_conditions"]))
		{
			if($post["mm_include_terms_on_reg"] == "yes") {
				MM_OptionUtils::setOption(MM_OPTION_TERMS_STATUS, "1");
			}
			else {
				MM_OptionUtils::setOption(MM_OPTION_TERMS_STATUS, "0");
			}
			
			MM_OptionUtils::setOption(MM_OPTION_TERMS_CONTENT, $post["mm_terms_and_conditions"]);
			
			return new MM_Response("Terms and Conditions saved successfully");
		}
		else {
			return new MM_Response("Error saving terms and conditions. Some required parameters are missing.", MM_Response::$ERROR);
		}
	}
}
?>

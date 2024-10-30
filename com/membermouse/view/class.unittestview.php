<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_UnitTestView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_UNIT_TEST_GROUP1:
					return $this->runBasicTest($post);
					
				case self::$MM_JSACTION_UNIT_TEST_GROUP2:
					return $this->runProcessTest($post);
					
				case self::$MM_JSACTION_UNIT_TEST_GROUP3:
					return $this->runCustomTest($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function runBasicTest($post){
		$objs = array(
			'MM_User'=>new MM_User(false),
			'MM_AccountType'=>new MM_AccountType(false),
			'MM_MemberType'=>new MM_MemberType(false),
			'MM_OrderHistory'=>new MM_OrderHistory(false),
		);
		
		$responses = "";
		foreach($objs as $objName=>$objRef){
			$unitTest = new MM_UnitTestEngine();
			$result = $unitTest->runEntityTest($objRef);
			if($result instanceof MM_Response){
				return $result;
			}
			$responses .= $result;
		}
		
		return new MM_Response($responses);
	}
	
	private function runProcessTest($post){
		$unitTest = new MM_UnitTestEngine();
		$responses = $unitTest->runProcessTest();
	
		if($responses instanceof MM_Response){
			return $responses;
		}
		
		return new MM_Response($responses);
		
	}
	
	private function runCustomTest($post){
		$responses = "";
		
		if(isset($post["results"])){
			$responses.= "/******* Javascript instantiation *****/\n";
			$responses.=$post["results"]."\n";
		}
		
		$unitTest = new MM_UnitTestEngine();
		$response = $unitTest->runCustomTest();
		if($response instanceof MM_Response){
			return $response;
		}
		$responses .= $response;
		
		return new MM_Response($responses);
		
	}
	
	public function getData(MM_DataGrid $dg)
	{
		return array();
	}
	
}
?>

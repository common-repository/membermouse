<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_IPNTestView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_CHOOSE_FORM:
					return $this->chooseForm($post);
					
				case self::$MM_JSACTION_TEST_CALL_API:
					return $this->testApiMethod($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	public function getData(MM_DataGrid $dg)
	{
		return array();
	}
	
	private function makeRequest($data){
		
		$url = MM_OptionUtils::getOption("siteurl")."/wp-content/plugins/".MM_PLUGIN_NAME."/modules/ipn/callback.php";
		$postvars = "";
		foreach($data as $k=>$v){
			$postvars.=$k."=".$v."&";
		}
		$postvars.="show_response=1";
		$sent = $url." : ".$postvars;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$result = curl_exec($ch);
		curl_close($ch);
		
		return array('sent'=>$sent, 'response'=>$result);
	}
	
	private function testApiMethod($post){
		$info = new stdClass();
		foreach($post as $k=>$v){
			$info->$k = $v;
		}
		
		$data = $this->makeRequest($info);
		$userId = (isset($info->custom))?$info->custom:0;
		if($userId<=0){
			$user = new MM_User();
			$email = (isset($post["ccustemail"]))?$post["ccustemail"]:$post["payer_email"];
			$user->setEmail($email);
			$user->getDataByEmail();
			$userId = $user->getId();
		}
		$user = new MM_User($userId);
		if($user->isValid()){
			$data["response"].= "\n====Validating USER====\n";
			$data["response"].= "Name: ".$user->getFirstName()." ".$user->getLastName()."\n";
			$data["response"].= "Status: ".$user->getStatus()."\n";
		}
		else{
			$data["response"].= "\n====Validating USER====\n<b>User is invalid</b>\n";
		}
		return new MM_Response($data);
		
	}
	
	private function chooseForm($post)
	{
		if(!isset($post["form"])){
			return new MM_Response("No method chosen.", MM_Response::$ERROR);
		}
		$formFile = MM_MODULES."/ipntest.".strtolower($post["form"]).".php";
		if(file_exists($formFile)){
				ob_start();
				require_once($formFile);
				$contents = ob_get_contents();
				ob_end_clean();
				return new MM_Response($contents);
		}
		return new MM_Response("No interface found for this method.", MM_Response::$ERROR);
	}
	
	public static function generateRows($method, $req){
		if(empty($req) || count($req)<=0){
			return "";
		}
		$rows = "";
		
		$keys = MM_Api::getKeyList(true);
		$keyObj = array_pop($keys);
		$apiKey = new MM_Api($keyObj->id);
		$rows .= "
			<tr>
				<td>API Key</td>
				<td><input type='text' name='api_key' id='api_key' value='".$apiKey->getApiKey()."' style='width: 220px;' /></td>
			</tr>";
		$rows .= "
			<tr>
				<td>API Secret</td>
				<td><input type='text' id='api_secret' value='".$apiKey->getApiSecret()."' style='width: 220px;' /></td>
			</tr>";
		
		
		foreach($req as $k=>$v){
			$label = preg_replace("/(\_)/", " ", ucfirst($k));
			$str = "";
			if(is_array($v)){
				$str = call_user_func_array(array('MM_Utils',$v[0]), $v[1]);
			}
			else{
				$str = $v;
			}
			$width = "220";
			$rows .= "
				<tr>
					<td>{$label}</td>
					<td><input type='text' id='{$k}' value='{$str}' style='width: {$width}px;' /></td>
				</tr>";
		}
		$rows.="
			<tr>
				<td colspan='2'><input type='button' name='apitest' value=\"Submit\"  class='button-secondary' onclick=\"mmjs.callApiFunction('".$method."');\" /></td>
			</tr>
		";
		return $rows;
	}
}
?>

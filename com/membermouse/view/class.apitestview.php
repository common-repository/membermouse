<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ApiTestView extends MM_View
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
	
	private function makeRequest($method, $data){
		
		$url = MM_API_URL."?q=/".$method;
		$postvars = "apikey=".$data->api_key."&apisecret=".$data->api_secret."&";
		foreach($data as $k=>$v){
			$postvars.=$k."=".$v."&";
		}
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
		if(!isset($post["api_method"])){
			return new MM_Response("Could not find method to complete call.", MM_Response::$ERROR);
		}
		
		$info = new stdClass();
		foreach($post as $k=>$v){
			$info->$k = $v;
		}
		
		$data = $this->makeRequest($post["api_method"], $info);
		$obj = json_decode($data["response"]);
		if(!isset($obj->response_code)){
			$data["response"] .= "Could not find valid return object.".json_encode($data);
			return new MM_Response($data, MM_Response::$ERROR);
		}
		if($obj->response_code=='200'){
			switch($post["api_method"]){
				case "createMember":
					$data["response"] .= "\n\n".$this->createMemberValidate($info);
					break;
				case "updateMember":
					$data["response"] .= "\n\n".$this->updateMemberValidate($info);
					break;
				case "getMember":
					$data["response"] .= "\n\n".$this->getMemberValidate($info);
					break;
			}
		}
		return new MM_Response($data);
		
	}
	
	private function getMemberValidate($data){
		global $wpdb;
		
		return "";
	}

	private function updateMemberValidate($data){
		global $wpdb;
		
		$user = new MM_User();
		$user->setEmail($data->email);
		$user->getDataByEmail();
		
		$info = "";
		if($user->isValid()){
			$info .= "Local DB: User found.\n";
			$sql = "select * from {$wpdb->users} where id='".$user->getId()."'";
			$row = $wpdb->get_row($sql);
			foreach($row as $key=>$val){
				if(preg_match("/(email)/", $key)){
					continue;
				}
				foreach($data as $k=>$v){
					if(preg_match("/(".$k.")/", $key)){
						$info.= "new database value for {$key} : {$v} \n";
					}
				}
			}
		}
		return $info;
	}
	
	private function createMemberValidate($data){
		$user = new MM_User();
		$user->setEmail($data->email);
		$user->getDataByEmail();
		
		$info = "";
		if($user->isValid()){
			$info .= "Local DB: User found.\n";
		}
		else{
			$info .= "Local DB: User not found [".$data->email. "].\n";
		}
		
		$orderId = $user->getMainOrderId();
		if(intval($orderId)>0){
			$info .= "LimeLight Order Response: ". json_encode(MM_LimeLightService::getOrder($orderId))."\n";
		}
		else{
			$info .= "LimeLight Order Response: No main order associated\n";
		}
		return $info;
	}
	
	private function chooseForm($post)
	{
		if(!isset($post["form"])){
			return new MM_Response("No method chosen.", MM_Response::$ERROR);
		}
		$formFile = MM_MODULES."/apitest.".strtolower($post["form"]).".php";
		if(file_exists($formFile)){
				ob_start();
				require_once($formFile);
				$contents = ob_get_contents();
				ob_end_clean();
				return new MM_Response($contents);
		}
		return new MM_Response("No interface found for this method.", MM_Response::$ERROR);
	}
	
	public static function generateRows($method, $req, $useApiKeys=true){
		if(empty($req) || count($req)<=0){
			return "";
		}
		$rows = "";
		
		if($useApiKeys){
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
		}
		
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

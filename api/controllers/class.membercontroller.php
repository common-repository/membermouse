<?php

class MemberController implements RestController {
	private $REGEX_INTEGER_ONLY = "^[0-9]+$";
	private $REGEX_FLOAT_ONLY = "^[0-9\.]+$";
	private $REGEX_CONTAINS_NUMBERS = "[0-9]+";
	private $REGEX_CONTAINS_SOMETHING = "[\w\W\-\.]+";
	private $REGEX_CONTAINS_ALPHA = "[a-zA-Z]+";
	private $REGEX_BOOLEAN_ONLY = "^(0|1)$";
	private $REGEX_CONTAINS_EMAIL = "(\@)";
	private $REGEX_ALPHANUMERIC_ONLY = "^[a-zA-Z0-9\_\-\.\s]+$";
    function execute(RestServer $rest) {
//       	return new GenericView("place_order.php");
    }
    
    public function createMember($rest){
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/createMember");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
		$req = new stdClass();
		$req->first_name = $this->REGEX_CONTAINS_ALPHA;
		$req->last_name = $this->REGEX_CONTAINS_ALPHA;
		$req->email = $this->REGEX_CONTAINS_ALPHA; 
		$req->member_type_id = $this->REGEX_INTEGER_ONLY;
		$req->phone = $this->REGEX_CONTAINS_NUMBERS;
  
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	if(!isset($post[$key]) || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key])))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else
        		$data->$key = stripslashes($post[$key]);
        }
        
        foreach($post as $k=>$v){
        	$data->$k = $v;
        }
        
		$result = MM_APIService::createMember($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest, $result->message, $result->message);
    }
    
    public function getMember($rest){
    
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/getMember");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
		$req = new stdClass();
		$req->user_type = $this->REGEX_CONTAINS_ALPHA;
		$req->member_id = $this->REGEX_INTEGER_ONLY;
  
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	if(!isset($post[$key]) || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key])))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else
        		$data->$key = stripslashes($post[$key]);
        }
        
        if(!preg_match("/(membermouse|limelight)/", $data->user_type)){
 			return new Response($rest,  null,"user_type is not valid",RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);       	
        }
        
        foreach($post as $k=>$v){
        	$data->$k = $v;
        }
        
		$result = MM_APIService::getMember($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest, $result->message, $result->message);
    }
    
    public function updateMember($rest){
    	
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/updateMember");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
		$req = new stdClass();
		$req->email = $this->REGEX_CONTAINS_ALPHA;
  
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	if(!isset($post[$key]) || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key])))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else
        		$data->$key = stripslashes($post[$key]);
        }
        
        foreach($post as $k=>$v){
        	$data->$k = $v;
        }
        
		$result = MM_APIService::updateMember($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest, $result->message, $result->message);
    }
}
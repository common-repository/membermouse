<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class OrderController implements RestController {
	private $REGEX_INTEGER_ONLY = "^[0-9]+$";
	private $REGEX_FLOAT_ONLY = "^[0-9\.]+$";
	private $REGEX_CONTAINS_NUMBERS = "[0-9]+";
	private $REGEX_CONTAINS_SOMETHING = "[\w\W\-\.]+";
	private $REGEX_CONTAINS_ALPHA = "[a-zA-Z]+";
	private $REGEX_BOOLEAN_ONLY = "^(0|1|No|Yes)$";
	private $REGEX_CONTAINS_EMAIL = "(\@)";
	private $REGEX_ALPHANUMERIC_ONLY = "^[a-zA-Z0-9\_\-\.\s]+$";
    function execute(RestServer $rest) {
       	return new GenericView("place_order.php") ;
    }
    
    public function deleteOrder($rest){
        $post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/deleteOrder");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
       	$req = new stdClass();
		$req->order_id = $this->REGEX_INTEGER_ONLY;
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->product_ids = $this->REGEX_CONTAINS_NUMBERS;
    
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
        
		$result = MM_APIService::deleteOrder($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_NOCHANGE, RESPONSE_ERROR_MESSAGE_NOCHANGE);
		}
	    return new Response($rest);
    }
    
    public function preAuthorizeOrder($rest){
        $post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/preAuthorizeOrder");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
       	$req = new stdClass();
		$req->product_ids = $this->REGEX_CONTAINS_NUMBERS;
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->email = $this->REGEX_CONTAINS_EMAIL;
    
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	/*
        	 * || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key]))
        	 */
        	if(!isset($post[$key]) )
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else
        		$data->$key = stripslashes($post[$key]);
        }
		$result = MM_APIService::preAuthorizeOrder($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest);
    }
    
    public function updateOrder($rest)
    {
        $post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/updateOrder");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
       	$req = new stdClass();
		$req->main_order_id = $this->REGEX_INTEGER_ONLY;
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->order_status = $this->REGEX_CONTAINS_NUMBERS;
		$req->is_recurring = $this->REGEX_BOOLEAN_ONLY;
		$req->new_order_id = $this->REGEX_INTEGER_ONLY;
		$req->first_name = $this->REGEX_CONTAINS_ALPHA;
		$req->last_name = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_address = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_city = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_state = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_zip = $this->REGEX_CONTAINS_NUMBERS;
		$req->shipping_country = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_address = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_city = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_state = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_zip = $this->REGEX_CONTAINS_NUMBERS;
		$req->billing_country = $this->REGEX_CONTAINS_ALPHA;
		$req->phone = $this->REGEX_CONTAINS_NUMBERS;

		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	if(!isset($post[$key]))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else{
        		if($regex == $this->REGEX_BOOLEAN_ONLY){
        			$post[$key] = ($post[$key]=="No" || $post[$key] == "0")?0:1;
        		}
        		$data->$key = stripslashes($post[$key]);
        	}
        }
        
		$result = MM_APIService::updateOrder($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest);
    }
    
    public function updateCampaign($rest)
    {
        $post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/updateCampaign");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
		$req = new stdClass();
		$req->action_type = "^(add|edit|delete)$";
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->campaign_name = $this->REGEX_CONTAINS_ALPHA;
		$req->campaign_description = $this->REGEX_CONTAINS_ALPHA;
		$req->product_ids = $this->REGEX_CONTAINS_NUMBERS;
		$req->countries = $this->REGEX_CONTAINS_ALPHA;
		$req->payment_methods = $this->REGEX_CONTAINS_ALPHA;
		
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
        
        if(!isset($post["shipping_methods"]))
        {
        	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : shipping_methods",RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        }
		$post["shipping_methods"] = stripslashes($post["shipping_methods"]);
        $shipping = json_decode($post["shipping_methods"]);
        
        if(is_null($shipping))
        {
        	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : shipping_methods",RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        }
		$data->shipping_methods = $shipping;
		$result = MM_APIService::updateCampaign($data);
        
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest);
    }
    
    public function updateProduct($rest)
    {
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/updateProduct");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
    	$req = new stdClass();
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->product_id = $this->REGEX_INTEGER_ONLY;
		$req->product_name = $this->REGEX_CONTAINS_ALPHA;
		$req->product_sku = $this->REGEX_CONTAINS_SOMETHING;
		$req->product_price = $this->REGEX_FLOAT_ONLY;
		$req->product_description = $this->REGEX_CONTAINS_ALPHA;
		$req->product_category = $this->REGEX_CONTAINS_ALPHA;
		$req->is_free_trial = $this->REGEX_BOOLEAN_ONLY;
		$req->is_shippable = $this->REGEX_BOOLEAN_ONLY;
		$req->rebill_product_id = $this->REGEX_INTEGER_ONLY;
		$req->rebill_period = $this->REGEX_INTEGER_ONLY;
    
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	if(!isset($post[$key]) || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key])))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else{
        		if($regex == $this->REGEX_BOOLEAN_ONLY){
        			$post[$key] = ($post[$key]=="No" || $post[$key] == "0")?0:1;
        		}
        		$data->$key = stripslashes($post[$key]);
        	}
        }
        
		$result = MM_APIService::updateProduct($data);
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest);
    }
    
    public function newMember($rest)
    {
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/newMember");
        
        if(!Utils::isAuthenticated($post,$rest))
        {
	    	return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_AUTH,RESPONSE_ERROR_CODE_AUTH, RESPONSE_ERROR_MESSAGE_AUTH);
        }
        
		$req = new stdClass();
		
		$req->customer_id = $this->REGEX_INTEGER_ONLY;
		$req->campaign_id = $this->REGEX_INTEGER_ONLY;
		$req->first_name = $this->REGEX_CONTAINS_ALPHA;
		$req->last_name = $this->REGEX_CONTAINS_ALPHA;
		$req->email = $this->REGEX_CONTAINS_ALPHA; // TODO MATT ENFORCE EMAIL?
		$req->phone = $this->REGEX_CONTAINS_NUMBERS;
		$req->shipping_address = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_city = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_state = $this->REGEX_CONTAINS_ALPHA;
		$req->shipping_zip = $this->REGEX_CONTAINS_NUMBERS;
		$req->shipping_country = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_address = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_city = $this->REGEX_CONTAINS_ALPHA;
		$req->billing_state =$this->REGEX_CONTAINS_ALPHA;
		$req->billing_zip = $this->REGEX_CONTAINS_NUMBERS;
		$req->billing_country = $this->REGEX_CONTAINS_ALPHA;
		$req->order_id = $this->REGEX_INTEGER_ONLY;
		$req->order_total = $this->REGEX_FLOAT_ONLY;
		$req->product_ids = $this->REGEX_CONTAINS_NUMBERS;
  
		$data = new stdClass();
		foreach($req as $key=>$regex)
        {
        	/*
        	 *  || (isset($post[$key]) && !preg_match("/".$regex."/", $post[$key]))
        	 */
        	if(!isset($post[$key]))
        	{
	    		return new Response($rest,  null,RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : ".$key,RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        	else
        		$data->$key = stripslashes($post[$key]);
        }
        
		$result = MM_APIService::newMember($data);
		
        Utils::logRequest(json_encode($result), "/newMember");
		if($result->type == MM_Response::$ERROR)
		{
	    	return new Response($rest, null,$result->message, RESPONSE_ERROR_CODE_CONFLICT, RESPONSE_ERROR_MESSAGE_CONFLICT);
		}
	    return new Response($rest, $result->message, $result->message);
    }
}

?>

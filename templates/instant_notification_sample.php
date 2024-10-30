<?php 
$params = $_GET;

if(!isset($params["event_type_id"])) {
	// Event type ID is required
	exit;
}

$eventTypeId = $params["event_type_id"];

switch($eventTypeId) {
	// CREATE NEW MEMBER EVENT
	case 1:
		$username = $params["username"];
		$email = $params["email"];
		$memberTypeId = $params["member_type_id"];
		$password = $params["password"];
		$status = $params["status"];
		
		$billingAddress = $params["billing_address"];
		$bilingCity = $params["billing_city"];
		$billingState = $params["billing_state"];
		$billingZipCode = $params["billing_zipcode"];
		$billingCountry = $params["billing_country"];
		
		$shippingAddress = $params["shipping_address"];
		$bilingCity = $params["shipping_city"];
		$shippingState = $params["shipping_state"];
		$shippingZipCode = $params["shipping_zipcode"];
		$shippingCountry = $params["shipping_country"];
		
		// perform appropriate action
		
		break;
		
	// UPDATE MEMBER EVENT
	case 2:
		$memberId = $params["member_id"];
	
		$username = $params["username"];
		$email = $params["email"];
		$memberTypeId = $params["member_type_id"];
		$password = $params["password"];
		$status = $params["status"];
		
		$billingAddress = $params["billing_address"];
		$bilingCity = $params["billing_city"];
		$billingState = $params["billing_state"];
		$billingZipCode = $params["billing_zipcode"];
		$billingCountry = $params["billing_country"];
		
		$shippingAddress = $params["shipping_address"];
		$bilingCity = $params["shipping_city"];
		$shippingState = $params["shipping_state"];
		$shippingZipCode = $params["shipping_zipcode"];
		$shippingCountry = $params["shipping_country"];
		
		// perform appropriate action
		
		break;
	
	default:
		// unsupported event type
		
		break;
}
?>
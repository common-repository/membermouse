<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_LimeLightUtils
{
	public static $COUNTRY_ID_US = "US";
	
	public static function getLLCustomerUrl($customerId)
	{
		global $mmSite;
						
		$url = $mmSite->getLLUrl();
		$url .= "admin/customers.php?search_req=detail&file_path=&show_by_id=".$customerId;
		$url .= "&act=show_details&id_".$customerId."=".$customerId."&page=0";
		
		return $url;
	}
	
	public static function getLLOrderUrl($orderId)
	{
		global $mmSite;
						
		$url = $mmSite->getLLUrl();
		$url .= "admin/orders.php?show_details=show_details&show_folder=view_all&fromPost=1&show_by_id=".$orderId;
		$url .= "act=&id_".$orderId."&page=0";
		
		return $url;
	}
	
	public static function getLLPlaceOrderUrl()
	{
		global $mmSite;
						
		$url = $mmSite->getLLUrl();
		$url .= "admin/placeorder.php";
		
		return $url;
	}
	
	public static function sendErrorNoticeEmail($orderId, $errorMsgs, $warningMsgs) 
	{
		if($errorMsgs != "") {
			$errorMsgs = "The following errors were detected when processing order ID ".$orderId.":".MM_Email::$BR.MM_Email::$BR.$errorMsgs;
		}
		
		if($warningMsgs != "") {
			$warningMsgs = "The following warnings were detected when processing order ID ".$orderId.":".MM_Email::$BR.MM_Email::$BR.$warningMsgs;
		}
		
		if($errorMsgs != "" || $warningMsgs != "")
		{
			$emailAcct = new MM_EmailAccount();
			$emailAcct->getDefault();
			
			$email = new MM_Email();
			
			$email->setSubject("Issues processing order ID ".$orderId);
			$email->setBody($errorMsgs.MM_Email::$BR.MM_Email::$BR.MM_Email::$BR.$warningMsgs);
			$email->setToName($emailAcct->getName());
			$email->setToAddress($emailAcct->getAddress());
			$email->setFromName(MM_NOTICE_EMAIL_NAME);
			$email->setFromAddress(MM_NOTICE_EMAIL_ADDRESS);
			
			$result = $email->send();
		}
	}
	
	public static function getErrorMessage($data) 
	{	
		if(!isset($data["responseCode"]) && !isset($data["response_code"]))
		{
			return new MM_Response("No response code found", MM_Response::$ERROR);
		}
		
		if(isset($data["responseCode"])) 
		{
			$responseCode = intval($data["responseCode"]);
			
			// success
			if($responseCode == 100) {
				return new MM_Response();
			}
		
			if($data["errorFound"] == "1" && $responseCode != 800) {
				return new MM_Response($data["errorMessage"], MM_Response::$ERROR);
			}
			else {
				return new MM_Response($data["declineReason"], MM_Response::$ERROR);
			}
		}
		else 
		{
			$responseCode = intval($data["response_code"]);
			
			switch($responseCode) 
			{
				case "100":
				case "343":
				case "353":
				case "355":
				case "360":
				case "380":
					return new MM_Response();

				case "200": 
					return new MM_Response("return Invalid login credentials", MM_Response::$ERROR);

				case "320":
					return new MM_Response("Invalid Product Id", MM_Response::$ERROR);

				case "321": 
					return new MM_Response("Existing Product Category Id Not Found", MM_Response::$ERROR);

				case "322": 
					return new MM_Response("Invalid Category Id", MM_Response::$ERROR);

				case "323": 
					return new MM_Response("Digital Delivery and Digital URL must be paired together and digital URL must be a valid URL", MM_Response::$ERROR);

				case "324": 
					return new MM_Response("If rebill_product is a valid product, you must pass rebill_days greater than 0 days", MM_Response::$ERROR);

				case "325":
					return new MM_Response("Length Does Not Meet Minimum", MM_Response::$ERROR);

				case "326":
					return new MM_Response("URL is invalid", MM_Response::$ERROR);
					
				case "329":
					return new MM_Response("Credit card is invalid", MM_Response::$ERROR);

				case "330": 
					return new MM_Response("No Status Passed", MM_Response::$ERROR);

				case "331": 
					 return new MM_Response("Invalid Criteria", MM_Response::$ERROR);

				case "332": 
					 return new MM_Response("Start and end date are required", MM_Response::$ERROR);

				case "333": 
					 return new MM_Response("No Orders Found", MM_Response::$ERROR);

				case "334": 
					 return new MM_Response("Invalid Start Date format", MM_Response::$ERROR);

				case "335": 
					 return new MM_Response("Invalid End Date format", MM_Response::$ERROR);

				case "336": 
					 return new MM_Response("Wild Card Unsupported for this search criteria", MM_Response::$ERROR);

				case "337": 
					 return new MM_Response("Last 4 or First 4 must be 4 characters exactly", MM_Response::$ERROR);

				case "338": 
					 return new MM_Response("Timestamp invalid", MM_Response::$ERROR);

				case "339": 
					 return new MM_Response("Total Amount must be numeric and non-negative", MM_Response::$ERROR);

				case "340": 
					 return new MM_Response("Invalid country code", MM_Response::$ERROR);

				case "341": 
					 return new MM_Response("Invalid state code", MM_Response::$ERROR);

				case "342": 
					 return new MM_Response("Invalid Email Address", MM_Response::$ERROR);

				case "344": 
					 return new MM_Response("Invalid Number Format", MM_Response::$ERROR);

				case "345": 
					 return new MM_Response("Must be a 1 or 0.  \"1\" being \"On\" or true. \"0\" being \"Off\" or false.", MM_Response::$ERROR);

				case "350": 
					 return new MM_Response("Invalid order Id supplied", MM_Response::$ERROR);

				case "351": 
					 return new MM_Response("Invalid status or action supplied", MM_Response::$ERROR);

				case "352": 
					 return new MM_Response("Uneven Order/Status/Action Pairing", MM_Response::$ERROR);

				case "353": 
					 return new MM_Response("Cannot stop recurring", MM_Response::$ERROR);

				case "354": 
					 return new MM_Response("Cannot reset recurring", MM_Response::$ERROR);

				case "355": 
					 return new MM_Response("Cannot start recurring", MM_Response::$ERROR);

				case "360": 
					 return new MM_Response("Cannot stop upsell recurring", MM_Response::$ERROR);

				case "370": 
					 return new MM_Response("Invalid amount supplied", MM_Response::$ERROR);

				case "371": 
					 return new MM_Response("Invalid keep recurring flag supplied", MM_Response::$ERROR);

				case "380": 
					 return new MM_Response("Order is not recurring", MM_Response::$ERROR);

				case "390": 
					 return new MM_Response("Invalid number of days supplied", MM_Response::$ERROR);

				case "400": 
					 return new MM_Response("Invalid campaign Id supplied", MM_Response::$ERROR);

				case "500": 
					 return new MM_Response("Invalid customer Id supplied", MM_Response::$ERROR);

				case "600": 
					 return new MM_Response("Invalid product Id supplied", MM_Response::$ERROR);

				case "700": 
					 return new MM_Response("Invalid method supplied", MM_Response::$ERROR);

				case "800": 
					 return new MM_Response("Transaction was declined", MM_Response::$ERROR);

				case "1000": 
					 return new MM_Response("SSL is required", MM_Response::$ERROR);
			}
			
			// success
			if($responseCode == 100) {
				return new MM_Response();
			}
		
			if($data["errorFound"] == "1" && $responseCode != 800) {
				return new MM_Response($data["errorMessage"], MM_Response::$ERROR);
			}
			else {
				return new MM_Response($data["declineReason"], MM_Response::$ERROR);
			}
		}
	}
	
	public static function getCountryOptions(){
		$countries = array();
		
			$countries["AX"]
				= "Aaland Islands";
				
			$countries["AF"]
				= "Afghanistan";
				
			$countries["AL"]
				= "Albania";
				
			$countries["DZ"]
				= "Algeria";
				
			$countries["AS"]
				= "American Samoa";
				
			$countries["AD"]
				= "Andorra";
				
			$countries["AO"]
				= "Angola";
				
			$countries["AI"]
				= "Anguilla";
				
			$countries["AQ"]
				= "Antarctica";
				
			$countries["AG"]
				= "Antigua and Barbuda";
				
			$countries["AR"]
				= "Argentina";
				
			$countries["AM"]
				= "Armenia";
				
			$countries["AW"]
				= "Aruba";
				
			$countries["AU"]
				= "Australia";
				
			$countries["AT"]
				= "Austria";
				
			$countries["AZ"]
				= "Azerbaijan";
			
			$countries["BS"]
				= "Bahamas";
				
			$countries["BH"]
				= "Bahrain";
				
			$countries["BD"]
				= "Bangladesh";
				
			$countries["BB"]
				= "Barbados";
				
			$countries["BY"]
				= "Belarus";
				
			$countries["BE"]
				= "Belgium";
				
			$countries["BZ"]
				= "Belize";
				
			$countries["BJ"]
				= "Benin";
				
			$countries["BM"]
				= "Bermuda";
				
			$countries["BT"]
				= "Bhutan";
				
			$countries["BO"]
				= "Bolivia";
				
			$countries["BA"]
				= "Bosnia and Herzegowina";
				
			$countries["BW"]
				= "Botswana";
				
			$countries["BV"]
				= "Bouvet Island";
				
			$countries["BR"]
				= "Brazil";
				
			$countries["IO"]
				= "British Indian Ocean Territory";
				
			$countries["BN"]
				= "Brunei Darussalam";
				
			$countries["BG"]
				= "Bulgaria";
				
			$countries["BF"]
				= "Burkina Faso";
				
			$countries["BI"]
				= "Burundi";
				
			$countries["KH"]
				= "Cambodia";
				
			$countries["CM"]
				= "Cameroon";
				
			$countries["CA"]
				= "Canada";
				
			$countries["CV"]
				= "Cape Verde";
				
			$countries["KY"]
				= "Cayman Islands";
				
			$countries["CF"]
				= "Central African Republic";
				
			$countries["TD"]
				= "Chad";
				
			$countries["CL"]
				= "Chile";
				
			$countries["CN"]
				= "China";
				
			$countries["CX"]
				= "Christmas Island";
				
			$countries["CC"]
				= "Cocos (Keeling) Islands";
				
			$countries["CO"]
				= "Colombia";
				
			$countries["KM"]
				= "Comoros";
				
			$countries["CG"]
				= "Congo";
				
			$countries["CD"]
				= "Congo, The Democratic Republic of the";
				
			$countries["CK"]
				= "Cook Islands";
				
			$countries["CR"]
				= "Costa Rica";
				
			$countries["CI"]
				= "Cote D'Ivoire";
				
			$countries["HR"]
				= "Croatia";
				
			$countries["CU"]
				= "Cuba";
				
			$countries["CY"]
				= "Cyprus";
				
			$countries["CZ"]
				= "Czech Republic";
				
			$countries["DK"]
				= "Denmark";
				
			$countries["DJ"]
				= "Djibouti";
				
			$countries["DM"]
				= "Dominica";
				
			$countries["DO"]
				= "Dominican Republic";
				
			$countries["EC"]
				= "Ecuador";
				
			$countries["EG"]
				= "Egypt";
				
			$countries["SV"]
				= "El Salvador";
				
			$countries["GQ"]
				= "Equatorial Guinea";
				
			$countries["ER"]
				= "Eritrea";
				
			$countries["EE"]
				= "Estonia";
				
			$countries["ET"]
				= "Ethiopia";

			$countries["FK"]
				= "Falkland Islands (Malvinas)";
				
			$countries["FO"]
				= "Faroe Islands";
				
			$countries["FJ"]
				= "Fiji";
				
			$countries["FI"]
				= "Finland";
				
			$countries["FR"]
				= "France";
				
			$countries["FX"]
				= "France, Metropolitan";
				
			$countries["GF"]
				= "French Guiana";
				
			$countries["PF"]
				= "French Polynesia";
				
			$countries["TF"]
				= "French Southern Territories";
				
			$countries["GA"]
				= "Gabon";
				
			$countries["GM"]
				= "Gambia";
				
			$countries["GE"]
				= "Georgia";
				
			$countries["DE"]
				= "Germany";
				
			$countries["GH"]
				= "Ghana";
				
			$countries["GI"]
				= "Gibraltar";
				
			$countries["GR"]
				= "Greece";
				
			$countries["GL"]
				= "Greenland";
				
			$countries["GD"]
				= "Grenada";
				
			$countries["GP"]
				= "Guadeloupe";
				
			$countries["GU"]
				= "Guam";
				
			$countries["GT"]
				= "Guatemala";
				
			$countries["GG"]
				= "Guernsey";
				
			$countries["GN"]
				= "Guinea";
				
			$countries["GW"]
				= "Guinea-bissau";
				
			$countries["GY"]
				= "Guyana";
				
			$countries["HT"]
				= "Haiti";
				
			$countries["HM"]
				= "Heard and McDonald Islands";
				
			$countries["VA"]
				= "Holy See (Vatican City State)";
				
			$countries["HN"]
				= "Honduras";
				
			$countries["HK"]
				= "Hong Kong";
				
			$countries["HU"]
				= "Hungary";
				
			$countries["IS"]
				= "Iceland";
				
			$countries["IN"]
				= "India";
				
			$countries["ID"]
				= "Indonesia";
				
			$countries["IR"]
				= "Iran, Islamic Republic of";
				
			$countries["IQ"]
				= "Iraq";
				
			$countries["IE"]
				= "Ireland";
				
			$countries["IM"]
				= "Isle of Man";
				
			$countries["IL"]
				= "Israel";
				
			$countries["IT"]
				= "Italy";

			$countries["JM"]
				= "Jamaica";
				
			$countries["JP"]
				= "Japan";
				
			$countries["JE"]
				= "Jersey";
				
			$countries["JO"]
				= "Jordan";
				
			$countries["KZ"]
				= "Kazakhstan";
				
			$countries["KE"]
				= "Kenya";
				
			$countries["KI"]
				= "Kiribati";
				
			$countries["KP"]
				= "Korea, Democratic People's Republic of";
				
			$countries["KR"]
				= "Korea, Republic of";
				
			$countries["KW"]
				= "Kuwait";
				
			$countries["KG"]
				= "Kyrgyzstan";

			$countries["LA"]
				= "Lao People's Democratic Republic";
				
			$countries["LV"]
				= "Latvia";
				
			$countries["LB"]
				= "Lebanon";
				
			$countries["LS"]
				= "Lesotho";
				
			$countries["LR"]
				= "Liberia";
				
			$countries["LY"]
				= "Libyan Arab Jamahiriya";
				
			$countries["LI"]
				= "Liechtenstein";
				
			$countries["LT"]
				= "Lithuania";
				
			$countries["LU"]
				= "Luxembourg";
				
			$countries["MO"]
				= "Macau";
				
			$countries["MK"]
				= "Macedonia, The Former Yugoslav Republic of";
				
			$countries["MG"]
				= "Madagascar";
				
			$countries["MW"]
				= "Malawi";
				
			$countries["MY"]
				= "Malaysia";
				
			$countries["MV"]
				= "Maldives";
				
			$countries["ML"]
				= "Mali";
				
			$countries["MT"]
				= "Malta";
				
			$countries["MH"]
				= "Marshall Islands";
				
			$countries["MQ"]
				= "Martinique";
				
			$countries["MR"]
				= "Mauritania";
				
			$countries["MU"]
				= "Mauritius";
				
			$countries["YT"]
				= "Mayotte";
				
			$countries["MX"]
				= "Mexico";
				
			$countries["FM"]
				= "Micronesia, Federated States of";
				
			$countries["MD"]
				= "Moldova, Republic of";
				
			$countries["MC"]
				= "Monaco";
				
			$countries["MN"]
				= "Mongolia";
				
			$countries["ME"]
				= "Montenegro";
				
			$countries["MS"]
				= "Montserrat";
				
			$countries["MA"]
				= "Morocco";
				
			$countries["MZ"]
				= "Mozambique";
				
			$countries["MM"]
				= "Myanmar";
	
			$countries["NA"]
				= "Namibia";
				
			$countries["NR"]
				= "Nauru";
				
			$countries["NP"]
				= "Nepal";
				
			$countries["NL"]
				= "Netherlands";
				
			$countries["AN"]
				= "Netherlands Antilles";
				
			$countries["NC"]
				= "New Caledonia";
				
			$countries["NZ"]
				= "New Zealand";
				
			$countries["NI"]
				= "Nicaragua";
				
			$countries["NE"]
				= "Niger";
				
			$countries["NG"]
				= "Nigeria";
				
			$countries["NU"]
				= "Niue";
				
			$countries["NF"]
				= "Norfolk Island";
				
			$countries["244"]
				= "Northern Ireland";
				
			$countries["MP"]
				= "Northern Mariana Islands";
				
			$countries["NO"]
				= "Norway";
				
			$countries["OM"]
				= "Oman";
				
			$countries["PK"]
				= "Pakistan";
				
			$countries["PW"]
				= "Palau";
				
			$countries["PS"]
				= "Palestinian Territory, Occupied";
				
			$countries["PA"]
				= "Panama";
				
			$countries["PG"]
				= "Papua New Guinea";
				
			$countries["PY"]
				= "Paraguay";
				
			$countries["PE"]
				= "Peru";
				
			$countries["PH"]
				= "Philippines";
				
			$countries["PN"]
				= "Pitcairn";
				
			$countries["PL"]
				= "Poland";
				
			$countries["PT"]
				= "Portugal";
				
			$countries["PR"]
				= "Puerto Rico";
				
			$countries["QA"]
				= "Qatar";

			$countries["RE"]
				= "Reunion";
				
			$countries["RO"]
				= "Romania";
				
			$countries["RU"]
				= "Russian Federation";
				
			$countries["RW"]
				= "Rwanda";
				
			$countries["BL"]
				= "Saint Barthelemy";
				
			$countries["SH"]
				= "Saint Helena, Ascension and Tristan da Cunhda";
				
			$countries["KN"]
				= "Saint Kitts and Nevis";
				
			$countries["LC"]
				= "Saint Lucia";
				
			$countries["MF"]
				= "Saint Martin";
				
			$countries["PM"]
				= "Saint Pierre and Miquelon";
				
			$countries["VC"]
				= "Saint Vincent and the Grenadines";
				
			$countries["WS"]
				= "Samoa";
				
			$countries["SM"]
				= "San Marino";
				
			$countries["ST"]
				= "Sao Tome and Principe";
				
			$countries["SA"]
				= "Saudi Arabia";
				
			$countries["SN"]
				= "Senegal";
				
			$countries["RS"]
				= "Serbia";
				
			$countries["SC"]
				= "Seychelles";
				
			$countries["SL"]
				= "Sierra Leone";
				
			$countries["SG"]
				= "Singapore";
				
			$countries["SK"]
				= "Slovakia";

			$countries["SI"]
				= "Slovenia";
				
			$countries["SB"]
				= "Solomon Islands";
				
			$countries["SO"]
				= "Somalia";
				
			$countries["ZA"]
				= "South Africa";
				
			$countries["GS"]
				= "South Georgia and the South Sandwich Islands";
				
			$countries["ES"]
				= "Spain";
				
			$countries["LK"]
				= "Sri Lanka";
				
			$countries["SD"]
				= "Sudan";
				
			$countries["SR"]
				= "Suriname";
				
			$countries["SJ"]
				= "Svalbard and Jan Mayen Islands";
				
			$countries["SZ"]
				= "Swaziland";
				
			$countries["SE"]
				= "Sweden";
				
			$countries["CH"]
				= "Switzerland";
				
			$countries["SY"]
				= "Syrian Arab Republic";

			$countries["TW"]
				= "Taiwan, Province of China";
				
			$countries["TJ"]
				= "Tajikistan";
				
			$countries["TZ"]
				= "Tanzania, United Republic of";
				
			$countries["TH"]
				= "Thailand";
				
			$countries["TP"]
				= "East Timor";
				
			$countries["TL"]
				= "Timor-Leste";
				
			$countries["TG"]
				= "Togo";
				
			$countries["TK"]
				= "Tokelau";
				
			$countries["TO"]
				= "Tonga";
				
			$countries["TT"]
				= "Trinidad and Tobago";
				
			$countries["TN"]
				= "Tunisia";
				
			$countries["TR"]
				= "Turkey";
				
			$countries["TM"]
				= "Turkmenistan";
				
			$countries["TC"]
				= "Turks and Caicos Islands";
				
			$countries["TV"]
				= "Tuvalu";
		
			$countries["UG"]
				= "Uganda";
				
			$countries["UA"]
				= "Ukraine";
				
			$countries["AE"]
				= "United Arab Emirates";
				
			$countries["GB"]
				= "United Kingdom";
				
			$countries["US"]
				= "United States";
				
			$countries["UM"]
				= "United States Minor Outlying Islands";
				
			$countries["UY"]
				= "Uruguay";
				
			$countries["UZ"]
				= "Uzbekistan";

			$countries["VU"]
				= "Vanuatu";
				
			$countries["VE"]
				= "Venezuela, Bolivarian Republic of";
				
			$countries["VN"]
				= "Viet Nam";
				
			$countries["VG"]
				= "Virgin Islands (British)";
				
			$countries["VI"]
				= "Virgin Islands (U.S.)";
				
			$countries["WF"]
				= "Wallis and Futuna Islands";
				
			$countries["EH"]
				= "Western Sahara";
				
			$countries["YE"]
				= "Yemen";
				
			$countries["YU"]
				= "Yugoslavia";
				
			$countries["ZR"]
				= "Zaire";
				
			$countries["ZM"]
				= "Zambia";
				
			$countries["ZW"]
				= "Zimbabwe";
				
			return $countries;
	}
	
	public static function getCountryName($iso)
	{
		switch($iso) 		
		{
			case "AX":
				return "Aaland Islands";
				
			case "AF":
				return "Afghanistan";
				
			case "AL":
				return "Albania";
				
			case "DZ":
				return "Algeria";
				
			case "AS":
				return "American Samoa";
				
			case "AD":
				return "Andorra";
				
			case "AO":
				return "Angola";
				
			case "AI":
				return "Anguilla";
				
			case "AQ":
				return "Antarctica";
				
			case "AG":
				return "Antigua and Barbuda";
				
			case "AR":
				return "Argentina";
				
			case "AM":
				return "Armenia";
				
			case "AW":
				return "Aruba";
				
			case "AU":
				return "Australia";
				
			case "AT":
				return "Austria";
				
			case "AZ":
				return "Azerbaijan";
			
			case "BS":
				return "Bahamas";
				
			case "BH":
				return "Bahrain";
				
			case "BD":
				return "Bangladesh";
				
			case "BB":
				return "Barbados";
				
			case "BY":
				return "Belarus";
				
			case "BE":
				return "Belgium";
				
			case "BZ":
				return "Belize";
				
			case "BJ":
				return "Benin";
				
			case "BM":
				return "Bermuda";
				
			case "BT":
				return "Bhutan";
				
			case "BO":
				return "Bolivia";
				
			case "BA":
				return "Bosnia and Herzegowina";
				
			case "BW":
				return "Botswana";
				
			case "BV":
				return "Bouvet Island";
				
			case "BR":
				return "Brazil";
				
			case "IO":
				return "British Indian Ocean Territory";
				
			case "BN":
				return "Brunei Darussalam";
				
			case "BG":
				return "Bulgaria";
				
			case "BF":
				return "Burkina Faso";
				
			case "BI":
				return "Burundi";
				
			case "KH":
				return "Cambodia";
				
			case "CM":
				return "Cameroon";
				
			case "CA":
				return "Canada";
				
			case "CV":
				return "Cape Verde";
				
			case "KY":
				return "Cayman Islands";
				
			case "CF":
				return "Central African Republic";
				
			case "TD":
				return "Chad";
				
			case "CL":
				return "Chile";
				
			case "CN":
				return "China";
				
			case "CX":
				return "Christmas Island";
				
			case "CC":
				return "Cocos (Keeling) Islands";
				
			case "CO":
				return "Colombia";
				
			case "KM":
				return "Comoros";
				
			case "CG":
				return "Congo";
				
			case "CD":
				return "Congo, The Democratic Republic of the";
				
			case "CK":
				return "Cook Islands";
				
			case "CR":
				return "Costa Rica";
				
			case "CI":
				return "Cote D'Ivoire";
				
			case "HR":
				return "Croatia";
				
			case "CU":
				return "Cuba";
				
			case "CY":
				return "Cyprus";
				
			case "CZ":
				return "Czech Republic";
				
			case "DK":
				return "Denmark";
				
			case "DJ":
				return "Djibouti";
				
			case "DM":
				return "Dominica";
				
			case "DO":
				return "Dominican Republic";
				
			case "EC":
				return "Ecuador";
				
			case "EG":
				return "Egypt";
				
			case "SV":
				return "El Salvador";
				
			case "GQ":
				return "Equatorial Guinea";
				
			case "ER":
				return "Eritrea";
				
			case "EE":
				return "Estonia";
				
			case "ET":
				return "Ethiopia";

			case "FK":
				return "Falkland Islands (Malvinas)";
				
			case "FO":
				return "Faroe Islands";
				
			case "FJ":
				return "Fiji";
				
			case "FI":
				return "Finland";
				
			case "FR":
				return "France";
				
			case "FX":
				return "France, Metropolitan";
				
			case "GF":
				return "French Guiana";
				
			case "PF":
				return "French Polynesia";
				
			case "TF":
				return "French Southern Territories";
				
			case "GA":
				return "Gabon";
				
			case "GM":
				return "Gambia";
				
			case "GE":
				return "Georgia";
				
			case "DE":
				return "Germany";
				
			case "GH":
				return "Ghana";
				
			case "GI":
				return "Gibraltar";
				
			case "GR":
				return "Greece";
				
			case "GL":
				return "Greenland";
				
			case "GD":
				return "Grenada";
				
			case "GP":
				return "Guadeloupe";
				
			case "GU":
				return "Guam";
				
			case "GT":
				return "Guatemala";
				
			case "GG":
				return "Guernsey";
				
			case "GN":
				return "Guinea";
				
			case "GW":
				return "Guinea-bissau";
				
			case "GY":
				return "Guyana";
				
			case "HT":
				return "Haiti";
				
			case "HM":
				return "Heard and McDonald Islands";
				
			case "VA":
				return "Holy See (Vatican City State)";
				
			case "HN":
				return "Honduras";
				
			case "HK":
				return "Hong Kong";
				
			case "HU":
				return "Hungary";
				
			case "IS":
				return "Iceland";
				
			case "IN":
				return "India";
				
			case "ID":
				return "Indonesia";
				
			case "IR":
				return "Iran, Islamic Republic of";
				
			case "IQ":
				return "Iraq";
				
			case "IE":
				return "Ireland";
				
			case "IM":
				return "Isle of Man";
				
			case "IL":
				return "Israel";
				
			case "IT":
				return "Italy";

			case "JM":
				return "Jamaica";
				
			case "JP":
				return "Japan";
				
			case "JE":
				return "Jersey";
				
			case "JO":
				return "Jordan";
				
			case "KZ":
				return "Kazakhstan";
				
			case "KE":
				return "Kenya";
				
			case "KI":
				return "Kiribati";
				
			case "KP":
				return "Korea, Democratic People's Republic of";
				
			case "KR":
				return "Korea, Republic of";
				
			case "KW":
				return "Kuwait";
				
			case "KG":
				return "Kyrgyzstan";

			case "LA":
				return "Lao People's Democratic Republic";
				
			case "LV":
				return "Latvia";
				
			case "LB":
				return "Lebanon";
				
			case "LS":
				return "Lesotho";
				
			case "LR":
				return "Liberia";
				
			case "LY":
				return "Libyan Arab Jamahiriya";
				
			case "LI":
				return "Liechtenstein";
				
			case "LT":
				return "Lithuania";
				
			case "LU":
				return "Luxembourg";
				
			case "MO":
				return "Macau";
				
			case "MK":
				return "Macedonia, The Former Yugoslav Republic of";
				
			case "MG":
				return "Madagascar";
				
			case "MW":
				return "Malawi";
				
			case "MY":
				return "Malaysia";
				
			case "MV":
				return "Maldives";
				
			case "ML":
				return "Mali";
				
			case "MT":
				return "Malta";
				
			case "MH":
				return "Marshall Islands";
				
			case "MQ":
				return "Martinique";
				
			case "MR":
				return "Mauritania";
				
			case "MU":
				return "Mauritius";
				
			case "YT":
				return "Mayotte";
				
			case "MX":
				return "Mexico";
				
			case "FM":
				return "Micronesia, Federated States of";
				
			case "MD":
				return "Moldova, Republic of";
				
			case "MC":
				return "Monaco";
				
			case "MN":
				return "Mongolia";
				
			case "ME":
				return "Montenegro";
				
			case "MS":
				return "Montserrat";
				
			case "MA":
				return "Morocco";
				
			case "MZ":
				return "Mozambique";
				
			case "MM":
				return "Myanmar";
	
			case "NA":
				return "Namibia";
				
			case "NR":
				return "Nauru";
				
			case "NP":
				return "Nepal";
				
			case "NL":
				return "Netherlands";
				
			case "AN":
				return "Netherlands Antilles";
				
			case "NC":
				return "New Caledonia";
				
			case "NZ":
				return "New Zealand";
				
			case "NI":
				return "Nicaragua";
				
			case "NE":
				return "Niger";
				
			case "NG":
				return "Nigeria";
				
			case "NU":
				return "Niue";
				
			case "NF":
				return "Norfolk Island";
				
			case "244":
				return "Northern Ireland";
				
			case "MP":
				return "Northern Mariana Islands";
				
			case "NO":
				return "Norway";
				
			case "OM":
				return "Oman";
				
			case "PK":
				return "Pakistan";
				
			case "PW":
				return "Palau";
				
			case "PS":
				return "Palestinian Territory, Occupied";
				
			case "PA":
				return "Panama";
				
			case "PG":
				return "Papua New Guinea";
				
			case "PY":
				return "Paraguay";
				
			case "PE":
				return "Peru";
				
			case "PH":
				return "Philippines";
				
			case "PN":
				return "Pitcairn";
				
			case "PL":
				return "Poland";
				
			case "PT":
				return "Portugal";
				
			case "PR":
				return "Puerto Rico";
				
			case "QA":
				return "Qatar";

			case "RE":
				return "Reunion";
				
			case "RO":
				return "Romania";
				
			case "RU":
				return "Russian Federation";
				
			case "RW":
				return "Rwanda";
				
			case "BL":
				return "Saint Barthelemy";
				
			case "SH":
				return "Saint Helena, Ascension and Tristan da Cunhda";
				
			case "KN":
				return "Saint Kitts and Nevis";
				
			case "LC":
				return "Saint Lucia";
				
			case "MF":
				return "Saint Martin";
				
			case "PM":
				return "Saint Pierre and Miquelon";
				
			case "VC":
				return "Saint Vincent and the Grenadines";
				
			case "WS":
				return "Samoa";
				
			case "SM":
				return "San Marino";
				
			case "ST":
				return "Sao Tome and Principe";
				
			case "SA":
				return "Saudi Arabia";
				
			case "SN":
				return "Senegal";
				
			case "RS":
				return "Serbia";
				
			case "SC":
				return "Seychelles";
				
			case "SL":
				return "Sierra Leone";
				
			case "SG":
				return "Singapore";
				
			case "SK":
				return "Slovakia";

			case "SI":
				return "Slovenia";
				
			case "SB":
				return "Solomon Islands";
				
			case "SO":
				return "Somalia";
				
			case "ZA":
				return "South Africa";
				
			case "GS":
				return "South Georgia and the South Sandwich Islands";
				
			case "ES":
				return "Spain";
				
			case "LK":
				return "Sri Lanka";
				
			case "SD":
				return "Sudan";
				
			case "SR":
				return "Suriname";
				
			case "SJ":
				return "Svalbard and Jan Mayen Islands";
				
			case "SZ":
				return "Swaziland";
				
			case "SE":
				return "Sweden";
				
			case "CH":
				return "Switzerland";
				
			case "SY":
				return "Syrian Arab Republic";

			case "TW":
				return "Taiwan, Province of China";
				
			case "TJ":
				return "Tajikistan";
				
			case "TZ":
				return "Tanzania, United Republic of";
				
			case "TH":
				return "Thailand";
				
			case "TP":
				return "East Timor";
				
			case "TL":
				return "Timor-Leste";
				
			case "TG":
				return "Togo";
				
			case "TK":
				return "Tokelau";
				
			case "TO":
				return "Tonga";
				
			case "TT":
				return "Trinidad and Tobago";
				
			case "TN":
				return "Tunisia";
				
			case "TR":
				return "Turkey";
				
			case "TM":
				return "Turkmenistan";
				
			case "TC":
				return "Turks and Caicos Islands";
				
			case "TV":
				return "Tuvalu";
		
			case "UG":
				return "Uganda";
				
			case "UA":
				return "Ukraine";
				
			case "AE":
				return "United Arab Emirates";
				
			case "GB":
				return "United Kingdom";
				
			case self::$COUNTRY_ID_US:
				return "United States";
				
			case "UM":
				return "United States Minor Outlying Islands";
				
			case "UY":
				return "Uruguay";
				
			case "UZ":
				return "Uzbekistan";

			case "VU":
				return "Vanuatu";
				
			case "VE":
				return "Venezuela, Bolivarian Republic of";
				
			case "VN":
				return "Viet Nam";
				
			case "VG":
				return "Virgin Islands (British)";
				
			case "VI":
				return "Virgin Islands (U.S.)";
				
			case "WF":
				return "Wallis and Futuna Islands";
				
			case "EH":
				return "Western Sahara";
				
			case "YE":
				return "Yemen";
				
			case "YU":
				return "Yugoslavia";
				
			case "ZR":
				return "Zaire";
				
			case "ZM":
				return "Zambia";
				
			case "ZW":
				return "Zimbabwe";
			
			default:
				return $iso;
		}
	}
	
	public static function getCountryCode($country)
	{
		switch(strtolower($country)) 		
		{
			case strtolower("Aaland Islands"):
				return "AX";
				
			case strtolower("Afghanistan"):
				return "AF";
				
			case strtolower("Albania"):
				return "AL";
				
			case strtolower("Algeria"):
				return "DZ";
				
			case strtolower("American Samoa"):
				return "AS";
				
			case strtolower("Andorra"):
				return "AD";
				
			case strtolower("Angola"):
				return "AO";
				
			case strtolower("Anguilla"):
				return "AI";
				
			case strtolower("Antarctica"):
				return "AQ";
				
			case strtolower("Antigua and Barbuda"):
				return "AG";
				
			case strtolower("Argentina"):
				return "AR";
				
			case strtolower("Armenia"):
				return "AM";
				
			case strtolower("Aruba"):
				return "AW";
				
			case strtolower("Australia"):
				return "AU";
				
			case strtolower("Austria"):
				return "AT";
				
			case strtolower("Azerbaijan"):
				return "AZ";
				
			case strtolower("Bahamas"):
				return "BS";
				
			case strtolower("Bahrain"):
				return "BH";
				
			case strtolower("Bangladesh"):
				return "BD";
				
			case strtolower("Barbados"):
				return "BB";
				
			case strtolower("Belarus"):
				return "BY";
				
			case strtolower("Belgium"):
				return "BE";
				
			case strtolower("Belize"):
				return "BZ";
				
			case strtolower("Benin"):
				return "BJ";
				
			case strtolower("Bermuda"):
				return "BM";
				
			case strtolower("Bhutan"):
				return "BT";
				
			case strtolower("Bolivia"):
				return "BO";
				
			case strtolower("Bosnia and Herzegowina"):
				return "BA";
				
			case strtolower("Botswana"):
				return "BW";
				
			case strtolower("Bouvet Island"):
				return "BV";
				
			case strtolower("Brazil"):
				return "BR";
				
			case strtolower("British Indian Ocean Territory"):
				return "IO";
				
			case strtolower("Brunei Darussalam"):
				return "BN";
				
			case strtolower("Bulgaria"):
				return "BG";
				
			case strtolower("Burkina Faso"):
				return "BF";
				
			case strtolower("Burundi"):
				return "BI";
				
			case strtolower("Cambodia"):
				return "KH";
				
			case strtolower("Cameroon"):
				return "CM";
				
			case strtolower("Canada"):
				return "CA";
				
			case strtolower("Cape Verde"):
				return "CV";
				
			case strtolower("Cayman Islands"):
				return "KY";
				
			case strtolower("Central African Republic"):
				return "CF";
				
			case strtolower("Chad"):
				return "TD";
				
			case strtolower("Chile"):
				return "CL";
				
			case strtolower("China"):
				return "CN";
				
			case strtolower("Christmas Island"):
				return "CX";
				
			case strtolower("Cocos (Keeling) Islands"):
				return "CC";
				
			case strtolower("Colombia"):
				return "CO";
				
			case strtolower("Comoros"):
				return "KM";
				
			case strtolower("Congo"):
				return "CG";
				
			case strtolower("Congo, The Democratic Republic of the"):
				return "CD";
				
			case strtolower("Cook Islands"):
				return "CK";
				
			case strtolower("Costa Rica"):
				return "CR";
				
			case strtolower("Cote D'Ivoire"):
				return "CI";
				
			case strtolower("Croatia"):
				return "HR";
				
			case strtolower("Cuba"):
				return "CU";
				
			case strtolower("Cyprus"):
				return "CY";
				
			case strtolower("Czech Republic"):
				return "CZ";
				
			case strtolower("Denmark"):
				return "DK";
				
			case strtolower("Djibouti"):
				return "DJ";
				
			case strtolower("Dominica"):
				return "DM";
				
			case strtolower("Dominican Republic"):
				return "DO";
				
			case strtolower("Ecuador"):
				return "EC";
				
			case strtolower("Egypt"):
				return "EG";
				
			case strtolower("El Salvador"):
				return "SV";
				
			case strtolower("Equatorial Guinea"):
				return "GQ";
				
			case strtolower("Eritrea"):
				return "ER";
				
			case strtolower("Estonia"):
				return "EE";
				
			case strtolower("Ethiopia"):
				return "ET";

			case strtolower("Falkland Islands (Malvinas)"):
				return "FK";
				
			case strtolower("Faroe Islands"):
				return "FO";
				
			case strtolower("Fiji"):
				return "FJ";
				
			case strtolower("Finland"):
				return "FI";
				
			case strtolower("France"):
				return "FR";
				
			case strtolower("France, Metropolitan"):
				return "FX";
				
			case strtolower("French Guiana"):
				return "GF";
				
			case strtolower("French Polynesia"):
				return "PF";
				
			case strtolower("French Southern Territories"):
				return "TF";
				
			case strtolower("Gabon"):
				return "GA";
				
			case strtolower("Gambia"):
				return "GM";
				
			case strtolower("Georgia"):
				return "GE";
				
			case strtolower("Germany"):
				return "DE";
				
			case strtolower("Ghana"):
				return "GH";
				
			case strtolower("Gibraltar"):
				return "GI";
				
			case strtolower("Greece"):
				return "GR";
				
			case strtolower("Greenland"):
				return "GL";
				
			case strtolower("Grenada"):
				return "GD";
				
			case strtolower("Guadeloupe"):
				return "GP";
				
			case strtolower("Guam"):
				return "GU";
				
			case strtolower("Guatemala"):
				return "GT";
				
			case strtolower("Guernsey"):
				return "GG";
				
			case strtolower("Guinea"):
				return "GN";
				
			case strtolower("Guinea-bissau"):
				return "GW";
				
			case strtolower("Guyana"):
				return "GY";
				
			case strtolower("Haiti"):
				return "HT";
				
			case strtolower("Heard and McDonald Islands"):
				return "HM";
				
			case strtolower("Holy See (Vatican City State)"):
				return "VA";
				
			case strtolower("Honduras"):
				return "HN";
				
			case strtolower("Hong Kong"):
				return "HK";
				
			case strtolower("Hungary"):
				return "HU";
				
			case strtolower("Iceland"):
				return "IS";
				
			case strtolower("India"):
				return "IN";
				
			case strtolower("Indonesia"):
				return "ID";
				
			case strtolower("Iran, Islamic Republic of"):
				return "IR";
				
			case strtolower("Iraq"):
				return "IQ";
				
			case strtolower("Ireland"):
				return "IE";
				
			case strtolower("Isle of Man"):
				return "IM";
				
			case strtolower("Israel"):
				return "IL";
				
			case strtolower("Italy"):
				return "IT";

			case strtolower("Jamaica"):
				return "JM";
				
			case strtolower("Japan"):
				return "JP";
				
			case strtolower("Jersey"):
				return "JE";
				
			case strtolower("Jordan"):
				return "JO";
				
			case strtolower("Kazakhstan"):
				return "KZ";
				
			case strtolower("Kenya"):
				return "KE";
				
			case strtolower("Kiribati"):
				return "KI";
				
			case strtolower("Korea, Democratic People's Republic of"):
				return "KP";
				
			case strtolower("Korea, Republic of"):
				return "KR";
				
			case strtolower("Kuwait"):
				return "KW";
				
			case strtolower("Kyrgyzstan"):
				return "KG";

			case strtolower("Lao People's Democratic Republic"):
				return "LA";
				
			case strtolower("Latvia"):
				return "LV";
				
			case strtolower("Lebanon"):
				return "LB";
				
			case strtolower("Lesotho"):
				return "LS";
				
			case strtolower("Liberia"):
				return "LR";
				
			case strtolower("Libyan Arab Jamahiriya"):
				return "LY";
				
			case strtolower("Liechtenstein"):
				return "LI";
				
			case strtolower("Lithuania"):
				return "LT";
				
			case strtolower("Luxembourg"):
				return "LU";
				
			case strtolower("Macau"):
				return "MO";
				
			case strtolower("Macedonia, The Former Yugoslav Republic of"):
				return "MK";
				
			case strtolower("Madagascar"):
				return "MG";
				
			case strtolower("Malawi"):
				return "MW";
				
			case strtolower("Malaysia"):
				return "MY";
				
			case strtolower("Maldives"):
				return "MV";
				
			case strtolower("Mali"):
				return "ML";
				
			case strtolower("Malta"):
				return "MT";
				
			case strtolower("Marshall Islands"):
				return "MH";
				
			case strtolower("Martinique"):
				return "MQ";
				
			case strtolower("Mauritania"):
				return "MR";
				
			case strtolower("Mauritius"):
				return "MU";
				
			case strtolower("Mayotte"):
				return "YT";
				
			case strtolower("Mexico"):
				return "MX";
				
			case strtolower("Micronesia, Federated States of"):
				return "FM";
				
			case strtolower("Moldova, Republic of"):
				return "MD";
				
			case strtolower("Monaco"):
				return "MC";
				
			case strtolower("Mongolia"):
				return "MN";
				
			case strtolower("Montenegro"):
				return "ME";
				
			case strtolower("Montserrat"):
				return "MS";
				
			case strtolower("Morocco"):
				return "MA";
				
			case strtolower("Mozambique"):
				return "MZ";
				
			case strtolower("Myanmar"):
				return "MM";
	
			case strtolower("Namibia"):
				return "NA";
				
			case strtolower("Nauru"):
				return "NR";
				
			case strtolower("Nepal"):
				return "NP";
				
			case strtolower("Netherlands"):
				return "NL";
				
			case strtolower("Netherlands Antilles"):
				return "AN";
				
			case strtolower("New Caledonia"):
				return "NC";
				
			case strtolower("New Zealand"):
				return "NZ";
				
			case strtolower("Nicaragua"):
				return "NI";
				
			case strtolower("Niger"):
				return "NE";
				
			case strtolower("Nigeria"):
				return "NG";
				
			case strtolower("Niue"):
				return "NU";
				
			case strtolower("Norfolk Island"):
				return "NF";
				
			case strtolower("Northern Ireland"):
				return "244";
				
			case strtolower("Northern Mariana Islands"):
				return "MP";
				
			case strtolower("Norway"):
				return "NO";
				
			case strtolower("Oman"):
				return "OM";
				
			case strtolower("Pakistan"):
				return "PK";
				
			case strtolower("Palau"):
				return "PW";
				
			case strtolower("Palestinian Territory, Occupied"):
				return "PS";
				
			case strtolower("Panama"):
				return "PA";
				
			case strtolower("Papua New Guinea"):
				return "PG";
				
			case strtolower("Paraguay"):
				return "PY";
				
			case strtolower("Peru"):
				return "PE";
				
			case strtolower("Philippines"):
				return "PH";
				
			case strtolower("Pitcairn"):
				return "PN";
				
			case strtolower("Poland"):
				return "PL";
				
			case strtolower("Portugal"):
				return "PT";
				
			case strtolower("Puerto Rico"):
				return "PR";
				
			case strtolower("Qatar"):
				return "QA";

			case strtolower("Reunion"):
				return "RE";
				
			case strtolower("Romania"):
				return "RO";
				
			case strtolower("Russian Federation"):
				return "RU";
				
			case strtolower("Rwanda"):
				return "RW";
				
			case strtolower("Saint Barthelemy"):
				return "BL";
				
			case strtolower("Saint Helena, Ascension and Tristan da Cunhda"):
				return "SH";
				
			case strtolower("Saint Kitts and Nevis"):
				return "KN";
				
			case strtolower("Saint Lucia"):
				return "LC";
				
			case strtolower("Saint Martin"):
				return "MF";
				
			case strtolower("Saint Pierre and Miquelon"):
				return "PM";
				
			case strtolower("Saint Vincent and the Grenadines"):
				return "VC";
				
			case strtolower("Samoa"):
				return "WS";
				
			case strtolower("San Marino"):
				return "SM";
				
			case strtolower("Sao Tome and Principe"):
				return "ST";
				
			case strtolower("Saudi Arabia"):
				return "SA";
				
			case strtolower("Senegal"):
				return "SN";
				
			case strtolower("Serbia"):
				return "RS";
				
			case strtolower("Seychelles"):
				return "SC";
				
			case strtolower("Sierra Leone"):
				return "SL";
				
			case strtolower("Singapore"):
				return "SG";
				
			case strtolower("Slovakia"):
				return "SK";

			case strtolower("Slovenia"):
				return "SI";
				
			case strtolower("Solomon Islands"):
				return "SB";
				
			case strtolower("Somalia"):
				return "SO";
				
			case strtolower("South Africa"):
				return "ZA";
				
			case strtolower("South Georgia and the South Sandwich Islands"):
				return "GS";
				
			case strtolower("Spain"):
				return "ES";
				
			case strtolower("Sri Lanka"):
				return "LK";
				
			case strtolower("Sudan"):
				return "SD";
				
			case strtolower("Suriname"):
				return "SR";
				
			case strtolower("Svalbard and Jan Mayen Islands"):
				return "SJ";
				
			case strtolower("Swaziland"):
				return "SZ";
				
			case strtolower("Sweden"):
				return "SE";
				
			case strtolower("Switzerland"):
				return "CH";
				
			case strtolower("Syrian Arab Republic"):
				return "SY";

			case strtolower("Taiwan, Province of China"):
				return "TW";
				
			case strtolower("Tajikistan"):
				return "TJ";
				
			case strtolower("Tanzania, United Republic of"):
				return "TZ";
				
			case strtolower("Thailand"):
				return "TH";
				
			case strtolower("East Timor"):
				return "TP";
				
			case strtolower("Timor-Leste"):
				return "TL";
				
			case strtolower("Togo"):
				return "TG";
				
			case strtolower("Tokelau"):
				return "TK";
				
			case strtolower("Tonga"):
				return "TO";
				
			case strtolower("Trinidad and Tobago"):
				return "TT";
				
			case strtolower("Tunisia"):
				return "TN";
				
			case strtolower("Turkey"):
				return "TR";
				
			case strtolower("Turkmenistan"):
				return "TM";
				
			case strtolower("Turks and Caicos Islands"):
				return "TC";
				
			case strtolower("Tuvalu"):
				return "TV";
		
			case strtolower("Uganda"):
				return "UG";
				
			case strtolower("Ukraine"):
				return "UA";
				
			case strtolower("United Arab Emirates"):
				return "AE";
				
			case strtolower("United Kingdom"):
				return "GB";
				
			case strtolower("United States"):
				return self::$COUNTRY_ID_US;
				
			case strtolower("United States Minor Outlying Islands"):
				return "UM";
				
			case strtolower("Uruguay"):
				return "UY";
				
			case strtolower("Uzbekistan"):
				return "UZ";

			case strtolower("Vanuatu"):
				return "VU";
				
			case strtolower("Venezuela, Bolivarian Republic of"):
				return "VE";
				
			case strtolower("Viet Nam"):
				return "VN";
				
			case strtolower("Virgin Islands (British)"):
				return "VG";
				
			case strtolower("Virgin Islands (U.S.)"):
				return "VI";
				
			case strtolower("Wallis and Futuna Islands"):
				return "WF";
				
			case strtolower("Western Sahara"):
				return "EH";
				
			case strtolower("Yemen"):
				return "YE";
				
			case strtolower("Yugoslavia"):
				return "YU";
				
			case strtolower("Zaire"):
				return "ZR";
				
			case strtolower("Zambia"):
				return "ZM";
				
			case strtolower("Zimbabwe"):
				return "ZW";
			
			default:
				return $country;
		}
	}
	
	public static function getPaymentOptions(){
		$paymentOptions = array();
		
		$paymentOptions["visa"] = "VISA";
		$paymentOptions["master"] = "Master Card";
		$paymentOptions["discover"] = "Discover";
		$paymentOptions["amex"] = "AMEX";
		
		return $paymentOptions;
	}
	
	public static function getPaymentMethodName($id)
	{
		switch($id) 
		{
			case "visa":
				return "VISA";
				
			case "master":
				return "Master Card";
				
			case "discover":
				return "Discover";
				
			case "amex":
				return "AMEX";
		}
	}
}
?>

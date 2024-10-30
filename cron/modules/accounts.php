<?php
class accounts extends Runner
{
	public static $KEY_THRESHOLD_WARNING_DATE = 'mm-threshold-warning-date';
	public static $TABLE_POSTS = "wp_posts";
	private function isValidDateTime($dateTime)
	{
	    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
	        if (checkdate($matches[2], $matches[3], $matches[1])) {
	            return true;
	        }
	    }
	
	    return false;
	}
	public function Process()
	{
		$flaggedUserMsgs = "";
		$response = MM_MemberMouseService::getAllSites($dg->sortBy,$dg->sortDir,0,1000);
		$data = $response->response_data;
		if(is_array($data)){
			for($i=0; $i<count($data); $i++){
				$msgToAdmins = "";
				$row = $data[$i];
				$totalMembers = $row->total_members;
				$paidMembers = $row->paid_members;
				$memberId = $row->member_id;
				
				$user = new MM_User($memberId);
				if($user->isValid()){
					$memberType = new MM_MemberType($user->getMemberTypeId());
					if($memberType->isValid()){
						$accountType = new MM_AccountType($memberType->getAccountTypeId());
						if($accountType->isValid()){
							$dbPaidMembers = $accountType->getNumPaidMembers();
							$dbTotalMembers = $accountType->getNumTotalMembers();
							
							$msgStart = "ID: ".$memberId."\n";
							$msgStart .= "Name: ".$user->getFullName()."\n";
							$msgStart .= "Email: ".$user->getEmail()."\n";
							$msgStart .= "Member Type: ".$memberType->getName()." [".$user->getMemberTypeId()."]\n";
							$msgStart .= "Account Type: ".$accountType->getName()." [".$accountType->getId()."]\n";
							if(intval($totalMembers)>intval($dbTotalMembers)){
								$msgToAdmins = "Total Members Exceeded: {$totalMembers} of {$dbTotalMembers}\n";
							}
							if(intval($paidMembers)>intval($dbPaidMembers)){
								$msgToAdmins .= "Paid Members Exceeded: {$paidMembers} of {$dbPaidMembers}\n";
							}
							
							if(!empty($msgToAdmins)){
								$msgToAdmins = $msgStart.$msgToAdmins;
							}
						}
						else{
							$msgToAdmins = $user->getFullName()." [".$memberId."] does not have a valid account type: ".$memberType->getAccountTypeId();
						}
					}
					else{
						$msgToAdmins = $user->getFullName()." [".$memberId."] does not have a valid member type: ".$user->getMemberTypeId();
					}
				}
				else{
					$msgToAdmins = "Could not find a valid user with ID: {$memberId}";
				}
				
				if(!empty($msgToAdmins)){
					$lastSent = get_user_meta($user->getId(),self::$KEY_THRESHOLD_WARNING_DATE, true);
					echo "Last Sent: {$lastSent}\n\n";
					if($this->isValidDateTime($lastSent)){
						$nextRun = Date("Y-m-d h:i:s", strtotime(date("Y-m-d h:i:s", strtotime($lastSent)) . " +7 day"));
						$today = Date("Y-m-d h:i:s");
						if(strtotime($nextRun)<=strtotime($today)){
							echo "Has been 7 days...reset new date {$today}....\n";
							delete_user_meta($user->getId(),self::$KEY_THRESHOLD_WARNING_DATE );
							add_user_meta($user->getId(), self::$KEY_THRESHOLD_WARNING_DATE, $today);
							$flaggedUserMsgs .= $msgToAdmins."\n\n--------------\n\n";
						}
						else{
							echo "Not sending until {$nextRun}\n\n";
						}
					}
					else{
						echo "date has not been set yet...\n";
						delete_user_meta($user->getId(),self::$KEY_THRESHOLD_WARNING_DATE );
						add_user_meta($user->getId(), self::$KEY_THRESHOLD_WARNING_DATE, Date("Y-m-d h:i:s"));
						$flaggedUserMsgs .= $msgToAdmins."\n\n--------------\n\n";
					}
					
				}
			}
			
			$user= new MM_User();
			$emailAccount = MM_EmailAccount::getDefaultAccount();
			$context = new MM_Context($user, $emailAccount);
			
			$email = new MM_Email();
			$email->setContext($context);
			$email->setSubject("MemberMouse Account Type Report");
			$email->setBody($flaggedUserMsgs);
			$email->setToName("Matt Young");
			$email->setToAddress("matt@membermouse.com");
			$email->setFromName($emailAccount->getName());
			$email->setFromAddress($emailAccount->getAddress());
			
			$response = $email->send();
			if($response->type==MM_Response::$ERROR){
				echo "ERROR! ".$response->message."\n\n";
			}
			else{
				echo " \n\nEmail sent \n\n {$flaggedUserMsgs}\n\n";
			}
		}
		return true;
	}
	
	public function getNextRunDate(){
		$date = Date("Y-m-d h:i:s");
		return Date("Y-m-d h:i:s", strtotime(date("Y-m-d h:i:s", strtotime($date)) . " +1 day"));
	}
}
?>
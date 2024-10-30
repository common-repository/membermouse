<?php
class notifications extends Runner
{
	public static $TABLE_POSTS = "wp_posts";
	public function Process()
	{
		global $wpdb;
		
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_CRON_INSTALLED, "1");
		
		$users = MM_User::getAllMembers(true);
		$newAccessArr = MM_ContentDeliveryEngine::getUserSchedules($users);
		
		if(is_array($newAccessArr)){
			foreach($newAccessArr as $userId=>$postId){
				if(MM_ContentDeliveryEngine::sendNotification($userId, $postId)){
					echo "Sent to user\n";	
				}
				else{
					echo "Could not send to user.";
				}
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
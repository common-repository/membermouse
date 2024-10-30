<?php
function mm_smarttag($content, $echo=true)
{
	$user = new MM_User();
	$emailAccount = new MM_EmailAccount();
	$context = new MM_Context($user, $emailAccount);
	
	$content = MM_SmartTagEngine::processContent($content, $context);
	if($echo)
	{
		echo $content;
	}
	else{
		return $content;
	}
}

function mm_IsContentAvailable($postIds=null, $userId=null){
	global $post, $current_user;
	$postId = (is_null($postIds))?$post->ID:$postIds;
	$userId = (is_null($userId))?$current_user->ID:$userId;

 	$iscontentavailable = preg_replace("/[\s]+/", "", $postIds);
 	$contentPostIdArr = explode(",", $iscontentavailable);
 	
 	if(is_array($contentPostIdArr) && !empty($contentPostIdArr)){
 		$contentIsAvailable = false;
 		foreach($contentPostIdArr as $postId){
 			$protectedContent = new MM_ProtectedContentEngine();
 			if(preg_match("/^(-)/", $postId)){
 				$postId = preg_replace("/[^0-9]+/", "", $postId);
 				
 				if(!$protectedContent->canAccessPost($postId, $userId)){
 					$contentIsAvailable = true;
 				}
 			}
 			else{
 				if($protectedContent->canAccessPost($postId, $userId)){
 					$contentIsAvailable = true;
 				}
 			}
 		}
 	}
 	
 	return $contentIsAvailable;
}

function mm_isMemberType($memberTypeId=0)
{
	global $current_user;
	
	$userId = (isset($current_user->ID) && intval($current_user->ID)>0)?$current_user->ID:0;
	$user = new MM_User($userId);
	$userMemberTypeId = $user->getMemberTypeId();
	
	if($userMemberTypeId == $memberTypeId)
		return true;
	
	return false;
}

function mm_hasAccessTag($accessTagId=0)
{
	global $current_user;
	
	$userId = (isset($current_user->ID) && intval($current_user->ID)>0)?$current_user->ID:0;
	$user = new MM_User($userId);
	return $user->hasAccessTag($accessTagId);
}

function mm_daysAsMember()
{
	global $current_user;
	if(MM_Utils::isAdmin($current_user->ID)){
 	 	$adminPreviewObj = MM_Preview::getData();
 	 	
 	 	if(!$adminPreviewObj){	
 	 		return false;
 	 	}
 	 	return $adminPreviewObj->getDays();
	}
	$userId = (isset($current_user->ID) && intval($current_user->ID)>0)?$current_user->ID:0;
	if($userId<=0){
		return -1;
	}
	$user = new MM_User($userId);
	return $user->getDaysAsMember();
}


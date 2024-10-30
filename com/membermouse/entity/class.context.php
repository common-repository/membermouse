<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Context
{	
	public $member = null;
	public $emailAccount = null;
	
	public function __construct(MM_User $user, MM_EmailAccount $emailAccount) 
 	{
 		$this->member = $user;
 		$this->emailAccount = $emailAccount;
 	}
}
?>

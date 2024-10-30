<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$username = "matt";
$password = "pass";
$url = "http://mmcentral.localhost/?q=/newMember";
$postvars = "user=".$username."&pwd=".$password;
 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST      ,1);
curl_setopt($ch, CURLOPT_POSTFIELDS    , $postvars);
curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
$contents = curl_exec($ch);
curl_close($ch);
echo $contents;
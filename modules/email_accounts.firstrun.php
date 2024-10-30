<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>

<!-- Email Accounts Dialog -->
<div id="mm-email-accounts-dialog"></div>

<script>mmJQuery("#mm-email-accounts-dialog").dialog({autoOpen: false, buttons: {
	"Save Employee Account": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
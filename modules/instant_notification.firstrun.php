<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- INI Dialog -->
<div id="mm-instant-notification-dialog"></div>

<script>mmJQuery("#mm-instant-notification-dialog").dialog({autoOpen: false, buttons: {
	"Save Event Notification": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
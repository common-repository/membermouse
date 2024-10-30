<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>

<!-- Account Types Dialog -->
<div id="mm-account-types-dialog"></div>

<script>mmJQuery("#mm-account-types-dialog").dialog({autoOpen: false, buttons: {
	"Save Account Type": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
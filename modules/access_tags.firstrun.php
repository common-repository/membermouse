<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>

<!-- Access Tags Dialog -->
<div id="mm-access-tags-dialog"></div>

<script>mmJQuery("#mm-access-tags-dialog").dialog({autoOpen: false, buttons: {
	"Save Access Tag": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
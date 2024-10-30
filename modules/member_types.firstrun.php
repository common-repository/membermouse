<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Member Types Dialog -->
<div id="mm-member-types-dialog"></div>

<script>mmJQuery("#mm-member-types-dialog").dialog({autoOpen: false, buttons: {
	"Save Member Type": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
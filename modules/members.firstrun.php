<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Member Types Dialog -->
<div id="mm-create-member-dialog"></div>

<script>mmJQuery("#mm-create-member-dialog").dialog({autoOpen: false, buttons: {
	"Create Member": function() { mmjs.createMember(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>
<?php
// $Id: onupdate.inc.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
// referer check
$ref = xoops_getenv('HTTP_REFERER');
if( $ref == '' || strpos( $ref , XOOPS_URL.'/modules/system/admin.php' ) === 0 ) {
	/* module specific part */
	/* General part */

	// Keep the values of block's options when module is updated (by nobunobu)
	include dirname( __FILE__ ) . "/updateblock.inc.php" ;

}
?>
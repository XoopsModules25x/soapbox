<?php
/**
 * $Id: header.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
global $xoopsModule;
include("../../mainfile.php");

include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/include/functions.php";
$myts = & MyTextSanitizer :: getInstance();
?>

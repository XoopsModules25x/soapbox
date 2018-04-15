<?php
/**
 *
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Soapbox;

global $xoopsModule;
include  dirname(dirname(__DIR__)) . '/mainfile.php';

$myts = \MyTextSanitizer:: getInstance();

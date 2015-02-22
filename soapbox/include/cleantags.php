<?php
// $Id: cleantags.php,v 0.0.1 2005/10/26 13:30:00 domifara Exp $
/* This file comes from a post by tREXX [www.trexx.ch] in http://www.php.net/manual/en/function.strip-tags.php */
//  ------------------------------------------------------------------------ //
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}

if( ! class_exists( 'SoapboxCleantags' ) ) {

class SoapboxCleantags
{
	/*
	 * Allow these tags
	 */
	var $allowedTags ;
	
	/*
	 * Disallow these attributes/prefix within a tag
	 */
	var $stripAttrib ;

	/*
	 * Disallow these attributes/prefix within a tag
	 */
	var $stripattribpaterns ;

	// render form as plain html
	function SoapboxCleantags()
	{
//		$this->allowedTags = '<h1><b><i><a><ul><li><pre><hr><blockquote>';
		$this->allowedTags = '<a><acronym><address><b><br><blockquote><cite><code><div><dd><del><dl><dt><em><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><pre><s><span><strong><sub><table><tr><td><th><u><ul>';
		/*
		 * Disallow these attributes/prefix within a tag
		 */
		$this->stripAttrib = 'javascript|onclick|ondblclick|onmousedown|onmouseup|onmouseover|'.
		               'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|vbscript|about';

		$this->stripattribpaterns = '/(<[^>]*)('.$this->stripAttrib.')([^>]*>)/is';
	}
	
	/*
	 * @return string
	 * @param string
	 * @desc Strip forbidden tags and delegate tag-source check to cleanAttributes()
	 */
	function cleanTags($source)
	{
		$source = strip_tags($source, $this->allowedTags);
		if (preg_match($this->stripattribpaterns, $source) ) {
			$source = $this->cleanAttributes($source);
		}
	   return $source;
	}
	
	/*
	 * @return string
	 * @param string
	 * @desc Strip forbidden attributes from a tag
	 */
	function cleanAttributes($tagSource="")
	{
		$tagSource = preg_replace($this->stripattribpaterns, "\\1forbidden\\3" , $tagSource);
		if (preg_match($this->stripattribpaterns , $tagSource) ) {
			$tagSource = $this->cleanAttributes($tagSource);
		}
 	   return $tagSource;
	}

}

// create a instance in global scope
$GLOBALS['SoapboxCleantags'] = new SoapboxCleantags() ;

}	

?>
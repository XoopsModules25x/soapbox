<?php namespace XoopsModules\Soapbox;

//
/* This file comes from a post by tREXX [www.trexx.ch] in http://www.php.net/manual/en/function.strip-tags.php */
//  ------------------------------------------------------------------------ //
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Soapbox;

if (!class_exists(Cleantags::class)) {
    /**
     * Class Cleantags
     */
    class Cleantags
    {
        /*
         * Allow these tags
         */
        public $allowedTags;

        /*
         * Disallow these attributes/prefix within a tag
         */
        public $stripAttrib;

        /*
         * Disallow these attributes/prefix within a tag
         */
        public $stripattribpaterns;

        // render form as plain html

        /**
         * Cleantags constructor.
         */
        public function __construct()
        {
            //        $this->allowedTags = '<h1><b><i><a><ul><li><pre><hr><blockquote>';
            $this->allowedTags = '<a><abbr><address><b><br><blockquote><cite><code><div><dd><del><dl><dt><em><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><pre><s><span><strong><sub><table><tr><td><th><u><ul>';
            /*
             * Disallow these attributes/prefix within a tag
             */
            $this->stripAttrib = 'javascript|onclick|ondblclick|onmousedown|onmouseup|onmouseover|' . 'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|vbscript|about';

            $this->stripattribpaterns = '/(<[^>]*)(' . $this->stripAttrib . ')([^>]*>)/is';
        }

        /*
         * @return string
         * @param string
         * @desc Strip forbidden tags and delegate tag-source check to cleanAttributes()
         */
        /**
         * @param $source
         * @return mixed|string
         */
        public function cleanTags($source)
        {
            $source = strip_tags($source, $this->allowedTags);
            if (preg_match($this->stripattribpaterns, $source)) {
                $source = $this->cleanAttributes($source);
            }

            return $source;
        }

        /*
         * @return string
         * @param string
         * @desc Strip forbidden attributes from a tag
         */
        /**
         * @param  string $tagSource
         * @return mixed|string
         */
        public function cleanAttributes($tagSource = '')
        {
            $tagSource = preg_replace($this->stripattribpaterns, "\\1forbidden\\3", $tagSource);
            if (preg_match($this->stripattribpaterns, $tagSource)) {
                $tagSource = $this->cleanAttributes($tagSource);
            }

            return $tagSource;
        }
    }

    // create a instance in global scope
    $cleantags = new Soapbox\Cleantags();
}

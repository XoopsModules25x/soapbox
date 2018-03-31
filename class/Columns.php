<?php namespace XoopsModules\Soapbox;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright      {@link https://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */

use XoopsModules\Soapbox;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
//require_once XOOPS_ROOT_PATH . '/modules/soapbox/include/cleantags.php';

/**
 * Class Columns
 */
class Columns extends \XoopsObject
{
    /**
     * Columns constructor.
     */
    public function __construct()
    {
        $this->initVar('columnID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('author', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('total', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('colimage', XOBJ_DTYPE_TXTBOX, 'blank.png', false, 255);
        $this->initVar('created', XOBJ_DTYPE_INT, 1033141070, false);
        //not in table
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);
    }

    //##################### HACK Methods ######################
    //HACK for utf-8   clean when if utf-8 text is lost bytes
    /**
     * returns a specific variable for the object in a proper format
     *
     * @access public
     * @param  string $key    key of the object's variable to be returned
     * @param  string $format format to use for the output
     * @return mixed  formatted value of the variable
     */
    public function getVar($key, $format = 's')
    {
        $cleantags = new Soapbox\Cleantags();
        $ret = $this->vars[$key]['value'];
        //HACK for lost last byte cleaning of multi byte string
        //---------------------------------------
        if (XOOPS_USE_MULTIBYTES === 1) {
            switch ($this->vars[$key]['data_type']) {
                case XOBJ_DTYPE_TXTBOX:
                case XOBJ_DTYPE_TXTAREA:
                    $ret = $this->getJ_cleanLostByteTail($ret);
                    break 1;
                default:
                    break 1;
            }
        }
        //---------------------------------------
        switch ($this->vars[$key]['data_type']) {

            case XOBJ_DTYPE_TXTBOX:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'e':
                    case 'edit':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'p':
                    case 'preview':
                    case 'f':
                    case 'formpreview':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = $ts->htmlSpecialChars($ts->stripSlashesGPC($ret));
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_TXTAREA:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        $ts     = \MyTextSanitizer::getInstance();
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value'])
                                   || 1 === $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value'])
                                   || 1 === $this->vars['dosmiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || 1 === $this->vars['dobr']['value']) ? 1 : 0;
                        //----------------
                        if (1 === $html && 0 !== $br) {
                            $text = preg_replace(">((\015\012)|(\015)|(\012))/", '>', $ret);
                            $text = preg_replace("/((\015\012)|(\015)|(\012))</", '<', $ret);
                        }
                        $ret = $cleantags->cleanTags($ts->displayTarea($ret, $html, $smiley, $xcode, $image, $br));
                        //----------------
                        break 1;
                    case 'e':
                    case 'edit':
                        $ret = htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        $ts     = \MyTextSanitizer::getInstance();
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value'])
                                   || 1 === $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value'])
                                   || 1 === $this->vars['dosmiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || 1 === $this->vars['dobr']['value']) ? 1 : 0;
                        //----------------
                        if (1 === $html && 0 !== $br) {
                            $text = preg_replace(">((\015\012)|(\015)|(\012))/", '>', $ret);
                            $text = preg_replace("/((\015\012)|(\015)|(\012))</", '<', $ret);
                        }
                        $ret = $cleantags->cleanTags($ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br));
                        //----------------
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_INT:
                $ret = (int)$ret;
                break;
            case XOBJ_DTYPE_ARRAY:
                $ret = unserialize($ret);
                break;
            case XOBJ_DTYPE_SOURCE:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        break 1;
                    case 'e':
                    case 'edit':
                        $ret = htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = $ts->stripSlashesGPC($ret);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts  = \MyTextSanitizer::getInstance();
                        $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            default:
                if ('' !== $ret && '' !== $this->vars[$key]['options']) {
                    switch (strtolower($format)) {
                        case 's':
                        case 'show':
                            $selected = explode('|', $ret);
                            $options  = explode('|', $this->vars[$key]['options']);
                            $i        = 1;
                            $ret      = [];
                            foreach ($options as $op) {
                                if (in_array($i, $selected)) {
                                    $ret[] = $op;
                                }
                                ++$i;
                            }
                            $ret = implode(', ', $ret);
                            break;
                        case 'e':
                        case 'edit':
                            $ret = explode('|', $ret);
                            break 1;
                        default:
                            break 1;
                    }
                }
                break;
        }

        return $ret;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function getJ_cleanLostByteTail($text)
    {
        if ('UTF-8' === strtoupper(_CHARSET)) {
            $text = preg_replace('/[\xC0-\xFD]$/', '', $text);
            $text = preg_replace('/[\xE0-\xFD][\x80-\xBF]$/', '', $text);
            $text = preg_replace('/[\xF0-\xFD][\x80-\xBF]{2}$/', '', $text);
            $text = preg_replace('/[\xF8-\xFD][\x80-\xBF]{3}$/', '', $text);
            $text = preg_replace('/[\xFC-\xFD][\x80-\xBF]{4}$/', '', $text);
            $text = preg_replace('/^([\x80-\xBF]+)/', '', $text);
        } elseif ('EUC-JP' === strtoupper(_CHARSET)) {
            if (preg_match('/[\x80-\xff]$/', $text)) {
                $tmp = preg_replace('/\x8F[\x80-\xff]{2}/', '', $text); //EUC-jp EX 3 byte Foreign string
                $tmp = preg_replace('/[\x80-\xff]{2}/', '', $tmp);
                if (preg_match('/[\x80-\xff]$/', $tmp)) {
                    $text = substr($text, 0, -1);
                }
                if (preg_match('/^[\x80-\xff]/', $tmp)) {
                    $text = substr($text, 1);
                }
            }
        } else {
            if (preg_match('/[\x80-\xff]$/', $text)) {
                $tmp = preg_replace('/[\x80-\xff]{2}/', '', $text);
                if (preg_match('/[\x80-\xff]$/', $tmp)) {
                    $text = substr($text, 0, -1);
                }
                if (preg_match('/^[\x80-\xff]/', $tmp)) {
                    $text = substr($text, 1);
                }
            }
        }

        return $text;
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        $ret  = [];
        $vars = $this->getVars();
        foreach (array_keys($vars) as $i) {
            $ret[$i] = $this->getVar($i);
        }

        return $ret;
    }
}

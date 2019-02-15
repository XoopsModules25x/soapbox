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
if (!defined('XOBJ_SOAPBOX_DTYPE_FLOAT')) {
    define('XOBJ_SOAPBOX_DTYPE_FLOAT', 21);
}

/**
 * Class Articles
 */
class Articles extends \XoopsObject
{
    public $pre_offline;
    public $_sbcolumns;

    /**
     * Articles constructor.
     */
    public function __construct()
    {
        $this->initVar('articleID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('columnID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('headline', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('lead', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('bodytext', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('teaser', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('submit', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('datesub', XOBJ_DTYPE_LTIME, time(), false);
        $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('html', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('smiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('xcodes', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('breaks', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('block', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('artimage', XOBJ_DTYPE_TXTBOX, 'blank.png', false, 255);
        $this->initVar('votes', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('rating', XOBJ_SOAPBOX_DTYPE_FLOAT, 0.0000, false);
        $this->initVar('commentable', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('offline', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);

        $this->pre_offline = 1;
        $this->_sbcolumns  = false;
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
                        $html   = !empty($this->vars['html']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['xcodes']['value'])
                                   || 1 === $this->vars['xcodes']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['smiley']['value'])
                                   || 1 === $this->vars['smiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['breaks']['value'])
                                   || 1 === $this->vars['breaks']['value']) ? 1 : 0;
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
                        $html   = !empty($this->vars['html']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['xcodes']['value'])
                                   || 1 === $this->vars['xcodes']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['smiley']['value'])
                                   || 1 === $this->vars['smiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['breaks']['value'])
                                   || 1 === $this->vars['breaks']['value']) ? 1 : 0;
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
            case XOBJ_SOAPBOX_DTYPE_FLOAT:
                if (function_exists('floatval')) {
                    $ret = (float)$ret;
                } else {
                    $ret = (int)$ret;
                }
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
                        return htmlspecialchars($ret, ENT_QUOTES);
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
     * clean values of all variables of the object for storage.
     * also add slashes whereever needed
     *
     * @return bool true if successful
     * @access public
     */
    public function cleanVars()
    {
        $ts = \MyTextSanitizer::getInstance();
        foreach ($this->vars as $k => $v) {
            $cleanv = $v['value'];
            if (!$v['changed']) {
            } else {
                $cleanv = is_string($cleanv) ? trim($cleanv) : $cleanv;
                switch ($v['data_type']) {
                    case XOBJ_DTYPE_TXTBOX:
                        if ('' === $cleanv && '0' !== $cleanv && $v['required']) {
                            $this->setErrors("$k is required.");
                            continue 2;
                        }
                        if (isset($v['maxlength']) && strlen($cleanv) > (int)$v['maxlength']) {
                            $this->setErrors("$k must be shorter than " . (int)$v['maxlength'] . ' characters.');
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_TXTAREA:
                        if ('' === $cleanv && '0' !== $cleanv && $v['required']) {
                            $this->setErrors("$k is required.");
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_SOURCE:
                        $cleanv = $cleanv;
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_INT:
                        $cleanv = (int)$cleanv;
                        break;
                    //HACK by domifara
                    case XOBJ_SOAPBOX_DTYPE_FLOAT:
                        if (function_exists('floatval')) {
                            $cleanv = (float)$cleanv;
                        } else {
                            $cleanv = (int)$cleanv;
                        }
                        break;
                    case XOBJ_DTYPE_EMAIL:
                        if ('' === $cleanv && $v['required']) {
                            $this->setErrors("$k is required.");
                            continue 2;
                        }
                        if ('' !== $cleanv
                            && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv)) {
                            $this->setErrors('Invalid Email');
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_URL:
                        if ('' === $cleanv && $v['required']) {
                            $this->setErrors("$k is required.");
                            continue 2;
                        }
                        if ('' !== $cleanv && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                            $cleanv = 'http://' . $cleanv;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_ARRAY:
                        $cleanv = serialize($cleanv);
                        break;
                    case XOBJ_DTYPE_STIME:
                    case XOBJ_DTYPE_MTIME:
                    case XOBJ_DTYPE_LTIME:
                        $cleanv = !is_string($cleanv) ? (int)$cleanv : strtotime($cleanv);
                        break;
                    default:
                        break;
                }
            }
            $this->cleanVars[$k] = $cleanv;
            unset($cleanv);
        }
        if (count($this->_errors) > 0) {
            return false;
        }
        $this->unsetDirty();

        return true;
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

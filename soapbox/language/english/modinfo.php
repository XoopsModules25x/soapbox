<?php
/**
 * $Id: modinfo.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

// Module Info
// The name of this module
global $xoopsModule;
define("_MI_SB_MD_NAME","Soapbox");

// A brief description of this module
define("_MI_SB_MD_DESC","OpEd for your site");

// Sub menus in main menu block
define("_MI_SB_SUB_SMNAME1","Submit an article");

// A brief description of this module
define("_MI_SB_ALLOWSUBMIT","1. User submissions:");
define("_MI_SB_ALLOWSUBMITDSC","Allow users to submit articles to your website?");

define("_MI_SB_AUTOAPPROVE","2. Auto approve articles:");
define("_MI_SB_AUTOAPPROVEDSC","Auto approves submitted articles without admin intervention.");

define("_MI_SB_ALLOWADMINHITS","3. Admin counter reads:");
define("_MI_SB_ALLOWADMINHITSDSC","Allow admin hits for counter stats?");

define("_MI_SB_PERPAGE","4. Maximum articles per page (Admin side):");
define("_MI_SB_PERPAGEDSC","Maximum number of articles per page to be displayed at once in Articles Admin.");

define("_MI_SB_PERPAGEINDEX","5. Maximum articles per page (User side):");
define("_MI_SB_PERPAGEINDEXDSC","Maximum number of articles per page to be displayed at once in the user side of the module.");

define("_MI_SB_IMGDIR","6. Image base directory:");
define("_MI_SB_IMGDIRDSC","This is the directory that holds the operational images. (No trailing \"/\")");

define("_MI_SB_UPLOADDIR","7. Image upload directory:");
define("_MI_SB_UPLOADDIRDSC","This is the directory where columnists\" and articles\" pics will be stored. (No trailing \"/\")");

define("_MI_SB_IMGWIDTH","8. Maximum image width:");
define("_MI_SB_IMGWIDTHDSC","Sets the maximum allowed width of an image when uploading.");

define("_MI_SB_IMGHEIGHT","9. Maximum image height:");
define("_MI_SB_IMGHEIGHTDSC","Sets the maximum allowed height of an image when uploading.");

define("_MI_SB_MAXFILESIZE","10. Maximum upload size:");
define("_MI_SB_MAXFILESIZEDSC","Sets the maximum file size allowed when uploading files. Restricted to max upload permitted on the server.");

define("_MI_SB_DATEFORMAT","11. Date format:");
define("_MI_SB_DATEFORMATDSC","Sets the display date format for articles.");

define("_MI_SB_ALLOWCOMMENTS","12. Control comments at the story level:");
define("_MI_SB_ALLOWCOMMENTSDSC","If you set this option to 'Yes', you'll see comments only on those articles that have their comment checkbox marked in the admin form. <br /><br />Select 'No' to have comments managed at the global level (look below under the tag 'Comment rules'.");

define("_MI_SB_MOREARTS","13. Articles in author&#8217s side-box:");
define("_MI_SB_MOREARTSDSC","Specify the number of articles to display in the lateral box.");

define("_MI_SB_COLSINMENU","14. Include columns in menu?:");
define("_MI_SB_COLSINMENUDSC","If you set this option to 'Yes', authorized users will be able to see the columns names in their module menu. This is obviously not recommended for sites with MANY columns.");

define("_MI_SB_COLSPERINDEX","15. How many column teasers do you want to see in each index page?:");
define("_MI_SB_COLSPERINDEXDSC","How many columns should appear per index page? [Default = 3]");

define("_MI_SB_ALLOWRATING","16. Would you like to include the rating option in articles?:");
define("_MI_SB_ALLOWRATINGDSC","If set to yes, articles will show a rating ratio button battery to allow users to rate the article? [Default = Yes]");

define("_MI_SB_INTROTIT","17. Introduction headline:");
define("_MI_SB_INTROTITDSC","Text of the index page headline.");
define("_MI_SB_INTROTITDFLT","Welcome to this space");

define("_MI_SB_INTROTEXT","18. Introduction text:");
define("_MI_SB_INTROTEXTDSC","Text of the index page's introductory message.");
define("_MI_SB_INTROTEXTDFLT","In this area of the site you will find our catalogue of editorial columns, as well as the latest article from each of our authors. Click on a column's name to see all the articles associated to that column, or on an article's name to read directly the article. Depending on privileges, you can rate each article, select notification options or leave your comments.");

define("_MI_SB_BUTTSTXT","19. Visible create buttons:");
define("_MI_SB_BUTTSTXTDSC","If set to 'Yes', tables in the index page of the admin side will show a 'Create' button. Default value: 'No'.");

define("_MI_SB_WARNING","This module comes as is, without any guarantees whatsoever. Although this module is not beta, it is still under active development. This release can be used in a live website or a production environment, but its use is under your own responsibilityi, which means the author is not responsible.");
define("_MI_SB_AUTHORMSG","Soapbox is my first XOOPS module and as such contains all the errors of someone just beginning in the world of PHP and such things. My first steps in this world I gave them with the help of Catzwolf, now retired from the XOOPS world, and Soapbox owes him a lot, including both good pointers and mistakes.<br /><br />The history of Soapbox is told in more detail in the documentation, but I must thank here the valuable help of many XOOPS users, including herko, w4z004, marcan, ackbarr, Mithrandir, Predator and many more.<br /><br />Of course, I also take into account the help of those that have criticized, praised or commented the module, those who have looked in it more than it's designed to do, and in general to all those that have accepted the risk and installed the module in their sites for their enyojment. To you all, my friends, many thanks.");

// Names of admin menu items
define("_MI_SB_ADMENU1","Manager");
define("_MI_SB_ADMENU2","New Column");
define("_MI_SB_ADMENU3","New Article");
define("_MI_SB_ADMENU4","Permissions");
define("_MI_SB_ADMENU5","Blocks");
define("_MI_SB_ADMENU6","Go to module");

//Names of Blocks and Block information
define("_MI_SB_ARTSRATED","Best rated articles");
define("_MI_SB_ARTSRATED_DSC","Shows best rated articles");
define("_MI_SB_ARTSNEW","Recent articles");
define("_MI_SB_ARTSNEW_DSC","Shows recent articles");
define("_MI_SB_ARTSTOP","Most read articles");
define("_MI_SB_ARTSTOP_DSC","Shows most read articles");
define("_MI_SB_ARTSPOTLIGHT","Spotlight articles [single column]");
define("_MI_SB_ARTSPOTLIGHT_DSC","Shows spotlight articles [single column]");
define("_MI_SB_ARTSPOTLIGHT2","Spotlight articles [multicolumn]");
define("_MI_SB_ARTSPOTLIGHT2_DSC","Shows spotlight articles [multicolumn]");

// Defines for the About page
define("_MI_SB_AUTHOR_INFO","Author's information");
define("_MI_SB_AUTHOR_WEBSITE","Author's website");
define("_MI_SB_AUTHOR_EMAIL","Author's E-mail");
define("_MI_SB_AUTHOR_CREDITS","Module credits");
define("_MI_SB_MODULE_INFO","General module information");
define("_MI_SB_MODULE_STATUS","Version");
define("_MI_SB_MODULE_DEMO","Demo site");
define("_MI_SB_MODULE_SUPPORT","Support site");
define("_MI_SB_MODULE_BUG","Submit a bug");
define("_MI_SB_MODULE_FEATURE","Request a feature");
define("_MI_SB_MODULE_DISCLAIMER","Disclaimer");
define("_MI_SB_AUTHOR_WORD","Author's comment");

// Text for notifications
define("_MI_SB_GLOBAL_NOTIFY","Global");
define("_MI_SB_GLOBAL_NOTIFYDSC","Global notification options.");

define("_MI_SB_COLUMN_NOTIFY","Column");
define("_MI_SB_COLUMN_NOTIFYDSC","Notification options that apply to the current column.");

define("_MI_SB_ARTICLE_NOTIFY","Article");
define("_MI_SB_ARTICLE_NOTIFYDSC","Notification options that apply to the current article.");

define("_MI_SB_GLOBAL_NEWCOLUMN_NOTIFY","New column");
define("_MI_SB_GLOBAL_NEWCOLUMN_NOTIFYCAP","Notify me when a new column is created.");
define("_MI_SB_GLOBAL_NEWCOLUMN_NOTIFYDSC","Receive notification when a new column is created.");
define("_MI_SB_GLOBAL_NEWCOLUMN_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : New column");

define("_MI_SB_GLOBAL_ARTICLEMODIFY_NOTIFY","Modify article requested");
define("_MI_SB_GLOBAL_ARTICLEMODIFY_NOTIFYCAP","Notify me of any article modification request.");
define("_MI_SB_GLOBAL_ARTICLEMODIFY_NOTIFYDSC","Receive notification when any article modification request is submitted.");
define("_MI_SB_GLOBAL_ARTICLEMODIFY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : Article modification requested");

define("_MI_SB_GLOBAL_ARTICLEBROKEN_NOTIFY","Broken article submitted");
define("_MI_SB_GLOBAL_ARTICLEBROKEN_NOTIFYCAP","Notify me of any broken article report.");
define("_MI_SB_GLOBAL_ARTICLEBROKEN_NOTIFYDSC","Receive notification when any broken article report is submitted.");
define("_MI_SB_GLOBAL_ARTICLEBROKEN_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : Broken article reported");

define("_MI_SB_GLOBAL_ARTICLESUBMIT_NOTIFY","Article submitted");
define("_MI_SB_GLOBAL_ARTICLESUBMIT_NOTIFYCAP","Notify me when any new article is submitted and is awaiting approval.");
define("_MI_SB_GLOBAL_ARTICLESUBMIT_NOTIFYDSC","Receive notification when any new article is submitted and is waiting approval.");
define("_MI_SB_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : New article submitted");

define("_MI_SB_GLOBAL_NEWARTICLE_NOTIFY","New article");
define("_MI_SB_GLOBAL_NEWARTICLE_NOTIFYCAP","Notify me when any new article is published.");
define("_MI_SB_GLOBAL_NEWARTICLE_NOTIFYDSC","Receive notification when any new article is published.");
define("_MI_SB_GLOBAL_NEWARTICLE_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : New article");

define("_MI_SB_COLUMN_ARTICLESUBMIT_NOTIFY","Article submitted");
define("_MI_SB_COLUMN_ARTICLESUBMIT_NOTIFYCAP","Notify me when a new article is submitted and waiting approval to the current column.");   
define("_MI_SB_COLUMN_ARTICLESUBMIT_NOTIFYDSC","Receive notification when a new article is submitted and waiting approval in the current column.");      
define("_MI_SB_COLUMN_ARTICLESUBMIT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted in column"); 

define("_MI_SB_COLUMN_NEWARTICLE_NOTIFY","New article");
define("_MI_SB_COLUMN_NEWARTICLE_NOTIFYCAP","Notify me when a new article is posted in the current column.");   
define("_MI_SB_COLUMN_NEWARTICLE_NOTIFYDSC","Receive notification when a new article is posted in the current column.");      
define("_MI_SB_COLUMN_NEWARTICLE_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : New article in column"); 

define("_MI_SB_ARTICLE_APPROVE_NOTIFY","Article approved");
define("_MI_SB_ARTICLE_APPROVE_NOTIFYCAP","Notify me when this article is approved.");
define("_MI_SB_ARTICLE_APPROVE_NOTIFYDSC","Receive notification when this article is approved.");
define("_MI_SB_ARTICLE_APPROVE_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} auto-notify : Article approved");

define("_MI_SB_ALLOWEDSUBMITGROUPS","Which groups can submit?");
define("_MI_SB_ALLOWEDSUBMITGROUPSDSC","User groups that can submit articles.");

//HACK by domifara
define("_MI_SB_FORM_OPTIONS","Form Options");
define("_MI_SB_FORM_OPTIONS_DESC","Select the editor to use. If you have a 'simple' install (e.g you use only xoops core editor class, provided in the standard xoops core package), then you can just select DHTML");
define("_MI_SB_FORM_COMPACT","Compact");
define("_MI_SB_FORM_DHTML","DHTML");
define("_MI_SB_FORM_SPAW","Spaw Editor");
define("_MI_SB_FORM_HTMLAREA","HtmlArea Editor");
define("_MI_SB_FORM_FCK","FCK Editor");
define("_MI_SB_FORM_KOIVI","Koivi Editor");
define("_MI_SB_FORM_TINYMCE","TinyMCE Editor");

// 1.06
define("_MI_SB_SUBMITS", 'Submissions');
define("_MI_SB_ADD_ARTICLE", 'Add Article');
define("_MI_SB_ADD_COLUMN", 'Add Column');


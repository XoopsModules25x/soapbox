<?php
/**
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

// Module Info
// The name of this module
global $xoopsModule;
define('_MI_SOAPBOX_NAME', 'Soapbox');

// A brief description of this module
define('_MI_SOAPBOX_DESC', 'OpEd for your site');

// Sub menus in main menu block
define('_MI_SOAPBOX_SUB_SMNAME1', 'Submit an article');

// A brief description of this module
define('_MI_SOAPBOX_ALLOWSUBMIT', '1. User submissions:');
define('_MI_SOAPBOX_ALLOWSUBMITDSC', 'Allow users to submit articles to your website?');

define('_MI_SOAPBOX_AUTOAPPROVE', '2. Auto approve articles:');
define('_MI_SOAPBOX_AUTOAPPROVEDSC', 'Auto approves submitted articles without admin intervention.');

define('_MI_SOAPBOX_ALLOWADMINHITS', '3. Admin counter reads:');
define('_MI_SOAPBOX_ALLOWADMINHITSDSC', 'Allow admin hits for counter stats?');

define('_MI_SOAPBOX_PERPAGE', '4. Maximum articles per page (Admin side):');
define('_MI_SOAPBOX_PERPAGEDSC', 'Maximum number of articles per page to be displayed at once in Articles Admin.');

define('_MI_SOAPBOX_PERPAGEINDEX', '5. Maximum articles per page (User side):');
define('_MI_SOAPBOX_PERPAGEINDEXDSC', 'Maximum number of articles per page to be displayed at once in the user side of the module.');

define('_MI_SOAPBOX_IMGDIR', '6. Image base directory:');
define('_MI_SOAPBOX_IMGDIRDSC', 'This is the directory that holds the operational images. (No trailing \'/\')');

define('_MI_SOAPBOX_UPLOADDIR', '7. Image upload directory:');
define('_MI_SOAPBOX_UPLOADDIRDSC', 'This is the directory where columnists\' and articles\' pics will be stored. (No trailing \'/\')');

define('_MI_SOAPBOX_IMGWIDTH', '8. Maximum image width:');
define('_MI_SOAPBOX_IMGWIDTHDSC', 'Sets the maximum allowed width of an image when uploading.');

define('_MI_SOAPBOX_IMGHEIGHT', '9. Maximum image height:');
define('_MI_SOAPBOX_IMGHEIGHTDSC', 'Sets the maximum allowed height of an image when uploading.');

define('_MI_SOAPBOX_MAXFILESIZE', '10. Maximum upload size:');
define('_MI_SOAPBOX_MAXFILESIZEDSC', 'Sets the maximum file size allowed when uploading files. Restricted to max upload permitted on the server.');

define('_MI_SOAPBOX_DATEFORMAT', '11. Date format:');
define('_MI_SOAPBOX_DATEFORMATDSC', 'Sets the display date format for articles.');

define('_MI_SOAPBOX_ALLOWCOMMENTS', '12. Control comments at the story level:');
define('_MI_SOAPBOX_ALLOWCOMMENTSDSC', 'If you set this option to \'Yes\', you\'ll see comments only on those articles that have their comment checkbox marked in the admin form. <br><br>Select \'No\' to have comments managed at the global level (look below under the tag \'Comment rules\'.');

define('_MI_SOAPBOX_MOREARTS', '13. Articles in author&#8217s side-box:');
define('_MI_SOAPBOX_MOREARTSDSC', 'Specify the number of articles to display in the lateral box.');

define('_MI_SOAPBOX_COLSINMENU', '14. Include columns in menu?:');
define('_MI_SOAPBOX_COLSINMENUDSC', 'If you set this option to \'Yes\', authorized users will be able to see the columns names in their module menu. This is obviously not recommended for sites with MANY columns.');

define('_MI_SOAPBOX_COLSPERINDEX', '15. How many column teasers do you want to see in each index page?:');
define('_MI_SOAPBOX_COLSPERINDEXDSC', 'How many columns should appear per index page? [Default = 3]');

define('_MI_SOAPBOX_ALLOWRATING', '16. Would you like to include the rating option in articles?:');
define('_MI_SOAPBOX_ALLOWRATINGDSC', 'If set to yes, articles will show a rating ratio button battery to allow users to rate the article? [Default = Yes]');

define('_MI_SOAPBOX_INTROTIT', '17. Introduction headline:');
define('_MI_SOAPBOX_INTROTITDSC', 'Text of the index page headline.');
define('_MI_SOAPBOX_INTROTITDFLT', 'Welcome to this space');

define('_MI_SOAPBOX_INTROTEXT', '18. Introduction text:');
define('_MI_SOAPBOX_INTROTEXTDSC', 'Text of the index page\'s introductory message.');
define('_MI_SOAPBOX_INTROTEXTDFLT',
       'In this area of the site you will find our catalogue of editorial columns, as well as the latest article from each of our authors. Click on a column\'s name to see all the articles associated to that column, or on an article\'s name to read directly the article. Depending on privileges, you can rate each article, select notification options or leave your comments.');

define('_MI_SOAPBOX_BUTTSTXT', '19. Visible create buttons:');
define('_MI_SOAPBOX_BUTTSTXTDSC', 'If set to \'Yes\', tables in the index page of the admin side will show a \'Create\' button. Default value: \'No\'.');

define('_MI_SOAPBOX_WARNING',
       'This module comes as is, without any guarantees whatsoever. Although this module is not beta, it is still under active development. This release can be used in a live website or a production environment, but its use is under your own responsibilityi, which means the author is not responsible.');
define('_MI_SOAPBOX_AUTHORMSG',
       'Soapbox is my first XOOPS module and as such contains all the errors of someone just beginning in the world of PHP and such things. My first steps in this world I gave them with the help of Catzwolf, now retired from the XOOPS world, and Soapbox owes him a lot, including both good pointers and mistakes.<br><br>The history of Soapbox is told in more detail in the documentation, but I must thank here the valuable help of many XOOPS users, including herko, w4z004, marcan, ackbarr, Mithrandir, Predator and many more.<br><br>Of course, I also take into account the help of those that have criticized, praised or commented the module, those who have looked in it more than it\'s designed to do, and in general to all those that have accepted the risk and installed the module in their sites for their enyojment. To you all, my friends, many thanks.');

// Names of admin menu items
define('_MI_SOAPBOX_ADMENU1', 'Manager');
define('_MI_SOAPBOX_ADMENU2', 'New Column');
define('_MI_SOAPBOX_ADMENU3', 'New Article');
define('_MI_SOAPBOX_ADMENU4', 'Permissions');
define('_MI_SOAPBOX_ADMENU5', 'Blocks');
define('_MI_SOAPBOX_ADMENU6', 'Go to module');

//Names of Blocks and Block information
define('_MI_SOAPBOX_ARTSRATED', 'Best rated articles');
define('_MI_SOAPBOX_ARTSRATED_DSC', 'Shows best rated articles');
define('_MI_SOAPBOX_ARTSNEW', 'Recent articles');
define('_MI_SOAPBOX_ARTSNEW_DSC', 'Shows recent articles');
define('_MI_SOAPBOX_ARTSTOP', 'Most read articles');
define('_MI_SOAPBOX_ARTSTOP_DSC', 'Shows most read articles');
define('_MI_SOAPBOX_ARTSPOTLIGHT', 'Spotlight articles [single column]');
define('_MI_SOAPBOX_ARTSPOTLIGHT_DSC', 'Shows spotlight articles [single column]');
define('_MI_SOAPBOX_ARTSPOTLIGHT2', 'Spotlight articles [multicolumn]');
define('_MI_SOAPBOX_ARTSPOTLIGHT2_DSC', 'Shows spotlight articles [multicolumn]');

// Defines for the About page
define('_MI_SOAPBOX_AUTHOR_INFO', 'Author\'s information');
define('_MI_SOAPBOX_AUTHOR_WEBSITE', 'Author\'s website');
define('_MI_SOAPBOX_AUTHOR_EMAIL', 'Author\'s E-mail');
define('_MI_SOAPBOX_AUTHOR_CREDITS', 'Module credits');
define('_MI_SOAPBOX_MODULE_INFO', 'General module information');
define('_MI_SOAPBOX_MODULE_STATUS', 'Version');
define('_MI_SOAPBOX_MODULE_DEMO', 'Demo site');
define('_MI_SOAPBOX_MODULE_SUPPORT', 'Support site');
define('_MI_SOAPBOX_MODULE_BUG', 'Submit a bug');
define('_MI_SOAPBOX_MODULE_FEATURE', 'Request a feature');
define('_MI_SOAPBOX_MODULE_DISCLAIMER', 'Disclaimer');
define('_MI_SOAPBOX_AUTHOR_WORD', 'Author\'s comment');

// Text for notifications
define('_MI_SOAPBOX_GLOBAL_NOTIFY', 'Global');
define('_MI_SOAPBOX_GLOBAL_NOTIFYDSC', 'Global notification options.');

define('_MI_SOAPBOX_COLUMN_NOTIFY', 'Column');
define('_MI_SOAPBOX_COLUMN_NOTIFYDSC', 'Notification options that apply to the current column.');

define('_MI_SOAPBOX_ARTICLE_NOTIFY', 'Article');
define('_MI_SOAPBOX_ARTICLE_NOTIFYDSC', 'Notification options that apply to the current article.');

define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFY', 'New column');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYCAP', 'Notify me when a new column is created.');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYDSC', 'Receive notification when a new column is created.');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New column');

define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFY', 'Modify article requested');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYCAP', 'Notify me of any article modification request.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYDSC', 'Receive notification when any article modification request is submitted.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Article modification requested');

define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFY', 'Broken article submitted');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYCAP', 'Notify me of any broken article report.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYDSC', 'Receive notification when any broken article report is submitted.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken article reported');

define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFY', 'Article submitted');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYCAP', 'Notify me when any new article is submitted and is awaiting approval.');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYDSC', 'Receive notification when any new article is submitted and is waiting approval.');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article submitted');

define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFY', 'New article');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYCAP', 'Notify me when any new article is published.');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYDSC', 'Receive notification when any new article is published.');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article');

define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFY', 'Article submitted');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYCAP', 'Notify me when a new article is submitted and waiting approval to the current column.');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYDSC', 'Receive notification when a new article is submitted and waiting approval in the current column.');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted in column');

define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFY', 'New article');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYCAP', 'Notify me when a new article is posted in the current column.');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYDSC', 'Receive notification when a new article is posted in the current column.');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article in column');

define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFY', 'Article approved');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYCAP', 'Notify me when this article is approved.');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYDSC', 'Receive notification when this article is approved.');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Article approved');

define('_MI_SOAPBOX_ALLOWEDSUBMITGROUPS', 'Which groups can submit?');
define('_MI_SOAPBOX_ALLOWEDSUBMITGROUPSDSC', 'User groups that can submit articles.');

//Editors
define('MI_SOAPBOX_EDITOR_ADMIN', 'Editor: Admin');
define('MI_SOAPBOX_EDITOR_ADMIN_DESC', 'Select the Editor to use by the Admin');
define('MI_SOAPBOX_EDITOR_USER', 'Editor: User');
define('MI_SOAPBOX_EDITOR_USER_DESC', 'Select the Editor to use by the User'); //define('_MI_SOAPBOX_FORM_COMPACT', 'Compact');
//define('_MI_SOAPBOX_FORM_DHTML', 'DHTML');
//define('_MI_SOAPBOX_FORM_SPAW', 'Spaw Editor');
//define('_MI_SOAPBOX_FORM_HTMLAREA', 'HtmlArea Editor');
//define('_MI_SOAPBOX_FORM_FCK', 'FCK Editor');
//define('_MI_SOAPBOX_FORM_KOIVI', 'Koivi Editor');
//define('_MI_SOAPBOX_FORM_TINYMCE', 'TinyMCE Editor');

// 1.06
define('_MI_SOAPBOX_SUBMITS', 'Submissions');
define('_MI_SOAPBOX_ADD_ARTICLE', 'Add Article');
define('_MI_SOAPBOX_ADD_COLUMN', 'Add Column');

//Help
define('_MI_SOAPBOX_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_SOAPBOX_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_SOAPBOX_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_SOAPBOX_OVERVIEW', 'Overview');

//define('_MI_SOAPBOX_HELP_DIR', __DIR__);

//help multi-page
define('_MI_SOAPBOX_DISCLAIMER', 'Disclaimer');
define('_MI_SOAPBOX_LICENSE', 'License');
define('_MI_SOAPBOX_SUPPORT', 'Support');

//Tag
define('_MI_SOAPBOX_USETAG', 'Use tags?');
define('_MI_SOAPBOX_USETAGDSC', 'Tags module required \"TAG\"');

define('_MI_SOAPBOX_HOME', 'Home');
define('_MI_SOAPBOX_ABOUT', 'About');

define('_MI_SOAPBOX_SHOW_SAMPLE_BUTTON', 'Show Sample Button?');
define('_MI_SOAPBOX_SHOW_SAMPLE_BUTTON_DESC', 'If yes, the "Add Sample Data" button will be visible to the Admin. It is Yes as a default for first installation.');
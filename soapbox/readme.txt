/**
 * $Id: article.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar & carnuke
 * Licence: GNU
 */

------------------------
Thanks for your patience
------------------------
First of all, I'd like to thank all xoopsers for your patience with Soapbox. This module has taught me many, many things, and has given me many, many headaches, but I can truly say my knowledge of PHP and MySQL has grown giant steps in the past couple of months. My respect for XOOPS original developer, Kazumi Ono, has also grown a lot. This is a very smart and very powerful piece of software.


------------------
About this release
------------------
This is version 1.5 of Soapbox. Actually, this is NOT the full version, but a stripped down one. It includes all the necessary files to run the module, but for the sake of keeping the size small, it has left out:

+ The language files, which will be available in a separate download once the debugging is complete.
+ The help files, which I still have to update and which will also be available in a separate download in different formats.
For a few days, this will be available as a release candidate, while xoopsers look for bugs and I work on the docs. If you find bugs, please report them to hsalazar@xoops.org. I'll try to fix them if possible.


I've evaluated the inclusion of some other functions, as well as the possible implementation of several suggestions. Her's a couple of comments about them:

+ Most suggestions indicate that xoopsers want to use Soapbox as a general article management module, which is not its intended use. That's why they keep asking for subcategories, several authors per column, expiration dates and such. Soapbox was designed to be used as an editorial column module, with columns being defined as single-author contributions to a site.
+ I've tried to include an internal search function, but decided against it for two reasons: first, XOOPS internal function is good enough for most purposes; second, haven't found a way to paginate results when there are many.
+ I built a version including SPAW as WYSIWYG editor, but decided against it because of the size. Then, I learned some of XOOPS' brightest are working on a cross-browser WYSIWYG editor and decided to wait for their implementation before including such a feature in Soapbox.

Changes for this release:

+ Tested more carefully the blocks, to make sure they don't cause more problems. It might still have surprises, though.
+ Optimized the code updating references to server variables and other stuff.
+ Used Mithrandir's code to optimize the menu in the admin side.
+ Included my own version of Predator's addition: a breadcrumb for the admin side, with a few style tweaks. For next release, will copy this idea to implement it in the user side.
+ Included a function to eliminate image tags from blocks, so now you'll only have images in the articles themselves, not in the teasers, blocks or any other calls.
+ Modified the constant strings so that the module configuration options are more easily read.
+ Included several suggestions by domifara.
+ Corrected the variables needed to be able to change the module's name in the admin section.
+ Added another block: Soapbox spotlight.
+ Included new versions of the admin icons.
+ Modified the admin section to invoke tables using functions.
+ In the admin side, the columns page now includes the list of columns.
+ In the admin side, the articles page now includes the list of articles.
+ In the admin side, changed a bit the behavior of the submissions page.

And a bunch more...

-------------
Introduction:
-------------
Thanks for giving Soapbox an opportunity to serve you. This little module has a single purpose: to help you build in your site an editorial column section, where you can manage the columns and articles published in an ordered fashion.

This module was built using XOOPS 2.0.6 as test bed. It's based on Catzwolf's module called WF-FAQ, with what we could say a lot of changes. But it does have its main features: uses Smarty templates, can take comments, it's fully searchable, includes notification options, and lets users rate the articles as well as send private messages to the authors. Also, you can decide which groups of users see what specific columns.

This document is short because, to be honest, Soapbox includes a LARGE help file. In fact, most of the module's weight is due to the inclusion of this help file, which can be accessed through a button in the admin side. If you want a leaner module, just leave the help folder out of your upload and keep it handy to have local access to the help. Just remember that yuo won't have it available while online.

--------------
How to install
--------------
Soapbox is installed as a regular XOOPS module, which means you should copy the complete /soapbox folder into the /modules directory of your website. Then log in to your site as administrator, go to System Admin > Modules, look for the Soapbox icon in the list of uninstalled modules and click in the install icon. Follow the directions in the screen and you'll be ready to go.

For a complete description of the module and its features, you can use the included help file. Access it directly in yoursite/modules/soapbox/help/soapbox.help or else use the 'Help' button in the admin side of the module. If you prefer NOT to include the help file on your server, simply open the directory "Help" on your local machine and open "soapbox.html" in your browser.

--------
Feedback
--------
As every other man, I'd like to know if this module is useful to you, if it has bugs, if it can be in any way improved. In any of these circumstances, don't hesitate: mail me at hsalazar@xoops.org and I'll be listening to good suggestions and positive critics..

-------
History
-------
18 February 2004 v1.00
First public release. 

21 February 2004 v1.10
First wave of changes
+ DONE: Images are separated in two directories, so it's feasible to define a different uploads directory but still have the module's operational images available. Thanks, svaha.
+ DONE: If for some reason a column's author has no real name on his/her profile, the module will use instead the username. Thanks, csloh.
+ DONE: Included as default the apostrophes so the MySQL script works for everyone. Thanks, johnwyp and xgian.
+ DONE: Corrected a style reference in the the navigational bars. Thanks, studioC.
+ DONE: Deleted variable that stopped search from working. Now it's functioning ok. Thanks, loukaum.
+ DONE: Pruned more text strings from the code, adding them to language constants files. Thanks, blueangel.
+ DONE: Re-checked the submission form from all user scenarios. Works ok for authors and webmasters. Couldn'r replicate the bug that shows only the introduction. Thanks, krayc.
+ DONE: Changed the easy print function so it translates HTML and XCodes appropriately. Thanks, tjnemez.
+ DONE: Added the possibility of using the [pagebreak] tag to create multipage articles. Thanks, meme.
+ DONE: Renamed the article retrieval variables, so the module won't crash when used together with any of Catzwolf's modules. Thanks, JackJ.
+ DONE: Made the title in the user side independent from the module's name so the admin can keep the module's directory but display a different name in the site. Thanks, JackJ.

Credits and thanks are included in the help file. However, I'd like to add a very special thanks to Richard Strauss (carnuke) for going well beyond the call of duty in the testing phase of this module. Without his keen eye, this module would be quite buggy and incomplete. In a very literal sense, he has co-authored this module. Thanks, Richard!!!

26 February 2004 v1.20
More bugfixes and small changes

16 March 2004 v1.30
+ Supposedly I repaired the blocks stuff. The idea is this: you, as user, only see those blocks configured in blocks admin for you to see. And what do you see in those blocks? Well, only those articles belonging to columns you're authorized to see. I've checked and rechecked and in my installation it works. Does it work for you?
+ Rebuilt the module so that now the styles are in an external file and are thus easier to change. This is definitely not streamlined, but should make things a bit easier should you want to change the look of the module.
+ Added more options in the user side: icons in the index and column files to "print" and "send to friend". The admin sees also two more icons to "edit" and "delete".
+ Fixed a problem with the autoteaser.
+ In the admin side, added files to manage the module's blocks without leaving the module's environment. These files were contributed (unknowingly) by G. I. Joe.
+ Please check if it still crashes the browser often. It doesn't happen in my installation, but it might in yours...
+ Fixed a bug in the search function so it works ok.
+ I believe I have finally gotten all language constants out of the code (but there could be still some lurkinf there).
+ Corrected a date bug in the "More articles by..." box.
+ Corrected (mostly, I think) the inclusion of images in the DHTML boxes.
+ Corrected (mostly, I think) the inclusion of apostrophes in text boxes.
+ Added the validation of language.
And a bunch more...

17 March 2004 v1.40
+ Fixed problems that produced blank page on block activation.

23 August 2004 v1.50
This release.

---
END
---

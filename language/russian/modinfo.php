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
define('_MI_SOAPBOX_NAME', 'Блог');

// A brief description of this module
define('_MI_SOAPBOX_DESC', 'OpEd для Вашего сайта');

// Sub menus in main menu block
define('_MI_SOAPBOX_SUB_SMNAME1', 'Добавить статью');

// A brief description of this module
define('_MI_SOAPBOX_ALLOWSUBMIT', '1. Публикации пользователя:');
define('_MI_SOAPBOX_ALLOWSUBMITDSC', 'Разрешить пользователям присылать свои статьи на Ваш сайт?');

define('_MI_SOAPBOX_AUTOAPPROVE', '2. Авто утверждение статей:');
define('_MI_SOAPBOX_AUTOAPPROVEDSC', 'Авто одобрение представленных статей без вмешательства администратора.');

define('_MI_SOAPBOX_ALLOWADMINHITS', '3. Учитывать посещения админа:');
define('_MI_SOAPBOX_ALLOWADMINHITSDSC', 'Учитывать посещения админа на страницы модуля?');

define('_MI_SOAPBOX_PERPAGE', '4. Максимальное кол-во статей на странице (со стороны администратора):');
define('_MI_SOAPBOX_PERPAGEDSC', 'Максимальное количество статей которое будет паказано на странице администрирования');

define('_MI_SOAPBOX_PERPAGEINDEX', '5. Максимальное кол-во статей на странице (со стороны пользователя):');
define('_MI_SOAPBOX_PERPAGEINDEXDSC', 'Максимальное количество статей которое будет паказано на странице модуля');

define('_MI_SOAPBOX_IMGDIR', '6. Каталог базы изображений:');
define('_MI_SOAPBOX_IMGDIRDSC', 'Это каталог, который содержит изображения. (не ставить \'/\')');

define('_MI_SOAPBOX_UPLOADDIR', '7. Каталог загрузки изображений:');
define('_MI_SOAPBOX_UPLOADDIRDSC', 'Это каталог, в котором будут храниться изображения колонок и статей. (не ставить \'/\')');

define('_MI_SOAPBOX_IMGWIDTH', '8. Максимальная ширина изображения:');
define('_MI_SOAPBOX_IMGWIDTHDSC', 'Устанавливает максимально допустимую ширину изображения при загрузке.');

define('_MI_SOAPBOX_IMGHEIGHT', '9. Максимальная высота изображения:');
define('_MI_SOAPBOX_IMGHEIGHTDSC', 'Устанавливает максимально допустимую высоту изображения при загрузке.');

define('_MI_SOAPBOX_MAXFILESIZE', '10. Максимальный размер загружаемого файла:');
define('_MI_SOAPBOX_MAXFILESIZEDSC', 'Устанавливает максимальный размер файла при загрузке файлов. Ограничено разрешенной на сервере.');

define('_MI_SOAPBOX_DATEFORMAT', '11. Формат даты:');
define('_MI_SOAPBOX_DATEFORMATDSC', 'Устанавливает формат даты для статей.');

define('_MI_SOAPBOX_ALLOWCOMMENTS', '12. Контроль комментарии на уровне сюжета:');
define('_MI_SOAPBOX_ALLOWCOMMENTSDSC',
       'Если установить этот параметр "Да", Вы увидите комментарии только тех статей, которые имеют комментарии отмеченые флажком. <br><br>Выберите "Нет", чтобы получать комментарии, управляемые на глобальном уровне (смотрите ниже под тегом \'Правила комментариев\'.');

define('_MI_SOAPBOX_MOREARTS', '13. Кол-во статей в боковом поле:');
define('_MI_SOAPBOX_MOREARTSDSC', 'Укажите количество статей для показа в боковом поле.');

define('_MI_SOAPBOX_COLSINMENU', '14. Включить колонки в меню?:');
define('_MI_SOAPBOX_COLSINMENUDSC', 'Если установить этот параметр, чтобы "Да", авторизованные пользователи смогут видеть имена колонок в меню модуля. Это не рекомендуется для сайтов с большим числом колонок.');

define('_MI_SOAPBOX_COLSPERINDEX', '15. Сколько тизеров колонок Вы хотите видеть на главной странице?:');
define('_MI_SOAPBOX_COLSPERINDEXDSC', 'Сколько столбцов должно появляться на главной странице? [По умолчанию = 3]');

define('_MI_SOAPBOX_ALLOWRATING', '16. Вы хотите включить опцию рейтинга в статьях?:');
define('_MI_SOAPBOX_ALLOWRATINGDSC', 'Если установлено значение да, статьи будет участвовать в рейтинге, чтобы пользователи могли оценить статью? [По умолчанию = Да]');

define('_MI_SOAPBOX_INTROTIT', '17. Введите заголовок:');
define('_MI_SOAPBOX_INTROTITDSC', 'Текст заголовка главной страницы.');
define('_MI_SOAPBOX_INTROTITDFLT', 'Добро пожаловать');

define('_MI_SOAPBOX_INTROTEXT', '18. Введение текст:');
define('_MI_SOAPBOX_INTROTEXTDSC', 'Текст на главной странице, вводное сообщение.');
define('_MI_SOAPBOX_INTROTEXTDFLT',
       'In this area of the site you will find our catalogue of editorial columns, as well as the latest article from each of our authors. Click on a column\'s name to see all the articles associated to that column, or on an article\'s name to read directly the article. Depending on privileges, you can rate each article, select notification options or leave your comments.');

define('_MI_SOAPBOX_BUTTSTXT', '19. Показать кнопки в меню Менеджер:');
define('_MI_SOAPBOX_BUTTSTXTDSC', 'Если установлено значение "Да", в таблице Менеджер со стороны администратора будут паказаны кнопки создания колонок и статей. Значение по умолчанию: "Нет".');

define('_MI_SOAPBOX_WARNING',
       'Этот модуль поставляется как есть, без каких-либо гарантий вообще. Хотя этот модуль не бета, это все еще в стадии активного развития. Этот релиз может быть использован в живом сайте или производственной среде, но его использование под Вашим собственным responsibilityi, что означает, что автор не несет ответственности.');
define('_MI_SOAPBOX_AUTHORMSG',
       'Soapbox is my first XOOPS module and as such contains all the errors of someone just beginning in the world of PHP and such things. My first steps in this world I gave them with the help of Catzwolf, now retired from the XOOPS world, and Soapbox owes him a lot, including both good pointers and mistakes.<br><br>The history of Soapbox is told in more detail in the documentation, but I must thank here the valuable help of many XOOPS users, including herko, w4z004, marcan, ackbarr, Mithrandir, Predator and many more.<br><br>Of course, I also take into account the help of those that have criticized, praised or commented the module, those who have looked in it more than it\'s designed to do, and in general to all those that have accepted the risk and installed the module in their sites for their enyojment. To you all, my friends, many thanks.');

// Names of admin menu items
define('_MI_SOAPBOX_ADMENU1', 'Менеджер');
define('_MI_SOAPBOX_ADMENU2', 'Новая колонка');
define('_MI_SOAPBOX_ADMENU3', 'Новая статья');
define('_MI_SOAPBOX_ADMENU4', 'Права доступа');
define('_MI_SOAPBOX_ADMENU5', 'Блоки');
define('_MI_SOAPBOX_ADMENU6', 'Перейти к модулю');

//Names of Blocks and Block information
define('_MI_SOAPBOX_ARTSRATED', 'Лучшие по рейтингу');
define('_MI_SOAPBOX_ARTSRATED_DSC', 'Показывает лучший рейтинг');
define('_MI_SOAPBOX_ARTSNEW', 'Последние статьи');
define('_MI_SOAPBOX_ARTSNEW_DSC', 'Показывать последние статьи');
define('_MI_SOAPBOX_ARTSTOP', 'Самые читаемые статьи');
define('_MI_SOAPBOX_ARTSTOP_DSC', 'Показывать наиболее читаемые статьи');
define('_MI_SOAPBOX_ARTSPOTLIGHT', 'Популярные статьи [одиночная колонка]');
define('_MI_SOAPBOX_ARTSPOTLIGHT_DSC', 'Показать популярные статьи [одиночная колонка]');
define('_MI_SOAPBOX_ARTSPOTLIGHT2', 'Популярные статьи [несколько колонок]');
define('_MI_SOAPBOX_ARTSPOTLIGHT2_DSC', 'Показать популярные статьи [несколько колонок]');

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
define('_MI_SOAPBOX_GLOBAL_NOTIFY', 'Глобальный');
define('_MI_SOAPBOX_GLOBAL_NOTIFYDSC', 'Глобальные параметры уведомлений.');

define('_MI_SOAPBOX_COLUMN_NOTIFY', 'Колонки');
define('_MI_SOAPBOX_COLUMN_NOTIFYDSC', 'Варианты уведомлений, которые относятся к текущей колонке.');

define('_MI_SOAPBOX_ARTICLE_NOTIFY', 'Статьи');
define('_MI_SOAPBOX_ARTICLE_NOTIFYDSC', 'Варианты уведомлений, которые относятся к данной статье.');

define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFY', 'Новая колонка');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYCAP', 'Сообщите мне, когда создается новая колонка.');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYDSC', 'Получать уведомление, когда создается новая колонка.');
define('_MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Новая колонка');

define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFY', 'Изменение запрошенной статьи');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYCAP', 'Сообщите мне о любой модификации статьи.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYDSC', 'Получать уведомление, когда статья будет модифицирована.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Статья модифицирована');

define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFY', 'Битые статьи');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYCAP', 'Сообщите мне о любых битых статьях.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYDSC', 'Получать уведомление, когда появляются битые статьи.');
define('_MI_SOAPBOX_GLOBAL_ARTICLEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Битая статья');

define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFY', 'Представлена статья');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYCAP', 'Сообщите мне, когда новая статья представлена ​​и ожидает утверждения.');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYDSC', 'Получать уведомление, когда представлена новая статья и ждет одобрения.');
define('_MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Представлена новая статья');

define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFY', 'Новая статья');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYCAP', 'Сообщите мне, когда новая статья опубликована.');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYDSC', 'Получать уведомление, когда новая статья опубликована.');
define('_MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Новая статья');

define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFY', 'Представлена статья');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYCAP', 'Сообщите мне, когда представлена новая статья и ждет одобрение в текущей колонке.');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYDSC', 'Получать уведомление, когда представлена новая статья и ждет одобрения в текущей колонке.');
define('_MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Представлена статья в текущей колонке');

define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFY', 'Новая статья');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYCAP', 'Сообщите мне, когда новая статья опубликована в текущей колонке.');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYDSC', 'Получать уведомление, когда новая статья опубликована в текущей колонке.');
define('_MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Новая статья в текущей колонке');

define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFY', 'Статья утверждена');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYCAP', 'Сообщите мне, когда эта статья будет одобрена.');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYDSC', 'Получать уведомление, когда эта статья одобрена.');
define('_MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} автоматически оповещать: Статья утверждена');

define('_MI_SOAPBOX_ALLOWEDSUBMITGROUPS', 'Какие группы могут представлять?');
define('_MI_SOAPBOX_ALLOWEDSUBMITGROUPSDSC', 'Группы пользователей, которые могут представить статьи.');

//HACK by domifara
define('_MI_SOAPBOX_FORM_OPTIONS', 'Параметры формы');
define('_MI_SOAPBOX_FORM_OPTIONS_DESC', 'Выберите редактор. Если есть стандартные (e.g использовать только XOOPS базовый редактор, который входит в основной пакет XOOPS), то Вы можете просто выбрать DHTML');
define('_MI_SOAPBOX_FORM_COMPACT', 'Compact');
define('_MI_SOAPBOX_FORM_DHTML', 'DHTML');
define('_MI_SOAPBOX_FORM_SPAW', 'Spaw Editor');
define('_MI_SOAPBOX_FORM_HTMLAREA', 'HtmlArea Editor');
define('_MI_SOAPBOX_FORM_FCK', 'FCK Editor');
define('_MI_SOAPBOX_FORM_KOIVI', 'Koivi Editor');
define('_MI_SOAPBOX_FORM_TINYMCE', 'TinyMCE Editor');

// 1.06
define('_MI_SOAPBOX_SUBMITS', 'На проверку');
define('_MI_SOAPBOX_ADD_ARTICLE', 'Добавить статью');
define('_MI_SOAPBOX_ADD_COLUMN', 'Добавить колонку');
//Help
define('_MI_SOAPBOX_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_SOAPBOX_HELP_HEADER', __DIR__ . '/help/helpheader.html');
define('_MI_SOAPBOX_BACK_2_ADMIN', 'Вернуться в администрацию ');
define('_MI_SOAPBOX_OVERVIEW', 'Обзор');

//define('_MI_SOAPBOX_HELP_DIR', __DIR__);

//help multi-page
define('_MI_SOAPBOX_DISCLAIMER', 'Disclaimer');
define('_MI_SOAPBOX_LICENSE', 'Лицензия');
define('_MI_SOAPBOX_SUPPORT', 'Поддержка');

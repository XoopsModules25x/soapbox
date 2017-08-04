<?php
/**
 * $Id: admin.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

define('_AM_SOAPBOX_ABOUT', 'About');
define('_AM_SOAPBOX_ACTION', 'Действие');
define('_AM_SOAPBOX_ADMINARTMNGMT', 'Управление статьями');
define('_AM_SOAPBOX_ADMINCOLMNGMT', 'Управление колонками');
define('_AM_SOAPBOX_ALLOWCOMMENTS', 'Может ли статья быть прокомментирована?');
define('_AM_SOAPBOX_ARTAUTHORIZED', 'Статья была разрешена.');
define('_AM_SOAPBOX_ARTBODY', 'Основной текст<span style=\'font-size: xx-small; font-weight: normal; display: block;\'>(Вы можете создавать многостраничные статьи, вставив тег [pagebreak] куда вы хотите поставить разрыв страницы.)</span>');
define('_AM_SOAPBOX_ARTCOLNAME', 'Имя колонки');
define('_AM_SOAPBOX_ARTCREATED', 'Создан');
define('_AM_SOAPBOX_ARTCREATEDOK', 'Статья была успешно создана!');
define('_AM_SOAPBOX_ARTHEADLINE', 'Заголовок');
define('_AM_SOAPBOX_ARTID', 'Id');
define('_AM_SOAPBOX_ARTISDELETED', 'Статья была удалена.');
define('_AM_SOAPBOX_ARTISOFF', 'Статья отключена');
define('_AM_SOAPBOX_ARTISON', 'Статья включена');
define('_AM_SOAPBOX_ARTISSUB', 'Статья еще не утверждена');
define('_AM_SOAPBOX_ARTLEAD', 'Первый абзац статьи<span style=\'font-size: xx-small; font-weight: normal; display: block;\'>(Первый абзац статьи.)</span>');
define('_AM_SOAPBOX_ARTMODIFIED', 'Статья успешно изменена!');
define('_AM_SOAPBOX_ARTNOTCREATED', 'Сожалею. Не удалось создать эту статью!');
define('_AM_SOAPBOX_ARTNOTUPDATED', 'Сожалею. Не удалось обновить статью!');
define('_AM_SOAPBOX_ARTS', 'Статьи');
define('_AM_SOAPBOX_ARTTEASER', 'Анотация к статье<span style=\'font-size: xx-small; font-weight: normal; display: block;\'>(Текст показывается только на главной странице.)</span>');
define('_AM_SOAPBOX_ARTSTEXT', 'В этом списке показаны опубликованные статьи, присутствующие в модуле, и онлайн и офлайн.');
define('_AM_SOAPBOX_AUTHART', 'Авторизовать представленные');
define('_AM_SOAPBOX_AUTHOR', 'Автор');
define('_AM_SOAPBOX_AUTHORIZE', 'Авторизовать');
define('_AM_SOAPBOX_AUTOTEASER', 'Создать анотацию автоматически?:');
define('_AM_SOAPBOX_AUTOTEASERAMOUNT', 'Кол-во символов авто-анотации:');
define('_AM_SOAPBOX_BACK2IDX', 'Отменено. Переадресация на главную страницу');
define('_AM_SOAPBOX_BLOCK', ' Добавить в статьях блоки?');
define('_AM_SOAPBOX_BLOCKS', 'Блоки');
define('_AM_SOAPBOX_BY', 'Автор: ');
define('_AM_SOAPBOX_BREAKS', ' Использовать разрыв страницы?');
define('_AM_SOAPBOX_CANCEL', 'Отмена');
define('_AM_SOAPBOX_CLEAR', 'Очистить');
define('_AM_SOAPBOX_COLCREATED', 'Новая колонка была создана и сохранена! Теперь установите разрешения.');
define('_AM_SOAPBOX_COLDESCRIPT', 'Описание колонки');
define('_AM_SOAPBOX_COLIMAGE', 'Изображение колонки');
define('_AM_SOAPBOX_COLIMAGEUPLOAD', ' Загрузить изображение колонки');
define('_AM_SOAPBOX_COLISDELETED', 'Колонка %s была удалена');
define('_AM_SOAPBOX_COLMODIFIED', 'Выбранная колонка была изменена и сохранена!');
define('_AM_SOAPBOX_COLNAME', 'Имя колонки');
define('_AM_SOAPBOX_COLPERMS', 'Права доступа колонки');
define('_AM_SOAPBOX_COLPOSIT', 'Позиция колонки');
define('_AM_SOAPBOX_COLS', 'Колонки');
define('_AM_SOAPBOX_COLSHEADER', 'Модуль администратора колонок');
define('_AM_SOAPBOX_COLSTEXT', 'В этом списке отображаются колонки, присутствующие в модуле.');
define('_AM_SOAPBOX_CREATE', 'Создать');
define('_AM_SOAPBOX_CREATEART', 'Создать статью');
define('_AM_SOAPBOX_CREATECOL', 'Создать колонку');
define('_AM_SOAPBOX_CREATINGART', ' > Создание новой статьи');
define('_AM_SOAPBOX_CREATINGCOL', ' > Создание новой колонки');
define('_AM_SOAPBOX_CREATEIN', 'Создать в категории:');
define('_AM_SOAPBOX_DELETE', 'Удалить');
define('_AM_SOAPBOX_DELETEART', 'Удалить статью');
define('_AM_SOAPBOX_DELETECOL', 'Удалить колонку');
define('_AM_SOAPBOX_DELETESUBM', 'Удалить представленные');
define('_AM_SOAPBOX_DELETETHISARTICLE', 'Удалить эту статью?');
define('_AM_SOAPBOX_DELETETHISCOL', 'Вы уверены, что хотите удалить эту колонку?');
define('_AM_SOAPBOX_DESCRIP', 'Описание колонки');
define('_AM_SOAPBOX_DOHTML', ' Включить HTML-теги');
define('_AM_SOAPBOX_DOSMILEY', ' Включить смайлики');
define('_AM_SOAPBOX_DOXCODE', ' Включить коды XOOPS');
define('_AM_SOAPBOX_EDITART', 'Редактировать статью');
define('_AM_SOAPBOX_EDITCOL', 'Изменить колонку');
define('_AM_SOAPBOX_EDITING', ' > Редактирование \'');
define('_AM_SOAPBOX_EDITSUBM', 'Изменить представленные');
define('_AM_SOAPBOX_FILEEXISTS', 'Файл с таким именем уже существует на сервере. Пожалуйста, выберите другой!');
define('_AM_SOAPBOX_GOMOD', 'Перейти к модулю');
define('_AM_SOAPBOX_HELP', 'Помощь');
define('_AM_SOAPBOX_HILITE', 'Выделить блоки');
define('_AM_SOAPBOX_ID', 'Id');
define('_AM_SOAPBOX_IDXINTRO', 'Cегодня, %s, содержание этого модуля выглядит следующим образом:');
define('_AM_SOAPBOX_INDEX', 'Главная');
define('_AM_SOAPBOX_INVENTORY', 'Пункты резюме');
define('_AM_SOAPBOX_MODADMIN', ' Модуль администратора: ');
define('_AM_SOAPBOX_MODART', 'Изменить статью');
define('_AM_SOAPBOX_MODCOL', 'Изменить существующий столбец');
define('_AM_SOAPBOX_MODCONTENT', 'Содержание модуля');
define('_AM_SOAPBOX_MODIFY', 'Изменить');
define('_AM_SOAPBOX_MODIFYCOL', 'Изменить колонку');
define('_AM_SOAPBOX_MODIFYTHISCOL', 'Изменить эту колонку?');
define('_AM_SOAPBOX_MODULEHEAD', 'Статьи и колонки');
define('_AM_SOAPBOX_NEEDONECOLUMN', 'Чтобы создать статью, Вы должны сначала создать колонку.');
define('_AM_SOAPBOX_NEWART', 'Создать новую статью');
define('_AM_SOAPBOX_NEWCOL', 'Создать колонку');
define('_AM_SOAPBOX_NO', 'Нет');
define('_AM_SOAPBOX_NOARTS', 'Нет статей для показа');
define('_AM_SOAPBOX_NOCOLS', 'Нет колонок для показа');
define('_AM_SOAPBOX_NOCOLTOEDIT', 'Нет колонок для редактирования!');
define('_AM_SOAPBOX_NOHILITE', 'Ни одина колонка не выделена');
define('_AM_SOAPBOX_NOPERMSSET', 'Невозможно установить права доступа: Нет созданных колонок!');
define('_AM_SOAPBOX_NOSUBMISSYET', 'Нет в настоящее время представлений ожидающих утверждения.');
define('_AM_SOAPBOX_NOTUPDATED', 'Ошибка обновления базы данных!');
define('_AM_SOAPBOX_OPTIONS', 'Опции');
define('_AM_SOAPBOX_OPTS', 'Модуль параметров конфигурации');
define('_AM_SOAPBOX_ORDERUPDATED', 'Переупорядочить по весу');
define('_AM_SOAPBOX_PERMS', 'Права доступа');
define('_AM_SOAPBOX_PERMSMNGMT', 'Управление правами доступа');
define('_AM_SOAPBOX_PERMSNOTE',
       '<div><b>ЗАМЕТКА:</b> Обратите внимание, что даже если Вы установите здесь права просмотра а группа не может видеть статьи или блоки, то Вам необходимо установить параметры доступа для групп к модулю. Чтобы сделать это, перейдите <b>Система > Группы</b>, выберите соответствующие группы и установите флажки, чтобы предоставить доступ.</div>');
define('_AM_SOAPBOX_REORDERCOL', 'Изменение порядка колонок');
define('_AM_SOAPBOX_SELALL', 'Все статьи');
define('_AM_SOAPBOX_SELAPV', 'Одобренные статьи');
define('_AM_SOAPBOX_SELOFF', 'Оффлайн статьи');
define('_AM_SOAPBOX_SELONL', 'Онлайн статьи');
define('_AM_SOAPBOX_SELSUB', 'Представленные статьи');
define('_AM_SOAPBOX_SELECT_COLS', 'Выбрать колонки, какая группа может просматривать');
define('_AM_SOAPBOX_SELECTSTATUS', 'Выберите статус: ');
define('_AM_SOAPBOX_SELECT_IMG', 'Выберите изображение статьи:');
define('_AM_SOAPBOX_SHOWARTS', 'Статьи');
define('_AM_SOAPBOX_SHOWCOLS', 'Колонки');
define('_AM_SOAPBOX_SHOWSUBMISSIONS', 'Статьи ожидающие разрешения');
define('_AM_SOAPBOX_SHWALL', 'Показаны все статьи');
define('_AM_SOAPBOX_SHWAPV', 'Показаны утвержденные статьи');
define('_AM_SOAPBOX_SHWOFF', 'Показаны оффлайн статьи');
define('_AM_SOAPBOX_SHWONL', 'Показаны онлайн статьи');
define('_AM_SOAPBOX_SHWSUB', 'Показаны представленные статьи');
define('_AM_SOAPBOX_STATUS', 'Статус');
define('_AM_SOAPBOX_SUBS', 'Статьи > Авторизация \'');
define('_AM_SOAPBOX_SUBTEXT', 'Этот список показывает статьи, представленные авторами; Эти статьи ждут проверки.');
define('_AM_SOAPBOX_SUBMITS', 'На проверку');
define('_AM_SOAPBOX_SUBMITSMNGMT', 'Управление представленными статьями');
define('_AM_SOAPBOX_SWITCHOFFLINE', ' Установить статью оффлайн?');
define('_AM_SOAPBOX_TOTART', 'Опубликованные статьи:  <strong>%s</strong> ');
define('_AM_SOAPBOX_TOTCOL', 'Колонки:  <strong>%s</strong> ');
define('_AM_SOAPBOX_TOTOFF', 'Оффлайн статьи:  <strong>%s</strong> ');
define('_AM_SOAPBOX_TOTSUB', 'Представленные статьи:  <strong>%s</strong> ');

define('_AM_SOAPBOX_UPLOADIMAGE', 'Загрузить изображение статьи:');
define('_AM_SOAPBOX_WEIGHT', 'Вес');
define('_AM_SOAPBOX_YES', 'Да');
//add by domifara
define('_AM_SOAPBOX_DATESUBNOCHANGE', 'дата этой статьи не изменяется');
define('_AM_SOAPBOX_NOTIFY', 'Уведомлять о публикации?');
define('_AM_SOAPBOX_POSTED', 'Опубликовано:');
define('_AM_SOAPBOX_REORDERART', 'Упорядочить статьи (вес)');
//1.06
//ModuleAdmin
define('_AM_SOAPBOX_MODULEADMIN_MISSING', 'Ошибка: Класс ModuleAdmin отсутствует. Пожалуйста, установите ModuleAdmin класса в /Frameworks (смотреть /docs/readme.txt)');
define('_AM_SOAPBOX_NEED_APPROVAL', 'Необходимо одобрение:  <strong>%s</strong> ');
//Permissions
define('_AM_SOAPBOX_PERMISSIONS_GLOBAL', 'Глобальные разрешения');
define('_AM_SOAPBOX_PERMISSIONS_GLOBAL_DESC', 'Только пользователи из выбранной Вами группы могут глобально');
define('_AM_SOAPBOX_PERMISSIONS_GLOBAL_4', 'Участвовать в рейтинге');
define('_AM_SOAPBOX_PERMISSIONS_GLOBAL_8', 'Отправлять статьи');
define('_AM_SOAPBOX_PERMISSIONS_GLOBAL_16', 'Авто одобрение');
define('_AM_SOAPBOX_PERMISSIONS_APPROVE', 'Возможность одобрять статьи');
define('_AM_SOAPBOX_PERMISSIONS_APPROVE_DESC', 'Только выбранные Вами пользователи могут подтвердить');
define('_AM_SOAPBOX_PERMISSIONS_VIEW', 'Разрешения для просмотра');
define('_AM_SOAPBOX_PERMISSIONS_VIEW_DESC', 'Только пользователи из выбранной Вами группы могут просматривать');
define('_AM_SOAPBOX_PERMISSIONS_SUBMIT', 'Отправлять статьи');
define('_AM_SOAPBOX_PERMISSIONS_SUBMIT_DESC', 'Только пользователи из выбранной вами группы могут отправить');
define('_AM_SOAPBOX_PERMISSIONS_NOPERMSSET', 'Невозможно установить разрешение: Не установлен рейтинг! Сначала создайте рейтинг.');

define('_AM_SOAPBOX_UPGRADEFAILED0', "Ошибка обновления - не удалось переименовать поле '%s'");
define('_AM_SOAPBOX_UPGRADEFAILED1', "Ошибка обновления - не удалось добавить новые поля");
define('_AM_SOAPBOX_UPGRADEFAILED2', "Ошибка обновления - невозможно переименовать таблицу '%s'");
define('_AM_SOAPBOX_ERROR_COLUMN', 'Не удалось создать столбец в базе данных: %s');
define('_AM_SOAPBOX_ERROR_BAD_XOOPS', 'Этот модуль требует XOOPS %s+ (%s установлено)');
define('_AM_SOAPBOX_ERROR_BAD_PHP', 'Этот модуль требует PHP версии %s+ (%s установлено)');
define('_AM_SOAPBOX_ERROR_TAG_REMOVAL', 'Не удалось удалить теги из модуля тегов');

define('_AM_SOAPBOX_ADD_SAMPLEDATA', 'Добавить пример данных (удалит ВСЕ текущие данные)');
define('_AM_SOAPBOX_SAMPLEDATA_SUCCESS', 'Примеры успешно загружены');

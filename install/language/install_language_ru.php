<?php

/* ****************************************************************************************************
 *  File: /install/install_language.php
 *  Purpose: A language file for Install. It's used whenever the install file needs to output language.
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not,
 *   see http://www.gnu.org/licenses/.
 *
 *   Copyright (c) 2010 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

/* Upgrade Step 1 */
$lang["upgrade_title"] = "Обновление Hotaru CMS";

$lang["upgrade_step1"] = "Шаг 1/3: Проверка ваших существующих настроек";
$lang["upgrade_step1_details"] = "To upgrade Hotaru to version " . $h->version . ", click 'Next'...";
$lang["upgrade_step1_old_version"] = "В настоящее время используется версия Hotaru CMS ";
$lang["upgrade_step1_old_no_version"] = "Мы не смогли найти существующий номер версии Hotaru CMS в базе данных.";
$lang["upgrade_step1_current_version"] = "У Вас уже установлена последняя версия Hotaru . <br/> Если вы хотите запустить обновление сценария снова нажмите кнопку \"Далее\", в противном случае вы можете закрыть это окно браузера .";

/* Upgrade Step 2 */
$lang["upgrade_step2"] = "Шаг 2 из 3: Обновление базы данных";
$lang["upgrade_step2_details"] = "Поздравляем! Вы успешно обновили Hotaru CMS.";

/* Upgrade Step 2 */
$lang["upgrade_step3"] = "Шаг 3 из 3: Проверить плагины, шаблоны";
$lang["upgrade_step3_details"] = "Вы успешно обновили Hotaru CMS.";
$lang["upgrade_step3_instructions"] = "После нажатия \"Готово\" вы можете найти некоторые из ваших плагинов которые нуждаются в обновлении. Вы также можете проверить последние номера версий плагинов на странице управления плагинами в админ панели. Возможно для корректной работой с последней версией Hotaru CMS вам понадобиться настроить шаблоны плагинов которые вы изменяли.";
$lang["upgrade_step3_go_play"] = "Нажмите \"Готово\", чтобы получить доступ к вашему Hotaru сайту!";
$lang["upgrade_home"] = "Финиш";

/* Install Common */
$lang["install_title"] = "Установить Hotaru CMS ";
$lang["admin_theme_header_hotarucms"] = "Hotaru CMS";
$lang["install_next"] = "Дальше";
$lang["install_back"] = "Назад";
$lang["install_trouble"] = "Возникли проблемы? Читайте <a href='http://docs.hotarucms.org'> Документацию </a> или обратитесь за помощью на <a href='http://forums.hotarucms.org'> Форум </a> ,";

/* Install Step 1 */
$lang["install_step0"] = "Добро пожаловать в Hotaru CMS";
$lang["install_step0_welcome"] = "Установка Hotaru может быть завершена всего за 4 шага и обычно требует только несколько минут.";
$lang["install_step0_select"] = "Выберите ниже, начать новую установку или обновить существующий  Hotaru сайт ...";
$lang["install_new"] = "Новая установка";
$lang["install_new2"] = "Установить";
$lang["install_upgrade"] = "Обновить существующего сайта";
$lang["install_upgrade2"] = "Обновление";

/* Install Step 2 */
$lang["install_step1"] = "Шаг 1 из 4: Настройка базы данных";
$lang["install_step1_instructions"] = "Чтобы настроить базу данных для Hotaru CMS, вам нужно сделать следующее";
$lang["install_step1_instructions1"] = "Создать базу данных с именем <i> hotaru </i> в панели управления веб-хостинга. Запишите имя пользователя и пароль!";
$lang["install_step1_instructions2"] = "Переименовать файл в папке config <code>settings_default.php</code> на <code>settings.php</code>.";
$lang["install_step1_instructions3"] = "Открыть файл <code>settings.php</code> и заполнить раздел \"Database Details\".";
$lang["install_step1_instructions4"] = "Заполните <code> baseurl </code> , например <i> http://example.com/ </i> . Не забудьте слеш в конце (/)";
$lang["install_step1_instructions5"] = "Сохранить и загрузить файл <code>settings.php</code> на сервер в папку config, затем нажмите \"Далее\" ...";
$lang["install_step1_instructions_create_db"] = "Сначала создайте новую базу данных на сервере, заполните форму. Эти данные будут уникальными для вашего сервера и настройки базы данных.";
$lang["install_step1_instructions_manual_setup"] = "Если вы предпочитаете отредактировать файл настроек вручную";
$lang["install_step1_instructions_manual_setup_click"] = "кликните здесь";
$lang["install_step1_warning"] = "<b>Предупреждение</b>";
$lang["install_step1_warning_note"] = "Когда вы нажмите \"Далее\", будет создана новая таблица базы данных, все старые таблицы будут удалены!";

$lang["install_step1_baseurl"] = "<b>Baseurl</b>";
$lang["install_step1_baseurl_explain"] = "например http://example.com/ (Обязательно слеш в конце '/')";
$lang["install_step1_dbuser"] = "<b>База данных пользователей</b>";
$lang["install_step1_dbuser_explain"] = "Добавить личные данные в базу данных";
$lang["install_step1_dbpassword"] = "<b>Пароль базы данных</b>";
$lang["install_step1_dbpassword_explain"] = "";
$lang["install_step1_dbname"] = "<b>Имя базы данных</b>";
$lang["install_step1_dbname_explain"] = "";
$lang["install_step1_dbprefix"] = "<b>Префикс База данных</b>";
$lang["install_step1_dbprefix_explain"] = "Префикс базы данных, например, \"hotaru_\"";
$lang["install_step1_dbhost"] = "<b>Database Host</b>";
$lang["install_step1_dbhost_explain"] = "Скорей всего вам не нужно это изменять";

$lang["install_step1_dbpassword_error"] = "Есть проблема с паролем, который вы ввели.";
$lang["install_step1_baseurl_error"] = "Є проблема з baseurl";
$lang["install_step1_dbuser_error"] = "Есть проблема с базой данных пользователей";
$lang["install_step1_dbname_error"] = "Есть проблема с именем базы данных ";
$lang["install_step1_dbprefix_error"] = "Есть проблема с префиксом базы данных";
$lang["install_step1_dbhost_error"] = "Есть проблема с базой данных хостинга";

$lang["install_step1_settings_file_already_exists"] = "На вашем сервере уже существует файл настроек Hotaru. Если вы нажмете \"Обновить\" ваш существующий файл будет обновляться с указанными выше настройками.";
$lang["install_step1_settings_db_already_exists"] = "На сервере уже имеется база данных Hotaru с живыми таблицами. Действуйте с осторожностью.";
$lang["install_step1_update_file_writing_success"] = "Файл \"settings\" был создан.";
$lang["install_step1_update_file_writing_failure"] = "Есть проблема при создании файла \"settings\".";
$lang["install_step1_no_db_exists_failure"] = "База данных не существует, либо настройки подключения неверны.";
$lang["install_step1_no_table_exists_failure"] = "Таблица не существует или, может быть, неверный префикс базы данных в файле \"settings\".";


/* Install Step 3 */
$lang["install_step2"] = "Шаг 2 из 4: Создание таблиц базы данных";
$lang["install_step2_checking_tables"] = "Проверка существующих таблиц в базе данных:";
$lang["install_step2_no_tables"] = "Ни одной из существующих таблиц не было найдено в базе данных";
$lang["install_step2_creating_table"] = "Создание таблицы";
$lang["install_step2_adding_data"] = "Добавление данных в";
$lang["install_step2_deleting_table"] = "Удаление существующих таблиц";
$lang["install_step2_already_exists"] = "Кажется, в базе данных уже есть таблицы для Hotaru CMS.";
$lang["install_step2_continue"] = "Нажмите \"Далее\", чтобы продолжить.";
$lang["install_step2_rebuild_note"] = "<i> Примечание</i>: Если вы хотите начать все заново,";
$lang["install_step2_rebuild_link"] = "удалять и восстанавливать таблицы базы данных";
$lang["install_step2_success"] = "Таблицы базы данных успешно созданы. Нажмите \"Далее\", чтобы настроить Hotaru CMS.";
$lang["install_step2_fail"] = "Есть некоторые ошибки в создании таблиц базы данных. Не все таблицы могли быть правильно созданы.";
$lang["install_step2_existing_db"] = "У вас уже есть существующая установка Hotaru CMS. <br/> Если вы продолжите, эта установка удалит все существующие таблицы и параметры, в том числе сообщения, пользователей и данные плагинов.";
$lang["install_step2_existing_confirm"] = "Подтвердите, что вы хотите продолжить эту установку, введя «DELETE» в поле и нажмите кнопку";
$lang["install_step2_existing_go_upgrade1"] = "Кроме того, вы, возможно, пожелаете";
$lang["install_step2_existing_go_upgrade2"] = "запустить сценарий обновления";
$lang["install_step2_form_delete_confirm"] = "подтвердить";
$lang["install_step2_form_delete"] = "Обновление";



/* Install Step 4 */
$lang["install_step3"] = "Шаг 3 из 4: Регистрация Admin";
$lang["install_step3_instructions"] = "Зарегистрироваться в качестве администратора сайта";
$lang["install_step3_username"] = "Имя пользователя:";
$lang["install_step3_email"] = "E-Mail:";
$lang["install_step3_password"] = "Пароль:";
$lang["install_step3_password_verify"] = "Пароль (повторить):";
$lang["install_step3_csrf_error"] = "Внимание! Вы вызвали ошибку CSRF. Это должно произойти, когда кто-то пытается взломать сайт ...";
$lang["install_step3_username_error"] = "Твое имя пользователя должно иметь не менее 4 символов и может содержать буквы, тире и подчеркивания";
$lang["install_step3_password_error"] = "Пароль должен иметь не менее 8 символов и может содержать только латинские буквы, цифры и следующие символы: @ * # - _";
$lang["install_step3_password_match_error"] = "Поля с паролями не совпадают";
$lang["install_step3_email_error"] = "Этот адрес электронной почты не может быть настоящим";
$lang["install_step3_make_note"] = "Заполните новое имя пользователя, адрес электронной почты и пароль, прежде чем нажать \"Далее\" ...";
$lang["install_step3_update_success"] = "Обновлено успешно";
$lang["install_step3_form_update"] = "Обновить";

/* Install Step 5 */
$lang["install_step4"] = "Шаг 4 из 4: Завершение";
$lang["install_step4_installation_complete"] = "База данных была успешно обновлена";
$lang["install_step4_installation_delete"] = "<span style='color: red;'><b> Внимание:</b> Вы <b> должны </b> удалить папку install в противном случае остается возможность, без вашего ведома, запустить скрипт установки и удалить ваш Hotaru CMS !  </span>";

$lang["install_step4_form_check_php"] = "Проверьте настройки PHP";
$lang["install_step4_form_check_php_warning"] = "Примечание: На Вашем сервере отсутствует модуль PHP:";
$lang["install_step4_form_check_php_success"] = "Ваш сервер имеет необходимые модули PHP";
$lang["install_step4_form_check_php_version"] = "Hotaru не был протестирован на этой версии PHP. Возможно вам нужно обновление";


$lang["install_step4_installation_go_play"] = "Поздравляю! Теперь вы можете смело идти и играть с вашим новым Hotaru сайтом!";
$lang["install_home"] = "Начать!";

?>
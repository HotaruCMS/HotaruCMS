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
$lang["upgrade_title"] = "Оновлення Hotaru CMS";

$lang["upgrade_step1"] = "Крок 1/3: Перевірка існуючих налаштувань";
$lang["upgrade_step1_details"] = "To upgrade Hotaru to version " . $h->version . ", click 'Next'...";
$lang["upgrade_step1_old_version"] = "В даний час використовується версія Hotaru CMS";
$lang["upgrade_step1_old_no_version"] = "Ми не змогли знайти існуючий номер версії Hotaru CMS в базі даних.";
$lang["upgrade_step1_current_version"] = "У Вас вже встановлена остання версія Hotaru. <br/> Якщо ви хочете запустити оновлення сценарію знову, натисніть кнопку \"Далі\", в іншому випадку ви можете закрити це вікно браузера.";

/* Upgrade Step 2 */
$lang["upgrade_step2"] = "Крок 2 з 3: Оновлення бази даних";
$lang["upgrade_step2_details"] = "Вітаємо! Ви успішно оновили Hotaru CMS.";

/* Upgrade Step 2 */
$lang["upgrade_step3"] = "Крок 3 з 3: Перевірити плагіни, шаблони";
$lang["upgrade_step3_details"] = "Ви успішно оновили Hotaru CMS.";
$lang["upgrade_step3_instructions"] = "Після натискання \"Готово\" ви можете знайти деякі з ваших плагінів які потребують оновлення. Ви також можете перевірити останні номери версій плагінів на сторінці управління плагінами в адмін панелі. Можливо для коректної роботи з останньою версією Hotaru CMS вам знадобитися налаштувати шаблони плагінів які ви змінювали.";
$lang["upgrade_step3_go_play"] = "Натисніть \"Готово\", щоб отримати доступ до вашого Hotaru сайту!";
$lang["upgrade_home"] = "Фініш";

/* Install Common */
$lang["install_title"] = "Встановити Hotaru CMS ";
$lang["admin_theme_header_hotarucms"] = "Hotaru CMS";
$lang["install_next"] = "Далі";
$lang["install_back"] = "Назад";
$lang["install_trouble"] = "Виникли проблеми? Читайте <a href='http://docs.hotarucms.org'> Документацію </a> або зверніться за допомогою на <a href='http://forums.hotarucms.org'> Форум </a> ,";

/* Install Step 1 */
$lang["install_step0"] = "Ласкаво просимо до Hotaru CMS";
$lang["install_step0_welcome"] = "Установка Hotaru може бути завершена всього за 4 кроки і зазвичай вимагає тільки кілька хвилин.";
$lang["install_step0_select"] = "Виберіть нижче, почати нову установку або оновити існуючий Hotaru сайт ...";
$lang["install_new"] = "Нова установка";
$lang["install_new2"] = "Встановити";
$lang["install_upgrade"] = "Оновлення існуючого сайту";
$lang["install_upgrade2"] = "Оновлення";

/* Install Step 2 */
$lang["install_step1"] = "Крок 1 з 4: Налаштування бази даних";
$lang["install_step1_instructions"] = "Щоб налаштувати базу даних для Hotaru CMS, вам потрібно зробити наступне";
$lang["install_step1_instructions1"] = "Створити базу даних з ім'ям <i> hotaru </i> в панелі управління веб-хостингу. Запишіть ім'я користувача та пароль!";
$lang["install_step1_instructions2"] = "Перейменувати файл в папці config <code>settings_default.php</code> на <code>settings.php</code>.";
$lang["install_step1_instructions3"] = "Відкрити файл <code> settings.php </ code> і заповнити розділ \"Database Details\".";
$lang["install_step1_instructions4"] = "Заповніть <code> baseurl </code>, наприклад <i> http://example.com/ </i>. Не забудьте слеш в кінці(/)";
$lang["install_step1_instructions5"] = "Зберегти і завантажити файл <code> settings.php </code> на сервер в папку config та натисніть \"Далі\" ...";
$lang["install_step1_instructions_create_db"] = "Спершу створіть нову базу даних на сервері, заповніть форму. Ці дані будуть унікальними для вашого сервера і налаштування бази даних.";
$lang["install_step1_instructions_manual_setup"] = "Якщо ви віддаєте перевагу відредагувати файл налаштувань вручну";
$lang["install_step1_instructions_manual_setup_click"] = "клацніть тут";
$lang["install_step1_warning"] = "<b>Попередження</b>";
$lang["install_step1_warning_note"] = "Коли ви натисните \"Далі\", буде створена нова таблиця бази даних, всі старі таблиці будуть видалені!";

$lang["install_step1_baseurl"] = "<b>Baseurl</b>";
$lang["install_step1_baseurl_explain"] = "наприклад http://example.com/ (Обов'язково слеш в кінці '/')";
$lang["install_step1_dbuser"] = "<b>База даних користувачів</b>";
$lang["install_step1_dbuser_explain"] = "Додати особисті дані в базу даних";
$lang["install_step1_dbpassword"] = "<b> Пароль бази даних </b>";
$lang["install_step1_dbpassword_explain"] = "";
$lang["install_step1_dbname"] = "<b> Ім'я бази даних </b>";
$lang["install_step1_dbname_explain"] = "";
$lang["install_step1_dbprefix"] = "<b>Префікс База даних</b>";
$lang["install_step1_dbprefix_explain"] = "Префікс бази даних, наприклад, \"hotaru_\"";
$lang["install_step1_dbhost"] = "<b>Database Host</b>";
$lang["install_step1_dbhost_explain"] = "Швидше за все вам не потрібно це змінювати";

$lang["install_step1_dbpassword_error"] = "Є проблема з паролем, який ви ввели.";
$lang["install_step1_baseurl_error"] = "Есть проблема с baseurl";
$lang["install_step1_dbuser_error"] = "Є проблема з базою даних користувачів";
$lang["install_step1_dbname_error"] = "Є проблема з ім'ям бази даних";
$lang["install_step1_dbprefix_error"] = "Є проблема з префіксом бази даних";
$lang["install_step1_dbhost_error"] = "Є проблема з базою даних хостингу";

$lang["install_step1_settings_file_already_exists"] = "На вашому сервері вже існує файл налаштувань Hotaru. Якщо ви натиснете \"Оновити\" ваш існуючий файл буде оновлюватися з зазначеними вище налаштуваннями.";
$lang["install_step1_settings_db_already_exists"] = "На сервері вже є база даних Hotaru з живими таблицями. Дійте з обережністю.";
$lang["install_step1_update_file_writing_success"] = "Файл \"settings\" був створений.";
$lang["install_step1_update_file_writing_failure"] = "Є проблема при створенні файлу \"settings\".";
$lang["install_step1_no_db_exists_failure"] = "База даних не існує, або настройки підключення невірні.";
$lang["install_step1_no_table_exists_failure"] = "Таблиця не існує або, можливо, невірний префікс бази даних у файлі \"settings\".";


/* Install Step 3 */
$lang["install_step2"] = "Крок 2 з 4: Створення таблиць бази даних";
$lang["install_step2_checking_tables"] = "Перевірка існуючих таблиць в базі даних:";
$lang["install_step2_no_tables"] = "Жодної з існуючих таблиць не було знайдено в базі даних";
$lang["install_step2_creating_table"] = "Створення таблиці";
$lang["install_step2_adding_data"] = "Додавання даних в";
$lang["install_step2_deleting_table"] = "Видалення існуючих таблиць";
$lang["install_step2_already_exists"] = "Здається, в базі даних вже є таблиці для Hotaru CMS.";
$lang["install_step2_continue"] = "Натисніть \"Далі\", щоб продовжити.";
$lang["install_step2_rebuild_note"] = "<i> Примітка </i>: Якщо ви хочете почати все заново,";
$lang["install_step2_rebuild_link"] = "видаляти і відновлювати таблиці бази даних";
$lang["install_step2_success"] = "Таблиці бази даних успішно створені. Натисніть \"Далі\", щоб налаштувати Hotaru CMS.";
$lang["install_step2_fail"] = "Є деякі помилки в створенні таблиць бази даних. Не всі таблиці могли бути правильно створені.";
$lang["install_step2_existing_db"] = "У вас вже є існуюча установка Hotaru CMS. <br/> Якщо ви продовжите, ця установка видалить всі існуючі таблиці та параметри, в тому числі повідомлення, користувачів і дані плагінів.";
$lang["install_step2_existing_confirm"] = "Підтвердіть, що ви хочете продовжити цю установку, ввівши «DELETE» в поле і натисніть кнопку";
$lang["install_step2_existing_go_upgrade1"] = "Крім того, ви, можливо, побажаєте";
$lang["install_step2_existing_go_upgrade2"] = "запустити сценарій оновлення";
$lang["install_step2_form_delete_confirm"] = "підтвердити";
$lang["install_step2_form_delete"] = "Оновлення";



/* Install Step 4 */
$lang["install_step3"] = "Крок 3 з 4: Реєстрація Admin";
$lang["install_step3_instructions"] = "Зареєструватися в якості адміністратора сайту";
$lang["install_step3_username"] = "Ім'я користувача:";
$lang["install_step3_email"] = "E-Mail:";
$lang["install_step3_password"] = "Пароль:";
$lang["install_step3_password_verify"] = "Пароль (повторити):";
$lang["install_step3_csrf_error"] = "Увага! Ви викликали помилку CSRF. Це повинно статися, коли хтось намагається зламати сайт ...";
$lang["install_step3_username_error"] = "Твоє ім'я користувача повинно мати не менше 4 символів і може містити літери, тире і підкреслення";
$lang["install_step3_password_error"] = "Пароль повинен мати не менше 8 символів і може містити тільки латинські літери, цифри і такі символи: @ * # - _";
$lang["install_step3_password_match_error"] = "Поля з паролями не збігаються";
$lang["install_step3_email_error"] = "Ця адреса електронної пошти не може бути справжньою";
$lang["install_step3_make_note"] = "Заповніть нове ім'я користувача, адресу електронної пошти та пароль, перш ніж натиснути \"Далі\" ...";
$lang["install_step3_update_success"] = "Оновлено успішно";
$lang["install_step3_form_update"] = "Оновити";

/* Install Step 5 */
$lang["install_step4"] = "Крок 4 з 4: Завершення";
$lang["install_step4_installation_complete"] = "База даних була успішно оновлена";
$lang["install_step4_installation_delete"] = "<span style = 'color: red;'> <b> Увага: </ b> Ви <b> повинні </ b> видалити папку install в іншому випадку залишається можливість, без вашого відома, запустити скрипт установки і видалити ваш Hotaru CMS ! </ span>";

$lang["install_step4_form_check_php"] = "Перевірте налаштування PHP";
$lang["install_step4_form_check_php_warning"] = "Примітка: На Вашому сервері відсутній модуль PHP:";
$lang["install_step4_form_check_php_success"] = "Ваш сервер має необхідні модулі PHP";
$lang["install_step4_form_check_php_version"] = "Hotaru не тестувався на цій версії PHP. Можливо вам потрібне оновлення";


$lang["install_step4_installation_go_play"] = "Вітаю! Тепер ви можете сміливо йти і грати з вашим новим Hotaru сайтом!";
$lang["install_home"] = "Почати!";

?>
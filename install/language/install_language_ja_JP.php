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
$lang["upgrade_title"] = "Hotaru CMS のアップグレード";

$lang["upgrade_step1"] = "ステップ 1/3: 既存の設定をチェックします。";
$lang["upgrade_step1_details"] = "To upgrade Hotaru to version " . $h->version . ", click 'Next'...";
$lang["upgrade_step1_old_version"] = "現在 Hotaru CMS のバージョンを実行しています。";
$lang["upgrade_step1_old_no_version"] = "Hotaru CMS の既存のバージョン番号、データベースで見つかりませんでした。";
$lang["upgrade_step1_current_version"] = "インストール Hotaru の最新バージョンが既にある <br/>。場合実行、アップグレード スクリプトをもう一度クリックして '次へ'、それ以外の場合今このブラウザー ウィンドウを閉じることができます。";

/* Upgrade Step 2 */
$lang["upgrade_step2"] = "ステップ 2/3: アップグレードのデータベース";
$lang["upgrade_step2_details"] = "おめでとうございます ！Hotaru CMS が正常にアップグレードされました。";

/* Upgrade Step 2 */
$lang["upgrade_step3"] = "ステップ 3/3: チェックのプラグイン、テンプレート";
$lang["upgrade_step3_details"] = "Hotaru CMS が正常にアップグレードされました。";
$lang["upgrade_step3_instructions"] = "After clicking \"Finish\" you may find some of your plugins need upgrading. You can check the latest version numbers from the Plugin Management page in your admin dashboard. You may also need to modify any templates you have customised to make sure they work with the latest version of Hotaru CMS.";
$lang["upgrade_step3_go_play"] = "クリックして「完了」Hotaru サイトにアクセス ！";
$lang["upgrade_home"] = "完了";

/* Install Common */
$lang["install_title"] = "Hotaru CMS のセットアップ";
$lang["admin_theme_header_hotarucms"] = "Hotaru CMS ";
$lang["install_next"] = "次";
$lang["install_back"] = "戻る";
$lang["install_trouble"] = "トラブルですか？<a href='http://docs.hotarucms.org'> ドキュメント</a> を読んで、または <a href='http://forums.hotarucms.org'> フォーラム</a> で助けを求めます。";

/* Install Step 1 */
$lang["install_step0"] = "Hotaru CMS へようこそ";
$lang["install_step0_welcome"] = "インストール Hotaru ちょうど 4 つのステップで完了することができます通常はわずか数分を必要とします。";
$lang["install_step0_select"] = "新規インストールまたは既存の Hotaru サイトをアップグレードするかどうかの下にあるを選択.";
$lang["install_new"] = "新規インストール";
$lang["install_new2"] = "インストール";
$lang["install_upgrade"] = "既存のサイトをアップグレードします。";
$lang["install_upgrade2"] = "アップグレード";

/* Install Step 2 */
$lang["install_step1"] = "ステップ 1/4: データベースのセットアップ";
$lang["install_step1_instructions"] = "Hotaru CMS のデータベースを設定するには、次の操作をする必要があります。";
$lang["install_step1_instructions1"] = "あなたの web ホストのコントロール パネルの [<i>ほたる</i> と呼ばれるデータベースを作成します。ユーザー名とパスワードを書き留めておいてください ！";
$lang["install_step1_instructions2"] = "Config フォルダーに <code>settings_default.php</code> をコピーし、名前を <code>settings.php</code> に変更します。";
$lang["install_step1_instructions3"] = "<code>Settings.php</code> を開き、「データベースの詳細」欄に記入します。";
$lang["install_step1_instructions4"] = "<code>Baseurl</code>、例えば <i>http://example.com/</i> を入力してください。末尾のスラッシュ (/) を忘れないでください。";
$lang["install_step1_instructions5"] = "保存 <code>settings.php</code> config フォルダーにサーバーにアップロードし、「次へ」をクリックします.";
$lang["install_step1_instructions_create_db"] = "まず、サーバー上の新しいデータベースを作成し、以下のフォームに記入。これらの詳細は、サーバーとデータベースのセットアップに固有になります。";
$lang["install_step1_instructions_manual_setup"] = "設定ファイルを手動で編集したい場合";
$lang["install_step1_instructions_manual_setup_click"] = "ここをクリックします。";
$lang["install_step1_warning"] = "<b>警告</b>";
$lang["install_step1_warning_note"] = "ときに「次へ」をクリックすると、新しいデータベース テーブルが作成されます、必要があります任意の古いものを削除する ！";

$lang["install_step1_baseurl"] = "<b>Baseurl</b>";
$lang["install_step1_baseurl_explain"] = "e.g. http://example.com/ (Needs trailing slash '/')";
$lang["install_step1_dbuser"] = "<b>Database User</b>";
$lang["install_step1_dbuser_explain"] = "あなた自身のデータベースの詳細を追加します。";
$lang["install_step1_dbpassword"] = "<b>Database Password</b>";
$lang["install_step1_dbpassword_explain"] = "";
$lang["install_step1_dbname"] = "<b>Database Name</b>";
$lang["install_step1_dbname_explain"] = "";
$lang["install_step1_dbprefix"] = "<b>Database Prefix</b>";
$lang["install_step1_dbprefix_explain"] = "Database prefix, e.g. 'hotaru_'";
$lang["install_step1_dbhost"] = "<b>Database Host</b>";
$lang["install_step1_dbhost_explain"] = "あなたはおそらくこれを変更する必要はありません。";

$lang["install_step1_dbpassword_error"] = "入力したパスワードに問題がありました。";
$lang["install_step1_baseurl_error"] = "Baseurl で問題が発生しました";
$lang["install_step1_dbuser_error"] = "データベース ユーザーに問題があった";
$lang["install_step1_dbname_error"] = "データベース名を持つ問題があった";
$lang["install_step1_dbprefix_error"] = "データベースの接頭辞を持つ問題があった";
$lang["install_step1_dbhost_error"] = "データベース ホストと問題が発生しました";

$lang["install_step1_settings_file_already_exists"] = "サーバー上の Hotaru 設定ファイルが既に存在します。'更新' キーを押して、既存のファイルが上記の設定で更新されます。";
$lang["install_step1_settings_db_already_exists"] = "ライブのテーブルを使用してサーバーに Hotaru データベースが既に存在します。慎重に進みます。";
$lang["install_step1_update_file_writing_success"] = "'設定' ファイルが作成されました。";
$lang["install_step1_update_file_writing_failure"] = "'設定' ファイルを作成する問題があった。";
$lang["install_step1_no_db_exists_failure"] = "データベースが存在しないか、接続設定が正しくないです。";
$lang["install_step1_no_table_exists_failure"] = "テーブルが存在しないか設定ファイルのデータベースの接頭辞が正しいされる可能性があります。";


/* Install Step 3 */
$lang["install_step2"] = "ステップ 2/4: データベース テーブルを作成します。";
$lang["install_step2_checking_tables"] = "データベース内の既存のテーブルをチェックします。";
$lang["install_step2_no_tables"] = "データベースに既存のテーブルが見つかりませんでした。";
$lang["install_step2_creating_table"] = "テーブルを作成します。";
$lang["install_step2_adding_data"] = "データを追加します。";
$lang["install_step2_deleting_table"] = "既存のテーブルを削除します。";
$lang["install_step2_already_exists"] = "既に Hotaru cms データベース内のテーブルがあるようです。";
$lang["install_step2_continue"] = "続行するには、「次へ」をクリックします。";
$lang["install_step2_rebuild_note"] = "<i>Note</i>: If you'd like to start fresh, ";
$lang["install_step2_rebuild_link"] = "削除して、データベース テーブルを再構築";
$lang["install_step2_success"] = "データベースのテーブルが正常に作成されました。Hotaru CMS を構成するには、「次へ」をクリックします。";
$lang["install_step2_fail"] = "データベース テーブルの作成でエラーが発生しました。すべてのテーブルが正しく作成されている可能性があります。";
$lang["install_step2_existing_db"] = "You already have an existing installation of Hotaru CMS.<br/>If you continue, this installation will DELETE all your existing tables and settings, including posts, users and plugin data.";
$lang["install_step2_existing_confirm"] = "Confirm you wish to continue this install by typing 'DELETE' in the box and press the button";
$lang["install_step2_existing_go_upgrade1"] = "Alternatively, you may wish to ";
$lang["install_step2_existing_go_upgrade2"] = "run the upgrade script";
$lang["install_step2_form_delete_confirm"] = "confirm";
$lang["install_step2_form_delete"] = "Update";



/* Install Step 4 */
$lang["install_step3"] = "ステップ 3/4: 管理者登録";
$lang["install_step3_instructions"] = "サイト管理者として自分自身を登録します。";
$lang["install_step3_username"] = "Username:";
$lang["install_step3_email"] = "Email:";
$lang["install_step3_password"] = "Password:";
$lang["install_step3_password_verify"] = "Password (again):";
$lang["install_step3_csrf_error"] = "Ah! You've triggered a CSRF error. That's only supposed to happen when someone tries hacking into the site...";
$lang["install_step3_username_error"] = "あなたのユーザ名が 4 文字以上である必要があり、文字、ハイフン、アンダー スコアのみを含めることができます。";
$lang["install_step3_password_error"] = "パスワードは 8 文字以上である必要があり、だけ文字、数字およびこれらの記号を含めることができます: @ * ＃ - _";
$lang["install_step3_password_match_error"] = "パスワード フィールドが一致しません。";
$lang["install_step3_email_error"] = "有効な電子メール アドレスとして解析をしません";
$lang["install_step3_make_note"] = "「次へ」をクリックしてする前に、新しいユーザー名、メール アドレスとパスワードのメモを作る.";
$lang["install_step3_update_success"] = "正常に更新されました";
$lang["install_step3_form_update"] = "更新プログラム";

/* Install Step 5 */
$lang["install_step4"] = "ステップ 4/4: 完了";
$lang["install_step4_installation_complete"] = "データベースを正常にアップグレードされました";
$lang["install_step4_installation_delete"] = "<span style='color: red;'> <b>警告:</b> インストール フォルダーを削除する <b>必要があります</b> または他の誰かインストール スクリプトを実行することができるすべてを拭く ！</span>";

$lang["install_step4_form_check_php"] = "PHP の設定を確認します。";
$lang["install_step4_form_check_php_warning"] = "注： サーバーに必要な PHP モジュールはありません";
$lang["install_step4_form_check_php_success"] = "サーバーに必要な PHP モジュール";
$lang["install_step4_form_check_php_version"] = "Hotaru はこのバージョンの PHP でテストされていません。アップグレードする必要があります。";


$lang["install_step4_installation_go_play"] = "行うか？さて、新しい Hotaru サイトで遊ぶ ！";
$lang["install_home"] = "開始しなさい!";

?>
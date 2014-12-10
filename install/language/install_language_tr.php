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
$lang["upgrade_title"] = "Hotaru CMS Güncelle";

$lang["upgrade_step1"] = "Adım 1/3: Mevcut kurulum denetimi";
$lang["upgrade_step1_details"] = "To upgrade Hotaru to version " . $h->version . ", click 'Next'...";
$lang["upgrade_step1_old_version"] = "Şu anda Hotaru CMS sürümünü çalıştırıyorsunuz";
$lang["upgrade_step1_old_no_version"] = "Veritabanında Hotaru CMS'nin varolan bir sürümü bulunamadı.";
$lang["upgrade_step1_current_version"] = "Hotaru son sürümü zaten yüklü.<br/>Eğer yükseltme komutunu çalıştırmak istiyorsanız 'İleri' butonuna tıklayınız, aksi takdirde bu tarayıcı penceresini kapatabilirsiniz.";

/* Upgrade Step 2 */
$lang["upgrade_step2"] = "Adım 2/3: Veritabanı Güncelleme";
$lang["upgrade_step2_details"] = "Tebrikler! Hotaru CMS'yi başarıyla yüklediniz.";

/* Upgrade Step 2 */
$lang["upgrade_step3"] = "Adım 3/3: Eklentileri ve Şablonları Kontrol Et";
$lang["upgrade_step3_details"] = "Hotaru CMS'yi başarıyla yüklediniz.";
$lang["upgrade_step3_instructions"] = "\"Bitir\" butonuna tıkladıktan sonra eklentileri ve bazı iyileştirme uygulamalarını bulabilirsiniz. Yönetici gösterge paneli yönetim sayfasından son sürüme ait verileri kontrol edebilirsiniz. Ayrıca Hotaru CMS'nin son sürümüyle çalıştığınızdan emin olmak için özelleştirilmiş şablonları değiştirmeniz gerekebilir.";
$lang["upgrade_step3_go_play"] = "\"Bitir\" butonuna tıklayarak Hotaru sitenize erişebilirsiniz!";
$lang["upgrade_home"] = "Bitir";

/* Install Common */
$lang["install_title"] = "Hotaru CMS Kurulumu";
$lang["admin_theme_header_hotarucms"] = "Hotaru CMS";
$lang["install_next"] = "İleri";
$lang["install_back"] = "Geri";
$lang["install_trouble"] = "Sorun mu yaşıyorsunuz? <a href='http://docs.hotarucms.org'>Belgeleri</a> okuyun ya da <a href='http://forums.hotarucms.org'>Forum</a> üzerinden yardım isteyin.";

/* Install Step 1 */
$lang["install_step0"] = "Hotaru İçerik Yönetim Sistemi'ne Hoşgeldiniz";
$lang["install_step0_welcome"] = "Hotaru kurulumu normal şartlar altında 4 adımda tamamlanır ve birkaç dakika sürer.";
$lang["install_step0_select"] = "Yeni bir yükleme yapmak ve mevcut Hotaru sisteminizi yükseltmek seçeneklerinden birisini seçin.";
$lang["install_new"] = "Yeni Yükleme";
$lang["install_new2"] = "Yükleme";
$lang["install_upgrade"] = "Mevcut Siteyi Yükseltme";
$lang["install_upgrade2"] = "Güncelle";

/* Install Step 2 */
$lang["install_step1"] = "Adım 1/4: Veritabanı Kurulumu";
$lang["install_step1_instructions"] = "Hotaru CMS kurulumu için, bir veritabanı kurarak aşağıdakileri yapmanız gerekmektedir";
$lang["install_step1_instructions1"] = "Web sunucunuzun kontrol panelinden <i>hotaru</i> isimli bir veritabanı oluşturun. Kullanıcı adınızı ve şifrenizi not edin!";
$lang["install_step1_instructions2"] = "<code>settings_default.php</code> yapılandırma klasörünü kopyalayın ve <code>settings.php</code> olarak yeniden adlandırın.";
$lang["install_step1_instructions3"] = "<code>settings.php</code> dosyasını açık ve \"Veritabanı Ayarları\" bölümünü doldurun.";
$lang["install_step1_instructions4"] = "<code>baseurl</code> alanını doldurun, örneğin; <i>http://example.com/</i>. Sondaki taksimi unutmayın (/)";
$lang["install_step1_instructions5"] = "<code>settings.php</code> dosyasını güncelleyin ve sunucuya kaydedin. Ardından \"İleri\" butonuna tıklayın.";
$lang["install_step1_instructions_create_db"] = "Aşağıdaki formu doldurun ve sunucuda yeni bir veritabanı oluşturun. Bu ayarlardaki sunucu ve veritabanı bilgilerinden bağımsız olacaktır.";
$lang["install_step1_instructions_manual_setup"] = "Ayarlar dosyasını el ile düzenlemek isterseniz";
$lang["install_step1_instructions_manual_setup_click"] = "buraya tıklayın";
$lang["install_step1_warning"] = "<b>Uyarı</b>";
$lang["install_step1_warning_note"] = "\"İleri\" butonuna tıkladığınız zaman, eski tablolar silinecek ve yeni veritabanı tabloları oluşturulacaktır.";

$lang["install_step1_baseurl"] = "<b>Baz Alınacak Link</b>";
$lang["install_step1_baseurl_explain"] = "Örn: http://example.com/ (Taksim işaretini '/' unutmayın)";
$lang["install_step1_dbuser"] = "<b>Veritabanı Kullanıcısı</b>";
$lang["install_step1_dbuser_explain"] = "Kendi veritabanı bilgilerinizi ekleyin";
$lang["install_step1_dbpassword"] = "<b>Veritabanı Şifresi</b>";
$lang["install_step1_dbpassword_explain"] = "";
$lang["install_step1_dbname"] = "<b>Veritabanı Adı</b>";
$lang["install_step1_dbname_explain"] = "";
$lang["install_step1_dbprefix"] = "<b>Veritabanı Öneki</b>";
$lang["install_step1_dbprefix_explain"] = "Veritabanı öneki, Örn; 'hotaru_'";
$lang["install_step1_dbhost"] = "<b>Veritabanı Sunucusu</b>";
$lang["install_step1_dbhost_explain"] = "Muhtemelen bunu değiştirmeniz gerekmez";

$lang["install_step1_dbpassword_error"] = "Girdiğiniz parola ile ilgili bir sorun var.";
$lang["install_step1_baseurl_error"] = "Baz alınacak URL'de bir sorun var";
$lang["install_step1_dbuser_error"] = "Veritabanı kullanıcısında bir sorun var";
$lang["install_step1_dbname_error"] = "Veritabanı adında bir sorun var";
$lang["install_step1_dbprefix_error"] = "veritabanı ön ekinde bir sorun var";
$lang["install_step1_dbhost_error"] = "Veritabanı sunucusunda bir sorun var";

$lang["install_step1_settings_file_already_exists"] = "Sunucu üzerinde Hotaru ayar dosyası zaten var. Eğer \"Güncelle\" butonuna tıklarsanız mevcut dosya yukarıdaki ayarlar ile değiştirilecektir.";
$lang["install_step1_settings_db_already_exists"] = "Sunucu üzerinde Hotaru veritabanında zaten canlı tablolar var.";
$lang["install_step1_update_file_writing_success"] = "'Ayarlar' dosyası oluşturuldu.";
$lang["install_step1_update_file_writing_failure"] = "'Ayarlar' dosyası oluşturulurken bir sorunla karşılaşıldı.";
$lang["install_step1_no_db_exists_failure"] = "Veritabanı yok ya da bağlantı ayarlarında hata var.";
$lang["install_step1_no_table_exists_failure"] = "Hiçbir tablo yok, ayarlar dosyasındaki  veritabanı öneki yanlış olabilir.";


/* Install Step 3 */
$lang["install_step2"] = "Adım 2/4: Veritabanı Tablolarını Oluştur";
$lang["install_step2_checking_tables"] = "Veritabanındaki mevcut tabloları denetle:";
$lang["install_step2_no_tables"] = "Mevcut tablo veritabanında bulundu";
$lang["install_step2_creating_table"] = "Tablo oluştur";
$lang["install_step2_adding_data"] = "Veri ekleme";
$lang["install_step2_deleting_table"] = "Mevcut tabloları silme";
$lang["install_step2_already_exists"] = "Bu veritabanında Hotaru CMS için tablolar zaten oluşturulmuş görünüyor.";
$lang["install_step2_continue"] = "Devam etmek için \"İleri\" butonuna tıklayın.";
$lang["install_step2_rebuild_note"] = "<i>Not</i>: En baştan başlamak istiyorsanız,";
$lang["install_step2_rebuild_link"] = "silin ve veritabanı tablolarını yeniden oluşturun";
$lang["install_step2_success"] = "Veritabanı tabloları başarıyla oluşturuldu. \"İleri\" butonuna tıklayarak Hotaru CSM'yi yapılandırabilirsiniz.";
$lang["install_step2_fail"] = "Veritabanı tabloları oluşturulurken bazı hatalara rastlandı. Tüm tablolar doğru şekilde oluşturulmamış olabilir.";
$lang["install_step2_existing_db"] = "Zaten mevcutta yüklenmiş bir Hotaru CMS sistem var.<br/>Eğer devam ederseniz, bu yükleme; kullanıcı mesajları ve eklenti verileri dahil olmak üzere mevcut tüm tablolar ve ayarlar silinecektir.";
$lang["install_step2_existing_confirm"] = "Yüklemeye devam etmek istiyorsanız bu kutuya 'SİL' yazın ve onay butonuna tıklayın";
$lang["install_step2_existing_go_upgrade1"] = "Alternatif olarak, isteyebilrisiniz";
$lang["install_step2_existing_go_upgrade2"] = "Güncelleme komutunu çalıştır";
$lang["install_step2_form_delete_confirm"] = "Onayla";
$lang["install_step2_form_delete"] = "Güncelle";



/* Install Step 4 */
$lang["install_step3"] = "Adım 3/4: Yönetici Hesabı";
$lang["install_step3_instructions"] = "Site yöneticisi olarak kendinizi kaydedin";
$lang["install_step3_username"] = "Kullanıcı Adı:";
$lang["install_step3_email"] = "E-Posta:";
$lang["install_step3_password"] = "Şifre:";
$lang["install_step3_password_verify"] = "Şifre (Tekrar):";
$lang["install_step3_csrf_error"] = "Ah! Bir CSRF hatası tespit ettik. Bunun sadece birisi siteye saldırmaya çalıştığında gerçekleşmesi gerekiyordu.";
$lang["install_step3_username_error"] = "Kullanıcı adınız en az 4 karakter olmalıdır, harfler ve alt tire içerebilir";
$lang["install_step3_password_error"] = "Parolanız en az 8 karakter olmalıdır ve yalnızca harf, rakam ve bu simgeleri içerebilr: @ * # - _";
$lang["install_step3_password_match_error"] = "Parola alanları uyuşmuyor";
$lang["install_step3_email_error"] = "Bu, geçerli bir E-Posta adresi değil";
$lang["install_step3_make_note"] = "\"İleri\" butonuna tıklamadan önce yeni adınızı, E-Posta adresinizi ve şifrenizi bir yere not edin ...";
$lang["install_step3_update_success"] = "Başarıyla güncellendi";
$lang["install_step3_form_update"] = "Güncelle";

/* Install Step 5 */
$lang["install_step4"] = "Adım 4/4: Tamamlama";
$lang["install_step4_installation_complete"] = "Veritabanı başarıyla güncellendi";
$lang["install_step4_installation_delete"] = "<span style='color: red;'><b>UYARI:</b> Dosyaya yükleme komutu vererek <b>her şeyi</b> silmiş olacaksınız ve klasörü silmek için başka bir kullanıcı tarafından çalıştırılmaması gerekmektedir.</span>";

$lang["install_step4_form_check_php"] = "PHP Kurulumu Kontrol Et";
$lang["install_step4_form_check_php_warning"] = "Not: Sunucu PHP modülü eksik:";
$lang["install_step4_form_check_php_success"] = "Sunucunuzda gerekli PHP modülleri bulunmaktadır";
$lang["install_step4_form_check_php_version"] = "Hotaru, PHP'nin bu sürümünde test edilmemiştir. Yükseltmeniz gerekebilir";


$lang["install_step4_installation_go_play"] = "Bitti? Tamamdır, gidin ve yeni Hotaru siteniz ile oynayın!";
$lang["install_home"] = "Başlayın!";

?>
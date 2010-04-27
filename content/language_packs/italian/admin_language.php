<?php
/* **************************************************************************************************** 
 * ADMIN LANGUAGE
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

/* Title */
$lang["admin"] = "Admin";

/* Breadcrumbs */
$lang["admin_breadcrumbs_home"] = "Home";
$lang["admin_breadcrumbs_login"] = "Login";

/* Login */
$lang["admin_login_failed"] = "Login fallito";
$lang["admin_login_error_cookie"] = "Errore cookie. Username non fornito";
$lang["admin_login_email_invalid"] = "Email non valida";
$lang["admin_theme_login"] = "Login";
$lang["admin_theme_login_username"] = "Username";
$lang["admin_theme_login_password"] = "Password";
$lang["admin_theme_login_instructions"] = "Inserisci il tuo username e password:";
$lang["admin_theme_login_form_submit"] = "Login";
$lang["admin_theme_login_forgot_password"] = "Hai dimenticato la tua password?";
$lang['admin_theme_login_forgot_password_submit'] = "Invia";
$lang['admin_theme_login_forgot_password_submit_instruct_1'] = "Inserisci l'indirizzo email utilizzato per il tuo account Admin:";
$lang['admin_theme_login_forgot_password_submit_instruct_2'] = "Ti sarà inviato un codice di conferma via email. Cliccalo per ricevere una nuova password provvisoria. Potrai modificarla successivamente dopo aver eseguito il login.";
$lang["admin_theme_update_email"] = "Email:";

/* Forgotten Password */
$lang['admin_email_password_conf_sent'] = "Un'email è stata inviata all'indirizzo che hai fornito";
$lang['admin_email_password_conf_success'] = "E' stata inviata una nuova password all'indirizzo email che hai inserito.";
$lang['admin_email_password_conf_fail'] = "Non è stato possibile confermare il tuo indirizzo email. Per favore prova di nuovo.";

/* News */
$lang["admin_news_posted_by"] = "Inviata da";
$lang["admin_news_on"] = "il";
$lang["admin_news_read_more"] = "Leggi";
$lang["admin_news_more_threads"] = "Ulteriori news recenti...";

/* Announcements /class.hotaru.php */
$lang['admin_announcement_delete_install'] = "Per cortesia, eliminare la cartella install prima che qualcuno si diverta con la tua installazione cancellando il tuo database!";
$lang['admin_announcement_plugins_disabled'] = "Vai alla Gestione Plugin ed abilita i plugins.";
$lang['admin_announcement_users_disabled'] = "Per cortesia abilita il plugin Utenti nella Gestione Plugin.";
$lang['admin_announcement_change_site_email'] = "Please change the site email address in the Settings page.";
$lang['admin_announcement_site_closed'] = SITE_NAME . " è temporaneamente chiuso!";

/* Plugins */
$lang["admin_plugins_install_done"] = "Plugin installato e atticato con successo";
$lang["admin_plugins_install_sorry"] = "Mi dispiace,";
$lang["admin_plugins_install_requires"] = "richiede";
$lang["admin_plugins_uninstall_done"] = "Plugin installato";
$lang["admin_plugins_uninstall_all_done"] = "Tutti i plugins sono stati disinstallati";
$lang["admin_plugins_upgrade_done"] = "Plugin aggiornato ed attivato";
$lang["admin_plugins_page_refresh"] = "Ricarica questa pagina";
$lang["admin_plugins_activated"] = "Plugin attivato";
$lang["admin_plugins_deactivated"] = "Plugin disattivato";
$lang['admin_plugins_on'] = "On";
$lang['admin_plugins_off'] = "Off";
$lang['admin_plugins_install'] = "Installa";
$lang['admin_plugins_uninstall'] = "Disinstalla";
$lang['admin_plugins_upgrade'] = "Aggiorna";
$lang['admin_plugins_installed'] = "Installato";
$lang['admin_plugins_order_updated'] = "Ordine aggiornato";
$lang['admin_plugins_order_last'] = "è già l'ultimo.";
$lang['admin_plugins_order_zero'] = "Errore: il valore dell'ordinamento è zero.";
$lang['admin_plugins_order_first'] = "è già il primo.";
$lang['admin_plugins_order_last'] = "è già l'ultimo.";
$lang['admin_plugins_order_above'] = "Errore: il plugin da muovere sopra ha lo stesso valore di ordinamento.";
$lang['admin_plugins_order_below'] = "Error: il plugin da muovere sotto ha lo stesso valore di ordinamento.";
$lang["admin_theme_plugins"] = "Gestione Plugin";
$lang["admin_theme_plugins_installed"] = "Installato";
$lang["admin_theme_plugins_not_installed"] = "Non installato";
$lang["admin_theme_plugins_on_off"] = "On/Off";
$lang["admin_theme_plugins_active"] = "On";
$lang["admin_theme_plugins_inactive"] = "Off";
$lang["admin_theme_plugins_switch"] = "Scambia";
$lang["admin_theme_plugins_plugin"] = "Plugin";
$lang["admin_theme_plugins_install"] = "Installa";
$lang["admin_theme_plugins_uninstall"] = "Disinstalla";
$lang["admin_theme_plugins_order_up"] = "Muovi sopra";
$lang["admin_theme_plugins_order_down"] = "Muovi sotto";
$lang["admin_theme_plugins_details"] = "Dettagli";
$lang["admin_theme_plugins_requires"] = "Requisiti";
$lang["admin_theme_plugins_description"] = "Descrizione";
$lang["admin_theme_plugins_author"] = "Autore";
$lang["admin_theme_plugins_close"] = "Chiuso";
$lang["admin_theme_plugins_no_plugins"] = "Nessun plugin addizionale richiesto.";
$lang["admin_theme_plugins_guide"] = "Guida Gestione Plugin";
$lang["admin_theme_plugins_guide1"] = "Per aggiornare un plugin: disattivalo, aggiorna sovrascrivendo i files della nuova versione e riattiva il plugin.";
$lang["admin_theme_plugins_guide2"] = "La colonna di ordinamento serve a verificare quale plugin viene controllato per primo per l'hooks di aggancio.";
$lang["admin_theme_plugins_guide3"] = "Disinstallare un plugin cancellerà sia dal database le tabelle <i>plugins</i> ed i suoi <i>pluginhooks</i>, ma non i <i>pluginsettings</i>.";
$lang["admin_theme_plugins_guide4"] = "Ogni altra informazione creata nel database dal plugin non verrà rimossa.";
$lang["admin_theme_plugins_deactivate_all"] = "Disattiva tutti i plugins";
$lang["admin_theme_plugins_activate_all"] = "Attiva (aggiorna) tutti i plugins";
$lang["admin_theme_plugins_uninstall_all"] = "Disinstalla tutti i plugins";
$lang["admin_theme_plugins_settings"] = "Configurazione";
$lang["admin_theme_plugins_readme"] = "Leggimi";
$lang["admin_theme_plugins_more_info"] = "Maggiori informazioni";
$lang["admin_theme_plugins_readmetxt"] = "readme.txt";

/* Settings */
$lang['admin_settings_update_success'] = "Aggiornamento avvenuto con successo";
$lang['admin_settings_update_failure'] = "Errore durante il salvataggio";
$lang["admin_settings_theme_activate_success"] = "Il tema è stato correttamente attivato.";
$lang["admin_theme_settings"] = "Opzioni";
$lang["admin_theme_settings_title"] = "Opzioni Hotaru";
$lang["admin_theme_settings_setting"] = "Opzioni";
$lang["admin_theme_settings_value"] = "Valore";
$lang["admin_theme_settings_default"] = "Default";
$lang["admin_theme_settings_notes"] = "Note";
$lang["admin_plugin_settings_inactive"] = "Questo plugin è attualmente inattivo.";

/* Maintenance */
$lang['admin_maintenance_clear_all_cache_success'] = "Tutti i file della cache sono stati cancellati con successo";
$lang['admin_maintenance_clear_cache_success'] = "Cache cancellata con successo";
$lang['admin_maintenance_clear_cache_failure'] = "Nessun file della cache è stato trovato";
$lang['admin_maintenance_optimize_success'] = "Tutte le tabelle del database sono state ottimizzate";
$lang['admin_maintenance_table_emptied'] = "Tabella svuotata";
$lang['admin_maintenance_table_deleted'] = "Tabella cancellata";
$lang['admin_maintenance_settings_removed'] = "Opzioni rimosse";
$lang["admin_maintenance_site_closed"] = SITE_NAME . " sarà chiuso con la prossima visualizzazione di pagina";
$lang["admin_maintenance_site_opened"] = SITE_NAME . " sarà riaperto con la prossima visualizzazione di pagina";
$lang['admin_maintenance_announcement_updated'] = "Annuncio sito aggiornato";
$lang['admin_maintenance_system_report_success'] = "Nuovo report di sistema generato";
$lang['admin_maintenance_system_report_failure'] = "Impossibile generare un report di sistema";
$lang['admin_maintenance_system_report_emailed'] = "Report di sistema inviato a HotaruCMS.org";

$lang["admin_theme_maintenance"] = "Manutenzione";
$lang["admin_theme_maintenance_title"] = "Manutenzione Hotaru";
$lang["admin_theme_maintenance_site"] = "Sito:";
$lang["admin_theme_maintenance_announcement"] = "Visualizza questo annuncio in alto su ogni pagina del sito:";
$lang["admin_theme_maintenance_announcement_enable"] = "Abilitato";
$lang["admin_theme_maintenance_announcement_tags"] = "<small>Permessi: &lt;div&gt;&lt;p&gt;&lt;span&gt;&lt;b&gt;&lt;u&gt;&lt;i&gt;&lt;a&gt;&lt;img&gt;&lt;blockquote&gt;&lt;del&gt;&lt;br&gt;</small>";
$lang["admin_theme_maintenance_close_site"] = "Chiuso " . SITE_NAME . " per manutenzione";
$lang["admin_theme_maintenance_open_site"] = "Apri " . SITE_NAME . " ai visitatori";
$lang["admin_theme_maintenance_close_site_desc"] = "Solo utenti con \"permessi ammministrativi\" potranno visualizzare il sito.";
$lang["admin_theme_maintenance_open_site_desc"] = "Hai finito la manutenzione? Torna online.";
$lang["admin_theme_maintenance_cache"] = "Cache:";
$lang["admin_theme_maintenance_plugin_settings"] = "Opzioni plugin:";
$lang["admin_theme_maintenance_db_tables"] = "Tabelle database:";
$lang["admin_theme_maintenance_settings"] = "opzioni";
$lang["admin_theme_maintenance_all_cache"] = "Pulisci tutte le cartelle della cache";
$lang["admin_theme_maintenance_all_cache_desc"] = "cancella tutte le cartelle della cache, elencate di seguito.";
$lang["admin_theme_maintenance_db_cache"] = "Pulisci la cache del database";
$lang["admin_theme_maintenance_db_cache_desc"] = "cancella le queries cached del database.";
$lang["admin_theme_maintenance_css_js_cache"] = "Pulisci la cache css/js";
$lang["admin_theme_maintenance_css_js_cache_desc"] = "cancella i files cache CSS e JavaScript dei plugins.";
$lang["admin_theme_maintenance_html_cache"] = "Pulisci la cache HTML";
$lang["admin_theme_maintenance_html_cache_desc"] = "cancella la cache dei blocchi di codice HTML, ad esempio i widget della sidebar";
$lang["admin_theme_maintenance_rss_cache"] = "Pulisci la cache RSS";
$lang["admin_theme_maintenance_rss_cache_desc"] = "cancella i feed RSS nella cache.";
$lang["admin_theme_maintenance_debug"] = "Debug:";
$lang["admin_theme_maintenance_system_report"] = "Genera un report di sistema";
$lang["admin_theme_maintenance_email_system_report"] = "Invia il report di sistema a HotaruCMS.org";
$lang["admin_theme_maintenance_email_system_report_note"] = "<span style='color: red'>(Da utilizzare solo se richiesto nei forums ufficiali di supporto)</span>";
$lang["admin_theme_maintenance_debug_delete"] = "Pulisci i file cache del debug";
$lang["admin_theme_maintenance_debug_view"] = "Clicca il nomefile per visualizzare i logs:";
$lang["admin_theme_maintenance_debug_no_files"] = "<i>Al momento non ci sono file debug</i>";
$lang["admin_theme_maintenance_optimize"] = "Ottimizzazione:";
$lang["admin_theme_maintenance_optimize_database"] = "Ottimizza il database";
$lang["admin_theme_maintenance_optimize_desc"] = "Ottimizza tutte le tabelle del database.";
$lang["admin_theme_maintenance_empty"] = "Svuota";
$lang["admin_theme_maintenance_remove"] = "Rimuovi";
$lang["admin_theme_maintenance_drop"] = "Cancella";
$lang["admin_theme_maintenance_db_table_warning"] = "<b>Attenzione: Utilizzare con estrema cautela!</b>";
$lang["admin_theme_maintenance_plugin_settings_explanation"] = "Alcuni plugins di Hotaru CMS aggiungono dei settaggi direttamente al database. Per evitare si dover riconfigurare tutti i plugind ad ogni aggiornamento, questi settaggi non vengono rimossi, nemmeno quando i plugins vengono disinstallati. Se per qualche ragione, vuoi eliminare questi settaggi, puoi farlo da qui. E' altamente raccomandato di disinstallare ciascun plugin prima di proseguire.";
$lang["admin_theme_maintenance_empty_explanation"] = "Svuotando le tabelle, rimuoverai ogni dato presente, pur mantenendo la struttura del database. Ricorda, alcuni plugins dipendono dai dati contenuti in queste tabelle, per cui procedendo alla rimozione di tali dati potresti causare gravi problemi al funzionamento del sito. A meno che a svuotare le tabelle non sia uno sviluppatore, si consiglia di procedere con estrema attenzione.";
$lang["admin_theme_maintenance_no_db_tables_to_empty"] = "Nessuna tabella del database da svuotare.";
$lang["admin_theme_maintenance_no_plugin_settings_to_delete"] = "Nessun settaggio dei plugin da eliminare.";

/* Blocked List */
$lang['admin_blocked_list_empty'] = "Nessun valore inserito";
$lang['admin_blocked_list_added'] = "Nuovo elemento aggiunto";
$lang['admin_blocked_list_updated'] = "Elemento aggiornato";
$lang["admin_blocked_list_removed"] = "Elemento rimosso";
$lang['admin_blocked_list_exists'] = "Elemento già esistente";
$lang['admin_blocked_list_update'] = "Aggiornato";
$lang["admin_theme_blocked_desc"] = "Di per sé, questa lista non fa nulla, ma i plugins installati possono usarla per bloccare gli utenti durante la registrazione, l'invio di nuovi articoli ecc.";
$lang["admin_theme_blocked_list"] = "Lista bloccati";
$lang["admin_theme_blocked_type"] = "Tipo";
$lang["admin_theme_blocked_value"] = "Valore";
$lang["admin_theme_blocked_edit"] = "Modifica";
$lang["admin_theme_blocked_remove"] = "Rimuovi";
$lang["admin_theme_blocked_new"] = "Blocca un nuovo elemento:";
$lang["admin_theme_blocked_search"] = "Cerca un elemento:";
$lang["admin_theme_blocked_filter"] = "Filtra elementi:";
$lang["admin_theme_blocked_submit_add"] = "Aggiungi";
$lang["admin_theme_blocked_submit_search"] = "Cerca";
$lang["admin_theme_blocked_submit_filter"] = "Filtra";
$lang["admin_theme_blocked_ip"] = "Indirizzo IP";
$lang["admin_theme_blocked_email"] = "Indirizzo email/dominio";
$lang["admin_theme_blocked_url"] = "URL";
$lang["admin_theme_blocked_username"] = "Username";
$lang["admin_theme_blocked_all"] = "Tutto";

/* Pagination */
$lang['pagination_first'] = "Primo";
$lang['pagination_last'] = "Ultimo";
$lang['pagination_previous'] = "Precendente";
$lang['pagination_next'] = "Prossimo";

/* navigation */
$lang["admin_theme_navigation_home"] = "Home";
$lang["admin_theme_navigation_admin"] = "Admin";
$lang["admin_theme_navigation_login"] = "Login";
$lang["admin_theme_navigation_logout"] = "Logout";

/* main */
$lang["admin_theme_main_admin_cp"] = "Pannello di Controllo Admin";
$lang["admin_theme_main_admin_home"] = "Admin Home";
$lang["admin_theme_main_latest"] = "Le ultime da Hotaru CMS";
$lang["admin_theme_main_stats"] = "Statistiche";

/* theme settings */
$lang["admin_theme_plugin_settings"] = "Opzioni plugins";
$lang["admin_theme_theme_settings"] = "Opzion tema";
$lang["admin_theme_theme_no_settings"] = " non ha alcuna opzione.";
$lang["admin_theme_theme_activate"] = " può essere attivato, semplicemente cliccando qui.";
$lang["admin_theme_theme_activate_success"] = " Il tema è stato correttamente attivato.";
$lang["admin_theme_theme_activate_error"] = " Il tema non è stato attivato a causa di un errore.";


/* footer */
$lang["admin_theme_footer_having_trouble_vist_forums"] = "Haa problemi? Visita http://www.hotarucms.it oppure il sito ufficiale ";
$lang["admin_theme_footer_for_help"] = "per ottenere aiuto in lingua italiana oppure in inglese.";

/* 404 */
$lang["admin_theme_404_page_not_found"] = "Pagina non trovata.";

/* Account */
$lang["admin_theme_account"] = "Account";

?>
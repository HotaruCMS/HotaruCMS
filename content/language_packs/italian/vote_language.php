<?php
/**
 * VOTE LANGUAGE
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

/* Sidebar */
$lang["vote_admin_sidebar"] = "Voti";

/* Navigation */
$lang["vote_navigation_top_posts"] = "Top Posts";
$lang["vote_navigation_latest"] = "Ultimi";

/* Vote Button */
$lang["vote_button_vote"] = "Vota!";
$lang["vote_button_unvote"] = "Rimuovi";
$lang["vote_button_voted"] = "Votato";
$lang["vote_button_up_link"] = "Su!";
$lang["vote_button_up"] = "Su";
$lang["vote_button_down_link"] = "Giù!";
$lang["vote_button_down"] = "Giù";
$lang["vote_button_yes_link"] = "Si!";
$lang["vote_button_yes"] = "Si";
$lang["vote_button_no_link"] = "No!";
$lang["vote_button_no"] = "No";
$lang['vote_already_voted'] = "Mi dispiace, hai già votato per questo posts.";

/* Show Post - ALert links and flagged message */
$lang["vote_alert"] = "Segnala";
$lang["vote_alert_reason_title"] = "Motivo della segnalazione:";
$lang["vote_alert_reason_1"] = "Spam";
$lang["vote_alert_reason_2"] = "Inappropriato";
$lang["vote_alert_reason_3"] = "Link non funzioante";
$lang["vote_alert_reason_4"] = "Duplicato";
$lang["vote_alert_reason_5"] = "Categoria errata";
$lang["vote_alert_reason_6"] = "Veramente inutile";
$lang["vote_alert_already_flagged"] = "Hai già segnalato questo post";
$lang["vote_alert_flagged_message_1"] = "Questo post è stato segnalato da";
$lang["vote_alert_flagged_message_2"] = "come";
$lang["vote_alert_flagged_message_user"] = "utente";
$lang["vote_alert_flagged_message_users"] = "utenti";
$lang["vote_alert_flagged_message_reason"] = "motivo";
$lang["vote_alert_flagged_message_reasons"] = "motivi";
$lang["vote_alert_post_buried"] = "Questo post è stato nascosto.";

/* Vote Settings */
$lang["vote_settings_header"] = "Settaggio voti";
$lang["vote_settings_vote_type"] = "Scegli la tipologia di voto che vuoi utilizzare:";
$lang["vote_settings_vote_unvote"] = "Vota & Rimuovi - Metodo Standard di votazione";
$lang["vote_settings_up_down"] = "Su & Giù - Vota i post +1 e -1 ";
$lang["vote_settings_yes_no"] = "Si/No Sondaggio - Mostra i voti pro/contro un post";
$lang["vote_settings_vote_auto"] = "Auto-vota su invio:";
$lang["vote_settings_submit_vote"] = "Vota automaticamente un post, subito dopo il suo invio";
$lang["vote_settings_vote_anonymous"] = "Permetti voti anonimi:";
$lang["vote_settings_anonymous_votes"] = "Lascia votare i posts, dai non-membri del sito";
$lang["vote_settings_submit_vote_value"] = "Assegna un valore automatico di voto pari a";
$lang["vote_settings_submit_vote_value_invalid"] = "Il voto automatico deve essere un numero intero positivo";
$lang["vote_settings_vote_promote_bury"] = "Promozione e occultamento:";
$lang["vote_settings_votes_to_promote"] = "Numero dei voti necessari per arrivare in home page:";
$lang["vote_settings_votes_to_promote_invalid"] = "Il numero dei voti necessari deve essere un numero intero positivo";
$lang["vote_settings_upcoming_duration"] = "Numero di giorni nei quali un nuovo post può permanere nella pagina Ultimi in arrivo (Upcoming):";
$lang["vote_settings_upcoming_duration_invalid"] = "Il numero di giorni deve essere un numero intero positivo";
$lang["vote_settings_no_front_page"] = "Termine entro il quale i nuovi post possono raggiungere la pagina principale (in giorni):";
$lang["vote_settings_back_to_latest"] = "Riporta indietro i posts in \"Ultimi\" (Latest) se <i>i voti rimossi</i> tornano sotto la soglia dei voti necessari per andare in home page.";
$lang["vote_settings_no_front_page_invalid"] = "Il numero dei giorni deve essere un numero intero positivo";
$lang["vote_settings_use_alerts"] = "Abilita segnalazioni (esempio bandierina / occultamente). Le segnalazioni sono attive solo sui \"nuovi\" posts";
$lang["vote_settings_alerts_to_bury"] = "Numero delle segnalazioni necessarie per occultare automaticamente un post:";
$lang["vote_settings_alerts_to_bury_invalid"] = "Il numero delle segnalazioni deve essere un numero intero positivo";
$lang["vote_settings_physical_delete"] = "Rimuovi fisicamente dal database, un post che diviente occultato";
$lang["vote_settings_other"] = "Altro";
$lang["vote_settings_posts_widget"] = "Mostra la somma dei voti prima del link del post, nei widgets <small>(richiede Posts Widget plugin)</small>";
$lang["vote_settings_vote_on_url_click"] = "Automaticamente conta un utente quando clicca il link URL";

?>
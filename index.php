<?php

	/*
	 *	-> benötigte Dateien includen
	 */

	include ("inc/function.php");	
	include ("inc/termine.php");
	include ("inc/main.php");
	include ("inc/rss.php");
	
	/*
	 *	-> Instanz der Klasse MySql
	 *	-> stellt die Datenbank-Verbindung her
	 */
	
	$mysql = new MySql();
	
	/*
	 *	-> Instanz der Klasse Admin
	 *	-> wird für die Administration verwendet
	 */
	
	$admin = new Admin($mysql);
	
	/*
	 *	-> Fallauswahl zur Programmausführung
	 *	-> erfolgt in Abhängigkeit von "$_REQUEST['action']"
	 */
	 	
	if (isset($_REQUEST['action'])) {
		$action = strtolower($_REQUEST['action']);
		
		// Beispiel der debug-Function
		//debug($_REQUEST['action']);
		
		switch ($action) {
			case "change_menu":
				// wenn Kalender-Menupunkt aktiv
				if ($_GET['id'] == 8) {
					show_calender($mysql, date("m"));	// aktueller Monat wird mit übergeben
				} else {
					main($mysql, $_GET['id'], true);				
				}			
				break;
			case "box_update":		
				$menu_id = explode("navi", $_GET['menu_id']);
				$menu_id = $menu_id[1];
				$update = new Box($mysql, $_GET['id'], $menu_id, false);
				$update->update_box_position($_GET['top'], $_GET['left'], $menu_id);	
				break;
			case "edit_notizen":
				$edit = new Box($mysql, $_POST['id'], 1, false);
				$edit->edit_notizen($_POST['notiz_text']);
				break;				
			case "show_admin":
				$admin->show_admin();
				break;
				
			//	alles was mit Box zutun hat
			
			case "show_box":
				$admin->show_box($_REQUEST['id']);
				break;	
			case "show_all_box":
				if (isset($_REQUEST['id'])) {
					$admin->show_all_box($_REQUEST['id']);
				} else {
					$admin->show_all_box();
				}
				break;
			case "new_box":
				$admin->new_box($_REQUEST['id']);
				break;
			case "insert_box":
				$admin->insert_box($_REQUEST['Name'], $_REQUEST['Farbe'], $_REQUEST['Menu'], $_REQUEST['Art']);
				break;		
			case "edit_box":
				$admin->edit_box($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "update_box":
				$admin->update_box($_REQUEST['Name'], $_REQUEST['Farbe'], $_REQUEST['what'], $_REQUEST['id']);
				break;
			case "remove_box":
				$admin->remove_box ($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "delete_box":
				$admin->delete_box ($_REQUEST['what'], $_REQUEST['id']);
				break;
				
			//	alles was mit Links zutun hat
				
			case "new_link":
				$admin->new_link($_REQUEST['id']);
				break;
			case "insert_link":
				$admin->insert_link($_REQUEST['Box'], $_REQUEST['Name'], $_REQUEST['URL']);
				break;
			case "edit_link":
				$admin->edit_link($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "update_link":		
				$admin->update_link($_REQUEST['what'], $_REQUEST['id'], $_REQUEST['name'], $_REQUEST['url']);
				break;
			case "remove_link":
				$admin->remove_link($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "delete_link":
				$admin->delete_link($_REQUEST['what'], $_REQUEST['id']);
				break;
				
			//	alles was mit Menu zutun hat
				
			case "show_menu":
				$admin->show_menu();
				break;
			case "new_menu":
				$admin->new_menu();
				break;
			case "insert_menu":
				$admin->insert_menu($_REQUEST['Name'], $_REQUEST['Beschreibung']);
				break;	
			case "edit_menu":
				$admin->edit_menu($_REQUEST['id']);
				break;
			case "update_menu":
				$admin->update_menu($_REQUEST['Name'], $_REQUEST['id']);
				break;
			case "remove_menu":
				$admin->remove_menu($_REQUEST['id']);
				break;
			case "delete_menu":
				$admin->delete_menu($_REQUEST['id']);
				break;
				
			//	Termin aktualisieren
			
			case "get_termin":
				get_termin($mysql, $_REQUEST['id'], $_REQUEST['month'], true);
				break;
			case "edit_termin":
				get_form();
				break;
				
			// Kalender aktualisieren
			
			case "show_calender":
				show_calender($mysql, $_REQUEST['month']);
				break;
				
			// Rss
			
			case "get_rss":
				echo get_rss($mysql, $_REQUEST['feed']);
				break;
			case "new_rss":
				$admin->new_rss($_REQUEST['id']);
				break;
			case "insert_rss":
				$admin->insert_rss($_REQUEST['id'], $_REQUEST['Name'], $_REQUEST['URL']);
				break;
			case "edit_rss":
				$admin->edit_rss($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "update_rss":		
				$admin->update_rss($_REQUEST['what'], $_REQUEST['id'], $_REQUEST['name'], $_REQUEST['url']);
				break;
			case "remove_rss":
				$admin->remove_rss($_REQUEST['what'], $_REQUEST['id']);
				break;
			case "delete_rss":
				$admin->delete_rss($_REQUEST['what'], $_REQUEST['id']);
				break;	
				
			// Thema
			
			case "show_theme":
				$admin->show_theme();
				break;
			case "edit_theme":
				$admin->edit_theme($_REQUEST['id']);
				break;
			case "update_theme":
				$admin->update_theme($_REQUEST['id'], $_REQUEST['Farbe']);
				break;
				
			//	default
		
			default:
				die('<p class="error">Fehler 404 - Seite wurde nicht gefunden</p>');
				break;
		}
	} else {
		//get_body_css($mysql);
		get_box_css($mysql);
		display_html($mysql);
	}
	
?>
<?php

	/**
	 *	-> Diese Function gibt den html-Teil der Seite aus
	 */
	
	function display_html ($mysql) {	
		echo <<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Startseite</title>  
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <link rel="stylesheet" type="text/css" href="style/box.css" />
    <script type="text/javascript" src="js/mootools-core-1.4.5.js"></script>
    <script type="text/javascript" src="js/mootools-more-1.4.0.1.js"></script>
    <script type="text/javascript" src="js/function.js"></script> 
  </head>
  <body>
  	<div id="container">\n
EOT;
	show_menu($mysql);
	main($mysql, 1, false);

		echo <<< EOT
		</div>
	</div>
  </body>
</html>
EOT;
	}

	/**
	 *	-> Dies Function gibt das Menü aus
	 */
	 
	function show_menu ($db) {
		$db->set("what" , "ID AS id, Name");
		$db->set("from" , "Menu");
		$db->set("where",  0);
		$db->set("order", "Anordnung ASC");
		$db->set("show" ,  0);
		
		$res = $db->do_query ();
		
		if ($res->num_rows > 0) {
			echo '<div id="menu">
					<ul id="navi">';
					
			while ($rs = $res->fetch_object()) {
				if ($rs->id == 1) {
					echo '	<li id="navi1" class="active"><span id="navi_span1">' . $rs->Name . '</span></li>';
				} 
				else 
				{
					echo '	<li id="navi' . $rs->id . '" class="inactive"><span id="navi_span' . $rs->id . '">' . $rs->Name . '</span></li>';
				}
	 		}
	 		
	 		echo '	<li id="admin_navi" class="inactive"><span id="admin_span">Admin</span></li>';
	 			 		
	 		echo '	</ul>
	 			  </div>
	 			  <div id="main">';
	 	} 
	 	else 
	 	{
	 		echo '<p class="error"><b>Fehler:</b> keine Datensätze vorhanden</p>';
		}
	}

	/**
	 *	-> Diese Function gibt alle Boxen und Notizen aus, die zu einem bestimmten Menüpunkt gehören
	 */

	function main ($db, $id, $inline_style) {				
		$db->set("what" , "Box.ID AS id, Box.Art_ID AS Art");
		$db->set("from" , "Box INNER JOIN con_Menu_Box ON Box.ID = con_Menu_Box.Box_ID INNER JOIN Menu ON con_Menu_Box.Menu_ID = Menu.ID");
		$db->set("where", "Menu.ID = '" . $id . "'");
		$db->set("order",  0);
		$db->set("show" ,  0);
		
		$res = $db->do_query ();
		
		if ($res->num_rows > 0) {
			while ($rs = $res->fetch_object()) {
			
				/* 
				 *	Links-Teil 
				 */
			
				$db->set("what" , "Name, Beschreibung, URL, Span_ID");
				$db->set("from" , "Links");
				$db->set("where", "Box_ID = '" . $rs->id . "' AND Anzeigen = '1'");
				$db->set("order", "ID");
				$db->set("show" ,  0);
				
				$res2 = $db->do_query ();
				
				if ($res2->num_rows > 0) {
					$box = new Box ($db, $rs->id, $id, $rs->Art, $inline_style);		
					
					$text = "";
					
					while ($rs2 = $res2->fetch_object()) {						
						$text.= draw_anchor($rs2->URL, $rs2->Name, $rs2->Name) . "<br />";
					}

					$box->set_content($text);
					$box->draw_box();
				}
				
				/*
				 *	Notizen-Teil
				 */
				 
				$db->set("what" , "Text");
				$db->set("from" , "Notizen");
				$db->set("where", "Box_ID = $rs->id");
				$db->set("order",  0);
				$db->set("show" ,  0);
				
				$res3 = $db->do_query ();
				
				if ($res3->num_rows > 0) {
					$box = new Box ($db, $rs->id, $id, $rs->Art, $inline_style);
					
					$rs3 = $res3->fetch_object();
					
					$text = '<div id="notiz_box'.$rs->id.'" class="div_notiz_box">' . nl2br($rs3->Text) . '</div>';
								
					$box->set_content($text);
					$box->draw_box();
				}
				
				/*
				 *	Termin-Teil
				 */
				
				if ($rs->Art == 3) {
					$box = new Box ($db, $rs->id, $id, $rs->Art, $inline_style);
					$text = '<div id="termin' . $rs->id . '">' . get_termin($db, $rs->id, date("m"), false) . '</div>';
					$box->set_content($text);
					$box->draw_box();
				}

				/*
				 *	Rss-Nachichten
				 */
				 
				if ($rs->Art == 4) {
					$box = new Box ($db, $rs->id, $id, $rs->Art, $inline_style);					
					
					$db->set("what" , "Feed_Name");
					$db->set("from" , "Rss");
					$db->set("where", "Box_ID = '$rs->id'");
					$db->set("order",  0);
					$db->set("show" ,  0);
					
					$text = '<div id="feed_menu">';
					$res_feed = $db->do_query ();
					
					if ($res_feed->num_rows > 0) {
						while ($rs_feed = $res_feed->fetch_object()) {
							if ($rs_feed->Feed_Name == "Spiegel") {
								$class = 'active';
							} else {
								$class = 'inactive';
							}
							
							$text.= '<span id="get_rss:box' . $rs->id . '_' . $rs_feed->Feed_Name . '" class="action_span ' . $class . '">' . $rs_feed->Feed_Name . '</span>';
						}
					}
					
					$text.= '</div><div id="feed_news">';					
					$text.= get_rss($db, "Spiegel");
					$text.= '</div>';					
					
					$box->set_content($text);
					$box->draw_box();
				}
				
			}
		} else {
			echo '<p class="error"><b>Fehler:</b> keine Datensätze vorhanden</p>';
		}
	}
	
	
	/**
	 *	-> Diese Funktion erzeugt die Box.css Datei
	 */
	function get_box_css($db) {
		$file = "style/box.css";
		
		if (file_exists($file)) {				
			$db->set("what" , "Box.ID AS id, Box.Name AS name, Box.Farbe AS farbe, con_Menu_Box.Box_Top AS box_top, con_Menu_Box.Box_Left AS box_left, Menu.ID as mid");
			$db->set("from" , "Box INNER JOIN con_Menu_Box ON Box.ID = con_Menu_Box.Box_ID INNER JOIN Menu ON con_Menu_Box.Menu_ID = Menu.ID");
			$db->set("where",  0);
			$db->set("order",  0);
			$db->set("show" ,  0);
			
			$res = $db->do_query();
		
			if ($res->num_rows > 0) {
				$css = "/* Box */\n\n";
			
				while ($rs = $res->fetch_object()) {
					$css.= "#box$rs->id.menu$rs->mid {\n   background-color: #$rs->farbe; \n   top: $rs->box_top; \n   left: $rs->box_left; \n}\n\n";

				}
			} else {
				$css = "";
			}
			
			$handle = fopen($file, "w");
			fputs($handle, $css);
			fclose($handle);
		} else {
			die("<p>Fehler: Datei nicht gefunden!</p>");
		}
	}
	
	/**
	 *	-> Diese Function erzeugt die body.css - 6D87D6 & 062170
	 */
	 function get_body_css($db) {	 
		$file = "style/body.css";
		
		if (file_exists($file)) {		
		
			$db->set("what" , "ID, Bereich, Farbe");
			$db->set("from" , "Thema");
			$db->set("where",  0);
			$db->set("order",  0);
			$db->set("show" ,  0);
			
			$res = $db->do_query ();
		
			if ($res->num_rows > 0) {
				$body_farbe = "";
				$menu_farbe = "";
				$link_farbe = "";

			
				while ($rs = $res->fetch_object()) {
					switch ($rs->Bereich) {
						case "Menu":
							$menu_farbe = $rs->Farbe;
							break;
						case "Hintergrund":
							$body_farbe = $rs->Farbe;
							break;
						case "Aktiver Menupunkt":
							$link_farbe = $rs->Farbe;
							break;
					}
				}
			} else {
				$css = "";
			}
			
			$css = <<<EOT
/*	body   */

body { 
	background-color: #$body_farbe;
	font: 14px/20px "Helvetica Neue", Helvetica, Arial, sans-serif;
}

/*	menu   */

#menu {
	position: absolute;
	background-color: #$menu_farbe;
	top: 0px;
	left: 0px;
	width: 100%;
	min-width: 800px; 
	height: 40px;
}

/*	navi   */

#navi span {
	font-weight: bold; 
	text-decoration: none; 
	text-transform: none; 
	cursor: pointer;
}

#navi > li {
	display: inline;
	text-decoration: none;
	padding: 0.5em 1em;
	margin: 0em 1em;
}

#navi > .active {
	color: #$link_farbe;
	background-color: #$body_farbe;
	border-bottom: 1px solid #$body_farbe;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topright: 5px;
	-moz-border-radius-topleft: 5px;
}

#navi .inactive {
	color: #B8BCCD;
}


#navi .inactive:hover {
	color: #fff;
}
EOT;
			
			$handle = fopen($file, "w");
			fputs($handle, $css);
			fclose($handle);
		} else {
			die("<p>Fehler: Datei nicht gefunden!</p>");
		}
	 }
	
?>
<?php

	/**
	 *	-> autoload-Function zum automatischen Einbinden aller PHP-Klassen
	 */

	function __autoload ($class_name) {		
   		require_once 'class_' . strtolower($class_name) . '.php';
	}
  
	/**
	 *	-> Diese Function gibt einen übergebenen Text in einer bestimmten Farbe zurück
	 */
	 
	function set_color ($text, $color) {
		return '<span style="color: #' . $color . ';">'. $text . '</span>';
	}
	 
	/**
	 *	-> Diese Function überprüft, ob der übergebene Wert eine Ganzzahl ist
	 *	-> gibt 1 zurück, wenn Ganzzahl
	 *	-> gibt 2 zurück, wenn keine Ganzzahl
	 */
	 
	function if_int ($zahl) {
		if (is_int($zahl / 2) == true) {
			return 1;
		} else {
			return 2;
		}
	 }
	 
	/**
	 *	-> Diese Function setzt ein Auswahlfeld (Radiobutton) auf selected, wenn $x und $y gleich sind
	 */
	 
	function if_selected ($x, $y) {
		if ($x == $y) {
			return 'selected="selected"';
		} else {
			return '';
		}
	}
	
	/**
	 *	-> erzeugt einen Link, aus übergebenen Parametern
	 */
	 
	function draw_anchor ($href, $title, $text) {
		if (isset($title) && trim($title) != "") {
			$new_title = ' title="' . $title . '"';
		} else {
			$new_title = "";
		}
	
		$ausgabe = '<a href="' . $href . '" ' . $new_title . '>' . $text . '</a>';
		
		return $ausgabe;
	}
  
	/**
	 *	-> Diese Function wandelt die SQL-Datetime in ein normales Datum oder Uhrzeit um
	 *	-> wenn der Parameter $para: true  -> dann wird das Datum und die Uhrzeit zurück gegeben
	 *								 false -> dann wird nur das Datum zurück gegeben
	 */

	function sql_to_date ($date, $para) {
		$date_array1 = explode(" ", $date);
		$date_array2 = explode("-", $date_array1[0]);
    
		$new_date = $date_array2[2] . '.' . $date_array2[1] . '.' . $date_array2[0];
    
		if ($para == true) {
			$new_date.= ' - ' . $date_array1[1] . ' Uhr';
		}
		
		return $new_date;
	}
	
	/**
	 *	-> Diese Function gibt den Monat zurück
	 */
	 
	function get_month($m) {
		switch($m) {
			case "01":
				$month = "Januar";
				break;
			case "02":
				$month = "Februar";
				break;
			case "03":
				$month = "M&auml;rz";
				break;
			case "04":
				$month = "April";
				break;
			case "05":
				$month = "Mai";
				break;
			case "06":
				$month = "Juni";
				break;
			case "07":
				$month = "Juli";
				break;
			case "08":
				$month = "August";
				break;
			case "09":
				$month = "September";
				break;
			case "10":
				$month = "Oktober";
				break;
			case "11":
				$month = "November";
				break;
			case "12":
				$month = "Dezember";
				break;
			default:
				$month = "Fehler";
				break;
		}
		
		return $month;
	}
	
	/**
	 *	-> diese Funktion gibt den Wochentag einer übergebenen Zahl zurück
	 */
	 
	function get_wochentag ($tag) {
		switch ($tag) {
			case 1:
				$wt = "Montag";
				break;
			case 2:
				$wt = "Dienstag";
				break;
			case 3:
				$wt = "Mittwoch";
				break;
			case 4:
				$wt = "Donnerstag";
				break;
			case 5:
				$wt = "Freitag";
				break;
			case 6:
				$wt = "Samstag";
				break;
			case 7:
				$wt = "Sonntag";
				break;
			default:
				break;
		}
		
		return $wt;
	}
	
	/**
	 *	-> Gibt eine Box aus
	 */
	
	function alert($art, $text1, $text2) {
		switch ($art) {
			case "alert":
				$class = "alert";
				break;
			case "error":
				$class = "error";
				break;
			default:
				$class = "error";
				break;
		}
		
		$ausgabe = <<<EOT
<div class="$class">
	<p>$text1</p>
	$text2
</div>	
EOT;
		
		return $ausgabe;
	}
	
	/**
	 * -> Zeigt alle Werte die der Function übergeben werden, in einer roten Box oben rechts, an
	 * -> ist nützlich um sich den Inhalt von Parametern anzeigen zu lassen
	 */

	function debug () {
		$args = func_get_args(); 
		
		echo '<div class="debug">';		
		
		foreach ($args as $a) {   
        	echo '<p>Wert: ' . strtolower($a) . '</p>';
    	}  
	
		echo '</div>';		
	}

?>
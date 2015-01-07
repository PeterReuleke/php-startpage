<?php

	/**
	 *	-> Diese Function gibt den Inhalt einer Termin-Box aus
	 */

	function get_termin ($db, $box_id, $monat, $echo) {
		$db->set("what" , "Text, Datum");
		$db->set("from" , "Termine");
		$db->set("where", "Box_ID = $box_id");
		$db->set("order", "Datum ASC");
		$db->set("show" ,  0);
		
		$res = $db->do_query ();
		
		if ($res->num_rows > 0) {		
			if ($monat-1 > 0) {
				$text = '<span id="month:' . $box_id . '_' . ($monat-1) . '" class="action_span termin_left month">' . htmlentities('<< ', ENT_HTML5,  "ISO-8859-1") . get_month((string) $monat-1) . '</span>';
			}
			
			if ($monat+1 < 13) {
				$text = '<span id="month:' . $box_id . '_' . ($monat+1) . '" class="action_span termin_right month">' . get_month((string) $monat+1) . htmlentities(' >>', ENT_HTML5,  "ISO-8859-1") . '</span>';
			}
				
			$text.= '<ul class="termin">';
			
			while ($rs = $res->fetch_object()) {					
				$date = preg_split("/\./", $rs->Datum);
				
				if ($date[0] == $monat) {
					if ($date[1] == date("d") && $date[0] == date("m")) {
						$text.= '<li class="today">' . $date[1] . '.' . $date[0] . '.' . date("Y") . '  -  ' . htmlentities($rs->Text, ENT_HTML5,  "ISO-8859-1") . '</li>'; 
					}
					else
					{
						$text.= '<li>'. $date[1] . '.' . $date[0] . '.' . date("Y") . '  -  ' . htmlentities($rs->Text, ENT_HTML5,  "ISO-8859-1") . '</li>'; 
					}
				}			
			}
			
			$text .= '</ul>'; 
			

		}
		else 
		{
			$text = 'kein Inhalt';
		}
			
		if ($echo == true) {
			echo $text;
		}	
		else
		{
			return $text;
		}
	}


?>
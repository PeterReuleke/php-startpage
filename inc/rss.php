<?php

	function get_rss ($db, $feed_name) {		
		$db->set("what" , "Feed_Adresse");
		$db->set("from" , "Rss");
		$db->set("where", "Feed_Name = '$feed_name'");
		$db->set("order",  0);
		$db->set("show" , "Feed_Adresse");
		
		$feed = $db->do_query ();

		if (file_get_contents($feed) != "") {
			$rss_file = join(' ', file($feed));
							
			$rss_array = explode("<item>", $rss_file );
			
			$i = 0;
			$text = '';
			
			foreach ($rss_array as $news) {
				
				if ($i < 20) {
					$title = parse_rss("title>", $news);
					$link = parse_rss("link>", $news);
					
					if ($i > 0) {
						$title_array = explode(":", $title);
						
						switch ($feed_name) {
							case "Spiegel":
								$title0 = htmlentities($title_array[0], ENT_HTML5,  "ISO-8859-1");
								$title1 = htmlentities($title_array[1], ENT_HTML5,  "ISO-8859-1");
								
								$text.= '<a style="font-weight: normal; font-size: 13px;" href="' . $link . '"><b>' . $title0 . '</b>:' . $title1 . '</a><br />';	
								break;
							case "Stern":
								$title0 = $title_array[0];
								$title1 = $title_array[1];
								
								$text.= '<a style="font-weight: normal; font-size: 13px;" href="' . $link . '"><b>' . $title0 . '</b>:' . $title1 . '</a><br />';	
								break;
							case "Bild":							
								if (count($title_array) == 1) {
									$title0 = $title_array[0];
								
									$text.= '<a style="font-weight: normal; font-size: 13px;" href="' . $link . '"><b>' . $title0 . '</b></a><br />';
								} else {
									$title0 = $title_array[0];
									$title1 = $title_array[1];								

									$text.= '<a style="font-weight: normal; font-size: 13px;" href="' . $link . '"><b>' . $title0 . '</b>:' . $title1 . '</a><br />';
								}	
								break;
							default:
								$title0 = $title_array[0];
								$title1 = $title_array[1];
								
								$text.= '<a style="font-weight: normal; font-size: 13px;" href="' . $link . '"><b>' . $title0 . '</b>:' . $title1 . '</a><br />';	
								break;
						}					
					}
				}

				
				$i++;
			}
			
			return $text;		
		} else {
			return "<p>RSS-Feed nicht gefunden</p>";
		}

	}

	function parse_rss ($tag, $feed) {
		$ret = explode($tag, $feed);
		return substr($ret[1], 0, -2);
	}



?>


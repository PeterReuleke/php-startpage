<?php

class Admin {
	private $db;
	private $art;
	
	/**
	 *	-> Konstruktor 
	 */
	
	public function __construct ($sql) {
		$this->db = $sql;		
	}
	
	/**
	 *	-> Diese Function gibt die Admin-Maske aus
	 */
	 
	 public function show_admin () {
	 	echo <<<EOT
			<div id="admin">
				<div id="admin_menu">
					<table>
						<tr>
							<th class="th3">Menu verwalten</th>
						</tr>
						<tr>
							<td class="td2 td3"><span id="show_menu" class="action_span">Alle Anzeigen</span></td>
						</tr>
						<tr>
							<td class="td1 td3"><span id="show_theme" class="action_span">Thema ändern</span></td>
						</tr>
					</table>
					<br />
					<table>
						<tr>
							<th class="th3">Boxen verwalten</th>
						</tr>
						<tr>
							<td class="td2 td3"><span id="show_all_box" class="action_span">Alle</span></td>
						</tr>
EOT;

		$this->db->set("what" , "ID, Name");
		$this->db->set("from" , "Menu");
		$this->db->set("where",  0);
		$this->db->set("order", "Anordnung ASC");
		$this->db->set("show" ,  0);

		$res = $this->db->do_query ();	
		
		$i = 0;
		
		while ($rs = $res->fetch_object()) {		
			echo '<tr>
					<td class="td' . if_int($i) . ' td3"><span id="show_all_box:id_' . $rs->ID . '" class="action_span">' . $rs->Name . '</span></td>
				  </tr>';
			$i++;
		}


		echo '	</table>
			</div>
			<div id="admin_main">';

		// ...

		echo '		</div>
				</div>';
	}	

	/**
	 *	-> Diese Function zeigt alle Boxen an
	 */
	 
	 public function show_all_box ($id = 0) {	 
	 	if ($id != 0) {
	 		$from = "Box, con_Menu_Box, Menu";
	 		$where = "Box.ID = con_Menu_Box.Box_ID AND con_Menu_Box.Menu_ID = Menu.ID AND Menu.ID = $id";
	 	} else {
	 		$from = "Box";
	 		$where = 0;
	 	}
	 
		$this->db->set("what" , "Box.ID, Box.Name, Box.Farbe");
		$this->db->set("from" ,  $from);
		$this->db->set("where",  $where);
		$this->db->set("order", "ID");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		
		if ($res->num_rows > 0) {		
			$i = 1;
		
			echo '<table>
					<tr>
						<th class="th3">Name</th>
						<th class="th2">Farbe</th>
						<th class="th1"></th>
						<th class="th1"></th>
					</tr>';
					
			while ($rs = $res->fetch_object()) {
				echo '<tr>
						<td class="td' . if_int($i) . '">
							<span id="show_Box:' . $rs->Name . '_' . $rs->ID . '" class="action_span">' . $rs->Name . '</span>
						</td>
						<td class="td' . if_int($i) . '">' . set_color($rs->Farbe, $rs->Farbe) . '</td>
						<td class="td' . if_int($i) . '">
							<span id="edit_box:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/edit.gif" class="pic" /></span>
						</td>
						<td class="td' . if_int($i) . '">
							<span id="remove_box:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/delete.gif" class="pic" /></span>
						</td>
				  </tr>';
				  
				 $i++;					  
			}
			echo '</table>';
			
		} else {
			echo '<p>keine Boxen vorhanden</p>';
		}
		
		echo '<br /><span id="new_box:id_' . $id . '" class="action_span">Neue Box hinzufügen</span><br />';
	 }
	 
	/**
	 *	-> Diese Function gibt die Maske zur Bearbeitung einer Box aus
	 *	-> ID: 1 = Link-Box
	 *	-> ID: 2 = Notiz-Box
	 *	-> ID: 3 = RSS-Box (noch nicht realisiert)
	 */
	 
	public function show_box ($id = 0) {	
		$this->db->set("what" , "Art_ID");
		$this->db->set("from" , "Box");
		$this->db->set("where", "ID = '$id'");
		$this->db->set("order",  0);
		$this->db->set("show" , "Art_ID");
					
		$art_id = $this->db->do_query ();
		
		switch ($art_id) {
			case 1:
				$this->db->set("what" , "Links.ID, Links.Name, Links.URL");
				$this->db->set("from" , "Links INNER JOIN Box ON Links.Box_ID = Box.ID");
				$this->db->set("where", "Box.ID = '$id'");
				$this->db->set("order", "Links.ID ASC");
				$this->db->set("show" ,  0);
							
				$res = $this->db->do_query ();

				echo <<<EOT
					<table id="sortierbar">
						<tr>
							<th class="th3">Name</th>
							<th class="th4">URL</th>
							<th class="th1"></th>
							<th class="th1"></th>
						</tr>
EOT;
				$i = 1;

				while ($rs = $res->fetch_object()) {
					echo '<tr>
							<td class="td' . if_int($i) . '">' . $rs->Name . '</td>
							<td class="td' . if_int($i) . '">' . $rs->URL . '</td>
							<td class="td' . if_int($i) . '"><span id="edit_link:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/edit.gif" class="pic" title="Bearbeiten" /></span></td>
							<td class="td' . if_int($i) . '"><span id="remove_link:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/delete.gif" class="pic" title="Löschen" /></span></td>
						  </tr>';					
					$i++;
				}
				echo '</table><br /><span id="new_link:id_' . $id . '" class="action_span">Neuen Link hinzufügen</span>';
				
				break;
			case 2:
				echo '<p class="error">Notiz-Boxen können nur direkt in der jeweiligen Box bearbeitet werden</p>';
				break;
			case 3:
				$this->db->set("what" , "Termine.ID, Termine.Text, Termine.Datum");
				$this->db->set("from" , "Termine INNER JOIN Box ON Termine.Box_ID = Box.ID");
				$this->db->set("where", "Box.ID = '$id'");
				$this->db->set("order", "Termine.ID ASC");
				$this->db->set("show" ,  0);
							
				$res = $this->db->do_query ();

				echo <<<EOT
					<table>
						<tr>
							<th class="th3">Text</th>
							<th class="th2">Datum</th>
							<th class="th1"></th>
							<th class="th1"></th>
						</tr>
EOT;
				$i = 1;

				while ($rs = $res->fetch_object()) {
					echo '<tr>
							<td class="td' . if_int($i) . '">' . htmlentities($rs->Text) . '</td>
							<td class="td' . if_int($i) . '">' . $rs->Datum . '</td>
							<td class="td' . if_int($i) . '"><span id="edit_termin:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/edit.gif" class="pic" title="Bearbeiten" /></span></td>
							<td class="td' . if_int($i) . '"><span id="remove_termin:' . $id . '_' . $rs->ID . '" class="action_span"><img src="img/delete.gif" class="pic" title="Löschen" /></span></td>
						  </tr>';					
					$i++;
				}
				echo '</table><br /><span id="new_termin:id_' . $id . '" class="action_span">Neuen Termin hinzufügen</span>';
			case 4:
				// RSS
				break;
			default:
				echo '<p class="error">Fehler</p>';
				break;
		}
	}

	/**
	 *	-> Diese Function gibt die Maske zum Erstellen einer neuen Box aus
	 */
	 
	public function new_box ($id) {
		$this->show_all_box($id);
	
		echo <<<EOT
			<div id="alert_box">
			<table>
				<tr>
					<td>Name</td>
					<td><input type="text" id="Name" class="input"><td>
				</tr>
				<tr>
					<td>Farbe</td>
					<td><input type="text" id="Farbe"  class="input"><td>
				</tr>
				<tr>
					<td>Menu-Tab</td>
					<td>
EOT;
		$this->db->set("what" , "ID, Name");
		$this->db->set("from" , "Menu");
		$this->db->set("where",  0);
		$this->db->set("order", "Anordnung ASC");
		$this->db->set("show" ,  0);
					
		$res = $this->db->do_query ();		
		
		while ($rs = $res->fetch_object()) {
			echo ' <input type="checkbox" name="checkbox' . $rs->ID . '" class="checkbox" value="' . $rs->ID . '">' . $rs->Name . '<br />';
		}	
							
		echo <<<EOT
					</td>
				</tr>
				<tr>
					<td>Art</td>
					<td><select id="Art" class="input">
EOT;
				
		$this->db->set("what" , "ID, Art");
		$this->db->set("from" , "Box_Art");
		$this->db->set("where",  0);
		$this->db->set("order",  0);
		$this->db->set("show" ,  0);
					
		$res = $this->db->do_query ();	
		
		while ($rs = $res->fetch_object()) {
			echo '<option value="' . $rs->ID . '">' . $rs->Art . '</option>';
		}
				
		echo <<<EOT
					</select></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<span id="insert_box" class="action_span">Hinzufügen</span>
						<span id="show_all_box:id_$id" class="action_span">Abbrechen</span>
					</td>
			</table>
			</div>
EOT;
		$this->farb_tool();
	}
	
	/**
	 *	-> Diese Function erstellt eine neue Box
	 */
	 
	 public function insert_box ($name, $farbe, $menu, $art) {	 
	 	$name = htmlentities($name);
	 	
	 	$menu_id = explode(",", $menu);
	 	
	 	if (count($menu_id) > 1) {
	 		if (trim($name) == "" or trim($farbe) == "" or trim($art) == "") {
		 		echo '<div class="error"><p>Fehler: unvollständige Eingabe</p></div>';
		 		$this->show_box($id);
		 		die();
	 		}

			$sql = "INSERT INTO Box ( Name, Farbe, Anzeigen, Art_ID ) VALUES ( ?, ?, 1, ? )";		
			$res = $this->db->prepare($sql);
			$res->bind_param('ssi', $name, $farbe, $art);
			$res->execute();
			
			$this->db->set("what" , "ID");
			$this->db->set("from" , "Box");
			$this->db->set("where", "Name = '$name' AND Farbe = '$farbe'");
			$this->db->set("order",  0);
			$this->db->set("show" , "ID");
			
			$id = $res = $this->db->do_query ();

			for ($i = 0; $i < (count($menu_id)-1); $i++) {
				$sql = "INSERT INTO con_Menu_Box ( Menu_ID, Box_ID, Box_Top, Box_Left ) VALUES ( ?, ?, '100px', '100px')";		
				$res = $this->db->prepare($sql);
				$res->bind_param('ii', $menu_id[$i], $id);
				$res->execute();	
			}
			
			if ($art == 2) {
			 	$text = "...";
			 
				$sql = "INSERT INTO Notizen ( Text, Datum, Box_ID ) VALUES ( ?, NOW(), ? )";
				$res = $this->db->prepare($sql);
				$res->bind_param('si', nl2br($text), $id);
				$res->execute();
			}
			
			echo '<div id="alert_box"><p>Box wurde erstellt</p></div>';
	 	} else {
	 		echo '<div class="error"><p>Fehler: unvollständige Eingabe</p></div>';
	 		$this->show_all_box();
	 		die();
	 	}
	 }
	 
	/**
	 *	-> Diese Function gibt die Maske zur Bearbeitung einer Box aus
	 *	-> ID: 1 = Link-Box
	 *	-> ID: 2 = Notiz-Box
	 *	-> ID: 3 = Termin-Box
	 *	-> ID: 4 = RSS-Box
	 */
	 
	public function edit_box ($box_id, $id) {	 
		$this->db->set("what" , "Box.ID, Box.Name, Box.Farbe");
		$this->db->set("from" , "Box, con_Menu_Box, Menu");
		$this->db->set("where", "Box.ID = con_Menu_Box.Box_ID AND con_Menu_Box.Menu_ID = Menu.ID AND Menu.ID = $box_id");
		$this->db->set("order", "ID");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		
		if ($res->num_rows > 0) {		
			$i = 1;
		
			echo '<table>
					<tr>
						<th class="th3">Name</th>
						<th class="th2">Farbe</th>
						<th class="th1"></th>
						<th class="th1"></th>
					</tr>';
					
			while ($rs = $res->fetch_object()) {			
				if ($id == $rs->ID) {
					echo '<tr>
							<td class="td' . if_int($i) . '"><input type="text" id="Name" class="input" value="' . $rs->Name . '"></td>
							<td class="td' . if_int($i) . '"><input type="text" id="Farbe" class="input" value="' . $rs->Farbe . '"></td>
							<td class="td' . if_int($i) . '"><span id="update_box:' . $box_id . '_' . $id . '" class="action_span"><img src="img/check_green.gif" class="pic" /></span></td>
							<td class="td' . if_int($i) . '"><span id="show_all_box:id_' . $box_id . '" class="action_span"><img  src="img/delete.gif" class="pic" /></span></td>
						  </tr>';
				} else {
					echo '<tr>
							<td class="td' . if_int($i) . '">' . $rs->Name . '</td>
							<td class="td' . if_int($i) . '">' . set_color($rs->Farbe, $rs->Farbe) . '</td>
							<td class="td' . if_int($i) . '"></td>
							<td class="td' . if_int($i) . '"></td>
						  </tr>';	
				}
				  
				 $i++;					  
			}
			echo '</table>';
			
		} else {
			echo '<p>keine Boxen vorhanden</p>';
		}
		
		$this->farb_tool();
		
		echo '</div>';
	}
	
	/**
	 *	-> Diese Function ändert den Namen und die Farbe einer Box
	 */
	 
	public function update_box ($name, $farbe, $box_id, $id) {	
		$name = htmlentities($name);
	
 		if (trim($name) == "" or trim($farbe) == "" or trim($box_id) == "" or trim($id) == "") {
 			$this->show_all_box($box_id);
 			die('<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>');
 		}
 		
		$sql = "UPDATE Box SET Name = ?, Farbe = ? WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('ssi', $name, $farbe, $id);
		$res->execute();
		
		$this->show_all_box($box_id);
	}
	
	
	/**
	 *	-> Diese Function gibt ein Fenster aus, welches den Benutzer fragt, ob er die Box wirklich löschen will
	 */
	
	public function remove_box ($box_id, $id) {
		$this->show_all_box($box_id);
		
		echo '<div id="alert_box">
				<p>Wollen Sie diese Box wirklich löschen?</p>
				<span id="delete_box:' . $box_id . '_' . $id . '" class="action_span">Ja</span>
				<span id="show_all_box:Name_' . $box_id . '" class="action_span">Nein</span>
			  </div>';
	}
	
	/**
	 *	-> Diese Function löscht eine Box und alle mit ihr verbundenen Inhalte
	 */
	 
	 public function delete_box ($box_id, $id) {
	 	if (trim($box_id) == "" or trim($id) == "") {
	 		die('<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>');
	 	}
		
		$this->db->set("what" , "Art_ID");
		$this->db->set("from" , "Box");
		$this->db->set("where", "ID = '$id'");
		$this->db->set("order",  0);
		$this->db->set("show" , "Art_ID");
		
		$art = $this->db->do_query ();
		
		switch ($art) {
			case 1:
				$sql = "DELETE FROM Links WHERE Box_ID = ?";		
				$res = $this->db->prepare($sql);
				$res->bind_param('i', $id);
				$res->execute();
				break;
			case 2:
				$sql = "DELETE FROM Notizen WHERE Box_ID = ?";		
				$res = $this->db->prepare($sql);
				$res->bind_param('i', $id);
				$res->execute();
				break;
			default:
				echo "Fehler!";
		}
		
		$sql = "DELETE FROM con_Menu_Box WHERE Box_ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('i', $id);
		$res->execute();
		
		$sql = "DELETE FROM Box WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('i', $id);
		$res->execute();

		$this->show_all_box($box_id);
	 }
	
		
	/**
	 *	-> Diese Function zeigt alle Menupunkte an
	 */	
	 
	public function show_menu () {
		$this->db->set("what" , "ID, Name");
		$this->db->set("from" , "Menu");
		$this->db->set("where",  0);
		$this->db->set("order", "ID");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		
		if ($res->num_rows > 0) {		
			$i = 1;
		
			echo '<table>
					<tr>
						<th class="th3">Menupunkte</th>
						<th class="th1"></th>
						<th class="th1"></th>
					</tr>';
					
			while ($rs = $res->fetch_object()) {
				echo '<tr>
						<td class="td' . if_int($i) . '">' . $rs->Name . '</td>
						<td class="td' . if_int($i) . '"><span id="edit_menu:Name_' . $rs->ID . '" class="action_span"><img src="img/edit.gif" class="pic" /></span></td>
						<td class="td' . if_int($i) . '"><span id="remove_menu:Name_' . $rs->ID . '" class="action_span"><img src="img/delete.gif" class="pic" /></span></td>
					 </tr>';
							
				  
				 $i++;					  
			}
			echo '</table>';
			
		} else {
			echo '<p>keine Menupunkte vorhanden</p>';
		}
		
		echo '<br /><span id="new_menu" class="action_span">Neuen Punkt hinzufügen</span>';
	}
		
	/**
	 *	-> Diese Function gibt die Maske zum Erstellen eines neuen Menu_Tabs aus
	 */
	 
	public function new_menu () {
		$this->show_menu();
	
		echo <<<EOT
			<div id="alert_box">
			<table>
				<tr>
					<td>Name</td>
					<td><input type="text" id="Name" class="input"><td>
				</tr>
				<tr>
					<td>Beschreibung</td>
					<td><input type="text" id="Beschreibung"  class="input"><td>
				</tr>
				<tr>
					<td></td>
					<td>
						<span id="insert_menu" class="action_span">Hinzufügen</span>
						<span id="show_menu" class="action_span">Abbrechen</span>
					</td>
			</table>
			</div>
EOT;
	}
	
	/**
	 *	-> Diese Function erstellt einen neuen Menu-Tab
	 */
	 
	 public function insert_menu ($name, $beschreibung) {
	 	$name = htmlentities($name);
	 	$beschreibung = htmlentities($beschreibung);
	 
 		if (trim($name) == "") {
 			echo '<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>';
 			$this->show_menu();
 			die();
 		}
	 
		$sql = "INSERT INTO Menu ( Name, Beschreibung ) VALUES ( ?, ? )";		
		$res = $this->db->prepare($sql);
		$res->bind_param('ss', $name, $beschreibung);
		$res->execute();
				 
	 	$this->show_menu();
	 }
	
	/**
	 *	-> Diese Function gibt die Maske zur Bearbeitung eines Menu-Tabs aus
	 */
	 
	public function edit_menu ($id) {
		$this->db->set("what" , "ID, Name");
		$this->db->set("from" , "Menu");
		$this->db->set("where",  0);
		$this->db->set("order", "ID ASC");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();

		echo <<<EOT
			<table>
				<tr>
					<th class="th3">Name</th>
					<th class="th1"></th>
					<th class="th1"></th>
				</tr>
EOT;
		$i = 1;

		while ($rs = $res->fetch_object()) {
			if ($id == $rs->ID) {
				echo '<tr>
						<td class="td' . if_int($i) . '"><input type="text" id="Name" class="input" value="' . $rs->Name . '"></td>
						<td class="td' . if_int($i) . '"><span id="update_menu:Name_' . $id . '" class="action_span"><img src="img/check_green.gif" class="pic" /></span></td>
						<td class="td' . if_int($i) . '"><span id="show_menu:Name_' . $id . '" class="action_span"><img  src="img/delete.gif" class="pic" /></span></td>
					  </tr>';	
			} else {
				echo '<tr>
						<td class="td' . if_int($i) . '">' . $rs->Name . '</td>
						<td class="td' . if_int($i) . '"></td>
						<td class="td' . if_int($i) . '"></td>
					  </tr>';	
			}
				
			$i++;
		}
	}
	
	/**
	 *	-> Diese Function Bearbeitet einen bestimmten Menu-Tab
	 */
	 
	 public function update_menu ($name, $id) {	 
	 	$name = htmlentities($name);
	 
 		if (trim($name) == "" or trim($id) == "") {
 			echo '<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>';
 			$this->show_menu();
 			die();
 		}
 		
		$sql = "UPDATE Menu SET Name = ? WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('si', $name, $id);
		$res->execute();
		
		$this->show_menu();
	 }
	 
	/**
	 *	-> Diese Function gibt ein Fenster aus, welches den Benutzer fragt, ob er den Menupunt wirklich löschen will
	 */
	
	public function remove_menu ($id) {
		$this->show_menu();
		
		echo '<div id="alert_box">
				<p>Wollen Sie diesen Menupunkt wirklich löschen?</p>
				<span id="delete_menu:Name_' . $id . '" class="action_span">Ja</span>
				<span id="show_menu" class="action_span">Nein</span>
			  </div>';
	}
	
	/**
	 *	-> Diese Function löscht einen Link
	 */
	 
	public function delete_menu ($id) {
		if (trim($id) == "") {
	 		echo '<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>';
	 		$this->show_menu();
	 		die();
	 	}

		$sql = "DELETE FROM Menu WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('i', $id);
		$res->execute();

		$this->show_menu();
	}
	 
	/**
	 *	-> Diese Function gibt eine Maske zur Erstellung eines neuen Links aus
	 */
	 
	 public function new_link ($id) {

		$this->show_box($id);			
			
		echo <<<EOT
			<div id="alert_box">
			<table>
				<tr>
					<td>Name</td>
					<td><input type="text" id="Name" size="60" class="input"><td>
				</tr>
				<tr>
					<td>URL</td>
					<td><input type="text" id="URL" size="60" class="input"><td>
				</tr>
				<tr>
					<td>Welche Box</td>
					<td><select id="Box" class="input">
EOT;
				
		$this->db->set("what" , "ID, Name");
		$this->db->set("from" , "Box");
		$this->db->set("where",  0);
		$this->db->set("order",  0);
		$this->db->set("show" ,  0);
					
		$res = $this->db->do_query ();	
		
		while ($rs = $res->fetch_object()) {
			echo '<option value="' . $rs->ID . '" ' . if_selected($rs->ID, $id) . '>' . $rs->Name . '</option>';
		}
				
		echo <<<EOT
					</select></td>
				</tr>
				<tr>
					<td></td>
EOT;
		echo '<td>
				<span id="insert_link:id_' . $id . '" class="action_span">Hinzufügen</span>
				<span id="show_box:Name_' . $id . '" class="action_span">Abbrechen</span>
			 </td>
			</table>
			</div>';
			
	}
	
	/**
	 *	-> Diese Function fügt einen neuen Link hinzu
	 */
	 
	public function insert_link ($id, $name, $url) {
		$name = htmlentities($name);
		$url = htmlentities($url);
	
 		if (trim($id) == "" or trim($name) == "" or trim($url) == "") {
 			echo '<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>';
 			$this->edit_box($id);
 			die();
 		}

		$sql = "INSERT INTO Links ( Box_ID, Name, URL, Anzeigen ) VALUES ( ?, ?, ?, '1' )";		
		$res = $this->db->prepare($sql);
		$res->bind_param('iss', $id, $name, $url);
		$res->execute();

		$this->show_box($id);
	}
	
	/**
	 *	-> Diese Function gibt eine Maske zur Bearbeitung eines Links aus
	 */
	 
	public function edit_link ($box_id, $id) {
		$this->db->set("what" , "Links.ID, Links.Name, Links.URL");
		$this->db->set("from" , "Links INNER JOIN Box ON Links.Box_ID = Box.ID");
		$this->db->set("where", "Box.ID = '$box_id'");
		$this->db->set("order", "Links.ID ASC");
		$this->db->set("show" ,  0);
					
		$res = $this->db->do_query ();

		echo <<<EOT
			<table>
				<tr>
					<th class="th3">Name</th>
					<th class="th4">URL</th>
					<th class="th1"></th>
					<th class="th1"></th>
				</tr>
EOT;
		$i = 1;

		while ($rs = $res->fetch_object()) {
			if ($id == $rs->ID) {
				echo '<tr>
						<td class="td' . if_int($i) . '"><input type="text" id="name" class="input" value="' . $rs->Name . '"></td>
						<td class="td' . if_int($i) . '"><input type="text" id="url" class="input" size="60" value="' . $rs->URL . '"></td>
						<td class="td' . if_int($i) . '"><span id="update_link:' . $box_id . '_' . $rs->ID . '" class="action_span"><img src="img/check_green.gif" class="pic" /></span></td>
						<td class="td' . if_int($i) . '"><span id="show_box:Name_' . $box_id . '" class="action_span"><img  src="img/delete.gif" class="pic" /></span></td>
					  </tr>';	
			} else {
				echo '<tr>
						<td class="td' . if_int($i) . '">' . $rs->Name . '</td>
						<td class="td' . if_int($i) . '">' . $rs->URL . '</td>
						<td class="td' . if_int($i) . '"></td>
						<td class="td' . if_int($i) . '"></td>
					  </tr>';	
			}
				
			$i++;
		}
	}
	
	/**
	 *	-> Diese Function ändert einen Link
	 *
	 *	FEHLER: 
	 *			-> wenn man ein Formular (z.B.: Link-Bearbeiten) abschickt, und ein "&"-Zeichen im Feldinhalt enthalten ist, werden nicht alle Daten übertragen
	 *			-> es wird dann nur der Feldinhalt bis zu dem "&"-Zeichen übertragen
	 *			-> man könnte im Admin_EventHandler (in function.js) das "&"-Zeichen durch ein anderes Zeichen ersetzten, und dieses andere Zeichen
	 *			   dann im php-Skript wieder durch das "&" ersetzen (noch NICHT realisiert)
	 */
	 
	public function update_link ($box_id, $id, $name, $url) {
		$name = htmlentities($name);
		$url = htmlentities($url);
	
 		if (trim($box_id) == "" or trim($id) == "" or trim($name) == "" or trim($url) == "") {
			echo '<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>';
			$this->edit_box($box_id);
			die();
 		}

		$sql = "UPDATE Links SET Name = ?, URL = ? WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('ssi', $name, $url, $id);
		$res->execute();
		
		$this->show_box($box_id);
	}
	
	/**
	 *	-> Diese Function gibt ein Fenster aus, welches den Benutzer fragt, ob er den Link wirklich löschen will
	 */
	
	public function remove_link ($box_id, $id) {
		$this->show_box($box_id);
		
		echo '<div id="alert_box">
				<p>Wollen Sie diesen Link wirklich löschen?</p>
				<span id="delete_link:' . $box_id . '_' . $id . '" class="action_span">Ja</span>
				<span id="show_box:Name_' . $box_id . '" class="action_span">Nein</span>
			  </div>';
	}
	
	/**
	 *	-> Diese Function löscht einen Link
	 */
	 
	 public function delete_link ($box_id, $id) {
	 	if (trim($box_id) == "" or trim($id) == "") {
	 		die('<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>');
	 	}

		$sql = "DELETE FROM Links WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('i', $id);
		$res->execute();

		$this->show_box($box_id);
	 }
	 
	/**
	 *	-> Diese Function gibt das Tool zur Auswahl der Farbe aus
	 */
	 
	public function farb_tool () {
		echo <<<EOT
<div id="farb_tool">
	<h2 id="set_color">Farbtool</h2>
	<div id="red" class="slider advanced">
		<div class="knob">
		</div>
	</div>
	<div id="green" class="slider advanced">
		<div class="knob">
		</div>
	</div>
	<div id="blue" class="slider advanced">
		<div class="knob">
		</div>
	</div>
</div>
EOT;
	}
	
	/**
	 *	-> Diese Function gibt die Themen-Übersicht aus
	 */
	 
	public function show_theme ($id=0) {	
		$this->db->set("what" , "ID, Bereich, Farbe");
		$this->db->set("from" , "Thema");
		$this->db->set("where",  0);
		$this->db->set("order", "ID");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		
		if ($res->num_rows > 0) {	
			$i = 1;		
	
			echo <<<EOT
				<table>
					<tr>
						<th class="th3">Bereich</th>
						<th class="th2">Farbe</th>
						<th class="th1"></th>
					</tr>
EOT;
			$i = 1;

			while ($rs = $res->fetch_object()) {
				echo '<tr>
						<td class="td' . if_int($i) . '">' . $rs->Bereich . '</td>
						<td class="td' . if_int($i) . '">' . set_color($rs->Farbe, $rs->Farbe) . '</td>
						<td class="td' . if_int($i) . '"><span id="edit_theme:Name_' . $rs->ID . '" class="action_span"><img src="img/edit.gif" class="pic" title="Bearbeiten" /></span></td>
					  </tr>';					
				$i++;
			}
			echo '</table>';
		} else {
			echo "Fehler: kein Thema vorhanden.";
		}

	}
	
	/**
	 *	-> Diese Function gibt die Maske zum Ändern des Themas aus
	 */
	
	public function edit_theme ($id){
		$this->db->set("what" , "ID, Bereich, Farbe");
		$this->db->set("from" , "Thema");
		$this->db->set("where",  0);
		$this->db->set("order", "ID");
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		
		if ($res->num_rows > 0) {		
			$i = 1;
		
			echo '<table>
					<tr>
						<th class="th3">Bereich</th>
						<th class="th2">Farbe</th>
						<th class="th1"></th>
					</tr>';
					
			while ($rs = $res->fetch_object()) {			
				if ($id == $rs->ID) {
					echo '<tr>
							<td class="td' . if_int($i) . '">' . $rs->Bereich . '</td>
							<td class="td' . if_int($i) . '"><input type="text" id="Farbe" class="input" value="' . $rs->Farbe . '"></td>
							<td class="td' . if_int($i) . '"><span id="update_theme:Name_' . $id . '" class="action_span"><img src="img/check_green.gif" class="pic" /></span></td>
						  </tr>';
				} else {
					echo '<tr>
							<td class="td' . if_int($i) . '">' . $rs->Bereich . '</td>
							<td class="td' . if_int($i) . '">' . set_color($rs->Farbe, $rs->Farbe) . '</td>
							<td class="td' . if_int($i) . '"></td>
						  </tr>';	
				}
				  
				 $i++;					  
			}
			echo '</table>';
			
		} else {
			echo '<p>keine Boxen vorhanden</p>';
		}
		
		$this->farb_tool();
	}
	
	/**
	 *	-> Diese Function speichert die Änderungen am Thema
	 */
	 
	public function update_theme ($id, $farbe) {	
 		if (trim($farbe) == "" or trim($id) == "") {
 			$this->show_theme();
 			die('<p class="error"><b>Fehler:</b> unvollständige Eingabe</p>');
 		}
 		
		$sql = "UPDATE Thema SET Farbe = ? WHERE ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('si', $farbe, $id);
		$res->execute();
		
		$this->show_theme();
	}
	
}

?>
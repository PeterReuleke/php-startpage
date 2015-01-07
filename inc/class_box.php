<?php

class Box {
	private $id;
	private $menu_id;
	private $db;	
	
	private $art;
	private $inline_style;
	private $box_id;
	private $name;
	private $farbe;
	private $top;
	private $left;
	private $anzeigen;

	private $content;

	/**
	 *	-> Konstruktor 
	 */
	
	public function __construct ($sql, $id, $menu_id, $art = 1, $style = false) {
		$this->db = $sql;		
		$this->id = $id;
		$this->menu_id = $menu_id;
		$this->art = $art;
		$this->inline_style = $style;

		$this->get_box_style ();
	}
	
	/**
	 *	-> Diese Function legt die Eigenschaften der Box fest
	 *	-> dies geschieht in Abhängigkeit von der id
	 */

	private function get_box_style () {	
		$this->db->set("what" , "Box.ID AS id, Box.Name AS name, Box.Farbe AS farbe, con_Menu_Box.Box_Top AS box_top, con_Menu_Box.Box_Left AS box_left, Box.Anzeigen AS anzeigen");
		$this->db->set("from" , "Box INNER JOIN con_Menu_Box ON Box.ID = con_Menu_Box.Box_ID INNER JOIN Menu ON con_Menu_Box.Menu_ID = Menu.ID");
		$this->db->set("where", "Box.ID = '".$this->id."' AND Menu.ID = '".$this->menu_id."'");
		$this->db->set("order",  0);
		$this->db->set("show" ,  0);
		
		$res = $this->db->do_query ();
		$rs = $res->fetch_object();
		
		$this->box_id = $rs->id;
		$this->name = $rs->name;
		$this->farbe = $rs->farbe;
		$this->top = $rs->box_top;
		$this->left = $rs->box_left;
		$this->anzeigen = $rs->anzeigen;		
	}
	
	/**
	 *	-> Setter-Function für die Eigenschaft content 
	 */
	
	public function set_content ($new_content) {
		$this->content = $new_content;
	}
	
	/**
	 *	-> Getter-Function der Eigenschaft content
	 */
	 
	public function get_content () {
		return $this->content;
	}

	/**
	 *	-> Diese Function gibt den Kopf der Box zurück
	 */
	 
	private function get_box_head () {	
		switch ($this->art) {
			case 1:
				$class = "link_box";
				break;
			case 2:
				$class = "notiz_box";
				break;
			case 3:
				$class = "termin_box";
				break;
			case 4:
				$class = "rss_box";
				break;
			default:
				$class = "link_box";
				break;
		}
		
		if ($this->inline_style) {
			$ausgabe = <<< EOT
			<div id="box$this->box_id" class="box $class" style="background-color: #$this->farbe; top: $this->top; left: $this->left;">
				<div class="box_head">$this->name</div>
				<div class="box_body">\n
EOT;
		} else {
			$ausgabe = <<< EOT
			<div id="box$this->box_id" class="box $class menu$this->menu_id">
				<div class="box_head">$this->name</div>
				<div class="box_body">\n
EOT;
		}
		
		return $ausgabe;
	}
	
	/**
	 *	-> Diese Function schließt die Box
	 */

	private function get_box_foot () {
		$ausgabe = '	</div>
					</div>';
		
		return $ausgabe;
	}
	
	/**
	 *	-> Gibt den HTML-Code der Box aus
	 */
	
	public function draw_box () {
		echo $this->get_box_head() . $this->get_content() . $this->get_box_foot();		
	}
	
	/**
	 *	-> Diese Function führt ein Update-Query aus, zum Ändern der Box-Position
	 */

	public function update_box_position ($new_top, $new_left, $menu_id) {
		$sql = "UPDATE con_Menu_Box SET Box_Top = ?, Box_Left = ? WHERE Menu_ID = ? AND Box_ID = ?";		
		$res = $this->db->prepare($sql);
		$res->bind_param('ssii', $new_top, $new_left, $menu_id, $this->id);
		$res->execute();
	}
	
	/**
	 *	-> Diese Function speichert Änderungen einer Notiz-Box
	 */
	 
	public function edit_notizen ($text) {
	 	if (trim($text) == "") {
	 		$text = "...";
	 	}
	 
		$sql = "UPDATE Notizen SET Text = ?, Datum = NOW() WHERE Box_ID = ?";
		$res = $this->db->prepare($sql);
		$res->bind_param('si', nl2br($text), $this->id);
		$res->execute();
		
		echo nl2br($text);
	}

	/**
	 *	-> Diese Function gibt zurück, ob die Box angezeigt werden soll oder nicht
	 */

	public function is_activ () {
		if ($this->anzeigen == 1) {
			return true;
		} else {
			return false;
		}
	}

}

?>
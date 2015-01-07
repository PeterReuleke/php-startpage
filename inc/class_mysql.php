<?php 

	require_once 'constants.php';
	
	class MySql extends mysqli { 
	     
	    protected $con;      
	    public $result; 
	    
		/*
		 *	-> Die Eigenschaften what, from, where, order und show werden für den SQL-Query benötigt
		 */
	    
		public $what;
		public $from;
		public $where;
		public $order;
		public $show;
		public $sql;
	
	    /**
	     *	-> Konstruktor
	     */
	     
	    public function __construct () { 	         
	        $this->con = @parent::__construct(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME); 
	
	        if (mysqli_connect_error()) {
	        	die ('<br /><b>Fehler beim Verbinden!</b><br />Connect Errno: ' . mysqli_connect_errno() . '<br />Connect Error: ' . mysqli_connect_error());
	        }         
	    } 

		/**
		 *	-> Diese Function weisst einer Eigenschaft einen übergebenen Wert zu
		 *	-> $var1 bestimmt die Eigenschaft
		 *	-> $var2 bestimmt den zu übergebenen Wert
		 */
		 
		public function set ($var1, $var2) {
			$this->$var1 = $var2;
		}
		
		/**
		 *	-> Diese Function gibt den Wert einer Eigenschaft wieder
		 *	-> $var bestimmt die Eigenschaft
		 */
		
		public function get ($var) {
			return $this->$var;
		}
		
		/**
		 *	-> Diese Function stellt ein SQL Select aus verschiedenen Eigenschaften zusammen
		 */
		 
		public function get_sql () {
			$this->sql = "SELECT $this->what FROM $this->from ";
		
			if ($this->where != "0") {
				$this->sql.= "WHERE $this->where ";
			}
			if ($this->order != "0") {
				$this->sql.= "ORDER BY $this->order";
			}
			
			//debug($this->sql);
			
			return $this->sql;
		}		

		/**
		 *	-> Diese Function führt ein SQL Select-Query aus
		 */
		 
	    public function do_query () { 	         
	        $this->result = parent::query($this->get_sql()); 
	         
	        if ($this->error) {
	        	die('<br />Fehler bei der Abfrage!<br />Query: <pre>' . $this->get_sql() . '</pre><br />Antwort: ' . $this->error); 
	        }
	         
			if ($this->show != "0") {
 				if ($rs = $this->result->fetch_object()) {	
  					$show = $this->show;
					return $rs->$show;
				}							
			} else {
				return $this->result;		
			}	
	    } 
	
	    /**
	     *	-> Destruktor
	     */
	
	    public function __destruct () { 
	        if ($this->con) {
	        	$this->close(); 
	        }
	    } 

} 

?>
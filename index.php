<?php
class DataBase 
{
	protected $query = 'CREATE TABLE IF NOT EXISTS MyBook (
							id SERIAL PRIMARY KEY,
							title CHARACTER VARYING(30),
							isbn INTEGER,
							author CHARACTER VARYING(50))';
		private $insert_array = array('TBL' => 'MyBook','VALUES' => array('title' => 'HELLO JAVA', 'isbn'=>110, 'author' => 'This is professional book.'));
		private $delete_array = array('TBL' => 'MyBook', 'VALUES' => array('title' => 'HELLO JAVA', 'id' => 48));
		private $update_array = array('TBL' => 'MyBook', 'OLDS' => array('title' => 'HELLO JAVA123', 'id' => 56), 'NEWS' => array('title' =>'HELLO PHP', 'id' => 11, 'author' => 'This is a perfect book book.'));
		private $show_array = array('TBL' => 'MyBook');
		
		public $db, $result;
		
	protected function ConnectToDB()
	{
		$this->db = pg_connect("host=localhost port=5432 dbname=database1 user=postgres password=myadmin123",  'PGSQL_CONNECT_FORCE_NEW');
		$this->result = pg_query($this->db, $this->query);
	}

	protected function Insert($insert_array)
	{
		// String for hold columns' data seperately.
		static $data_query;
		$this->query = 'INSERT INTO ' . $insert_array['TBL'] . '(';
		
		foreach ($insert_array['VALUES'] as $columns => $data) {
			
			// Put each array member with ', ' after it in query string.
			$this->query .= $columns .  ', '; 
			$data_query .= "'" . $data . "', ";
		}
		
		// Remove ', ' from end of strings.
		$this->query = substr($this->query, 0 , -2);
		$data_query = substr($data_query, 0, -2);
		
		// Append strings together for build query.
		$this->query .= ') VALUES (' . $data_query . ')';
		echo $this->query;
		$this->result = pg_query($this->db, $this->query);
		
	}
		
	protected function Delete($delete_array)
	{
		$this->query = 'DELETE FROM ' . $delete_array['TBL'] . ' WHERE ';
			
		// Build query for delete row in table.
		foreach ($delete_array['VALUES'] as $columns => $data) {
			if (!isset($data)) {
				continue;
			} else {
				if (is_string($data)) {
					$this->query .= $columns . " LIKE '". $data . "' AND ";
				} else {
					$this->query .= $columns . '=' .$data . ' AND ';
				}
			}
			
			
			
		}
		// Remove  last 'AND' from query.
		$this->query = substr($this->query, 0 , -4);
		
		$this->result = pg_query($this->db, $this->query);
	}
	
	protected function Update($update_array)
	{
		$this->query = "UPDATE " . $update_array['TBL'] . " SET ";
			
		foreach ($update_array['NEWS'] as $columns => $data) {
				$this->query .= $columns . " = '" . $data . "', ";
		}
		
		$this->query = substr($this->query, 0, -2);
		
		$this->query .= ' WHERE ';
		
		foreach ($update_array['OLDS'] as $columns => $data) {
			if (is_string($data)) {
				$this->query .= $columns . " LIKE '". $data . "' AND ";
			}
			else	
				$this->query .= $columns . ' = ' . $data . ' AND ';
		}
		
		$this->query = substr($this->query, 0, -4);
		echo $this->query;
		$this->result = pg_query($this->db, $this->query);
	}
	
}

/**
 * 
 */
class Book extends DataBase {
	
	
	public $id;
	public $title;
	public $isbn;
	public $author;
	
	
	public function Book($array=0)
	{
		// Connect To Database. 
		$this->ConnectToDB();
		
		if($array>=1){
			
			// Build query for strings and digits.
			$this->query = 'SELECT * FROM ' . $array['TBL'] . ' WHERE ';
			foreach($array['VALUES'] as $key => $value)
			{
				if (is_string($value)) 
					$this->query .= $key . " LIKE '" . $value . "' AND ";
				else
					$this->query .= $key . ' = ' . $value . ' AND ';
			}
		
		
			// Remove  last ' AND' from query.
			$this->query = substr($this->query, 0 , -4);
			
			// Query for get values.
			$res = pg_query($this->query);
			
			if (!$res) {
			  echo "An error occurred.\n";
			  exit;
			}
			
			// Set values into class properties.
			while ($row = pg_fetch_row($res)) {
				$this->id 	  = $row[0];
				$this->title  = $row[1];
				$this->isbn   = $row[2];
				$this->author = $row[3]; 
			}
		}
	}
	
	function Save()
	{
		$this->Insert(array('TBL' => 'MyBook',
							'VALUES' => array('title' => $this->title, 'isbn' => $this->isbn, 'author' => $this->author)));
	}
	
	function Remove() 
	{
		$this->Delete(array('TBL' => 'MyBook', 
							'VALUES' => array('id' => $this->id, 'title' => $this->title, 'isbn' => $this->isbn, 'author' => $this->author)));
	}
	
	function Edit()
	{
		$this->Update(array('TBL' => 'MyBook', 
			   				'OLDS' => array('title' => 'HELLO PHP', 'id' => 23), 
			  				'NEWS' => array('title' => $this->title, 'id' => $this->id, 'author' => $this->author)));
	}
	
}

// Import data to MyBook table.
$Book1 				= 	new Book();
$Book1->title		=	'cisco+';
$Book1->isbn		=   237;
$Book1->author 		=   'masdari';
$Book1->Save();


// Export data from MyBook table.
$array = array('TBL' => 'MyBook', 'VALUES' => array('title' => 'php7'));
$Book2 				= 	new Book($array);
echo $Book2->id . "<br>";
echo $Book2->title . '<br>';
echo $Book2->isbn . '<br>';
echo $Book2->author . '<br>';


// Remove row from MyBook table.
$Book3         = new Book();
$Book3->id     = 3;
$Book3->title  = 'new book333';
$Book3->isbn   = 34;
$Book3->author = 'karami';
$Book3->Remove();


// Update row from MyBook table.
$Book4          = new Book();
$Book4->id      = 56;
$Book4->title   = 'Learn PHP7';
$Book4->author  = 'bagheri';
$Book4->Edit();

//$Book1->title		=	'updateded book';
//$Book1->save();


?>
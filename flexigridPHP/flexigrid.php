<?php

//Flexigrid jQuery plugin PHP class
//Joseph Ensign


class flexigrid implements SplSubject{

	//DB Constants
	const DB_HOST = "localhost";
	const DB_USER = "root";
	const DB_PASS = "";
	const MAIN_DB = "sample_mvc";

	private $page, //current page
			$rp, //results per page
			$sortname, //sortby field
			$total, //total results
			$sortorder, //Ascending or descending
			$query, //sql query
			$qtype, //query type
			$db_resource_id, //db resource
			$table_name, //table name
			$query_components; //query components (LIMIT, WHERE, ORDER BY)
			
	private $_flexigrids,
			$_observers,
			$_currentFlexigrid;


	public function __construct($table_n) {
		//Assigns data-members respective post values and the table
		$this->set_vals($table_n);
		//Uses above values to generate query components
		$this->set_query_components();
		//if the table is empty kill the script... Should be customized per ones needs
		if($this->record_count() == 0) {
			exit("No Records");
		}
		//or assign total the value
		$this->total = $this->record_count();
		//array of "UI component observers"
		$this->_observers = array();
	}
	//associates an Observer object with a Flexigrid
	public function attach(SplObserver $observer) {
		array_push($this->_observers, $observer);
		$this->notify();
	}
	//disassociates an observer
	public function detach(SplObserver $observer) {
		
		if(in_array($observer, $this->_observers)) {
			
			$key = array_search($observer, $this->_observers);
			
			if($this->_observers[$key] == $observer) {
			
				try {
					unset($this->_observers[$key]);
					return TRUE;
				} catch(Exception $e) {
					die($e->getMessage());
				}
			
			}
			
		}	
	}
	//ping observer when change is made
	public function notify() {
		foreach($this->_observers as $key => $val) {
			$val->update($this);
		}
	}
	//Perform Query. Returns array or resource
	private function do_query($sql, $return_array = FALSE) {
	
		$this->db_resource_id = mysql_connect(self::DB_HOST, self::DB_USER, self::DB_PASS);
		mysql_select_db(self::MAIN_DB);
	
		$query = mysql_query($sql) or die(mysql_error());
		
		if($return_array == FALSE) {
			return $query;
		} else {
			
			while($row = mysql_fetch_assoc($query)) {
				$data[] = $row;
			}
			
			return $data;	
		}
	}
	//returns the number of records
	private function record_count() {
		//Currently id is assumed
		$sql = "SELECT COUNT(id) FROM {$this->table_name}";
		$r = $this->do_query($sql);
		
		while($row = mysql_fetch_array($r)) {
			return $row[0];
		}
	}
	
	//Sets vals based on Asynchronous POSTs
	private function set_vals($t_name) {
	
		$this->table_name = $t_name;
		$this->page = isset($_POST['page']) ? $_POST['page'] : 1;
		$this->rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
		$this->sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
		$this->sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
		$this->query = isset($_POST['query']) ? $_POST['query'] : false;
		$this->qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;
	
	}
	
	//Set query components in an array w/ elements "sort", "limit", "where"
	//No return value, sets -flexigrid::$query_components[]
	private function set_query_components() {
	
		$start = (($this->page-1) * $this->rp);
		
		if($this->query) {
			$this->query_components['where'] = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' ";
		}
		else {
			$this->query_componenets['where'] = "";
		}
		
		$this->query_components['sort'] = "ORDER BY {$this->sortname} {$this->sortorder}";
		$this->query_components['limit'] = "LIMIT $start, {$this->rp}";
	}
	
	//Output JSON
	public function output_json($id, $fields = array()) {

		if(!in_array($id, $fields)) {
			array_push($fields, $id);
		}
	
		if(empty($fields)) {
			die("Fatal Error: " . __FUNCTION__ . " expects an array as its second argument.");
		}
		
		else {
			
			try {
				//Generate SQL query
				$sql = "SELECT " . implode(', ', $fields) . " FROM {$this->table_name} " . implode(" ", $this->query_components);
				//set content-type to json
				header("Content-type: application/json");
				//populate array with required values (Notice "rows" index points to an empty array)
				$jsonData = array('page'=>$this->page,'total'=>$this->total,'rows'=>array());
				//SQL result
				$res = $this->do_query($sql);
				//iterate through all results
				for($i=0;$i<$this->total;$i++) {
					//set ID
					$entry_array['id'] = mysql_result($res, $i, $id);
					//populate columns/cells
					foreach($fields as $field => $val):
						$entry_array['cell'][$val] = mysql_result($res, $i, $val);
					endforeach;
					//Rows index now points to populated array
					$jsonData['rows'][] = $entry_array;
				}
				//return encoded array
				echo json_encode($jsonData);
				//kill script
				exit();
			}
			catch(Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
	}
}
?>
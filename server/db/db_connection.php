<?php
/*
 *  @author kku
 *  @comment
 *    common database read/write operations
 */
class DBConnection {
	private $db = NULL;

	function __construct() {
	}
	
	function __destruct() {
		$this->close();
	}
	
	public function connect(){
		require_once("db_config.php");
		
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($this->db->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " 
        . $mysqli->connect_error;
			$this->db = NULL;
			return false;
		}
		
		return true;
	}
	
	public function close() {
		if ($this->db != NULL)
			$this->db->close();
	}
	
	private function prepare_statement($query){
		$stmt = $this->db->prepare($query);		
		if(!$stmt)
			return NULL;	
		return $stmt;
	}
	
	/*
	 * ENSURES: returns NULL if addUser failed, user_id if successful 
	 */
	public function add_user($ip){
		$query = "INSERT INTO nodes(
      user_ip,
			user_join_time) 
			VALUES(?, NOW()) 
      ON DUPLICATE KEY UPDATE user_ip = ?, user_join_time = NOW();
		";
			
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return NULL;
		$bind = $stmt->bind_param("ss", $ip, $ip);
		if(!$bind)
			return NULL;
		
		$exec = $stmt->execute();
		if(!$exec)
			return NULL;
			
		return $stmt->insert_id;
	}
	
	/*
	 *	ENSURES: returns user_id of the user with the corresponding ip
	 *		NULL if not match was found
	 */
	public function get_uid($ip){
		$query = "SELECT user_id FROM nodes WHERE user_ip = ?;";
		
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return NULL;
		$bind = $stmt->bind_param("s", $ip);
		if(!$bind)
			return NULL;
		
		$exec = $stmt->execute();
		if(!$exec)
			return NULL;

    $stmt->store_result();
		if($stmt->num_rows == 0)
			return NULL;

		$stmt->bind_result($uid);
		$stmt->fetch();
		
		return $uid;
	}

  /* add_neighbor: add a neighbor to the user with uid 
   * ENSURES: returns neighbor is on success, NULL otherwise
   *  insert 2 edges into edges table:
   *    uid -> ngh_id
   *    ngh_id -> uid
   */
  public function add_neighbor($uid, $neighbor_ip){
    $ngh_id = $this->get_uid($neighbor_ip);
    if(!$ngh_id)
      $ngh_id = $this->add_user($neighbor_ip);
    
    if(!$ngh_id)
      return NULL;
      
		$query = "INSERT INTO edges(
      src_id,
			target_id) 
			VALUES(?, ?)
      ON DUPLICATE KEY UPDATE src_id = src_id;
		";
			
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return NULL;
		$bind = $stmt->bind_param("ii", $uid, $ngh_id);
		if(!$bind)
			return NULL;
		
		$exec = $stmt->execute();
		if(!$exec)
			return NULL;

    /* duplicate edges */
		$bind = $stmt->bind_param("ii", $ngh_id, $uid);
		if(!$bind)
			return NULL;
		
		$exec = $stmt->execute();
		if(!$exec)
			return NULL;
			
		return $ngh_id;
  }

  /* delete a user with $uid
   * ENSURES: returns true on success, false otherwise
   */
  public function delete_user($uid){
    /* delete from nodes table */
		$query = "DELETE FROM nodes
      WHERE user_id=? ;
		";
			
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return false;
		if(!$stmt->bind_param("i", $uid))
			return false;
		
		if(!$stmt->execute())
			return false;

    /* delete from edges table */
    $query = "DELETE FROM edges
      WHERE src_id=? OR target_id=? ;
    ";

		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return false;
		if(!$stmt->bind_param("ii", $uid, $uid))
			return false;
		
		if(!$stmt->execute())
			return false;
			
		return true; 
  }

  /* get_all_edges: return all edges in an array mapping id to all neighbor ids
   * ENSURES: returns NULL on failure 
   */
  function get_all_edges(){ 
    $query = "SELECT * FROM edges";

		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return NULL;	
		if(!$stmt->execute())
			return NULL;
 
    $stmt->store_result();
		if($stmt->num_rows == 0)
			return NULL;
			
    $ret = array();
		$stmt->bind_result($src_id, $target_id);		
    while($stmt->fetch()){
      if(array_key_exists($src_id, $ret)){
        $ret[$src_id][] = $target_id;
      }
      else{
        $ret[$src_id] = array($target_id);
      }
    } 

    return $ret;
  }
}
?>

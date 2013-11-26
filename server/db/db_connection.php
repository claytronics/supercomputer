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

  /* ENSURES: update user_join_time to the time now,
   *  returns user_id of the client
   */
  public function keep_user_alive($ip){ 
		$query = "UPDATE nodes SET user_join_time = NOW() WHERE user_ip = ?;";
			
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return NULL;
	  if(!$stmt->bind_param("s", $ip))
			return NULL;
		
		if(!$stmt->execute())
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

  /* remove_inactive_users: remove users who have not reported in the past 
   *  minute */
  public function remove_inactive_users(){
    /* 1 minute ago */
    $now = date("Y-m-d H:i:s", strtotime("-1 minute"));

		$query = "SELECT user_id FROM nodes WHERE user_join_time < ?;";
		
		$stmt = $this->prepare_statement($query);
    if(!$stmt)
      return 0;
		if(!$stmt->bind_param("s", $now))
			return 0;
		
		if(!$stmt->execute())
			return 0;

    $stmt->store_result();
    $num = $stmt->num_rows;
		if($num == 0)
			return 0;

		$stmt->bind_result($uid);
		while($stmt->fetch()){
      $this->delete_user($uid);
    }
		
		return $num;
  }

  /* get_all_edges: return all edges in an array mapping id to all neighbor ids
   * ENSURES: returns NULL on failure. removes all inactive users before 
   *  generating the graph
   */
  function get_all_edges(){ 
    $removed = $this->remove_inactive_users();

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

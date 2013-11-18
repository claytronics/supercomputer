<?php
/*
 * register.php - allow user devices to register their ip address 
 * ENSURES: returns JSON object with success = 0 on failure,
 *  success = 1 and user_id set to the user's id on success
 */

require_once("util.php");

function register(){
  require_once("db/db_connection.php");
  $ip = get_client_ip();

  $db = new DBConnection();
  $db->connect();

  $uid = $db->add_user($ip);
  $db->close();

  if(!$uid)
    return json_encode(array("success" => 0));

  return json_encode(array("success" => 1, "user_id" => $uid));
}

/* return json object to client */
echo register();

?>

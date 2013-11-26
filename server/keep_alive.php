<?php
/*
 * keep_alive.php - allow user devices to respond that they are still alive
 * ENSURES: returns JSON object with success = 0 on failure,
 *  success = 1 and user_id set to the user's id on success
 */

require_once("util.php");

function keep_alive(){
  require_once("db/db_connection.php");
  $ip = get_client_ip();

  $db = new DBConnection();
  $db->connect();

  $db->keep_user_alive($ip);
  $db->close();

  return json_encode(array("success" => 1, "user_id" => $uid));
}

/* return json object to client */
echo keep_alive();

?>

<?php
/* neighbor.php - allow a user to add his/her neighbor 
 * REQUIERS: neighbor's ip address is in the url
 * ENSURES: returns JSON with success = 0 and error_msg set on failure, 
 *  success = 1 on success with neighbor_id field set to the 
 *  neighbor's id
 */

require_once("util.php");
require_once("db/db_connection.php");

function reply_fail($msg){ 
  echo json_encode(array("success" => 0, "error_msg" => $msg));
  exit(0);
}

if(!isset($_GET['neighbor_ip']))
  reply_fail("neighbor ip not provided");

$ngh_ip = $_GET['neighbor_ip'];

$db = new DBConnection();
$db->connect();

$user_ip = get_client_ip();

if(strcmp($user_ip, $ngh_ip) == 0){
  $db->close();
  reply_fail("user ip and neighbor ip are the same");
}

$uid = $db->get_uid($user_ip);

if(!$uid){
  $db->close();
  reply_fail("failed to get user id");
}

$ngh_id = $db->add_neighbor($uid, $ngh_ip);
if(!$ngh_id){
  $db->close();
  reply_fail("failed to add neighbor into database");
}

$db->close();

echo json_encode(array("success" => 1, "neighbor_id" => $ngh_id));

?>

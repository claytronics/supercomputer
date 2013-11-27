<?php
/* neighbor.php - allow a user to add his/her neighbor 
 * REQUIERS: neighbor's ip address is in the url
 * ENSURES: returns JSON with success = 0 and error_msg set on failure, 
 *  success = 1 on success with neighbor_id field set to the 
 *  neighbor's id
 */

require_once("util.php");
require_once("detect_location_change.php");
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

$info = $db->add_neighbor($uid, $ngh_ip);
if($info == NULL){
  $db->close();
  reply_fail("failed to add neighbor into database");
}

$ngh_id = $info["neighbor_id"];

/* check if the user is likely to have changed location if the edge 
 * did not exist before */
if(!$info["edge_exists"]){
  if(detect_location_change($db->get_all_edges(), $uid, $ngh_id)){
    log_location_change($uid, $ngh_id);
  }
}

$db->close();

echo json_encode(array(
  "success" => 1, 
  "neighbor_id" => $ngh_id
  ));

?>

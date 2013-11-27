<?php
/*
 * helper functions 
 */

function get_client_ip(){
  if($_SERVER["REMOTE_ADDR"])
    return $_SERVER["REMOTE_ADDR"];

  return NULL;
}

function log_location_change($uid, $ngh_id){
  $file = "log/location_change_log.log";
  $data = "[".date("Y-m-d H:i:s")."] ";
  $data .= "user ".$uid." changed location after adding user ".$ngh_id;
  $data .= " as neighbor\n";

  $fh = fopen($file, "a");
  fwrite($fh, $data);
  fclose($fh);
}
?>

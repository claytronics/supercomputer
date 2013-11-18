<?php
/*
 * helper functions 
 */

function get_client_ip(){
  if($_SERVER["REMOTE_ADDR"])
    return $_SERVER["REMOTE_ADDR"];

  return NULL;
}
?>

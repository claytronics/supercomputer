<?php
/* detect_location_change: report whether a client has likely changed his/her 
 *  position when he/she adds a neighbor
 * Uses Dijkstra's algorithm to determine the shortest distance from client
 *  to the neighbor before adding the neighbor and compare it to the shortest
 *  distance from the client to the neighbor after adding the neighbor
 * @author Kevin Ku
 * @date Nov. 27, 2013
 */

require_once("shortest_path.php");

/* min difference in shortest path lengths needed for a client to be 
 *  considered as changing location */
define("LOC_CHANGE_THRESHOLD", 10);

/* REQUIRES: $client is the user id of the client that added $neighbor as 
 *  his/her neighbor
 *  $neighbor is the id of the neighbor
 */
function detect_location_change($graph, $client, $neighbor){

  /* get new shortest distance from client to neighbor */
  $d_new = find_shortest_path($graph, $client, $neighbor);

  /* remove direct edge between the client and neighbor */
  $f = function($node) use (&$client, &$neighbor){ 
    $node != $client && $node != $neighbor; 
  };
  $graph[$client] = array_filter($graph[$client], $f);
  $graph[$neighbor] = array_filter($graph[$neighbor], $f);

  $d_old = find_shortest_path($graph, $client, $neighbor);

  return (abs($d_old - $d_new) >= LOC_CHANGE_THRESHOLD);
}

?>

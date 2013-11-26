<?php
/* graph.php - returns a JSON mapping each node to a list of its neighbors 
 */

require_once("db/db_connection.php");
$db = new DBConnection();
$db->connect();

$edges = $db->get_all_edges();

$db->close();

if($edges == NULL)
  echo json_encode(array());
else
  echo json_encode($edges);

?>

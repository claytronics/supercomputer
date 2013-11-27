<?php
/*
 * Implementation of Dijkstra's Algorithm
 * @author Kevin Ku
 * @date Nov 26, 2013
 */

require_once("distance_pq.php");

/* REQUIRES: $graph is an array mapping each vertex to array of neighbors */
function dijkstra($graph, $target, $visited, $pq){
  if($pq->isEmpty())
    return $visited;

  $current = $pq->extract();
  $node = $current["data"];
  $d = $current["priority"]; /* distance */

  /* touch */
  if(array_key_exists($node, $visited))
    return dijkstra($graph, $target, $visited, $pq);

  /* enter current node */
  $visited[$node] = $d;

  if($node == $target)
    return $visited;

  /* generate new frontier */
  $neighbors = array_diff($graph[$node], array_keys($visited));
  
  foreach($neighbors as $ngh){
    $pq->insert($ngh, $d + 1);
  }

  return dijkstra($graph, $target, $visited, $pq);
}

/* REQUIRES: $graph is an array mapping each vertex to array of neighbors 
 * ENSURES: returns shortest path from source to target in graph
 */
function find_shortest_path($graph, $source, $target){
  if(sizeof($graph) == 0)
    return false;

  /* pq to store distances */
  $pq = new DPQ();

  /* add all nodes into the pq */
  $pq->insert($source, 0);
  foreach(array_keys($graph) as $k){
    if($k != $source)
      $pq->insert($k, INF);
  }

  /* run dijkstra's */
  $sd = dijkstra($graph, $target, array(), $pq);

  return $sd[$target];
}
?>

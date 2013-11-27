<?php

/* priority queue where priority = shortest distance */
class DPQ extends SplPriorityQueue{
  public function __construct(){
    $this->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
  }

  /* $d1, $d2 are distances */
  public function compare($d1, $d2){
    if($d1 == $d2)
      return 0;

    return $d1 > $d2 ? -1 : 1;
  }
}

?>

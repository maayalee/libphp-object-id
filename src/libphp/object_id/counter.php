<?php
namespace libphp\object_id;

use libphp\core\time;
use libphp\object_id\errors\backward_timestamp;

abstract class counter {
  public function __construct() {
  }

  abstract protected function get_current_time();

  public function inc() {
    $current_time = $this->get_current_time();
    if ($current_time < $this->last_time)
      throw new backward_timestamp('current time is little than last time');

    if ($current_time != $this->last_time) {
      $this->increment = 0; 
    }
    $this->increment++;
    $this->last_time = $current_time;
    return $this->increment;
  } 

  public function reset() {
    $this->increment = 0;
    $this->last_time = 0;
  }

  public function get_increment() {
    return $this->increment;
  }

  public function get_last_inc_time() {
    return $this->last_time;
  }

  private $increment = 0; 
  private $last_time = 0;
}

<?php
namespace libphp\object_id;

use libphp\core\time;
use libphp\object_id\errors\backward_timestamp;
use libphp\object_id\errors\increment_count_overflow;

class increment_counter {
  public function __construct($max_increment_count) {
    $this->max_increment_count = $max_increment_count;
  }

  public function inc() {
    $current_time = $this->get_current_time();
    if ($current_time < $this->last_time)
      throw new backward_timestamp('current time is little than last time');

    if ($this->is_same_sec($current_time)) {
      if ($this->increment >= $this->max_increment_count)
        throw new increment_count_overflow('');
    }
    else {
      $this->increment = 0; 
    }
    $this->increment++;
    $this->last_time = $current_time;
    return $this->increment;
  }

  protected function get_current_time() {
    return time::get_time();
  }

  private function is_same_sec($current_time) {
    return $current_time == $this->last_time;
  }

  public function reset($max_increment_count) {
    $this->increment = 0;
    $this->last_time = 0;
    $this->max_increment_count = $max_increment_count;
  }

  public function get_increment() {
    return $this->increment;
  }

  public function get_last_inc_time() {
    return $this->last_time;
  }

  private $increment = 0; 
  private $last_time = 0;
  private $max_increment_count;
}

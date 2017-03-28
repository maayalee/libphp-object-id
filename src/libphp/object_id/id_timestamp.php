<?php
namespace libphp\object_id;

use libphp\core\time;
use libphp\object_id\errors\backward_timestamp;
use libphp\object_id\errors\increment_count_overflow;

class id_timestamp {
  public function __construct($max_increment_count) {
    $this->max_increment_count = $max_increment_count;
  }
  
  public function generate($current_time) {
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
  }

  private function is_same_sec($current_time) {
    return $current_time == $this->last_time;
  }

  public function reset() {
    $this->increment = 0;
    $this->last_time = 0;
  }

  public function get_time() {
    return $this->last_time;
  }

  public function get_increment() {
    return $this->increment;
  }

  private $increment = 0; 
  private $last_time = 0;
  private $max_increment_count = 0;
}

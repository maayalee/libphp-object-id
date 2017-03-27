<?php
namespace libphp\object_id;

use libphp\core\time;
use libphp\object_id\errors\backward_timestamp;
use libphp\object_id\errors\increment_count_overflow;

class id_timestamp {
  public function __construct() {
  }
  
  public function generate($current_time, $max_increment_count) {
    if ($current_time < $this->last_timestamp)
      throw new backward_timestamp('current time is little than last time');

    if ($current_time == $this->last_timestamp) {
      if ($this->increment >= $max_increment_count)
        throw new increment_count_overflow('');
    }
    else {
      $this->increment = 0; 
    }
    $this->increment++;
    $this->last_timestamp = $current_time;
    return array('gen_timestamp'=>$current_time, 'gen_increment'=>$this->increment);
  }

  public function reset() {
    $this->increment = 0;
    $this->last_timestamp = 0;
  }

  public function get_timestamp() {
    return $this->last_timestamp;
  }

  public function get_increment() {
    return $this->increment;
  }

  private $increment = 0; 
  private $last_timestamp = 0;

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new id_timestamp();
    }
    return $instance;
  }
}

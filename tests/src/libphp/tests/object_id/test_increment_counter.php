<?php
namespace libphp\tests\object_id;

use libphp\object_id\increment_counter;

class test_increment_counter extends increment_counter {
  public function set_current_time($current_time) {
    $this->current_time = $current_time;
  }

  public function inc_current_time() {
    $this->current_time += 1;
  }

  public function dec_current_time() {
    $this->current_time -= 1;
  }

  protected function get_current_time() {
    return $this->current_time;
  }

  private $current_time;
}

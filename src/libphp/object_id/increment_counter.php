<?php
namespace libphp\object_id;

use libphp\core\time;

class increment_counter extends counter {
  protected function get_current_time() {
    return time::get_time();
  }
}

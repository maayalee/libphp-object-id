<?php
namespace libphp\object_id;

use libphp\object_id\object_id;

class object_id_builder {
  public function __construct($counter) {
    $this->counter = $counter;
    $this->machine_name = '';
    $this->process_id = 0;
  }

  public static function create($counter) {
    return new self($counter);
  }

  public function get_counter() {
    return $this->counter;
  }

  public function get_machine_name() {
    return $this->machine_name;
  }

  public function get_process_id() {
    return $this->process_id;
  }

  public function machine_name($name) {
    $this->machine_name = $name;
    return $this;
  }

  public function process_id($id) {
    $this->process_id = $id;
    return $this;
  }

  public function build() {
    return object_id::create_with_builder($this);
  }

  private $counter;
  private $machine_name;
  private $process_id;
}


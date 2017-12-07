<?php
namespace libphp\objectid;

use libphp\objectid\Counter;
use libphp\objectid\ObjectID;

class ObjectIDBuilder {
  public function __construct(Counter $counter) {
    $this->counter = $counter;
    $this->machineName = '';
    $this->processID = 0;
  }

  public static function create(Counter $counter) {
    return new self($counter);
  }

  public function getCounter() {
    return $this->counter;
  }

  public function getMachineName() {
    return $this->machineName;
  }

  public function getProcessID() {
    return $this->processID;
  }

  public function machineName(string $name) {
    $this->machineName = $name;
    return $this;
  }

  public function processID(int $id) {
    $this->processID = $id;
    return $this;
  }

  public function build() {
    return ObjectID::createWithBuilder($this);
  }

  private $counter;
  private $machineName;
  private $processID;
}


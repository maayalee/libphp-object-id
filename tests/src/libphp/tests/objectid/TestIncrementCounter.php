<?php
namespace libphp\tests\objectid;

use libphp\objectid\Counter;

class TestIncrementCounter extends Counter {
  public function setCurrentTime(int $currentTime) {
    $this->currentTime = $currentTime;
  }

  public function incCurrentTime() {
    $this->currentTime += 1;
  }

  public function decCurrentTime() {
    $this->currentTime -= 1;
  }

  protected function getCurrentTime() {
    return $this->currentTime;
  }

  private $currentTime;
}

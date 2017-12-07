<?php
namespace libphp\objectid;

use libphp\objectid\errors\BackwardTimestamp;

/**
 * @class Counter
 *
 * @brief 1초 범위 안에서 증가하는 숫자를 생성하는 클래스. 
 *
 * @author Lee, Hyeon-gi
 */
abstract class Counter {
  public function __construct() {
  }

  abstract protected function getCurrentTime();

  public function inc() {
    $currentTime = $this->getCurrentTime();
    if ($currentTime < $this->lastTime)
      throw new BackwardTimestamp('current time is little than last time');

    if ($currentTime != $this->lastTime) {
      $this->increment = 0; 
    }
    $this->increment++;
    $this->lastTime = $currentTime;
    return $this->increment;
  } 

  public function reset() {
    $this->increment = 0;
    $this->lastTime = 0;
  }

  public function getIncrement() {
    return $this->increment;
  }

  public function getLastIncTime() {
    return $this->lastTime;
  }

  private $increment = 0; 
  private $lastTime = 0;
}

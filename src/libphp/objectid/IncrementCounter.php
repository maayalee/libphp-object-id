<?php
namespace libphp\objectid;

class IncrementCounter extends Counter {
  /*
   * 현재 시간을 리턴한다 (UNIXTIMESTAMP)
   * 
   * @return int OS시스템에 따라 32/64비트 int형이 결정됨
   */
  protected function getCurrentTime() {
    return time();
  }
}

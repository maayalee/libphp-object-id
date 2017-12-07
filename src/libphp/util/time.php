<?php 
namespace libphp\core;

class time {
  /*
   * 현재 시간을 리턴한다 (UNIXTIMESTAMP)
   * 
   * @return int OS시스템에 따라 32/64비트 int형이 결정됨
   */
  public static function get_time() {
    return time();
  }
  
  /*
   * 현재 시간을 리턴한다 (UNIXTIMESTAMP)
   * 
   * @return float OS시스템에 따라 32/64비트 float형이 결정됨
   */
  public static function get_microtime() {
    return microtime(true);
  }
  
  public static function get_ymd_his() {
    return date('Y-m-d H:i:s');
  }
  
  /**
   * 현재 요일을 반환
   *
   * @return string
   */
  public static function get_day_of_week() {
    $days = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
    return $days[date('w', microtime(true))];
  }
}
?>

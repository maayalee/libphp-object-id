<?php
namespace libphp\core;

class env {

  public static function is_64bit_machine() {
    return PHP_INT_MAX == 9223372036854775807 ? true : false;
  }

  /**
   * 프로시저 아이디를 리턴
   *
   * return int 프로시저아이디
   */
  public static function get_process_id() {
    return getmypid();
  }

  /**
   * 
   */
  public static function get_host_name() {
    return gethostname();
  }

  public static function get_os_information() {
    return php_uname();
  }

  public static function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
    else
      $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
}

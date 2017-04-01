<?php
namespace libphp\object_id;

use libphp\core\time;
use libphp\core\env;
use libphp\object_id\errors\increment_count_overflow;

/**
 * @class object_id
 *
 * @brieif 머신, 프로세스간 겹치지 않는 유니크 아이디를 생성하는 클래스
 *
 *         Mongodb의 ObjectID도 이와 유사한 방식을 사용한다. 
 *
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte)
 *
 * @authoer Lee, Hyeon-gi
 */ 
class object_id extends id {
  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  const TIMESTAMPE_BYTE = 4;
  const MACHINE_ID_BYTE = 3;
  const PROCESS_ID_BYTE = 2;
  const INCREMENT_COUNT_BYTE = 2;
  const MAX_INCREMENT_COUNT_PER_SEC = 65535;

  public function __construct() {
  } 

  /**
   * 헥스 문자열로 변환한다.
   *
   * @return string hex str
   */
  public function to_string() {
    return bin2hex($this->binary);
  } 

  public function to_hash($size) {
    return substr(md5($this->binary), 0, $size);
  }

  public function equal($id) {
    return $this->binary == $id->get_value() ? true : false;
  }

  

  public function init($counter) {
    $count = $counter->inc();
    if ($count > self::MAX_INCREMENT_COUNT_PER_SEC)
      throw new increment_count_overflow('');

    $this->append_timestamp($counter->get_last_inc_time());
    $this->append_machine_id(env::get_host_name());
    $this->append_process_id(env::get_process_id());
    $this->append_increment_count($count); 
  }

  public function init_with_machine_id($counter, $machine_id) {
    $count = $counter->inc();
    if ($count > self::MAX_INCREMENT_COUNT_PER_SEC)
      throw new increment_count_overflow('');

    $this->append_timestamp($counter->get_last_inc_time());
    $this->append_machine_id($machine_id);
    $this->append_process_id(env::get_process_id());
    $this->append_increment_count($count); 
  }

  public function init_by_string($hex) {
    $this->binary = hex2bin($hex);
  }

  private function append_timestamp($time) {
    $this->binary .= pack(self::ULONG_4BYTE_LE, $time);
  } 

  /**
   * 머신간 고유값 생성 메서드
   * 
   * 클라우드 서비스의 hostname은 public ip address기반으로 유니크하게 정해져있이므로
   * 머신 아이디로 사용하기에 충분한다.
   * 설사 hostname이 동일하더라도 process_id로 인해 동일한 id값이 생성되는 일은 거의 없다.
   *
   * 다음은 Ruby의 mongodb driver의 구현법과 동일한다.
   *
   * @return string 해시된 머신이름
   */
  private function append_machine_id($name) {
    $this->binary .= substr(md5($name), 0, self::MACHINE_ID_BYTE);
  }

  private function append_process_id($process_id) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $process_id);
  }

  private function append_increment_count($count) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $count);
  }

  public function get_timestamp() {
    $this->unpacks();
    return $this->timestamp;
  }

  public function get_machine_id() {
    $this->unpacks();
    return $this->machine_id;
  }

  public function get_process_id() {
    $this->unpacks();
    return $this->process_id;
  }

  public function get_increment_count() {
    $this->unpacks();
    return $this->increment_count;
  } 

  /**
   * 원본 데이터 형태로  아이디 필드 정보드를 언팩한다
   *
   * @virtual
   */ 
  private function unpacks() {
    if (!$this->unpacked) {
      ASSERT(strlen($this->binary) == 
        (self::TIMESTAMPE_BYTE + self::MACHINE_ID_BYTE + self::PROCESS_ID_BYTE + self::INCREMENT_COUNT_BYTE));

      $result = array();
      $offset = 0;
      $data = unpack(self::ULONG_4BYTE_LE, substr($this->binary, $offset, self::TIMESTAMPE_BYTE));
      $this->timestamp = $data[1];
      $offset += self::TIMESTAMPE_BYTE;

      $this->machine_id = substr($this->binary, $offset, self::MACHINE_ID_BYTE);
      $offset += self::MACHINE_ID_BYTE;

      $data = unpack(self::USHORT_2BYTE_LE, substr($this->binary, $offset, self::PROCESS_ID_BYTE));
      $this->process_id = $data[1];
      $offset += self::PROCESS_ID_BYTE;

      $data = unpack(self::USHORT_2BYTE_LE, substr($this->binary, $offset, self::INCREMENT_COUNT_BYTE));
      $this->increment_count = $data[1];

      $this->unpacked = true;
    }
  }

  private $binary = '';
  private $unpacked = false;
  private $timestamp;
  private $machine_id;
  private $process_id;
  private $increment_count;

  /**
   * object_id를 생성한다.
   *
   * @param counter counter 타임스탬프 정보를 생성하는 객체
   * @return object_id
   */
  public static function create($counter) {
    $result = new object_id();
    $result->init($counter);
    return $result;
  }

  public static function create_with_machine_id($counter, $machine_id) {
    $result = new object_id();
    $result->init_with_machine_id($counter, $machine_id);
    return $result;
  }

  public static function create_with_string($hex) {
    $result = new object_id();
    $result->init_by_string($hex);
    return $result;
  }
}

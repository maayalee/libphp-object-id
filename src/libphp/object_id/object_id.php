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

  public static function create_with_string(string $hex) {
    $instance = new object_id();
    $instance->load_string($hex);
    return $instance;
  }

  public static function create_with_builder(object_id_builder $builder) {
    $machine_name = $builder->get_machine_name();
    $process_id = $builder->get_process_id();

    $instance = new object_id();
    if (empty($machine_name))
      $machine_name = env::get_host_name();
    if (empty($process_id))
      $process_id = env::get_process_id();
    $instance->generate($builder->get_counter(), $machine_name, $process_id);
    return $instance;
  }

  /**
   * 헥스 문자열로 변환한다.
   *
   * @return string hex str
   */
  public function to_string() {
    return bin2hex($this->binary);
  } 

  public function to_hash(int $size) {
    return substr(md5($this->binary), 0, $size);
  }

  public function equal(id $id) {
    return $this->to_string() == $id->to_string();
  }

  private function load_string(string $hex) {
    $this->binary = hex2bin($hex);
  }

  private function generate(counter $counter, string $machine_name, 
    int $process_id) {
    $count = $counter->inc();
    if ($count > self::MAX_INCREMENT_COUNT_PER_SEC)
      throw new increment_count_overflow('');

    $this->append_timestamp($counter->get_last_inc_time());
    $this->append_machine_id($machine_name);
    $this->append_process_id($process_id);
    $this->append_increment_count($count);
  }

  protected function append_timestamp(int $time) {
    $this->binary .= pack(self::ULONG_4BYTE_LE, $time);
  } 

  protected function append_machine_id(string $name) {
    $this->binary .= substr(md5($name), 0, self::MACHINE_ID_BYTE);
  }

  protected function append_process_id(int $process_id) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $process_id);
  }

  protected function append_increment_count(int $count) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $count);
  } 

  public function get_timestamp() {
    return unpack(self::ULONG_4BYTE_LE, substr($this->binary, 0, 
      self::TIMESTAMPE_BYTE))[1];
  }

  public function get_machine_id() {
    return substr($this->binary, 4, self::MACHINE_ID_BYTE);
  }

  public function get_process_id() {
    return unpack(self::USHORT_2BYTE_LE, 
        substr($this->binary, 7, self::PROCESS_ID_BYTE))[1];
  }

  public function get_increment_count() {
    return unpack(self::USHORT_2BYTE_LE, 
        substr($this->binary, 10, self::INCREMENT_COUNT_BYTE))[1];
  }

  private $binary = '';
}

<?php
namespace libphp\object_id;

/**
 * @class object_id
 *
 * @brieif 머신, 프로세스간 겹치지 않는 유니크 아이디를 생성하는 클래스
 *
 *         Mongodb의 ObjectID도 이와 유사한 방식을 사용한다. 
 *
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte)
 * @authoer Lee, Hyeon-gi
 */ 
class object_id extends id {
  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  const TIMESTAMPE_BYTE = 4;
  const MACHINE_ID_BYTE = 3;
  const PROCESS_ID_BYTE = 2;
  const INCREMENT_COUNT_BYTE = 2;
  const TOTAL_BYTE = 11;

  public function __construct() {
  }
  
  public function init($current_time = -1, $max_generate_count = ID_Timestamp::MAX_GENERATE_COUNT_PER_SEC) {
    $timestamp = ID_Timestamp::get_instance()->generate($current_time, $max_generate_count);
    $this->binary = '';
    $this->binary .= pack(self::ULONG_4BYTE_LE, $timestamp['gen_timestamp']);
    $this->binary .= ID::create_hashed_machine_name(self::MACHINE_ID_BYTE);
    $this->binary .= pack(self::USHORT_2BYTE_LE, ID::create_process_id());
    $this->binary .= pack(self::USHORT_2BYTE_LE, $timestamp['gen_increment']);
  }

  public function init_by_string($hex_string) {
    $this->binary = hex2bin($hex_string);
  }

  public function to_string() {
    return bin2hex($this->binary);
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
      ASSERT('strlen($this->binary) == self::TOTAL_BYTE');
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

      $this->unpacked != $this->unpacked;
    }
  }

  private $unpacked = false;
  private $timestamp;
  private $machine_id;
  private $process_id;
  private $increment_count;

  /**
   * object_id를 생성한다.
   *
   * @param current_time int 유닉스 타임 스탬프
   * @param max_generate_count int 초당 생성할 수 있는 아이디 최대 갯수
   *
   * @return object_id
   */
  public static function create($current_time = -1, $max_generate_count = ID_Timestamp::MAX_GENERATE_COUNT_PER_SEC) {
    $result = new object_id();
    $result->init($current_time, $max_generate_count);
    return $result;
  }
}

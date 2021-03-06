<?php
namespace libphp\objectid;

use libphp\objectid\errors\IncrementCountOverflow;


/**
 * @class ObjectID
 *
 * @brieif 머신, 프로세스간 겹치지 않는 유니크 아이디를 생성하는 클래스
 *
 *         Mongodb의 ObjectID도 이와 유사한 방식을 사용한다. 
 *
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte)
 *
 * @authoer Lee, Hyeon-gi
 */ 
class ObjectID extends ID {
  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  const TIMESTAMPE_BYTE = 4;
  const MACHINE_ID_BYTE = 3;
  const PROCESS_ID_BYTE = 2;
  const INCREMENT_COUNT_BYTE = 2;
  const MAX_INCREMENT_COUNT_PER_SEC = 65535; 

  public function __construct() {
  }

  public static function createWithString(string $hex) {
    $instance = new ObjectID();
    $instance->loadString($hex);
    return $instance;
  }

  public static function createWithBuilder(ObjectIDBuilder $builder) {
    $machineName = $builder->getMachineName();
    $processID = $builder->getProcessID();

    $instance = new ObjectID();
    if (empty($machineName)) {
      $machineName = gethostname();
    }
    if (empty($processID)) {
      $processID = getmypid();
    }
    $instance->generate($builder->getCounter(), $machineName, $processID);
    return $instance;
  }

  /**
   * 헥스 문자열로 변환한다.
   *
   * @return string hex str
   */
  public function toString() {
    return bin2hex($this->binary);
  } 

  public function toHash(int $size) {
    return substr(md5($this->binary), 0, $size);
  }

  public function equal(id $id) {
    return $this->toString() == $id->toString();
  }

  private function loadString(string $hex) {
    $this->binary = hex2bin($hex);
  }

  private function generate(counter $counter, string $machineName, int $processID) {
    $count = $counter->inc();
    if ($count > self::MAX_INCREMENT_COUNT_PER_SEC)
      throw new IncrementCountOverflow('');

    $this->appendTimestamp($counter->getLastIncTime());
    $this->appendMachine_id($machineName);
    $this->appendProcessID($processID);
    $this->appendIncrementCount($count);
  }

  protected function appendTimestamp(int $time) {
    $this->binary .= pack(self::ULONG_4BYTE_LE, $time);
  } 

  protected function appendMachine_id(string $name) {
    $this->binary .= substr(md5($name), 0, self::MACHINE_ID_BYTE);
  }

  protected function appendProcessID(int $processID) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $processID);
  }

  protected function appendIncrementCount(int $count) {
    $this->binary .= pack(self::USHORT_2BYTE_LE, $count);
  } 

  public function getTimestamp() {
    return unpack(self::ULONG_4BYTE_LE, substr($this->binary, 0, self::TIMESTAMPE_BYTE))[1];
  }

  public function getMachineID() {
    return substr($this->binary, 4, self::MACHINE_ID_BYTE);
  }

  public function getProcessID() {
    return unpack(self::USHORT_2BYTE_LE, substr($this->binary, 7, self::PROCESS_ID_BYTE))[1];
  }

  public function getIncrementCount() {
    return unpack(self::USHORT_2BYTE_LE, substr($this->binary, 10, self::INCREMENT_COUNT_BYTE))[1];
  }

  private $binary = '';
}

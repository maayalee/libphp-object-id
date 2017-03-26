<?php
namespace libphp\object_id;

use libphp\core\env;

/**
 * @class id
 *
 * @birief Unique Identifier 처리를 위한 베이스 클래스
 * @author Lee, Hyeon-gi
 */
abstract class id {  
  const DEFAULT_HASH_SIZE = 3;
  
  abstract public function init_by_string($data);
  abstract public function to_string(); 

  public function get_value() {
    return $this->binary;
  }

  public function equal($id) {
    return $this->binary == $id->get_value() ? true : false;
  }

  /**
   * 문자열 타입으로 해시한 값을 되돌려준다.
   *
   * @param size string 해시 사이즈
   *
   * @return string 해시한 문자열
   */
  public function to_hash($size = self::DEFAULT_HASH_SIZE) {
    return substr(md5($this->binary), 0, $size);
  }

  protected $binary = '';

  /**
   * 머신간 고유값 생성 메서드
   * 
   * 클라우드 서비스의 hostname은 public ip address기반으로 유니크하게 정해져있이므로
   * 머신 아이디로 사용하기에 충분한다.
   * 설사 hostname이 동일하더라도 process_id로 인해 동일한 id값이 생성되는 일은 거의 없다.
   *
   * 다음은 Ruby의 mongodb driver의 구현법과 동일한다.
   *
   * @return string 해시된 3바이트 문자열
   */
  static public function create_hashed_machine_name($hash_size) {
    $result = env::get_host_name();
    return substr(md5($result), 0, $hash_size);
  }

  /**
   * 프로세스간 고유값 생성을 위한 메서드
   */
  static public function create_process_id() {
    return env::get_process_id();
  }
}


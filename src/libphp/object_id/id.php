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
}


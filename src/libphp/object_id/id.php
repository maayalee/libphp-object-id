<?php
namespace libphp\object_id;

/**
 * @class id
 *
 * @birief Unique Identifier 처리를 위한 베이스 클래스
 * @author Lee, Hyeon-gi
 */
abstract class id {  
  abstract public function to_string(); 
  /**
   * 문자열 타입으로 해시한 값을 되돌려준다.
   *
   * @param size string 해시 사이즈
   *
   * @return string 해시한 문자열
   */
  abstract public function to_hash(int $size);

  abstract public function equal(id $id);
}


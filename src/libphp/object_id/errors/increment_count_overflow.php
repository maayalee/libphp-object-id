<?php
namespace libphp\object_id\errors;

class increment_count_overflow extends object_id_error {
  public function __construct($msg) {
    parent::__construct($msg, error_codes::ERROR_CODE_INCREMENT_COUNT_OVERFLOW);
  }
}

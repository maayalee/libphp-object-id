<?php
namespace libphp\object_id\errors;

class backward_timestamp extends object_id_error {
  public function __construct($msg) {
    parent::__construct($msg, error_codes::ERROR_CODE_BACKWARD_TIMESTAMP);
  }
}

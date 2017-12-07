<?php
namespace libphp\objectid\errors;

class IncrementCountOverflow extends ObjectIDError {
  public function __construct($msg) {
    parent::__construct($msg, ErrorCodes::ERROR_CODE_INCREMENT_COUNT_OVERFLOW);
  }
}

<?php
namespace libphp\objectid\errors;

class BackwardTimestamp extends ObjectIDError {
  public function __construct($msg) {
    parent::__construct($msg, ErrorCodes::ERROR_CODE_BACKWARD_TIMESTAMP);
  }
}

<?php
namespace libphp\objectid\errors;

class ObjectIDError extends \Exception {
  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  } 
}

<?php
namespace libphp\object_id\errors;


class object_id_error extends \Exception {
  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  } 
}

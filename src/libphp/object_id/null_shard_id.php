<?php
namespace libphp\object_id;

class null_shard_id extends shard_id {
  public function __construct() {
    parent::__construct('');
  }

  public function is_null() {
    return true;
  }
}


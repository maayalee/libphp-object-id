<?php
namespace libphp\tests\object_id;

use libphp\core\time;
use libphp\test\test_case;
use libphp\test\test_suite;
use libphp\object_id\id;
use libphp\object_id\increment_counter;
use libphp\object_id\object_id;
use libphp\object_id\errors\increment_count_overflow;
use libphp\object_id\errors\backward_timestamp;

class object_id_test extends test_case {
  private $server;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create_id() {
    $id = object_id::create($this->counter);
    $this->assert($id->get_timestamp() == $this->counter->get_last_inc_time(), 
    "get unpack timestamp");
  }

  public function test_not_duplicated_ids() {
    $ids = $this->generate_ids(1000);
    $this->assert(count($ids) == 1000, 'not duplicated ids');
  }

  public function test_reset_increment() {
    $id = object_id::create($this->counter);
    $this->assert(1 == $this->counter->get_increment(), 'inc is 1');
    $id = object_id::create($this->counter);
    $this->assert(2 == $this->counter->get_increment(), 'inc is 2');

    $this->counter->inc_current_time();
    $id = object_id::create($this->counter);
    $this->assert(1 == $this->counter->get_increment(), 'inc is 1');
  }

  public function test_throw_backward_timestamp() {
    $this->assert(true == $this->process_generate_ids(1));
    $this->counter->dec_current_time();
    $this->assert(false == $this->process_generate_ids(1));
  }

  public function test_throw_increment_count_overflow() {
    $this->assert(true == $this->process_generate_ids($this->get_max_increment_count()));
    $this->assert(false == $this->process_generate_ids($this->get_max_increment_count() + 1));
  }

  private function process_generate_ids($count) {
    try {
      $this->generate_ids($count);
    }
    catch (backward_timestamp $e) {
      return false;
    }
    catch (increment_count_overflow $e) {
      return false;
    }
    return true;
  }

  private function generate_ids($count) {
    $ids = array();
    for ($i = 0; $i < $count; $i++) {
      $id = object_id::create($this->counter);
      array_push($ids, $id->to_string());
    }
    return array_unique($ids);
  }

  public function set_up() {
    $this->counter = $this->create_counter();
    $timestamp = time::get_time();
    $this->counter->set_current_time($timestamp);

  }

  public function tear_down() {
  }

  private function create_counter() {
    return new test_increment_counter();
  }

  private function get_max_increment_count() {
    return object_id::MAX_INCREMENT_COUNT_PER_SEC;
  }

  private $counter;

  public static function create_suite() {
    $suite = new test_suite('object_id_test');
    $suite->add(new object_id_test('test_create_id'));
    $suite->add(new object_id_test('test_not_duplicated_ids'));
    $suite->add(new object_id_test('test_reset_increment'));
    $suite->add(new object_id_test('test_throw_backward_timestamp'));
    $suite->add(new object_id_test('test_throw_increment_count_overflow'));
    return $suite;
  }
}

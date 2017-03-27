<?php
namespace libphp\tests\object_id;

use libphp\core\time;
use libphp\test\test_case;
use libphp\test\test_suite;
use libphp\object_id\id;
use libphp\object_id\id_timestamp;
use libphp\object_id\object_id;
use libphp\object_id\errors\increment_count_overflow;
use libphp\object_id\errors\backward_timestamp;

class object_id_test extends test_case {
  private $server;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create_id() {
    $timestamp = time::get_time();

    $id = object_id::create_by_time($timestamp, $this->get_max_increment_count());
    $this->assert($id->get_timestamp() == $timestamp, "get unpack timestamp");
  }

  public function test_not_duplicated_ids() {
    $timestamp = time::get_time();
    $ids = $this->generate_ids($this->get_max_increment_count(), $timestamp);
    $this->assert(count($ids) == $this->get_max_increment_count(), 'not duplicated ids');
  }

  public function test_reset_increment() {
    $timestamp = time::get_time();
    $id = object_id::create_by_time($timestamp, $this->get_max_increment_count());
    $this->assert(1 == id_timestamp::get_instance()->get_increment(), 'inc is 1');
    $id = object_id::create_by_time($timestamp, $this->get_max_increment_count());
    $this->assert(2 == id_timestamp::get_instance()->get_increment(), 'inc is 2');

    $reset_increment_timestamp = $timestamp + 1;
    $id = object_id::create_by_time($reset_increment_timestamp, $this->get_max_increment_count());
    $this->assert(1 == id_timestamp::get_instance()->get_increment(), 'inc is 1');
  }

  public function test_throw_backward_timestamp() {
    $timestamp = time::get_time();
    $this->assert(true == $this->process_generate_ids(1, $timestamp));
    $backward_timestamp = $timestamp - 1;
    $this->assert(false == $this->process_generate_ids(1, $backward_timestamp));
  }

  public function test_throw_increment_count_overflow() {
    $timestamp = time::get_time();
    $this->assert(false == $this->process_generate_ids($this->get_max_increment_count() + 1, $timestamp));
  }

  private function process_generate_ids($count, $current_time) {
    try {
      $this->generate_ids($count, $current_time, $this->get_max_increment_count());
    }
    catch (backward_timestamp $e) {
      return false;
    }
    catch (increment_count_overflow $e) {
      return false;
    }
    return true;
  }

  private function generate_ids($count, $current_time = -1) {
    $ids = array();
    for ($i = 0; $i < $count; $i++) {
      $id = object_id::create_by_time($current_time, $this->get_max_increment_count());
      array_push($ids, $id->to_string());
    }
    return array_unique($ids);
  }

  public function set_up() {
    id_timestamp::get_instance()->reset();
  }

  public function tear_down() {
  }

  private function get_max_increment_count() {
    return 10;
  }

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
